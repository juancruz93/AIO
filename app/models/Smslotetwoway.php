<?php

use Sigmamovil\General\Misc\SanitizeString;

class Smslotetwoway extends Modelbase {

  public $idSmsLoteTwoway,
          $idSmsTwoway,
          $idAdapter,
          $indicative,
          $message,
          $status,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $phone,
          $messageId,
          $userResponse,
          $userResponseGroup,
          $totalUserResponse;

  public function initialize() {
    $this->belongsTo("idSmsTwoway", "Smstwoway", "idSmsTwoway");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
  }

  public function beforeValidationOnCreate() {
    parent::beforeValidationOnCreate();
    if (isset($this->message)) {
      $sanitize = new SanitizeString($this->message);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->sanitizeSpecialsSms();
      $this->message = $sanitize->getString();
    }
  }

  public function beforeValidationOnUpdate() {
    parent::beforeValidationOnUpdate();
    if (isset($this->message)) {
      $sanitize = new SanitizeString($this->message);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->sanitizeSpecialsSms();
      $this->message = $sanitize->getString();
    }
  }

}
