<?php

class PriceList extends Modelbase {

  public $idPriceList;
  public $idMasteraccount;
  public $idAllied;
  public $idServices;
  public $idCountry;
  public $name;
  public $description;
  public $accountingMode;
  public $minValue;
  public $maxValue;
  public $price;
  public $status;
  public $created;
  public $updated;
  public $deleted;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->belongsTo("idServices", "Services", "idServices");
    $this->belongsTo("idCountry", "Country", "idCountry");
    $this->hasMany("idPriceList", "PaymentPlanxservice", "idPriceList");
  }

}
