<?php

class BlockadeController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Bloqueos");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "blockade");
  }

  public function listAction() {
    
  }

  public function newAction() {
    $indicative = Indicative::find();
    $this->view->setVar('indicative', $indicative);
  }

}
