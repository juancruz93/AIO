<?php

class MailContent extends Modelbase {

  public $idMailContent,
          $idMail,
          $typecontent,
          $content,
          $plaintext;

  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail");
  }

}
