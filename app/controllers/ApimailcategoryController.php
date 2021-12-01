<?php

/**
 * @RoutePrefix("/api/mailcategory")
 */
class ApimailcategoryController extends ControllerBase {

  /**
   * 
   * @Post("/getmailcategory/{page:[0-9]+}")
   */
  public function getmailcategoryAction($page) {
    try {
      
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      
      $mailcategory = MailCategory::find();
      if (!$mailcategory) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      return $this->set_json_response($wrapper->findMailCategory($page, $data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemailcategory")
   */
  public function savemailcategoryAction() {
    try {

      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $wrapper->validateNameMailCategory($arrayData['name']);
      $objReturn = array();
      if ($wrapper->saveMailCategory($arrayData)) {
        $objReturn = array("message" => "Se ingresó la categoría correctamente");
      }
      $this->trace("success", "Se ha guardado la categoría de mail");

      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getonemailcategory/{id:[0-9]+}")
   */
  public function getonemailcategoryAction($id) {
    try {

      $mailcategory = MailCategory::findFirst(array("conditions" => "idMailCategory = ?0", "bind" => array($id)));

      if (!$mailcategory) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      return $this->set_json_response(array($mailcategory), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/editmailcategory")
   */
  public function editmailcategoryAction() {
    try {

      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $objReturn = array();
      if ($wrapper->editMailCategory($arrayData)) {
        $objReturn = array("message" => "Se modificó la categoría correctamente");
      }
      $this->trace("success", "Se ha editado la categoría de mail");

      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/deletemailcategory")
   */
  public function deletemailcategoryAction() {
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $mailcategory = new stdClass();
      $mailcategory->idmailcategory = $arrayData['idMailCategory'];

      if (!$mailcategory) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $objReturn = array();
      if (!$wrapper->deleteMailCategory($mailcategory)) {
        $this->db->rollback();
      }
      $objReturn = array("message" => "Se eliminó la categoría correctamente");
      $this->db->commit();
      $this->trace("success", "Se ha eliminado la categoría de mail");

      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting mailcategorylist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * flowchart - Library
   * @Get("/getautocompletecategory")
   */
  public function getautocompletecategoryAction() {
    try {
      $filter = $_GET["q"];
      if (empty($filter)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      return $this->set_json_response($wrapper->getautomaticcampaignautocomplete($filter));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get smstemplateautocomplete ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getallmailcategory")
   */
  public function getallmailcategoryAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      return $this->set_json_response($wrapper->getallmailcategorys());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get smstemplateautocomplete ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemailcategoryinmail")
   */
  public function savemailcategoryinmailAction() {
    try {

      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $wrapper->validateNameMailCategory($arrayData['name']);
      $objReturn = array();
      $objReturn['msg'] = "Se ingresó la categoría correctamente";
      $objReturn["category"] = $wrapper->saveMailCategoryInMail($arrayData);
      $this->trace("success", "Se ha guardado la categoría de mail");

      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}