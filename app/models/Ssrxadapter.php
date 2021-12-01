<?php

class Ssrxadapter extends Modelbase {

  public $idSsrxAdapter;
  public $idSmsSendingRule;
  public $idAdapter;
  public $byDefault;
  public $prefix;
  public $createdBy;
  public $updatedBy;
  public $created;
  public $updated;

  public function initialize() {
    $this->belongsTo("idSmsSendingRule", "Smssendingrule", "idSmsSendingRule");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
  }

}
