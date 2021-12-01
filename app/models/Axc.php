<?php

class Axc extends Modelbase
{

  public $idAccount;
  public $idServices;

  public function initialize() {
    $this->belongsTo('idAccount', 'Account', 'idAccount');
    $this->belongsTo('idServices', 'Services', 'idServices');
  }

  public function beforeValidationOnCreate() {
    $this->created = time();
  }

  public function beforeValidationOnUpdate() {
    $this->updated = time();
  }

}
