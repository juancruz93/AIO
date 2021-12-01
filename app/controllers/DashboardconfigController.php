<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DashboardconfigController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Configuración del Dashboard");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("idAllied",$this->user->Usertype->idAllied);
  }
  public function configdashboardAction(){
    
  }
  
}