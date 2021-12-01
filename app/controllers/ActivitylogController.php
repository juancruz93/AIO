<?php

class ActivityLogController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Actividad de cuenta");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "activitylog");
  }

  public function listAction() {
    
  }

}
