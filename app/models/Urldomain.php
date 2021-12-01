<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Url;

class Urldomain extends Modelbase {

    public $idUrldomain;

    public function initialize() {
        $this->hasMany("idUrldomain", "Accountclassification", "idUrldomain");
        $this->hasMany("idUrldomain", "Config", "idUrldomain");
        $this->hasMany("idUrldomain", "Maxurldomain", "idUrldomain");
        $this->hasOne("idUrldomain", "Alliedconfig", "idUrldomain");
        $this->hasMany("idUrldomain", "Ppxsxurldomain", "idUrldomain");
    }

    public function validation() {
        $this->validate(new PresenceOf(array(
            'field' => 'name',
            'message' => 'La URL es obligatoria, por favor valida la información.'
        )));

        $this->validate(new PresenceOf(array(
            'field' => 'description',
            'message' => 'La URL debe tener una descripción, por favor valida la información.'
        )));

        $this->validate(new Uniqueness(array(
            'field' => 'name',
            'message' => 'La URL ya existe, por favor valida la información.'
        )));

        $this->validate(new Url(array(
            'field' => 'name',
            'message' => 'La URL es invalida, por favor valida la información.'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
