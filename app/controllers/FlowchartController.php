<?php

class FlowchartController extends ControllerBase{
  public function indexAction(){
    
  }
  public function callAction(){
    
  }
  
  public function popoversegmentAction(){
    
  }
  
  public function popovertimeAction(){
    
  }
  
  public function popovermailAction(){
    
  }
  
  public function popoversmsAction(){
    
  }
  
  public function popoveractionAction(){
    
  }
    
  public function popoverclickAction(){
    
  }
    
  public function popoverlinksAction(){
    
  }
  
  public function connectionAction(){
    
  }
  public function popoversurveyAction(){
    
  }
  public function frameeditorAction($idTemplate){
    $mailtemplatecontent = \MailTemplateContent::findFirst(array(
        "conditions" => "idMailTemplate = ?0",
        "bind" => array($idTemplate)
    ));
    
   $this->view->setVar('mail_content',$mailtemplatecontent); 
  }
  
  public function statictisAction(){}
}

