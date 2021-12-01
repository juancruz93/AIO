<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Receiversms extends Modelbasemongo {

  public $id,
          $idSmslote,
          $dataReceiver;


  public function getSource() {
    return "receiver_sms";
  }

}

