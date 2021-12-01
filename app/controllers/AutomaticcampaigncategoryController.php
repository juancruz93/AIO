<?php

class AutomaticcampaigncategoryController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Categorías de campañas automáticas");
    parent::initialize();
  }
  
  public function indexAction(){
    $this->view->setVar("app_name","automaticcampaigncategory");
  }
  
  public function listAction(){}
  
  public function createAction(){
    $form = new AutomaticcampaigncategoryForm();
    
    $this->view->setVar("form",$form);
  }
  
  public function editAction(){
    $form = new AutomaticcampaigncategoryForm();
    
    $this->view->setVar("form",$form);
  }
}