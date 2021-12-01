<?php

/**
 * @RoutePrefix("/api/smstwoway")
 */
class ApismstwowayController extends \ControllerBase  {

  /**
   * @Post("/")
   */
  public function receivedsmsAction() {
    try {
      $json = $this->request->getRawBody();
        
//      $json = "{\"results\":[{\"from\":\"573188372817\",\"to\":\"AIO-32356\",\"text\":\"SIGMA\",\"cleanText\":\"SIGMA\",\"keyword\":null,\"receivedAt\":\"2017-07-19T16:03:29.818+0000\",\"messageId\":\"2622184738587601044\",\"pairedMessageId\":\"14\",\"price\":{\"pricePerMessage\":800.000000,\"currency\":\"COP\"},\"callbackData\":null}],\"messageCount\":1,\"pendingMessageCount\":0\n}";
      \Phalcon\DI::getDefault()->get('logger')->log("Api de Infobip 1");
      \Phalcon\DI::getDefault()->get('logger')->log(print_r($json,true));
      if (!$json || !isset($json) || empty($json)) {
        $this->logger->log("NO HA ENVIADO NINGUN DATO EN SMS DOBLEVÍA POR FAVOR VALIDE LA INFORMACIÓN");
        $this->logger->log($json);
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $result = json_decode($json);
      $boolNew = $wrapper->registerReceiverLote($result);
      
      $getFuncitonPost = new \Sigmamovil\Wrapper\SmstwowaypostnotifyWrapper();
      $getTwoWayCredentials = $getFuncitonPost->findPostCredentials($boolNew["dataTwoWay"]->Smstwoway->idSubaccount);
      //Si La subcuenta es 28 (desarrollo), se lanza la peticion al webhook
      
      if($boolNew["dataTwoWay"]->Smstwoway->idSubaccount == $getTwoWayCredentials["idSubaccount"]){
        unset($result->results[0]->price);
        $result->results[0]->userResponseGroup =$boolNew["dataTwoWay"]->userResponseGroup;
        $result->results[0]->totalUserResponse = $boolNew["dataTwoWay"]->totalUserResponse;
        $dataWebhook = json_encode($result);
        $this->sendPostAnswerSmsTwoway($dataWebhook,$getTwoWayCredentials["url"]);
      }
      \Phalcon\DI::getDefault()->get('logger')->log("Api de Infobip 2");
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

  /**
   *
   * @Post("/createsmslotetwoway")
   */
  public function createsmslotetwowayAction() {
    try {
      $contentsraw = $this->getRequestContent();
      //$contentsraw = '{"sentNow":false,"optionsAvanced":true,"sendNotification":true,"divideSending":true,"typeResponse":"[{response:si,action:Agregar,homologate:sdfsfsd},{response:no,action:Eliminar,homologate:dfsdfsdf}]","name":"sdfsfsf","category":"152","gmt":"-0900","dtpicker":"2017-08-02 15:44:15","receiver":"57; 3188856705; mensaje de ejemplo","emailNotification":"ej1@aio.com","quantity":3,"sendingTime":"6","timeFormat":"week"}';
      $data = json_decode($contentsraw, true);
      
      $this->db->begin();
      
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      //Se convierte el valor del elemento de arreglo a string
      if(isset($data["category"]["idSmsCategory"])){
      $data["category"] = $data["category"]["idSmsCategory"];  
      }
      if(isset($data["gmt"]["gmt"])){
        $data["gmt"] = $data["gmt"]["gmt"];  
      }

      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $response = $wrapper->createSmsLotetwoway($data);
      
      $this->db->commit();
      
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("createsmslotetwowayAction... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("createsmslotetwowayAction... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Put("/editsmslotetwowaysend")
   */
  public function editsmslotetwowaysendAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);

      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $this->trace("success", "Se ha editado el envío de sms");

      return $this->set_json_response($wrapper->editSmstwowaySend($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallsmstwoway/{page:[0-9]+}")
   */
  public function getallsAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wrapper->getallsmstwoway($page, $data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding smstwoway... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getone/{idSmsTwoway:[0-9]+}")
   */
  public function getoneAction($idSmsTwoway) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wrapper->getSms($idSmsTwoway), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/detailsms")
   */
  public function detailsmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wraper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wraper->getDetail($data['idSmsTwoway'], $data['page'], $data['filter']), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/info/{idSmsTwoway:[0-9]+}")
   */
  public function infoAction($idSmsTwoway) {
    try {
      $wraper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wraper->getInfo($idSmsTwoway), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/createcsv")
   */
  public function createcsvAction() {
    try {
      $this->db->begin();
      $wraper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $response = $wraper->createCsv($_FILES, $_POST);
      $this->db->commit();
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/changestatus/{idSmsTwoway:[0-9]+}")
   */
  public function changestatusAction($idSmsTwoway) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wraper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wraper->changeStatus($idSmsTwoway, $data['status']), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savesmstowwaycontact")
   */
  public function savesmstowwaycontactAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);

      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $wrapper->getDataSaveContact($data);
      $this->db->begin();
      $response = $wrapper->saveSmstwowayContact();
      $this->db->commit();
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getalledit/")
   */
  public function getalleditAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wrapper->getDataEdit($data));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getalleditcontact")
   */
  public function getalleditcontactAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wrapper->getAllEdit($data));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/changestatusedit")
   */
  public function changestatuseditAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $id = json_decode($contentsraw, true);

      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response(array('response' => $wrapper->changeCancelEdit($id)), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Post("/editcsv")
   */
  
  public function editcsvAction() {
 
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      
       if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      return $this->set_json_response($wrapper->editCsvTwoway($data), 200);
    }catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while editing editCsvTwoway... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while editing editCsvTwoway... {$exc}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
   }


   public function sendPostAnswerSmsTwoway($data,$url){
      $objJsonData = json_decode($data);
      $objJsonData->results[0]->keypass = hash('md5', 'pruebadev');

      $curl = curl_init();
      curl_setopt_array($curl, array(
        //CURLOPT_URL => "http://gamethemed.com.co/ejemploWebhook.php",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        //CURLOPT_MAXREDIRS => 10,
        //CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($objJsonData),
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            //"Authorization: Basic {$key}",
            "Content-Type: application/json"
        )
    ));
      //\Phalcon\DI::getDefault()->get('logger')->log(print_r(curl_exec($curl),true));
      curl_exec($curl);
      curl_close($curl);

   }
   
   /**
    *@Post("/singlesmstwoway")
    */
   public function createsinglesmstwowayAction(){
     try {
      $this->logger->log("Hora inicio proceso createsinglesmstwoway". date("h:i:s"));
      //Recibe peticion Json
      $json = $this->getRequestContent();
      
      //Se acepta Si data llega en base64
      if (base64_decode($json, true)) {
        $json = base64_decode($json, true);
      }
      //Decodificacion de Data
      $data = json_decode($json, true);
      //Valida existencia de data
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      //Inicia Transaccion
//      $this->db->begin();
      $wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      //Ejecuta metodo Wrapper
      $res = $wrapper->createSingleSmstwoway($data);
//      $this->db->commit();
      $this->logger->log("Hora Fin proceso createsinglesmstwoway". date("h:i:s"));
      return $this->set_json_response($res, 200);
    } catch (InvalidArgumentException $ex) {
//      $this->db->rollback();
      $this->logger->log("Exception while send singleSmsTwoway... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
//      $this->db->rollback();
      $this->logger->log("Exception while send singleSMS singleSmsTwoway... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
   }
   
    /**
    *@Post("/getavalaiblecountry")
    */
   public function getavalaiblecountryAction(){
     try {
      
      $sql = " select cxa.idCountry as idcountry, c.name as country from cxa"
            ." left join country as c on cxa.idCountry = c.idCountry "
            ." where idAccount = ".(int) \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount
            ." and status = 1";
      $result = array();
      $jsonresult = null;
      
      if(count($this->db->fetchAll($sql))>0){
        $result['result'] = $this->db->fetchAll($sql);
        
      }else{
        $result['result'] = false;
      }
      $jsonresult = json_encode($result);
      
      return $this->set_json_response($jsonresult, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while send singleSmsTwoway... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get countries avalaibles to send... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
   }
}
