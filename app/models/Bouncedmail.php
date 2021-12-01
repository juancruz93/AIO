<?php

class Bouncedmail extends Modelbasemongo{
  
  public $email,
         $datetime,
         $description,
         $type,
         $code;
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "bounced_mail";
  }

  
  
}