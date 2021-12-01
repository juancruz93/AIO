<?php

class Whatsapp extends Modelbase {

    public $idWhatsapp,
      $idSmsCategory,
      $idSubaccount,
      $idAutomaticCampaign,
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
      $total,
      $advancedoptions,
      $divide,
      $sendingTime,
      $quantity,
      $timeFormat,
      $dateNow,  
      $gmt,  
      $originalDate,
      $typeResponse;

  public function initialize() {
    $this->belongsTo("idSmsCategory", "SmsCategory", "idSmsCategory");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasMany("idWhatsapp", "Smslotetwoway", "idWhatsapp");
    $this->hasMany("idWhatsapp", "Smsxc", "idWhatsapp");
    $this->hasMany("idWhatsapp", "Post", "idWhatsapp");
    $this->belongsTo("idWhatsapp", "AutomaticCampaignStep", "idWhatsapp");
  }

}