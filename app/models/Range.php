<?php

class Range extends Modelbase {
  
  public  $idRange,
          $since,
          $until,
          $space,
          $value,
          $created,
          $updated,
          $deleted,
          $createdBy, 
          $updatedBy;
  
  public function initialize() {  
    $this->hasMany("idRange", "Ratextange", "idRange");
  }
}
