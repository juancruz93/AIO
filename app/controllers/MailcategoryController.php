<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class MailcategoryController extends ControllerBase{
  
  public function initialize() {
    $this->tag->setTitle("Categoría de correos");
    parent::initialize();
  }


  public function indexAction(){
    $MailCategory = MailCategory::find(array("orderBy"=>"idMailCategory Desc"));
    
    $this->validateModel($MailCategory, "No se encontró ", "mailcategory/index");
    $this->view->setVar("app_name", "mailcategory");
  }
  
  public function listAction(){}
  
  public function addAction(){
    $form = new MailcategoryForm();
    $this->view->setVar("form",$form);
  }
  
  public function editAction(){
    $form = new MailcategoryForm();
    $this->view->setVar("form",$form);
  }
  
}

