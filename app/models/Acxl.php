<?php

class Acxl extends Modelbase {

  public $idAcxl,
         $idAutomaticCampaignStep, 
         $idMailLink;

  public function initialize() {
    $this->belongsTo("idAutomaticCampaignStep", "AutomaticCampaignStep", "idAutomaticCampaignStep", array(
        "foreignKey" => true,
    ));

    $this->belongsTo("idMail_link", "Maillink", "idMail_link", array(
        "foreignKey" => true,
    ));
  }

  public function incrementClicks() {
    $this->totalClicks += 1;
  }

}