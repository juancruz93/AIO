<?php

class CurrencyController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle('Divisas');
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "currency");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new CurrencyForm());
  }

  public function editAction() {
    $this->view->setVar("form", new CurrencyForm());
  }

}
