<?php

/**
 * Description of ApireportemailController
 *
 * @author jose.quinones
 */

/**
 * @RoutePrefix("/api/reportemail")
 */
class ApireportemailController extends \ControllerBase {
  
  /**
   * @Post("/")
   */
  public function findreportemailAction() {
    try {
      $json = $this->request->getRawBody();
//      $json = "{\"results\":[{\"from\":\"573188372817\",\"to\":\"AIO-32356\",\"text\":\"SIGMA\",\"cleanText\":\"SIGMA\",\"keyword\":null,\"receivedAt\":\"2017-07-19T16:03:29.818+0000\",\"messageId\":\"2622184738587601044\",\"pairedMessageId\":\"14\",\"price\":{\"pricePerMessage\":800.000000,\"currency\":\"COP\"},\"callbackData\":null}],\"messageCount\":1,\"pendingMessageCount\":0\n}";
      if (!$json || !isset($json) || empty($json)) {
        $this->logger->log("NO HA ENVIADO NINGUN DATO EN SMS DOBLEVÍA POR FAVOR VALIDE LA INFORMACIÓN");
        $this->logger->log($json);
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $result = json_decode($json);
      $boolNew = $wrapper->registerReceiverLote($result);
      //la api esta sujeta a INFOBIT entonces no es necesario retornar algo
      return $this->set_json_response(["status" => "ok"]);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("InvalidArgumentException receivedsms ... {$ex->getMessage()} --> \n {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception receivedsms ... {$ex->getMessage()} --> \n {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
