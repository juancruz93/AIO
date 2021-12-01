<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class IpController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("lista de IP");
    parent::initialize();
  }

  public function indexAction(){
    $this->view->setVar("app_name", "ip");
  }
  
  public function listAction(){}
  
  public function createAction(){}
  
  public function editAction(){}
  
}

