<?php

namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Client;
use App\Models\Entity\Invoice;

/**
 * Controller v1 de Importação
 */
class ImportController {

    /**
     * Container Class
     * @var [object]
     */
    private $container;

    /**
     * Indexs da tabela cliente no CSV
     */
    private $clientFields;

    /**
     * Indexs da tabela fatura no CSV
     */
    private $invoiceFields;


    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container) {

        $this->container = $container;
        
        // Defini os index da tabela cliente no CSV
        $this->clientFields = [
            'cpf_cnpj' => 0,
            'name' => 1,
            'email' => 2,
        ];

        // Defini os index da tabela fatura no CSV
        $this->invoiceFields = [
            'date_due' => 3,
            'total' => 4,
        ];
    }


    /**
     * Cria uma empresa
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function importCSV($request, $response, $args) {

        $logger = $this->container->get('logger');

        //Extrair dados de cliente e fatura do CSV
        if($this->extractClientAndInvoiceFromCSV()){
            $data['msg'] = 'success';
            $status = 200;
            
            //Registra a importação no log
            $logger->info('Import CSV SUCCESS');
        }else{  
            $data['msg'] = 'error';
            $status = 409;
            
            //Registra a importação no log
            $logger->info('Import CSV ERROR');
        }
        
        $return = $response->withJson($data, $status)
            ->withHeader('Content-type', 'application/json');
        return $return;       
    }

    
    /**
     * Extrai os dados de Cliente e Fatura do CSV
     * @return void
     */
    public function extractClientAndInvoiceFromCSV(){
        //Valida se existe o arquivo
        if(file_exists('./public/upload/faturas.csv')){
            $csvFile = array_map('str_getcsv', file('./public/upload/faturas.csv'));
            // Remover Header do CSV
            array_shift($csvFile);
            
            foreach ($csvFile as $row) {
                //Inserir cliente no banco de dados
                $client = $this->insertClientInDB($row);
                //Inserir fatura no banco de dados
                $this->insertInvoiceInDB($row, $client);
            }

            $return = true;
        }else{
            $return = false;
        }
        
        return $return;
    }

    
    /**
     * Extrai os dados de Cliente da linha do CSV
     * com base na variavel clientFields
     * @return void
     */
    public function insertClientInDB($row){
        
        $entityManager = $this->container->get('em');
        $clientsRepository = $entityManager->getRepository('App\Models\Entity\Client');
        
        //Defini os campos do cliente
        $cpf_cnpj = $row[$this->clientFields['cpf_cnpj']];
        $name = $row[$this->clientFields['name']];
        $email = $row[$this->clientFields['email']];

        $client = $clientsRepository->findOneBy(['cpf_cnpj' => $cpf_cnpj]);
        //Valida se o cliente já está cadastrado no banco de dados
        if (!$client) {
            $newClient = (new Client)
                ->setCPF_CNPJ($cpf_cnpj)
                ->setName($name)
                ->setEmail($email);
            //Persistir dados
            $entityManager->persist($newClient);
            $entityManager->flush();
            //Registra a criação do cliente
            $logger = $this->container->get('logger');
            $logger->info('Client Created!', $newClient->getValues());
            
            $client = $newClient; 
        }else{
            $client = $client;
        }

        return $client;
    }

    
    /**
     * Extrai os dados de Fatura da linha do CSV
     * com base na variavel invoiceFields
     * @return void
     */
    public function insertInvoiceInDB($row, $client){
        
        $entityManager = $this->container->get('em');
        $invoicesRepository = $entityManager->getRepository('App\Models\Entity\Invoice');
        $clientsRepository = $entityManager->getRepository('App\Models\Entity\Client');
        
        //Defini os campos da fatura
        $date_due = $row[$this->invoiceFields['date_due']];
        $total = $row[$this->invoiceFields['total']];
        
        //Valida se a fatura já está cadastrada no banco de dados
        $where = [
            'client_id' => $client->getId(),
            'date_due' => $date_due,
            'total' => $total,
        ];
        $invoice = $invoicesRepository->findOneBy($where);
        
        if (!$invoice) {
            $newInvoice = (new Invoice)
                ->setClientID($client)
                ->setDateDue($date_due)
                ->setTotal($total);
            
            //Persistir dados
            $entityManager->persist($newInvoice);
            $entityManager->flush();
            
            //Registra a criação da fatura
            $logger = $this->container->get('logger');
            $logger->info('Invoice Created!', $newInvoice->getValues());
        }
    }
}