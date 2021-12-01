<?php

class AutomaticCampaignConfiguration extends Modelbase {
  
  public $idAutomaticCampaignConfiguration;
  public $idAutomaticCampaign;
  public $created;
  public $updated;
  public $configuration;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasOne("idAutomaticCampaign","AutomaticCampaign","idAutomaticCampaign");
  }
  
}
