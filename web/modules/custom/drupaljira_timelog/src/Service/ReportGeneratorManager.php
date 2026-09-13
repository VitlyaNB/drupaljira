<?php

namespace Drupal\drupaljira_timelog\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\drupaljira_timelog\Attribute\ReportGenerator;
use Drupal\drupaljira_timelog\ReportGeneratorInterface;

/**
 * Provides the ReportGenerator plugin manager.
 */
final class ReportGeneratorManager extends DefaultPluginManager {

  /**
   * Constructs a new ReportGeneratorManager object.
   *
   * @param \Traversable<string, mixed> $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cacheBackend,
    ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct(
      'Plugin/ReportGenerator',
      $namespaces,
      $moduleHandler,
      ReportGeneratorInterface::class,
      ReportGenerator::class
    );

    $this->alterInfo('report_generator_info');
    $this->setCacheBackend($cacheBackend, 'report_generator_plugins');
  }

}
