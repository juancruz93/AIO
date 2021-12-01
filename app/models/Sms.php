<?php

use Sigmamovil\General\Misc\SanitizeString;

class Sms extends Modelbase
{

  public $idSms,
      $idSmsCategory,
      $idSubaccount,
      $logicodeleted,
      $notification,
      $email,
      $name,
      $startdate,
      $message,
      $confirm,
      $type,
      $created,
      $updated,
      $createdBy,
      $updatedBy,
      $status,
      $receiver,
      $sent,
      $advancedoptions,
      $divide,
      $sendingTime,
      $timeFormat,
      $idAutomaticCampaign,
      $externalApi,
      $morecaracter,    
      $idAutoresponder,
      $sendpush,
      $idntfyanaconas;

  public function initialize() {
    $this->belongsTo("idSmsCategory", "SmsCategory", "idSmsCategory");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idAutoresponder", "Autoresponder", "idAutoresponder");
    $this->hasMany("idSms", "Smslote", "idSms");
    $this->hasMany("idSms", "Smsxc", "idSms");
    $this->hasMany("idSms", "Post", "idSms");
    $this->belongsTo("idSms", "AutomaticCampaignStep", "idSms");
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
