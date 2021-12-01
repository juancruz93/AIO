<?php

/*
 * Model Category Landing Page
 */

class LandingPageCategory extends Modelbase {

  public $idLandingPageCategory,
          $idAccount,
          $name,
          $description,
          $status;

  public function initiaize() {
    $this->hasMany("idLandingPageCategory", "Landing_Page", "idLandingPageCategory");
    $this->belongsTo("idAccount", "account", "idAccount");
  }

  public function getSource() {
    return "landing_page_category";
  }

}
