<?php

use Sigmamovil\General\Misc\SanitizeString;

class Smslote extends Modelbase {

  public $idSmslote,
          $idSms,
          $idAdapter,
          $indicative,
          $message,
          $status,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $phone,
          $messageCount;

  public function initialize() {
    $this->belongsTo("idSms", "Sms", "idSms");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
  }

  public function beforeValidationOnCreate() {
    parent::beforeValidationOnCreate();
    if (isset($this->message)) {
      $sanitize = new SanitizeString($this->message);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->nonPrintable();
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
      $sanitize->nonPrintable();
      $this->message = $sanitize->getString();
    }
  }

  public function validateApiReciver($receiver, $indicative = null) {
    $dataReciver = array();

    if ($indicative != null) {
      $country = \Country::find(array(
                  'conditions' => "phoneCode = ?0",
                  "bind" => array(0 => $indicative)
              ))->toArray();

      foreach ($receiver as $key => $value) {
        if ((strlen($value["phone"]) >= $country[0]["minDigits"]) && (strlen($value["phone"]) <= $country[0]["maxDigits"])) {
          $dataReciver[] = $value;
        }
      }
    } else {
      foreach ($receiver as $key => $value) {

        $arr = explode(";", $value);

        if (isset($arr[0])) {
          $country = \Country::find(array(
                      'conditions' => "phoneCode = ?0",
                      "bind" => array(0 => $arr[0])
                  ))->toArray();
          if ($country) {
            if ((strlen($arr[1]) >= $country[0]['minDigits']) && (strlen($arr[1]) <= $country[0]['maxDigits'])) {
              $dataReciver[] = implode(';', $arr);
            }
          }
        }
      }
    }
    return $dataReciver;
  }

}
