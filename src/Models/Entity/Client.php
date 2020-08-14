<?php

namespace App\Models\Entity;


/**
 * @Entity @Table(name="clients")
 **/
class Client {

    /**
     * @var int
     * @Id @Column(type="integer") 
     * @GeneratedValue
     */
    public $id;

    /**
     * @var string
     * @Column(type="string") 
     */
    public $cpf_cpnj;


    public function getId(){
        return $this->id;
    }

    public function getCPF_CNPJ(){
        return $this->$cpf_cpnj;
    }

    public function setCPF_CNPJ($cpf_cpnj){
        $this->cpf_cpnj = $cpf_cpnj;
        return $this;  
    }
}