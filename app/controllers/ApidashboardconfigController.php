<?php

/**
 * @RoutePrefix("/api/dashboardconfig")
 */
class ApidashboardconfigController extends ControllerBase {

  /**
   * @Get("/getallimage/{idaccount:[0-9]+}/{page:[0-9]+}")
   */
  public function getallimageAction($idaccount, $page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      return $this->set_json_response($wrapper->getAllImage($idaccount, $page), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Get("/getconfigdashboardclient/{idaccount:[0-9]+}")
   */
  public function getconfigdashboardclientAction($idaccount) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      return $this->set_json_response($wrapper->getConfigDashboardClient($idaccount), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcondigdashboard/{idaccount:[0-9]+}")
   */
  public function getcondigdashboardAction($idaccount) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      return $this->set_json_response($wrapper->getconfigdashboard($idaccount), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getdefaultdashboard")
   */
  public function getdefaultdashboardAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      return $this->set_json_response($wrapper->getDefaultDashboard(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/uploadimage/{idaccount:[0-9]+}")
   */
  public function uploadimageAction($idaccount) {
    try {
      $this->db->begin();
      $file = $_FILES['file'];
      $this->validateFile($file);
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      $wrapper->saveImage($file, $idaccount);
      $this->db->commit();
      return $this->set_json_response(true, 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding uploadimageAction(DASHBOARDCONFIG)... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding uploadimageAction(DASHBOARDCONFIG)... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savecondigdashboard/{idaccount:[0-9]+}")
   */
  public function savecondigdashboardAction($idaccount) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\DashboardconfigWrapper();
      return $this->set_json_response($wrapper->saveConfig($idaccount, $arrayData), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  private function validateFile($file) {
    if (empty($file['name'])) {
      throw new InvalidArgumentException("No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información");
    }

    if ($file['error']) {
      throw new InvalidArgumentException("Ha ocurrido un error mientras se cargaba el archivo, por favor valide la información");
    }

    if ($file['type'] != 'image/jpeg') {
      throw new InvalidArgumentException("El archivo no es una imagen.");
    }
  }

}
