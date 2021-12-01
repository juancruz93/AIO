<?php

class PlanType extends Modelbase {

  public $idPlanType;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $description;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasMany("idPlanType", "PaymentPlanxService", "idPlanType");
    $this->hasMany("idPlanType", "PaymentPlanxservice","idPlanType");
  }

}
