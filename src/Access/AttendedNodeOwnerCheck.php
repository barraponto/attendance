<?php

namespace Drupal\attendance\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Determines access to attendance reports.
 */
class AttendedNodeOwnerCheck implements AccessInterface {

  /**
   * Checks whether current user owns attended node (from path arguments).
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   * @param \Drupal\node\NodeInterface $node
   *   The attended node from route parameters.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $node) {
    $allowed = ($account->id() == 1) || ($node->getOwnerId() == $account->id());
    return $allowed ? AccessResult::allowed() : AccessResult::forbidden();
  }

}
