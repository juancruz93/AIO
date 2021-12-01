<?php

class Dashboard extends Modelbase {

  public $idDashboard,
          $idAccount,
          $content,
          $created,
          $updated,
          $updatedBy,
          $createdBy;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
  }
  
  public function gerSource(){
    return "dashboard";
  }
}