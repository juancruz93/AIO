<?php

class Sxc extends Modelbasemongo
{

  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "sxc";
  }

}
