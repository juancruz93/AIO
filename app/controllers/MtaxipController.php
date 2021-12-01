<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class MtaxipController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("lista de MTA por IP");
    parent::initialize();
  }

  public function indexAction(){
    $this->view->setVar("app_name", "mtaxip");
  }
  
  public function listAction(){}
  
  public function createAction(){}
  
  public function editAction(){}
  
}

