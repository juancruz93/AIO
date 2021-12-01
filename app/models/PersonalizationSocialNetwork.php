<?php

class PersonalizationSocialNetwork extends Modelbase
{

  public $idPersonalizationSocialNetwork ,
      $idFooterBlock,
      $created,
      $updated,
      $createdBy,
      $updatedBy,
      $idSocialNetwork,
      $deleted,
      $url,
      $title,
      $position;

  public function initialize() {
    $this->belongsTo("idFooterBlock", "FooterBlock", "idFooterBlock");
    $this->belongsTo("idSocialNetwork", "SocialNetwork", "idSocialNetwork");
  }

}
