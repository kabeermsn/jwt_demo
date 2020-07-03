<?php

namespace Drupal\fourspots_login\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Jwtlogout entity entities.
 */
class JWTLogoutEntityViewsData extends EntityViewsData {

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
