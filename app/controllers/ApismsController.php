<?php

/**
 * @RoutePrefix("/api/sms")
 */
class ApismsController extends \ControllerBase {

  /**
   *
   * @Post("/countsaxssms")
   */
  public function countsaxssmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      
      //AQUI ESCRIBIO Garcia

      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response(array('message' => $wrapper->countsaxssms($data)), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding countsaxssms... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding countsaxssms... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/countcontact")
   */
  public function getcountcontactsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wrapper->getCountContacts($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/createsmssend")
   */
  public function createsmssendAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $this->trace("success", "Se ha creado el envío de sms");

      return $this->set_json_response($wrapper->createSmsSend($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/createsmslote")
   */
  public function createsmsloteAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $this->logger->log(print_r($contentsraw));
      $data = json_decode($contentsraw, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $this->trace("success", "Se ha creado el envío de sms");

      return $this->set_json_response($wrapper->createSmsLote($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("createsmslote... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("createsmslote... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Put("/editsmssend/{idSms:[0-9]+}")
   */
  public function editsmssendAction($idSms) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $this->trace("success", "Se ha editado el envío de sms");

      return $this->set_json_response($wrapper->editSmsSend($idSms, $data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Route("/delete/{idSms:[0-9]+}", methods="DELETE")
   */
  public function deleteAction($idSms) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $this->trace("success", "Se ha eliminado el envío de sms");

      return $this->set_json_response($wrapper->smsCancelAction($idSms), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/getall/{page:[0-9]+}")
   */
  public function getallAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wrapper->getallsms($page, $data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getone/{idSms:[0-9]+}")
   */
  public function getoneAction($idSms) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $wrapper->findOneSms($idSms);
      return $this->set_json_response($wrapper->getSms(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/createsmsencrypted")
   */
  public function createsmsencryptedAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode(base64_decode($contentsraw), true);
      if($data == null){
        return $this->set_json_response(["message" => "La información enviada no está encriptada en base64"]);
        \Phalcon\DI::getDefault()->get("db")->rollback();
      }
      
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $this->trace("success", "Se ha creado el envío de sms");

      return $this->set_json_response($wrapper->createSmsEncrypted($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/detailsms/{idsms:[0-9]+}")
   */
  public function detailsmsAction($idSms) {
    try {
      $wraper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wraper->getDetailSms($idSms), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   *
   * @Post("/detailsmslote")
   */
  public function detailsmsloteAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wraper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wraper->getDetailSmsLote($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/verifysmstwowayservice")
   */
  public function verifysmstwowayserviceAction() {
    try {
      $wraper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wraper->verifyServiceTwoway(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while verifying Sms two-way service... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while verifying Sms two-way service {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/createcsv")
   */
  public function createcsvAction() {
    try {
      $idSubaccount = $this->user->Usertype->idSubaccount;
      $saxss = \Saxs::find(array("conditions" => "idSubaccount = ?0 and status=1", "bind" => array($this->user->userType->idSubaccount)));
      foreach ($saxss as $saxs) {
        if ($saxs->idServices == $this->services->sms) {
          $amount = $saxs->amount;
        }
      }
      $smsloteform = new SmsloteForm();
      $this->db->begin();

      $this->validateFastSending($this->request->isPost());
      $datenow = $this->request->getPost("sendnow");
      
      if ($_FILES['csv']["error"] == 4) {
        throw new InvalidArgumentException("No has seleccionado un archivo CSV");
      }
      if ($this->request->getPost("notification")) {
        $email = $this->request->getPost("email");
        //$email = preg_split("/[\n, ;]+/", $email);
        $email = preg_split("/[\s\n, ;]+/", $email);//Clean vars
        
        //Validación para saber que cada posición del array es un email
        for ($index = 0; $index < count($email); $index++){
          if($email[$index]==""||$email[$index]==null){
            unset($email[$index]);//Delete item null
            //throw new InvalidArgumentException("Uno de los correos es nulo o vacio");
          }
        }
        
        if (count($email) > 8) {
          throw new InvalidArgumentException("No se puede ingresar más 8 correos electrónicos");
        }
        if($idSubaccount != "420" || $idSubaccount != 420){
            if (count($email) > $amount) {
              throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms. Si nesesitas más saldo contacta al administrador");
            }
        }
        $email = implode(",", $email);
      }
      if ($_FILES['csv']['size'] > 8388608) {
        throw new InvalidArgumentException("El archivo CSV excede el tamaño las 8 megabytes aceptadas");
      }
      $startdate = $this->request->getPost("startdate");

      if ($this->request->getPost("sendnow") == 'on') {
        $startdate = date('Y-m-d G:i:s', time());
      }

      if (strtotime($startdate) < strtotime("now") && !$datenow) {
        throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
      }

      $subaccount = Subaccount::findFirst(array(
                  "conditions" => "idSubaccount = ?0",
                  "bind" => array(0 => $this->user->Usertype->idSubaccount)
      ));

      $sms = new Sms();
      $smsloteform->bind($this->request->getPost(), $sms);
      if (!$smsloteform->isValid()) {
        foreach ($smsloteform->getMessages() as $msg) {
          throw new \InvalidArgumentException($msg);
        }
      }
      $advancedoptions = $this->request->getPost("advancedoptions");
      $notification = $this->request->getPost("notification");
      $divide = $this->request->getPost("divide");
      $quantity = $this->request->getPost("quantity");
      $sendingTime = $this->request->getPost("sendingTime");
      $timeFormat = $this->request->getPost("timeFormat");
      $sendpush = $this->request->getPost("sendpush");

      if ($advancedoptions == 'on') {
        if ($divide == 'on') {
          if (!isset($quantity) or $quantity == 0) {
            throw new InvalidArgumentException("Debes indicar una cantidad correcta");
          }
          if (!isset($sendingTime) or $sendingTime == "") {
            throw new InvalidArgumentException("Debes elegir un tiempo de envío");
          }
          if (!isset($timeFormat) or $timeFormat == "") {
            throw new InvalidArgumentException("Debes elegir un formato de tiempo");
          }
          $sms->divide = 1;
          $sms->sendingTime = $sendingTime;
          $sms->timeFormat = $timeFormat;
        } else {
          $sms->divide = 0;
        }
      }
      $sms->advancedoptions = 0;
      if ($advancedoptions == 'on' AND ( $notification == 'on' || $divide == 'on' || $sendpush == 'on')) {
        $sms->advancedoptions = 1;
      }

      (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");
      $sms->idSubaccount = $this->user->Usertype->idSubaccount;
      $sms->confirm = 0;
      $sms->status = $this->statusSms->draft;
      $sms->sent = 0;
      $sms->logicodeleted = 0;
      $sms->type = $this->typeSms->csv;

      $sms->notification = 0;
      if ($notification == 'on') {
        $sms->notification = 1;
        $sms->email = $email;
      }
      if ($divide == 0) {
        $sms->quantity = null;
      }

      if ($sms->advancedoptions == 0) {
        $sms->notification = 0;
        $sms->email = null;
        $sms->divide = 0;
        $sms->sendingTime = null;
        $sms->quantity = null;
        $sms->timeFormat = null;
      }

      $sms->dateNow = 1;
      $sms->gmt = null;
      $sms->originalDate = null;
      if($this->request->getPost("morecaracter") == 'on'){
        $sms->morecaracter = 1;    
      }else{
        $sms->morecaracter = 0;        
      }
      
      if ($this->request->getPost("sendnow") != 'on') {
        $sms->dateNow = 0;
        $sms->gmt = $this->request->getPost("timezone");
        $sms->originalDate = $this->request->getPost("startdate");
      }
      $sms->sendpush = $sendpush != NULL ? 1 : 0;
      if($sms->sendpush == 1 && $sms->morecaracter == 1){
        throw new \InvalidArgumentException("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.");  
      }
      if (!$sms->save()) {
        foreach ($sms->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
        throw new InvalidArgumentException("No se logro crear el smslote {$message}");
      }

      $file = new Sigmamovil\General\Misc\FileManager();
      $resul = $file->csvsms($_FILES['csv'], $sms->idSms, $this->user->Usertype->idSubaccount);
      $sms->target = $resul["success"];
      if (!$sms->save()) {
        foreach ($sms->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
        throw new InvalidArgumentException("No se logro crear el smslote {$message}");
      }

      foreach ($sms->Subaccount->Saxs as $key) {
        if ($key->idServices == 1 && $key->status ==1) {
          $saxs = $key;
          $amount = $saxs->amount;
          $totalAmount = $saxs->totalAmount;
          $subaccountName = $this->user->Usertype->Subaccount->name;
          $accountName = $this->user->Usertype->Subaccount->Account->name;
          $arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName,
          );
        }
      }
      
      if($idSubaccount != "420" || $idSubaccount != 420){
          if ($resul["success"] > $saxs->amount) {
            sleep(2);
            $sendMailNot= new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
            $sendMailNot->sendSmsNotification($arraySaxs);
            throw new InvalidArgumentException("Solo puedes hacer " . $saxs->amount . " envíos de SMS. Si necesitas más saldo, contacta con tu administrador.");
          }
      }

      $this->db->commit();
      return $this->set_json_response("Se realizó el registro de sms correctamente", 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function validateFastSending($data) {
    if ($data['name'] == "") {
      $this->notification->info("Es obligatorio el nombre del envío");
      return $this->response->redirect("");
    }
    if ($data['idSmsCategory'] == "") {
      $this->notification->info("Debe elegir una categoría");
      return $this->response->redirect("");
    }
    if ($data['notification'] and $data['email'] == "") {
      $this->notification->info("Debe indicar al menos una dirección de notificación");
      return $this->response->redirect("");
    }
    if ($data['divide']) {
      if (!isset($data['quantity']) or $data['quantity'] < 1) {
        $this->notification->info("Debe indicar una cantidad de envíos por intervalo mayor a 1");
        return $this->response->redirect("");
      }
      if (!isset($data['sendingTime'])) {
        $this->notification->info("Debe elegir un tiempo de envio correcto");
        return $this->response->redirect("");
      }
      if (!isset($data['timeFormat'])) {
        $this->notification->info("Debe elegir un formato de tiempo correcto");
        return $this->response->redirect("");
      }
    }
  }

  /**
   * @Post("/changestatus/{idSms:[0-9]+}")
   */
  public function changestatusAction($idSms) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $wraper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wraper->changeStatus($idSms, $data['status']), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/singlesms")
   */
  public function createsinglesmsAction() {
    try {
    //$this->logger->log("[Ingreso a la SingleSms]:".date("H:i:s"));
      $json = $this->getRequestContent();
      if (base64_decode($json, true)) {
        $json = base64_decode($json, true);
      }
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $this->db->begin();
    //$this->logger->log("[WRAPPER a la SingleSms]:".date("H:i:s"));
    //$this->logger->log(print_r($data,true));
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
    //$this->logger->log("[WRAPPER a la createSingleSms]:".date("H:i:s"));
      $res = $wrapper->createSingleSms($data);
    //$this->logger->log("[WRAPPER a la createSingleSms]:".date("H:i:s"));
      $this->db->commit();
    
    //$this->logger->log("[Final a la SingleSms]:".date("H:i:s"));
      return $this->set_json_response($res, 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while send singleSMS contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while send singleSMS contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/find/{idSms:[0-9]+}")
   */
  public function findAction($idSms) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $wrapper->findOneSms($idSms);
      return $this->set_json_response($wrapper->getSms(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/downloadreportsmsfailed/{idSms:[0-9]+}")
   */
  public function downloadreportsmsfailedAction($idSms) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $this->set_json_response($wrapper->downloadSmsFailedReport($data, $idSms), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => "Ha ocurrido un error, contacte al administrador"), 500);
    }
  }
      
  /**
   *  Metodo que me permite eliminar varios lotes creados en 
   *  la table de smslote este metodo es usado en caso de que 
   *  la persona quiera volver a subir un archivo tipo CSV.
   * 
   * @Post("/deletevariouslotes/{idSms:[0-9]+}")
   */
  public function deletevariouslotesAction($idSms) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper(); 
      $msg = $wrapper->deleteVariousSmsLotes($idSms);
      
      return $this->set_json_response($msg, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting varios smslotes contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * Metodo que permite saber por integracion la informacion de la campaña SMS atraves de su id
   * 
   * @Post("/getsmscampaigndetail")
   */
  public function getsmscampaigndetailAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper(); 
      $response= $wrapper->getsmscampaigndetail($data);
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Sms Campaign... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while while finding Sms Campaign... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * Function for validate the balance of the account when he create a new Campaing
   * @Post("/validatebalance")
   */
  public function validatebalanceAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $response= $wrapper->validateBalance($data);
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * Function for validate the balance of the account when he create a new Campaing
   * @Post("/getbalancesubaccount")
   */
  public function getbalancesubaccountAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $response= $wrapper->getbalancesubaccount($data);
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/sendmailnotsmsbalance")
   */
  public function sendmailnotsmsbalanceAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      $response= $wrapper->sendmailnotsmsbalance($data);
      return $this->set_json_response($response, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while while finding Sms Validate Balance... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   * 
   * @Post("/downloadfailedsmscontact/{idSms:[0-9]+}")
   */
  public function downloadfailedsmscontactAction($idSms) {      
    try {      
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $this->set_json_response($wrapper->downloadSmsFailedReportContact($data, $idSms), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => "Ha ocurrido un error, contacte al administrador"), 500);
    }
  }
}
