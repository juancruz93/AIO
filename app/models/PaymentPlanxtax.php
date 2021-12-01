<?php

class PaymentPlanxtax extends Modelbase {

  public $idPaymentPlanxtax;
  public $idPaymentPlan;
  public $idTax;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idPaymentPlan", "PaymentPlan", "idPaymentPlan");
    $this->belongsTo("idTax", "Tax", "idTax");
  }

}
