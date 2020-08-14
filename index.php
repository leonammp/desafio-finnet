<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

// Incluindo o arquivo de configuração de dependências e autoload
require "bootstrap.php";

/**
 * Instalação do Slim
 * @var string
 */
$app->get('/', function (Request $request, Response $response) use ($app) {
    $logger = $this->get('logger');
    $logger->info('Request Log /');

    $data = [
        "msg" => "Desafio Finnet"
    ];
    $return = $response->withJson($data, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Auth JWT - Autenticação para retornar um JWT
 */
$app->get('/auth', function (Request $request, Response $response) use ($app) {

    $key = $this->get("secretkey");

    $token = array(
        "user" => "desafio-finnet",
        //"exp"   => time() + 30 * 60 // 30 min
    );

    $jwt = JWT::encode($token, $key);

    return $response->withJson(["msg" => "success",     "auth-jwt" => $jwt], 200)
        ->withHeader('Content-type', 'application/json');   
});

$app->run();