<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Company;
use App\Models\Entity\Invoice;
use App\Models\Entity\Client;

use App\v1\Controllers\MailController;

/**
 * Controller v1 de Empresas
 */
class CompanyController {

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
     * Cria uma empresa
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createCompany($request, $response, $args) {
        
        $params = (object) $request->getParams();
        
        //Pega o Entity Manager do Container
        $entityManager = $this->container->get('em');
        $company = (new Company())
            ->setName($params->name)
            ->setPassword($params->password);
                
        //Registra a criação da empresa
        $logger = $this->container->get('logger');
        $logger->info('Company Created!', $company->getValues());

        //Persiste a entidade no banco de dados
        $entityManager->persist($company);
        $entityManager->flush();

        $data['msg'] = 'success';
        $data['data'] = $company->getValues();

        $return = $response->withJson($data, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;       
    }


    /**
     * Exibe as informações de uma empresa 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewCompany($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $companiesRepository = $entityManager->getRepository('App\Models\Entity\Company');
        $company = $companiesRepository->find($id); 

        //Verifica se existe uma empresa com o ID informado
        if (!$company) {
            $logger = $this->container->get('logger');
            $logger->warning("Company {$id} Not Found");
            throw new \Exception("Company not Found", 404);
        }

        $data['msg'] = 'success';
        $data['data'] = $company->getValues(); 

        $return = $response->withJson($data, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }


    /**
     * Exibe as informações das faturas 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewInvoices($request, $response, $args) {

        //Pegar as informações consolidadas dos clientes e faturas
        $clientsAndInvoices = $this->generateClientsAndInvoices();

        //Registra a consulta feita
        $logger = $this->container->get('logger');
        $logger->info('Invoices Checked!');

        $data['msg'] = 'success';
        $data['data'] = $clientsAndInvoices;

        $return = $response->withJson($data, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }


    /**
     * Envia o email de aviso das faturas para os clientes 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function sendEmailNotification($request, $response, $args) {

        $entityManager = $this->container->get('em');
        $clientsRepository = $entityManager->getRepository('App\Models\Entity\Client');
        $invoicesRepository = $entityManager->getRepository('App\Models\Entity\Invoice');
        
        //Pegar as informações consolidadas dos clientes e faturas 
        $clientsAndInvoices = $this->generateClientsAndInvoices();

        //Pegar apenas os 5 primeiros clientes para não acabar a cota do MailTrap hehehe :)
        $clientsAndInvoices = array_slice($clientsAndInvoices, 0, 5);

        foreach ($clientsAndInvoices as $clientCPF_CNPJ => $clientValues) {
            $clientName = $clientValues['info']['name'];
            $clientEmail = $clientValues['info']['email'];

            $invoicesHTML = '';
            //Criar html das faturas
            foreach ($clientValues['invoices'] as $key => $invoice){
                $invoicesHTML .= "
                    <br/> Fatura #{$key}
                    <br/> Data de vencimento: {$invoice['date_due']} 
                    <br/> Total da fatura: R$ {$invoice['total']}
                    <br/>
                ";
            }
            //Criar corpo do html
            $emailBody = "
                <html>
                    <body>
                        Olá, {$clientName}! <br/> <br/>
                        Suas faturas já estão consolidadas e prontas para serem pagas! <br/>
                        {$invoicesHTML} <br/>
                        Atenciosamente, <br/>
                        Finnet.
                    </body>    
                </html>
            ";

            //Enviar email para o cliente
            $subject = 'Suas faturas estão prontas para serem pagas!';
            (new MailController($this->container))
                ->sendEmail($emailBody, $subject, $clientEmail, $clientName);
        }

        //Registra a consulta feita
        $logger = $this->container->get('logger');
        $logger->info('Notification emails sent!');

        $data['msg'] = 'success';

        $return = $response->withJson($data, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }


    /**
     * Gera um dicionario com os clietes e suas faturas 
     * @return array
     */
    public function generateClientsAndInvoices(){

        $entityManager = $this->container->get('em');
        $clientsRepository = $entityManager->getRepository('App\Models\Entity\Client');
        $invoicesRepository = $entityManager->getRepository('App\Models\Entity\Invoice');
        
        $return = [];
        
        //Procurar todas as faturas
        $invoices = $invoicesRepository->findAll();
        
        foreach ($invoices as $invoice) {
            //Pegar cpf_cnpj do cliente para ser a chave do dicionario
            $clientCPF_CNPJ = $invoice->getClientID()->getCPF_CNPJ();
            
            //Se não existir o cliente no return, adiciona ele
            if (!isset($return[$clientCPF_CNPJ])) {
                $return[$clientCPF_CNPJ] = [
                    //Informações do cliente
                    'info' => [
                        'name' =>  $invoice->getClientID()->getName(),
                        'email' =>  $invoice->getClientID()->getEmail(),
                    ], 
                    //Faturas do cliente
                    'invoices' => [],
                ];

            } 
            
            $invoice_values = [
                'date_due' => $invoice->getDateDue(),
                'total' => $invoice->getTotal(),
            ];

            //Adiciona a fatura ao cliente
            array_push($return[$clientCPF_CNPJ]['invoices'], $invoice_values);
        }

        return $return;
    }
}