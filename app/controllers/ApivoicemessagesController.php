<?php

/**
 * Description of ApivoicemessagesController
 *
 * @author jose.quinones
 */
use Sigmamovil\Wrapper\VoicemessagesWrapper as vm;
/**
 * @RoutePrefix("/api/voicemessages")
 */
class ApivoicemessagesController extends ControllerBase {
  
  /**
   * 
   * @Post("/createlote")
   */  
  public function createloteAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new vm();
      //return $this->set_json_response($wrapper->createlote($data), 200);
      return $this->set_json_response(["message" => "El servicio no esta disponible."], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }    
  }
}
