<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex;

class SubaccountForm extends Form
{
    public function initialize()
    {
        $name = new Text("name");
        $name->addFilter('trim');
        $name->addValidator(new PresenceOf(array('message' => 'El campo Nombre es obligatorio')));
        $name->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo nombre debe de tener al menos 2 caracteres',
                'max' => 80,
                'messageMaximum' => 'El campo nombre debe de tener máximo 80 caracteres'
            )
        ));
        $this->add($name);

        /*$this->add(new Text("name", array(
            'autofocus' => 'autofocus',
        )));*/

        $description = new TextArea("description");
        $description->addFilter('trim');
        $description->addValidator(new StringLength(
            array(
                'max' => 140,
                'messageMaximum' => 'El campo Descripcion debe de tener máximo 80 caracteres'
            )
        ));
        $this->add($description);

        /*$this->add(new TextArea("description", array(

            'autofocus' => 'autofocus',
        )));*/

        $fileSpace = new Numeric("diskSpace");
//        $fileSpace->addValidator(new PresenceOf(array('message' => 'El campo Espacio disponible en disco (MB) es obligatorio')));
//        $fileSpace->addValidator(new Regex(array(
//            'pattern' => '/^[0-9]+/',
//            'message' => 'El campo Espacio disponible en disco (MB) debe ser númerico, por favor valide la información'
//        )));
        $this->add($fileSpace);

        /*$this->add(new Numeric("fileSpace", array(

        )));*/

        /* $contactLimit = new Numeric("contactLimit");
         $contactLimit->addValidator(new PresenceOf(array('message' => 'El campo Limite de Contactos es obligatorio')));
         $contactLimit->addValidator(new Regex(array(
             'pattern' => '/^[0-9]+/',
             'message' => 'El campo Limite de Contactos debe ser númerico, por favor valide la información'
         )));
         $this->add($contactLimit);*/

        $this->add(new Numeric("contactLimit", array()));

        /*$mailLimit = new Numeric("mailLimit");
        $mailLimit->addValidator(new PresenceOf(array('message' => 'El campo Limite de Correos es obligatorio')));
        $mailLimit->addValidator(new Regex(array(
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo Limite de Correos debe ser númerico, por favor valide la información'
        )));
        $this->add($mailLimit);*/

        $this->add(new Numeric("mailLimit", array()));

        /*$smsLimit = new Numeric("smsLimit");
        $smsLimit->addValidator(new PresenceOf(array('message' => 'El campo Limite de Mensajes de Texto es obligatorio')));
        $smsLimit->addValidator(new Regex(array(
            'pattern' => '/^[0-9]+/',
            'message' => 'El campo Limite de Mensajes de Texto debe ser númerico, por favor valide la información'
        )));
        $this->add($smsLimit);*/

        $this->add(new Numeric("smsLimit", array()));


        $this->add(new Select('city', array()));

        $this->add(new Check('status', array(
            'value' => '1'
        )));
    }
}
