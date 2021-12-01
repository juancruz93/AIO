<?php

class Dcxmailclass extends Modelbase
{

  public $idDcxmailclass;
  public $idMailClass;
  public $idDetailConfig;

  public function initialize()
  {
    $this->belongsTo('idMailClass', 'Mailclass', 'idMailClass');
    $this->belongsTo('idDetailConfig', 'DetailConfig', 'idDetailConfig');
  }

}