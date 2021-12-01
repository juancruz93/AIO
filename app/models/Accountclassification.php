<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;

class Accountclassification extends Modelbase
{

  public $idMta,
      $idAdapter,
      $idUrldomain,
      $idMailClass,
      $idAccountclassification,
      $fileSpace,
      $mailLimit,
      $contactLimit,
      $smsLimit,
      $created,
      $updated,
      $smsSpeed,
      $senderAllowed,
      $footerEditable,
      $typeBilling,
      $expiryDate,
      $createdBy,
      $updatedBy;

  public function initialize() {
    $this->belongsTo("idMta", "Mta", "idMta");
    $this->belongsTo("idAdapter", "Adapter", "idAdapter");
    $this->belongsTo("idUrldomain", "Urldomain", "idUrldomain");
    $this->belongsTo("idMailClass", "Mailclass", "idMailClass");
    $this->belongsTo("idFooter", "Footer", "idFooter");
    $this->hasMany("idAccountclassification", "Account", "idAccountclassification");
  }

  public function validation() {

//        $this->validate(new Regex(array(
//            'field' => 'fileSpace',
//            'pattern' => '/^[0-9]+/',
//            'message' => 'El campo espacio disponible en disco esta vacío, por favor valide la información'
//        )));
//        $this->validate(new PresenceOf(array(
//            'field' => 'mailLimit',
//            'message' => 'El campo limite de correos es obligatorio, por favor valide la información'
//        )));
//
//        $this->validate(new Regex(array(
//            'field' => 'mailLimit',
//            'pattern' => '/^[0-9]+/',
//            'message' => 'El campo limite de correos no debe tener espacio, por favor valide la información'
//        )));
//        $this->validate(new Regex(array(
//            'field' => 'contactLimit',
//            'pattern' => '/^[0-9]+/',
//            'message' => 'El campo limite de contactos esta vacío, por favor valide la información'
//        )));
//
//        $this->validate(new Regex(array(
//            'field' => 'smsLimit',
//            'pattern' => '/^[0-9]+/',
//            'message' => 'El campo limite de SMS esta vacío, por favor valide la información'
//        )));
//        $this->validate(new PresenceOf(array(
//            'field' => 'smsVelocity',
//            'message' => 'El campo capacidad de envío por segundo es obligatorio, por favor valide la información'
//        )));

    $this->validate(new PresenceOf(array(
      'field' => 'senderAllowed',
      'message' => 'El campo agregar remitente es obligatorio, por favor valide la información'
    )));

    $this->validate(new PresenceOf(array(
      'field' => 'footerEditable',
      'message' => 'El campo footer editable es obligatorio, por favor valide la información'
    )));

    $this->validate(new PresenceOf(array(
      'field' => 'expiryDate',
      'message' => 'El campo Fecha de expiración es obligatorio, por favor valide la información'
    )));

    if ($this->validationHasFailed() == true) {
      return false;
    }
  }

}
