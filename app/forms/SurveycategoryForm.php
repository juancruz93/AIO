<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SurveycategoryForm
 *
 * @author juan.pinzon
 */
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;

class SurveycategoryForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre de categoría",
        "maxlength" => 40,
        "minlength" => 2,
        "required" => "true",
        "autofocus" => "true",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre de categoría está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 40,
        "min" => 2,
        "messageMaximum" => "El campo nombre de categoría debe tener máximo 40 caracteres",
        "messageMinimum" => "El campo nombre de categoría debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre");
    $this->add($name);
    
    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Descripción de categoría",
        "maxlength" => 200,
        "minlength" => 2,
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
    $description->addValidator(new StringLength(array(
        "max" => 200,
        "messageMaximum" => "El campo descripción de categoría debe tener máximo 200 caracteres",
    )));
    $description->setLabel("Descripción");
    $this->add($description);
    
    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $status->setLabel("*Estado");
    $this->add($status);
  }

}
