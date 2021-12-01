<?php

class Rate extends Modelbase {
  
  public  $idRate,
          $idAllied,
          $idServices,
          $name,
          $description,
          $accountingMode,
          $countries,
          $dateInitial,
          $dateEnd,
          $status,
          $created,
          $updated,
          $deleted,
          $createdBy, 
          $updatedBy;
  
  public function initialize() {
    $this->belongsTo("idAllied", "Allied", "idAllied");    
    $this->belongsTo("idServices", "Services", "idServices");
    $this->hasMany("idRate", "Ratextange", "idRate");
  }
}
