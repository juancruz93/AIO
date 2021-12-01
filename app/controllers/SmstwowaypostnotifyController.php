<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SmstwowaypostnotifiyController
 *
 * @author juan.pinzon
 */
class SmstwowaypostnotifyController extends ControllerBase {
  
   public function initialize() {
    $this->tag->setTitle("Smstwoway Post Notifications");
    parent::initialize();
  }
  
  public function indexAction(){
    $this->view->setVar("app_name", "smstwowaypostnotify");
  }
  public function listAction(){
    
  }
  
   public function createAction(){
    
  }
  //put your code here
}
