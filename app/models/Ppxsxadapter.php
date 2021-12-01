<?php

class Ppxsxadapter extends Modelbase {

  public $idPpxsxAdapter;
  public $idPaymentPlanxService;
  public $idAdapter;
  public $created;
  public $createdBy;

  public function initialize() {
    $this->belongsTo("idPaymentPlanxService", "PaymentPlanxservice", "idPaymentPlanxService");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
  }

}
