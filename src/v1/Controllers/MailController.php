<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Company;

/**
 * Controller v1 de Email
 */
class MailController {

    /**
     * Container Class
     * @var [object]
     */
    private $container;

    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * Enviar email
     * @param [object] $data
     * @param string $to
     * @return void
     */
    public function sendMail($data, $to) {
        

        /**
         * Registra o envio do email
         */
        $logger = $this->container->get('logger');
        $logger->info('Email OK!');

               
    }
}