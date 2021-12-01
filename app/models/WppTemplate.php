<?php

use Sigmamovil\General\Misc\SanitizeString;

class WppTemplate extends Modelbase {

  public $idWppTemplate;
  public $wppTemplateCategory;
  public $idAccount;
  public $name;
  public $content;
  public $approved;
  public $status;
  public $deleted;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;
  
  public function initialize() {
    //$this->belongsTo("idWppTemplateCategory", "WppTemplateCategory", "idWppTemplateCategory");
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
