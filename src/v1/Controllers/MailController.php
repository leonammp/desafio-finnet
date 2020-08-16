<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
     * PHPMailer Class
     * @var [object]
     */
    private $mail;


    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container) {
        $this->container = $container;

        $smtp = $this->container['smtp'];
        //Configurar SMTP
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->Host = $smtp['host'];
        $this->mail->Username = $smtp['username'];
        $this->mail->Password = $smtp['password'];
        $this->mail->Port = $smtp['port'];
        $this->mail->SMTPSecure = 'tls';
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
        $this->mail->Encoding = 'base64';
        //Definir Headers 
        $this->mail->setFrom('info@mailtrap.io', 'Mailtrap');
        $this->mail->addReplyTo('info@mailtrap.io', 'Mailtrap');
    }

    
    /**
     * Enviar email
     * @param [object] $mailContent
     * @param string $subject
     * @param string $to
     * @param string $name
     * @return bool
     */
    public function sendEmail($mailContent, $subject, $to, $name) {

        $this->mail->Subject = $subject;
        $this->mail->Body = $mailContent;
        $this->mail->addAddress($to, $name);

        //Registra o envio do email
        $logger = $this->container->get('logger');

        if($this->mail->send()){
            $logger->info("Nofitication email sent to {$to}!");
            $return = true;
        }else{
            $logger->info("Nofitication email error to {$to}! {$this->mail->ErrorInfo}");
            $return = false;
        }
        
        return $return;  
    }
}