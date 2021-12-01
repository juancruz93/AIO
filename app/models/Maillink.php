<?php

class Maillink extends Modelbase {

  public $idMailLink,
          $idAccount;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount", array(
        "foreignKey" => true,
    ));

    //$this->hasMany("idMailLink", "Mxl", "idMailLink");
    //$this->hasMany("idMailLink", "Mxcxl", "idMailLink");
  }

  public function getSource() {
    return "mail_link";
  }

}
