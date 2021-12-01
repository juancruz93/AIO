<?php

/**
 * @RoutePrefix("/api/smscategory")
 */
class ApismscategoryController extends ControllerBase {

  /**
   * @Get("/getall")
   */
  public function getallcategoryAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmscategoryWrapper();
      return $this->set_json_response($wrapper->findallcategory(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms category... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/getsmscategory/{page:[0-9]+}")
   */
  public function getsmscategoryAction($page){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $smscategory = SmsCategory::find();
      
      if (!$smscategory) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new \Sigmamovil\Wrapper\SmscategoryWrapper();
      
      return $this->set_json_response($wrapper->findSmsCategory($page, $data), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception finding smscategory... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding smscategory ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/deletesmscategory")
   */
  public function deletesmscategoryAction(){
    
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $smscategory = new stdClass();
      $smscategory->idsmscategory = $arrayData['idSmsCategory'];

      if (!$smscategory) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmscategoryWrapper();
      $objReturn = array();
      if (!$wrapper->deleteSmsCategory($smscategory)) {
        $this->db->rollback();
      }
      $objReturn = array("message" => "Se eliminó la categoría correctamente");
      $this->db->commit();
      $this->trace("success", "Se ha eliminado la categoría de sms");

      return $this->set_json_response($objReturn, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting smscategorylist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
}