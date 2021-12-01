<?php

class AdditionalInfo extends Modelbase
{

  public $idAdditionalInfo ,
      $idFooterBlock,
      $created,
      $updated,
      $createdBy,
      $updatedBy,
      $deleted,
      $text,
      $position;

  public function initialize() {
    $this->hasMany("idFooterBlock", "FooterBlock", "idFooterBlock");
  }

}
