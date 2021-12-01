<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../smstwoway/SmsScript.php");
require_once(__DIR__ . "/../sender/PrepareSmsSendingRule.php");

class SmsSender extends SmsScript {

  public function __construct() {
    parent::__construct();
//  $this->elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('https://testtrack.sigmamovil.com/'));
    $this->elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
  }

  public function verifyExist($id) {
    try {
      \Phalcon\DI::getDefault()->get('logger')->log("Inicio del Envío " . date("Y-m-d H:i:s", time()));
      $smsTwoWay = \Smstwoway::findFirst(array(
                  "conditions" => "idSmsTwoway = ?0",
                  "bind" => array(0 => $id)
      ));
      if (!$smsTwoWay) {
        throw new Sigmamovil\General\Exceptions\ValidateExistException("El sms doble via que intenta de enviar no se encuentra registrado, por favor validar.");
      }

      if (($smsTwoWay->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) or ( $smsTwoWay->status == \Phalcon\DI::getDefault()->get('statusSms')->paused)) {

        $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;
        $smsTwoWay->save();
        
        $this->refreshViewSmsTwoway($smsTwoWay);
        //ENVIAR A NODE
        
        $smsLoteTwoway = Smslotetwoway::find(array(
                    "conditions" => "idSmsTwoway = ?0",
                    "bind" => array($smsTwoWay->idSmsTwoway)
        ));

        foreach ($smsTwoWay->Subaccount->Account->accountConfig->detailConfig as $key) {
          if ($key->idServices == 7 && $key->status ==1) {
            $detailConfig = $key;
            $speed = $this->speedSms($key->speed);
          }
        }

        foreach ($smsTwoWay->Subaccount->Saxs as $key) {
          if ($key->idServices == 7 && $key->status ==1) {
            $saxs = $key;
          }
        }

        $count = 0;
        $total = 0;
        $totaluserresponse = 0;

        if ($smsTwoWay->divide == 0) {
          foreach ($smsLoteTwoway as $send) {
            if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              if ($saxs->amount <= $count) {
                \Phalcon\DI::getDefault()->get('logger')->log("No tienes envios suficientes");
                $this->sendMailNotificationFailure($smsTwoWay);
                return "No tienes envios suficientes";
              }
              
              if ($this->sanitizeMessage($smsTwoWay->type)) {
                $sanitize = new \Sigmamovil\General\Misc\SanitizeString($send->message);
                $sanitize->strTrim();
                $sanitize->sanitizeAccents();
                $sanitize->sanitizeSpecialsSms();
                $sanitize->nonPrintable();
                $send->message = $sanitize->getString();
              }
              
              $response = $this->sendSms($this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way), $send, $smsTwoWay);
              $send->response = $response->messages[0]->status->name;
              $send->status = "undelivered";
              //if ($send->response == "PENDING_ENROUTE") {
              if (in_array($send->response, $this->arrayinfobitAnswerCharged)) {
                $send->messageId = $response->messages[0]->messageId;
                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $send->idAdapter = $this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way)->idAdapter;
                $send->updatedBy = "desarrollo@sigmamovil.com";
                $send->messageId = $response->messages[0]->messageId;
                $totaluserresponse = $totaluserresponse + $send->totalUserResponse;
                $count++;
              }
              $send->save();
              
              $this->refreshViewSmsTwoway($send);
            }
            $total++;
          }
          $smsTwoWay->sent = $count;
          $smsTwoWay->total = $total;
        } else if ($smsTwoWay->divide == 1) {
          $quantity = 0;
          foreach ($smsLoteTwoway as $send) {
            if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              if ($saxs->amount <= $count) {
                \Phalcon\DI::getDefault()->get('logger')->log("No tienes envios suficientes");
                $this->sendMailNotificationFailure($smsTwoWay);
                return "No tienes envios suficientes";
              }
              
              if ($this->sanitizeMessage($smsTwoWay->type)) {
                $sanitize = new \Sigmamovil\General\Misc\SanitizeString($send->message);
                $sanitize->strTrim();
                $sanitize->sanitizeAccents();
                $sanitize->sanitizeSpecialsSms();
                $send->message = $sanitize->getString();
              }

              $response = $this->sendSms($this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way), $send, $smsTwoWay);
              $send->response = $response->messages[0]->status->name;
              $send->status = "undelivered";
              //if ($send->response == "PENDING_ENROUTE") {
              if (in_array($send->response, $this->arrayinfobitAnswerCharged)) {
                $send->messageId = $response->messages[0]->messageId;
                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $send->idAdapter = $this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way)->idAdapter;
                $send->messageId = $response->messages[0]->messageId;
                $send->updatedBy = "desarrollo@sigmamovil.com";
                $totaluserresponse = $totaluserresponse + $send->totalUserResponse;
                $count++;
              }
              $send->save();
              $this->refreshViewSmsTwoway($send);
              $quantity++;
              $total++;
              if ($quantity == $smsTwoWay->quantity) {
                break;
              }
            }
          }
          $smsTwoWay->sent += $count;
          $smsTwoWay->total += $total;
        }

                $smsLoteTwoway = Smslotetwoway::find(array(
                            "conditions" => "idSmsTwoway = ?0 AND status = 'scheduled'",
                            "bind" => array($smsTwoWay->idSmsTwoway)
                ));
                $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                if (count($smsLoteTwoway) > 0) {
                    $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;
                }
                
//                if ($smsTwoWay->status == \Phalcon\DI::getDefault()->get('statusSms')->sending) {
//                    $more = "+ " . $smsTwoWay->sendingTime . " " . $smsTwoWay->timeFormat;
//                    $smsTwoWay->startdate = date("Y-m-d G:i:s", strtotime($more, strtotime($smsTwoWays->startdate)));
//                } 

        if ($smsTwoWay->notification == 1) {
          $this->sendMailNotification($smsTwoWay);
        }

        $smsTwoWay->save();
        $this->recalculateSaxsBySms($smsTwoWay->idSubaccount, $totaluserresponse);
//                $activitylog = new \Sigmamovil\General\Misc\ActivityLogMisc();
//                $user = \User::findFirst(array(
//                            "conditions" => "email = ?0",
//                            "bind" => array($smsTwoWay->createdBy)
//                ));
//                $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$count} mensajes de SMS DOBLE VIA el dia " .
//                        date("Y-m-d H:i:s", time()) .
//                        ", con id de envío {$smsTwoWay->idSmsTwoway}";
//                $activitylog->saveActivityLog($user, \Phalcon\DI::getDefault()->get('services')->sms, ($count * -1), $desc);
//                \Phalcon\DI::getDefault()->get('logger')->log("Se modificaron los estados");
//                \Phalcon\DI::getDefault()->get('logger')->log("Final del envío " . date("Y-m-d H:i:s", time()));
        return "Se modificaron los estados";
      } else {
        \Phalcon\DI::getDefault()->get('logger')->log("El estado del envio es enviado");
        return "El estado del envio es enviado";
      }
    } catch (\ValidateExistException $ex) {
      $this->registLog($ex);
    } catch (\InvalidArgumentException $ex) {
      $this->recalculateSaxsBySms($smsTwoWay->idSubaccount, $totaluserresponse);
      $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->saveSms($smsTwoWay);
      $this->sendMessageNode($smsTwoWay);
      $this->registLog($ex);
    } catch (Exception $ex) {
      $this->recalculateSaxsBySms($smsTwoWay->idSubaccount, $totaluserresponse);
      $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->saveSms($smsTwoWay);
      $this->sendMessageNode($smsTwoWay);
      $this->registLog($ex);
    }
  }

  public function sendMailNotification($smsTwoWay) {
    try {
      $idAllied = $smsTwoWay->Subaccount->Account->idAllied;
      $mail = Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and idAllied = ?1',
                  'bind' => array(0 => 'smstwoway-finished', 1 => $idAllied)
      ));
      $data = new stdClass();
      if ($mail) {
        $data->fromName = $mail->fromName;
        $data->fromEmail = $mail->fromEmail;
        $data->from = array($mail->fromEmail => $mail->fromName);
        $data->subject = $mail->subject;

        $mail->content = str_replace("%NAME_SENT%", $smsTwoWay->name, $mail->content);
        $mail->content = str_replace("%DATETIME_SENT%", $smsTwoWay->startdate, $mail->content);
        $mail->content = str_replace("%TOTAL_SENT%", $smsTwoWay->sent, $mail->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($mail->content));
        $content = $editorObj->render();

        $data->html = str_replace("tmp-url", $url, $content);
        $data->plainText = $mail->plainText;
      } else {
        $data->fromEmail = (isset($smsTwoWay->Subaccount->Account->Allied->email)) ? $smsTwoWay->Subaccount->Account->Allied->email : "soporte@sigmamovil.com";
        $data->fromName = (isset($smsTwoWay->Subaccount->Account->Allied->name)) ? $smsTwoWay->Subaccount->Account->Allied->name : "soporte Sigmamovil";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificación de envío de SMS doble-via";
        $target = json_decode($smsTwoWay->receiver);

        $content = '<table style="background-color: #E6E6E6; width: 100%;">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding: 20px;"><center>'
                . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody></tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p></p>'
                . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
                . 'Estimado usuario:'
                . '</span></h2>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p></p>'
                . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
                . 'Se le informa que se ha enviado el envío de SMS doble vía <b>' . $smsTwoWay->name . '</b> se realizó satisfactoriamente en la fecha <b>' . $smsTwoWay->startdate . "h</b>, con ". $smsTwoWay->sent ." mensajes enviados en total."
                . '</span></p>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

        $data->html = str_replace("tmp-url", "prueba", $content);
        $data->plainText = "Se ha enviado un SMS doble-via.";
      }
      $to = [];
      if (isset($smsTwoWay->email) && $smsTwoWay->email != "") {
        $email = explode(",", trim($smsTwoWay->email));
        foreach ($email as $key) {
          array_push($to, trim($key));
        }
      }
      $data->to = array_unique($to);


      $mtaSender = new \Sigmamovil\General\Misc\MtaSender($this->mta->address, $this->mta->port);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating masteraccount: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function sendMailNotificationFailure($smsTwoWay) {
    try {
      $idAllied = $smsTwoWay->Subaccount->Account->idAllied;
      $systemMail = Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and deleted=0 and idAllied = ?1',
                  'bind' => array(0 => 'smstwoway-finished', 1 => $idAllied)
      ));
      $data = new stdClass();

      if ($systemMail) {
        $data->fromName = $systemMail->fromName;
        $data->fromEmail = $systemMail->fromEmail;
        $data->from = array($systemMail->fromEmail => $systemMail->fromName);

        $data->subject = $systemMail->subject;
        $systemMail->content = str_replace("%NAME_SENT%", $smsTwoWay->name, $systemMail->content);
        $systemMail->content = str_replace("%DATETIME_SENT%", $smsTwoWay->startdate, $systemMail->content);
        $systemMail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLink($smsTwoWay->$idSmsTwoway, $smsTwoWay->idSubaccount, "complete"), $systemMail->content);
        $systemMail->content = str_replace("%LINK_SUMMARY_SENT%", $this->encodeLink($smsTwoWay->$idSmsTwoway, $smsTwoWay->idSubaccount, "summary"), $systemMail->content);
        $systemMail->content = str_replace("%TOTAL_SENT%", $smsTwoWay->sent, $systemMail->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($systemMail->content));
        $content = $editorObj->render();

        $data->html = str_replace("tmp-url", $url, $content);
//      $plainText = str_replace("tmp-url", $url, $mail->plainText);
        $data->plainText = $smsTwoWay->plainText;
      } else {
        $data->fromEmail = $smsTwoWay->Subaccount->Account->Allied->email;
        $data->fromName = $smsTwoWay->Subaccount->Account->Allied->name;
        $data->from = array($smsTwoWay->Subaccount->Account->Allied->email => $smsTwoWay->Subaccount->Account->Allied->name);

        $data->subject = "Notificación de envio satisfactorio de SMS doble-via";
//      $data->target = array($this->mail->createdBy);
//        $content = '<table style="background-color: #E6E6E6; width: 100%;">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="padding: 20px;"><center>'
//                . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
//                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
//                . '<tbody></tbody>'
//                . '</table>'
//                . '</td>'
//                . '</tr>'
//                . '<tr>'
//                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
//                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="padding-left: 0px; padding-right: 0px;">'
//                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
//                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
//                . '<p></p>'
//                . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
//                . 'Estimado usuario:'
//                . '</span></h2>'
//                . '</td>'
//                . '</tr>'
//                . '</tbody>'
//                . '</table>'
//                . '</td>'
//                . '</tr>'
//                . '</tbody>'
//                . '</table>'
//                . '</td>'
//                . '</tr>'
//                . '</tbody>'
//                . '</table>'
//                . '</td>'
//                . '</tr>'
//                . '<tr>'
//                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
//                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="padding-left: 0px; padding-right: 0px;">'
//                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
//                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
//                . '<tbody>'
//                . '<tr>'
//                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
//                . '<p></p>'
//                . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
//                . 'Se le informa que se ha detenido el envio de sms por que te has quedado sin saldo para seguir enviando, '
//                . ' para comprar mas sms comunicate con soporte.'
//                . '</span></p>'
//                . '</td>'
//                . '</tr>'
//                . '</tbody>'
//                . '</table>'
//                . '</td>'
//                . '</tr>'
//                . '</tbody>'
//                . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';
        $content = '<table style="background-color: #E6E6E6; width: 100%;">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding: 20px;"><center>'
                . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody></tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p></p>'
                . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
                . 'Estimado usuario:'
                . '</span></h2>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p></p>'
                . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
                . 'Se le informa que se ha enviado el envío de SMS doble via<b>' . $smsTwoWay->name . '</b> satisfactoriamente en la fecha <b>' . $smsTwoWay->startdate . "h</b>"
                . '</span></p>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

        $data->html = str_replace("tmp-url", "prueba", $content);
        $data->plainText = "Se realizado un envio de SMS doble-via.";
      }
      $email = explode(",", trim($smsTwoWay->email));
      $to = [];
      foreach ($email as $key) {
        array_push($to, trim($key));
      }
      $data->to = $to;

//    $mailSender = new \Sigmamovil\General\Misc\MailSender();
//    $mailSender->setData($data);
//    $mailSender->setHtml($html);
//    $mailSender->setPlainText($plainText);
//    $mailSender->sendBasicMail();
      //\Phalcon\DI::getDefault()->get('logger')->log(print_r($data, true));
      $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
      //return $this->set_json_response($mtaSender, 200);
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
//      $this->trace("fail", $e->getTraceAsString());
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating sendMailNotificatio: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function registLog($ex) {
    \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage() . "----" . $ex->getTraceAsString());
    \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
  }
/**
 * 
 * @param type $data
 */
  public function refreshViewSmsTwoway($data) {
    $this->elephant->initialize();
    $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms-two-way", "data" => ["idSms" => $data->idSmsTwoway, "status" => $data->status]]);
    $this->elephant->close();
  }
  
  private function sanitizeMessage($type){
    if ($type == "csv") {
      return true;
    }
  }

}

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sender = new SmsSender();

$sender->verifyExist($id);
