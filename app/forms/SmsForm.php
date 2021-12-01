<?php

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

/**
 * Description of SmsForm
 *
 * @author juan.pinzon
 */
class SmsForm extends Form{

  public function initialize() {
    $name = new Text("name", array('autofocus' => 'autofocus', 'required' => 'required', 'maxlength' => 60, 'minlength' => 5));
    $name->addFilter('trim');
    $name->addValidator(new PresenceOf(array('message' => 'El campo nombre  es obligatorio')));
    $name->addValidator(new StringLength(
            array(
        'min' => 5,
        'messageMinimum' => 'El campo nombre  debe de tener al menos 5 caracteres',
        'max' => 60,
        'messageMaximum' => 'El campo nombre  debe de tener máximo 60 caracteres'
            )
    ));
    $this->add($name);

    $this->add(new Check('notification', array()));

    $email = new TextArea("email", array());
    $email->addFilter('trim');
    $email->addValidator(new StringLength(
            array(
        'max' => 500,
        'messageMaximum' => 'El campo direcciones de correo electronico no puede tener mas de 500 caracteres'
            )
    ));
    $this->add($email);

    $idSmsCategory = new Numeric("idSmsCategory", array());
    $idSmsCategory->addValidator(new StringLength(array(
        'min' => 1,
        'messageMinimum' => 'El campo de idSmsCategory es obligatorio'
            )
    ));


    $this->add(new Numeric('idSmsCategory', array()));
  }

}
