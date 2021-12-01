<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\Email;

class User extends Modelbase {

  public $idUser,
          $idRole,
          $idCity,
          $created,
          $updated,
          $cellphone,
          $name,
          $lastname,
          $email,
          $idUsertype,
          $password,
          $deleted,
          $deletedBy,
          $registerType;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->belongsTo("idRole", "Role", "idRole");
    $this->belongsTo("idUsertype", "Usertype", "idUsertype");
    $this->belongsTo("idCity", "City", "idCity");
    $this->hasMany("idUser", "Importfile", "idUser");
    $this->hasMany("idUser", "Apikey", "idUser");
    $this->hasMany("idUser", "ActivityLog", "idUser");
  }

  public function beforeValidationOnCreate() {
    $this->created = time();
  }

  public function beforeValidationOnUpdate() {
    $this->updated = time();
  }

  public function validation() {
    $this->validate(new PresenceOf(array(
        'field' => 'name',
        'message' => 'El nombre es obligatorio, por favor valide la información'
    )));

    $this->validate(new StringLength(array(
        "field" => "name",
        "min" => 2,
        "messageMinimum" => "El nombre de usuario es muy corto, debe tener al menos 2 caracteres",
        "max" => 80,
        "messageMaximum" => "El nombre de usuario debe de tener máximo 80 caracteres"
    )));

    $this->validate(new PresenceOf(array(
        'field' => 'lastname',
        'message' => 'El apellido es obligatorio, por favor valide la información'
    )));

    $this->validate(new StringLength(array(
        "field" => "lastname",
        "min" => 2,
        "messageMinimum" => "El apellido de usuario es muy corto, debe tener al menos 2 caracteres",
        "max" => 80,
        "messageMaximum" => "El apellido de usuario debe de tener máximo 80 caracteres"
    )));

    $this->validate(new Email(array(
        'field' => 'email',
        'message' => 'El email del usuario ingresado no es valido, por favor valide la información'
    )));

    $this->validate(new PresenceOf(array(
        'field' => 'password',
        'message' => 'La contraseña es obligatoria, por favor valide la información'
    )));

    $this->validate(new StringLength(array(
        "field" => "password",
        "min" => 8,
        "message" => "La contraseña es muy corta, debe tener como minimo 8 caracteres"
    )));

    if ($this->validationHasFailed() == true) {
      return false;
    }
  }

}
