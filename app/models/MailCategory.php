<?php

class MailCategory extends Modelbase {

  public $idMailCategory,
          $name,
          $status,
          $description,
          $deleted,
          $idAccount;

  public function initiaize() {
    $this->hasMany("idMailCategory", "mxmc", "idMailCategory");
    $this->belongsTo("idAccount", "account", "idAccount");
  }

}
