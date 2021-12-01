<?php

/**
 * Description of ReportController
 *
 * @author desarrollo3
 */
class ReportController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Reportes");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "report");
  }

  public function indexsmsAction() {
    $this->view->setVar("app_name", "report");
  }

  public function listAction() {
    
  }

  public function listrechargeAction() {
    
  }
  
  public function changeplanuserAction() {
    
  }
  
  public function listsmsAction() {
    
  }

  public function listgraphAction() {
    
  }

  public function smsAction() {
    
  }

  public function graphAction() {
    $this->view->setVar("app_name", "graph");
  }

  public function excelsmsAction() {
    
  }

  public function excelsmsdayAction() {
    
  }

  public function infosmsAction() {
    
  }

  public function infomailAction() {
    
  }
  
  public function statisticmailAction(){
      
  }
  
  public function reportvalidationAction(){
      
  }
  
  public function listsmschannelAction() {
    
  }
  
  public function reportmailAction($title){
       
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if($idAllied != Null){
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $wrapper->mailExport($idAllied, $title);
    }
    else{
      return $this->response->redirect("report/index#/mail");
    }
  }
  
  public function reportsmsAction($title){
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if($idAllied != Null){
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $wrapper->smsExport($idAllied, $title);
    }
    else{
      return $this->response->redirect("report/index#/sms");
    }
  }
  
  public function smsxemailAction(){

  }
  
  public function infosmsbydestinatariesAction(){
    
  }
  
  public function downloadsmxemailAction($idSms){
    $conditions = array(
      "conditions" => "idSubaccount = ?0 AND idSms = ?1",
      "bind" => array($this->user->Usertype->Subaccount->idSubaccount,$idSms),
    );    
    $sms = \Sms::findFirst($conditions);
    if($sms != Null){
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $wrapper->downloadsmxemail($sms);
    }
    else{
      return $this->response->redirect("report/index#/smsxemail");
    }
  }
}
