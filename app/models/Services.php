<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Services extends Modelbase {

    public $idServices;
    public $created;
    public $updated;
    public $name;
    public $deleted;
    public $description;
    public $createdBy;
    public $updateBy;

    public function initialize() {
        $this->hasMany("idServices", "Axc", "idServices");
        $this->hasMany("idServices", "Mxs", "idServices");
        $this->hasMany("idServices", "Alxs", "idServices");
        $this->hasMany("idServices", "Sxs", "idServices");
        $this->hasMany("idServices", "PriceList", "idServices");
        $this->hasMany("idServices", "PaymentPlanxservice", "idServices");
        $this->hasMany("idServices", "Services", "idServices");
        $this->hasMany("idServices", "Rate", "idServices");
    }

    public function validation() {
        //Campo name
        $this->validate(new PresenceOf(array(
            'field' => 'name',
            'message' => 'Debe colocar un nombre a la nueva plataforma, por favor valide la información'
        )));

        $this->validate(new Uniqueness(array(
            'field' => 'name',
            'message' => 'Ya existe una plataforma registrada con ese nombre, por favor valide la información'
        )));

        //Campo signal
        $this->validate(new PresenceOf(array(
            'field' => 'description',
            'message' => 'La plataforma debe tener una descripción, por favor valide la información'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
