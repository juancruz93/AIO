<?php

use Sigmamovil\General\Misc\SanitizeString;

class SmsTemplate extends Modelbase {

  public $idSmsTemplate;
  public $idSmsTemplateCategory;
  public $idAccount;
  public $name;
  public $content;
  public $deleted;
  public $status;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;
  public $morecaracter;
  
  public function initialize() {
    $this->belongsTo("idSmsTemplateCategory", "SmsTemplateCategory", "idSmsTemplateCategory");
    $this->belongsTo("idaccount", "Account", "idAccount");
  }

  public function beforeValidationOnCreate() {
    parent::beforeValidationOnCreate();
    if (isset($this->content)) {
      $sanitize = new SanitizeString($this->content);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->sanitizeSpecialsSms();
      $this->content = $sanitize->getString();
    }
  }

  public function beforeValidationOnUpdate() {
    parent::beforeValidationOnUpdate();
    if (isset($this->content)) {
      $sanitize = new SanitizeString($this->content);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->sanitizeSpecialsSms();
      $this->content = $sanitize->getString();
    }
  }

}
