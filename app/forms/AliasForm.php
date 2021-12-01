<?php

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class AliasForm extends Form
{

  public function initialize() {

    $name = new Text("name", array('autofocus' => 'autofocus', 'maxlength' => 20));
    $name->addFilter('trim');
    $name->addValidator(new PresenceOf(array('message' => 'El campo nombre del aliado es obligatorio')));
    $name->addValidator(new StringLength(
        array(
      'min' => 2,
      'messageMinimum' => 'El campo nombre del aliado debe de tener al menos 2 caracteres',
      'max' => 20,
      'messageMaximum' => 'El campo nombre del aliado debe de tener máximo 20 caracteres'
        )
    ));
    $this->add($name);

    $nit = new Text("nit", array('max' => 99999999999999999999, 'min' => 0));
    $nit->addFilter('trim');
    $nit->addValidator(new PresenceOf(array('message' => 'El campo nit del aliado es obligatorio')));
    $nit->addValidator(new StringLength(
        array(
      'min' => 2,
      'messageMinimum' => 'El campo nit del aliado debe de tener al menos 2 caracteres',
      'max' => 20,
      'messageMaximum' => 'El campo nit del aliado debe de tener máximo 20 caracteres'
        )
    ));
    $this->add($nit);

    $address = new Text("address", array('maxlength' => 40));
    $address->addFilter('trim');
    $address->addValidator(new PresenceOf(array('message' => 'El campo direccion del aliado es obligatorio')));
    $address->addValidator(new StringLength(
        array(
      'min' => 2,
      'messageMinimum' => 'El campo direccion del aliado debe de tener al menos 2 caracteres',
      'max' => 40,
      'messageMaximum' => 'El campo direccion del aliado debe de tener máximo 40 caracteres'
        )
    ));
    $this->add($address);

    $phone = new Text("phone", array(
      'class' => 'input-field input-hoshi',
      'maxlength' => 40
    ));
    $phone->addValidator(new PresenceOf(array('message' => 'El campo telefono del aliado es obligatorio')));
    $phone->addValidator(new StringLength(
        array(
      'min' => 2,
      'messageMinimum' => 'El campo telefono del aliado debe de tener al menos 2 caracteres',
      'max' => 40,
      'messageMaximum' => 'El campo telefono del aliado debe de tener máximo 40 caracteres'
        )
    ));
    $this->add($phone);

    $this->add(new Text("zipcode", array(
      'class' => 'input-field input-hoshi',
      'maxlength' => 10
    )));

    $email = new Text("email", array('maxlength' => 40));
    $email->addFilter('trim');
    $email->addValidator(new PresenceOf(array('message' => 'El campo correo del aliado es obligatorio')));
    $email->addValidator(new EmailValidator(
        array(
      'message' => 'Correo del aliado no valido'
        )
    ));
    $email->addValidator(new StringLength(
        array(
      'min' => 2,
      'messageMinimum' => 'El campo correo del aliado debe de tener al menos 2 caracteres',
      'max' => 40,
      'messageMaximum' => 'El campo correo del aliado debe de tener máximo 40 caracteres'
        )
    ));
    $this->add($email);

    $this->add(new Check('status', array(
      'value' => '1'
    )));
  }

}
