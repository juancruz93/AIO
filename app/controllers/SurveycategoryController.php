<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SurveycategoryController
 *
 * @author juan.pinzon
 */
class SurveycategoryController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Categorías de encuestas");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "surveycategory");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new SurveycategoryForm);
  }

  public function editAction() {
    $this->view->setVar("form", new SurveycategoryForm);
  }

}
