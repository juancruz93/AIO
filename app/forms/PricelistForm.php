<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;

class PricelistForm extends Form {

  public function initialize() {
    $idCountry = new Select("idCountry", []);
    $idCountry->addFilter("trim");
    $idCountry->setLabel("*País");
    $this->add($idCountry);
    
    $idServices = new Select("idServices", []);
    $idServices->addFilter("trim");
    $idServices->setLabel("*Servicio");
    $this->add($idServices);

    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre de la lista",
        "required" => "required",
        "maxlength" => "70",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre de lista está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 70,
        "min" => 2,
        "messageMaximum" => "El campo nombre de lista debe tener máximo 40 caracteres",
        "messageMinimum" => "El campo nombre de lista debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre de lista");
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Descripción de la lista",
        "required" => "required",
        "maxlength" => "100",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
    $description->addValidator(new SpaceValidatorForm(array(
        "field" => "description",
        "message" => "El campo descripción está vacío, por favor valide la información"
    )));
    $description->addValidator(new StringLength(array(
        "max" => 100,
        "min" => 2,
        "messageMaximum" => "El campo descripción de lista debe tener máximo 100 caracteres",
        "messageMinimum" => "El campo descripción de lista debe tener al menos 2 caracteres"
    )));
    $description->setLabel("*Descripción");
    $this->add($description);

    $accountingMode = new Select("accountingMode", array(
        "" => "",
        "contact" => "Contacto",
        "sending" => "Envío"
            ), array(
        "class" => "chosen form-control",
        "style" => "width: 100%;",
        "data-ng-model" => "data.accountingMode"
    ));
    $accountingMode->addFilter("trim");
    $accountingMode->setLabel("*Modo de contabilidad");
    $this->add($accountingMode);

    $minValue = new Numeric("minValue", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Valor mínimo",
        "min" => 0,
        "max" => 999999999,
        "required" => "required",
        "data-ng-model" => "data.minValue"
    ));
    $minValue->addFilter("trim");
    $minValue->addValidator(new StringLength(array(
        "max" => 999999999,
        "min" => 0,
        "messageMaximum" => "El campo valor mínimo debe tener máximo 9999999 digitos",
        "messageMinimum" => "El campo valor mínimo debe tener al menos el cero"
    )));
//    $minValue->addValidator(new SpaceValidatorForm(array(
//        "field" => "minValue",
//        "message" => "El campo valor mínimo está vacío, por favor valide la información"
//    )));
    $minValue->addValidator(new Numericality(array(
        "message" => "El campo valor mínimo deber ser de tipo númerico"
    )));
    $minValue->setLabel("*Valor mínimo de mensajes o contactos");
    $this->add($minValue);

    $maxValue = new Numeric("maxValue", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Valor máximo",
        "min" => 0,
        "max" => 999999999,
        "required" => "required",
        "data-ng-model" => "data.maxValue"
    ));
    $maxValue->addFilter("trim");
    $maxValue->addValidator(new StringLength(array(
        "max" => 999999999,
        "min" => 0,
        "messageMaximum" => "El campo valor máximo debe tener máximo 9999999 digitos",
        "messageMinimum" => "El campo valor máximo debe tener al menos el cero"
    )));
    /*$maxValue->addValidator(new SpaceValidatorForm(array(
        "field" => "maxValue",
        "message" => "El campo valor máximo está vacío, por favor valide la información"
    )));*/
    $maxValue->addValidator(new Numericality(array(
        "message" => "El campo valor máximo deber ser de tipo númerico"
    )));
    $maxValue->setLabel("*Valor máximo de mensajes o contactos");
    $this->add($maxValue);

    $price = new Numeric("price", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Precio unitario",
        "min" => 0,
        "max" => 999999999999999,
        "required" => "required",
        "step" => "any",
        "data-ng-model" => "data.price"
    ));
    $price->addFilter("trim");
    $price->addValidator(new StringLength(array(
        "max" => 999999999999999,
        "min" => 0,
        "messageMaximum" => "El campo precio debe tener máximo 999999999999999 digitos",
        "messageMinimum" => "El campo precio debe tener al menos 0 digitos"
    )));
    /*
    $price->addValidator(new SpaceValidatorForm(array(
        "field" => "price",
        "message" => "El campo precio está vacío, por favor valide la información"
    )));
     */
//    $price->addValidator(new Numericality(array(
//        "message" => "El campo precio deber ser de tipo númerico"
//    )));
    $price->setLabel("*Precio unitario");
    $this->add($price);

    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $this->add($status);
  }

}
