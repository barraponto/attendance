<?php

namespace Drupal\attendance\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AttendanceTypeForm.
 */
class AttendanceTypeForm extends EntityForm {

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
