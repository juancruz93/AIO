<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mxvp
 *
 * @author jordan.zapata
 */
class Mxvp extends Modelbasemongo {

  public
          $ip,
          $openunit,
          $totalopen,
          $type;

  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "mxvp";
  }

}
