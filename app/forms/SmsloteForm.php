<?php

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;

class SmsloteForm extends Form
{

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

    $quantity = new Numeric("quantity", array());
    $this->add($quantity);

    $datesend = new Text("startdate");
    $this->add($datesend);


    $email = new TextArea("email", array('maxlength' => 500, "rows" => 2));
    $email->addFilter('trim');
    $email->addValidator(new StringLength(
        array(
      'max' => 500,
      'messageMaximum' => 'El campo direcciones de correo electronico no puede tener mas de 500 caracteres'
        )
    ));
    $this->add($email);

    $receiver = new TextArea("receiver", array("rows" => 2));
    $receiver->addFilter('trim');
    $this->add($receiver);

    $this->add(new Check('notification', array(
      'value' => '1'
    )));
    $this->add(new Check('advancedoptions', array(
      'value' => '1'
    )));
    $this->add(new Check('divide', array(
      'value' => '1'
    )));

    $this->add(new Select('idSmsCategory', SmsCategory::find(), array(
      'using' => array('idSmsCategory', 'name'),
      'class' => ' ',
        )
    ));

    $this->add(new Check('sendnow', array(
      'value' => '1'
    )));
    
    $this->add(new Check('smstemplate', array(
      'value' => '1'
    )));
    
    
    $this->add(new Check('morecaracter', array(
      'value' => '1'
    )));
    
    $csv = new File("csv", array('required' => 'required'));
    $csv->setAttributes(array("accept" => ".csv"));
    $this->add($csv);
  }

}
