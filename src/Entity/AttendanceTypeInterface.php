<?php

namespace Drupal\attendance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Attendance type entities.
 */
interface AttendanceTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the target bundles for attendance.
   *
   * @return array
   *   The target bundles (node types) ids.
   */
  public function getTargetBundles();

}
