<?php

/**
 * Description of LogsxMtaxEvent
 *
 * @author jose.quinones
 */
class LogsxMtaxEvent extends Modelbasemongo {
  //  public $receiver;
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "track_events";
  }
}
