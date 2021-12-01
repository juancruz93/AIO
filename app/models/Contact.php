<?php

class Contact extends Modelbasemongo {

//  public $name,
//      $lastname,
//      $email,
//      $indicative,
//      $phone,
//      $birthdate;

  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "contact";
  }

}
