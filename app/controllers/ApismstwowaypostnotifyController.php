<?php

/**
 * @RoutePrefix("/api/smstwowaypostnotify")
 */
class ApismstwowaypostnotifyController extends \ControllerBase {

  /**
   * @Post("/create")
   */
  public function createAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $respuest = null;
      //Edit
      if ($data["smstwowaydata"]['edit'] == true) {
        $wrapper = new \Sigmamovil\Wrapper\SmstwowaypostnotifyWrapper();
        return $this->set_json_response($wrapper->editSmsTwowayPostNotify($data), 200);
        $this->trace("success", "Se ha Editado el envío de sms!");
      }
      //Create
      else {
        $wrapper = new \Sigmamovil\Wrapper\SmstwowaypostnotifyWrapper();
        return $this->set_json_response($wrapper->createSmsTwowayPostNotify($data), 200);
        $this->trace("success", "Se ha Creado el envío de sms!");
      }
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while creating Smstwoway post notifications... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getsavedcredentials")
   */
  public function getsavedcredentialsAction() {
    try {
      $wrapper = new Sigmamovil\Wrapper\SmstwowaypostnotifyWrapper();
      $idSubaccount = $this->user->usertype->Subaccount->idSubaccount;
      return $this->set_json_response($wrapper->findPostCredentials($idSubaccount));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding post credentials ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
