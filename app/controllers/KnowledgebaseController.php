<?php

class KnowledgebaseController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Base del conocimiento");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "knowledgebase");
  }


  public function listAction() {
 
  }
  public function importAction() {
 
  }
  public function validateAction() {
 
  }
  
   public function downloadAction($name) {
    $this->view->disable();
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename=result_'.$name);
    header('Pragma: public');
    header('Expires: 0');
    header('Content-Type: application/download');
//    $route =  __DIR__ . '\..\..\tmp\csv\\'.$name; // Windows
    $route =  __DIR__ . "/../../tmp/csv/".$name; // Linux
    readfile($route);
    unlink($route);
  }


}
