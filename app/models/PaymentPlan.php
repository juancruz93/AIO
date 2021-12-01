<?php

class PaymentPlan extends Modelbase {

  public $idPaymentPlan;
  public $idMasteraccount;
  public $idAllied;
  public $idCountry;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $type;
  public $name;
  public $description;
  public $diskSpace;
  public $courtesyplan;
  public $createdBy;
  public $updatedBY;
  public $emailnotification;
  
  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idAlied", "Allied", "idAllied");
    $this->belongsTo("idCountry", "Country", "idCountry");
    $this->hasMany("idPaymentPlan", "PaymentPlanxtax", "idPaymentPlan");
    $this->hasMany("idPaymentPlan", "PaymentPlanxservice", "idPaymentPlan");
    $this->hasMany("idPaymentPlan", "Masteraccount", "idPaymentPlan");
    $this->hasMany("idPaymentPlan", "Allied", "idPaymentPlan");
    $this->hasMany("idPaymentPlan", "HistoryPaymentPlan", "idPaymentPlan");
  }

}
