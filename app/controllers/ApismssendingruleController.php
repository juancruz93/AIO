<?php

use Sigmamovil\Wrapper\SmssendingruleWrapper as ssrw;

/**
 * @RoutePrefix("/api/smssendingrule")
 */
class ApismssendingruleController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}/{name}")
   */
  public function listsmssendingruleAction($page, $name = "") {
    try {
      $wrapper = new ssrw();
      return $this->set_json_response($wrapper->listSmsSendingRule($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding smssendingrule ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listindicative")
   */
//  public function listfullindicativeAction() {
//    try {
//      $wrapper = new ssrw();
//      return $this->set_json_response($wrapper->listfullindicative());
//    } catch (InvalidArgumentException $ex) {
//      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
//    } catch (Exception $ex) {
//      $this->logger->log("Exception while finding full indicative ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
//      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
//    }
//  }

  /**
   * @Get("/show/{id:[0-9]+}")
   */
  public function showsmssendingruleAction($id) {
    try {
      $wrapper = new ssrw();
      return $this->set_json_response($wrapper->showsmssendingrule($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while findFirst smssendingrule with config... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createsmssendingruleAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $this->db->begin();
      $wrapper = new ssrw();
      $this->trace("success", "Se ha creado la regla de envío de sms");

      return $this->set_json_response($wrapper->createSmsSendingRule($data));
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while create smssendingrule ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit")
   */
  public function editsmssendingruleAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $this->db->begin();
      $wrapper = new ssrw();
      $this->trace("success", "Se ha editado la regla de envío de sms");

      return $this->set_json_response($wrapper->editsmssendingrule($data));
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while edit smssendingrule ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Delete("/delete/{id:[0-9]+}")
   */
  public function deletesmssendingruleAction($id) {
    try {
      $wrapper = new ssrw();
      $this->trace("success", "Se ha eliminado la regla de envío de sms");

      return $this->set_json_response($wrapper->deletesmssendingrule($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete smssendingrule ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listall")
   */
  public function listallAction() {
    try {
      $wrapper = new ssrw();
      return $this->set_json_response($wrapper->listAllSmsSendingRule());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding smssendingrule ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
