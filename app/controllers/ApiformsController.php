<?php

/**
 * Description of ApiformsController
 * @RoutePrefix("/api/forms")
 * @author desarrollo3
 */
class ApiformsController extends \ControllerBase {

  /**
   *
   * @Post("/savebasicinformation")
   */
  public function savebasicinformationAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $wrapper->setData($data);
      return $this->set_json_response($wrapper->saveBasicInformation());
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/listforms/{page:[0-9]+}")
   */
  public function listformsAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->listForms($page, $arraydata));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/saveforms/{idForm:[0-9]+}")
   */
  public function saveformsAction($idForm) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $this->trace("success", "Se ha guardado el formulario");

      return $this->set_json_response($wrapper->saveForm($idForm, $arraydata));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getallformscategories")
   */
  public function getallformscategoriesAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getAllFormsCategories());
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getinformationform/{idinfo:[0-9]+}")
   */
  public function getinformationformAction($idinfo) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getInformationForm($idinfo));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getcontentform/{idform:[0-9]+}")
   */
  public function getcontentformAction($idform) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getContentForm($idform));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getoptin/{idform:[0-9]+}")
   */
  public function getoptinAction($idform) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getOptinForm($idform));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getwelcomemail/{idform:[0-9]+}")
   */
  public function getwelcomemailAction($idform) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getWelcomeMailForm($idform));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getnotificationform/{idform:[0-9]+}")
   */
  public function getnotificationformAction($idform) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      return $this->set_json_response($wrapper->getNotificationForm($idform));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Put("/updatebasicinformation/{idform:[0-9]+}")
   */
  public function updatebasicinformationAction($idform) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $wrapper->setData($arraydata);
      return $this->set_json_response($wrapper->updateBasicInformation($idform));
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addformcategory")
   */
  public function addformcategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $this->trace("success", "Se ha guardado el la categoría del formulario");

      return $this->set_json_response(array("idFormCategory" => $wrapper->saveFormCategoryAction($arrayData), "msg" => "Se ha guardado la categoría"), 200, "OK");
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $ex) {
      $this->logger->log("Exception while creating emailName: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }
  
  /**
   *
   * @Post("/getsuscriptsform/{idForm:[0-9]+}/{page:[0-9]+}")
   */
  public function getsuscriptsformAction($idForm, $page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $arrayDef = [];
      $wrapper->setReport($arrayDef);//clear array respuest
      $wrapper->setSearch($data);
      //$wrapper->setPage($page);
      $wrapper->getContactsForm($idForm, $page);
      $wrapper->getFieldsPersonalitiesForms($idForm);
      $wrapper->getFieldsForms($idForm, $contentsraw);
      return $this->set_json_response($wrapper->getReport(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while forms forms... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while forms forms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/dowloadreportcontactsform/{idForm:[0-9]+}")
   */
  public function dowloadreportcontactsformAction($idForm) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      //$wrapper->setSearch($data);
      return $this->set_json_response($wrapper->dowloadReportContactForms($idForm, $contentsraw), 200);
//      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/deleteform")
   */
  public function deleteformAction(){
    try{
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $forms = new stdClass();
      $forms->idform = $arrayData['idForm'];
      
      if (!$forms) {
        throw new InvalidArgumentException("Verifique la informacion enviada");
      } 
        
      $wrapper = new \Sigmamovil\Wrapper\FormsWrapper();
      $objReturn = array();
      if (!$wrapper->deleteForm($forms)){
        $this->db->rollback();
      }
      $objReturn = array("message" => "Se eliminó el formulario correctamente.");
      $this->db->commit();
      $this->trace("success", "se ha eliminado el formulario");
      
      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while deleting forms ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
