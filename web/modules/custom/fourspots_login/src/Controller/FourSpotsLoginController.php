<?php

namespace Drupal\fourspots_login\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\fourspots_login\LoginService;
use Drupal\jwt\Authentication\Provider\JwtAuth;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class TexUserController.
 */
class FourSpotsLoginController extends ControllerBase {

  /**
   * Drupal\fourspots_login\LoginService definition.
   *
   * @var \Drupal\fourspots_login\LoginService
   */
  protected $fourLoginService;

  /**
   * The JWT Auth Service.
   *
   * @var \Drupal\jwt\Authentication\Provider\JwtAuth
   */
  private $auth;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new TexUserController object.
   */
  public function __construct(
    LoginService $fourspots_login_login,
    JwtAuth $auth,
    RequestStack $request_stack
  ) {
    $this->fourLoginService = $fourspots_login_login;
    $this->auth = $auth;
    $this->requestStack = $request_stack;

    $this->currentRequest = $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('fourspots_login.login'),
      $container->get('jwt.authentication.jwt'),
      $container->get('request_stack')
    );
  }

  /**
   * [jwtLoginAction description]
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function jwtLoginAction(Request $request) {
    $parameters = $request->request->all();
    $username = (isset($parameters['username']))? Xss::filter($parameters['username']):'';
    $password = (isset($parameters['password']))? Xss::filter($parameters['password']):'';
    if($username == '') {
      $jsonData = json_decode($this->currentRequest->getContent(), false);
      $username = ($jsonData->username) ? Xss::filter($jsonData->username):'';
      $password = ($jsonData->password) ? Xss::filter($jsonData->password):'';
    }
    $response = $this->fourLoginService->validLoginCredentials($username, $password);
    if($response->status == 1) {
      $token = $this->auth->generateToken();
      if ($token === FALSE) {
        $response->status == 0;
        $response->message = "Error. Please set a key in the JWT admin page.";
        return new JsonResponse($response, 500);
      }

      $response->token = $token;
    }
    return new JsonResponse($response);
  }

  /**
   * [jwtLogoutAction description]
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function jwtLogoutAction(Request $request) {
    $auth_header = $request->headers->get('Authorization');
    $this->fourLoginService->saveJWTToken($auth_header);

    $response = new \stdClass();
    $response->status = 1;
    $response->message = 'successfully logged out';
    return new JsonResponse($response);
  }

}
