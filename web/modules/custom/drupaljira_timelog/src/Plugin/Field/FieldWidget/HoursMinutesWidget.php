<?php

namespace Drupal\drupaljira_timelog\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'hours_minutes_widget' widget.
 */
#[FieldWidget(
  id: "hours_minutes_widget",
  label: new TranslatableMarkup("Hours + Minutes"),
  field_types: ["decimal", "float", "numeric"]
)]
final class HoursMinutesWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $decimalValue = (float) ($items[$delta]->value ?? 0.0);

    $hours = (int) floor($decimalValue);
    $minutes = (int) round(($decimalValue - $hours) * 60);

    if ($minutes === 60) {
      $hours++;
      $minutes = 0;
    }

    $element['#type'] = 'fieldset';
    $element['#title'] = $element['#title'] ?? $this->t('Estimate');

    $element['hours'] = [
      '#type' => 'number',
      '#title' => $this->t('Hours'),
      '#default_value' => $hours,
      '#min' => 0,
    ];

    $element['minutes'] = [
      '#type' => 'number',
      '#title' => $this->t('Minutes'),
      '#default_value' => $minutes,
      '#min' => 0,
      '#max' => 59,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => $value) {
      $h = (int) ($value['hours'] ?? 0);
      $m = (int) ($value['minutes'] ?? 0);

      $decimal = $h + ($m / 60.0);
      $values[$delta]['value'] = round($decimal, 4);
    }

    return $values;
  }

}
