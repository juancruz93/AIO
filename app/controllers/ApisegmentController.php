<?php

use \Sigmamovil\Wrapper\SegmentWrapper as segw;

/**
 * @RoutePrefix("/api/segment")
 */
class ApisegmentController extends \ControllerBase {

  private $elephant;

  public function getElephant() {
    return $this->elephant;
  }

  public function setElephant($elephant) {
    $this->elephant = $elephant;
  }

  /**
   * 
   * @Post("/customfieldbycustomfields")
   */
  public function customfieldbycustomfieldsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      foreach ($data as $key) {
        $arr[] = $key->idContactlist;
      }

      $customfield = \Customfield::find(
                      array("conditions" =>
                          'idContactlist IN (?0) AND deleted  = 0',
//                'idContactlist IN ({contactlist:array})',
                          'bind' => array(
                              0 => implode(",", $arr)
                          )
                      )
      );
      $arr = array();
      $prueba = [['name' => "Nombre", "type" => "Text", "value" => "", "idCustomfield" => "name"], ['name' => "Correo electrónico", "type" => "Text", "value" => "", "idCustomfield" => "email"],
          ['name' => "Apellido", "type" => "Text", "value" => "", "idCustomfield" => "lastname"], ['name' => "Fecha de nacimiento", "type" => "Date", "value" => "", "idCustomfield" => "birthdate"],
          ['name' => "Movil", "type" => "Numerical", "value" => "", "idCustomfield" => "phone"]];
      foreach ($prueba as $key) {
        $obj = new stdClass();
        $obj->idCustomfield = $key["idCustomfield"];
        $obj->name = $key["name"];
        $obj->type = $key["type"];
        $obj->value = $key["value"];
        $arr[] = $obj;
      }
      foreach ($customfield as $key) {
        $arr[] = $key;
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

  /**
   * 
   * @Post("/addsegment")
   */
  public function addsegmentAction() {
    try {
      $json = $this->getRequestContent();
      $data = json_decode($json);

      $this->setElephant(new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000')));

      $wrapper = new segw();
      $idSegment = $wrapper->addSegment($data);
      
      
      $this->getElephant()->initialize();
      $this->getElephant()->emit('create-segment', array(
          'idSegment' => $idSegment
      ));
      $this->getElephant()->close();
      
      $this->trace("success", "Se ha creado el segmento exitosamente");

      return $this->set_json_response(array("message" => "Se ha creado el segmento"), 200);
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
   * @Get("/getallcontactlistbysubaccount")
   */
  public function getallcontactlistbysubaccountAction() {
    try {
      $contactlist = Contactlist::find(["conditions" => "idSubaccount = ?0 AND deleted=0", "bind" => [0 => $this->user->Usertype->idSubaccount]]);
      $arr = array();
      foreach ($contactlist as $key) {
        $arr[] = $key;
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

  /**
   * 
   * @Post("/getallsegment/{page:[0-9]+}")
   */
  public function getallsegmentAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $wrapper = new \Sigmamovil\Wrapper\SegmentWrapper();
      $wrapper->findAllSegment($page, $contentsraw);
      return $this->set_json_response($wrapper->getSegments(), 200);
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
   * @Get("/findsegment/{page:[0-9]+}")
   */
  public function findsegmentAction($idSegment) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SegmentWrapper();
      $wrapper->findsegment($idSegment);
      return $this->set_json_response($wrapper->getSegments(), 200);
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
   * @Put("/editsegment")
   */
  public function editsegmentAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $this->setElephant(new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000')));
      
      $wrapper = new \Sigmamovil\Wrapper\SegmentWrapper();
      $wrapper->setSegment($data);
      $idSegment = $wrapper->editSegment();
      
      $this->getElephant()->initialize();
      $this->getElephant()->emit('create-segment', array(
          'idSegment' => $idSegment
      ));
      $this->getElephant()->close();
      
      $this->trace("success", "Se ha editado el segmento");

      return $this->set_json_response(array("message" => "Se ha editado el segmento exitosamente"), 200);
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
   * @Delete("/deletesegmen/{idSegment:[0-9]+}")
   */
  public function deletesegmentAction($idSegment) {
    try {
      $contactwrapper = new Sigmamovil\Wrapper\SegmentWrapper();
      $contactwrapper->deleteSegment($idSegment);
      $this->trace("success", "Se ha eliminado el segmento");

      return $this->set_json_response(array("message" => "El segmento se ha eliminado correctamente"));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while deleting segment: {$ex->getMessage()}", 400);
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting segment: {$ex->getMessage()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
