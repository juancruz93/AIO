<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiaccountingController
 *
 * @author juan.pinzon
 */
use Sigmamovil\Wrapper\AccountingWrapper as aw;

/**
 * @RoutePrefix("/api/accounting")
 */
class ApiaccountingController extends ControllerBase {

  /**
   * @Get("/list/{page:[0-9]+}")
   */
  public function listAction($page) {
    try {
      $wrapper = new aw();
      return $this->set_json_response($wrapper->listAccounts($page));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding accounting ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Post("/gethabeasdata/{page:[0-9]+}")
   */
  public function gethabeasdataAction($id){
    try {
      $wrapper = new \Sigmamovil\Wrapper\AccountingWrapper();
      return $this->set_json_response($wrapper->gethabeasdata($id));
    } catch (Exception $ex) {
      $this->logger->log("Exception while save automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
}
