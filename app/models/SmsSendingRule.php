<?php

class SmsSendingRule extends Modelbase {

  public $idSmsSendingRule;
  public $idCountry;
  public $idIndicative;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $createdBy;
  public $updatedBy;
  public $description;

  public function initialize() {
    $this->belongsTo("idIndicative", "Indicative", "idIndicative");
    $this->belongsTo("idCountry", "Country", "idCountry");
    $this->hasMany("idSmsSendingRule", "Mxssr", "idSmsSendingRule");
  }

}
