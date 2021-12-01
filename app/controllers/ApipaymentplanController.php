<?php

use Sigmamovil\Wrapper\PaymentplanWrapper as ppw;

/**
 * @RoutePrefix("/api/paymentplan")
 */
class ApipaymentplanController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}/{name}")
   */
  public function listpaymentplanAction($page, $name = "") {
    try {
      $wrapper = new ppw();
      return $this->set_json_response($wrapper->listPaymentPlan($page, $name));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/show/{id:[0-9]+}")
   */
  public function showpaymentplanAction($idPaymentPlan) {
    try {
      $wrapper = new ppw();
      return $this->set_json_response($wrapper->getViewPaymentPlan($idPaymentPlan));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while show paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create")
   */
  public function createpaymentplanAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      
      $data = json_decode($json, TRUE);
//      var_dump($data);
//      exit();
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new ppw();
      $res = $wrapper->createPaymentPlan($data);
      $this->db->commit();
      $this->trace("success", "Se ha creado el plan de pago");

      return $this->set_json_response($res);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while create paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/get/{id:[0-9]+}")
   */
  public function getpaymentplanAction($id) {
    try {
      $wrapper = new ppw();
      return $this->set_json_response($wrapper->getPaymentPlan($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/edit")
   */
  public function editpaymentplanAction() {
    try {
      $this->db->begin();
      $json = $this->request->getRawBody();
      $data = json_decode($json, TRUE);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new ppw();
      $msg = $wrapper->editPaymentPlan($data);
      $this->db->commit();
      $this->trace("success", "Se ha editado el plan de pago");

      return $this->set_json_response($msg);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while edit paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Delete("/delete/{id:[0-9]+}")
   */
  public function deletepaymentplanAction($id) {
    try {
      $wrapper = new ppw();
      $this->trace("warining", "Se ha eliminado el plan de pago");

      return $this->set_json_response($wrapper->deletePaymentPlan($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/validatecourtesyplan")
   */
  public function validatecourtesyplanAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      
      $wrapper = new ppw();
      if ( $wrapper->validateCourtesyPlan($data) == true) {
        return $this->set_json_response(array('message' => "El aliado ya cuenta con un plan de cortesia para el país seleccionado."), 200);
      } else {
        return $this->set_json_response(200);
      }
      
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while delete paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Get("/listservices")
   */
  public function listservicesAction() {
    try {
      $wrapper = new ppw();
      return $this->set_json_response($wrapper->getServices());
    }catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while delete paymentplan ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
    }

}
