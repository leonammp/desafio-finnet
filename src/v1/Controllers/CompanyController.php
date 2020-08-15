<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Company;
use App\Models\Entity\Invoice;
use App\Models\Entity\Client;

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
        $data['data'] = $company;

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
        $data['data'] = $company; 

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

        $entityManager = $this->container->get('em');
        $clientsRepository = $entityManager->getRepository('App\Models\Entity\Client');
        $invoicesRepository = $entityManager->getRepository('App\Models\Entity\Invoice');
        
        $return = [];
        
        //Procurar todas as faturas
        $invoices = $invoicesRepository->findAll();
        
        foreach ($invoices as $invoice) {
            //Pegar nome do cliente para ser a chave do dicionario
            $client_name = $invoice->getClientID()->getName();
            
            //Se não existir o cliente no return, adiciona ele
            if (!isset($return[$client_name])) {
                $return[$client_name] = [];
            }
            
            $invoice_values = [
                'date_due' => $invoice->getDateDue(),
                'total' => $invoice->getTotal(),
            ];

            //Adiciona a fatura ao cliente
            array_push($return[$client_name], $invoice_values);
        }

        //Registra a consulta feita
        $logger = $this->container->get('logger');
        $logger->info('Invoices Checked!');

        $data['msg'] = 'success';
        $data['data'] = $return;

        $return = $response->withJson($data, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}