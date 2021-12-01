<?php

class Saxs extends Modelbase
{

  public $idSaxs,
      $idSubaccount,
      $idServices,
      $accountingMode,
      $amount,
      $status,
      $amountQuestion,
      $amountAnswer,
      $totalAmountQuestion,
      $totalAmountAnswer,
      $totalAmount,
      $diskSpace;

  public function initialize() {
    $this->hasOne("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idServices", "Services", "idServices");
  }

}
