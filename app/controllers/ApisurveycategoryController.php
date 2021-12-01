<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Apisurveycategory
 *
 * @author juan.pinzon
 */
use Sigmamovil\Wrapper\SurveycategoryWrapper as sc;

/**
 * @RoutePrefix("/api/surveycategory")
 */
class ApisurveycategoryController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}/{name}")
   */
  public function listsurveycategoryAction($page, $name = "") {
    try {
      $wrapper = new sc();
      return $this->set_json_response($wrapper->listSurveyCategory($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createsurveycategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new sc();
      $this->trace("success", "Se ha guardado la categoría de una encuesta");

      return $this->set_json_response($wrapper->createSurveyCategory($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getone/{id:[0-9]+}")
   */
  public function getsurveycategoryAction($id) {
    try {
      $wrapper = new sc();
      return $this->set_json_response($wrapper->getOneSurveyCategory($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getOne surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit")
   */
  public function editsurveycategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new sc();
      $this->trace("success", "Se ha editado la categoría de una encuesta");

      return $this->set_json_response($wrapper->editSurveyCategory($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Delete("/delete/{id:[0-9]+}")
   */
  public function deletesurveycategoryAction($id) {
    try {
      $wrapper = new sc();
      $this->trace("success", "Se ha eliminado la categoría de una encuesta");

      return $this->set_json_response($wrapper->deleteSurveyCategory($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
