<?php

class SmsCategory extends Modelbase
{

  public $idSmsCategory;
  public $idAccount;
  public $created;
  public $updated;
  public $status;
  public $name;
  public $description;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasMany("idSmsCategory", "Sms", "idSmsCategory");
    $this->hasMany("idSmsCategory", "Smsxemail", "idSmsCategory");
  }

}
