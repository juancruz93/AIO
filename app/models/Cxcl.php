<?php

class Cxcl extends Modelbase
{

  public $idContactlist,
      $idContact,
      $idForm,
      $created,
      $createdBy,
      $updatedBy,
      $unsuscribed,
      $deleted,
      $status,
      $spam,
      $bounced,
      $blocked,
      $active,
      $singlePhone;

  public function initialize() {
    $this->belongsTo("idContactlist", "Contactlist", "idContactlist");
  }

}
