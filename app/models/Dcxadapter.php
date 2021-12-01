<?php

class Dcxadapter extends Modelbase
{

  public $idDcxadapter;
  public $idDetailConfig;
  public $idAdapter;

  public function initialize()
  {
    $this->belongsTo('idDetailConfig', 'DetailConfig', 'idDetailConfig');
    $this->belongsTo('idAdapter', 'Adapter', 'idAdapter');
  }

}