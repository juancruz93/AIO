<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class EmailsenderController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Correo de remitente");
    parent::initialize();
  }

  public function indexAction(){
    $this->view->setVar("app_name", "emailsender");
  }
  
  public function listAction(){}
  
  public function createAction(){}
  
  public function editAction(){}
  
}

