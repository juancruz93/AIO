<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * @RoutePrefix("/api/post")
*/

 class ApipostController extends ControllerBase {
 
   /**
   * @Post("/save")
   */
  public function saveAction(){
    try{
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\PostWrapper();
      return $this->set_json_response($wrapper->save($arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
