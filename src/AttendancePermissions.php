<?php

namespace Drupal\attendance;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\attendance\Entity\AttendanceType;

/**
 * Provides per-type dynamic permissions for attendance creation.
 */
class AttendancePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of attendance type permissions.
   *
   * @return array
   *   The attendance types creation permissions.
   */
  public function attendanceTypePermissions() {
    $permissions = [];
    foreach (AttendanceType::loadMultiple() as $type) {
      $type_id = $type->id();
      $permissions["add $type_id attendance entries"] = [
        'title' => $this->t('%type_name: Add attendance entries', ['%type_name' => $type->label()]),
      ];
    }

    return $permissions;
  }

}
