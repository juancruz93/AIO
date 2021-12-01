<?php

class Smstwoway extends Modelbase
{

  public $idSmsTwoway,
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
      $total,
      $advancedoptions,
      $divide,
      $sendingTime,
      $timeFormat,
      $idAutomaticCampaign,
      $dateNow,    
      $typeResponse,
      $originalDate,
      $quantity,
      $gmt,
      $international,
      $idcountry;

  public function initialize() {
    $this->belongsTo("idSmsCategory", "SmsCategory", "idSmsCategory");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasMany("idSmsTwoway", "Smslotetwoway", "idSmsTwoway");
    $this->hasMany("idSmsTwoway", "Smsxc", "idSmsTwoway");
    $this->hasMany("idSmsTwoway", "Post", "idSmsTwoway");
    $this->belongsTo("idSmsTwoway", "AutomaticCampaignStep", "idSmsTwoway");
  }
}
