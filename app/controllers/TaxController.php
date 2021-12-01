<?php

class TaxController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle('Impuestos');
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "tax");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new TaxForm());
  }

  public function editAction() {
    $this->view->setVar("form", new TaxForm());
  }

}
