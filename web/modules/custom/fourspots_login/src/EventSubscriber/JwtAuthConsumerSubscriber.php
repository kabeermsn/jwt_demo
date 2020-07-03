<?php

namespace Drupal\fourspots_login\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jwt\Authentication\Event\JwtAuthEvents;
use Drupal\jwt\Authentication\Event\JwtAuthValidEvent;
use Drupal\jwt\Authentication\Event\JwtAuthValidateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class JwtAuthConsumerSubscriber.
 *
 * @package Drupal\fourspots_login
 */
class JwtAuthConsumerSubscriber implements EventSubscriberInterface {

  /**
   * A User Interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Symfony\Component\HttpFoundation\Request definition.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_manager,
    RequestStack $request_stack
  ) {
    $this->entityManager = $entity_manager;
    $this->requestStack = $request_stack;

    $this->currentRequest = $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[JwtAuthEvents::VALIDATE][] = array('validate');
    $events[JwtAuthEvents::VALID][] = array('loadUser');

    return $events;
  }

  /**
   * Validates that a uid is present in the JWT.
   *
   * This validates the format of the JWT and validate the uid is a
   * valid uid in the system.
   *
   * @param \Drupal\jwt\Authentication\Event\JwtAuthValidateEvent $event
   *   A JwtAuth event.
   */
  public function validate(JwtAuthValidateEvent $event) {
    if($this->isTokenBlackListed()) {
      $event->invalidate("JWT token logged out");
    }

    $token = $event->getToken();
    $userName = $token->getClaim(['drupal', 'uid']);
    if ($userName === NULL) {
      $event->invalidate("No Drupal uid was provided in the JWT payload.");
    }
    $users = $this->entityManager->getStorage('user')->loadByProperties(['name' => $userName, 'status' => 1]);
    if (count($users) == 0) {
      $event->invalidate("No UID exists.");
    }
  }

  /**
   * Load and set a Drupal user to be authentication based on the JWT's uid.
   *
   * @param \Drupal\jwt\Authentication\Event\JwtAuthValidEvent $event
   *   A JwtAuth event.
   */
  public function loadUser(JwtAuthValidEvent $event) {
    $token = $event->getToken();
    $user_storage = $this->entityManager->getStorage('user');
    $userName = $token->getClaim(['drupal', 'uid']);
    $users = $user_storage->loadByProperties(['name' => $userName, 'status' => 1]);
    if(count($users)) {
      $user = reset($users);
      $event->setUser($user);
    }
  }

  /**
   * [isTokenBlackListed description]
   * @return boolean          [description]
   */
  protected function isTokenBlackListed() {
    $auth_header = $this->currentRequest->headers->get('Authorization');

    $jwtBlackListEntities = $this->entityManager->getStorage('jwt_logout_entity')->loadByProperties([
      'name' => $auth_header
    ]);
    return count($jwtBlackListEntities) ? 1:0;
  }

}
