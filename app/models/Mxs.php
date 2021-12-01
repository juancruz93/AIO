<?php

class Mxs extends Modelbase
{

  public $idMasteraccount;
  public $idServices;

  public function initialize() {
    $this->belongsTo('idMasteraccount', 'Masteraccount', 'idMasteraccount');
    $this->belongsTo('idServices', 'Services', 'idServices');
  }

}
