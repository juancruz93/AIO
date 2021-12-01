<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;

class Currency extends Modelbase {

  public $idCurrency;
  public $name;
  public $shortName;
  public $symbol;
  public $deleted;
  public $status;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasMany("idCurrency", "Country", "idCurrency");
  }

}
