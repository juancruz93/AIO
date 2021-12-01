<?php

class Smstwowaypostnotify extends Modelbase {

  public $idSmsTwoWayPostNot,
          $idSubaccount,
          $url,
          $password,
          $created,
          $updated,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
  }
  
  public function getSource() {
    return "sms_two_way_post_notify";
  }

}
