<?php

//

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class LanguageForm extends Form {

  public function initialize() {
    $name = new Text("name", array(
        'autofocus' => 'autofocus',
        'required' => 'required'
    ));
    $name->addFilter('trim');
    $name->addValidator(new PresenceOf(array('message' => 'El campo "nombre" es obligatorio')));
    $name->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo nombre debe de tener al menos 2 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo nombre debe de tener máximo 60 caracteres'
            )
    ));
    $this->add($name);

    $shortName = new Text("shortName", array(
        'autofocus' => 'autofocus',
        'required' => 'required'
    ));
    $shortName->addFilter('trim');
    $shortName->addValidator(new PresenceOf(array('message' => 'El campo "nombre corto" es obligatorio')));
    $shortName->addValidator(new StringLength(
            array(
        'min' => 2,
        'messageMinimum' => 'El campo nombre corto debe de tener al menos 2 caracteres',
        'max' => 6,
        'messageMaximum' => 'El campo nombre corto debe de tener máximo 6 caracteres'
            )
    ));
    $this->add($shortName);
  }

}
