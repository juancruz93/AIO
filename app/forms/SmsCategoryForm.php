<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;

class SmsCategoryForm extends Form {

  public function initialize() {
    $name = new Text("name",array(
        "autofocus" => "true",
        "minlength" => 2,
        "maxlength" => 80,
        "required" => "required"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El campo nombre debe tener al menos 2 caracteres",
        "max" => 80,
        "messageMaximum" => "El campo nombre debe tener máximo 45 caracteres"
    )));
    $this->add($name);
    
    $description = new Textarea("description",array(
        "maxlength" => 400,
        "rows" => 2
    ));
    $name->addFilter("trim");
    $description->addValidator(new StringLength(array(
        "max" => 400,
        "messageMaximum" => "El campo descripción debe tener máximo 100 caracteres"
    )));
    $this->add($description);
    
    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $status->setLabel("*Estado");
    $this->add($status);
  }

}
