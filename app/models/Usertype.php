<?php

class Usertype extends Modelbase
{

  public $idUsertype,
      $idAccount,
      $name,
      $idMasteraccount,
      $idAllied,
      $idSubaccount;

  public function initialize() {
    $this->hasOne("idUsertype", "User", "idUsertype");
    $this->hasOne("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasOne("idAllied", "Allied", "idAllied");
    $this->hasOne("idAccount", "Account", "idAccount");
    $this->hasMany("idUsertype", "User", "idUsertype");
  }

}
