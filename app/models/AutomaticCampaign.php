<?php

class AutomaticCampaign extends Modelbase {

  public $idAutomaticCampaign;
  public $idAutomaticCampaignCategory;
  public $idSubaccount;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $startDate;
  public $endDate;
  public $description;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasOne("idAutomaticCampaign", "AutomaticCampaignConfiguration", "idAutomaticCampaign");
    $this->hasMany("idAutomaticCampaign", "Mail", "idAutomaticCampaign");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasMany("idAutomaticCampaign", "AutomaticCampaignStep", "idAutomaticCampaign");
    $this->belongsTo("idAutomaticCampaignCategory", "AutomaticCampaignCategory", "idAutomaticCampaignCategory");
  }

}
