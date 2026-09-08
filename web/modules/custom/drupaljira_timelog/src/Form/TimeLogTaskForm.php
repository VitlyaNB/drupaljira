<?php

namespace Drupal\drupaljira_timelog\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom form to log time for a specific task.
 */
final class TimeLogTaskForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new TimeLogTaskForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, AccountInterface $currentUser) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'drupaljira_timelog_task_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?EntityInterface $task = NULL): array {
    if (!$task) {
      return $form;
    }

    // Disable browser HTML5 validation.
    $form['#attributes']['novalidate'] = 'novalidate';

    // Renders messages directly in the form HTML.
    $form['messages'] = [
      '#type' => 'status_messages',
      '#weight' => -100,
    ];

    // Store task in form state for validation and submission.
    $form_state->set('task_id', $task->id());

    $form['#title'] = $this->t('Log Time for Task: @title', ['@title' => $task->label()]);

    $form['hours'] = [
      '#type' => 'number',
      '#title' => $this->t('Hours'),
      '#step' => 'any',
      '#min' => -999,
      '#required' => TRUE,
    ];

    $form['log_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Log Date'),
      '#default_value' => date('Y-m-d'),
      '#required' => TRUE,
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Notes'),
      '#required' => FALSE,
    ];

    $form['over_estimate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I am writing off more marks'),
      '#default_value' => 0,
    ];

    $form['over_estimate_reason'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reason for excess'),
      '#states' => [
        'visible' => [
          ':input[name="over_estimate"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="over_estimate"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Time Log'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // 1. Validate hours > 0.
    $rawHours = $form_state->getValue('hours');
    if (!is_numeric($rawHours) || (float) $rawHours <= 0) {
      $form_state->setErrorByName('hours', $this->t('Hours must be strictly greater than zero.'));
    }

    // 2. Validate log_date <= today.
    $logDate = $form_state->getValue('log_date');
    $today = date('Y-m-d');
    if ($logDate > $today) {
      $form_state->setErrorByName('log_date', $this->t('Log date cannot be in the future.'));
    }

    // 3. Server-side validation for reason if checkbox checked.
    $isOverEstimate = (bool) $form_state->getValue('over_estimate');
    $reason = trim((string) $form_state->getValue('over_estimate_reason'));
    if ($isOverEstimate && $reason === '') {
      $form_state->setErrorByName('over_estimate_reason', $this->t('Reason for excess is required when exceeding marks.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $taskId = $form_state->get('task_id');
    $isOverEstimate = (bool) $form_state->getValue('over_estimate');

    $storage = $this->entityTypeManager->getStorage('time_log');

    $timeLogData = [
      'task' => $taskId,
      'uid' => $this->currentUser->id(),
      'hours' => number_format((float) $form_state->getValue('hours'), 2, '.', ''),
      'log_date' => $form_state->getValue('log_date'),
      'notes' => $form_state->getValue('notes'),
      'over_estimate_reason' => $isOverEstimate ? trim((string) $form_state->getValue('over_estimate_reason')) : '',
    ];

    $timeLog = $storage->create($timeLogData);
    $timeLog->save();

    $this->messenger()->addStatus($this->t('Successfully logged @hours hours for task.', [
      '@hours' => $timeLogData['hours'],
    ]));

    $form_state->setRedirect('entity.time_log.collection');
  }

}
