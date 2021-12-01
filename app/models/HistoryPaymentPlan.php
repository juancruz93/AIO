<?php

class HistoryPaymentPlan extends \Modelbase {

  public $idHistoryPaymentPlan,
      $idMasterConfig,
      $idAccountConfig,
      $idAlliedconfig,
      $idPaymentPlan,
      $services,
      $mailClass,
      $adapter,
      $urlDomain,
      $amountSms,
      $amountMail,
      $totalAmountMail,
      $totalAmountSms,
      $mta;

  public function getSource() {
    return "history_payment_plan";
  }

  public function initialize() {
    $this->belongsTo("idMasterConfig", "MasterConfig", "idMasterConfig");
    $this->belongsTo("idAccountConfig", "AccountConfig", "idAccountConfig");
    $this->belongsTo("idAlliedconfig", "Alliedconfig", "idAlliedconfig");
    $this->belongsTo("idPaymentPlan", "PaymentPlan", "idPaymentPlan");
  }

}
