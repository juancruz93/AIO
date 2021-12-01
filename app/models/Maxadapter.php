<?php

class Maxadapter extends Modelbase
{

  public $idMaxadapter,
      $idMasteraccount,
      $idAdapter,
      $created,
      $updated;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
  }

}
