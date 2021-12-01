<?php

class Ppxsxmta extends Modelbase {

  public $idPpxsxMta;
  public $idPaymentPlanxService;
  public $idMta;
  public $created;
  public $udpated;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idPaymentPlanxService", "PaymentPlanxservice", "idPaymentPlanxService");
    $this->belongsTo("idMta", "Mta", "idMta");
  }

}
