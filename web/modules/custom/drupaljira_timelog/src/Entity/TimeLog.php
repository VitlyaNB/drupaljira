<?php

namespace Drupal\drupaljira_timelog\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\drupaljira_timelog\Form\TimeLogForm;
use Drupal\drupaljira_timelog\TimeLogListBuilder;

/**
 * Defines the TimeLog entity class.
 */
#[ContentEntityType(
  id: 'time_log',
  label: new TranslatableMarkup('Time Log'),
  label_collection: new TranslatableMarkup('Time Logs'),
  base_table: 'time_log',
  admin_permission: 'administer time log entities',
  translatable: FALSE,
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'owner' => 'uid',
    'label' => 'id'
  ],
  handlers: [
    'view_builder' => EntityViewBuilder::class,
    'list_builder' => TimeLogListBuilder::class,
    'views_data' => 'Drupal\views\EntityViewsData',
    'access' => EntityAccessControlHandler::class,
    'form' => [
      'default' => TimeLogForm::class,
      'add' => TimeLogForm::class,
      'edit' => TimeLogForm::class,
      'delete' => ContentEntityDeleteForm::class,
    ],
    'route_provider' => [
      'html' => AdminHtmlRouteProvider::class,
    ],
  ],
  links: [
    'canonical' => '/admin/structure/time-log/{time_log}',
    'add-form' => '/admin/structure/time-log/add',
    'edit-form' => '/admin/structure/time-log/{time_log}/edit',
    'delete-form' => '/admin/structure/time-log/{time_log}/delete',
    'collection' => '/admin/structure/time-log',
  ]
)]
class TimeLog extends ContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(
    EntityTypeInterface $entity_type,
  ): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    // 1. Связь с задачей (Task reference) - ОБЯЗАТЕЛЬНОЕ ПОЛЕ.
    $fields['task'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Task'))
      ->setDescription(
        new TranslatableMarkup('The task this time log belongs to.')
      )
      ->setSetting('target_type', 'node')
      ->setSetting(
        'handler_settings',
        ['target_bundles' => ['task' => 'task']],
      )
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 2. Автор списания (User reference) - ОБЯЗАТЕЛЬНОЕ ПОЛЕ.
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('User'))
      ->setDescription(
        new TranslatableMarkup('The user who logged the time.')
      )
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getCurrentUserId')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -5,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 3. Часы (Hours) - Decimal, ОБЯЗАТЕЛЬНОЕ ПОЛЕ.
    $fields['hours'] = BaseFieldDefinition::create('decimal')
      ->setLabel(new TranslatableMarkup('Hours'))
      ->setDescription(
        new TranslatableMarkup('The number of hours spent.')
      )
      ->setSetting('precision', 10)
      ->setSetting('scale', 2)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 4. Дата списания (Log Date) - ОБЯЗАТЕЛЬНОЕ ПОЛЕ.
    $fields['log_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(new TranslatableMarkup('Log Date'))
      ->setDescription(
        new TranslatableMarkup('The date of time spent.')
      )
      ->setSetting('datetime_type', 'date')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 5,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 5. Заметки (Notes) - Необязательное текстовое поле.
    $fields['notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Notes'))
      ->setDescription(
        new TranslatableMarkup('Arbitrary notes for the time log.')
      )
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'basic_string',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 6. Причина превышения оценки (Over estimate reason) - Необязательное.
    $fields['over_estimate_reason'] = BaseFieldDefinition::create(
      'string_long',
    )
      ->setLabel(new TranslatableMarkup('Over Estimate Reason'))
      ->setDescription(
        new TranslatableMarkup('Reason for exceeding the estimate.')
      )
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 15,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'basic_string',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Системное поле времени изменения.
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(
        new TranslatableMarkup('The time entity was last edited.')
      );

    return $fields;
  }

  /**
   * Helper callback for current user ID default value.
   *
   * @return array
   *   An array containing the current user ID.
   */
  public static function getCurrentUserId(): array {
    return [\Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(
    EntityStorageInterface $storage,
    array &$values,
  ): void {
    parent::preCreate($storage, $values);
    if (empty($values['uid'])) {
      $values['uid'] = \Drupal::currentUser()->id();
    }
  }

}
