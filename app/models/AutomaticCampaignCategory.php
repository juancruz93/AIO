<?php

class AutomaticCampaignCategory extends Modelbase{
  
  public $idAutomaticCampaignCategory;
  public $idAccount;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $description;
  public $createdBy;
  public $updatedBy;
  
  public function initialize(){
    $this->belongsTo("idAccount","Account","idAccount");
    $this->hasOne("idAutomaticCampaignCategory", "AutomaticCampaignCategory", "idAutomaticCampaignCategory");
  }
}