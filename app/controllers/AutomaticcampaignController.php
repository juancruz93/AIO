<?php

class AutomaticcampaignController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Campañas Automáticas");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "automaticcampaign");
  }

  public function createAction() {
    
  }

  public function editAction() {
    
  }

  public function listAction() {
    
  }

  public function viewschemeAction() {
    
  }

}
