<?php

class MailStructureController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Estructuras de correo");
    parent::initialize();
  }

  public function indexAction() {
    
  }

  public function createAction() {
    
  }

  public function editor_frameAction() {
    $arrayAssets = array();
    $this->view->setVar('assets', $arrayAssets);
  }

  public function editAction($id) {
    $template = Mailstructure::findFirst(["conditions" => "idMailStructure = ?0", "bind" => [0 => $id]]);
    $this->view->setVar('mailstructure', $template);
  }

}
