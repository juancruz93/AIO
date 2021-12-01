<?php

/**
 * Description of Logs
 *
 * @author jose.quinones
 */
class Logs extends Modelbasemongo {
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "logs";
  }
  //put your code here
}
