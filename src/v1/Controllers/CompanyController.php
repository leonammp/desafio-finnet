<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Company;

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
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $company = (new Company())->setName($params->name)
            ->setPassword($params->password);
        
        /**
         * Registra a criação da empresa
         */
        $logger = $this->container->get('logger');
        $logger->info('Company Created!', $company->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($company);
        $entityManager->flush();
        $return = $response->withJson($company, 201)
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

        /**
         * Verifica se existe uma empresa com o ID informado
         */
        if (!$company) {
            $logger = $this->container->get('logger');
            $logger->warning("Company {$id} Not Found");
            throw new \Exception("Company not Found", 404);
        }    

        $return = $response->withJson($company, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }
}