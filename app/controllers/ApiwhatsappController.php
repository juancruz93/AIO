<?php

/**
 * @RoutePrefix("/api/whatsapp")
 */
class ApiwhatsappController extends \ControllerBase  {

    /**
   * @Post("/getallwhatsapp/{page:[0-9]+}")
   */
  public function getallsAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
      return $this->set_json_response($wrapper->getallwhatsapp($page, $data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding whatsapp... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getallcategory")
   */
  public function getallcategoryAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
      return $this->set_json_response($wrapper->findallcategory(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms category... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcontactlist")
   */
  public function getcontactlistAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
      $wrapper->getAllContanctList();
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms category... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listwpptemplate")
   */
  public function listwpptemplateAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
      return $this->set_json_response($wrapper->listwpptemplate());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

    /**
   * @Post("/countcontacts")
   */
  public function countcontactsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\WhatsappWrapper();
      return $this->set_json_response($wrapper->getCountContacts($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding whatsapp... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}