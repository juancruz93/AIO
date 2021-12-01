<?php

class Dcxmta extends Modelbase
{

  public $idDcxadapter;
  public $idDetailConfig;
  public $idMta;

  public function initialize()
  {
    $this->belongsTo('idDetailConfig', 'DetailConfig', 'idDetailConfig');
    $this->belongsTo('idMta', 'Mta', 'idMta');
  }

}