<?php

class WpptemplateController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Plantillas de WPP");
    parent::initialize();
  }
  
  public function indexAction(){
    $this->view->setVar("app_name", "wpptemplate");
  }
  
  public function listAction(){
    
  }
  
  public function createAction(){
    
  }
  
  public function editAction(){
    
  }
}