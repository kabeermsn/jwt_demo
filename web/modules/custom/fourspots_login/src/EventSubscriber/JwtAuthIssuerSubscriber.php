<?php

namespace Drupal\fourspots_login\EventSubscriber;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jwt\Authentication\Event\JwtAuthEvents;
use Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class JwtAuthIssuerSubscriber.
 *
 * @package Drupal\fourspots_login
 */
class JwtAuthIssuerSubscriber implements EventSubscriberInterface {

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
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    RequestStack $request_stack
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;

    $this->currentRequest = $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[JwtAuthEvents::GENERATE][] = ['setStandardClaims', 100];
    $events[JwtAuthEvents::GENERATE][] = ['setDrupalClaims', 99];
    return $events;
  }

  /**
   * Sets the standard claims set for a JWT.
   *
   * @param \Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent $event
   *   The event.
   */
  public function setStandardClaims(JwtAuthGenerateEvent $event) {
    $event->addClaim('iat', time());
    $event->addClaim('exp', strtotime('+1 month'));
  }

  /**
   * Sets claims for a Drupal consumer on the JWT.
   *
   * @param \Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent $event
   *   The event.
   */
  public function setDrupalClaims(JwtAuthGenerateEvent $event) {
    $username = $this->currentRequest->headers->get('PHP_AUTH_USER');
    if($username) {
      $accounts = $this->entityTypeManager->getStorage('user')->loadByProperties(['name' => $username, 'status' => 1]);
      if(count($accounts)) {
        $event->addClaim(
          ['drupal', 'uid'],
          $username
        );
      }
    }
  }
}
