<?php

/**
 * Description of LandingpagetemplateForm
 *
 * @author juan.pinzon
 */
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\StringLength;

class LandingpagetemplateForm extends Form {

  public function initialize() {
    $name = new Text("name", []);
    $name->addFilter("trim");
    $name->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo nombre está vacío, por favor valide la información"
    )));
    $name->addValidator(new StringLength(array(
        "max" => 40,
        "min" => 2,
        "messageMaximum" => "El campo nombre de moneda debe tener máximo 40 caracteres",
        "messageMinimum" => "El campo nombre de moneda debe tener al menos 2 caracteres"
    )));
    $name->setLabel("*Nombre de moneda");
    $this->add($name);

    $idLandingPageTemplateCategory = new Select("idLandingPageTemplateCategory", []);
    $idLandingPageTemplateCategory->addFilter("trim");
    $idLandingPageTemplateCategory->addValidator(new SpaceValidatorForm(array(
        "field" => "name",
        "message" => "El campo categoría está vacío, por favor valide la información"
    )));
    $this->add($idLandingPageTemplateCategory);
  }

}
