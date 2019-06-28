<?php

namespace Drupal\attendance;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Attendance entity.
 *
 * @see \Drupal\attendance\Entity\Attendance.
 */
class AttendanceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * Helper to check permissions generally or for owned attended nodes.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to check access.
   * @param string $permission
   *   The permission for entity operations.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  private function allowIfAnyOrOwnPermission(AccountInterface $account, EntityInterface $entity, $permission) {
    $node = $entity->get('attends')->entity;
    return AccessResult::allowedIfHasPermission($account, $permission)
      ->orIf(
        AccessResult::allowedIfHasPermission($account, $permission . ' for own node')
          ->andIf(AccessResult::allowedIf($node->getOwnerId() == $account->id()))
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\attendance\Entity\AttendanceInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return $this->allowIfAnyOrOwnPermission($account, $entity, 'view unpublished attendance entities');
        }
        return $this->allowIfAnyOrOwnPermission($account, $entity, 'view published attendance entities');

      case 'update':
        return $this->allowIfAnyOrOwnPermission($account, $entity, 'edit attendance entities');

      case 'delete':
        return $this->allowIfAnyOrOwnPermission($account, $entity, 'delete attendance entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, "add $entity_bundle attendance entries");
  }

}
