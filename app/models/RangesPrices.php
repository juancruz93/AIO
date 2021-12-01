<?php

class RangesPrices extends Modelbase {
  
  public  $idRangesPrices,
          $idServices,
          $quantity,
          $unitValue,
          $totalValue,
          $accountingMode;
  
  public function initialize() {  
    $this->belongsTo("idServices", "Services", "idServices");
  }
}
