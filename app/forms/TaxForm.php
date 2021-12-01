<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;

class TaxForm extends Form {

    public function initialize() {
        $idCountry = new Select("idCountry", []);
        $idCountry->addFilter("trim");
        $idCountry->setLabel("*País");
        $this->add($idCountry);

        $name = new Text("name", array(
            "class" => "undeline-input form-control",
            "placeholder" => "Nombre del impuesto",
            "required" => "required",
            "maxlength" => "70",
            "data-ng-model" => "data.name"
        ));
        $name->addFilter("trim");
        $name->addValidator(new SpaceValidatorForm(array(
            "field" => "name",
            "message" => "El campo nombre de moneda está vacío, por favor valide la información"
        )));
        $name->addValidator(new StringLength(array(
            "max" => 70,
            "min" => 2,
            "messageMaximum" => "El campo nombre de moneda debe tener máximo 40 caracteres",
            "messageMinimum" => "El campo nombre de moneda debe tener al menos 2 caracteres"
        )));
        $name->setLabel("*Nombre de impuesto");
        $this->add($name);

        $type = new Select("type", array(
                                        "" => "",
                                        "percentage" => "Porcentaje",
                                        "net" => "Neto"), 
                array(
                    "class" => "chosen form-control",
                    "required" => "required",
                    "style" => "width: 100%;",
                    "data-ng-model" => "data.type"
        ));
        $type->addFilter("trim");
        $type->addValidator(new SpaceValidatorForm(array(
            "field" => "type",
            "message" => "El campo tipo está vacío, por favor valide la información"
        )));
        $type->setLabel("*Tipo de valor");
        $this->add($type);

        $amount = new Numeric("amount", array(
            "class" => "undeline-input form-control",
            "placeholder" => "Valor del impuesto",
            "required" => "required",
            "data-ng-model" => "data.amount"
        ));
        $amount->addFilter("trim");
        $amount->addValidator(new SpaceValidatorForm(array(
            "field" => "amount",
            "message" => "El campo valor está vacío, por favor valide la información"
        )));
        $amount->addValidator(new Numericality(array(
            "message" => "El campo valor deber ser de tipo númerico"
        )));
        $amount->setLabel("*Valor");
        $this->add($amount);

        $description = new Textarea("description", array(
            "class" => "undeline-input form-control",
            "placeholder" => "Descripción del impuesto",
            "required" => "required",
            "maxlength" => "100",
            "data-ng-model" => "data.description"
        ));
        $description->addFilter("trim");
        $description->addValidator(new SpaceValidatorForm(array(
            "field" => "description",
            "message" => "El campo descripción está vacío, por favor valide la información"
        )));
        $description->setLabel("*Descripción");
        $this->add($description);

        $status = new Check("status", array(
            "value" => "1",
            "data-ng-model" => "data.status"
        ));
        $this->add($status);
    }

}
