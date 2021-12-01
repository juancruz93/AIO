<?php

/**
 * @RoutePrefix("/api/accountcategory")
 */

class ApiaccountcategoryController extends ControllerBase
{

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      return $this->set_json_response($wrapper->listAccountCategory($page, $arraydata));
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
  public function saveaccountcategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      $wrapper->saveAccountCategory($arrayData);
      return $this->set_json_response(["message" => "La categoria de cuenta ha sido creada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/get/{id:[0-9]+}")
   */
  public function getaccountcategoryAction($id) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      return $this->set_json_response($wrapper->getAccountCategory($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit")
   */
  public function editaccountcategoryAction() {
    try {
      
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);
      unset($arrayData['idMasteraccount'], $arrayData['idAllied']);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      $wrapper->editAccountCategory($arrayData);
      return $this->set_json_response(["message" => "La categoría de cuenta ha sido editada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{id:[0-9]+}", methods="DELETE")
   */
  public function deleteaccountcategoryAction($idAccountCategory) {
    try {
      if (empty($idAccountCategory)) {
        throw new InvalidArgumentException("No se ha seleccionado que categoría va a eliminar, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      $wrapper->deleteAccountCategory($idAccountCategory);
      return $this->set_json_response(["message" => "La categoría de cuenta ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getaccountcategories")
   */
  public function getaccountcategoriesAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      return $this->set_json_response($wrapper->getAccountCategories());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    
  public function gethabeasdataAction(){
    try {
      $wrapper = new \Sigmamovil\Wrapper\AccountcategoryWrapper();
      return $this->set_json_response($wrapper->getAccountCategories());
    } catch (Exception $ex) {
      $this->logger->log("Exception while save automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
}
