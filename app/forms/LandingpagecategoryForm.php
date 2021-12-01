<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class LandingpagecategoryForm extends Form {

    public function initialize() {

        $name = new Text('name', array(
            "minlength" => 2,
            "maxlength" => 80,
            "required" => "true",
        ));
        $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
        $name->addValidator(new StringLength(
                array(
            'min' => 2,
            'messageMinimum' => 'El campo Nombre debe de tener al menos 2 caracteres',
            'max' => 80,
            'messageMaximum' => 'El campo Nombre debe de tener máximo 80 caracteres'
                )
        ));
//        $name->addValidator(new RegexValidator(
//                array(
//            'pattern' => '/^[a-zA-Z]/',
//            'message' => 'El nombre no puede tener caracteres especiales')
//                )
//        );
        $this->add($name);

        $description = new Textarea('description', array(
            "maxlength" => 400,
        ));
        $description->setLabel('Descripcion');
        $description->addValidator(new StringLength(
                array(
            'max' => 400,
            'messageMaximum' => 'El campo descripcion debe de tener máximo 400 caracteres'
                )
        ));

        $this->add($description);

        $status = new Check("status", array());
        $status->setLabel("*Estado");
        $this->add($status);
    }

}
