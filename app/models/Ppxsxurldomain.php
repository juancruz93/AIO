<?php

class Ppxsxurldomain extends Modelbase {

  public $idPpxsxUrldomain;
  public $idPaymentPlanxService;
  public $idUrldomain;
  public $created;
  public $updated;
  public $createdBy;
  public $udpatedBY;

  public function initialize() {
    $this->belongsTo("idPaymentPlanxService", "PaymentPlanxservice", "idPaymentPlanxService");
    $this->belongsTo("idUrldomain", "Urldomain", "idUrldomain");
  }

}
