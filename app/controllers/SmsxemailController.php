<?php

class SmsxemailController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Sms por Email");
    parent::initialize();
  }
  
  public function indexAction(){
    $this->view->setVar("app_name", "smsxemailApp");
  }
  
  public function listAction(){
    
  }

  public function createAction(){
    
  }
  
  public function generatekeyAction(){
    try {
      $key = uniqid('', true);
      $data = $this->user->Usertype->Subaccount->idSubaccount . '-' . $this->user->idUser . '-' . $key;
      if(!$data){
        throw new InvalidArgumentException("No se ha podido generar una Clave.");
      }
      return $this->set_json_response(array('data' => $data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding listfullservices ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  public function smscategoryAction(){
    try {
      $sql ="SELECT name, idSmsCategory FROM sms_category WHERE name='Envio Sms Por Email'";
      $smsCategory = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
      $data = [];
      if (count($smsCategory) > 0) {
        $data[] = array(
            "idSmsCategory" => $smsCategory[0]['idSmsCategory'],
            "name" => $smsCategory[0]['name']
        );
      }
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding listfullservices ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
