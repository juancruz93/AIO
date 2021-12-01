<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;

class RangeForm extends Form {
  public function initialize() {
    $since = new Numeric("since", array(
      "class" => "undeline-input form-control",
      "placeholder" => 0,
      "disabled" => "disabled", 
      "min" => 0,
      "max" => 999999999,
      "required" => "required",
      "data-ng-model" => "data.since"
    ));
    $since->addFilter("trim");
    $since->addValidator(new StringLength(array(
      "max" => 999999999,
      "min" => 0,
      "messageMaximum" => "El campo valor mínimo debe tener máximo 9999999 digitos",
      "messageMinimum" => "El campo valor mínimo debe tener al menos el cero"
    )));
    $since->addValidator(new Numericality(array(
      "message" => "El campo valor mínimo deber ser de tipo númerico"
    )));
    $this->add($since);
    
    $until = new Numeric("until", array(
      "class" => "undeline-input form-control",
      "min" => 0,
      "max" => 999999999,
      "required" => "required",
      "data-ng-model" => "data.until"
    ));
    $until->addFilter("trim");
    $until->addValidator(new StringLength(array(
      "max" => 999999999,
      "min" => 0,
      "messageMaximum" => "El campo valor mínimo debe tener máximo 9999999 digitos",
      "messageMinimum" => "El campo valor mínimo debe tener al menos el cero"
    )));
    $until->addValidator(new Numericality(array(
      "message" => "El campo valor mínimo deber ser de tipo númerico"
    )));
    $this->add($until);
    
    $space = new Text("space", array(
        "class" => "undeline-input form-control",
        "maxlength" => "70",
        "data-ng-model" => "data.space"
    ));
    $space->addFilter("trim");
    $space->setLabel("*Capacidad");
    $this->add($space);
    
    $value = new Numeric("value", array(
      "class" => "undeline-input form-control",
      "min" => 0,
      "max" => 999999999,
      "required" => "required",
      "data-ng-model" => "data.value"
    ));
    $value->addFilter("trim");
    $value->addValidator(new StringLength(array(
      "max" => 999999999,
      "min" => 0,
      "messageMaximum" => "El campo value mínimo debe tener máximo 9999999 digitos",
      "messageMinimum" => "El campo value mínimo debe tener al menos el cero"
    )));
    $value->addValidator(new Numericality(array(
      "message" => "El campo valor mínimo deber ser de tipo númerico"
    )));
    $this->add($value);
  }
}
