<?php

class RechargeHistory extends \Modelbase {

  public $idRechargeHistory,
      $idMasterConfig,
      $idAccountConfig,
      $idAlliedconfig,
      $idServices,
      $rechargeAmount,
      $initialAmount,
      $initialTotal;

  public function getSource() {
    return "recharge_history";
  }

  public function initialize() {
    $this->belongsTo("idMasterConfig", "MasterConfig", "idMasterConfig");
    $this->belongsTo("idAccountConfig", "AccountConfig", "idAccountConfig");
    $this->belongsTo("idAlliedconfig", "Alliedconfig", "idAlliedconfig");
  }

}
