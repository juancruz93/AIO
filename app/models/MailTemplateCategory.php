<?php

class MailTemplateCategory extends Modelbase{
  
  public $idMailTemplateCategory;
  public $idAllied;
  public $idAccount;
  public $created;
  public $updated;
  public $deleted;
  public $name;
  public $createdBy;
  public $updatedBy;
  
  public function initialize(){
    $this->hasMany("idMailTemplateCategory", "MailTemplate", "idMailTemplateCategory");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->belongsTo("idAccount", "Account", "idAccount");
  }
}