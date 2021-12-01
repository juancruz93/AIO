<?php

Class MailTemplateImage extends Modelbase{
  
  public $idMailTemplateImage;
  public $idMailTemplate;
  public $idAsset;
  public $name;
  public $created;
  public $updated;
  public $createdBY;
  public $updateBy;
  
  public function initialize(){
    $this->belongsTo("idMailTemplateImage", "MailTemplateImage", "idMailTemplateImage");
    $this->belongsTo("idAsset", "Asset", "idAsset");
  }
}
