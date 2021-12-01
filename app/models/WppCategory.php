<?php

class WppCategory extends Modelbase
{

  public $idWppCategory;
  public $idAccount;
  public $created;
  public $updated;
  public $status;
  public $name;
  public $description;
  public $deleted;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idWppCategory", "Whatsapp", "idWppCategory");
  }

}