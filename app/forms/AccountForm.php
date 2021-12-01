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

class AccountForm extends Form {

  public function initialize() {
    
    $nit = new Text("nit", array(
        'class' => 'input-field input-hoshi',
        "required" => "required",
        'maxlength' => 20,
        'minlength' => 2
    ));
    $nit->addFilter('trim');
    //$nit->addValidator(new PresenceOf(array('message' => 'El campo de número de identificación es obligatorio')));
//    $nit->addValidator(new StringLength(
//            array(
//        'min' => 2,
//        'messageMinimum' => 'El campo número de identificación debe de tener al menos 2 caracteres',
//        'max' => 20,
//        'messageMaximum' => 'El campo número de identificación debe de tener máximo 20 caracteres'
//            )
//    ));
    $this->add($nit);

    $name = new Text("name", array('autofocus' => 'autofocus', 'required' => 'required', 'maxlength' => 100, 'minlength' => 2));
    $name->addFilter('trim');
    $name->addValidator(new PresenceOf(array('message' => 'El campo nombre es obligatorio')));
    $name->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo nombre  debe de tener al menos 2 caracteres',
        'max' => 100,
        'messageMaximum' => 'El campo nombre  debe de tener máximo 100 caracteres'
            )
    ));
    $this->add($name);


    $this->add(new Select('senderAllowed', array(
        '' => '',
        '0' => 'ON',
        '1' => 'OFF',
            ), array(
        "required" => "required"
    )));

    $prueba = new Select('accountingMode', array(
        '' => '',
        'contact' => 'Por Contacto',
        'sent' => 'Por Envío',
    ));
    $this->add($prueba);

    $this->add(new Select('subscriptionEmailMode', array(
        '' => '',
        'prepaid' => 'Prepago',
        'postpaid' => 'Pospago',
            ), array(
        "required" => "required"
    )));

    $this->add(new Select('subscriptionSmsMode', array(
        '' => '',
        'prepaid' => 'Prepago',
        'postpaid' => 'Pospago',
            ), array(
        "required" => "required"
    )));

    $phone = new Text("phone", array(
        'class' => 'input-field input-hoshi',
        'required' => 'required',
        'maxlength' => 45,
        'minlength' => 2
    ));
    $phone->addFilter('trim');
    $phone->addValidator(new PresenceOf(array('message' => 'El campo telefono  es obligatorio')));
    $phone->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo phone  debe de tener al menos 2 caracteres',
        'max' => 45,
        'messageMaximum' => 'El campo phone  debe de tener máximo 45 caracteres'
            )
    ));
    $this->add($phone);
    
    $email = new Email("email", array(
        'class' => 'input-field input-hoshi',
        "required" => "required",
        'maxlength' => 60,
        'minlength' => 2
    ));
    $email->addFilter('trim');
    $email->addValidator(new PresenceOf(array('message' => 'El campo correo electrónico  es obligatorio')));
    $email->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo email  debe de tener al menos 2 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo email  debe de tener máximo 40 caracteres'
            )
    ));
    $email->addValidator(new EmailValidator(
            array(
        'message' => 'Formato de correo del usuario invalido',
            )
    ));
    $this->add($email);

    $address = new Text("address", array(
        'class' => 'input-field input-hoshi',
        "required" => "required",
        'maxlength' => 40,
        'minlength' => 2
    ));
    $address->addFilter('trim');
//    $address->addValidator(new PresenceOf(array('message' => 'El campo dirección  es obligatorio')));
//    $address->addValidator(new StringLength(
//            array(
//        'min' => 2,
//        'messageMinimum' => 'El campo nombre  debe de tener al menos 2 caracteres',
//        'max' => 40,
//        'messageMaximum' => 'El campo nombre  debe de tener máximo 40 caracteres'
//            )
//    ));
    $this->add($address);
    
    $this->add(new Check('status', array(
        'value' => '1'
    )));

    $this->add(new Check('publicDomain', array(
        'value' => '1'
    )));

    $tolerance = new Numeric("tolerancePeriod", array(
        'class' => 'input-field input-hoshi',
        'min' => 0
    ));
    $tolerance->addFilter('trim');
    $tolerance->addValidator(new StringLength(
        array(
            'min' => 0,
            'messageMinimum' => 'El campo nombre  debe de tener al menos 2 caracteres'
        )
    ));
    $this->add($tolerance);
    
    $url = new Text("url", array(
        'class' => 'input-field input-hoshi',
        'min' => 0
    ));
    $url->addFilter('trim');

    $this->add($url);
    
    $hourInit = new Numeric("hourInit", array(
        'class' => 'input-field input-hoshi',
        'min' => 1,
        'max' => 24,
        "required" => "required",
    ));

    $this->add($hourInit);
    
    $hourEnd = new Numeric("hourEnd", array(
        'class' => 'input-field input-hoshi',
        'min' => 1,
        'max' => 24,
        "required" => "required",
    ));

    $this->add($hourEnd);
    
    $habeasData = new TextArea("habeasData", array(
        'class' => 'input-field input-hoshi',
        'cols'=> 50,
        'rows'=> 5,
        'maxlength' => 1000,
        'minlength' => 0
    ));
    //$habeasData->addFilter('trim');
    //$habeasData->addValidator(new PresenceOf(array('message' => 'El campo habeas data es obligatorio')));
    $habeasData->addValidator(new StringLength(
      array(
//        'min' => 0,
//        'messageMinimum' => 'El campo habeas data debe de tener al menos 0 caracteres',
        'max' => 1000,
        'messageMaximum' => 'El campo habeas data debe de tener máximo 1000 caracteres'
      )
    ));
    $this->add($habeasData);
  }
}
