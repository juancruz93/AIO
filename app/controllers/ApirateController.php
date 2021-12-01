<?php

/*
 * Api rate
 */
use Sigmamovil\Wrapper\RateWrapper;
/**
 * @RoutePrefix("/api/rate")
 */
class ApirateController extends ControllerBase 
{
  /**
  * @Post("/getall/{page:[0-9]+}")
  */
  public function getallAction($page) {
    try {
      $json = $this->request->getRawBody();
      $arrayData = json_decode($json, true);

      if (!$arrayData) {
          throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new Sigmamovil\Wrapper\RateWrapper();
      return $this->set_json_response($wrapper->getAll($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   * @Post("/create")
   */
  public function createAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      if($data['idServices'] == 5 && $data['accountingMode'] == 'unlimited'){
        if(count($data['ranges'])<1){
          throw new \InvalidArgumentException('La Encuesta ilimitada solo puede tener un Rango.');
        }
      } else {
        if(count($data['ranges'])<3){
          throw new \InvalidArgumentException('Debe realizar al menos 3 rangos.');
        }
      }
      $wrapper = new \Sigmamovil\Wrapper\RateWrapper();
      $rate = $wrapper->createAction($data);
      return $this->set_json_response(["message" => "La tarifa ha sido creada exitosamente y este es su codigo de tarifa: ".$rate->idRate]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
  * @Get("/getone/{id:[0-9]+}")
  */
  public function getoneAction($id) {
    try {
      $wrapper = new Sigmamovil\Wrapper\RateWrapper();
      return $this->set_json_response($wrapper->getOne($id));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
  * @Put("/edit/{idRate:[0-9]+}")
  */
  public function editAction($idRate) {
    try {
      
      $json = $this->request->getRawBody();
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      if($data['idServices'] == 5 && $data['accountingMode'] == 'unlimited'){
        if(count($data['ranges'])<1){
          throw new \InvalidArgumentException('La Encuesta ilimitada solo puede tener un Rango.');
        }
      } else {
        if(count($data['ranges'])<3){
          throw new \InvalidArgumentException('Debe realizar al menos 3 rangos.');
        }
      }
      $wrapper = new \Sigmamovil\Wrapper\RateWrapper();
      $wrapper->editAction($idRate,$data);
      return $this->set_json_response(["message" => "La tarifa ".$idRate." se ha editado correctamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
  * @Delete("/delete/{idRate:[0-9]+}")
  */
  public function deleteAction($idRate) {
    try {
      if (!$idRate) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\RateWrapper();
      $wrapper->deleteAction($idRate);
      return $this->set_json_response(["message" => "La tarifa se ha eliminado correctamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
