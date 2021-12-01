<?php

class MailtemplatecategoryController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Plantillas de correo");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "mailtemplatecategory");
  }

  public function listAction() {
    
  }

  public function createAction() {
    
  }

  public function editAction() {
   
  }
  
  public function selectAction(){
    
  }

  public function selectautorespAction(){

  }
}
