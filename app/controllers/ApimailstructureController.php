<?php

/**
 * @RoutePrefix("/api/mailstructure")
 */
class ApimailstructureController extends ControllerBase {

  /**
   * 
   * @Post("/create")
   */
  public function createAction() {
    try {
//      $dataJson = $this->request->getRawBody();
//      $arrayData = json_decode($dataJson, true);
      $wrapper = new Sigmamovil\Wrapper\MailstructureWrapper();
      $wrapper->createMailStructure($_POST, $_FILES['preview']);
      $objReturn = array("message" => "se creado la plantilla predeterminada");
      $this->trace("success", "Se ha creado la plantilla predeterminada");

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
   * @Post("/getall/{page:[0-9]+}")
   */
  public function getallAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $wrapper = new Sigmamovil\Wrapper\MailstructureWrapper();
      $wrapper->findAll($page, $arrayData);
      return $this->set_json_response($wrapper->getMailstructure(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Delete("/deletestructure/{id:[0-9]+}")
   */
  public function deletestructureAction($id) {
    try {
      $wrapper = new Sigmamovil\Wrapper\MailstructureWrapper();
      $wrapper->deletestructure($id);
      $this->trace("success", "Se ha eliminado estrcuturas prediseñadas");

      return $this->set_json_response(["msg" => "Se ha eliminado estructuras prediseñadas"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/editmailstructure")
   */
  public function editmailstructureAction() {
    try {
//      $dataJson = $this->request->getRawBody();
//      $arrayData = json_decode($dataJson, true);
      $wrapper = new Sigmamovil\Wrapper\MailstructureWrapper();
      $wrapper->editstructure($_POST, $_FILES['preview']);
      $this->trace("success", "Se ha eliminado estructuras prediseñadas");

      return $this->set_json_response(["msg" => "Se ha eliminado estructuras prediseñadas"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
