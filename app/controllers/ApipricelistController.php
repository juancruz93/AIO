<?php

use Sigmamovil\Wrapper\PricelistWrapper as PW;

/**
 * @RoutePrefix("/api/pricelist")
 */
class ApipricelistController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}/{name}")
   */
  public function listpricelistAction($page, $name = "") {
    try {
      $wrapper = new PW();
      return $this->set_json_response($wrapper->listPriceList($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding pricelist ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listfull/{idService:[0-9]+}/{name}")
   */
  public function listfullpricelistAction($idServices, $name = "") {
    try {
      $wrapper = new PW();
      return $this->set_json_response($wrapper->listfullPriceList($idServices, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding full pricelist ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createlistpriceAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json, TRUE);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new PW();
      $this->trace("success", "Se ha creado la lista de precios");

      return $this->set_json_response($wrapper->createPriceList($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/get/{id:[0-9]+}")
   */
  public function getpricelistAction($id) {
    try {
      $wrapper = new PW();
      return $this->set_json_response($wrapper->getPriceList($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit")
   */
  public function editpricelistAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json, TRUE);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new PW();
      $this->trace("success", "Se ha editado la lista de precios");

      return $this->set_json_response($wrapper->editPriceList($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Delete("/delete/{id:[0-9]+}")
   */
  public function deletepricelistAction($id) {
    try {
      $wrapper = new PW();
      $this->trace("success", "Se ha eliminado la lista de precios");

      return $this->set_json_response($wrapper->deletePriceList($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
