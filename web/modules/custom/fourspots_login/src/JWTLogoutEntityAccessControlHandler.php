<?php

namespace Drupal\fourspots_login;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Jwtlogout entity entity.
 *
 * @see \Drupal\fourspots_login\Entity\JWTLogoutEntity.
 */
class JWTLogoutEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\fourspots_login\Entity\JWTLogoutEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished jwtlogout entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published jwtlogout entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit jwtlogout entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete jwtlogout entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add jwtlogout entity entities');
  }

}
