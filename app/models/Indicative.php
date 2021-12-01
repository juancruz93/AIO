<?php

class Indicative extends Modelbase {

  public $idIndicative,
          $name,
          $phonecode;

  public function initialize() {
    $this->hasMany("idIndicative", "Ssrxadapter", "idIndicative");
  }

}
