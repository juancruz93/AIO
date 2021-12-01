<?php

class CustomizingController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Personalización");
    parent::initialize();
  }

  public function indexAction() {

    $this->view->setVar("app_name", "customizing");
    
  }

  public function addAction() {
    
  }

  public function listAction() {
    
  }
  public function editAction() {
    
  }

}
