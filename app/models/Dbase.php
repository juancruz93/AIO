<?php

use Phalcon\Mvc\Model\Validator\PresenceOf;

class Dbase extends Modelbase {
    public $idAccount;
    public $idDbase;
    
    public function initialize() {
        $this->belongsTo("idAccount", "Account", "idAccount");
        $this->hasMany("idDbase", "Contactlist", "idDbase");
    }
    
    public function validation() {
        $this->validate(new PresenceOf(array(
            'field' => 'name',
            'message' => 'Debe colocar un nombre a la base de datos, por favor valide la información'
        )));
        
        $this->validate(new SpaceValidator(array(
            'field' => 'name',
            'message' => 'El campo Nombre esta vacio, por favor valide la información'
        )));
    }   
}
