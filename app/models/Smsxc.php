<?php

class Smsxc extends Modelbasemongo{
  public $idSmsxc,
         $idSms,
         $idSmsTwoway,
         $message,
         $response,
         $updated,
         $created,
         $morecaracter;
  
  public function getSource() {
    return "smsxc";
  }
}
