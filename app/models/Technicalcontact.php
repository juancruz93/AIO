<?php

class Technicalcontact extends Modelbase
{

  public $idTechnicalcontact;
  public $idAllied;
  public $created;
  public $updated;
  public $name;
  public $lastname;
  public $email;
  public $phone;

  public function initialize() {
    $this->belongsTo("idAllied", "Allied", "idAllied");
  }

}
