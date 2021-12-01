<?php

class PpxsxmailClass extends Modelbase {

  public $idPpxsxMailClass;
  public $idPaymentPlanxService;
  public $idMailClass;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idPaymentPlanxService", "PaymentPlanxservice", "idPaymentPlanxService");
    $this->belongsTo("idMailClass", "Mailclass", "idMailClass");
  }

}
