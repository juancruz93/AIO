<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\StringLength;

class MasteraccountForm extends Form {

  public function initialize() {
    $name = new Text("nameMasterAccount", array(
        'autofocus' => 'autofocus',
        'required' => 'required'
    ));
    $name->addFilter('trim');
    $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
    $this->add($name);

    $description = new TextArea("description", array(
        'class' => 'input-field input-hoshi',
        'maxlength' => 254
    ));
    $description->addValidator(new StringLength(
            array(
        'max' => 254,
        'messageMaximum' => 'El campo descripción no puede tener mas de 254 caracteres'
            )
    ));
    $this->add($description);

    $nit = new Text("nit", array(
        'class' => 'input-field input-hoshi',
        'maxlength' => 80,
        'required' => 'required'
    ));
    $nit->addFilter('trim');
    $nit->addValidator(new StringLength(
            array(
        'max' => 60,
        'messageMaximum' => 'El campo nit no puede tener mas de 80 caracteres'
            )
    ));
    $nit->addValidator(new PresenceOf(array('message' => 'El campo nit es obligatorio')));
    $this->add($nit);

    $address = new Text("address", array(
        'class' => 'input-field input-hoshi',
        'maxlength' => 45,
        'required' => 'required'
    ));
    $address->addFilter('trim');
    $address->addValidator(new PresenceOf(array('message' => 'El campo dirección es obligatorio')));
    $address->addValidator(new StringLength(
            array(
        'max' => 45,
        'messageMaximum' => 'El campo dirección no puede tener mas de 45 caracteres'
            )
    ));
    $this->add($address);

    $phone = new Text("phone", array(
        'class' => 'input-field input-hoshi',
        'maxlength' => 45,
        'required' => 'required'
    ));
    $phone->addFilter('trim');
    $phone->addValidator(new PresenceOf(array('message' => 'El campo telefono es obligatorio')));
    $phone->addValidator(new StringLength(
            array(
        'max' => 45,
        'messageMaximum' => 'El campo telefono no puede tener mas de 45 caracteres'
            )
    ));
    $this->add($phone);

    $this->add(new Text("city", array(
        'class' => 'input-field input-hoshi',
        'required' => 'required'
    )));

    $this->add(new Text("country", array(
        'class' => 'input-field input-hoshi',
        'required' => 'required'
    )));

    $this->add(new Text("state", array(
        'class' => 'input-field input-hoshi',
        'required' => 'required'
    )));

    $this->add(new Check('status', array(
        'value' => '1',
        'ng-model' => 'status'
    )));
    $this->add(new Select('services[]', Services::find(), array(
        'using' => array('idServices', 'name'),
        'multiple' => 'multiple',
        'required' => 'required'
    )));
    $this->add(new Select('paymentPlan', PaymentPlan::find(array(
                "conditions" => "idMasteraccount is null and idAllied is null"
            )), array(
        'using' => array('idPaymentPlan', 'name'),
        'required' => 'required'
    )));
    $this->add(new Select("rule", SmsSendingRule::find(),array(
        "using" => array("idSmsSendingRule", "name"),
        "class" => "undeline-input select2",
        "required" => "true"
    )));
  }

}
