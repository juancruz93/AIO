<?php

use \Phalcon\Mvc\Model as Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Mta extends Model {

    public $idMta, $name, $description, $status, $deleted, $created, $updated;

    public function initialize() {
        $this->hasMany("idMta", "Accountclassification", "idMta");
        $this->hasMany("idMta", "Config", "idMta");
        $this->hasMany("idMta", "Maxmta", "idMta");
        $this->hasOne("idMta", "Alliedconfig", "idMta");
        $this->hasMany("idMta", "Ppxsxmta", "idMta");
        $this->hasMany("idMta", "Mtaxip", "idMta");        
    }

    public function validation() {
        $this->validate(new PresenceOf(array(
            'field' => 'name',
            'message' => 'El nombre del MTA es obligatorio, por favor valide la información'
        )));

        $this->validate(new PresenceOf(array(
            'field' => 'description',
            'message' => 'El MTA debe tener una descripción, por favor valide la información'
        )));

        $this->validate(new Uniqueness(array(
            'field' => 'name',
            'message' => 'El MTA ya existe, por favor valide la información'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    public function beforeValidationOnCreate() {
        $this->created = time();
        $this->updated = time();
        $this->deleted = 0;

        if (isset($this->name)) {
            $this->name = mb_strtolower($this->name, 'UTF-8');
            $this->name = strtoupper ($this->name);
        }

        if (isset($this->description)) {
            $this->description = (empty($this->description) ? "Sin descripción" : $this->description);
        }

        $email = "Indefinido";
        if (isset($this->createdBy)) {
            $email = $this->createdBy;
        }
        if (\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) {
            $email = \Phalcon\DI::getDefault()->get('user')->email;
        }
        $this->createdBy = $email;
        $this->updatedBy = $email;
        if (isset($this->email)) {
            $this->email = strtolower($this->email);
        }
    }

    public function beforeValidationOnUpdate() {
        $this->updated = time();
        $email = ((\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
        $this->updatedBy = $email;
        
        if (isset($this->description)) {
            $this->description = (empty($this->description) ? "Sin descripción" : $this->description);
        }

        if (isset($this->name)) {
            $this->name = mb_strtolower($this->name, 'UTF-8');
            $this->name = strtoupper ($this->name);
        }

        $this->updatedBy = $email;
    }

}
