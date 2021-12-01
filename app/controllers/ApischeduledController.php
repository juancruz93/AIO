<?php

/**
 * @RoutePrefix("/api/scheduled")
 */
class ApischeduledController extends ControllerBase {

  /**
   * 
   * @Post("/getscheduled/{initialSMS:[0-9]+}/{initialMail:[0-9]+}")
   */
  public function getscheduledAction($initialSMS,$initialMail) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ScheduledWrapper();
      $wrapper->findSendings($initialSMS,$initialMail, $data);
      return $this->set_json_response($wrapper->getSendings(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding scheduled sendings... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
