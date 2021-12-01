<?php

class Maxmta extends Modelbase
{

  public $idMaxmta,
      $idMta,
      $idMasteraccount,
      $created,
      $updated;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idMta", "Mta", "idMta");
  }

}
