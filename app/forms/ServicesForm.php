<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Form;

class ServicesForm extends Form {

    public function initialize() {
        $this->add(new Text("name", array(
            'class' => 'undeline-input form-control',
            'autofocus' => 'autofocus',
            'required' => 'required',
            'maxlength' => '100',
            'minlength' => '2',
        )));

        $this->add(new TextArea("description", array(
            'class' => 'undeline-input form-control',
            'required' => 'required',
            'maxlength' => '200',
            'minlength' => '2',
        )));
        
        $status = new Check("status", array(
            "value" => "1",
        ));
        $this->add($status);
    }

}
