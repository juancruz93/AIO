<?php

use Sigmamovil\Wrapper\AutomaticcampaigncategoryWrapper as acc;

/**
 * @RoutePrefix("/api/automacampcateg")
 */
class ApiautomaticcampaigncategoryController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listautocampcategAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new acc();
      return $this->set_json_response($wrapper->listautomaticcampaigncategory($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/save")
   */
  public function saveautomacampcategAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new acc();
      $wrapper->saveautomaticcampaigncategory($arrayData);
      $this->trace("success", "La categoría de automatización de campaña ha sido guardada");

      return $this->set_json_response(["message" => "La categoria de automatización de campaña ha sido guardada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/get/{page:[0-9]+}")
   */
  public function getautomaticcampcategAction($id) {
    try {
      $wrapper = new acc();
      return $this->set_json_response($wrapper->getautomaticcampaigncategory($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/edit")
   */
  public function editautomaticcampcategAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new acc();
      $wrapper->editautomaticcampaigncategory($arrayData);
      $this->trace("success", "La categoría de automatización de campaña ha sido editada");

      return $this->set_json_response(["message" => "La categoría de automatización de campaña ha sido editada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deleteautomaticcampcategAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado que categoría va a eliminar, por favor valide la información");
      }
      $wrapper = new acc();
      $wrapper->deleteautomaticcampaigncategory($arrayData);
      $this->trace("success", "La categoría de automatización de campaña ha sido eliminada");

      return $this->set_json_response(["message" => "La categoría de automatización de campaña ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcategory");
   */
  public function getcategoryAction() {
    try {
      $filter = $_GET["q"];
      if (empty($filter)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new acc();
      return $this->set_json_response($wrapper->getcategoryautocomplete($filter));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get getcategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/allcategory");
   */
  public function allcategoryAction() {
    try {
      $wrapper = new acc();
      return $this->set_json_response($wrapper->getallcategory());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get getcategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
