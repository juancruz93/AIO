<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email as EmailValidator;

class AccountcategoryForm extends Form {

  public function initialize() {

    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre de la categoría",
        "required" => "required",
        "autofocus" => "true",
        "maxlength" => "70",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "filed" => "name",
        "message" => "El campo nombre está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El campo nombre de categoría debe tener al menos 2 caracteres",
        "max" => 70,
        "messageMaximum" => "El campo de categoría debe tener máximo 45 caracteres"
    )));
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "rows" => 3,
        "maxlength" => "200",
        "placeholder" => "Descripción de la categoría",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
    $description->addValidator(new StringLength(array(
        "max" => 200,
        "messageMaximum" => "El campo descripción debe tener máximo 200 caracteres"
    )));
    $this->add($description);
  }
}