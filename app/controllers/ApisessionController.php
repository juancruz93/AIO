<?php

use Sigmamovil\Wrapper\SessionWrapper;

/**
 * @RoutePrefix("/api/session")
 */
class ApisessionController extends ControllerBase {

  /**
   * @Post("/login")
   */
  public function loginAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new SessionWrapper();
      return $this->set_json_response($wrapper->login($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/loginp")
   */
  public function loginpassAction() {
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new SessionWrapper();
      $data = $wrapper->loginPass($arrayData);
      $this->trace("success", "El usuario ha iniciado sesión", $this->session->get('idUser'));
      $this->db->commit();
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while validate user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/loginfb")
   */
  public function loginwithfacebookAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new SessionWrapper();
      $response = $wrapper->loginWithSocialNetworks($data);
      $this->trace("success", "El usuario ha iniciado sesión", $this->session->get('idUser'));
      $this->db->commit();
      return $this->set_json_response($response);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while validate user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/verifyStatus")
   */
  public function verifystatususerAction() {
    try {
      $us = $this->session->get("parcialUser");
      if (!isset($us)) {
        $this->notification->error("Ha ocurrido un errror con el inicio de sesión con Facebook por favor contacte con soporte");
        return $this->set_json_response(["status" => "notauthorized"]);
      }

      return $this->set_json_response(["status" => "authorized"]);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while validate user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/recoverpassgenerate")
   */
  public function recoverpassgenerateAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      $wrapper = new SessionWrapper();
      return $this->set_json_response($wrapper->recoverpass($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while validate user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
