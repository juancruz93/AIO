<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email as EmailValidator;

class AutomaticcampaigncategoryForm extends Form {

  public function initialize() {

    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        //"minlength" => 2,
        //"maxlength" => 45,
        "placeholder" => "Nombre de la categoría",
        "required" => "required",
        "autofocus" => "true",
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
        "max" => 45,
        "messageMaximum" => "El campo de categoría debe tener máximo 45 caracteres"
    )));
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        //"maxlength" => 200,
        "rows" => 3,
        "placeholder" => "Descripción de la categoría",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
//    $description->addValidator(new SpaceValidatorForm(array(
//        "field" => "description",
//        "message" => "El campo descripción está vacío, por favor valide la información"
//    )));
    $description->addValidator(new StringLength(array(
        "max" => 200,
        "messageMaximum" => "El campo descripción debe tener máximo 200 caracteres"
    )));
    $this->add($description);
  }

}
