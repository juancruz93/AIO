<?php

class MailTemplateContent extends Modelbase {
  
  public $idMailTemplateContent;
  public $idMailTemplate;
  public $created;
  public $updated;
  public $content;
  public $createdBY;
  public $updatedBy;
  
  public function initialize(){
    $this->belongsTo("idMailTemplate", "MailTemplate", "idMailTemplate");
  }
}
