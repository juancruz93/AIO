<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;

class PaymentplanForm extends Form {

  public function initialize() {
    $idCountry = new Select("idCountry", []);
    $idCountry->addFilter("trim");
    $idCountry->setLabel("*País");
    $this->add($idCountry);

    $type = new Select("type", array(
        "" => "",
        "public" => "Público",
        "private" => "Privado"
            ), array(
        "class" => "chosen form-control",
        "style" => "width: 100%;",
        "data-placeholder" => "Seleccione un tipo",
        "required" => "true",
        "data-ng-model" => "data.type"
    ));
    $type->addFilter("trim");
    $type->setLabel("*Tipo");
    $this->add($type);

    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre del plan",
        "required" => "true",
        "autofocus" => "true",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre del plan está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 40,
        "min" => 2,
        "messageMaximum" => "El campo nombre del plan debe tener máximo 40 caracteres",
        "messageMinimum" => "El campo nombre del plan debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre");
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Descripción del plan",
        "required" => "required",
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
        "messageMaximum" => "El campo descripción debe tener máximo 100 caracteres",
        "messageMinimum" => "El campo descripción debe tener al menos 2 caracteres"
    )));
    $description->setLabel("*Descripción");
    $this->add($description);

    $diskSpace = new Numeric("diskSpace", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Espacio en disco",
        "min" => 1,
        "max" => 200000,
        "required" => "true",
        "data-ng-model" => "data.diskSpace"
    ));
    $diskSpace->addFilter("trim");
    $diskSpace->addValidator(new SpaceValidatorForm(array(
        "field" => "diskSpace",
        "message" => "El campo espacio en disco está vacío, por favor valide la información"
    )));
    $diskSpace->addValidator(new StringLength(array(
        "max" => 6,
        "min" => 1,
        "messageMaximum" => "El campo espacio en disco debe tener máximo 5 digitos",
        "messageMinimum" => "El campo espacio en disco debe tener al menos 1 digitos"
    )));
    $diskSpace->addValidator(new Numericality(array(
        "message" => "El campo espacio en disco deber ser de tipo númerico"
    )));
    $diskSpace->setLabel("*Espacio en disco (MB)");
    $this->add($diskSpace);

    $status = new Check("status", array(
        "value" => "1",
        "data-ng-model" => "data.status"
    ));
    $this->add($status);
  }

}
