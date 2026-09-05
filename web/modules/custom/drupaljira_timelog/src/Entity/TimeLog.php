<?php

namespace Drupal\drupaljira_timelog\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityChangedTrait;
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
    'label' => 'id',
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
  ],
)]
class TimeLog extends ContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['task'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Task'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler_settings', [
        'target_bundles' => ['task' => 'task'],
      ])
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('User'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getCurrentUserId')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['hours'] = BaseFieldDefinition::create('decimal')
      ->setLabel(new TranslatableMarkup('Hours'))
      ->setSetting('precision', 6)
      ->setSetting('scale', 2)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['log_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(new TranslatableMarkup('Log Date'))
      ->setSetting('datetime_type', 'date')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Notes'))
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['over_estimate_reason'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Over Estimate Reason'))
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'));

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field.
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId(): array {
    return [\Drupal::currentUser()->id()];
  }

}
