<?php

namespace Drupal\fourspots_login\Plugin\rest\resource;

use Drupal\Component\Utility\Xss;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* Provides a resource to get view modes by entity and bundle.
*
* @RestResource(
*   id = "jwtlogin_resource",
*   label = @Translation("Jwt login resource"),
*   uri_paths = {
*     "create" = "/login/jwt"
*   }
* )
*/
class JWTLoginResource extends ResourceBase {

/**
 * A current user instance.
 *
 * @var \Drupal\Core\Session\AccountProxyInterface
 */
protected $currentUser;

/**
 * [$jwtAuth description]
 * @var \Drupal\jwt\Authentication\Provider\JwtAuth
 */
protected $jwtAuth;

/**
 * Drupal\user\UserAuthInterface definition.
 *
 * @var Drupal\user\UserAuthInterface
 */
protected $userAuth;

/**
 * {@inheritdoc}
 */
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
  $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
  $instance->logger = $container->get('logger.factory')->get('fourspots_login');
  $instance->currentUser = $container->get('current_user');
  $instance->jwtAuth = $container->get('jwt.authentication.jwt');
  $instance->userAuth = $container->get('user.auth');
  return $instance;
}

  /**
   * Responds to POST requests.
   *
   * @param string $payload
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($payload) {
    $token = $this->jwtAuth->generateToken();
    return new ModifiedResourceResponse([
      'status' => 1,
      'token' => $token
    ], 200);
  }
}
