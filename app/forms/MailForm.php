<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Forms\Element\Email;

class MailForm extends Form
{
    public function initialize()
    {
        $name = new Text("name");
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

        /*$replyto = new Email("replyto");
        $replyto->addFilter('trim');
        $replyto->setAttributes(array("minlength" => 4, "maxlength" => 40));
        $replyto->addValidator(new EmailValidator(
            array(
                'message' => 'Formato de correo invalido',
            )
        ));
        $this->add($replyto);*/

        $subject = new Text("subject");
        $subject->addFilter('trim');
        $subject->addValidator(new PresenceOf(array('message' => 'El campo Asunto es obligatorio')));
        $subject->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo Asunto debe de tener al menos 2 caracteres',
                'max' => 100,
                'messageMaximum' => 'El campo Asunto debe de tener máximo 100 caracteres'
            )
        ));
        $this->add($subject);
    }
}