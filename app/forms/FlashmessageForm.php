<?php

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Check;

class FlashmessageForm extends Form {

  public function initialize() {
    $this->add(new Text('name', array(
        'maxlength' => 100,
        'type' => 'text',
        'required' => 'required',
        'autofocus' => "autofocus",
        'class' => 'input-field input-hoshi',
        'id' => 'name'
    )));

    
    $this->add(new TextArea('message', array(
        'maxlength' => 1000,
        'type' => 'text',
        'rows' => 2,
        'required' => 'required',
        'class' => 'form-control',
        'id' => 'message'
    )));
//
//    $this->add(new Select('accounts', Account::find(), array(
//        'using' => array('idAccount', 'name'),
//        'multiple' => 'multiple',
//        'class' => 'input-field input-hoshi select2',
//        'name' => 'accounts[]',
//        'id' => 'accounts'
//    )));
//
//    $this->add(new Check('allAccounts', array(
//        'value' => 'all',
//        'id' => 'all'
//    )));
//
//    $this->add(new Check('certainAccounts', array(
//        'value' => 'any',
//        'id' => 'any'
//    )));

    $this->add(new Select("type", array(
        'info' => 'Info',
        'default' => 'Default',
        'success' => 'Success',
        'info' => 'Info',
        'warning' => 'Warning',
        'danger' => 'Danger',
        'dark' => 'Dark'
    )));

    $this->add(new Select("target[]", $this->getRecipients()));

    $this->add(new Select("category", array(
        'info' => 'Informativo',
        'admin' => 'Administrativo',
        'footer' => 'Pie de página',
    )));

    $this->add(new Text('start', array(
        'maxlength' => 80,
        'type' => 'text',
        'required' => 'required',
        'id' => 'datetimepicker1',
    )));

    $this->add(new Text('end', array(
        'maxlength' => 80,
        'type' => 'text',
        'required' => 'required',
        'id' => 'datetimepicker2',
    )));


//    $this->add(new Select('allied', Allied::find(), array(
//        'using' => array('idAllied', 'name'),
//        'multiple' => 'multiple',
//        'class' => 'input-field input-hoshi select2',
//        'name' => 'allied[]',
//        'id' => 'allied'
//    )));
//
//    $this->add(new Check('allAllied', array(
//        'value' => 'all',
//        'id' => 'all'
//    )));
//
//    $this->add(new Check('certainAllied', array(
//        'value' => 'any',
//        'id' => 'any'
//    )));
  }

  public function getRecipients() {
    $recipients = [];

    if ($this->user->UserType->idMasteraccount) {

      $results = \Allied::find(array("conditions" => "idMasteraccount= ?0", "bind" => array($this->user->UserType->idMasteraccount)));
      if ($results) {
        foreach ($results as $result) {
          $recipients[$result->idAllied] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas aliadas asociadas";
      }
    } else if ($this->user->UserType->idAllied) {
      $results = \Account::find(array("conditions" => "idAllied= ?0", "bind" => array($this->user->UserType->idAllied)));
      if ($results) {
        foreach ($results as $result) {
          $recipients[$result->idAccount] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas asociadas";
      }
    } else {

      $results = \Masteraccount::find();
      if ($results) {

        foreach ($results as $result) {
          $recipients[$result->idMasteraccount] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas master asociadas";
      }
    }

//    if($this->user->idRole == 4){}

    return $recipients;
  }

}
