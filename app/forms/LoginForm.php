<?php

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Email,
    Phalcon\Forms\Element\Password;

class LoginForm extends Form {

  public function initialize() {
    $this->add(new Email('email', array(
        'maxlength' => 80,
        'placeholder' => 'Correo',
        'required' => 'required',
        'autofocus' => 'autofocus',
        'class' => 'undeline-input form-control',
        'id' => 'email',
        "data-ng-model" => "data.email"
    )));

    $this->add(new Password('password', array(
        'maxlength' => 40,
        'placeholder' => 'Contraseña',
        'required' => 'required',
        'class' => 'undeline-input form-control',
        'id' => 'pass1',
        "data-ng-model" => "data.password"
    )));
  }

}
