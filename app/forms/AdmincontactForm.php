<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Email;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email as EmailValidator;

class AdmincontactForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "autofocus" => "true",
        "minlength" => 3,
        "maxlength" => 70,
        "required" => "required"
    ));
    $name->addFilter("trim");

    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre está vacío, por favor valide la información"
    )));

    $name->addValidator(new StringLength(array(
        "min" => 3,
        "messageMinimum" => "El campo nombre de contacto debe tener al menos 3 caracteres",
        "max" => 70,
        "messageMaximum" => "El campo nombre de contacto debe tener máximo 70 caracteres"
    )));
    $this->add($name);

    $lastname = new Text("lastname",array(
        "minlength" => 3,
        "maxlength" => 70,
        "required" => "required"
    ));
    $lastname->addFilter("trim");
    $lastname->addValidator(new SpaceValidatorForm(array(
        "field" => "lastname",
        "message" => "El campo apellido está vacío, por favor valide la información"
    )));
    $lastname->addValidator(new StringLength(array(
        "min" => 3,
        "messageMinimum" => "El campo apellido de contacto debe tener al menos 3 caracteres",
        "max" => 70,
        "messageMaximum" => "El campo apellido de contacto debe tener máximo 70 caracteres"
    )));
    $this->add($lastname);

    $email = new Email("email",array(
        "type" => "email",
        "minlength" => 6,
        "maxlength" => 80,
        "required" => "required"
    ));
    $email->addFilter("trim");
    $email->addValidator(new SpaceValidatorForm(array(
        "field" => "email",
        "message" => "El campo correo está vacío, por favor valide la información"
    )));
    $email->addValidator(new StringLength(array(
        "min" => 6,
        "messageMinimum" => "El campo correo de contacto debe tener al menos 6 caracteres",
        "max" => 80,
        "messageMaximum" => "El campo correo de contacto debe tener máximo 80 caracteres"
    )));
    $email->addValidator(new EmailValidator(array("message" => "El correo de contacto no es válido")));
    $this->add($email);

    $phone = new Text("phone",array(
        "minlength" => 7,
        "maxlength" => 29,
        "required" => "required"
    ));
    $phone->addFilter("trim");
    $phone->addValidator(new SpaceValidatorForm(array(
        "field" => "phone",
        "message" => "El campo teléfono está vacío, por favor valide la información"
    )));
    $phone->addValidator(new StringLength(array(
        "min" => 7,
        "messageMinimum" => "El campo teléfono de contacto debe tener al menos 7 caracteres",
        "max" => 29,
        "messageMaximum" => "El campo teléfono de contacto debe tener máximo 29 caracteres"
    )));
    $this->add($phone);
  }

}
