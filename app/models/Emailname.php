<?php

class Emailname extends Modelbase {

  public $idEmailname,
          $idAccount,
          $name,
          $status;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idEmailname", "Mail", "idEmailname");
  }

//  public function getSource() {
//    return "asd";
//  }

}
