<?php

class ContactlistCategory extends Modelbase {

  public $idContactlistCategory,
          $name,
          $description,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $deleted,
          $idAccount;

  public function initiaize() {
    $this->hasMany("idContactlistCategory", "Contactlist", "idContactlistCategory");
    $this->belongsTo("idAccount", "account", "idAccount");
  }

}
