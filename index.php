<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

// Incluindo o arquivo de configuração de dependências e autoload
require "bootstrap.php";

/**
 * Rotas da API
 */
require 'routes.php';

$app->run();