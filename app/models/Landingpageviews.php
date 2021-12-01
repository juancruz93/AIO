<?php

class Landingpageviews extends Modelbasemongo {

  public $idPageLandingViews,
          $idLandingPage,
          $IpAddress,
          $created,
          $updated,
          $deleted,
          $createdBy,
          $updatedBy;

  public function getSource() {
    return "landing_page_views";
  }
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

}
