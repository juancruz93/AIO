<?php

class Autoresponder extends Modelbase {

  public $idAutoresponder,
          $idSubaccount,
          $idNameSender,
          $idEmailsender,
          $idSmsCategory,
          $scheduleDate,
          $name,
          $target,
          $type,
          $subject,
          $replyTo,
          $time,
          $days,
          $status,
          $deleted,
          $quantitytarget,
          $birthdate,
          $optionAdvance,
          $customFields,
          $morecaracter;

  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idNameSender", "NameSender", "idNameSender");
    $this->belongsTo("idEmailsender", "Emailsender", "idEmailsender");
    $this->belongsTo("idSmsCategory", "SmsCategory", "idSmsCategory");
    $this->hasOne("idAutoresponder", "AutoresponderContent", "idAutoresponder");
    $this->hasMany("idAutoresponder", "Mail", "idAutoresponder");
  }

}
