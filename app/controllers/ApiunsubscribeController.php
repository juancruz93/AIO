<?php
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * @RoutePrefix("/api/unsubscribe")
 */
class ApiunsubscribeController extends ControllerBase {

  /**
   * 
   * @Get("/getcontact/{idcontact:[0-9]+}/{idmail:[0-9]+}")
   */
  public function getcontactAction($idcontact,$idMail) {
    try {

      if (!$idcontact) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      return $this->set_json_response($wrapper->getContact($idcontact,$idMail), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/insunsubscribe/{idcontact:[0-9]+}")
   */
  public function insunsubscribeAction($idcontact) {
    try {
      \Phalcon\DI::getDefault()->get('db')->begin();

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if (empty($data)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $this->trace("success", "El contacto de idContact = " . $idcontact . " ha cambiado su suscripción a algunas categorías");
      $wrapper->unsubcribeAllContact($data, $idcontact);
      \Phalcon\DI::getDefault()->get('db')->commit();
      return $this->set_json_response(array("message" => "La información de suscripción de categorías fue actualizada exitosamente."), 200);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->logger->log("Exception while change subscribed categories ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/insunsubscribeautomatic/{idcontact:[0-9]+}")
   */
  public function insunsubscribeautomaticAction($idContact) {
    try {
      $this->db->begin();
      $json = $this->getRequestContent();
      $data = json_decode($json);
      
      if (empty($data)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $msg = $wrapper->unsubcribeContactAutomaticCampaign($data, $idContact);
      $this->db->commit();
      $this->trace("success", "El contacto de idContact = " . $idContact . " ha cambiado su suscripción a algunas categorías");
      
      return $this->set_json_response($msg);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->logger->log("Exception while change subscribed categories ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Get("/insunsubscribesimple/{idmail:[0-9]+}/{idcontact:[0-9]+}")
   */
  public function insunsubscribesimpleAction($idmail,$idcontact) {
    try {
      \Phalcon\DI::getDefault()->get('db')->begin();
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $this->trace("success", "El contacto de idContact = " . $idcontact . " ha cambiado la contactlist enviada");
      $wrapper->unsubcribeContactSimple($idmail,$idcontact);
      \Phalcon\DI::getDefault()->get('db')->commit();
      return $this->set_json_response(array("message" => "La información de suscripción de categorías fue actualizada exitosamente."), 200);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->logger->log("Exception while change subscribed categories ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    /**
    * 
    * @Post("/getcontactsunsubscribe/{page:[0-9]+}")
    */
  public function getcontactsunsubscribeAction($page){
    try {
      $json = $this->getRequestContent();
      $data = json_decode($json);
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $wrapper->findContactsUnsuscribe($page, $data);

      return $this->set_json_response($wrapper->getContactsUnsuscribe(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * 
   * @Get("/deleteunsub/{idContact:[0-9]+}")
   */
  public function deleteUnsubAction($idContact) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $wrapper->deleteUnsub($idContact);
      $this->trace("success", "Se ha Eliminado la desuscripcion de un contacto");

      return $this->set_json_response(array("message" => "Se ha suscrito el contacto"), 200);
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
   * @Get("/getcategories")
   */
  public function getcategoriesAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      return $this->set_json_response($wrapper->getCategories(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    /**
   * 
   * @Post("/createcontactunsub")
   */
  public function createcontactunsubAction() {
    try {
      $this->db->begin();
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\UnsubscribeWrapper();
      $wrapper->createUnsub($data);
      $this->db->commit();
      $this->trace("success", "Se ha desuscrito el contacto");

      return $this->set_json_response(array('message' => "Se ha desuscrito el contacto"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while deleting selected contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while deleting selected contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
