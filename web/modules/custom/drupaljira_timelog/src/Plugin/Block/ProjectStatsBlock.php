<?php

namespace Drupal\drupaljira_timelog\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Project Statistics' Block.
 */
#[Block(
  id: "project_stats_block",
  admin_label: new TranslatableMarkup("Project Statistics"),
  category: new TranslatableMarkup("DrupalJira")
)]
final class ProjectStatsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * Constructs a new ProjectStatsBlock instance.
   *
   * @param array<string, mixed> $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RouteMatchInterface $routeMatch,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $routeMatch;
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
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $project = $this->getProjectFromContext();

    if (!$project) {
      return [];
    }

    return [
      '#theme' => 'drupaljira_project_stats',
      '#project' => $project,
      '#cache' => [
        'contexts' => ['route'],
        'tags' => ['node_list', 'time_log_list'],
      ],
    ];
  }

  /**
   * Determines project entity from current route context.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Project node entity or NULL.
   */
  protected function getProjectFromContext(): ?EntityInterface {
    $node = $this->routeMatch->getParameter('node');

    if (!$node instanceof EntityInterface) {
      return NULL;
    }

    if ($node->bundle() === 'project') {
      return $node;
    }

    if ($node->bundle() === 'task' && $node->hasField('field_project') && !$node->get('field_project')->isEmpty()) {
      /** @var \Drupal\Core\Entity\EntityInterface|null $project */
      $project = $node->get('field_project')->entity;
      return $project;
    }

    return NULL;
  }

}
