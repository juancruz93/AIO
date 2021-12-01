<?php

/**
 * @RoutePrefix("/api/knowledgebase")
 */
class ApiknowledgebaseController extends ControllerBase {

  /**
   * 
   * @Post("/importcsv")
   */
  public function importcsvAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\KnowledgebaseWrapper();
      
      if (isset($_FILES['file'])) {
        $csv = $_FILES['file'];
        $wrapper->setCSV($csv);
      }
      $wrapper->importcsv();
      $this->trace("success", "Se ha importado un archivo CSV a la lista de correos bloqueados");
      return $this->set_json_response(null, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while importing CSV... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   * 
   * @Post("/validatecsv")
   */
  public function validatecsvAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\KnowledgebaseWrapper();
      
      if (isset($_FILES['file'])) {
        $csv = $_FILES['file'];
        $wrapper->setCSV($csv);
      }
      $wrapper->validatecsv();
      $this->trace("success", "Se ha importado un archivo CSV a la lista de correos bloqueados");
      return $this->set_json_response(null, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while importing CSV... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * 
   * @Post("/getimports/{page:[0-9]+}")
   */
  public function getimportsAction($page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\KnowledgebaseWrapper();
      $wrapper->findImports($page);
      return $this->set_json_response($wrapper->getImports(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding imports... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
