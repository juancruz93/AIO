<?php

/**
 * @RoutePrefix("/api/namesender")
 */
class ApinamesenderController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listnamesenderAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $namesenderwrapper = new \Sigmamovil\Wrapper\NamesenderWrapper();
      return $this->set_json_response($namesenderwrapper->listNamesender($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding namesender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savenamesender")
   */
  public function savenamesenderAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData->name) {
        throw new InvalidArgumentException("El nombre de remitente es requerido");
      }

      $namesenderwrapper = new \Sigmamovil\Wrapper\NamesenderWrapper();
      $idNameSender = $namesenderwrapper->saveNamesender($arrayData);

      $this->trace("success", "La categoría de plantilla ha sido guardada");

      return $this->set_json_response(["message" => "La categoría de plantillas ha sido guardata exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save namesender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deletenamesenderAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado ningun nombre de remitente a eliminar, por favor valide la información");
      }
      $namesenderwrapper = new \Sigmamovil\Wrapper\NamesenderWrapper();
      $namesenderwrapper->deletenamesender($arrayData);
      $this->trace("success", "El nombre de remitente ha sido eliminada");

      return $this->set_json_response(["message" => "El nombre de remitente ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete namesender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Get("/getnamesender/{id:[0-9]+}")
   */
  public function getnamesenderAction($id) {    
    try {
      $namesenderwrapper = new \Sigmamovil\Wrapper\NamesenderWrapper();
      return $this->set_json_response($namesenderwrapper->getnamesender($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Post("/edit")
   */
  public function editnamesenderAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      
      if (!$arrayData['name']) {
        throw new InvalidArgumentException("El nombre de remitente es requerido");
      }
      $namesenderwrapper = new \Sigmamovil\Wrapper\NamesenderWrapper();
      $namesenderwrapper->editnamesender($arrayData);
      $this->trace("success", "El nombre del remitente ha sido editado");

      return $this->set_json_response(["message" => "El nombre del remitente ha sido editado exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit namesender ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
