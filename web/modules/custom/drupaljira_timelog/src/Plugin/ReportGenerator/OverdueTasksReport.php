<?php

namespace Drupal\drupaljira_timelog\Plugin\ReportGenerator;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\drupaljira_timelog\Attribute\ReportGenerator;
use Drupal\drupaljira_timelog\ReportGeneratorPluginBase;
use Drupal\drupaljira_timelog\Service\TaskStatService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Overdue Tasks Report plugin.
 */
#[ReportGenerator(
  id: 'overdue_tasks',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Overdue Tasks Report')
)]
final class OverdueTasksReport extends ReportGeneratorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The task stat service.
   *
   * @var \Drupal\drupaljira_timelog\Service\TaskStatService
   */
  protected TaskStatService $taskStatService;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new OverdueTasksReport object.
   *
   * @param array<string, mixed> $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\drupaljira_timelog\Service\TaskStatService $taskStatService
   *   The task stat service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    TaskStatService $taskStatService,
    EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->taskStatService = $taskStatService;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('drupaljira.task_stat'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function generate(EntityInterface $project): array {
    $nodeStorage = $this->entityTypeManager->getStorage('node');

    $query = $nodeStorage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'task')
      ->condition('field_project', $project->id());

    $taskIds = $query->execute();

    if (empty($taskIds)) {
      return [];
    }

    /** @var \Drupal\node\NodeInterface[] $tasks */
    $tasks = $nodeStorage->loadMultiple($taskIds);

    $overdueTasks = [];
    foreach ($tasks as $task) {
      $remaining = $this->taskStatService->getRemainingEstimate($task);
      if ($remaining < 0) {
        $overdueTasks[] = [
          'id' => $task->id(),
          'title' => $task->label(),
          'exceeded_by' => abs($remaining),
        ];
      }
    }

    return $overdueTasks;
  }

}
