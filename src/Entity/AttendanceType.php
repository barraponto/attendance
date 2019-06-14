<?php

namespace Drupal\attendance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Attendance type entity.
 *
 * @ConfigEntityType(
 *   id = "attendance_type",
 *   label = @Translation("Attendance type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\attendance\AttendanceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\attendance\Form\AttendanceTypeForm",
 *       "edit" = "Drupal\attendance\Form\AttendanceTypeForm",
 *       "delete" = "Drupal\attendance\Form\AttendanceTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\attendance\AttendanceTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "attendance_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "attendance",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *      "id",
 *      "label",
 *      "target_bundle",
 *      "target_field",
 *      "distance",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/attendance_type/{attendance_type}",
 *     "add-form" = "/admin/structure/attendance_type/add",
 *     "edit-form" = "/admin/structure/attendance_type/{attendance_type}/edit",
 *     "delete-form" = "/admin/structure/attendance_type/{attendance_type}/delete",
 *     "collection" = "/admin/structure/attendance_type"
 *   }
 * )
 */
class AttendanceType extends ConfigEntityBundleBase implements AttendanceTypeInterface {

  /**
   * The Attendance type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Attendance type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Attendance type target bundle.
   *
   * @var string
   */
  protected $target_bundle;

  /**
   * The Attendance type target field for geofencing.
   *
   * @var string
   */
  protected $target_field;

  /**
   * The Attendance type geofencing distance limit.
   *
   * @var string
   */
  protected $distance;

  public function getTargetBundle() {
    return $this->target_bundle;
  }

  public function getTargetField() {
    return $this->target_field;
  }

  public function getDistance() {
    return $this->distance;
  }

}
