<?php

class Cxa extends Modelbase {

  public  $idCxa,
          $idAccount,
          $idCountry,
          $status;

  public function initialize() {
   $this->hasMany("idCountry", "Country", "idCountry");
   $this->belongsTo("idAccount", "Account", "idAccount");
  }

}
