<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Forms\Element\Check;

class MailclassForm extends Form
{
    public function initialize()
    {
        $name = new Text("name", array(
            'class' => 'input-field input-hoshi',
            'autofocus' => 'autofocus',
            'required' => 'required',
        ));
        $name->addFilter('trim');
        $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
        $name->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo Nombre debe de tener al menos 2 caracteres',
                'max' => 40,
                'messageMaximum' => 'El campo Nombre debe de tener máximo 40 caracteres'
            )
        ));
        $this->add($name);

        $description = new Text("description", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
        ));
        $description->addFilter('trim');
        $description->addValidator(new PresenceOf(array('message' => 'El campo Descripción es obligatorio')));
        $description->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo Descripción debe de tener al menos 2 caracteres',
                'max' => 40,
                'messageMaximum' => 'El campo Descripción debe de tener máximo 120 caracteres'
            )
        ));
        $this->add($description);

        $this->add(new Check('status', array(
            'value' => '1'
        )));
    }
}
