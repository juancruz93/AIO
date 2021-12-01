<?php

class Dcxdkim extends Modelbase
{

  public $idDcxDkim;
  public $name_public_key;
  public $type_public_key;
  public $value_public_key;
  public $status;
  public $idDetailConfig;
  public $domain;
  public function initialize()
  {
    $this->belongsTo('idDetailConfig', 'DetailConfig', 'idDetailConfig');
  }

}