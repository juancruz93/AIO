<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email as EmailValidator;

class UserForm extends Form
{

    public function initialize($user, $role)
    {

        $name = new Text("name", array('autofocus' => 'autofocus'));
        $name->addFilter('trim');
        $name->setAttributes(array("minlength" => 2, "maxlength" => 40, "required" => "required", 'placeholder' => 'Nombre'));
        $name->addValidator(new PresenceOf(array('message' => 'El campo nombre del usuario es obligatorio')));
        $name->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo nombre del usuario debe de tener al menos 2 caracteres',
                'max' => 40,
                'messageMaximum' => 'El campo nombre del usuario debe de tener máximo 40 caracteres'
            )
        ));
        $this->add($name);

        $lastname = new Text("lastname");
        $lastname->addFilter('trim');
        $lastname->setAttributes(array("minlength" => 4, "maxlength" => 40, "required" => "required", 'placeholder' => 'Apellido'));
        $lastname->addValidator(new PresenceOf(array('message' => 'El campo apellido es obligatorio')));
        $lastname->addValidator(new StringLength(
            array(
                'min' => 2,
                'messageMinimum' => 'El campo apellido debe de tener al menos 2 caracteres',
                'max' => 40,
                'messageMaximum' => 'El campo apellido debe de tener máximo 40 caracteres'
            )
        ));
        $this->add($lastname);

        $email = new Email("email");
        $email->addFilter('trim');
        $email->setAttributes(array("minlength" => 4, "maxlength" => 60, "required" => "required", 'placeholder' => 'Email'));
        $email->addValidator(new PresenceOf(array('message' => 'El campo correo del usuario es obligatorio')));
        $email->addValidator(new EmailValidator(
            array(
                'message' => 'Formato de correo del usuario invalido',
            )
        ));
        $this->add($email);

        $pass1 = new Password("pass1", array('class' => 'input-field input-hoshi', 'required' => 'required'));
        $pass1->addFilter('trim');
        $pass1->setAttributes(array("minlength" => 8, "maxlength" => 20, "required" => "required", 'placeholder' => 'Contraseña'));
        $pass1->addValidator(new SpaceValidatorForm(array('message' => 'El campo contraseña del usuario es obligatorio')));
        $this->add($pass1);

        $pass2 = new Password("pass2", array('class' => 'input-field input-hoshi', 'required' => 'required'));
        $pass2->addFilter('trim');
        $pass2->setAttributes(array("minlength" => 8, "maxlength" => 20, "required" => "required", 'placeholder' => 'Repita la contraseña'));
        $pass2->addValidator(new SpaceValidatorForm(array('message' => 'El campo repetir contraseña del usuario es obligatorio')));
        $this->add($pass2);

        $tel = new Text("cellphone", array(
            'class' => 'input-field input-hoshi',
            "required" => "required"
        ));
        $tel->setAttributes(array("minlength" => 8, "maxlength" => 45, "required" => "required", 'placeholder' => 'Telefono'));
        $tel->addFilter('trim');
        $tel->addValidator(new PresenceOf(array('message' => 'El campo telefono del usuario es obligatorio')));
        $tel->addValidator(new StringLength(
            array(
                'min' => 8,
                'messageMinimum' => 'El campo telefono del usuario debe de tener al menos 8 caracteres',
                'max' => 45,
                'messageMaximum' => 'El campo telefono del usuario debe de tener máximo 45 caracteres'
            )
        ));
        $this->add($tel);

        /* $roles = Role::find();
          $r = array();

          if ($role->name == 'root') {
          foreach ($roles as $rol) {
          $r[$rol->idRole] = $rol->name;
          }
          } else {
          foreach ($roles as $rol) {
          if ($rol->name != 'root' && $rol->name != 'accounting' && $rol->name != 'admin_db') {
          $r[$rol->idRole] = $rol->name;
          }
          }
          } */

        /* $this->add(new Select('idRole', $r, array(
          'required' => 'required',
          'class' => 'input-field input-hoshi select2',
          'id' => 'idRole'
          )
          )); */
    }

}
