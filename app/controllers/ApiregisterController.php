<?php

use Sigmamovil\Wrapper\RegisterWrapper as rw;

/**
 * @RoutePrefix("/api/register")
 */
class ApiregisterController extends ControllerBase {

  /**
   * @Post("/create")
   */
  public function createAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      $data = json_decode($json, true);
      $wrapper = new rw();
      $res = $wrapper->createAccSubaccount($data);
      $this->db->commit();
      return $this->set_json_response($res);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while save new accountSubaccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listpay")
   */
  public function listpaymentplansAction() {
    try {
      $wrapper = new rw();
      return $this->set_json_response($wrapper->listpaymentplan());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding listpaymentsplans ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/detailpay/{id:[0-9]+}")
   */
  public function detailpaymentplanAction($id) {
    try {
      $wrapper = new rw();
      return $this->set_json_response($wrapper->detailPaymentPlan($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding detailpaymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/verify/{id:[0-9]+}")
   */
  public function verifyaccountAction($id) {
    try {
      $wrapper = new rw();
      $sub = $wrapper->verifyAccount($id);
      return $this->set_json_response($sub->idSubaccount);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding verifyAccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/assign")
   */
  public function assignpaymentplanAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new rw();
      return $this->set_json_response($wrapper->assignPaymentPlanToAccount($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding verifyAccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/appidfb")
   */
  public function getappidfacebookAction() {
    try {
      $wrapper = new rw();
      return $this->set_json_response($wrapper->getAppIdFacebookLogin());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding verifyAccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/accountfb")
   */
  public function createwithfacebookAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new rw();
      $response = $wrapper->createAccountWithFacebook($data);
      $this->db->commit();
      return $this->set_json_response($response);
    } catch (Exception $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding verifyAccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/completeprofile")
   */
  public function completeprofileuserAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new rw();
      $response = $wrapper->completeProfileUser($data);
      $this->db->commit();
      return $this->set_json_response($response);
    } catch (Exception $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding verifyAccount ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
