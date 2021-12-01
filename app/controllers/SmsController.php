<?php

ini_set('memory_limit', '512M');

class SmsController extends ControllerBase {

  protected $hoursms;
  protected $validatemorecaracter;
  protected $validatepush;

  public function initialize() {
    $this->tag->setTitle("Envíos de SMS");
    parent::initialize();
    $this->hoursms = new \stdClass();
    $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
    $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;
    

  }

  public function indexAction() {
    //Traigo los Estados de las campañas de Sms
    $flag = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 1 && $key->status==1) {
            $flag = true;
            $modelsManager = Phalcon\DI::getDefault()->get('modelsManager');
            $smsStatus = $modelsManager->createBuilder()
            ->columns("status")
            ->from("Sms")
            ->where("idSubaccount = {$this->user->Usertype->Subaccount->idSubaccount}")
            ->groupBy("status")
            ->getQuery()
            ->execute();
    foreach ($smsStatus as $key => $value) {
      $arraySmsStatus[] = $this->translateStatusSms($value["status"]);
    }
    $this->view->setVar("smsStatus", $arraySmsStatus);
      }
    }
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
   
  }



  public function createloteAction() {

    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);
    
    $flag = false;

    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 1 && $key->status==1 ) {
        $flag = true;
        $amount = $key->amount;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->user->Usertype->Subaccount->name;
        $accountName = $this->user->Usertype->Subaccount->Account->name;
        $arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName
        );
      }
    }
    
    if (!$flag) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    $idSubaccount = $this->user->Usertype->subaccount->idSubaccount;
    if($idSubaccount != 420 || $idSubaccount != "420"){//SI LA SUBCUENTA ES GALIAS (420) PERMITE SEGUIR ASI NO TENGA SALDO
        if (empty($data)) {
            if ($amount <= 0) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              //$sendMailNot->sendSmsNotification($arraySaxs);
              $this->notification->info("No tienes capacidad para enviar más sms");
              return $this->response->redirect("sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                $this->notification->info("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");
                return $this->response->redirect("sms");   
            }
        }
    }

    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
    $category = SmsCategory::find(["conditions" => "deleted = ?0 and idAccount = ?1", "bind" => [0 => 0, 1 => $idAccount], "order" => "created DESC"]);
    $this->view->setVar('category', $category);
    $smsloteform = new SmsloteForm();
    $this->view->setVar('smsloteform', $smsloteform);
    $this->view->setVar('hoursms', $this->hoursms);
    $this->view->setVar('idSubaccount', $idSubaccount);

    if (!empty($data)) {

      try {
        if($idSubaccount != 420 || $idSubaccount != "420"){
            if ($amount <= 0) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              //$sendMailNot->sendSmsNotification($arraySaxs);
              throw new InvalidArgumentException("No tienes capacidad para enviar más sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                throw new InvalidArgumentException("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");  
            }
        }     
        
        $this->validatemorecaracter = $data['morecaracter'];
        $this->validatepush = $data["sendpush"];
        $this->validateFastSending($data);
        $this->validateReceiver($data['receiver']);
        $sms = new Sms();

        $datenow = $data['datenow'];
        $timezone = $data['timezone'];

        if ($datenow) {
          $data["datesend"] = date('Y-m-d G:i:s', time());
          $timezone = "-0500";
        }
        
        if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
          throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
        }
        $dateStart = $this->validateDate($data["datesend"], $timezone);


        if (isset($data["email"])) {
          $email = explode(",", trim($data["email"]));
          if (!$data["notification"] or ! $data["advancedoptions"]) {
            $email = [];
          } else {
            $sms->notification = 1;
          }
          $sms->email = $email;
          $emails = $email;
        }
        if ($data["advancedoptions"]) {
          if ($data["divide"]) {
            /*if (!isset($data["quantity"]) or $data["quantity"] == 0) {
              throw new InvalidArgumentException("Debes indicar una cantidad correcta");
            }
            if (!isset($data["sendingTime"]) or $data["sendingTime"] == "") {
              throw new InvalidArgumentException("Debes elegir un tiempo de envío");
            }
            if (!isset($data["timeFormat"]) or $data["timeFormat"] == "") {
              throw new InvalidArgumentException("Debes elegir un formato de tiempo");
            }
            $sms->divide = 1;
            $sms->sendingTime = $data["sendingTime"];
            $sms->timeFormat = $data["timeFormat"];*/

            $sms->divide = 1;
            $sms->continueError = 0;
            if ($data["continueError"]) {
              $sms->continueError = 1;
            }
          }
        }

        $receiver = explode("\n", trim($data["receiver"]));
        if (empty($receiver[0])) {
          throw new InvalidArgumentException("Debes agregar al menos un destinatario");
        }

        /*if (count($receiver) > $amount) {
          $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
          //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de Sms
          $sendMailNot->sendSmsNotification($arraySaxs);
          throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envío(s) de SMS. Si necesitas más saldo, por favor contacta al administrador");
        }*/

//        $subaccount->smsLimit = $subaccount->smsLimit - count($receiver);
        $smsloteform->bind($data, $sms);
        if (!$smsloteform->isValid()) {
          foreach ($smsloteform->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $sms->advancedoptions = 0;
        if ($data["advancedoptions"] AND ( $data["notification"] || $data["divide"] || $data["sendpush"] )) {
          $sms->advancedoptions = 1;
        }

        $sms->target = count($receiver);
        $sms->idSubaccount = $this->user->Usertype->idSubaccount;
        $sms->status = $this->statusSms->scheduled;
        $sms->confirm = 1;
        $sms->logicodeleted = 0;
        $sms->type = $this->typeSms->lote;
        $sms->startdate = $dateStart;
        $sms->sent = 0;
        //$sms->morecaracter = $data['morecaracter'];
        if($data['morecaracter'] == "1" || $data['morecaracter'] == 1 || $data['morecaracter'] == true){
            $sms->morecaracter = 1;    
        }else{
            $sms->morecaracter = 0;    
        }

//        $sms->startdate = date("Y-m-d H:i:s", strtotime($data["datesend"]));
        $this->db->begin();
        (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");


        if ($sms->advancedoptions == 0) {
          $sms->notification = 0;
          $sms->email = null;
          $sms->divide = 0;
          $sms->continueError = 0;
          $sms->sendingTime = null;
          $sms->quantity = null;
          $sms->timeFormat = null;
        }

        $sms->dateNow = 1;
        $sms->gmt = null;
        $sms->originalDate = null;
        $sms->sendpush = $data["sendpush"];
        if (!$data["datenow"]) {
          $sms->dateNow = 0;
          $sms->gmt = $data["gmt"];
          $sms->originalDate = $data["originalDate"];
        }
        $sms->sendpush = $data["sendpush"]; 
        if (!$sms->save()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }

        $count = 0;
        $countMessage = 0;
        $validateMessageCount = 0;
        foreach ($receiver as $key) {
          $messageCount = 0;
          $arr = explode(";", $key);
          $flag = true;

          if (strstr($arr[0], "+")) {
            $flag = false;
          }
          if (mb_strlen(trim($arr[1]), 'UTF-8') != 10 || !is_numeric($arr[1])) {
            $flag = false;
          }
          if ($sms->morecaracter == 0 && mb_strlen($arr[2], 'UTF-8') > 160) {
            $flag = false;
          }
          if($sms->morecaracter == 1 && mb_strlen($arr[2], 'UTF-8') > 160){
            $countMessage = $countMessage + 2;
            $messageCount = 2;
          } else {
            $countMessage = $countMessage + 1;
            $messageCount = 1;
          }
//          if (!preg_match("/^[0-9a-zA-Z _.%]+$/", $arr[2])) {
//          if (preg_match("/[ñÑáéíóúÁÉÍÓÚ¿¡´]/", $arr[2])) {
//            $flag = false;
//          }
//          var_dump($flag);
            $validateMessageCount += $messageCount;
          if (count($arr) == 3 && $flag) {
            $smslote = new Smslote();
            $smslote->idSms = $sms->idSms;
            $smslote->indicative = $arr[0];
            $smslote->phone = trim($arr[1]);
            $smslote->message = trim($arr[2]);
            $smslote->status = $this->statusSms->scheduled;
            $smslote->messageCount = $messageCount;
            if (!$smslote->save()) {
              $this->db->rollback();
              foreach ($smslote->getMessages() as $message) {
                throw new InvalidArgumentException($message);
              }
              $this->trace("fail", "No se logro crear un lote");
            }
            $count++;
          }
        }
                        
        if ($count == 0) {
          $this->db->rollback();
          throw new InvalidArgumentException("El envío debe contener al menos un destinatario valido");
        }
        if($idSubaccount != 420 || $idSubaccount != "420"){
            if ($validateMessageCount > $amount) {
              if(abs($amount)){
                $tAvailable = (object) ["totalAvailable" => 0];
              } else {
                $tAvailable = (object) ["totalAvailable" => $amount];
              }
              $this->sendsmsnotsmsbalance($tAvailable);
              throw new \InvalidArgumentException("Solo puedes hacer " . $tAvailable->totalAvailable . " envío(s) de sms. Si nesesitas más saldo contacta al administrador");
            }
        }
        
        //Se realiza validaciones de los sms programados
        $balance = $this->validateBalance();
        $target = 0;
        if($balance['smsFindPending']){
          foreach ($balance['smsFindPending'] as $value){
            $target = $target + $value['target'];
          }
        }
        $amount = $balance['balanceConsumedFind'][0]['amount'];
        unset($balance);
        $totalTarget =  $amount - $target;
        $target = $target + count($countMessage);
        if($idSubaccount != 420 || $idSubaccount != "420"){
            if($target>$amount){
              $target = $target - $amount;
              if(abs($totalTarget)){
                $tAvailable = (object) ["totalAvailable" => 0];
              } else {
                $tAvailable = (object) ["totalAvailable" => $totalTarget];
              }
              $this->sendsmsnotsmsbalance($tAvailable);
              throw new \InvalidArgumentException("No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}");
            }
        }
        unset($target);
        unset($amount);
        unset($totalTarget);
        unset($tAvailable);
        
//        $subaccount->smsLimit = $subaccount->smsLimit - $count;
//        if (!$subaccount->save()) {
//          $this->db->rollback();
//          foreach ($subaccount->getMessages() as $message) {
//            throw new InvalidArgumentException($message);
//          }
//          $this->trace("fail", "No se logro crear una cuenta");
//        }

        $this->db->commit();
        $this->session->set("msgsuccesssmslote", true);
        return $this->set_json_response(["Se ha creado el lote de sms!"], 200, "OK");
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(["message" => $msg->getMessage()], 409, "FAIL");
      } catch (Exception $e) {
        $this->logger->log("Exception while creating account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
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
    /*if ($data['divide']) {
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
    }*/
  }

  public function validateReceiver($receiver) {
    $breaks = explode("\n", $receiver);
    $flagValidate = false;


    // recorre el array de destinatarios
    for ($i = 0; $i < count($breaks); $i++) {

      $sms = explode(";", $breaks[$i]);
      // valida si el el destinatario se encuentra separado por 3 partes con un (;     
      $count = count($sms);
      if ($count > 3) {
        $flagValidate = true;
      }
      if ($flagValidate) {
        throw new \InvalidArgumentException("Hay algún destinatario con el formato erróneo, por favor verifique.");
      }
      if(isset($this->validatepush)){
        if(($this->validatepush == 1 || $this->validatepush == true) && ( $this->validatemorecaracter == 1 || $this->validatemorecaracter == true)){
            throw new \InvalidArgumentException("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres."); 
        }  
      }

      // valida el numero de caracteres del mensaje de texto
      $countMessage = mb_strlen($sms[2], 'UTF-8');
      if($this->validatemorecaracter == "1" || $this->validatemorecaracter == 1 || $this->validatemorecaracter == true){
        if ($countMessage > 300) {
          throw new \InvalidArgumentException("Los mensajes deben contener un maximo de 300 caracteres.");
        }
      }else{
        if ($countMessage > 160) {
          throw new \InvalidArgumentException("Los mensajes deben contener un maximo de 160 caracteres.");
        }
      }

      // valida si el indicativo que ingresa es correcto con el pais
      $Country = Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $sms[0]]]);
      if (!$Country) {
        throw new \InvalidArgumentException("No se encuentra el indicativo del pais, por favor verifique.");
      }

      $phone = str_replace(' ', '', $sms[1]);

      //valida solo numeros del celular ingresado
      if (ctype_digit($phone)) {
        // valido para numeros
      } else {
        throw new \InvalidArgumentException("Existe un número que contiene letras (" . $phone . "), verifique.");
      }

      // valida que el numero de digitos sea correcto
      $valor = mb_strlen($phone, 'UTF-8');
      if ($valor != 10) {
        throw new \InvalidArgumentException("La cantidad de dígitos del número " . $sms[1] . " es incorrecto, verifique.");
      }

      // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
      $phone = substr($phone, 0, 3);
      $PhonePrefix = PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phone]]);
      if (!$PhonePrefix) {
        throw new \InvalidArgumentException("Verifique que el número " . $sms[1] . " sea valido, de acuerdo al indicativo del país.");
      }
    }
  }

  public function createcontactAction() {
    
    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);
    
    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms && $key->status==1) {
        $flag = true;
        $amount = $key->amount;
        $totalAmount = $key->totalAmount;
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
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    $idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
    if($idSubaccount != "420" || $idSubaccount != 420){
        if (empty($data)) {
            if ($amount <= 0) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              //$sendMailNot->sendSmsNotification($arraySaxs);
              $this->notification->info("No tienes saldo disponible para realizar envíos de SMS");
              return $this->response->redirect("sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                $this->notification->info("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");
                return $this->response->redirect("sms");   
            }
        }
    }

    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
    $category = SmsCategory::find(["conditions" => "deleted = ?0 and idAccount = ?1", "bind" => [0 => 0, 1 => $idAccount], "order" => "created DESC"]);
    $this->view->setVar('idAccount', $idAccount);
    $this->view->setVar('category', $category);
    $smsloteform = new SmsloteForm();
    $this->view->setVar('smsloteform', $smsloteform);
    $this->view->setVar('hoursms', $this->hoursms);
    $this->view->setVar('idSubaccount', $idSubaccount);

    if (!empty($data)) {
      try {
        
        if($idSubaccount != 420 || $idSubaccount != "420"){
            if ($amount <= 0) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              //$sendMailNot->sendSmsNotification($arraySaxs);
              throw new InvalidArgumentException("No tienes capacidad para enviar más sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                throw new InvalidArgumentException("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");  
            }
        } 
        
        $this->validateFastSending($data);

        $sms = new Sms();

        $datenow = $data['datenow'];
        $timezone = $data['timezone'];
        if ($datenow) {
          $data["datesend"] = date('Y-m-d G:i:s', time());
          $timezone = "-0500";
        }

        if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
          throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
        }
        $dateStart = $this->validateDate($data["datesend"], $timezone);


        if (isset($data["email"])) {
          $email = explode(",", trim($data["email"]));
          if (!$data["notification"] or ! $data["advancedoptions"]) {
            $email = [];
          }
          $sms->email = $email;
          $emails = $email;
//          var_dump($sms->email);
//          exit();
        }
        if ($data['morecaracter'] == false) {
            if (mb_strlen(trim($data['message']), 'UTF-8') > 160) {
                throw new \InvalidArgumentException("El campo mensaje debe tener máximo 160 caracteres");
            } 
        }else{
            if (mb_strlen(trim($data['message']), 'UTF-8') > 300) {
                throw new \InvalidArgumentException("El campo mensaje debe tener máximo 300 caracteres");
            }
        }
         
        if ($data["advancedoptions"]) {
          if ($data["divide"]) {
            /*if (!isset($data["quantity"]) or $data["quantity"] == 0) {
              throw new InvalidArgumentException("Debes indicar una cantidad correcta");
            }
            if (!isset($data["sendingTime"]) or $data["sendingTime"] == "") {
              throw new InvalidArgumentException("Debes elegir un tiempo de envío");
            }
            if (!isset($data["timeFormat"]) or $data["timeFormat"] == "") {
              throw new InvalidArgumentException("Debes elegir un formato de tiempo");
            }
            $sms->divide = 1;
            $sms->sendingTime = $data["sendingTime"];
            $sms->timeFormat = $data["timeFormat"];*/

            $sms->divide = 1;
            $sms->continueError = 0;
            if (isset($data["continueError"])) {
              $sms->continueError = 1;
            }
          }
        }


        if (!isset($data['receiver'])) {
          throw new InvalidArgumentException("Debes agregar al menos un destinatario");
        }
        if($idSubaccount != "420" || $idSubaccount != 420){
            if ($data['target'] > $amount) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              $sendMailNot->sendSmsNotification($arraySaxs);
              throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de SMS. Si necesitas más saldo contacta al administrador");
            }    
        }
        $smsloteform->bind($data, $sms);
        if (!$smsloteform->isValid()) {
          foreach ($smsloteform->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        $sms->advancedoptions = 0;
        if ($data["advancedoptions"] AND ( $data["notification"] || $data["divide"])) {
          $sms->advancedoptions = 1;
        }


        $this->db->begin();
        (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

        $sms->target = $data['target'];
        $sms->idSubaccount = $this->user->Usertype->idSubaccount;
        $sms->idSmsCategory = $data['idSmsCategory'];

        // se cambia el codigo para que valide primero antes de enviarlo
        // $sms->status = $this->statusSms->scheduled;
        $sms->status = 'draft';

        $sms->confirm = 1;
        $sms->logicodeleted = 0;
        $sms->type = $this->typeSms->contact;
        $sms->startdate = $dateStart;
        $sms->receiver = json_encode($data['receiver']);

        $sms->message = $data['message'];
//        $sms->sent = $data["AproximateSendings"];
        if($data['morecaracter'] == 1 || $data['morecaracter'] == '1' || $data['morecaracter'] == true){
            $sms->morecaracter = 1;    
        }else{
            $sms->morecaracter = 0;        
        }
        $sms->sent = 0;
        $sms->notification = 0;
        if ($data["notification"]) {
          $sms->notification = 1;
        }

        if ($sms->advancedoptions == 0) {
          $sms->notification = 0;
          $sms->email = null;
          $sms->divide = 0;
          $sms->continueError = 0;
          $sms->sendingTime = null;
          $sms->quantity = null;
          $sms->timeFormat = null;
        }

        $sms->dateNow = 1;
        $sms->gmt = null;
        $sms->originalDate = null;
        $sms->sendpush = $data['sendpush'];
        if($sms->sendpush == 1 && $sms->morecaracter == 1){
           throw new \InvalidArgumentException("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.");  
        }
        if (!$data["datenow"]) {
          $sms->dateNow = 0;
          $sms->gmt = $data["gmt"];
          $sms->originalDate = $data["originalDate"];
        }

        if ($data['switchrepeated']) {
          $sms->singleSendContact = 1;
        } else {
          $sms->singleSendContact = 0;
        }

        if (!$sms->save()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }

        $arrIdContactlist = array();
        $arrIdContact = array();
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        //si el envío es unico por contacto
        if ($sms->singleSendContact == 1) {
          //Si es de tipo contactlist
          if (strpos($sms->receiver, 'contactlists') !== false) {
            //Extrae todos los id de Contactlist del envío
            foreach ($data['receiver']['contactlists'] as $key) {
              $arrIdContactlist[] = (int) $key['idContactlist'];
            }
            //Genera un string separado por coma con los ids de contactlist
            $commaSeparatedIdContact = implode(",", $arrIdContactlist);
            /*Busca todos los contactos de esas listas en cxcl y se agrupa por idContact, ya que es equivalente a un DISTINCT */
            $cxcl = \Cxcl::find(array(
                        "conditions" => "idContactlist IN ($commaSeparatedIdContact) "
                        . "and deleted = 0 "
                        . "AND unsubscribed = 0 "
                        . "AND status='active'",
                        "group" => "idContact"
                    ))->toArray();
            //Se extraen los id Contact de la consulta anterior
            foreach ($cxcl as $val) {
              $arrIdContact[] = (int) $val["idContact"];
            }
            $idAccount = $this->user->Usertype->Subaccount->idAccount;
            $arrayAccounts = ['49',49,'101',101,'1387',1387];
            //if($idAccount != 49 || $idAccount != '49' || $idAccount != 101 || $idAccount != '101'){
                if(!in_array($idAccount, $arrayAccounts)){
                //Se genera los parametros a consultar en coleccion Contact
                $conditions = array(
                      'idContact' => array('$in' =>$arrIdContact),
                      'phone' => array('$ne' => ""),
                      'blockedPhone' => array('$in' => array("", null, "null"))
                  );
                
                $command = new \MongoDB\Driver\Command([
                  'aggregate' => 'contact',
                  'pipeline' => [
                      ['$match' => $conditions],
                      ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
                    ]
                  ]);
                $contact = $manager->executeCommand('aio', $command)->toArray();
                if ($contact) {
                  $contact = $contact[0]->result;
                  $this->logger->log(print_r(count($contact),true));
                  foreach ($contact as $c){
                    $c = $c->data;
                    $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
                  }
                }                
            }
          } else {
            $arrIdContactFromSegment = array();
            foreach ($data['receiver']['segment'] as $key) {
              $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);
              if ($sxcs) {
                foreach ($sxcs as $sxc) {
                  $arrIdContactFromSegment[] = (int) $sxc->idContact;
                }
              }
            }
            $contactConditions = array(
                'idContact' => array('$in' =>$arrIdContactFromSegment),
                'phone' => array('$ne' => ""),
                'blockedPhone' => array('$in' => array("", null, "null"))
            );
            
            $command = new \MongoDB\Driver\Command([
              'aggregate' => 'contact',
              'pipeline' => [
                  ['$match' => $contactConditions],
                  ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
                ]
              ]);
            
            $contactsFromSegment = $manager->executeCommand('aio', $command)->toArray();
            
            if ($contactsFromSegment) {
              $contactsFromSegment = $contactsFromSegment[0]->result;
              foreach ($contactsFromSegment as $c){
                $c = $c->data;
                $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
              }
            } 
          }
        }else{
          if (strpos($sms->receiver, 'contactlists') !== false) {
            //Extrae todos los id de Contactlist del envío
            foreach ($data['receiver']['contactlists'] as $key) {
              $arrIdContactlist[] = (int) $key['idContactlist'];
            }
            //Genera un string separado por coma con los ids de contactlist
            $commaSeparatedIdContact = implode(",", $arrIdContactlist);
            /*Busca todos los contactos de esas listas en cxcl y
              se agrupa por idContact, ya que es equivalente a un DISTINCT */
            $cxcl = \Cxcl::find(array(
                        "conditions" => "idContactlist IN ($commaSeparatedIdContact) "
                        . "and deleted = 0 "
                        . "AND unsubscribed = 0 "
                        . "AND status='active'"
                        //"group" => "idContact"
                    ))->toArray();
            //Se extraen los id Contact de la consulta anterior
            foreach ($cxcl as $val) {
              $arrIdContact[] = (int) $val["idContact"];
            }

            //var_dump((count($arrIdContact)));exit;

            foreach ($arrIdContact as $value) {
            //Se genera los parametros a consultar en coleccion Contact
             $conditions = array(
                  'idContact' => $value,
                  'phone' => array('$ne' => ""),
                  'blockedPhone' => array('$in' => array("", null, "null"))
              );
             //Se busca en la coleccion de Contact teniendo en cuenta los numeros repetidos
             $contact = \Contact::find(array($conditions));
             //Si hay resultados, aplique la validacion
             if($contact){ 
              foreach($contact as $c){
                $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
               }
             }
           }
          }else{
            $arrIdContactFromSegment = array();
            foreach ($data['receiver']['segment'] as $key) {
              $sxcs = \Sxc::findFirst([["idSegment" => $key->idSegment]]);
              if ($sxcs) {
                foreach ($sxcs as $sxc) {
                  $arrIdContactFromSegment[] = (int) $sxc->idContact;
                }
              }
            }
            foreach ($arrIdContactFromSegment as $value) {
              $contactConditions = array(
                'idContact' => $value,
                'phone' => array('$ne' => ""),
                'blockedPhone' => array('$in' => array("", null, "null"))
              );
              $contactsFromSegment = \Contact::findFirst(array($contactConditions));
              if($contactsFromSegment){ 
                foreach($contactsFromSegment as $c){
                  $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
                }
              }
            }
          }  
        }

        unset($cxcl);
        unset($conditions);
        unset($command);
            
        $this->db->commit();

        // Cuenta los erroneos en sms_failed
        $SmsFailed = SmsFailed::count(["conditions" => "idSms = ?0", 
                                       "bind" => [0 => (int) $sms->idSms]]);
        //Resta los registros erroneos del target de envío
        $sms->target = $sms->target - $SmsFailed;

        if (!$sms->save()) {
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se cambio el target error");
        }
        $this->session->set("msgsuccesssmscontact", true);
        return $this->set_json_response(["Envios" => $sms->target, "Invalidos" => $SmsFailed, "IdSms" => $sms->idSms], 200, "OK");
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(["message" => $msg->getMessage()], 409, "FAIL");
      } catch (Exception $e) {
        $this->logger->log("Exception while creating account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
    }
  }

  public function showloteAction($idSms) {
    if (!$idSms) {
      $this->notification->error("No puedes ingresar aqui");
      return $this->response->redirect("sms/index");
    }
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      $this->notification->error("Verifica la información enviada");
      return $this->response->redirect("sms/index");
    }
    $builder = $this->modelsManager->createBuilder()
            ->from('Smslote')
            ->where("Smslote.idSms = {$idSms}")
            ->orderBy('Smslote.status');

    $currentPage = $this->request->getQuery('page', null, 1);
    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => 15,
        "page" => $currentPage
    ));
    $page = $paginator->getPaginate();
    $this->view->setVar("page", $page);
    $this->view->setVar("sms", $sms);
  }

  public function deleteAction($idSms) {
    if (!$idSms) {
      $this->notification->error("No puedes ingresar aqui");
      return $this->response->redirect("sms/index");
    }
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      $this->notification->error("Verifica la información enviada");
      return $this->response->redirect("sms/index");
    }

    try {
      $flagLogicoDeleted = false;
      if ($sms->status == $this->statusSms->sent && $sms->startdate > date('Y-m-d', strtotime("-1 month"))) {
        $this->notification->error("Debes esperar un mes después de la fecha de envío para poder eliminar un registro");
        return $this->response->redirect("sms/index");
      }
      if ($sms->status == $this->statusSms->sent && $sms->startdate < date('Y-m-d', strtotime("-1 month"))) {
        $flagLogicoDeleted = true;
      }
      if ($sms->status != $this->statusSms->schenduled and ! $flagLogicoDeleted) {
        $this->trace('error', "No se pudo eliminar el sms: {$idSms}");
        throw new ErrorException("No se puede eliminar este envio por que no esta en estado programado");
      }

      $this->db->begin();
      if ($flagLogicoDeleted) {
        $sms->logicodeleted = 1;
        if (!$sms->update()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $this->trace('success', "Se elimino el sms: {$idSms}");
        $this->notification->warning("Se ha eliminado con exito el registro");
      } else {
        $subaccount = Subaccount::findFirst(array(
                    "conditions" => "idSubaccount = ?0",
                    "bind" => array(0 => $this->user->Usertype->idSubaccount)
        ));
        $subaccount->smsLimit = $subaccount->smsLimit + count($sms->smslote);

        if (!$sms->smslote->delete()) {
          $this->db->rollback();
          foreach ($sms->smslote->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        if (!$sms->delete()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        if (!$subaccount->update()) {
          $this->db->rollback();
          foreach ($subaccount->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $this->trace('success', "Se elimino el sms: {$idSms}");
        $this->notification->warning("Se ha eliminado con exito el registro");
      }
      $this->db->commit();
      return $this->response->redirect("sms/index");
    } catch (ErrorException $e) {
      $this->notification->error($e->getMessage());
      return $this->response->redirect("sms/index");
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function editAction($idSms) {

    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      echo $key->idServices;
      if ($key->idServices == 1 && $key->status==1) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    
    $idSubaccount = Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount;
    $this->view->setVar('idSubaccount', $idSubaccount);
    \Phalcon\DI::getDefault()->get('logger')->log("amount edit ".json_encode($amount));
    if($idSubaccount != "420" || $idSubaccount != 420){
      if ($amount == 0 || $amount < 0) {
        $this->notification->info("No tienes capacidad para enviar más sms");
        return $this->response->redirect("sms");
      }
    }

    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
    $category = SmsCategory::find(["conditions" => "deleted = ?0 and idAccount = ?1", "bind" => [0 => 0, 1 => $idAccount], "order" => "created DESC"]);
    $this->view->setVar('category', $category);

    $sql = "SELECT countries, gmt FROM timezone";
    $timezones = $this->db->fetchAll($sql);

    $this->view->setVar('timezones', $timezones);

    if (!$idSms) {
      $this->notification->error("No puedes ingresar aqui");
      return $this->response->redirect("sms/index");
    }
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));

    if (!$sms) {
      $this->notification->error("Verifica la información enviada");
      return $this->response->redirect("sms/index");
    }
    $customLogger = new \Logs();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
    $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
    $customLogger->idSms = $idSms;
    $customLogger->typeName = "editSmsMethod";
    $customLogger->detailedLogDescription = "Se ha editado el tipo " . $sms->type . " de sms";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    unset($customLogger);
    $sms->status = $this->statusSms->draft;
    $sms->update();
    $subaccount = Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array(0 => $this->user->Usertype->idSubaccount)
    ));
    $smsform = new SmsloteForm($sms);
    $this->view->setVar('sms', $sms);
    $arr = "";
    foreach ($sms->smslote as $key) {
      $arr = $arr . $key->indicative . " ," . $key->phone . " ," . $key->message . "\r\n";
    }
    $this->view->setVar('smsloteform', $smsform);
    $this->view->setVar('receiver', $arr);
    $this->view->setVar('sms', $sms);
    $this->view->setVar('hoursms', $this->hoursms);

    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);
//    var_dump($data);
//    exit;
    $this->logger->log("Inicio Edit");
      $this->logger->log(print_r($data, true));
      $this->logger->log("Final Edit");
    if (!empty($data)) {
      try {
        $this->validateFastSending($data);

        if ($sms->type != 'csv') {
          $this->validatemorecaracter = $data['morecaracter'];
          $this->validatepush = $data["sendpush"];
          $this->validateReceiver($data['receiver']);
        }
        $datenow = $data['datenow'];


        $receiver = explode("\n", trim($data["receiver"]));
        if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
          throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
        }
        if (isset($data["email"])) {

          $email = explode(",", trim($data["email"]));
          if (!$data["notification"] or ! $data["advancedoptions"]) {
            $email = [];
          }
          $sms->email = $email;
          $emails = $email;
        }

        if ($data["advancedoptions"]) {
          if ($data["divide"]) {
            /*if (!isset($data["quantity"]) or $data["quantity"] == 0) {
              throw new InvalidArgumentException("Debes indicar una cantidad correcta");
            }
            if (!isset($data["sendingTime"]) or $data["sendingTime"] == "") {
              throw new InvalidArgumentException("Debes elegir un tiempo de envío");
            }
            if (!isset($data["timeFormat"]) or $data["timeFormat"] == "") {
              throw new InvalidArgumentException("Debes elegir un formato de tiempo");
            }
            $sms->divide = 1;
            $sms->sendingTime = $data["sendingTime"];
            $sms->timeFormat = $data["timeFormat"];*/

            $sms->divide = 1;
            $sms->continueError = 0;
            if ($data["continueError"]) {
              $sms->continueError = 1;
            }
          }
        }

        if ($sms->type != 'csv') {
          if (empty($receiver[0])) {
            throw new InvalidArgumentException("Debes agregar al menos un destinatario");
          }
        }

        $smsform->bind($data, $sms);

        if (!$smsform->isValid()) {
          foreach ($smsform->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $sms->advancedoptions = 0;
        if ($data["advancedoptions"] AND ( $data["notification"] || $data["divide"] || $data["sendpush"] )) {
          $sms->advancedoptions = 1;
        }

        if ($sms->advancedoptions == 0) {
          $sms->notification = 0;
          $sms->email = null;
          $sms->divide = 0;
          $sms->continueError = 0;
          $sms->sendingTime = null;
          $sms->quantity = null;
          $sms->timeFormat = null;
        }

        $smslote = Smslote::find(array(
                    "conditions" => "idSms = ?0",
                    "bind" => array(0 => $sms->idSms)
        ));

        (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : $sms->startdate = date("Y-m-d H:i:s", strtotime($data["datesend"])));

        $this->db->begin();
        $sms->status = $this->statusSms->scheduled;
//        var_dump($data["datenow"]);
//        exit;
        $sms->dateNow = 1;
        $sms->gmt = null;
        $sms->originalDate = null;
        
        if($data['morecaracter'] == 1 || $data['morecaracter'] == '1' || $data['morecaracter'] == true){
            $sms->morecaracter = 1;    
        }else{
            $sms->morecaracter = 0;        
        }       
        
        if (!$data["datenow"]) {
          $sms->dateNow = 0;
          $sms->gmt = $data["gmt"];
          $sms->originalDate = $data["originalDate"];
        }
        $sms->sendpush = $data["sendpush"];
        if($sms->sendpush == 1 && $sms->morecaracter == 1){
           throw new \InvalidArgumentException("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.");  
        }
        if (!$sms->save()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }
        if ($sms->type != 'csv') {
          if (!$smslote->delete()) {
            $this->db->rollback();
            foreach ($smslote->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
            $this->trace("fail", "No se logro crear una cuenta");
          }
        }
        $count = 0;
        foreach ($receiver as $key) {
          $arr = explode(";", $key);
          $flag = true;
          if (strpos($arr[0], "+")) {
            $flag = false;
          }
          if (mb_strlen(trim($arr[1]), 'UTF-8') != 10) {
            $flag = false;
          }
          if ($data['morecaracter'] == false) {
            if (mb_strlen(trim($arr[2]), 'UTF-8') > 160) {
                $flag = false;
            } 
          }else{
            if (mb_strlen(trim($arr[2]), 'UTF-8') > 300) {
            // if (mb_strlen(str_replace(" ", "", $arr[2]),'UTF-8') > 160) {
            $flag = false;
            }
          } 

//          if (!preg_match("/^[0-9a-zA-Z _]+$/", $arr[2])) {
//            $flag = false;
//          }

          if (count($arr) == 3 && $flag) {
            $smslote = new Smslote();
            $smslote->idSms = $sms->idSms;
            $smslote->indicative = $arr[0];
            $smslote->phone = trim($arr[1]);
            $smslote->message = trim($arr[2]);
            $smslote->status = $this->statusSms->scheduled;
            if(mb_strlen(trim($arr[2]), 'UTF-8') <= 160){
                $smslote->messageCount = 1;
            }else if(in_array(mb_strlen(trim($arr[2]), 'UTF-8'), range(160, 300)) ) {
                $smslote->messageCount = 2;
            }
            if (!$smslote->save()) {
              $this->db->rollback();
              foreach ($smslote->getMessages() as $message) {
                throw new InvalidArgumentException($message);
              }
              $this->trace("fail", "No se logro crear una cuenta");
            }
            $count++;
          }
        }

        if ($sms->type != 'csv') {
          if ($count == 0) {
            $this->db->rollback();
            throw new InvalidArgumentException("El envío debe contener al menos un destinatario valido");
          }
        }
//        if ($count != count($sms->smslote)) {
//          if ($count > count($sms->smslote)) {
//            if (($count - count($sms->smslote)) > $this->user->Usertype->subaccount->smsLimit) {
//              throw new InvalidArgumentException("Solo puedes hacer " . $this->user->Usertype->subaccount->smsLimit . " envíos de sms si nesesitas más saldo contacta al administrador");
//            }
//            $subaccount->smsLimit = $subaccount->smsLimit - ($count - count($sms->smslote));
//          } else if ($count < count($sms->smslote)) {
//            $subaccount->smsLimit = $subaccount->smsLimit + (count($sms->smslote) - $count);
//          }
//        }

        if ($sms->type != 'csv') {
          $receiver = explode("\n", trim($data["receiver"]));
          if (empty($receiver[0])) {
            throw new InvalidArgumentException("Debes agregar al menos un destinatario");
          }
        }

        if($idSubaccount != "420" || $idSubaccount != 420){
          if (count($receiver) > $amount) {
            throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envío(s) de sms. Si nesesitas más saldo contacta al administrador");
          }
        }

        if (!$subaccount->save()) {
          $this->db->rollback();
          foreach ($subaccount->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }
        $this->db->commit();
        $this->session->set("msgsuccesssmsloteedit", true);

        return $this->set_json_response(["Se ha editado el lote de sms!"], 200, "OK");
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response([$msg->getMessage()], 409, "FAIL");
      } catch (Exception $e) {
        $this->logger->log("Exception while creating account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
    }
  }

  public function createcsvAction() {
    
    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);

    $saxss = \Saxs::find(array("conditions" => "idSubaccount = ?0", "bind" => array($this->user->userType->idSubaccount)));
    $flagSMSService = false;
    if ($saxss) {
      foreach ($saxss as $saxs) {
        if ($saxs->idServices == $this->services->sms) {
          $flagSMSService = true;
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

      if (!$flagSMSService) {
        $this->notification->info("No tienes el servicio de envío de sms asignado");
        return $this->response->redirect("");
      }
      $idSubaccount = $this->user->userType->idSubaccount;
      if($idSubaccount != "420" || $idSubaccount != 420){
        if(empty($data)){
            if ($amount <= 0) {
                $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
                $sendMailNot->sendSmsNotification($arraySaxs);
                $this->notification->info("No tienes saldo disponible para realizar envíos de SMS");
                return $this->response->redirect("sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                $this->notification->info("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");
                return $this->response->redirect("sms");   
            }
        }
      }
      $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
      $category = SmsCategory::find(["conditions" => "deleted = ?0 and idAccount = ?1", "bind" => [0 => 0, 1 => $idAccount], "order" => "created DESC"]);
      $this->view->setVar('category', $category);
      $smsloteform = new SmsloteForm();
      $this->view->setVar('smsloteform', $smsloteform);
      $this->view->setVar('hoursms', $this->hoursms);
      if ($this->request->isPost()) {

        try {
            
        if($idSubaccount != 420 || $idSubaccount != "420"){
            if ($amount <= 0) {
              $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
              $sendMailNot->sendSmsNotification($arraySaxs);
              throw new InvalidArgumentException("No tienes capacidad para enviar más sms");
            }
            $scheduled = \Sms::find([
                    "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                    "bind" => [0 => $idSubaccount]
            ]);
            $countTargetScheduled = 0;
            foreach($scheduled  as $sc){
                $countTargetScheduled += $sc->target;
            }
            $rest = $arraySaxs["amount"] - $countTargetScheduled;
            if($rest < 0){
                throw new InvalidArgumentException("No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");  
            }
        }
            
          $this->db->begin();
          $this->validateFastSending($this->request->isPost());
          $datenow = $this->request->getPost("sendnow");
          if ($_FILES['csv']["error"] == 4) {
            throw new InvalidArgumentException("No has seleccionado un archivo CSV");
          }
          if ($this->request->getPost("notification")) {
            $email = $this->request->getPost("email");
            $email = explode(",", $email);
            if (count($email) > 8) {
              throw new InvalidArgumentException("No se puede ingresar más 8 correos electrónicos");
            }
            if($idSubaccount != "420" || $idSubaccount != 420){
                if (count($email) > $amount) {
                  throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms. Si nesesitas más saldo contacta al administrador");
                }
            }
          }
          if ($_FILES['csv']['size'] > 2097152) {
            throw new InvalidArgumentException("El archivo CSV excede el tamaño las 2 megabytes aceptadas");
          }
          $startdate = $this->request->getPost("startdate");

          if ($this->request->getPost("sendnow") == 1) {
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
          $continueError = $this->request->getPost("continueError");
          $quantity = $this->request->getPost("quantity");
          $sendingTime = $this->request->getPost("sendingTime");
          $timeFormat = $this->request->getPost("timeFormat");

          if ($advancedoptions) {
          if ($divide) {
            /*if (!isset($data["quantity"]) or $data["quantity"] == 0) {
              throw new InvalidArgumentException("Debes indicar una cantidad correcta");
            }
            if (!isset($data["sendingTime"]) or $data["sendingTime"] == "") {
              throw new InvalidArgumentException("Debes elegir un tiempo de envío");
            }
            if (!isset($data["timeFormat"]) or $data["timeFormat"] == "") {
              throw new InvalidArgumentException("Debes elegir un formato de tiempo");
            }
            $sms->divide = 1;
            $sms->sendingTime = $data["sendingTime"];
            $sms->timeFormat = $data["timeFormat"];*/

            $sms->divide = 1;
            $sms->continueError = 0;
            if ($continueError) {
              $sms->continueError = 1;
            }
          }
        }

          $sms->advancedoptions = 0;
          if ($advancedoptions == 1 AND ( $notification == 1 || $divide == 1)) {
            $sms->advancedoptions = 1;
          }

          (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");
          $sms->idSubaccount = $this->user->Usertype->idSubaccount;
          $sms->confirm = 1;
          $sms->status = $this->statusSms->scheduled;
          $sms->sent = 0;
          $sms->logicodeleted = 0;
          $sms->type = $this->typeSms->csv;

          $sms->notification = 0;
          if ($notification == 1) {
            $sms->notification = 1;
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
          if ($this->request->getPost("sendnow") != 1) {
            $sms->dateNow = 0;
            $sms->gmt = $this->request->getPost("timezone");
            $sms->originalDate = $this->request->getPost("startdate");
          }

          if (!$sms->save()) {
            foreach ($sms->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
            $this->trace("fail", "No se logro crear el smslote {$message}");
          }

          $file = new Sigmamovil\General\Misc\FileManager();
          $resul = $file->csvsms($_FILES['csv'], $sms->idSms);
          $sms->target = $resul["success"];
          if (!$sms->save()) {
            foreach ($sms->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
            $this->trace("fail", "No se logro crear el smslote {$message}");
          }

          foreach ($sms->Subaccount->Saxs as $key) {
            if ($key->idServices == 1) {
              $saxs = $key;
            }
          }
          
          if($idSubaccount != "420" || $idSubaccount != 420){
              if ($resul["success"] > $saxs->amount) {
                throw new InvalidArgumentException("Solo puedes hacer " . $saxs->amount . " envíos de sms si nesesitas más saldo contacta al administrador");
              }
          }

          $this->db->commit();
          $this->notification->success("Se ha creado un envío de SMS por archivo de CSV con un total de registros validos de {$resul["success"]}");
          $this->trace("success", "se ha creado un envio de sms por csv: {$sms->idSms}/{$sms->name}");
//          return $this->response->redirect('sms/createcsv');
          return $this->response->redirect('sms');

//          return false;
        } catch (InvalidArgumentException $e) {
          $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp_sms_csv_{$sms->idSms}");
          $this->db->rollback();
          $this->logger->log("InvalidException while creating smslotecsv: {$e->getMessage()}");
          $this->logger->log($e->getTraceAsString());
          $this->notification->error($e->getMessage());
        } catch (Exception $e) {
          $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp_sms_csv_{$sms->idSms}");
          $this->db->rollback();
          $this->logger->log("Exception while creating smslotecsv: {$e->getMessage()}");
          $this->logger->log($e->getTraceAsString());
          $this->notification->error($e->getMessage());
        }
      }
    }

    /**
     * 
     * @param Date $date  "1990-10-21 13:00:00"
     * @param String $timezone "-0500"
     * @return Date 
     * @throws InvalidArgumentException
     */
  }

  public function validateDate($date, $timezone) {
    if (!isset($this->hoursms) || empty($this->hoursms)) {
      $this->hoursms = new \stdClass();
      $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
      $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;
    }
    $timezone = substr($timezone, 0, 3);
    if ($timezone[1] == 0) {
      $typeGmt = substr($timezone, 0, 1);
      $timezone = substr($timezone, 2, 2);
    }
    if ($typeGmt == "-") {
      if ($timezone > 5) {
        $timezone = $timezone - 5;
      } else {
        $typeGmt = "+";
        $timezone = 5 - $timezone;
      }
    } else if ($typeGmt == "+") {
      $timezone = 5 + $timezone;
    }
    $datenowstr = strtotime("{$typeGmt}{$timezone} hour", strtotime($date));
    $dateStart = date("Y-m-d H:i:s", $datenowstr);
    $hour = date("H", $datenowstr);

//    var_dump($hour,$this->hoursms->startHour,$this->hoursms->endHour);
//    exit();
    if ($hour < $this->hoursms->startHour || $hour >= $this->hoursms->endHour) {
      throw new InvalidArgumentException("La hora de envio debe de ser entre las {$typeGmt}-{$timezone}-- " . $this->hoursms->startHour . ":00  y las " . $this->hoursms->endHour . ":00 de acuerdo al GMT seleccionado");
    }
    return $dateStart;
  }

  public function toolsAction() {
    
  }

  public function editcontactAction($idSms) {
    if (!$idSms) {
      $this->notification->error("No puedes ingresar aqui");
      return $this->response->redirect("sms/index");
    }
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      $this->notification->error("Verifica la información enviada");
      return $this->response->redirect("sms/index");
    }
    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    $idSubaccount = $this->user->Usertype->subaccount->idSubaccount;
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }  foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    if ($amount == 0) {
      $this->notification->info("No tienes capacidad para enviar más sms");
      return $this->response->redirect("sms");
    }

    $sms->status = $this->statusSms->draft;
    $sms->update();

    $customLogger = new \Logs();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
    $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
    $customLogger->idSms = $idSms;
    $customLogger->typeName = "editSmsMethod";
    $customLogger->detailedLogDescription = "Se ha editado el tipo " . $sms->type . " de sms";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    unset($customLogger);

    $subaccount = Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array(0 => $this->user->Usertype->idSubaccount)
    ));
    $category = SmsCategory::find();
    $this->view->setVar('category', $category);
    $smsform = new SmsloteForm($sms);
    $this->view->setVar('sms', $sms);
    $this->view->setVar('smsloteform', $smsform);
    $this->view->setVar('hoursms', $this->hoursms);


    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);
    if (!empty($data)) {
      try {
        $datenow = $data['datenow'];
        $timezone = $data['timezone'];
        if ($datenow) {
          $data["datesend"] = date('Y-m-d G:i:s', time());
        }

        if ($data["datesend"]) {
          if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
            throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
          }
        }
        if ($data["datesend"]) {
          $dateStart = $this->validateDate($data["datesend"], $timezone);
        }
        if (isset($data["email"])) {
          $email = explode(",", trim($data["email"]));
          if (!$data["notification"]) {
            $email = [];
          }
          $sms->email = $email;
          $emails = $email;
//          var_dump($sms->email);
//          exit();
        }

        if (!isset($data['receiver'])) {
          throw new InvalidArgumentException("Debes agregar al menos un destinatario");
        }
        if($idSubaccount != "420" || $idSubaccount != 420){
            if ($data['target'] > $amount) {
              throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms si nesesitas más saldo contacta al administrador");
            }
        }
        $smsform->bind($data, $sms);
        if (!$smsform->isValid()) {
          foreach ($smsform->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $sms->target = $data['target'];
        $sms->idSmsCategory = $data['idSmsCategory'];
        if ($data['morecaracter'] == true) {
            if (mb_strlen(trim($data['message']), 'UTF-8') > 300) {
                throw new \InvalidArgumentException("El campo contenido debe tener máximo 300 caracteres!");
            }
        } else {
            if (mb_strlen(trim($data['message']), 'UTF-8') > 160) {
                throw new \InvalidArgumentException("El campo contenido debe tener máximo 160 caracteres");
            }
        }
                // se pone el estado en borrador para luego enviar confirmacion
        //$sms->status = $this->statusSms->scheduled;
        $sms->status = 'draft';
        if($data['morecaracter'] == true){
        $sms->morecaracter = 1;
        }else{
        $sms->morecaracter = 0;    
        }        
        
        $sms->type = $this->typeSms->contact;
        $sms->startdate = $dateStart;
        $sms->receiver = json_encode($data['receiver']);
        $sms->message = $data['message'];

        if ($data['switchrepeated']) {
          $sms->singleSendContact = 1;
        } else {
          $sms->singleSendContact = 0;
        }
        
        $sms->sendpush = $data['sendpush'];
        
        if($sms->sendpush == 1 && $sms->morecaracter == 1){
           throw new \InvalidArgumentException("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.");  
        }
        $this->db->begin();
        (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

        if (!$sms->save()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }



        //proceso para validar el envio de sms por contacto
        $SmsFailedDelete = SmsFailed::find(array(
                    "conditions" => "idSms = ?0",
                    "bind" => array(0 => (int) $idSms)
        ));
        if (!$SmsFailedDelete->delete()) {
          foreach ($SmsFailedDelete->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }

        $arrIdContactlist = array();
        $arrIdContact = array();
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        //si el envío es unico por contacto
        if ($sms->singleSendContact == 1) {
          //Si es de tipo contactlist
          if (strpos($sms->receiver, 'contactlists') !== false) {
            //Extrae todos los id de Contactlist del envío
            foreach ($data['receiver']['contactlists'] as $key) {
              $arrIdContactlist[] = (int) $key['idContactlist'];
            }
            //Genera un string separado por coma con los ids de contactlist
            $commaSeparatedIdContact = implode(",", $arrIdContactlist);
            /*Busca todos los contactos de esas listas en cxcl y se agrupa por idContact, ya que es equivalente a un DISTINCT */
            $cxcl = \Cxcl::find(array(
                        "conditions" => "idContactlist IN ($commaSeparatedIdContact) "
                        . "and deleted = 0 "
                        . "AND unsubscribed = 0 "
                        . "AND status='active'",
                        "group" => "idContact"
                    ))->toArray();
            //Se extraen los id Contact de la consulta anterior
            foreach ($cxcl as $val) {
              $arrIdContact[] = (int) $val["idContact"];
            }
            //Se genera los parametros a consultar en coleccion Contact
            $conditions = array(
                  'idContact' => array('$in' =>$arrIdContact),
                  'phone' => array('$ne' => ""),
                  'blockedPhone' => array('$in' => array("", null, "null"))
              );
            
            $command = new \MongoDB\Driver\Command([
              'aggregate' => 'contact',
              'pipeline' => [
                  ['$match' => $conditions],
                  ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
                ]
              ]);
            $contact = $manager->executeCommand('aio', $command)->toArray();

            if ($contact) {
              $contact = $contact[0]->result;
              $this->logger->log(print_r(count($contact),true));
              foreach ($contact as $c){
                $c = $c->data;
                $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
              }
            }
          } else {
            $arrIdContactFromSegment = array();
            foreach ($data['receiver']['segment'] as $key) {
              $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);
              if ($sxcs) {
                foreach ($sxcs as $sxc) {
                  $arrIdContactFromSegment[] = (int) $sxc->idContact;
                }
              }
            }
            $contactConditions = array(
                'idContact' => array('$in' =>$arrIdContactFromSegment),
                'phone' => array('$ne' => ""),
                'blockedPhone' => array('$in' => array("", null, "null"))
            );
            
            $command = new \MongoDB\Driver\Command([
              'aggregate' => 'contact',
              'pipeline' => [
                  ['$match' => $contactConditions],
                  ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
                ]
              ]);
            
            $contactsFromSegment = $manager->executeCommand('aio', $command)->toArray();
            
            if ($contactsFromSegment) {
              $contactsFromSegment = $contactsFromSegment[0]->result;
              foreach ($contactsFromSegment as $c){
                $c = $c->data;
                $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
              }
            } 
          }
        }else{
          if (strpos($sms->receiver, 'contactlists') !== false) {
            //Extrae todos los id de Contactlist del envío
            foreach ($data['receiver']['contactlists'] as $key) {
              $arrIdContactlist[] = (int) $key['idContactlist'];
            }
            //Genera un string separado por coma con los ids de contactlist
            $commaSeparatedIdContact = implode(",", $arrIdContactlist);
            /*Busca todos los contactos de esas listas en cxcl y
              se agrupa por idContact, ya que es equivalente a un DISTINCT */
            $cxcl = \Cxcl::find(array(
                        "conditions" => "idContactlist IN ($commaSeparatedIdContact) "
                        . "and deleted = 0 "
                        . "AND unsubscribed = 0 "
                        . "AND status='active'"
                        //"group" => "idContact"
                    ))->toArray();
            //Se extraen los id Contact de la consulta anterior
            foreach ($cxcl as $val) {
              $arrIdContact[] = (int) $val["idContact"];
            }

            //var_dump((count($arrIdContact)));exit;

            foreach ($arrIdContact as $value) {
            //Se genera los parametros a consultar en coleccion Contact
             $conditions = array(
                  'idContact' => $value,
                  'phone' => array('$ne' => ""),
                  'blockedPhone' => array('$in' => array("", null, "null"))
              );
             //Se busca en la coleccion de Contact teniendo en cuenta los numeros repetidos
             $contact = \Contact::find(array($conditions));
             //Si hay resultados, aplique la validacion
             if($contact){ 
              foreach($contact as $c){
                $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
               }
             }
           }
          }else{
            $arrIdContactFromSegment = array();
            foreach ($data['receiver']['segment'] as $key) {
              $sxcs = \Sxc::findFirst([["idSegment" => $key->idSegment]]);
              if ($sxcs) {
                foreach ($sxcs as $sxc) {
                  $arrIdContactFromSegment[] = (int) $sxc->idContact;
                }
              }
            }
            foreach ($arrIdContactFromSegment as $value) {
              $contactConditions = array(
                'idContact' => $value,
                'phone' => array('$ne' => ""),
                'blockedPhone' => array('$in' => array("", null, "null"))
              );
              $contactsFromSegment = \Contact::findFirst(array($contactConditions));
              if($contactsFromSegment){ 
                foreach($contactsFromSegment as $c){
                  $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative, $data['switchrepeated']);
                }
              }
            }
          }  
        }

        unset($cxcl);
        unset($conditions);
        unset($command);

        $this->db->commit();

        // Cuenta los erroneos en sms_failed
        $SmsFailed = SmsFailed::count(["conditions" => "idSms = ?0", 
                                       "bind" => [0 => (int) $sms->idSms]]);
        //Resta los registros erroneos del target de envío
        $sms->target = $sms->target - $SmsFailed;

        if (!$sms->save()) {
          foreach ($sms->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se cambio el target error");
        }

        $this->session->set("msgsuccesssmscontactedit", true);
        return $this->set_json_response(["Envios" => $sms->target, "Invalidos" => $SmsFailed, "IdSms" => $sms->idSms], 200, "OK");
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response([$msg->getMessage()], 409, "FAIL");
      } catch (Exception $e) {
        $this->logger->log("Exception while creating account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
    }
  }

  public function smscancelAction($idSms) {
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      $this->notification->error("No se encontró el sms, por favor valida la información");
      return $this->response->redirect("sms/");
    }
    try {
      $this->db->begin();

      $sms->status = 'canceled';
      if (!$sms->update()) {
        $this->db->rollback();
        foreach ($sms->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      $this->db->commit();
      $this->notification->warning("Se ha cancelado con exito el envio de sms");
      return $this->response->redirect("sms/");
    } catch (ErrorException $e) {
      $this->notification->error($e->getMessage());
      return $this->response->redirect("sms/");
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function showcontactAction($idSms) {
    $sms = Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      $this->notification->error("No se encontró el sms, por favor valida la información");
      return $this->response->redirect("sms/");
    }
    $this->view->setVar("sms", $sms);
  }

  public function validatePhoneSmsFailed($sms, $idcontact, $phone, $country, $repeated) {
    $phoneactual = $phone;
    $savesmsfail = 0;
    $messagefail = "";



    $phone = str_replace(' ', '', $phone);

    /* if ($repeated == true) {
      // valida si el numero se encuentra repetido en la lista
      $repeatedphone = $this->repeatednumbercontact($phone);
      if ($repeatedphone == true) {
      $messagefail = $messagefail . "El número se encuentra repetido.";
      $savesmsfail = 1;
      }
      } */

    // valida si el indicativo que ingresa es correcto con el pais
    $Country = Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $country]]);
    if (!$Country) {
      $messagefail = "No se encuentra el indicativo del pais.";
      $savesmsfail = 1;
    }

    //valida solo numeros del celular ingresado
    /* if (ctype_digit($phone)) {
      // valido para numeros
      } else {
      $messagefail = $messagefail . "Existe un número que contiene letras.";
      $savesmsfail = 1;
      } */

    // valida que el numero de digitos sea correcto
    /* $valor = strlen($phone);
      if ($valor != 10) {
      $messagefail = $messagefail . "La cantidad de dígitos del número es incorrecto.";
      $savesmsfail = 1;
      } */

    // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
    $phoneindi = substr($phone, 0, 3);
    $PhonePrefix = PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phoneindi]]);
    if (!$PhonePrefix) {
      $messagefail = $messagefail . "Verifique que el número sea valido, de acuerdo al indicativo del país.";
      $savesmsfail = 1;
    }

    if ($savesmsfail == 1) {

      $smsfailed = new SmsFailed();

      $smsfailed->idSms = $sms->idSms;
      $smsfailed->idContact = (int) $idcontact;
      $smsfailed->indicative = $country;
      $smsfailed->message = $sms->message;
      $smsfailed->phone = $phoneactual;
      $smsfailed->count = 1;
      $smsfailed->detail = $messagefail;
      $smsfailed->type = "contact";


      if (!$smsfailed->save()) {
        foreach ($smsfailed->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
        $this->trace("fail", "error al guardar en smsfailed");
      }
    }
    unset($PhonePrefix);
    unset($Country);
  }

  public function repeatednumbercontact($phone) {
    $valuerepeated;

    if (in_array($phone, $this->repeatednumber)) {
      $valuerepeated = true;
    } else {
      $valuerepeated = false;
      //array_push($this->repeatednumber, $phone);
      $this->repeatednumber[] = $phone;
    }

    return $valuerepeated;
  }

  public function validatecontactAction($idSms) {
    // pone en programado cuando ya confirma el sms para enviar
    $Sms = Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => (int) $idSms]]);
    $Sms->status = "scheduled";

    if (!$Sms->save()) {
      foreach ($Sms->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se cambio en el status error");
    }
  }

  public function translateStatusSms($status) {
    $statusSpanish = "";
    switch ($status) {
      case "sent":
        $statusSpanish = "Enviado";
        break;
      case "draft":
        $statusSpanish = "Borrador";
        break;
      case "sending":
        $statusSpanish = "En proceso de Envío";
        break;
      case "scheduled":
        $statusSpanish = "Programado";
        break;
      case "canceled":
        $statusSpanish = "Cancelado";
        break;
      case "paused":
        $statusSpanish = "Pausado";
        break;
    }
    return $statusSpanish;
  }
  
  public function validateBalance(){
    $date = date('Y-m-d h:i:s');
    $smsFindPending = \Smslote::query()
      ->columns(['Sms.idSms, SUM(Smslote.messageCount) AS target'])
      ->leftJoin('Sms','Sms.idSms = Smslote.idSms')
      ->where("Sms.idSubaccount = {$this->user->Usertype->subaccount->idSubaccount} AND Sms.status = 'scheduled' AND Sms.startdate >= '{$date}' ")
      ->execute();
    
    $balanceConsumedFind = \Saxs::find(array(
      'conditions' => 'idSubaccount = ?0 and idServices = ?1 and status=1',
      'bind' => array(
          0 => $this->user->Usertype->subaccount->idSubaccount,
          1 => 1
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

    $answer = ['smsFindPending'=>$smsFindPending->toArray(), 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];
    return $answer;
  }
  
  public function sendsmsnotsmsbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == 'sending') {
        $amount = $data->totalAvailable;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->user->Usertype->Subaccount->name;
        $accountName = $this->user->Usertype->Subaccount->Account->name;
        $this->arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName
        );
      }
    }
    $sendMailNot= new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
    //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de SMS
    $sendMailNot->sendSmsNotification($this->arraySaxs);
    return true;
  }

}
