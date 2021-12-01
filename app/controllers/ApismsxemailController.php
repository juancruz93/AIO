<?php

/*
 * Api smsxemail
 */
use Sigmamovil\Wrapper\SmsxemailWrapper;
/**
 * @RoutePrefix("/api/smsxemail")
 */
class ApismsxemailController extends ControllerBase {
  
  /**
  * @Post("/getall/{page:[0-9]+}")
  */
  public function getallAction($page) {
    try {
      
      $wrapper = new SmsxemailWrapper();
      return $this->set_json_response($wrapper->getAll($page));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }  
  /**
   * @Post("/create")
   */
  public function createAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new SmsxemailWrapper();
      $wrapper->createAction($data);
      return $this->set_json_response(["message" => "La Configuración de Email para envíos de SMS ha sido creada exitosamente. "]);
      //return $this->response->redirect('tools');
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
  * @Get("/getone")
  */
  public function getoneAction() {
    try {
      $wrapper = new SmsxemailWrapper();
      return $this->set_json_response($wrapper->getOne());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Get("/copykey/{idSmsxEmail:[0-9]+}")
   */
  public function copykeyAction($idSmsxEmail){
    try {
      $wrapper = new SmsxemailWrapper();
      return $this->set_json_response($wrapper->copyGenerator($idSmsxEmail));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while generate link survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  

  public function download(){
    try {
      var_dump("Entra");exit;
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while generate link survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
