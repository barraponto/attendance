<?php

namespace Drupal\attendance\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Attendance entity.
 *
 * @ingroup attendance
 *
 * @ContentEntityType(
 *   id = "attendance",
 *   label = @Translation("Attendance"),
 *   bundle_label = @Translation("Attendance type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\attendance\AttendanceListBuilder",
 *     "views_data" = "Drupal\attendance\Entity\AttendanceViewsData",
 *     "translation" = "Drupal\attendance\AttendanceTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\attendance\Form\AttendanceForm",
 *       "add" = "Drupal\attendance\Form\AttendanceForm",
 *       "edit" = "Drupal\attendance\Form\AttendanceForm",
 *       "delete" = "Drupal\attendance\Form\AttendanceDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\attendance\AttendanceHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\attendance\AttendanceAccessControlHandler",
 *   },
 *   base_table = "attendance",
 *   data_table = "attendance_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer attendance entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "owner" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/attendance/{attendance}",
 *     "add-page" = "/admin/content/attendance/add",
 *     "add-form" = "/admin/content/attendance/add/{attendance_type}",
 *     "edit-form" = "/admin/content/attendance/{attendance}/edit",
 *     "delete-form" = "/admin/content/attendance/{attendance}/delete",
 *     "collection" = "/admin/content/attendance",
 *   },
 *   bundle_entity_type = "attendance_type",
 *   field_ui_base_route = "entity.attendance_type.edit_form"
 * )
 */
class Attendance extends ContentEntityBase implements AttendanceInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    // Add the owner field.
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['attends'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Node ID'))
      ->setSetting('target_type', 'node')
      ->setTranslatable($entity_type->isTranslatable())
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_entity_id',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields[$entity_type->getKey('owner')]
      ->setDescription(t('The attendee user ID .'))
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('Attendee email'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Attendance info is public.'))
      ->setLabel(new TranslatableMarkup('Public'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
