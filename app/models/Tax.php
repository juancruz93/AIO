<?php

class Tax extends Modelbase {

  public $idTax;
  public $idCountry;
  public $idMasteraccount;
  public $idAllied;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $type;
  public $amount;
  public $description;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idCountry", "Country", "idCountry");
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->hasMany("idTax", "PaymentPlanxtax", "idTax");
  }

}
