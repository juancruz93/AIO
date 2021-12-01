<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class MtaForm extends Form {

    public function initialize($data = null) {
        $name = new Text("name", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'autofocus' => 'autofocus',
            'maxlength' => '90',
        ));
        $name->addFilter('trim');
        $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
        $name->addValidator(new StringLength(
                array(
            'min' => 2,
            'messageMinimum' => 'El campo Nombre debe de tener al menos 2 caracteres',
            'max' => 90,
            'messageMaximum' => 'El campo Nombre debe de tener máximo 40 caracteres'
                )
        ));
        $this->add($name);

        $description = new TextArea("description", array(
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'maxlength' => '200',
        ));
        $description->addFilter('trim');
        $description->addValidator(new PresenceOf(array('message' => 'El campo Descripción es obligatorio')));
        $description->addValidator(new StringLength(
                array(
            'min' => 2,
            'messageMinimum' => 'El campo Descripción debe de tener al menos 2 caracteres',
            'max' => 200,
            'messageMaximum' => 'El campo Descripción debe de tener máximo 120 caracteres'
                )
        ));
        $this->add($description);


        $status = array(
            'value' => 1,
            'checked' => "checked",
        );
        
        if (!empty($data)) {
            $status["value"] = $data->status;
            if (!$data->status) {
                unset($status['checked']);
            }
        }
        
        $this->add(new Check('status', $status));
    }

}
