<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    //login
    $this->group('/login', function() {
        $this->post('', \App\v1\Controllers\AuthController::class);
    });

    //rotas de empresa
    $this->group('/company', function(){
        $this->post('', 'App\v1\Controllers\CompanyController:createCompany');
        $this->get('', 'App\v1\Controllers\CompanyController:viewInvoices');
        //Validando se tem um integer no final da URL
        $this->get('/{id:[0-9]+}/', 'App\v1\Controllers\CompanyController:viewCompany');
    });

    //importar CSV
    $this->get('/importCSV', 'App\v1\Controllers\ImportController:importCSV');

    //visualizar Faturas
    $this->get('/invoices', 'App\v1\Controllers\CompanyController:viewInvoices');

    //enviar email de notificação
    $this->get('/sendEmails', 'App\v1\Controllers\CompanyController:sendEmailNotification');

});