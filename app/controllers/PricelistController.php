<?php

class PricelistController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Listas de precios");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "pricelist");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new PricelistForm());
  }

  public function editAction() {
    $this->view->setVar("form", new PricelistForm());
  }

}
