<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class NamesenderController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Nombre de remitentes");
    parent::initialize();
  }

  public function indexAction(){
    $this->view->setVar("app_name", "namesender");
  }
  
  public function listAction(){}
  
  public function createAction(){}
  
  public function editAction(){}
  
}

