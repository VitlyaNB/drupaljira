<?php

namespace Drupal\drupaljira_timelog\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for debugging TimeLog Entity operations.
 */
final class TimeLogDebugController extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new TimeLogDebugController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new self(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Demonstrates full CRUD operations for TimeLog entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $task
   *   The task node from upcasting.
   *
   * @return array
   *   A render array with debug output.
   */
  public function crud(EntityInterface $task): array {
    $storage = $this->entityTypeManager->getStorage('time_log');
    $currentUser = $this->currentUser();
    $items = [];

    // 1. Create.
    /** @var \Drupal\drupaljira_timelog\Entity\TimeLog $timeLog */
    $timeLog = $storage->create([
      'task' => $task->id(),
      'uid' => $currentUser->id(),
      'hours' => 2.00,
      'log_date' => date('Y-m-d'),
      'notes' => 'Debug CRUD test entry',
    ]);
    $timeLog->save();
    $entityId = $timeLog->id();
    $items[] = $this->t('1. Created record ID: @id', ['@id' => $entityId]);

    // 2. Load.
    /** @var \Drupal\drupaljira_timelog\Entity\TimeLog|null $loadedLog */
    $loadedLog = $storage->load($entityId);
    if ($loadedLog) {
      $items[] = $this->t('2. Loaded record ID @id with @hours hours and notes "@notes"', [
        '@id' => $loadedLog->id(),
        '@hours' => $loadedLog->get('hours')->value,
        '@notes' => $loadedLog->get('notes')->value,
      ]);

      // 3. Update.
      $loadedLog->set('hours', 4.50);
      $loadedLog->save();

      /** @var \Drupal\drupaljira_timelog\Entity\TimeLog $updatedLog */
      $updatedLog = $storage->load($entityId);
      $items[] = $this->t('3. Updated record ID @id hours to @hours', [
        '@id' => $updatedLog->id(),
        '@hours' => $updatedLog->get('hours')->value,
      ]);

      // 4. Delete.
      $updatedLog->delete();

      $deletedCheck = $storage->load($entityId);
      if ($deletedCheck === NULL) {
        $items[] = $this->t('4. Successfully deleted record ID @id (Load returned NULL)', ['@id' => $entityId]);
      }
    }

    return [
      '#theme' => 'item_list',
      '#title' => $this->t('CRUD Operations Log for Task: @title (ID: @id)', [
        '@title' => $task->label(),
        '@id' => $task->id(),
      ]),
      '#items' => $items,
    ];
  }

  /**
   * Displays all TimeLog records for a specific task sorted by log_date ASC.
   *
   * @param \Drupal\Core\Entity\EntityInterface $task
   *   The task node.
   *
   * @return array
   *   Render array with table.
   */
  public function list(EntityInterface $task): array {
    $storage = $this->entityTypeManager->getStorage('time_log');

    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('task', $task->id())
      ->sort('log_date', 'ASC');

    $ids = $query->execute();
    /** @var \Drupal\drupaljira_timelog\Entity\TimeLog[] $timeLogs */
    $timeLogs = $storage->loadMultiple($ids);

    $rows = [];
    foreach ($timeLogs as $log) {
      $rows[] = [
        'id' => $log->id(),
        'hours' => $log->get('hours')->value,
        'log_date' => $log->get('log_date')->value,
      ];
    }

    return [
      '#type' => 'table',
      '#header' => [
        $this->t('Record ID'),
        $this->t('Hours'),
        $this->t('Log Date'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No time logs found for this task.'),
    ];
  }

  /**
   * Displays total hours logged for a specific task.
   *
   * @param \Drupal\Core\Entity\EntityInterface $task
   *   The task node.
   *
   * @return array
   *   Render array with total hours.
   */
  public function sum(EntityInterface $task): array {
    $storage = $this->entityTypeManager->getStorage('time_log');

    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('task', $task->id());

    $ids = $query->execute();
    $totalHours = 0.00;

    if (!empty($ids)) {
      /** @var \Drupal\drupaljira_timelog\Entity\TimeLog[] $timeLogs */
      $timeLogs = $storage->loadMultiple($ids);
      foreach ($timeLogs as $log) {
        $totalHours += (float) $log->get('hours')->value;
      }
    }

    return [
      '#markup' => '<p>' . $this->t('Total hours logged for task @title: <strong>@sum</strong>', [
        '@title' => $task->label(),
        '@sum' => number_format($totalHours, 2, '.', ''),
      ]) . '</p>',
    ];
  }

}
