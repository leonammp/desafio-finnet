<?php

namespace App\Models\Entity;


/**
 * @Entity @Table(name="invoices")
 **/
class Invoice {

    /**
     * @var int
     * @Id @Column(type="integer") 
     * @GeneratedValue
     */
    public $id;
    
    /**
     * @ManyToOne(targetEntity="Client")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    public $client_id;
     
    /**
     * @var string
     * @Column(type="string") 
     */
    public $date_due;

    /**
     * @var float
     * @Column(type="decimal", precision=10, scale=2) 
     */
    public $total;


    public function getId(){
        return $this->id;
    }

    public function getClientID(){
        return $this->client_id;
    }

    public function getDateDue(){
        return $this->date_due;
    }

    public function getTotal(){
        return $this->total;
    }

    public function setClientID($client_id){
        $this->client_id = $client_id;
        return $this;  
    }

    public function setDateDue($date_due){
        $this->date_due = $date_due;
        return $this;  
    }

    public function setTotal($total){
        $this->total = $total;
        return $this;  
    }

    /**
     * @return App\Models\Entity\Invoice
     */
    public function getValues() {
        return get_object_vars($this);
    }
}