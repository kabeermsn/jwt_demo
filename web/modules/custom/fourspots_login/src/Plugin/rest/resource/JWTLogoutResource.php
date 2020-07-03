<?php

namespace Drupal\fourspots_login\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
* Provides a resource to get view modes by entity and bundle.
*
* @RestResource(
*   id = "jwtlogout_resource",
*   label = @Translation("Jwtlogout resource"),
*   uri_paths = {
*     "create" = "/logout/jwt"
*   }
* )
*/
class JWTLogoutResource extends ResourceBase {

/**
 * A current user instance.
 *
 * @var \Drupal\Core\Session\AccountProxyInterface
 */
protected $currentUser;

/**
 * [$requestStack description]
 * @var Symfony\Component\HttpFoundation\RequestStack
 */
protected $requestStack;

/**
 * [$fourLoginService description]
 * @var [type]
 */
protected $fourLoginService;

/**
 * {@inheritdoc}
 */
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
  $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
  $instance->logger = $container->get('logger.factory')->get('fourspots_login');
  $instance->currentUser = $container->get('current_user');
  $instance->requestStack = $container->get('request_stack');
  $instance->fourLoginService = $container->get('fourspots_login.login');
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
    // Prevent the use of jwt token on further request after logout
    /*$authHeader = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
    $this->fourLoginService->saveJWTToken($authHeader);*/
    //********************remove if functionality not required**************************//

    $response = [
      'status' => 1,
      'uid' => $this->currentUser->id()
    ];
    return new ModifiedResourceResponse($response, 200);
  }

}
