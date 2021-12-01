<?php

class PaymentPlanxservice extends Modelbase{
  
  public $idPaymentPlanxService;
  public $idPaymentPlan;
  public $idPlanType;
  public $idServices;
  public $idPriceList;
  public $created;
  public $updated;
  public $status;
  public $amount;
  public $amountQuestion;
  public $amountAnswer;
  public $speed;
  public $accountingMode;
  public $createdBy;
  public $updatedBy;
  
  public function initialize(){
    $this->belongsTo("idPaymentPlan", "PaymentPlan", "idPaymentPlan");
    $this->belongsTo("idPlanType", "PlanType", "idPlanType");
    $this->belongsTo("idServices", "Services", "idServices");
    $this->belongsTo("idPriceList", "PriceList", "idPriceList");
    $this->hasMany("idPaymentPlanxService", "Ppxsxadapter", "idPaymentPlanxService");
    $this->hasMany("idPaymentPlanxService", "PpxsxMailClass", "idPaymentPlanxService");
    $this->hasMany("idPaymentPlanxService", "Ppxsxmta", "idPaymentPlanxService");
    $this->hasMany("idPaymentPlanxService", "Ppxsxurldomain", "idPaymentPlanxService");
  }
}
