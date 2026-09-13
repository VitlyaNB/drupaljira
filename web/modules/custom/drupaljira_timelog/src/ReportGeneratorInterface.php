<?php

namespace Drupal\drupaljira_timelog;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines an interface for ReportGenerator plugins.
 */
interface ReportGeneratorInterface extends PluginInspectionInterface {

  /**
   * Generates report data for the given project.
   *
   * @param \Drupal\Core\Entity\EntityInterface $project
   *   The project node entity.
   *
   * @return array<string, mixed>
   *   The generated report data.
   */
  public function generate(EntityInterface $project): array;

  /**
   * Returns the readable label of the report.
   *
   * @return string
   *   The report label.
   */
  public function getLabel(): string;

}
