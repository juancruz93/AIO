<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class ReplytoController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Correo de respuesta");
    parent::initialize();
  }

  public function indexAction(){
    $this->view->setVar("app_name", "replyto");
  }
  
  public function listAction(){}
  
  public function createAction(){}
  
  public function editAction(){}
  
}

