<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Forms\Element\Numeric;

class SmsxemailForm extends Form {
  
  public function initialize() {
    
    $senderEmail = new Email("senderEmail", array(
        'class' => 'input-field input-hoshi',
        "required" => "true",
        'maxlength' => 60,
        'minlength' => 2
    ));
    $senderEmail->addFilter('trim');
    $senderEmail->addValidator(new PresenceOf(array('message' => 'El campo Correo del remitente es obligatorio')));
    $senderEmail->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo Correo del remitente debe de tener al menos 2 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo Correo del remitente debe de tener máximo 40 caracteres'
            )
    ));
    $senderEmail->addValidator(new EmailValidator(
            array(
        'message' => 'Formato de correo del usuario invalido',
            )
    ));
    $this->add($senderEmail);
    
    $generateKey = new Text("generateKey", array(
        'class' => 'input-field input-hoshi',
        "required" => "true",
        'maxlength' => 60,
        'minlength' => 2
    ));
    $generateKey->addFilter('trim');
    $generateKey->addValidator(new PresenceOf(array('message' => 'El campo Generar clave es obligatorio')));
    $generateKey->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El Correo de notificación debe de tener al menos 2 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo Correo de notificación debe de tener máximo 40 caracteres'
            )
    ));
    $this->add($generateKey);
    
    $notificationEmail = new Email("notificationEmail", array(
        'class' => 'input-field input-hoshi',
        "required" => "true",
        'maxlength' => 60,
        'minlength' => 2
    ));
    $notificationEmail->addFilter('trim');
    $notificationEmail->addValidator(new PresenceOf(array('message' => 'El campo Correo de notificación es obligatorio')));
    $notificationEmail->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El Correo de notificación debe de tener al menos 2 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo Correo de notificación debe de tener máximo 40 caracteres'
            )
    ));
    $notificationEmail->addValidator(new EmailValidator(
            array(
        'message' => 'Formato de Correo de notificación invalido',
            )
    ));
    $this->add($notificationEmail);
  }
}
