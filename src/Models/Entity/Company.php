<?php

namespace App\Models\Entity;


/**
 * @Entity @Table(name="companies")
 **/
class Company {

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
    public $name;

    /**
     * @var string
     * @Column(type="string") 
     */
    public $password;


    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->$name;
    }

    public function getPassword(){
        return $this->$password;
    }

    public function setName($name){
        $this->name = $name;
        return $this;  
    }

    public function setPassword($password){
        $this->password = $password;
        return $this;  
    }

    /**
     * @return App\Models\Entity\Company
     */
    public function getValues() {
        return get_object_vars($this);
    }

}