<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

use App\Models\Entity\Company;

/**
 * Controller de Autenticação
 */
class AuthController {

    /**
     * Container Class
     * @var [object]
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

    $entityManager = $this->container->get('em');
    $companiesRepository = $entityManager->getRepository('App\Models\Entity\Company');

    //Pegar o salt da Company
    $salt = (new Company())->getSalt();
    
    //Validar se a empresa existe
    $companyName = $request->getParam('name');
    $companyPassword =  sha1($request->getParam('password').$salt);

    $company = $companiesRepository->findOneBy(['name' => $companyName, 'password' => $companyPassword]);

    if(!$company){
        return $response->withJson(['msg' => 'Incorrect Login'], 401)
        ->withHeader('Content-type', 'application/json');   
    }

    //Gerar chave JWT
    $key = $this->container->get("secretkey");

    $token = array(
        "user" => $companyName,
        "exp"   => time() + 30 * 60 // expira em 30 min
    );

    $jwt = JWT::encode($token, $key);

    $data['msg'] = 'success';
    $data['auth-jwt'] = $jwt;

    return $response->withJson($data, 200)
        ->withHeader('Content-type', 'application/json');   
   }
}