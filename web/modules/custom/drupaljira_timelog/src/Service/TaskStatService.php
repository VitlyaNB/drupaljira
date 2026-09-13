<?php

namespace Drupal\drupaljira_timelog\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service for calculating time tracking statistics for tasks and projects.
 */
final class TaskStatService {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new TaskStatService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Calculates total logged hours for a specific task.
   *
   * @param \Drupal\Core\Entity\EntityInterface $task
   *   The task node entity.
   *
   * @return float
   *   Total hours logged.
   */
  public function getLoggedHours(EntityInterface $task): float {
    $storage = $this->entityTypeManager->getStorage('time_log');

    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('task', $task->id());

    $ids = $query->execute();

    if (empty($ids)) {
      return 0.0;
    }

    /** @var \Drupal\drupaljira_timelog\Entity\TimeLog[] $timeLogs */
    $timeLogs = $storage->loadMultiple($ids);
    $totalHours = 0.0;

    foreach ($timeLogs as $log) {
      $totalHours += (float) $log->get('hours')->value;
    }

    return $totalHours;
  }

  /**
   * Calculates remaining estimate for a task.
   *
   * @param \Drupal\Core\Entity\EntityInterface $task
   *   The task node entity.
   *
   * @return float
   *   Difference between estimate and logged hours. Negative indicates excess.
   */
  public function getRemainingEstimate(EntityInterface $task): float {
    $estimate = 0.0;
    if ($task->hasField('field_estimate') && !$task->get('field_estimate')->isEmpty()) {
      $estimate = (float) $task->get('field_estimate')->value;
    }

    return $estimate - $this->getLoggedHours($task);
  }

  /**
   * Calculates statistics for all tasks within a project.
   *
   * @param \Drupal\Core\Entity\EntityInterface $project
   *   The project node entity.
   *
   * @return array<string, mixed>
   *   Aggregated metrics array.
   */
  public function getProjectStats(EntityInterface $project): array {
    $nodeStorage = $this->entityTypeManager->getStorage('node');

    $query = $nodeStorage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'task')
      ->condition('field_project', $project->id());

    $taskIds = $query->execute();

    if (empty($taskIds)) {
      return [
        'total_tasks' => 0,
        'done_tasks' => 0,
        'total_estimate' => 0.0,
        'total_logged' => 0.0,
        'overestimate_tasks' => 0,
      ];
    }

    /** @var \Drupal\Core\Entity\EntityInterface[] $tasks */
    $tasks = $nodeStorage->loadMultiple($taskIds);

    $totalTasks = count($tasks);
    $doneTasks = 0;
    $totalEstimate = 0.0;
    $totalLogged = 0.0;
    $overestimateTasks = 0;

    foreach ($tasks as $task) {
      if ($task->hasField('field_estimate') && !$task->get('field_estimate')->isEmpty()) {
        $totalEstimate += (float) $task->get('field_estimate')->value;
      }

      $logged = $this->getLoggedHours($task);
      $totalLogged += $logged;

      if ($this->getRemainingEstimate($task) < 0) {
        $overestimateTasks++;
      }

      if ($task->hasField('field_status') && !$task->get('field_status')->isEmpty()) {
        $status = (string) $task->get('field_status')->value;
        if (strtolower($status) === 'done') {
          $doneTasks++;
        }
      }
    }

    return [
      'total_tasks' => $totalTasks,
      'done_tasks' => $doneTasks,
      'total_estimate' => $totalEstimate,
      'total_logged' => $totalLogged,
      'overestimate_tasks' => $overestimateTasks,
    ];
  }

}
