<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\StringLength;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class HabeasdataForm extends Form
{

    public function initialize()
    {
        $habeasData = new TextArea("habeasData", array(
        'class' => 'form-control', 
        'placeholder' => 'Digite su texto aqui...',
        'cols'=> 60,
        'rows'=>5,
        'maxlength' => 1000,
        'minlength' => 0
        ));
        //$habeasData->addFilter('trim');
        //$habeasData->addValidator(new PresenceOf(array('message' => 'El campo habeas data es obligatorio')));
        $habeasData->addValidator(new StringLength(
          array(
          //        'min' => 0,
          //        'messageMinimum' => 'El campo habeas data debe de tener al menos 0 caracteres',
            'max' => 1000,
            'messageMaximum' => 'El campo habeas data debe de tener máximo 1000 caracteres'
          )
        ));
        $this->add($habeasData);
    }
}