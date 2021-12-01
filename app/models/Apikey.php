<?php

class Apikey extends Modelbase {

  public $idApikey,
      $idUser,
      $apikey,
      $secret,
      $status;

  public function initialize() {
    $this->belongsTo("idUser", "User", "idUser");
  }

}
