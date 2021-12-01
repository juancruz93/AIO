<?php

class LandingPage extends Modelbase {

  public
          $idLandingPage,
          $idLandingPageCategory,
          $idSubaccount,
          $created,
          $updated,
          $deleted,
          $status,
          $startDate,
          $endDate,
          $name,
          $description,
          $createdBy,
          $updatedBy,
          $footerInfo,
          $countview

  ;

  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idLandingPageCategory", "LandingPageCategory", "idLandingPageCategory");
    $this->hasMany("idMail", "Mail", "idMail");
    $this->belongsTo("idLandingPage", "AutomaticCampaignStep", "idLandingPage");
  }

}
