<?php

class Historycontroller extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Historial de actividades");
    parent::initialize();
  }

  public function indexAction() {

    $this->view->setVar("app_name", "history");
  }

  public function listAction() {
    
  }

}
