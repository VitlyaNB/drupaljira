<?php

namespace Drupal\drupaljira_timelog\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for TimeLog edit forms.
 */
class TimeLogForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $status = parent::save($form, $form_state);

    if ($status === 1) {
      $this->messenger()->addMessage($this->t('Created new time log entry.'));
    }
    else {
      $this->messenger()->addMessage($this->t('Updated the time log entry.'));
    }

    $form_state->setRedirect('entity.time_log.collection');
    return $status;
  }

}
