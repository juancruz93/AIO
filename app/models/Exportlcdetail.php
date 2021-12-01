<?php

use Sigmamovil\General\Misc\SanitizeString;

class Exportlcdetail extends Modelbase {

  public $idExportlcdetail;
  public $idContactlist;
  public $fileName;
  public $route;
  public $emailNotificacion;
  public $created;
  
  public function initialize() {
    $this->belongsTo("idContactlist", "Contactlist", "idContactlist");
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
