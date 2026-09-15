<?php

namespace Drupal\drupaljira_timelog\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Service for formatting time duration values into human readable strings.
 */
final class DurationFormatter {

  use StringTranslationTrait;

  /**
   * Constructs a DurationFormatter object.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The string translation service.
   */
  public function __construct(TranslationInterface $stringTranslation) {
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * Formats a single hours float value into a human readable string.
   *
   * @param float $hours
   *   The hours value to format.
   *
   * @return string
   *   Formatted string with rounded hours and unit suffix.
   */
  public function format(float $hours): string {
    return (string) $this->t('@hours hrs', [
      '@hours' => number_format($hours, 2),
    ]);
  }

  /**
   * Assembles a human-readable time summary phrase.
   *
   * @param float $estimate
   *   Total estimated hours.
   * @param float $logged
   *   Total logged hours.
   * @param float $remaining
   *   Remaining estimate hours (can be negative if overdue).
   *
   * @return string
   *   Formatted summary string.
   */
  public function formatSummary(float $estimate, float $logged, float $remaining): string {
    $estimateStr = $this->format($estimate);
    $loggedStr = $this->format($logged);

    if ($remaining < 0) {
      $overdueStr = $this->format(abs($remaining));
      return (string) $this->t('@estimate (@logged written off, OVERDUE by @overdue)', [
        '@estimate' => $estimateStr,
        '@logged' => $loggedStr,
        '@overdue' => $overdueStr,
      ]);
    }

    $remainingStr = $this->format($remaining);
    return (string) $this->t('@estimate (@logged written off, @remaining left)', [
      '@estimate' => $estimateStr,
      '@logged' => $loggedStr,
      '@remaining' => $remainingStr,
    ]);
  }

}
