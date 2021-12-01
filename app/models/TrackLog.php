<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrackLog
 * Coleccion que permite guardar el rastreo del proceso de aperturas, click y desuscripcion de mail detalladamente
 *
 * @author felipe.garcia
 */
class TrackLog extends Modelbasemongo {
  
  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "track_log";
  }
  //put your code here
}
