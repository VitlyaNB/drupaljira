<?php

namespace Drupal\drupaljira_timelog;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of TimeLog entities.
 */
class TimeLogListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['task'] = $this->t('Task');
    $header['uid'] = $this->t('User');
    $header['hours'] = $this->t('Hours');
    $header['log_date'] = $this->t('Log Date');
    $header['notes'] = $this->t('Notes');
    $header['over_estimate_reason'] = $this->t('Over Estimate Reason');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\drupaljira_timelog\Entity\TimeLog $entity */
    $row['id'] = $entity->id();

    $task = $entity->get('task')->entity;
    $row['task'] = $task ? $task->label() : '-';

    $user = $entity->get('uid')->entity;
    $row['uid'] = $user ? $user->label() : '-';

    $row['hours'] = $entity->get('hours')->value;
    $row['log_date'] = $entity->get('log_date')->value;
    $row['notes'] = $entity->get('notes')->value ?? '-';
    $row['over_estimate_reason'] = $entity->get('over_estimate_reason')->value ?? '-';

    return $row + parent::buildRow($entity);
  }

}
