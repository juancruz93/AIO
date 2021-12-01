<?php

/**
 * @RoutePrefix("/api/ip")
 */
class ApiipController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listipAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $ipWrapper = new \Sigmamovil\Wrapper\IpWrapper();
      return $this->set_json_response($ipWrapper->listIp($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/saveip")
   */
  public function saveipAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData->ip) {
        throw new InvalidArgumentException("la dirección IP es requerida");
      }

      $ipWrapper = new \Sigmamovil\Wrapper\IpWrapper();
      $idIp = $ipWrapper->saveIp($arrayData);

      $this->trace("success", "La dirección IP ha sido guardada correctamente");

      return $this->set_json_response(["message" => "La direccion IP ha sido guardada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save ip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deleteipAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado ninguna dirección IP a eliminar, por favor valide la información");
      }
      $ipWrapper = new \Sigmamovil\Wrapper\IpWrapper();
      $ipWrapper->deleteIp($arrayData);
      $this->trace("success", "La dirección IP ha sido eliminada");

      return $this->set_json_response(["message" => "La dirección IP ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete ip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getip/{id:[0-9]+}")
   */
  public function getipAction($id) {
    try {
      $ipWrapper = new \Sigmamovil\Wrapper\IpWrapper();
      return $this->set_json_response($ipWrapper->getIp($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/edit")
   */
  public function editipAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);

      if (!$arrayData['ip']) {
        throw new InvalidArgumentException("la dirección IP es requerida");
      }
      $ipWrapper = new \Sigmamovil\Wrapper\IpWrapper();
      $ipWrapper->editIp($arrayData);
      $this->trace("success", "La dirección IP ha sido editada");

      return $this->set_json_response(["message" => "La dirección IP ha sido editada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit ip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
