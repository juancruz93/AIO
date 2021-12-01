<?php

use Sigmamovil\Wrapper\ActivitylogWrapper as alw;

/**
 * @RoutePrefix("/api/activitylog")
 */
class ApiactivitylogController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listactivitylogAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new alw();
      return $this->set_json_response($wrapper->listActivityLog($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding activitylog ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
