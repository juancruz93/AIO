<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;
use Sigmamovil\General\FormElements\Url;

class UrldomainForm extends Form {

    public function initialize($data = null) {
        $this->add(new Url("name", array(
            'class' => 'form-control input-field input-hoshi',
            'name' => 'name',
            'required' => 'required',
            'autofocus' => 'autofocus',
            'maxlength' => '100',
            'value' => (empty($data) ? "" : $data->name),
        )));

        $this->add(new TextArea("description", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '200',
        )));
        
        $status = new Check("status", array(
            "value" => "1",
        ));
        $this->add($status);
    }

}
