<?php

class SmssendingruleController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Reglas de envío de SMS");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "smssendingrule");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new SmssendingruleForm());
  }

  public function editAction() {
    $this->view->setVar("form", new SmssendingruleForm());
  }

  public function showAction() {
    
  }

}
