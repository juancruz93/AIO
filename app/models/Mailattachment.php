<?php

class Mailattachment extends Modelbase {

  public $idMailAttachment,
          $idMail,
          $idAsset,
          $createdon;

  public function getSource() {
    return "mail_attachment";
  }

  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail");
    $this->belongsTo("idAsset", "Asset", "idAsset");
  }

}
