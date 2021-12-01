<?php

/**
 * @RoutePrefix("/api/mtaxip")
 */
class ApimtaxipController extends ControllerBase {

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listmtaxipAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      return $this->set_json_response($mtaxipWrapper->listMtaxip($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemtaxip")
   */
  public function savemtaxipAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      $id = $mtaxipWrapper->saveMtaxip($arrayData);

      $this->trace("success", "La información ha sido guardada correctamente");

      return $this->set_json_response(["message" => "La información ha sido guardada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/delete")
   */
  public function deletemtaxipAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      $mtaxipWrapper->deleteMtaxip($arrayData);
      $this->trace("success", "La información ha sido eliminada");

      return $this->set_json_response(["message" => "La información ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getmtaxip/{id:[0-9]+}")
   */
  public function getmtaxipAction($id) {
    try {
      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      return $this->set_json_response($mtaxipWrapper->getMtaxip($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/edit")
   */
  public function editmtaxipAction() {
    try {
      $datajson = $this->request->getRawBody();
      $arrayData = json_decode($datajson, true);

      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      $mtaxipWrapper->editMtaxip($arrayData);
      $this->trace("success", "La información ha sido editada");

      return $this->set_json_response(["message" => "La información ha sido editada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getipmta")
   */
  public function getipmtaAction() {
    try {
      $mtaxipWrapper = new \Sigmamovil\Wrapper\MtaxipWrapper();
      return $this->set_json_response($mtaxipWrapper->getIpmta());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mtaxip ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
