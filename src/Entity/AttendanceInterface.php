<?php

namespace Drupal\attendance\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Attendance entities.
 *
 * @ingroup attendance
 */
interface AttendanceInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Attendance name.
   *
   * @return string
   *   Name of the Attendance.
   */
  public function getName();

  /**
   * Sets the Attendance name.
   *
   * @param string $name
   *   The Attendance name.
   *
   * @return \Drupal\attendance\Entity\AttendanceInterface
   *   The called Attendance entity.
   */
  public function setName($name);

  /**
   * Gets the Attendance creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Attendance.
   */
  public function getCreatedTime();

  /**
   * Sets the Attendance creation timestamp.
   *
   * @param int $timestamp
   *   The Attendance creation timestamp.
   *
   * @return \Drupal\attendance\Entity\AttendanceInterface
   *   The called Attendance entity.
   */
  public function setCreatedTime($timestamp);


}
