<?php

class MailStatisticNotification extends Modelbase {

  public $idMailStatisticNotification,
          $idMail,
          $idSubaccount,
          $created,
          $updated,
          $status,
          $scheduleDate,
          $target,
          $type,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasOne("idMail", "Mail", "idMail");
  }


}
