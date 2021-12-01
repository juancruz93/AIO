<?php

/**
 * @RoutePrefix("/api/blockade")
 */
class ApiblockadeController extends \ControllerBase {

  /**
   * 
   * @Post("/getallblock/{page:[0-9]+}")
   */
  public function getallblockAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $wrapper = new Sigmamovil\Wrapper\BlockadeWrapper();
      $wrapper->findBlocked($page, $contentsraw);
      return $this->set_json_response($wrapper->getBlocked(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/addblockade")
   */
  public function addblockadeAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new Sigmamovil\Wrapper\BlockadeWrapper();
      $wrapper->saveBlocked($data);
      $this->trace("success", "Se ha bloqueado un contacto");

      return $this->set_json_response(array("message" => "Se ha bloqueado un contacto"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/deleteblockade/{idBlocked:[0-9]+}")
   */
  public function deleteblockadeAction($idBlocked) {
    try {
      $wrapper = new Sigmamovil\Wrapper\BlockadeWrapper();
      $wrapper->deleteBlocked($idBlocked);
      $this->trace("success", "Se ha desbloqueado un contacto");

      return $this->set_json_response(array("message" => "Se ha desbloqueado un contacto"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}