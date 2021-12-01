<?php

class AccountCategory extends Modelbase
{

  public $idAccountCategory,
      $idMasteraccount,
      $idAllied,
      $name,
      $description,
      $deleted,
      $status,
      $expirationDate;

  public function initialize(){
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->hasMany("idAccountCategory", "Masteraccount", "idAccountCategory");
    $this->hasMany("idAccountCategory", "Allied", "idAccountCategory");
    $this->hasMany("idAccountCategory", "Account", "idAccountCategory");
  }

}
