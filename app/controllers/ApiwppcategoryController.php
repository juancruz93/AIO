<?php

/**
 * @RoutePrefix("/api/wppcategory")
 */
class ApiwppcategoryController extends ControllerBase {

  /**
   * 
   * @Post("/getwppcategory/{page:[0-9]+}")
   */
  public function getwppcategoryAction($page){
    try {
        
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\WppcategoryWrapper();
    
      return $this->set_json_response($wrapper->findWppCategory($page, $data), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception finding wppcategory... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding wppcategory ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/deletewppcategory")
   */
  public function deletewppcategoryAction(){
    
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      if (!$data) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\WppcategoryWrapper();
      return $this->set_json_response($wrapper->deletewppcategory($data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting smscategorylist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}