<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Check;

class AdapterForm extends Form {

    public function initialize() {
        $this->add(new Hidden("signal", array(
            'class' => 'input-field input-hoshi',
            'autofocus' => 'autofocus',
            'required' => 'required',
            'maxlength' => '100',
            "value" => 0
        )));

        $this->add(new Hidden("usedlr", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '10',
            "value" => 0
        )));

        $this->add(new Hidden("fsender", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '10',
            "value" => 0
        )));

        $this->add(new Hidden("fixedid", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '10',
            "value" => 0
        )));

        $this->add(new Text("fname", array(
            'class' => 'input-field input-hoshi',
            'autofocus' => 'autofocus',
            'required' => 'required',
            'maxlength' => '100',
        )));

        $this->add(new Text("prefix", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '5',
        )));

        $this->add(new Text("smscid", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '100',
        )));

        $this->add(new Text("uname", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '40',
        )));

        $this->add(new Password("passw", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '40',
            'minlength' => '4',
        )));

        $this->add(new Password("password", array(
            'class' => 'input-field input-hoshi',
            'maxlength' => '40',
            'minlength' => '4',
        )));

        $this->add(new Hidden("coding", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            "value" => 0
        )));

        $status = new Check("status", array(
            "value" => "1",
        ));
        $this->add($status);

        $this->add(new Text("urlIp", array(
            'class' => 'input-field input-hoshi',
            'maxlength' => '150',
            'required' => 'required',
        )));

        $this->add(new Check("international", array(
          'value' => 1,
          'checked' => "checked",
        )));
    }

}
