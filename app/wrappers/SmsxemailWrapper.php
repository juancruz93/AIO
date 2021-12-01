<?php

namespace Sigmamovil\Wrapper;

class SmsxemailWrapper extends \BaseWrapper {
  
  public function getAll($page){

    $conditions = array(
      "conditions" => "idSubaccount = ?0",
      "bind" => array($this->user->Usertype->Subaccount->idSubaccount),
      "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
      "offset" => $page,
      "order" => "idSmsxEmail DESC"
    );    
    $smsxemail = \Smsxemail::find($conditions);
    $data = array();
    if (count($smsxemail) > 0) {
      foreach ($smsxemail as $key => $value) {
        $data[$key] = array(
          "idSmsxEmail" => $value->idSmsxEmail,
          "senderEmail" => $value->senderEmail,
          "generateKey" => $value->generateKey,
          "idSmsCategory" => $value->idSmsCategory,
          "notificationEmail" => $value->notificationEmail,
        );
      }
    }
    
    $total = \Smsxemail::count($conditions);
    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $data
    );
  }

  public function createAction($data){
    
    $Smsxemail = \Smsxemail::findFirst(array("conditions" => "senderEmail = ?0","bind" => array($data['senderEmail'])));
    if($Smsxemail){
      throw new \InvalidArgumentException("El correo electronico del remitente ya existe.");    
    }
    $form = new \SmsxemailForm();
    $modelSmsxemail = new \Smsxemail();
    $modelSmsxemail->senderEmail = $data['senderEmail'];
    $modelSmsxemail->idSmsCategory = $data['idSmsCategory'];
    $modelSmsxemail->generateKey = $data['generateKey'];
    $modelSmsxemail->notificationEmail = $data['notificationEmail'];
    $modelSmsxemail->idSubaccount = ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->idSubaccount : NULL);
    //
    $form->bind($data, $modelSmsxemail);
    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    if (!$modelSmsxemail->save()) {
      foreach ($modelSmsxemail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    
    return $modelSmsxemail;
  }
  
  public function getOne(){
    $getSmsxemail = \Smsxemail::findFirst(array("conditions" => "idSubaccount = ?0","bind" => array($this->user->Usertype->Subaccount->idSubaccount)));
    return array(
      "idSmsxEmail"=>$getSmsxemail->idSmsxEmail,
      "senderEmail"=>$getSmsxemail->senderEmail,
      "generateKey"=>$getSmsxemail->generateKey,
      "idSmsCategory" =>$getSmsxemail->idSmsCategory,
      "notificationEmail"=>$getSmsxemail->notificationEmail,
      "idSubaccount"=>$getSmsxemail->idSubaccount,
    );
  }
  
  public function copyGenerator($idSmsxEmail){
    $modelSmsxemail = \Smsxemail::findFirst(array("conditions" => "idSmsxEmail =?0 ","bind" => array($idSmsxEmail)));
    if (!$modelSmsxemail) {
      throw new \InvalidArgumentException("La configuración de email para envíos de SMS no existe");
    }
    return ["copy" => $modelSmsxemail->generateKey];
  }
  
}
