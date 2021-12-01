<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Mailgoogleanalytics extends Modelbase {

  public  $idMailGoogleAnalytics,
          $idMail,
          $name,
          $links;

  public function getSource() {
    return "mail_google_analytics";
  }

  public function initialize() {
    $this->hasOne("idMail", "Mail", "idMail");
  }

}
