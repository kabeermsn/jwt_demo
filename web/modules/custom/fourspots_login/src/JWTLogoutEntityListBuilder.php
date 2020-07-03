<?php

namespace Drupal\fourspots_login;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Jwtlogout entity entities.
 *
 * @ingroup fourspots_login
 */
class JWTLogoutEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Jwtlogout entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\fourspots_login\Entity\JWTLogoutEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.jwt_logout_entity.edit_form',
      ['jwt_logout_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
