<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Subaccount extends Modelbase {

  public $idSubaccount,
          $idSmsTwoWayPostNot,
          $idAccount,
          $idCity,
          $diskSpace,
          $created,
          $updated,
          $status,
          $name,
          $description,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idSubaccount", "Saxs", "idSubaccount");
    $this->hasMany("idSubaccount", "Usertype", "idSubaccount");
    $this->hasMany("idSubaccount", "Sms", "idSubaccount");
    $this->hasMany("idSubaccount", "Importcontactfile", "idSubaccount");
    $this->hasMany("idSubaccount", "Mail", "idSubaccount");
    $this->hasMany("idSubaccount", "MailStatisticNotification", "idSubaccount");
    $this->hasMany("idSubaccount", "Contactlist", "idSubaccount");
    $this->hasMany("idSubaccount", "Autoresponder", "idSubaccount");
    $this->hasMany("idSubaccount", "Form", "idSubaccount");
    $this->hasMany("idSubaccount", "Survey", "idSubaccount");
    $this->hasMany("idSubaccount", "AutomaticCampaign", "idSubaccount");
    $this->hasOne("idCity", "City", "idCity");
    $this->hasMany("idSubaccount", "Smsxemail", "idSubaccount");
    $this->hasMany("idSmsTwoWayPostNot", "Smstwowaypostnotify", "idSmsTwoWayPostNot");
  }

  public function validation() {
    $this->validate(new PresenceOf(array(
        'field' => 'name',
        'message' => 'El nombre de la Subcuenta es obligatorio, por favor valide la información'
    )));

    /* $this->validate(new SpaceValidator(array(
      'field' => 'name',
      'message' => 'El campo nombre esta vacío, por favor valide la información'
      ))); */

//    $this->validate(new PresenceOf(array(
//      'field' => 'fileSpace',
//      'message' => 'El campo espacio disponible en disco es obligatorio, por favor valide la información'
//    )));

    /* $this->validate(new SpaceValidator(array(
      'field' => 'fileSpace',
      'message' => 'El campo espacio disponible en disco esta vacío, por favor valide la información'
      ))); */

    /* $this->validate(new SpaceValidator(array(
      'field' => 'smsLimit',
      'message' => 'El campo limite de SMS esta vacío, por favor valide la información'
      )));

      $this->validate(new SpaceValidator(array(
      'field' => 'contactLimit',
      'message' => 'El campo limite de contactos esta vacío, por favor valide la información'
      ))); */

    /* $this->validate(new SpaceValidator(array(
      'field' => 'messagesLimit',
      'message' => 'El campo limite de mensajes esta vacío, por favor valide la información'
      ))); */

    if ($this->validationHasFailed() == true) {
      return false;
    }
  }

}
