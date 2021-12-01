<?php

class Mxl extends Modelbase {

  public $idMail,
          $idMailLink;

  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail", array(
        "foreignKey" => true,
    ));

    $this->belongsTo("idMail_link", "Maillink", "idMail_link", array(
        "foreignKey" => true,
    ));
  }

  public function incrementClicks() {
    $this->totalClicks += 1;
  }

}
