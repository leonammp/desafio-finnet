<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    /**
     * Dentro de v1, o recurso /
     */
    $this->group('/', function() {
        $this->get('', function (Request $request, Response $response) use ($app) {
            $logger = $this->get('logger');
            $logger->info('Request Log /');

            $data = [
                "msg" => "Desafio Finnet"
            ];
            $return = $response->withJson($data, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    });

    /**
     * Dentro de v1, o recurso /auth
     */
    $this->group('/auth', function() {
        $this->get('', \App\v1\Controllers\AuthController::class);
    });
});