<?php

class State extends Modelbase
{

  public $idState,
      $idCountry,
      $name;

  public function initialize() {
    $this->hasMany("idState", "City", "idState");
    $this->belongsTo("idCountry", "Country", "idCountry");
  }

}
