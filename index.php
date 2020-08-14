<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';

$app = new \Slim\App;

/**
 * Instalação do Slim
 * @var string
 */
$app->get('/', function (Request $request, Response $response) use ($app) {
    $response->getBody()->write("Desafio Finnet!");
    return $response;
});


$app->run();