<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;

class Adapter extends Modelbase {

    public $idAdapter;
    public $created;
    public $updated;
    public $signal;
    public $usedlr;
    public $fsender;
    public $fixedid;
    public $fname;
    public $prefix;
    public $smscid;
    public $uname;
    public $passw;
    public $coding;
    public $status;
    public $deleted;
    public $createdBy;
    public $updatedBy;
    public $urlIp;
    public $international;

    public function initialize() {
        $this->hasMany("idAdapter", "Accountclassification", "idAdapter");
        $this->hasMany("idAdapter", "Config", "idAdapter");
        $this->hasMany("idAdapter", "Maxadapter", "idAdapter");
        $this->hasOne("idAdapter", "Alliedconfig", "idAdapter");
        $this->hasMany("idAdapter", "PaymentPlanxservice", "idAdapter");
        $this->hasMany("idAdapter", "Ppxsxadapter", "idAdapter");
        $this->hasMany("idAdapter", "Ssrxadapter", "idAdapter");
        $this->hasMany("idAdapter", "smslote", "idAdapter");
    }

    public function validation() {
        //CAMPO FNAME
        $this->validate(new Uniqueness(array(
            'field' => 'fname',
            'message' => 'Ya se encuentra un adaptador con este nombre registrado, por favor valide la información'
        )));

        $this->validate(new PresenceOf(array(
            'field' => 'fname',
            'message' => 'Debe colocar un nombre al adaptador, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'fname',
//      'message' => 'El campo Nombre esta vacio, por favor valide la información'
//    )));
        //CAMPO SIGNAL
        $this->validate(new PresenceOf(array(
            'field' => 'signal',
            'message' => 'El campo Signal es obligatorio, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'signal',
//      'message' => 'El campo Signal esta vacio, por favor valide la información'
//    )));

        $this->validate(new Regex(array(
            'field' => 'signal',
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo Signal debe ser númerico, por favor valide la información'
        )));

        //Campo prefix
        $this->validate(new PresenceOf(array(
            'field' => 'prefix',
            'message' => 'El campo Prefix es obligatorio, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'prefix',
//      'message' => 'El campo Prefix esta vacio, por favor valide la información'
//    )));
        //Campo smscid
        $this->validate(new PresenceOf(array(
            'field' => 'smscid',
            'message' => 'El campo SMSC ID es obligatorio, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'smscid',
//      'message' => 'El campo SMSC ID esta vacio, por favor valide la información'
//    )));
        //Campo usedlr
        $this->validate(new PresenceOf(array(
            'field' => 'usedlr',
            'message' => 'El campo USEDLR es obligatorio, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'usedlr',
//      'message' => 'El campo USEDLR esta vacio, por favor valide la información'
//    )));

        $this->validate(new Regex(array(
            'field' => 'usedlr',
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo USEDLR debe ser númerico, por favor valide la información'
        )));

        //Campo fsender
        $this->validate(new PresenceOf(array(
            'field' => 'fsender',
            'message' => 'El campo Sender es obligatorio, por favor valide la información'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'fsender',
//      'message' => 'El campo Sender esta vacio, por favor valide la información'
//    )));

        $this->validate(new Regex(array(
            'field' => 'fsender',
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo Sender debe ser númerico, por favor valide la información'
        )));

        //Campo fixedid
        $this->validate(new PresenceOf(array(
            'field' => 'fixedid',
            'message' => 'El campo Fixed ID es obligatorio, por favor valide la información'
        )));

        $this->validate(new PresenceOf(array(
            'field' => 'uname',
            'message' => 'El campo nombre de usuario es obligatorio, por favor valida la información.'
        )));

        $this->validate(new PresenceOf(array(
            'field' => 'passw',
            'message' => 'El campo contraseña es obligatorio, por favor valida la información.'
        )));

//    $this->validate(new SpaceValidator(array(
//      'field' => 'fixedid',
//      'message' => 'El campo Fixed ID esta vacio, por favor valide la información'
//    )));

        $this->validate(new Regex(array(
            'field' => 'fixedid',
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo Fixed ID debe ser númerico, por favor valide la información'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
