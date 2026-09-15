<?php

namespace Drupal\drupaljira_timelog\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\drupaljira_timelog\Service\DurationFormatter;
use Drupal\drupaljira_timelog\Service\TaskStatService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'time_summary_formatter' formatter.
 */
#[FieldFormatter(
  id: "time_summary_formatter",
  label: new TranslatableMarkup("Time Summary"),
  field_types: ["decimal", "float", "numeric"]
)]
final class TimeSummaryFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The task stat service.
   *
   * @var \Drupal\drupaljira_timelog\Service\TaskStatService
   */
  protected TaskStatService $taskStatService;

  /**
   * The duration formatter service.
   *
   * @var \Drupal\drupaljira_timelog\Service\DurationFormatter
   */
  protected DurationFormatter $durationFormatter;

  /**
   * Constructs a new TimeSummaryFormatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is being applied.
   * @param array<string, mixed> $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array<string, mixed> $third_party_settings
   *   Any third party settings.
   * @param \Drupal\drupaljira_timelog\Service\TaskStatService $taskStatService
   *   The task stat service.
   * @param \Drupal\drupaljira_timelog\Service\DurationFormatter $durationFormatter
   *   The duration formatter service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    TaskStatService $taskStatService,
    DurationFormatter $durationFormatter,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->taskStatService = $taskStatService;
    $this->durationFormatter = $durationFormatter;
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
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('drupaljira.task_stat'),
      $container->get('drupaljira.duration_formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $task = $items->getEntity();

    foreach ($items as $delta => $item) {
      $estimate = (float) $item->value;
      $logged = $this->taskStatService->getLoggedHours($task);
      $remaining = $this->taskStatService->getRemainingEstimate($task);

      $elements[$delta] = [
        '#markup' => $this->durationFormatter->formatSummary($estimate, $logged, $remaining),
      ];
    }

    return $elements;
  }

}
