<?php

use Phalcon\Mvc\Model as Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Mailclass extends Modelbase {

  public $idMailClass;

  public function initialize() {
    $this->hasMany("idMailClass", "Accountclassification", "idMailClass");
    $this->hasMany("idMailClass", "Config", "idMailClass");
    $this->hasOne("idMailClass", "Alliedconfig", "idMailClass");
    $this->hasMany("idMailClass", "Maxmailclass", "idMailClass");
    $this->hasMany("idMailClass", "PaymentPlanxservice", "idMailClass");
    $this->hasMany("idMailClass", "Mailclass", "idMailClass");
  }

  public function validation() {
    //Campo name
    $this->validate(new PresenceOf(array(
        'field' => 'name',
        'message' => 'Debe colocar un nombre a la nueva plataforma, por favor valide la información'
    )));

    $this->validate(new Uniqueness(array(
        'field' => 'name',
        'message' => 'Ya existe una Mail Class registrada con ese nombre, por favor valide la información'
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
