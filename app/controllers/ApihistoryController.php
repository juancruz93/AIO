<?php

/**
 * @RoutePrefix("/api/history")
 */
class ApihistoryController extends ControllerBase {

  /**
   * 
   * @Post("/gethistory/{page:[0-9]+}")
   */
  public function gethistoryAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\HistoryWrapper();
      $wrapper->findHistory($page, $data);
      return $this->set_json_response($wrapper->getHistory(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   * 
   * @Get("/getmasteraccounts")
   */
  public function getMasteraccountsAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\HistoryWrapper();
      $wrapper->findMasteraccounts();
      return $this->set_json_response($wrapper->getMasteraccounts(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   * 
   * @Get("/getallieds/{idMasteraccount:[0-9]+}")
   */
  public function getAlliedsAction($idMasteraccount) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\HistoryWrapper();
      $wrapper->findAllieds($idMasteraccount);
      return $this->set_json_response($wrapper->getAllieds(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getaccounts/{idAllied:[0-9]+}")
   */
  public function getAccountsAction($idAllied) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\HistoryWrapper();
      $wrapper->findAccounts($idAllied);
      return $this->set_json_response($wrapper->getAccounts(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getsubaccounts/{idAccount:[0-9]+}")
   */
  public function getSubaccountsAction($idAccount) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\HistoryWrapper();
      $wrapper->findSubaccounts($idAccount);
      return $this->set_json_response($wrapper->getSubaccounts(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
