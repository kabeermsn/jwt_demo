<?php

namespace Drupal\fourspots_login;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Flood\FloodInterface;
use Drupal\user\UserAuthInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class LoginService.
 */
class LoginService {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Flood\FloodInterface definition.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

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
   * Drupal\user\UserAuthInterface definition.
   *
   * @var Drupal\user\UserAuthInterface
   */
  protected $userAuth;

  /**
   * Drupal\Core\Extension\ModuleHandlerInterface definition.
   *
   * @var Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a new LoginService object.
   */



  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    FloodInterface $flood,
    ConfigFactoryInterface $config,
    RequestStack $request_stack,
    UserAuthInterface $user_auth,
    ModuleHandlerInterface $module_handler
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->flood = $flood;
    $this->config = $config;
    $this->requestStack = $request_stack;
    $this->userAuth = $user_auth;
    $this->moduleHandler = $module_handler;


    $this->currentRequest = $this->requestStack->getCurrentRequest();
  }

  /**
   * [validLoginCredentials description]
   * @param  [type] $userName [description]
   * @param  [type] $password [description]
   * @return [type]           [description]
   */
  public function validLoginCredentials($userName, $password) {
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    $response = new \stdClass();
    $userStorage = $this->entityTypeManager->getStorage('user');
    $password = trim($password);
    $flood_config = $this->config->get('user.flood');

    if (!$this->flood->isAllowed('user.failed_login_ip', $flood_config->get('ip_limit'), $flood_config->get('ip_window'))) {
      $response->status = 0;
      $response->message = t('Ip Blocked');
      return $response;
    }

    $accounts = $userStorage->loadByProperties(['name' => $userName, 'status' => 1]);
    $account = reset($accounts);
    if ($account) {
      if ($flood_config->get('uid_only')) {
        $identifier = $account->id();
      }
      else {
        $identifier = $account->id() . '-' . $this->currentRequest->getClientIP();
      }

      if (!$this->flood->isAllowed('user.failed_login_user', $flood_config->get('user_limit'), $flood_config->get('user_window'), $identifier)) {
        $response->status = 0;
        $response->message = t('User Blocked');
        return $response;
      }
    }
    else {
      $accounts = $userStorage->loadByProperties(['name' => $userName]);
      if(count($accounts)) {
        $response->status = 0;
        $response->message = t('User is inactive');
        return $response;
      }
      else {
        $response->status = 0;
        $response->message = t('No user Found');
        return $response;
      }
    }



    $uid = $this->userAuth->authenticate($userName, $password);
    if($uid) {
      $this->flood->clear('user.failed_login_user', $identifier);
      $response->status = 1;
      $response->uid = $uid;
      $response->userdata = [
        'uid' => $uid,
        'email' => $account->getEmail(),
        'roles' => $account->getRoles(true),
        'name' => $account->getUsername(),
        'uuid' => $account->uuid(),
        // 'admin' => ($account->get('field_admin')->value)? $account->get('field_admin')->value:0,
        // 'img_url' => ($account->user_picture->entity)? $account->user_picture->entity->createFileUrl():$base_url.'/'.$this->moduleHandler->getModule('fourspots_login')->getPath().'/assets/images/profile-default.png'
      ];
      if($account->hasField('field_customer_group')) {
        $response->userdata['customer_group_id'] = ($account->get('field_customer_group')->entity)? $account->get('field_customer_group')->entity->id():'';
      }
      return $response;
    }
    else {
      $this->flood->register('user.failed_login_ip', $flood_config->get('ip_window'));
      $this->flood->register('user.failed_login_user', $flood_config->get('user_window'), $identifier);

      $response->status = 0;
      if($this->checkIsImportedUser($account->id()))
        $response->message = t('invalid credentials, Your password has reset by system update, please use forget password link');
      else
        $response->message = t('invalid credentials');

      return $response;

    }
  }

  public function saveJWTToken($token) {
    $jwtLogEntity = $this->entityTypeManager->getStorage('jwt_logout_entity');
    $jwtLogEntity->create([
      'name' => $token
    ])->save();
  }

  /**
   * [checkIsImportedUser Check user created through import (For TAM EXPRESS PROJECT)]
   * @return [type] [description]
   */
  private function checkIsImportedUser($uid) {
    $importedUser = 0;
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    if($user->hasField('field_import_user') && $user->field_import_user->value)
      $importedUser = 1;
    return $importedUser;
  }
}
