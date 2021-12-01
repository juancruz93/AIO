<?php

use Sigmamovil\Wrapper\MailtemplatecategoryWrapper;

/**
 * @RoutePrefix("/api/mailcategorytemplatecategory")
 */
class ApimailtemplatecategoryController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listmailtemplatecategoryAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $mailtemplatecategorywrapper = new \Sigmamovil\Wrapper\MailtemplatecategoryWrapper();
      return $this->set_json_response($mailtemplatecategorywrapper->listmailtemplatecategory($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding plantilla ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getmailtempcategory")
   */
  public function getmailtemplatecategoryAction() {
    try {
      $mailtemplatecategorywrapper = new \Sigmamovil\Wrapper\MailtemplatecategoryWrapper();
      return $this->set_json_response($mailtemplatecategorywrapper->findMailTemplateCategory());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mail template category ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getmailtempcategfilt")
   */
  public function getmailtemplatecategoryfilterAction() {
    try {
      $mailtemplatecategorywrapper = new \Sigmamovil\Wrapper\MailtemplatecategoryWrapper();
      return $this->set_json_response($mailtemplatecategorywrapper->findMailTemplateCategoryFilter());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mail template category for filter ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemailtempcategory")
   */
  public function savemailtemplatecategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData->name) {
        throw new InvalidArgumentException("El nombre de la categoria es requerido");
      }

      $mailtempcatwrapper = new MailtemplatecategoryWrapper();
      $idMailTemplateCategory = $mailtempcatwrapper->saveMailTemplateCategory($arrayData);
 
      $this->trace("success", "La categoría de plantilla ha sido guardada");

      return $this->set_json_response(["message" => "La categoría de plantillas ha sido guardata exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save plantilla ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deletemailtemplatecategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No se ha seleccionado que categoría va a eliminar, por favor valide la información");
      }
      $mailtempcatwrapper = new MailtemplatecategoryWrapper();
      $mailtempcatwrapper->deletemailtemplatecategory($arrayData);
      $this->trace("success", "La categoría de plantilla ha sido eliminada");

      return $this->set_json_response(["message" => "La categoría de plantillas ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete plantilla ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getmailtemplate/{id:[0-9]+}")
   */
  public function getmailtemplateAction($id) {
    
    try {
      $mailtempcatwrapper = new MailtemplatecategoryWrapper();
      return $this->set_json_response($mailtempcatwrapper->getmailtemplate($id));
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
  public function editmailtemplatecategoryAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      
      if (!$arrayData['name']) {
        throw new InvalidArgumentException("El nombre de la categoria es requerido");
      }
      $mailtempcatwrapper = new MailtemplatecategoryWrapper();
      $mailtempcatwrapper->editmailtemplatecategory($arrayData);
      $this->trace("success", "La categoría de plantilla ha sido editada");

      return $this->set_json_response(["message" => "La categoría de plantillas ha sido editada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit plantilla ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
