<?php

class Sxs extends Modelbase
{

  public $idSubaccount;
  public $idServices;

  public function initialize() {
    $this->belongsTo('idSubaccount', 'Subaccount', 'idSubaccount');
    $this->belongsTo('idServices', 'Services', 'idServices');
  }

}
