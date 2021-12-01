<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;

class SmssendingruleForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre de regla",
        "maxlength" => 80,
        "required" => "true",
        "autofocus" => "true",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre de regla está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 80,
        "min" => 2,
        "messageMaximum" => "El campo nombre de regla debe tener máximo 80 caracteres",
        "messageMinimum" => "El campo nombre de regla debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre");
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Descripción de regla",
        "maxlength" => 200,
        "required" => "required",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
    $description->addValidator(new SpaceValidatorForm(array(
        "field" => "description",
        "message" => "El campo descripción está vacío, por favor valide la información"
    )));
    $description->addValidator(new StringLength(array(
        "max" => 200,
        "min" => 2,
        "messageMaximum" => "El campo descripción debe tener máximo 100 caracteres",
        "messageMinimum" => "El campo descripción debe tener al menos 2 caracteres"
    )));
    $description->setLabel("*Descripción");
    $this->add($description);

    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $status->setLabel("*Estado");
    $this->add($status);
  }

}
