<?php

class WppTemplateCategory extends Modelbase{
  
  public $idWppTemplateCategory;
  public $idAccount;
  public $name;
  public $deleted;
  public $status;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;
  
  public function initialize(){
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idWppTemplateCategory", "WppTemplate", "idWppTemplateCategory");
  }
}