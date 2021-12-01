<?php

class Mxmc extends Modelbase {

  public $idMxmc,
          $idMail,
          $idMailCategory,
          $deleted;

  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail");
    $this->belongsTo("idMailCategory", "MailCategory", "idMailCategory");
  }

}
