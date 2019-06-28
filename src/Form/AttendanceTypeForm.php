<?php

namespace Drupal\attendance\Form;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\Entity\BaseFieldOverride;
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
  protected $entityFieldManager;

  /**
   * Constructs an AttendanceTypeForm object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The Node Type entity storage.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The Entity field manager.
   */
  public function __construct(EntityStorageInterface $entity_storage, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityStorage = $entity_storage;
    $this->entityFieldManager = $entity_field_manager;
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

    $node_type_options = [];
    $node_types = $this->entityStorage->loadMultiple();
    foreach ($node_types as $type) {
      $node_type_options[$type->id()] = $type->label();
    }

    $form['target_bundles'] = [
      '#title' => $this->t('Node types this attendance bundle supports.'),
      '#type' => 'checkboxes',
      '#options' => $node_type_options,
      '#default_value' => $attendance_type->getTargetBundles() ?: [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $attendance_type = $this->entity;

    $definitions = $this->entityFieldManager->getFieldDefinitions('attendance', $attendance_type->id());
    if ($definitions['attends'] instanceof BaseFieldOverride) {
      $override = $definitions['attends'];
    }
    else {
      $override = BaseFieldOverride::createFromBaseFieldDefinition($definitions['attends'], $attendance_type->id());
    }
    $override->setSetting('handler_settings', ['target_bundles' => $attendance_type->getTargetBundles()]);
    $override->save();

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
