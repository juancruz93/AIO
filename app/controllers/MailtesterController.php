<?php

class MailtesterController extends ControllerBase{
  
  public function showAction($mailtester,$idAllied){
     $this->view->setVar("mailTester", $mailtester);
     $this->view->setVar("idAllied", $idAllied);
  }
}
