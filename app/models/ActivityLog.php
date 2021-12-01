<?php

class ActivityLog extends Modelbase {

  public $idActivityLog;
  public $idUser;
  public $idServices;
  public $amount;
  public $dataTime;
  public $description;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idUser", "User", "idUser");
    $this->belongsTo("idServices", "Services", "idServices");
  }

}
