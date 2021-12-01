<?php

class LogSmsSend extends Modelbasemongo {

  public $idSms,
          $totalSendProccess,
          $totalSendDB,
          $totalSmsProccess,
          $totalSmsDB,
          $created,
          $updated,
          $createdBy,
          $updatedBy;

  public function getSource() {
    return "log_sms_send";
  }
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

}
