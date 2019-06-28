<?php

namespace Drupal\attendance\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Attendance entities.
 */
class AttendanceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
