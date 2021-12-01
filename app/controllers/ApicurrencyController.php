<?php

use Sigmamovil\Wrapper\CurrencyWrapper;

/**
 * @RoutePrefix("/api/currency")
 */
class ApicurrencyController extends ControllerBase {

  /**
   * @Get("/listcurrency/{page:[0-9]+}/{name}")
   */
  public function listcurrencyAction($page, $name = "") {
    try {
      $wrapper = new CurrencyWrapper();
      return $this->set_json_response($wrapper->listCurrency($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createcurrencyAction() {
    try {
      $json = $this->request->getRawBody();
      $arrayData = json_decode($json, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new CurrencyWrapper();
      $this->trace("success", "La moneda ha sido creada");

      return $this->set_json_response($wrapper->createCurrency($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getone/{id:[0-9]+}")
   */
  public function getcurrencyAction($id) {
    try {
      $wrapper = new CurrencyWrapper();
      return $this->set_json_response($wrapper->getCurrency($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit/{id:[0-9]+}")
   */
  public function editcurrencyAction($id) {
    try {
      $json = $this->request->getRawBody();
      $arrayData = json_decode($json, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new CurrencyWrapper();
      $this->trace("success", "La moneda ha sido editada");

      return $this->set_json_response($wrapper->editCurrency($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Delete("/delete/{id:[0-9]+}")
   */
  public function deletecurrencyAction($id) {
    try {
      $wrapper = new CurrencyWrapper();
      $this->trace("success", "La moneda ha sido eliminada");

      return $this->set_json_response($wrapper->deleteCurrency($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
