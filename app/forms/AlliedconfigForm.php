<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Numeric;

class AlliedconfigForm extends Form
{

  public function initialize() {
    $this->add(new Numeric("fileSpace", array(
//      'required' => 'required',
    )));

    $this->add(new Numeric("mailLimit", array(
//      'required' => 'required',
    )));

    $this->add(new Numeric("contactLimit", array(
//      'required' => 'required',
    )));

    $this->add(new Numeric("smsLimit", array(
//      'required' => 'required',
    )));

    $this->add(new Numeric("smsVelocity", array(
//      'required' => 'required',
    )));
  }

}
