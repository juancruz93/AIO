<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class RateForm extends Form {
  
  public function initialize() {
    
    $name = new Text('name', array(
      "minlength" => 2,
      "maxlength" => 80,
      "required" => "true",
    ));
    $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
    $name->addValidator(new StringLength(
      array(
        'min' => 2,
        'messageMinimum' => 'El campo Nombre debe de tener al menos 2 caracteres',
        'max' => 80,
        'messageMaximum' => 'El campo Nombre debe de tener máximo 80 caracteres'
      )
    ));
    $this->add($name);
    
    $description = new Text('description', array(
      "minlength" => 2,
      "maxlength" => 80,
      "required" => "true",
    ));
    $description->addValidator(new PresenceOf(array('message' => 'El campo Descripcion es obligatorio')));
    $description->addValidator(new StringLength(
      array(
        'min' => 2,
        'messageMinimum' => 'El campo Descripcion debe de tener al menos 2 caracteres',
        'max' => 400,
        'messageMaximum' => 'El campo Descripcion debe de tener máximo 80 caracteres'
      )
    ));
    $this->add($description);
    
    $this->add(new Text('dateInitial', array(
      "class" => "input-field input-hoshi",
      "data-ng-model" => "data.dateEnd",
      "required" => "true",
    )));
    
    $this->add(new Text('dateEnd', array(
      'class' => 'input-field input-hoshi',
        "data-ng-model" => "data.dateEnd",
      'required' => 'true',
    )));

    $idServices = new Select("idServices", array(
        "class" => "chosen form-control",
        "data-ng-model" => "data.idServices",
        "required" => "true"
    ));
    $idServices->addFilter("trim");
    $idServices->setLabel("*Servicios");
    $idServices->addValidator(new PresenceOf(array('message' => 'El campo Servicios es obligatorio')));
    $this->add($idServices);
    
    $planType = new Select("planType", array(
        "" => "",
        "prepaid" => "Prepago",
        "postpaid" => "Postpago"
            ), array(
        "class" => "chosen form-control",
        "style" => "width: 100%;",
        "data-ng-model" => "data.planType",
        "required" => "true"
    ));
    $planType->addFilter("trim");
    $planType->setLabel("*Plan de pagos");
    $planType->addValidator(new PresenceOf(array('message' => 'El campo Plan de pagos es obligatorio')));
    $this->add($planType);
       
    $status = new Check("status", array());
    $status->setLabel("*Estado");
    $this->add($status);
  }
}
