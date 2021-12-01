<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class AccountclassificationForm extends Form
{

  public function initialize() {

//    $this->add(new Text("name", array(
//      'class' => 'input-field input-hoshi',
//      'required' => 'required',
//      'autofocus' => 'autofocus',
//    )));

    $this->add(new Select('idMta', Mta::find(), array(
      'using' => array('idMta', 'name'),
      'class' => 'input-field input-hoshi select2',
      'id' => 'input-90'
    )));

    $this->add(new Select('idAdapter', Adapter::find(), array(
      'using' => array('idAdapter', 'fname'),
      'class' => 'input-field input-hoshi select2',
      'id' => 'input-91'
    )));

    $this->add(new Select('idUrldomain', Urldomain::find(), array(
      'using' => array('idUrldomain', 'name'),
      'class' => 'input-field input-hoshi select2',
      'id' => 'input-92'
    )));

    $this->add(new Select('idMailClass', Mailclass::find(), array(
      'using' => array('idMailClass', 'name'),
      'class' => 'input-field input-hoshi select2',
      'id' => 'input-93'
    )));

    $fileSpace = new Numeric("fileSpace", array('class' => 'input-field input-hoshi'));
    $fileSpace->setAttributes(array("required" => 'required'));
    $fileSpace->addValidator(new PresenceOf(array('message' => 'El campo almacenamiento es obligatorio')));
    $this->add($fileSpace);

    $mailLimit = new Numeric("mailLimit", array('class' => 'input-field input-hoshi'));
    $mailLimit->setAttributes(array("required" => 'required'));
    $mailLimit->addValidator(new PresenceOf(array('message' => 'El campo limite de correos es obligatorio')));
    $this->add($mailLimit);

    $contactLimit = new Numeric("contactLimit", array('class' => 'input-field input-hoshi'));
    $contactLimit->setAttributes(array("required" => 'required'));
    $contactLimit->addValidator(new PresenceOf(array('message' => 'El campo limite de correos es obligatorio')));
    $this->add($contactLimit);

    $smsLimit = new Numeric("smsLimit", array('class' => 'input-field input-hoshi'));
    $smsLimit->setAttributes(array("required" => 'required'));
    $smsLimit->addValidator(new PresenceOf(array('message' => 'El campo limite de correos es obligatorio')));
    $this->add($smsLimit);

    $this->add(new Select('senderAllowed', array(
      '0' => 'No',
      '1' => 'Si',
    )));

    $footer = Footer::find(array(
        "conditions" => "idAllied = ?0 AND deleted = 0",
        "bind" => array(0 => $this->user->Usertype->idAllied)
    ));

    $this->add(new Select('idFooter', $footer, array(
        'using' => array('idFooter', 'name'),
        'class' => 'chzn-select'
    )));

    $this->add(new Select('footerEditable', array(
      '0' => 'No',
      '1' => 'Si',
    )));

    $this->add(new Date('expiryDate', array(
      'class' => 'input-field input-hoshi',
      'required' => 'true',
    )));
  }

}
