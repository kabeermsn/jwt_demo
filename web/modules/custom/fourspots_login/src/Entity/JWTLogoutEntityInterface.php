<?php

namespace Drupal\fourspots_login\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Jwtlogout entity entities.
 *
 * @ingroup fourspots_login
 */
interface JWTLogoutEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Jwtlogout entity name.
   *
   * @return string
   *   Name of the Jwtlogout entity.
   */
  public function getName();

  /**
   * Sets the Jwtlogout entity name.
   *
   * @param string $name
   *   The Jwtlogout entity name.
   *
   * @return \Drupal\fourspots_login\Entity\JWTLogoutEntityInterface
   *   The called Jwtlogout entity entity.
   */
  public function setName($name);

  /**
   * Gets the Jwtlogout entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Jwtlogout entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Jwtlogout entity creation timestamp.
   *
   * @param int $timestamp
   *   The Jwtlogout entity creation timestamp.
   *
   * @return \Drupal\fourspots_login\Entity\JWTLogoutEntityInterface
   *   The called Jwtlogout entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Jwtlogout entity published status indicator.
   *
   * Unpublished Jwtlogout entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Jwtlogout entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Jwtlogout entity.
   *
   * @param bool $published
   *   TRUE to set this Jwtlogout entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\fourspots_login\Entity\JWTLogoutEntityInterface
   *   The called Jwtlogout entity entity.
   */
  public function setPublished($published);

}
