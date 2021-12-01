<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\StringLength;

class CurrencyForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre de moneda",
        "required" => "required",
        "autofocus" => "autofocus",
        "maxlength" => "35",
        "data-ng-model" => "data.name"
    ));
    
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre de moneda está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 40,
        "min" => 2,
        "messageMaximum" => "El campo nombre de moneda debe tener máximo 40 caracteres",
        "messageMinimum" => "El campo nombre de moneda debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre de moneda");
    $this->add($name);
    
    $shortName = new Text("shortName", array(
        "class" => "undeline-input form-control",
        "maxlength" => 3,
        "placeholder" => "Abreviatura",
        "required" => "required",
        "style" => "text-transform:uppercase;",
        "data-ng-model" => "data.shortName",
    ));
    $shortName->addFilter("trim");
    $shortName->addValidator(new SpaceValidatorForm(array(
        "field" => "shortName",
        "message" => "El campo abreviatura está vacío, por favor valide la información"
    )));
    $shortName->addValidator(new StringLength(array(
        "max" => 3,
        "min" => 3,
        "messageMaximum" => "El campo abreviatura debe tener máximo 3 caracteres",
        "messageMinimum" => "El campo abreviatura de moneda debe tener al menos 3 caracteres"
    )));
    $shortName->setLabel("*Abreviatura");
    $this->add($shortName);
    
    $symbol = new Text("symbol", array(
        "class" => "undeline-input form-control",
        "maxlength" => "1",
        "placeholder" => "Símbolo",
        "required" => "true",
        "data-ng-model" => "data.symbol"
    ));
    $symbol->addFilter("trim");
    $symbol->addValidator(new SpaceValidatorForm(array(
        "field" => "symbol",
        "message" => "El campo símbolo está vacío, por favor valide la información"
    )));
    $symbol->addValidator(new StringLength(array(
        "max" => 1,
        "min" => 1,
        "messageMaximum" => "El campo símbolo debe tener máximo 1 caracteres",
        "messageMinimum" => "El campo símbolo debe tener al menos 1 caracter"
    )));
    $symbol->setLabel("*Símbolo");
    $this->add($symbol);
    
    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $this->add($status);
  }

}
