<?php

class DetailConfig extends Modelbase
{

  public $idDetailConfig,
      $idMasterConfig,
      $idAlliedconfig,
      $idAccountConfig,
      $idSubaccountConfig,
      $idPlanType,
      $idServices,
      $idPriceList,
      $status,
      $amount,
      $amountQuestion,
      $amountAnswer,
      $totalAmountQuestion,
      $totalAmountAnswer,
      $speed,
      $accountingMode,
      $totalAmount;

  public function initialize()
  {
    $this->hasMany("idDetailConfig", "Dcxmta", "idDetailConfig");
    $this->hasMany("idDetailConfig", "Dcxmailclass", "idDetailConfig");
    $this->hasMany("idDetailConfig", "Dcxadapter", "idDetailConfig");
    $this->hasMany("idDetailConfig", "Dcxurldomain", "idDetailConfig");
    $this->belongsTo('idMasterConfig', 'MasterConfig', 'idMasterConfig');
    $this->belongsTo('idAlliedconfig', 'Alliedconfig', 'idAlliedconfig');
    $this->belongsTo('idAccountConfig', 'AccountConfig', 'idAccountConfig');
    $this->belongsTo('idPlanType', 'PlanType', 'idPlanType');
    $this->belongsTo('idServices', 'Services', 'idServices');
    $this->belongsTo('idPriceList', 'PriceList', 'idPriceList');
  }

}
