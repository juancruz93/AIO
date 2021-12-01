<?php

class FooterBlock extends Modelbase
{

  public $idFooterBlock ,
      $idPersonalizationThemes,
      $created,
      $updated,
      $createdBy,
      $updatedBy,
      $deleted,
      $position;

  public function initialize() {
    $this->hasMany("idPersonalizationThemes", "PersonalizationThemes", "idPersonalizationThemes");
  }

}
