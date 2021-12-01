<?php

class DashboardImage extends Modelbase {

  public $idDashboardImage,
          $idAccount,
          $name,
          $size,
          $contentType,
          $dimensions,
          $extensions,
          $created,
          $updated,
          $updatedBy,
          $createdBys;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
  }
  
  public function gerSource(){
    return "dashboard_image";
  }
}
