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
    public $cpf_cnpj;

    /**
     * @var string
     * @Column(type="string") 
     */
    public $name;

    /**
     * @var string
     * @Column(type="string") 
     */
    public $email;


    public function getId(){
        return $this->id;
    }

    public function getCPF_CNPJ(){
        return $this->cpf_cnpj;
    }

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setCPF_CNPJ($cpf_cnpj){
        $this->cpf_cnpj = $cpf_cnpj;
        return $this;  
    }

    public function setName($name){
        $this->name = $name;
        return $this;  
    }

    public function setEmail($email){
        $this->email = $email;
        return $this;  
    }

    /**
     * @return App\Models\Entity\Client
     */
    public function getValues() {
        return get_object_vars($this);
    }
}