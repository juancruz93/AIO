<?php

class ContactlistController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Listas de contactos");
    parent::initialize();
  }

  public function showAction() {

    $contactlist = Contactlist::find(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array(0 => $this->user->userType->idSubaccount)
    ));

    $this->validateModel($contactlist, "No se encontró ", "contactlist/show");
    $this->view->setVar("app_name", "contactlist");
  }

  public function listAction() {
    
  }

  public function addAction() {
    
  }

  public function editAction() {
    
  }

  public function deleteAction() {
    
  }

  public function customfieldAction() {
    
  }

  public function addcustomfieldAction() {
    
  }

  public function editcustomfieldAction() {
    
  }

  public function deletecustomfieldAction() {
    
  }

}
