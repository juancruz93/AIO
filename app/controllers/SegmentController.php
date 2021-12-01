<?php

class SegmentController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Segmentos");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "segment");
  }

  public function listAction() {
    
  }

  public function newsegmentAction() {
    $contactlist = Contactlist::find(["conditions" => "idSubaccount = ?0", "bind" => [0 => $this->user->Usertype->idSubaccount]]);
    $this->view->setVar("contactlist", $contactlist);
  }

  public function editsegmentAction() {
    
  }

}
