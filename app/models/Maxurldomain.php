<?php

class Maxurldomain extends Modelbase
{

  public $idMaxurldomain,
      $idMasteraccount,
      $idUrldomain,
      $created,
      $updated;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idUrldomain", "Urldomain", "idUrldomain");
  }

}
