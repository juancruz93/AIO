<?php

class SmstemplateController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Plantillas de SMS");
    parent::initialize();
  }
  
  public function indexAction(){
    $this->view->setVar("app_name", "smstemplate");
  }
  
  public function listAction(){
    
  }
  
  public function createAction(){
    
  }
  
  public function editAction(){
    
  }
}