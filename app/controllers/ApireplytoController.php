<?php

/**
 * @RoutePrefix("/api/replyto")
 */
class ApireplytoController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listreplytoAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $replytoWrapper = new \Sigmamovil\Wrapper\ReplytoWrapper();
      return $this->set_json_response($replytoWrapper->listReplyto($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding replyto ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savereplyto")
   */
  public function savereplytoAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData->email) {
        throw new InvalidArgumentException("El correo es requerido");
      }

      $replytoWrapper = new \Sigmamovil\Wrapper\ReplytoWrapper();
      $idReplyTo = $replytoWrapper->saveReplyto($arrayData);

      $this->trace("success", "El correo de respuesta ha sido guardado");

      return $this->set_json_response(["message" => "El correo de respuesta ha sido guardado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save ReplyTo ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deletereplytoAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado ningun correo de respuesta a eliminar, por favor valide la información");
      }
      $replytoWrapper = new \Sigmamovil\Wrapper\ReplytoWrapper();
      $replytoWrapper->deletereplyto($arrayData);
      $this->trace("success", "El correo de respuesta ha sido eliminada");

      return $this->set_json_response(["message" => "El correo de respuesta ha sido eliminado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete ReplyTo ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Get("/getreplyto/{id:[0-9]+}")
   */
  public function getreplytoAction($id) {    
    try {
      $replytoWrapper = new \Sigmamovil\Wrapper\ReplytoWrapper();
      return $this->set_json_response($replytoWrapper->getreplyto($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ReplyTo ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Post("/edit")
   */
  public function editreplytoAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      
      if (!$arrayData['email']) {
        throw new InvalidArgumentException("El correo es requerido");
      }
      $replytoWrapper = new \Sigmamovil\Wrapper\ReplytoWrapper();
      $replytoWrapper->editreplyto($arrayData);
      $this->trace("success", "El correo de respuesta ha sido editado");

      return $this->set_json_response(["message" => "El correo de respuesta ha sido editado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit ReplyTo ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
