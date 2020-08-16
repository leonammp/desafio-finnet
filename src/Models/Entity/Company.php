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
     * @Column(type="string", unique=true) 
     */
    public $name;

    /**
     * @var string
     * @Column(type="string") 
     */
    public $password;

    /**
     * Password Salt
     */
    private $salt = '^Lr8uF%Xat5$';


    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getSalt(){
        return $this->salt;
    }

    public function setName($name){
        $this->name = $name;
        return $this;  
    }

    public function setPassword($password){
        $this->password = sha1($password.$this->salt);
        return $this;  
    }

    /**
     * @return App\Models\Entity\Company
     */
    public function getValues() {
        $values = get_object_vars($this);
        //Remover password e salt
        unset($values['password']);
        unset($values['salt']);
        return $values;
    }
}