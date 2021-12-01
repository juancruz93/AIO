<?php

use Sigmamovil\Wrapper\TaxWrapper as TW;

/**
 * @RoutePrefix("/api/tax")
 */
class ApitaxController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}/{name}")
   */
  public function listtaxAction($page, $name = "") {
    try {
      $wrapper = new TW();
      return $this->set_json_response($wrapper->listTax($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listfull/{id:[0-9]+}")
   */
  public function listtaxfullAction($id) {
    try {
      $wrapper = new TW();
      return $this->set_json_response($wrapper->listFullTax($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createtaxAction() {
    try {
      $json = $this->request->getRawBody();
      $arrayData = json_decode($json, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new TW();
      $this->trace("success", "Se ha creado un impuesto");

      return $this->set_json_response($wrapper->createTax($arrayData));
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
  public function gettaxAction($id) {
    try {
      $wrapper = new TW();
      return $this->set_json_response($wrapper->getTax($id));
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
  public function edittaxAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new TW();
      $this->trace("success", "Se ha editado un impuesto");

      return $this->set_json_response($wrapper->editTax($data));
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
  public function deletetaxAction($id) {
    try {
      $wrapper = new TW();
      $this->trace("success", "Se ha eliminado un impuesto");

      return $this->set_json_response($wrapper->deleteTax($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete tax ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
