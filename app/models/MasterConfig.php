<?php

class MasterConfig extends Modelbase
{

  public $idMasterConfig,
      $diskSpace,
      $idMasteraccount;

  public function initialize() {
    $this->hasMany("idMasterConfig", "DetailConfig", "idMasterConfig");
    $this->hasMany("idMasterConfig", "RechargeHistory", "idMasterConfig");
    $this->hasMany("idMasterConfig", "HistoryPaymentPlan", "idMasterConfig");
    $this->hasOne("idMasteraccount", "Masteraccount", "idMasteraccount");
  }

}
