<?php

class Maxmailclass extends Modelbase
{

  public $idMaxmailclass,
      $idMailClass,
      $idMasteraccount,
      $created,
      $updated;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idMailClass", "Mailclass", "idMailClass");
  }

}
