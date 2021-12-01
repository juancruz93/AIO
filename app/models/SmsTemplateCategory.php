<?php

class SmsTemplateCategory extends Modelbase{
  
  public $idSmsTemplateCategory;
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
    $this->hasMany("idSmsTemplateCategory", "SmsTemplate", "idSmsTemplateCategory");
  }
}