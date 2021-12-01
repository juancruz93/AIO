<?php

class Dcxurldomain extends Modelbase
{

  public $idDcxurldomain;
  public $idDetailConfig;
  public $idUrldomain;

  public function initialize()
  {
    $this->belongsTo('idDetailConfig', 'DetailConfig', 'idDetailConfig');
    $this->belongsTo('idUrldomain', 'Urldomain', 'idUrldomain');
  }

}