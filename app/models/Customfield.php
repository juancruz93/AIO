<?php

class Customfield extends \Modelbase {

  public $idCustomfield,
          $idContactlist,
          $name,
          $alternativename,
          $deleted,
          $defaultvalue,
          $type,
          $value,
          $created,
          $updated,
          $idAccount;

  public function initialize() {
    $this->belongsTo("idContactlist", "Contactlist", "idContactlist");
  }

}
