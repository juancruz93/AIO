<?php

class AccountConfig extends Modelbase
{
  public $idAccountConfig,
      $idAccount,
      $diskSpace,
      $idFooter,
      $senderAllowed,
      $footerEditable,
      $expiryDate;

  public function initialize()
  {
    $this->hasOne("idAccount", "Account", "idAccount");
    $this->hasMany("idAccountConfig", "DetailConfig", "idAccountConfig");
    $this->hasMany("idAccountConfig", "RechargeHistory", "idAccountConfig");
    $this->hasMany("idAccountConfig", "HistoryPaymentPlan", "idAccountConfig");
    $this->belongsTo("idFooter", "Footer", "idFooter");
  }
}