<?php

/**
 * @RoutePrefix("/api/footer")
 */
class ApifooterController extends ControllerBase {

  /**
   * @Get("/{page:[0-9]+}")
   */
  public function indexAction($page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FooterWrapper();
      return $this->set_json_response($wrapper->findFooter($page), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/findfooter/{idFooter:[0-9]+}")
   */
  public function findfooterAction($idFooter) {
    try {
      $footer = Footer::findFirst(array(
                  "conditions" => "idFooter = ?0",
                  "bind" => array(0 => $idFooter)
      ));

      if (!$footer) {
        throw new InvalidArgumentException("El footer que desea editar no existe, por favor valide la información");
      }

      return $this->set_json_response(array($footer), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding footer ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\FooterWrapper();
      $wrapper->saveFooter($arrayData);
      \Phalcon\DI::getDefault()->get('notification')->success("Se ha Guardado el footer correctamente");
      $this->trace("success", "Se ha guardado el footer");

      return $this->set_json_response(array("message" => ""), 200);
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array('message' => $msg->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/update")
   */
  public function updateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\FooterWrapper();
      $wrapper->updateFooter($arrayData);
      \Phalcon\DI::getDefault()->get('notification')->info("Se ha actualizado el footer correctamente");
      $this->trace("success", "Se ha actualizado el footer");

      return $this->set_json_response(array("message" => ""), 200);
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array('message' => $msg->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{idFooter:[0-9]+}", methods="DELETE")
   */
  public function deleteAction($idFooter) {
    try {
      $footer = Footer::findFirst(array(
                  "conditions" => "idFooter = ?0",
                  "bind" => array(0 => $idFooter)
      ));

      if (!$footer) {
        throw new InvalidArgumentException("El footer que desea editar no existe, por favor valide la información");
      }
      $footer->deleted = 1;

      if (!$footer->update()) {
        throw new InvalidArgumentException("No se pudo eliminar el registro");
      }
      $this->trace("success", "Se ha eliminado el footer");

      return $this->set_json_response(array("message" => "Se ha eliminado el footer correctamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding footer ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
