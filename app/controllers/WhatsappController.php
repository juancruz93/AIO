<?php

use Sigmamovil\General\Links\ParametersEncoder;

class WhatsappController extends ControllerBase {

    public function initialize() {
      $this->tag->setTitle("Envíos de Whatsapp");
      parent::initialize();
    }
  
    public function indexAction() {
      $flag = false;
      foreach ($this->user->Usertype->subaccount->saxs as $key) {
        if ($key->idServices == 13 && $key->status ==1) {
          $flag = true;
        }
      }
      if ($flag == false ) {
        $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
        return $this->response->redirect("");
      }
    }

    public function getreceiverAction(){
        try {
          $contentsraw = $this->getRequestContent();
          $data = json_decode($contentsraw, true);
          
          $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
          $wrapper->receiverWhatsapp($data);
          //return $this->set_json_response($wrapper->receiverWhatsapp($data), 200);
          return $this->set_json_response("Completado", 200);
        } catch (InvalidArgumentException $ex) {
          $this->db->rollback();
          return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
          $this->logger->log("Exception while finding contactlist ... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
        
      }

    public function listAction(){}

    public function createAction(){}
  

}