<?php

require './vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Psr7Middlewares\Middleware\TrailingSlash;
use Monolog\Logger;
use Firebase\JWT\JWT;

/**
 * Configurações
 */
$configs = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

/**
 * Container Resources do Slim.
 * Para carregar todas as dependências
 */
$container = new \Slim\Container($configs);

/**
 * Converte os Exceptions entro da Aplicação em respostas JSON
 */
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $statusCode = $exception->getCode() ? $exception->getCode() : 500;
        return $c['response']->withStatus($statusCode)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(["msg" => $exception->getMessage()], $statusCode);
    };
};

/**
 * Converte os Exceptions de Erros 405 - Not Allowed
 */
$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'Application/json')
            ->withHeader("Access-Control-Allow-Methods", implode(",", $methods))
            ->withJson(["msg" => "Method not Allowed; Method must be one of: " . implode(', ', $methods)], 405);
    };
};

/**
 * Converte os Exceptions de Erros 404 - Not Found
 */
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(['msg' => 'Page not found']);
    };
};


$isDevMode = true;
/**
 * Diretório de Entidades e Metadata do Doctrine
 */
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src/Models/Entity"), $isDevMode);

/**
 * Array de configurações da conexão com o banco
 */
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

/**
 * Instância do Entity Manager
 */
$entityManager = EntityManager::create($conn, $config);

/**
 * Coloca o Entity manager dentro do container com o nome de em (Entity Manager)
 */
$container['em'] = $entityManager;

/**
 * Serviço de Logging em Arquivo
 */
$container['logger'] = function($container) {
    $logger = new Monolog\Logger('desafio-finnet');
    $logfile = __DIR__ . '/log/desafio-finnet.log';
    $stream = new Monolog\Handler\StreamHandler($logfile, Monolog\Logger::DEBUG);
    $fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
        $stream, Monolog\Logger::INFO);
    $logger->pushHandler($fingersCrossed);
    
    return $logger;
};

/**
 * Serviço de EMAIL
 */
$container['smtp'] = [
    'host' => 'smtp.mailtrap.io',
    'port' => '2525',
    'username' => 'eae8ae766b08cd',
    'password' => '29cb50c79d9850',
];

/**
 * Token JWT
 */
$container['secretkey'] = "askjdfnQQWPDamdnoprodmvb";

$app = new \Slim\App($container);   

/**
 * @Middleware Tratamento da / do Request 
 * true - Adiciona a / no final da URL
 * false - Remove a / no final da URL
 */
$app->add(new TrailingSlash(false));

/**
 * Auth do JWT
 */
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "regexp" => "/(.*)/", //Regex para encontrar o Token nos Headers
    "header" => "X-Token", //O Header que vai conter o token
    "path" => "/", //Cobrir toda a API a partir do /
    "ignore" => ["/v1/login", "/v1/company", "/v1/importCSV", "/v1/sendEmails"], //Adicionar a exceção de cobertura 
    "secret" => $container['secretkey'], //Nosso secretkey criado
    "secure" => false, //Não validar se é HTTPS
    "error" => function ($response, $arguments) {
        $data["msg"] = $arguments["message"];
        return $response->withJson($data, 401)
            ->withHeader("Content-Type", "application/json");
    }
]));