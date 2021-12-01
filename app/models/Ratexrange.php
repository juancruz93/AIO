<?php

class Ratexrange extends Modelbase {
  
  public  $idRatexRange,
          $idRate,
          $idRange,
          $created,
          $updated,
          $deleted,
          $createdBy, 
          $updatedBy;
  
  public function initialize() {
    $this->hasMany("idRate", "Rate", "idRate");    
    $this->hasMany("idRange", "Range", "idRange");
  }
}
