<?php

class SocialNetwork extends Modelbase {

  public $idSocialNetwork,
          $name,
          $img,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $deleted;

  public function initialize() {
    $this->hasMany("idSocialNetwork", "PersonalizationSocialNetwork", "idSocialNetwork");
  }

}
