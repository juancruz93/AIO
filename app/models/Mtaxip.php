<?php

class Mtaxip extends Modelbase {

  public $idMtaxip,
          $idMta,
          $idIp,
          $created,
          $updated,
          $deleted,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->belongsTo("idMta", "Mta", "idMta");
    $this->belongsTo("idIp", "Ip", "idIp");

    
  }

  public function getSource() {
    return "mtaxip";
  }

}
