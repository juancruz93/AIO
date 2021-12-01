<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;

class SurveyForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre",
        "required" => "required",
        "autofocus" => "true",
        "maxlength" => "50",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "filed" => "name",
        "message" => "El campo Nombre está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El campo Nombre debe tener al menos 2 caracteres",
        "max" => 70,
        "messageMaximum" => "El campo Nombre debe tener máximo 70 caracteres"
    )));
    $this->add($name);

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "rows" => 3,
        "maxlength" => "200",
        "placeholder" => "Descripción",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");
    $description->addValidator(new StringLength(array(
        "max" => 200,
        "messageMaximum" => "El campo descripción debe tener máximo 200 caracteres"
    )));
    $this->add($description);

    $messageFinal = new Textarea("messageFinal", array(
        "class" => "undeline-input form-control",
        "rows" => 3,
        "maxlength" => "200",
        "placeholder" => "Mensaje que aparecerá al final de la encuesta",
        "required" => "true",
        "data-ng-model" => "data.messageFinal"
    ));
    $messageFinal->addFilter("trim");
    $messageFinal->addValidator(new StringLength(array(
        "max" => 200,
        "messageMaximum" => "El campo de mensaje final debe tener máximo 200 caracteres"
    )));
    $this->add($messageFinal);

    $url = new Text("url", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Url finalización",
        "data-ng-model" => "data.url"
    ));
    $url->addFilter('trim');

    $this->add($url);
  }

}
