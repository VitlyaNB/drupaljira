<?php

namespace Drupal\drupaljira_timelog\Plugin\ReportGenerator;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\drupaljira_timelog\Attribute\ReportGenerator;
use Drupal\drupaljira_timelog\ReportGeneratorPluginBase;
use Drupal\drupaljira_timelog\Service\TaskStatService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Project Summary Report plugin.
 */
#[ReportGenerator(
  id: 'project_summary',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Project Summary Report')
)]
final class ProjectSummaryReport extends ReportGeneratorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The task stat service.
   *
   * @var \Drupal\drupaljira_timelog\Service\TaskStatService
   */
  protected TaskStatService $taskStatService;

  /**
   * Constructs a new ProjectSummaryReport object.
   *
   * @param array<string, mixed> $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\drupaljira_timelog\Service\TaskStatService $taskStatService
   *   The task stat service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    TaskStatService $taskStatService,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->taskStatService = $taskStatService;
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
      $container->get('drupaljira.task_stat')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function generate(EntityInterface $project): array {
    return $this->taskStatService->getProjectStats($project);
  }

}
