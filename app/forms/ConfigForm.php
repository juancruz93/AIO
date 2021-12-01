<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Date;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email as EmailValidator;

class ConfigForm extends Form
{

  public function initialize() {
    $this->add(new Select('idMta[]', Mta::find(), array(
      'using' => array('idMta', 'name'),
      'multiple' => 'multiple',
    )));

    $this->add(new Select('idAdapter[]', Adapter::find(), array(
      'using' => array('idAdapter', 'fname'),
      'multiple' => 'multiple',
    )));

    $this->add(new Select('idUrldomain[]', Urldomain::find(), array(
      'using' => array('idUrldomain', 'name'),
      'multiple' => 'multiple',
    )));

    $this->add(new Select('idMailClass[]', Mailclass::find(), array(
      'using' => array('idMailClass', 'name'),
      'multiple' => 'multiple',
    )));

    $fileSpace = new Numeric("fileSpace");
    $fileSpace->addFilter('trim');
    $fileSpace->setAttributes(array("minlength" => 1, "maxlength" => 40));
    $fileSpace->addValidator(new StringLength(
        array(
      'max' => 40,
      'messageMaximum' => 'El campo nombre del usuario debe de tener máximo 40 caracteres'
        )
    ));
    $this->add($fileSpace);

    $this->add(new Numeric("mailLimit", array(
    )));

    $this->add(new Numeric("contactLimit", array(
    )));

    $this->add(new Numeric("smsLimit", array(
    )));

    $this->add(new Numeric("smsVelocity", array(
    )));

    $this->add(new Date("expiryDate", array(
      'required' => 'required',
    )));

//        $this->add(new Select('accountingMode', array(
//            'Contact' => 'Por Contacto',
//            'Mail' => 'Por Envío',
//        )));
//        
//        $this->add(new Select('subscriptionMode', array(
//            'Pre' => 'Prepago',
//            'Pos' => 'Pospago',
//        )));
  }

}
