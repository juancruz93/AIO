<?php

class Alxs extends Modelbase
{

  public $idAllied;
  public $idServices;

  public function initialize() {
    $this->belongsTo('idAllied', 'Allied', 'idAllied');
    $this->belongsTo('idServices', 'Services', 'idServices');
  }

}
