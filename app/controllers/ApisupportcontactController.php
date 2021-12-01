<?php

/**
 * Description of Apisupportcontact
 * @RoutePrefix("/api/supportcontact")
 * @author desarrollo3
 */
class ApisupportcontactController extends ControllerBase {

  /**
   * 
   * @Get("/getalltechnical/{page:[0-9]+}/{idallied:[0-9]+}")
   */
  public function getAllTechnicalAction($page, $idAllied) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SupportcontactWrapper();
      $wrapper->setIdAllied($idAllied);
      $wrapper->setOffset($page);
      $wrapper->getAllTecnichalContact();
      return $this->set_json_response($wrapper->getSupportcontact());
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/findfirsttechnical/{idSupportContact:[0-9]+}")
   */
  public function findFirstTechnicalAction($idSupportContact) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SupportcontactWrapper();
      $wrapper->setIdSupportContact($idSupportContact);
      $wrapper->getFirstTecnichalContact();
      return $this->set_json_response($wrapper->getSupportcontact());
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/addtechnical/{idallied:[0-9]+}")
   */
  public function addtechnicalAction($idAllied) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SupportcontactWrapper();
      $wrapper->setSupportContact($data);
      $wrapper->setIdAllied($idAllied);
      $wrapper->addTecnichalContact();
      $this->trace("success", "Se ha guardado un contacto técnico");

      return $this->set_json_response($wrapper->getSupportcontact());
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception contactos de soporte.. {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Put("/edittechnical/{idallied:[0-9]+}")
   */
  public function edittechnicalAction($idAllied) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SupportcontactWrapper();
      $wrapper->setSupportContact($data);
      $wrapper->setIdAllied($idAllied);
      $wrapper->editTecnichalContact();
      $this->trace("success", "Se ha editado un contacto técnico");

      return $this->set_json_response($wrapper->getSupportcontact());
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception contactos de soporte... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception contactos de soporte.. {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
