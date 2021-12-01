<?php

class RateController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Tarifas");
    parent::initialize();
  }
    
  public function indexAction()
  {
    $this->view->setVar("app_name", "rateApp");
  }
  
  public function listAction()
  {
    
  }

  public function createAction()
  {
    
  }
}
