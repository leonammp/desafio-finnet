<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    /**
     * Dentro de v1, registrar um novo login de empresa
     */
    $this->group('/company', function(){
        $this->post('', 'App\v1\Controllers\CompanyController:createCompany');
        
        //Validando se tem um integer no final da URL
        $this->get('/{id:[0-9]+}/', 'App\v1\Controllers\CompanyController:viewCompany');
    });

    /**
     * Dentro de v1, login
     */
    $this->group('/login', function() {
        $this->post('', \App\v1\Controllers\AuthController::class);
    });
});