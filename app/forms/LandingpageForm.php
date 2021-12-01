<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\StringLength;

class LandingpageForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre",
        "required" => "required",
        "autofocus" => "true",
        "maxlength" => "45",
        "data-ng-model" => "data.name"
    ));
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "filed" => "name",
        "message" => "El nombre de la Landing está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El nombre de la landing debe tener al menos 2 caracteres",
        "max" => 45,
        "messageMaximum" => "El nombre de la landing debe tener máximo 45 caracteres"
    )));
    $this->add($name);

    $nameauthor = new Text("nameauthor", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre del autor",
        "autofocus" => "true",
        "maxlength" => "45",
        "data-ng-model" => "data.nameauthor"
    ));
    $nameauthor->addFilter("trim");    
    $this->add($nameauthor);



    $email = new Text("email", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Correo electrónico",
        "required" => "required",
        "autofocus" => "true",
        "maxlength" => "45",
        "data-ng-model" => "data.email"
    ));
    $email->addFilter("trim");
    $email->addValidator(new SpaceValidatorForm(array(
        "filed" => "email",
        "message" => "El correo electrónico está vacío, por favor valide la información"
    )));
    $email->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El correo debe tener al menos 2 caracteres",
        "max" => 45,
        "messageMaximum" => "El correo debe tener máximo 45 caracteres"
    )));
    $this->add($email);
    
    $address = new Text("address", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Dirección",
        "autofocus" => "true",
        "maxlength" => "50",
        "data-ng-model" => "data.address"
    ));
    $address->addFilter("trim");   
   
    $this->add($address);
    
    $website = new Text("website", array(
        "class" => "undeline-input form-control",
        "placeholder" => "Nombre del sitio Web",
        "autofocus" => "true",
        "maxlength" => "45",
        "data-ng-model" => "data.website"
    ));
    $website->addFilter("trim");
    $website->addValidator(new SpaceValidatorForm(array(
        "filed" => "website",
        "message" => "El nombre del sitio Web está vacío, por favor valide la información"
    )));
    $website->addValidator(new StringLength(array(
        "min" => 2,
        "messageMinimum" => "El nombre del sitio Web debe tener al menos 2 caracteres",
        "max" => 45,
        "messageMaximum" => "El nombre del sitio Web debe tener máximo 45 caracteres"
    )));
    $this->add($website); 
    
    $nit = new Text("nit", array(
        "class" => "undeline-input form-control",
        "placeholder" => "NIT",
        "autofocus" => "true",
        "maxlength" => "45",
        "data-ng-model" => "data.nit"
    ));
    $website->addFilter("trim");
    $this->add($nit); 

    $description = new Textarea("description", array(
        "class" => "undeline-input form-control",
        "rows" => 3,
        "maxlength" => "200",
        "placeholder" => "Descripción",
        "data-ng-model" => "data.description"
    ));
    $description->addFilter("trim");    
    $this->add($description);
  }

}
