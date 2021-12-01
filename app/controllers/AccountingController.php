<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountingController
 *
 * @author juan.pinzon
 */
class AccountingController extends ControllerBase {

  public function initialize() {
    parent::initialize();
    $this->tag->setTitle("Contabilidad");
  }

  public function indexAction() {
    $this->view->setVar("app_name", "accounting");
  }

  public function listAction() {
    
  }

}
