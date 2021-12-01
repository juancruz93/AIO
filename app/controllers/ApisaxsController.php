<?php

/**
 * @RoutePrefix("/api/saxs")
 */
class ApisaxsController extends ControllerBase {

  /**
   * @Get("/getall")
   */
  public function getallAction() {
    try {
    $wrapper = new \Sigmamovil\Wrapper\SaxsWrapper();
    return $this->set_json_response($wrapper->getall(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding pricelist ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    /**
   * 
   * @Post("/savedkim/{idsubaccount:[0-9]+}")
   */
  public function savedkimAction($idsubaccount) {
      
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      
      $wrapper = new \Sigmamovil\Wrapper\SaxsWrapper();
      return $this->set_json_response($wrapper->saveDetailConfigDKIM($idsubaccount,$data->domain), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
