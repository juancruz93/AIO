<?php

class City extends Modelbase {

  public $idCity,
      $idState,
      $idCountry,
      $name;

  public function initialize() {
    $this->hasMany("idCity", "Masteraccount", "idCity");
    $this->hasMany("idCity", "Allied", "idCity");
    $this->hasMany("idCity", "User", "idCity");
    $this->hasMany("idCity", "Account", "idCity");
    $this->hasMany("idCity", "Subaccount", "idCity");
    $this->belongsTo("idState", "State", "idState");
  }

}
