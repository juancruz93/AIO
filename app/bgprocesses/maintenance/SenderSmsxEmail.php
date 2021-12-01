<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$smsxemail = new SenderSmsxEmail();
$smsxemail->index();

class SenderSmsxEmail {

  public $arrayinfobitAnswerChaged;
  public $arrayDataBlockedPhone = [];

  public function __construct() {
    $this->arrayinfobitAnswerCharged = \Phalcon\DI::getDefault()->get('infobitAnswersCharged')->toArray();
  }

  public function index() {
    //$hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
    //$testname = 'CODERE';
    //$username = 'emailtosms@sigmamovil.com';
    //$password = 'emsms2017+';
    
    $hostname = '{us2.imap.mailhostbox.com:993/imap/ssl}INBOX';
    $testname = 'CODERE';
    $username = 'emailtosms1@sigmamovil.com.co';
    $password = '(Zu@$jS9';

//   $hostname = '{imap-mail.outlook.com:993/imap/ssl}pruebaCodere';
//    $testname = 'INBOX';
//    $username = 'garfel94@hotmail.com';
//    $password = 'felipaOSABROSA';
    
    $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
    $mails = imap_search($inbox, "ALL");
    if (!is_array($mails)) {
      return;
    }
    $reverse = array_reverse($mails);
    foreach ($reverse as $value) {
      $overview = imap_fetch_overview($inbox, $value, 0);
      //Este el id donde quedo la ultima modificacion
      $imapbody = imap_fetchbody($inbox, $value, "1");
      
      if (count($overview) == 0) {
        continue;
      }
      if (!$imapbody) {
        continue;
      }
      if (imap_base64($imapbody)) {
        $base = imap_base64($imapbody);
        $htmlObj = new DOMDocument();
        $htmlObj->loadHTML($base);
        $content = $htmlObj->textContent;
        $count = strpos($content,"57;");
        $substr = substr($content, $count);
        $password = substr($content, 0, $count);
        //$password = substr($content, 0, 32);
        $receiver = explode(";", trim($substr));
        $clave = trim($password);
      } else {
        $content = imap_qprint($imapbody);
        $count = strpos($content,"57;");
        $substr = substr($content, $count);
        //$substr = substr($content, 32);
        $password = explode("\n", imap_fetchbody($inbox, $value, "1"));
        $receiver = explode(";", trim($substr));
        $clave = trim($password[0]);
      }
      
      $i = 0;
      $dividir = intval(count($receiver) / 3);
      $body = array();
      $text = " ";
      $target = 0;
      for ($f = 0; $f < $dividir; $f++) {
        for ($c = 0; $c < 3; $c++) {
          $body[$f][$c] = $receiver[$i];
          if ($dividir < count($body)) {
            break;
          }
          $i++;
        }
        $target++;
        $text .= implode(";", $body[$f]) . '&&';
      }
      $menor = explode("<", $overview[0]->from);
      if (filter_var($menor[0], FILTER_VALIDATE_EMAIL)) {
        $correo = $menor[0];
      } else {
        $mayor = explode(">", $menor[1]);
        $correo = $mayor[0];
      }

      $modelSmsxemail = \Smsxemail::findFirst(array("conditions" => "generateKey = ?0 ", "bind" => array($clave)));
      if ($modelSmsxemail) {
        $report = \ReportSmsxemail::findFirst(array("conditions" => "idEmail = ?0", "bind" => array($overview[0]->message_id)));
        if (!$report) {
          $data = ['id' => $overview[0]->message_id, 'email' => $modelSmsxemail->senderEmail, 'clave' => $modelSmsxemail->generateKey, 'idSubaccount' => $modelSmsxemail->idSubaccount, 'idSmsCategory' => $modelSmsxemail->idSmsCategory, 'notificationEmail' => $modelSmsxemail->notificationEmail, 'name' => imap_utf8($overview[0]->subject), 'target' => $target, 'receiver' => str_replace("\r\n", " ", trim($text))];
          if (!$modelSmsxemail) {
            throw new \InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
          }
          if ($correo == $modelSmsxemail->senderEmail && $clave == $modelSmsxemail->generateKey) {
            //$this->findSmsxemail($data);
            //Convierto cadena $data["receiver"] en array
            $explode = explode("&&", trim($data['receiver']));
            /* Al array resultante aplico funcion array_filter
              para eliminar posiciones del array en NULL */
            $receiver = array_filter($explode);
            $dataExploded = array();
            //Convierto cadena del array en posicion cero en otro array de tres posiciones
            //para extraer en posiciones indicativo, mensaje y destinatario
            $dataExploded = explode(";", $receiver[0]);
            $newData = array();
            $newData["idEmail"] = $data['id'];
            $newData["name"] = $data['name'];
            $newData["idSubaccount"] = $data['idSubaccount'];
            $newData["idSmsCategory"] = $data['idSmsCategory'];
            $newData["receiver"]["indicative"] = $dataExploded[0];
            $newData["receiver"]["phone"] = $dataExploded[1];
            $message = str_replace("\u00a0", " ", json_encode($dataExploded[2]));
            $newData["receiver"]["message"] = json_decode($message);
            $newData["notificationEmail"] = $data['notificationEmail'];
            $return = $this->createSingleSms($newData);
            imap_mail_move($inbox, $value, $testname);
            imap_setflag_full($inbox, $value, "\\Seen \\Flagged");
          }
        } else {
          //\Phalcon\DI::getDefault()->get('logger')->log("BREAK report****2013***** ");
          break;
        }
      } else {
        \Phalcon\DI::getDefault()->get('logger')->log("BREAK modelSmsxemail****2013******* ");
        break;
      }
    }
    imap_expunge($inbox);
    imap_close($inbox);
  }

  /*public function findSmsxemail($data) {
    $amount = 0;
    $saxs = \Saxs::find(array("conditions" => "idSubaccount = ?0 ", "bind" => array($data['idSubaccount'])));
    foreach ($saxs as $key) {
      if ($key->idServices == 1) {
        $flag = true;
        $amount = $key->amount;
      }
    }

    $subaccount = \Subaccount::findFirst(array("conditions" => "idSubaccount = ?0 ", "bind" => array($data['idSubaccount'])));
    $account = \Account::findFirst(array("conditions" => "idAccount = ?0 ", "bind" => array($subaccount->idAccount)));
    $sms = new \Sms();
    $data["datesend"] = date('Y-m-d G:i:s', time());
    $explode = explode("&&", trim($data['receiver']));
    $receiver = array_filter($explode);
    if (empty($receiver[0])) {
      throw new \InvalidArgumentException("Debes agregar al menos un destinatario");
    }
    if (count($receiver) > $amount) {
      throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envío(s) de sms. Si nesesitas más saldo contacta al administrador");
    }
    $sms->name = $data["name"];
    $sms->target = count($receiver);
    $sms->idSmsCategory = $data["idSmsCategory"];
    $sms->idSubaccount = $data["idSubaccount"];
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = "lote";
    $sms->startdate = $data["datesend"];
    $sms->sent = count($receiver);
    $sms->notification = count($data["notificationEmail"]);
    $sms->email = $data["notificationEmail"];
    $sms->receiver = str_replace("&&", " ", trim($data['receiver']));
    $sms->createdBy = $account->email;
    $sms->updatedBy = $account->email;
    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $array = array();
    $count = 0;
    foreach ($receiver as $key => $value) {
      $arr = explode(";", trim($value));
      //validar \u00a0 reemplazandolo por el espacio
      $messages = str_replace("\u00a0", " ", json_encode($arr[2]));
      $flag = true;
      if (strstr($arr[0], "+")) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el indicativo solo debe contener números'];
        $flag = false;
      }
      if (!is_numeric($arr[0])) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el indicativo solo debe contener números'];
        $flag = false;
      }
      if (strlen(trim($arr[1])) != 10) {
        $arr[1] = substr($arr[1], 0, 10);
      }
      if (trim($arr[0]) == 57 && strlen(trim($arr[1])) != 10 || !is_numeric($arr[1])) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el movil solo debe contener números'];
        $flag = false;
      }
      if (count($arr) == 2 && strlen(trim($arr[2])) > 160) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que al realizar un envió con varios destinatarios, cada destinatario al final de cada mensaje, debe contener  un ; '];
        $flag = false;
      }
      if (strlen(trim($arr[2])) > 160) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el contenido del mesaje solo debe contener 160 carateres'];
        $flag = false;
      }
      if (preg_match("/[ñÑáéíóúÁÉÍÓÚ¿¡´]/", $arr[2])) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el contenido del mensaje no debe contener ninguno de estos caracteres: ñ Ñ ¡ ¿ á é í ó ú Á É Í Ó Ú ´ '];
        $flag = false;
      }
      if (preg_match("/%%+[a-z0-9_]+%%/", $arr[2])) {
        $array[$key] = ['indicative' => $arr[0], 'phone' => $arr[1], 'message' => trim($messages), 'status' => 'undelivered', 'smsFailed' => 'Recuerde que el contenido del mensaje no debe contener ninguno de estos caracteres: a-z0-9_ '];
        $flag = false;
      }
      if (count($arr) == 3 && $flag) {
        $smslote = new \Smslote();
        $smslote->idSms = $sms->idSms;
        $smslote->indicative = $arr[0];
        $smslote->phone = trim($arr[1]);
        $smslote->message = json_decode($messages);
        $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
        $smslote->createdBy = $account->email;
        $smslote->updatedBy = $account->email;

        if (!$smslote->save()) {
          \Phalcon\DI::getDefault()->get("db")->rollback();
          foreach ($smslote->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $count++;
      } else {
        $sms->status = "undelivered";
        if (!$sms->save()) {
          \Phalcon\DI::getDefault()->get("db")->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
    $reportsms = new \ReportSmsxemail();
    $reportsms->idEmail = $data['id'];
    $reportsms->idSubaccount = $data['idSubaccount'];
    $reportsms->idSms = $sms->idSms;
    $reportsms->smsFailed = json_encode($array);
    if (!$reportsms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($reportsms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return ['id' => $data['id'], 'sms' => $sms->idSms];
  }*/

  public function createSingleSms($data) {
    $saxs = null;
    $saxs1 = \Saxs::find(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array($data["idSubaccount"])
    ));

    $subaccount = \Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0 ",
                "bind" => array($data['idSubaccount']))
    );
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0 ",
                "bind" => array($subaccount->idAccount))
    );

    foreach ($saxs1 as $key) {
      if ($key->Services->name == "Sms") {
        $saxs = $key;
      }
    }
    if (!isset($saxs)) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($saxs->amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más SMS");
    }
    $sms = new \Sms();
    //$smsform = new SmsForm();
    //$smsform->bind($data, $sms);
    $sms->idSmsCategory = $data["idSmsCategory"];
    $sms->name = $data["name"];
    $sms->idSubaccount = $data["idSubaccount"];
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = "single";
    $sms->startdate = date('Y-m-d G:i:s', time());
    $sms->dateNow = 1;
    $sms->sent = 0;
    $sms->total = 1;
    $sms->target = 1;
    $sms->externalApi = 1;
    $sms->notification = count($data["notificationEmail"]);
    $sms->email = $data["notificationEmail"];
    $sms->createdBy = $account->email;
    $sms->updatedBy = $account->email;

    if (!isset($data["receiver"])) {
      throw new \InvalidArgumentException("Debe haber un destinatario para el envío");
    }
    $sms->receiver = json_encode($data["receiver"]);

    if (/* !$smsform->valid() && */!$sms->save()) {
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $arraySmsFailed = array();
    $smslote->idSms = $sms->idSms;
    $flag = true;
    $contador = 0;
    if (strstr($data["receiver"]["indicative"], "+") || !is_numeric($data["receiver"]["indicative"])) {
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el indicativo solo debe contener números'
      ];
      $flag = false;
      $contador++;
    }

    if (trim($data["receiver"]["indicative"]) == 57 && strlen(trim($data["receiver"]["phone"])) != 10 || !is_numeric($data["receiver"]["phone"])) {
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el movil solo debe contener números'
      ];
      $flag = false;
      $contador++;
    }

    if (preg_match("/[ñÑáéíóúÁÉÍÓÚ¿¡´]/", $data["receiver"]["message"])) {
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el contenido del mensaje no debe contener ninguno de estos caracteres: ñ Ñ ¡ ¿ á é í ó ú Á É Í Ó Ú ´ '
      ];
      $flag = false;
      $contador++;
    }

    if (preg_match("/%%+[a-z0-9_]+%%/", $data["receiver"]["message"])) {
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el contenido del mensaje no debe contener ninguno de estos caracteres:a-z0-9_'
      ];
      $flag = false;
      $contador++;
    }

    if (strlen(trim(trim($data["receiver"]["message"]))) > 160) {
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el contenido del mesaje solo debe contener 160 carateres'
      ];
      $flag = false;
      $contador++;
    }
    
    if(in_array($data["receiver"]["phone"],$this->arrayDataBlockedPhone)){
      $arraySmsFailed[$contador] = [
          'indicative' => $data["receiver"]["indicative"],
          'phone' => $data["receiver"]["phone"],
          'message' => trim($data["receiver"]["message"]),
          'status' => 'undelivered',
          'smsFailed' => 'Recuerde que el contenido del mesaje solo debe contener 160 carateres'
      ];
      $flag = false;
      $contador++;
    }

    if ($flag) {
      $smslote = new \Smslote();
      $smslote->idSms = $sms->idSms;
      $smslote->idAdapter = 3;
      $smslote->indicative = $data["receiver"]["indicative"];
      $smslote->phone = trim($data["receiver"]["phone"]);
      $smslote->message = trim($data["receiver"]["message"]);
      $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
      if (!$smslote->save()) {
        foreach ($smslote->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      /*if($sms->idSubaccount == 2013 || $sms->idSubaccount == "2013"){
        $response = "PENDING_ENROUTE";
        $smslote->response =  "PENDING_ENROUTE";
      }else{*/
        $response = $this->sendSingleMessage($this->validReceiver($smslote));
        $smslote->response = $response->messages[0]->status->name;
      //}
      
      $smslote->createdBy = $account->email;
      $smslote->updatedBy = $account->email;

      //if ($smslote->response == "PENDING_ENROUTE") {
      if (in_array($smslote->response, $this->arrayinfobitAnswerCharged)) {
        $smslote->messageCount = 1;
        $smslote->status = "sent";
        $sms->sent = 1;
        $saxs->amount = (Int) $saxs->amount - 1;
      } else {
        $smslote->status = "undelivered";
        $smslote->messageCount = 0;
        $saxs->amount = (Int) $saxs->amount;
      }

      $sms->status = "sent";
      $sms->update();
      $smslote->save();
      $saxs->save();
      
      if($sms->email){
        $sendMailNot= new \Sigmamovil\General\Misc\SmsEmailNotification();
        $sendMailNot->sendMailNotification($sms);
      }
    } else {
      $sms->status = "canceled";
      if (!$sms->save()) {
        foreach ($sms->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $reportsms = new \ReportSmsxemail();
      $reportsms->idEmail = $data['idEmail'];
      $reportsms->idSubaccount = $data['idSubaccount'];
      $reportsms->idSms = $sms->idSms;
      $reportsms->smsFailed = json_encode($arraySmsFailed);
      if (!$reportsms->save()) {
        \Phalcon\DI::getDefault()->get("db")->rollback();
        foreach ($reportsms->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

    }

    return array(
        "idSms" => $sms->idSms,
        "messageId" => $smslote->idSmslote,
        "to" => "{$smslote->indicative}{$smslote->phone}",
        "status" => $smslote->status,
        "smsCount" => $sms->sent
    );
  }

  private function validReceiver($receiver) {
    if (strlen($receiver->message) > 160) {
      throw new \InvalidArgumentException("El mensaje excede los 160 caracteres");
    }
    return array(
        "from" => "AIO-SMS",
        "to" => "{$receiver->indicative}{$receiver->phone}",
        "text" => $receiver->message
    );
  }

  private function sendSingleMessage($receiver) {
    $adapter = \Adapter::findFirst(array(
                "conditions" => "fname = ?0",
                "bind" => array("INFOBIP(SINGLESMS)")
    ));

    $apiSms = new \Sigmamovil\General\Misc\ApisSms("S1gm4M0v1l");
    return $apiSms->apiInfobip($receiver, $adapter);
  }
  
  public function findBlockedPhone($idAccount){
    $blocked = Blocked::find([array(
      "idAccount" => (int) $idAccount,
      "deleted" => 0
    )]);
    if($blocked != false){
      foreach ($blocked as $value){
        if(!in_array($value->phone, $this->arrayDataBlockedPhone)){
          $this->arrayDataBlockedPhone[] = (string) $value->phone;
        }
      }
    }
    unset($blocked);
    return $this->arrayDataBlockedPhone;
  }

}
