<?php

namespace Drupal\attendance;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Attendance entity.
 *
 * @see \Drupal\attendance\Entity\Attendance.
 */
class AttendanceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\attendance\Entity\AttendanceInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished attendance entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published attendance entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit attendance entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete attendance entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add attendance entities');
  }

}
