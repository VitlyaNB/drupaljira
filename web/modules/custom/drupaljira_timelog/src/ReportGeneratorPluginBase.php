<?php

namespace Drupal\drupaljira_timelog;

use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for ReportGenerator plugins.
 */
abstract class ReportGeneratorPluginBase extends PluginBase implements ReportGeneratorInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel(): string {
    return (string) $this->pluginDefinition['label'];
  }

}
