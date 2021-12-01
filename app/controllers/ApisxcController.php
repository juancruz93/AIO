<?php

/**
 * @RoutePrefix("/api/sxc")
 */
class ApisxcController extends \ControllerBase {

  /**
   * 
   * @Post("/findcontactsegment/{page:[0-9]+}/{idSegment:[0-9]+}")
   */
  public function findcontactsegmentAction($page, $idSegment) {
    try {
      $contentsraw = $this->getRequestContent();
      $wrapper = new \Sigmamovil\Wrapper\SegmentWrapper();
      $wrapper->findAllSxc($idSegment, $page, $contentsraw);
      return $this->set_json_response($wrapper->getSxc(), 200);
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
   * @Get("/changestatus/{idContact:[0-9]+}/{idContastlist:[0-9]+}")
   */
  public function changestatusAction($idContact, $idContastlist) {
    try {
      $cxcl = \Cxcl::findFirst(array("conditions" => "idContact = ?0 AND idContactlist = ?1", "bind" => array(0 => $idContact, 1 => $idContastlist)));
//      $cxcl->unsubscribed = time();
      if ($cxcl->unsubscribed == 0) {
        $cxcl->unsubscribed = time();
        $msg = "Se ha desuscrito el contacto";
      } else {
        $cxcl->unsubscribed = 0;
        $msg = "Se ha suscrito el contacto";
      }

      if (!$cxcl->update()) {
        foreach ($cxcl->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
        $this->trace("fail", "No se logro crear una cuenta");
      }
      $this->trace("success", $msg);

      return $this->set_json_response(array("message" => $msg), 200);
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
   * @Get("/customfield/{idSegment:[0-9]+}")
   */
  public function customfieldAction($idSegment) {
    try {
      $segment = Segment::find(array(array("idSegment" => (int) $idSegment)));
//      var_dump($idSegment);
//      var_dump($segment);
      $arr = array();
      foreach ($segment as $key) {
        foreach ($key->contactlist as $value) {
          $customfield = Customfield::find(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $value["idContactlist"])));
          foreach ($customfield as $key) {
            $contactlist = new \stdClass();
            $contactlist->idCustomfield = $key->idCustomfield;
            $contactlist->name = $key->name;
            $contactlist->alternativename = $key->alternativename;
            $contactlist->defaultvalue = $key->defaultvalue;
            $contactlist->type = $key->type;
            $contactlist->value = $key->value;
            array_push($arr, $contactlist);
          }
        }
      }

      return $this->set_json_response($arr, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
