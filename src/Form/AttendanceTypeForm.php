<?php

namespace Drupal\attendance\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AttendanceTypeForm.
 */
class AttendanceTypeForm extends EntityForm {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * Constructs an AttendanceTypeForm object.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The Node Type entity storage.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager
   *   The Node Type entity storage.
   */
  public function __construct(EntityStorageInterface $entity_storage, EntityFieldManagerInterface $field_manager) {
    $this->entityStorage = $entity_storage;
    $this->fieldManager = $field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('node_type'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $attendance_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $attendance_type->label(),
      '#description' => $this->t("Label for the Attendance type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $attendance_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\attendance\Entity\AttendanceType::load',
      ],
      '#disabled' => !$attendance_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    $node_type_options = [];
    $node_types = $this->entityStorage->loadMultiple();
    foreach ($node_types as $type) {
      $node_type_options[$type->id()] = $type->label();
    }

    $form['node_type'] = [
      '#title' => $this->t('Node type this attendance bundle supports.'),
      '#type' => 'select',
      '#options' => $node_type_options,
      '#default_value' => $attendance_type->node_type,
    ];

    $field_options = [];
    $fields = $this->fieldManager->getFieldMapByFieldType('geofield');
    if (isset($fields['node'])) {
      foreach ($fields['node'] as $field_name => $field_info) {
        $field_options[$field_name] = $this->t('@field (used in %bundles)', [
          '@field' => $field_name,
          '%bundles' => implode(', ', $field_info['bundles']),
        ]);
      }
    }
    else {
      $this->messenger()->addWarning($this->t('No instance of a geofield was found. Geofencing will be disabled.'));
    }

    $form['field'] = [
      '#title' => $this->t('Field used to geofence the attendance block.'),
      '#type' => 'select',
      '#options' => $field_options,
      '#default_value' => $attendance_type->field,
    ];

    $form['distance'] = [
      '#title' => $this->t('Distance threshold to allow registrations.'),
      '#type' => 'number',
      '#field_suffix' => $this->t('miles'),
      '#description' => $this->t('A value of zero will disable geofencing.'),
      '#default_value' => $attendance_type->distance,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $attendance_type = $this->entity;
    $status = $attendance_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Attendance type.', [
          '%label' => $attendance_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Attendance type.', [
          '%label' => $attendance_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($attendance_type->toUrl('collection'));
  }

}
