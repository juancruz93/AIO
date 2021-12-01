<?php

use Sigmamovil\Wrapper\SmstemplateWrapper;

/**
 * @RoutePrefix("/api/smstemplate")
 */
class ApismstemplateController extends ControllerBase {

  /**
   * @Post("/listsmstemp/{page:[0-9]+}")
   */
  public function listsmstemplateAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->listSmsTemplate($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savesmstemp")
   */
  public function savesmstemplateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new SmstemplateWrapper();
      $wrapper->saveSmsTemplate($arrayData);
      $this->trace("success", "Se ha creado la plantilla de SMS");

      return $this->set_json_response(["message" => "La plantilla para SMS ha sido guardada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getsmstemp/{id:[0-9]+}")
   */
  public function getsmstemplateAction($id) {
    try {
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->getSmsTemplate($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/editsmstemp")
   */
  public function editsmstemplateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new SmstemplateWrapper();
      $wrapper->editSmsTemplate($arrayData);
      $this->trace("success", "Se ha editado la plantilla de SMS");

      return $this->set_json_response(["message" => "Se ha editado la plantilla de SMS exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * flowchart - Library
   * @Get("/getsmstemplateautocomplete");
   */
  public function getsmstemplateautocompleteAction() {
    try {
      $filter = $_GET["q"];
      if (empty($filter)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->getsmstemplateautocomplete($filter));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get smstemplateautocomplete ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getallsmstemplate");
   */
  public function getallsmstemplateAction() {
    try {
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->getallsmstemplate());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get smstemplateautocomplete ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/deletesmstemplate/{idSmstemplate:[0-9]+}", methods="DELETE")
   */
  public function deletesmstemplateAction($idSmsTemplate) {
    try {
      $smstemplate = SmsTemplate::findFirst(array('conditions' => "idSmsTemplate = ?0", 'bind' => array($idSmsTemplate)));
      if (!$smstemplate) {
        throw new InvalidArgumentException("No se ha encontrado la plantilla, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmstemplateWrapper;
      $wrapper->setSmstemplate($smstemplate);
      $wrapper->deleteSmstemplate();
      $this->trace("success", "Se ha eliminado la plantilla de SMS");

      return $this->set_json_response(array("message" => "Se ha eliminado la plantilla exitosamente", "smstemplate" => $smstemplate), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/gettags");
   */
  public function gettagsAction() {
    try {
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->getalltags());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get all tags ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listfull")
   */
  public function listfullsmstemplateAction() {
    try {
      $wrapper = new SmstemplateWrapper();
      return $this->set_json_response($wrapper->listFullSmsTemplateByAccount());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get all smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
