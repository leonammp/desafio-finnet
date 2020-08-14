<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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


$app->run();