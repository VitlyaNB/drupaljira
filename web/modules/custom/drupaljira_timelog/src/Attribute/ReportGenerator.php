<?php

namespace Drupal\drupaljira_timelog\Attribute;

use Drupal\Component\Plugin\Attribute\AttributeBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a ReportGenerator attribute object.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ReportGenerator extends AttributeBase {

  /**
   * Constructs a ReportGenerator attribute object.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|string $label
   *   The human-readable label of the plugin.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup|string $label,
  ) {
  }

}
