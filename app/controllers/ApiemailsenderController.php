<?php

/**
 * @RoutePrefix("/api/emailsender")
 */
class ApiemailsenderController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listemailsenderAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $emailsenderWrapper = new \Sigmamovil\Wrapper\EmailsenderWrapper();
      return $this->set_json_response($emailsenderWrapper->listEmailsender($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding emailsender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/saveemailsender")
   */
  public function saveemailsenderAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData->email) {
        throw new InvalidArgumentException("El correo remitente es requerido");
      }

      $emailsenderWrapper = new \Sigmamovil\Wrapper\EmailsenderWrapper();
      $idEmailsender = $emailsenderWrapper->saveEmailsender($arrayData);

      $this->trace("success", "El correo de remitente ha sido guardado");

      return $this->set_json_response(["message" => "El correo de remitente ha sido guardado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save emailsender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deleteemailsenderAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado ningun correo de remitente a eliminar, por favor valide la información");
      }
      $emailsenderWrapper = new \Sigmamovil\Wrapper\EmailsenderWrapper();
      $emailsenderWrapper->deleteEmailsender($arrayData);
      $this->trace("success", "El correo de remitente ha sido eliminado");

      return $this->set_json_response(["message" => "El correo de remitente ha sido eliminado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete emailsender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Get("/getemailsender/{id:[0-9]+}")
   */
  public function getemailsenderAction($id) {    
    try {
      $emailsenderWrapper = new \Sigmamovil\Wrapper\EmailsenderWrapper();
      return $this->set_json_response($emailsenderWrapper->getEmailsender($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding emailsender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Post("/edit")
   */
  public function editemailsenderAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      
      if (!$arrayData['email']) {
        throw new InvalidArgumentException("El correo remitente requerido");
      }
      $emailsenderWrapper = new \Sigmamovil\Wrapper\EmailsenderWrapper();
      $emailsenderWrapper->editEmailsender($arrayData);
      $this->trace("success", "El correo de remitente ha sido editado");

      return $this->set_json_response(["message" => "El correo de remitente ha sido editado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit emailsender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
