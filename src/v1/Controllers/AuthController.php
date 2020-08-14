<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firebase\JWT\JWT;

/**
 * Controller de Autenticação
 */
class AuthController {

    /**
     * Container
     * @var object s
     */
   protected $container;
   
   /**
    * Undocumented function
    * @param ContainerInterface $container
    */
   public function __construct($container) {
       $this->container = $container;
   }
   
   /**
    * Invokable Method
    * @param Request $request
    * @param Response $response
    * @param [type] $args
    * @return void
    */
   public function __invoke(Request $request, Response $response, $args) {

    /**
     * JWT Key
     */
    $key = $this->container->get("secretkey");

    $token = array(
        "user" => "desafio-finnet",
        "exp"   => time() + 30 * 60 // 30 min
    );

    $jwt = JWT::encode($token, $key);

    $data['msg'] = 'success';
    $data['auth-jwt'] = $jwt;

    return $response->withJson($data, 200)
        ->withHeader('Content-type', 'application/json');   
   }
}