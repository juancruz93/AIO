<?php

class Ip extends Modelbase {

  public $idIp,
          $ip,
          $status,
          $created,
          $updated,
          $deleted,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->hasMany("idIp", "Mtaxip", "idIp");
  }

  public function getSource() {
    return "ip";
  }

}
