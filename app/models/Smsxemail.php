<?php

class Smsxemail extends Modelbase {
  
  public  $idSmsxEmail,
          $idSubaccount,
          $idSmsCategory,
          $senderEmail,
          $generateKey,
          $notificationEmail;
          
  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");    
    $this->belongsTo("idSmsCategory", "SmsCategory", "idSmsCategory");    
  }
}
