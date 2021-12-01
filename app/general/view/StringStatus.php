<?php

namespace Sigmamovil\General\View;

class StringStatus
{

  public function statussms($status) {
    $string = "";
    switch ($status) {
      case \Phalcon\DI::getDefault()->get('statusSms')->draft;
        $string = "<span class='color-draft'>Borrador<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->scheduled;
        $string = "<span class='color-scheduled'>Programado<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->pending;
        $string = "<span class='color-scheduled'>Pendiente<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->sending;
        $string = "<span class='color-sending'>En proceso de envío<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->sent;
        $string = "<span class='color-sent'>Enviado<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->paused;
        $string = "<span class='color-paused'>Pausado<span>";
        break;
      case \Phalcon\DI::getDefault()->get('statusSms')->canceled;
        $string = "<span class='color-canceled'>Cancelado<span>";
      break;
      case \Phalcon\DI::getDefault()->get('statusSms')->undelivered;
      $string = "<span class='color-canceled'>No enviado<span>";
      break;

      default:
        break;
    }
    return $string;
  }

  public function typesms($type){
    $string = "";
    switch ($type) {
      case \Phalcon\DI::getDefault()->get('typeSms')->contact;
        $string = "Contacto";
        break;
      case \Phalcon\DI::getDefault()->get('typeSms')->csv;
        $string = "Csv";
        break;
      case \Phalcon\DI::getDefault()->get('typeSms')->lote;
        $string = "Envío rapido";
        break;
      case \Phalcon\DI::getDefault()->get('typeSms')->automatic;
        $string = "Contacto";
        break;
      
      default:
        break;
    }
    return $string;
  }
}
