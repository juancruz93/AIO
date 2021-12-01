<?php

use Phalcon\Mvc\Model\Validator\PresenceOf;

class Sendingcategory extends Modelbase
{
    public $idAccount;
    
    public function initialize()
    {
        $this->belongsTo("idAccount", "Account", "idAccount");
    }
    
    public function validation()
    {
        $this->validate(new PresenceOf(array(
            'field' => 'name',
            'message' => 'El nombre de la categoria es obligatorio, por favor valide la información'
        )));
        
        $this->validate(new PresenceOf(array(
            'field' => 'description',
            'message' => 'La categoria debe tener una descripción, por favor valide la información'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
