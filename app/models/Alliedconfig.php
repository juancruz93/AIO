<?php

class Alliedconfig extends Modelbase
{

  public $idAlliedconfig,
      $idAllied,
      $emailnotification,
      $diskSpace;

  public function initialize() {
    $this->hasOne("idAllied", "Allied", "idAllied");
    $this->hasMany("idAlliedconfig", "DetailConfig", "idAlliedconfig");
    $this->hasMany("idAlliedconfig", "RechargeHistory", "idAlliedconfig");
    $this->hasMany("idAlliedconfig", "HistoryPaymentPlan", "idAlliedconfig");
  }

}
