<?php

class MailTemplate extends Modelbase {

  public $idMailTemplate;
  public $idAccount;
  public $idMailTemplateCategory;
  public $idAllied;
  public $status;
  public $created;
  public $updated;
  public $deleted;
  public $name;
  public $createdBy;
  public $updatedBy;
  public $global;

  public function initialize() {
    $this->hasMany("idMailTemplate", "MailTemplateImage", "idMailTemplate");
    $this->hasMany("idMailTemplate", "FormOptin", "idMailTemplate");
    $this->hasMany("idMailTemplate", "FormWelcomeMail", "idMailTemplate");
    $this->hasMany("idMailTemplate", "FormNotificationMail", "idMailTemplate");
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->belongsTo("idMailTemplateCategory", "MailTemplateCategory", "idMailTemplateCategory");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->hasOne("idMailTemplate", "MailTemplateContent", "idMailTemplate");
  }

}
