<?php

/**
 * User: juan.dorado
 * Date: 01/07/2016
 * Time: 12:27
 */
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sms/SmsScript.php");
require_once(__DIR__ . "/../sender/PrepareSmsSendingRule.php");

class SmsSender extends SmsScript {
  
  public $arrayDataBlockedPhone = array();

  public function __construct() {
    parent::__construct();
    $this->setTypeSend("normal");
  }

  public function verifyExist($id) {
    $this->elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
    $this->elephant->initialize();
    try {
      $sms = \Sms::findFirst(array(
                  "conditions" => "idSms = ?0",
                  "bind" => array(0 => $id)
      ));
      $idSubaccount = $sms->idSubaccount;      
      if (!$sms) {
        \Phalcon\DI::getDefault()->get('logger')->log("No existe el sms");
        return "No existe el sms";
      }

      if (($sms->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) or ( $sms->status == \Phalcon\DI::getDefault()->get('statusSms')->paused)) {

        $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;

        $sms->save();
        $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms", "data" => ["idSms" => $id, "status" => $sms->status]]);

        $smsLote = Smslote::find(array(
                    "conditions" => "idSms = ?0",
                    "bind" => array($id)
        ));
        //Llamar funcion para obtener los blocked
        $this->findBlockedPhone($sms->Subaccount->idAccount);
        
        foreach ($smsLote as $send) {
          if(in_array($send->phone, $this->arrayDataBlockedPhone)){
            $send->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
            $send->messageCount = 0;
            $send->save();
          }
        }
        unset($smsLote);
        
        $smsLote = Smslote::find(array(
                    "conditions" => "idSms = ?0 AND status != 'canceled'",
                    "bind" => array(0 => $sms->idSms)
        ));


        $this->setLastSmsSend($smsLote->getLast());

        foreach ($sms->Subaccount->Account->accountConfig->detailConfig as $key) {
          if ($key->idServices == 1 && $key->status ==1) {
            $detailConfig = $key;
            $speed = $this->speedSms($key->speed);
          }
        }

        foreach ($sms->Subaccount->Saxs as $key) {
          if ($key->idServices == 1 && $key->status ==1) {
            $saxs = $key;
          }
        }

        $preparessr = new PrepareSmsSendingRule($sms);

        $count = 0;
        $total = 0;
        
        if ($sms->divide == 0) {
          foreach ($smsLote as $send) {
            if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              if($idSubaccount != "420" && $idSubaccount != 420){
                if ($saxs->amount <= $count) {
                    \Phalcon\DI::getDefault()->get('logger')->log("tiene saldo negativo o cero, id ".$sms->idSms." ,divide ".$sms->divide);
                    throw new \InvalidArgumentException("No tienes envios suficientes");
                } 
              }

              $preparessr->setFlag(TRUE);
              $preparessr->setOneSms($send);
              $preparessr->ruleSelect();
              $preparessr->configRuleSelect();
              $preparessr->configRuleDefault($preparessr->getFlag());
              if ($this->sanitizeMessage($sms->type)) {
                $sanitize = new \Sigmamovil\General\Misc\SanitizeString($send->message);
                $sanitize->strTrim();
                $sanitize->sanitizeAccents();
                $sanitize->sanitizeSpecials();  
                $sanitize->sanitizeSpecialsSms();
                $sanitize->nonPrintable();
                $send->message = $sanitize->getString();
              }
              $response = $this->sendSms($preparessr->getConfigRuleSelected()->Adapter, $send, $sms);
              if ($response == "continue") {
                if ($this->getLastSmsSend()->idSmslote != $send->idSmslote) {
                  continue;
                }
              } elseif (is_array($response)) {
                $count += $response["amountsent"];
                $total += $response["amountsent"] + $response["amountnotsent"];
                continue;
              }
              $send->response = $response;
              $send->status = "undelivered";
              //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
              if (in_array($response, $this->arrayinfobitAnswerCharged)) {
                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $send->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                $send->updatedBy = "desarrollo@sigmamovil.com";
                $count++;
//              $saxs->amount = $saxs->amount - 1;
//              $saxs->save();
              }
              $total++;
              $send->save();
              
            }

            if ($this->getLastSmsSend()->idSmslote == $send->idSmslote) {
              if (count($this->objectBase->messages) > 0 ) {
                $adapter = Adapter::findFirst(array(
                            "conditions" => "idAdapter = ?0",
                            "bind" => array(3)//3 en producción 6 en local
                ));
              
                $res = $this->prepareSmsInfobip($this->objectBase, $adapter);
                $response = $this->updateStatusSends($res, $adapter->idAdapter);
                $count += $response["amountsent"];
                $total += $response["amountsent"] + $response["amountnotsent"];
                
                //$send->messageCount = $send->messageCount +1;
              } 
            }
          }

          $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms-send", "data" => ["idSms" => $id, "sent" => $count, "total" => 0]]);
          $sms->sent = $count;
          $sms->total = $total;
        } else if ($sms->divide == 1) {
          $quantity = 0;
          foreach ($smsLote as $send) {
            if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              if($idSubaccount != "420" && $idSubaccount != 420){
                  if ($saxs->amount <= $count) {
                    \Phalcon\DI::getDefault()->get('logger')->log("tiene saldo negativo o cero, id ".$sms->idSms." ,divide ".$sms->divide);
                    throw new \InvalidArgumentException("No tienes envios suficientes");
                  }
              }

              $preparessr->setFlag(TRUE);
              $preparessr->setOneSms($send);
              $preparessr->ruleSelect();
              $preparessr->configRuleSelect();
              $preparessr->configRuleDefault($preparessr->getFlag());
//
//              if ($this->sanitizeMessage($sms->type)) {
//                $sanitize = new \Sigmamovil\General\Misc\SanitizeString($send->message);
//                $sanitize->strTrim();
//                $sanitize->sanitizeAccents();
//                $sanitize->sanitizeSpecials();  
//                $sanitize->sanitizeSpecialsSms();
//                $send->message = $sanitize->getString();
//              }

              $response = $this->sendSms($preparessr->getConfigRuleSelected(), $send, $this->sms);
              if ($response == "continue") {
                if ($this->getLastSmsSend()->idSmslote != $send->idSmslote) {
                  continue;
                }
              } elseif (is_array($response)) {
                $count += $response["amountsent"];
                $total += $response["amountsent"] + $response["amountnotsent"];
                $quantity += $response["amountsent"] + $response["amountnotsent"];
                if ($quantity == $sms->quantity) {
                  break;
                }
                continue;
              }
              $send->response = $response;
              $send->status = "undelivered";
              //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
              if (in_array($response, $this->arrayinfobitAnswerCharged)) {
                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $send->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                $send->updatedBy = "desarrollo@sigmamovil.com";
                $count++;
//              $saxs->amount = $saxs->amount - 1;
//              $saxs->save();
              }
              if (in_array($send->phone, $this->arrayDataBlockedPhone)) {
              //if(in_array($send->phone, $this->arrayDataBlockedPhone)){
                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
                $send->messageCount = 0;
              }
              $send->save();
              $quantity++;
              $total++;
              if ($this->getLastSmsSend()->idSmslote == $send->idSmslote) {
              //if ($this->getLastSmsSend()->idSmslote == $send->idSmslote) {
                if (count($this->objectBase->messages) > 0) {
                  $adapter = Adapter::findFirst(array(
                              "conditions" => "idAdapter = ?0",
                              "bind" => array(3)//3 en producción
                  ));
                  $res = $this->prepareSmsInfobip($this->objectBase, $adapter);
                  $response = $this->updateStatusSends($res, $adapter->idAdapter);
                  $count += $response["amountsent"];
                  $total += $response["amountsent"] + $response["amountnotsent"];
                  $quantity += $response["amountsent"] + $response["amountnotsent"];
                }
              }
              if ($quantity == $sms->quantity) {
                break;
              }
            }
          }
          $sms->sent += $count;
          $sms->total += $total;
        }

        //Por si las 
        $LogSmsSend = new \LogSmsSend();
        $LogSmsSend->idSms = $sms->idSms;
        $LogSmsSend->totalSendProccess = $sms->sent;
        $LogSmsSend->totalSmsProccess = $sms->total;

        $countSmsTotal = \Smslote::count(array(
                    "conditions" => "idSms = ?0",
                    "bind" => array(0 => $sms->idSms)
        ));

        $countSendTotal = \Smslote::count(array(
                    "conditions" => "idSms = ?0 AND status = 'sent'",
                    "bind" => array(0 => $sms->idSms)
        ));

        $LogSmsSend->totalSendDB = $countSendTotal;
        $LogSmsSend->totalSmsDB = $countSmsTotal;
        $LogSmsSend->save();

        $sms->sent = $countSendTotal;
        $sms->total = $countSmsTotal;
        $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms-send", "data" => ["idSms" => $id, "sent" => $sms->sent, "total" => $sms->total]]);
        $smsLote = Smslote::find(array(
                    "conditions" => "idSms = ?0 AND status = 'scheduled'",
                    "bind" => array(0 => $sms->idSms)
        ));

        $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
        if (count($smsLote) > 0) {
          $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;
        }
        if($countSendTotal == 0){
          $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
        }
        if ($sms->status == \Phalcon\DI::getDefault()->get('statusSms')->sending) {
          $more = "+ " . $sms->sendingTime . " " . $sms->timeFormat;
          $sms->startdate = date("Y-m-d G:i:s", strtotime($more, strtotime($sms->startdate)));
        } else {
          if ($sms->notification == 1) {
            $this->sendMailNotification($sms);
          }
        }

        $sms->save();
        $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms", "data" => ["idSms" => $id, "status" => $sms->status]]);
        $this->recalculateSaxsBySms($sms->idSubaccount);
//        $detailConfig->amount -=$count;
//        $detailConfig->save();
        $activitylog = new \Sigmamovil\General\Misc\ActivityLogMisc();
        $user = \User::findFirst(array(
                    "conditions" => "email = ?0",
                    "bind" => array($sms->createdBy)
        ));
        $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$count} mensajes de SMS el dia " .
                date("Y-m-d H:i:s", time()) .
                ", con id de envío {$sms->idSms}";
        $activitylog->saveActivityLog($user, \Phalcon\DI::getDefault()->get('services')->sms, ($count * -1), $desc);
//        $this->trace($desc);
        \Phalcon\DI::getDefault()->get('logger')->log("Se modificaron los estados");
        \Phalcon\DI::getDefault()->get('logger')->log("Final del envío " . date("Y-m-d H:i:s", time()));
        $this->elephant->close();
        
        if(isset($sms->idntfyanaconas) && !empty($sms->idntfyanaconas)){            
            $countSendTotal = \Smslote::count(array("conditions" => "idSms = ?0 AND status = 'sent'", "bind" => array(0 => $sms->idSms)));                
            $countNoSendTotal = \Smslote::count(array("conditions" => "idSms = ?0 AND status = 'undelivered'", "bind" => array(0 => $sms->idSms)));
            
            $yanaconas =  new \Sigmamovil\General\Misc\UpdateNotification();
            $yanaconas->setIdNotification($sms->idntfyanaconas);
            $yanaconas->setSent($countSendTotal, $countNoSendTotal);
            $yanaconas->executeQuery();            
            
        }
        
        return "Se modificaron los estados";
      } else {
        \Phalcon\DI::getDefault()->get('logger')->log("El estado del envio es enviado");
        return "El estado del envio es enviado";
      }
    } catch (\InvalidArgumentException $ex) {
      $this->recalculateSaxsBySms($sms->idSubaccount);
      $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->sendMailNotificationFailure($sms);
      $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms", "data" => ["idSms" => $id, "status" => $sms->status]]);
      $LogSmsSend = new \LogSmsSend();
      $LogSmsSend->idSms = $sms->idSms;
      $LogSmsSend->totalSendProccess = $sms->sent;
      $LogSmsSend->totalSmsProccess = $sms->total;

      $countSmsTotal = \Smslote::count(array(
                  "conditions" => "idSms = ?0",
                  "bind" => array(0 => $sms->idSms)
      ));

      $countSendTotal = \Smslote::count(array(
                  "conditions" => "idSms = ?0 AND status = 'sent'",
                  "bind" => array(0 => $sms->idSms)
      ));

      $LogSmsSend->totalSendDB = $countSendTotal;
      $LogSmsSend->totalSmsDB = $countSmsTotal;
      $LogSmsSend->save();

      $sms->sent = $countSendTotal;
      $sms->total = $countSmsTotal;
      $sms->save();
      $this->elephant->close();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage() . "----" . $ex->getTraceAsString());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $e) {
      $this->recalculateSaxsBySms($sms->idSubaccount);
      $sms->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->sendMailNotificationFailure($sms);
      $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms", "data" => ["idSms" => $id, "status" => $sms->status]]);
      $LogSmsSend = new \LogSmsSend();
      $LogSmsSend->idSms = $sms->idSms;
      $LogSmsSend->totalSendProccess = $sms->sent;
      $LogSmsSend->totalSmsProccess = $sms->total;

      $countSmsTotal = \Smslote::count(array(
                  "conditions" => "idSms = ?0",
                  "bind" => array(0 => $sms->idSms)
      ));

      $countSendTotal = \Smslote::count(array(
                  "conditions" => "idSms = ?0 AND status = 'sent'",
                  "bind" => array(0 => $sms->idSms)
      ));

      $LogSmsSend->totalSendDB = $countSendTotal;
      $LogSmsSend->totalSmsDB = $countSmsTotal;
      $LogSmsSend->save();

      $sms->sent = $countSendTotal;
      $sms->total = $countSmsTotal;
      $sms->save();
      $this->elephant->close();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $e->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTrace());
    }
  }

  public function sendMailNotification($sms) {
    try {
      $idAllied = $sms->Subaccount->Account->idAllied;
      $mail = Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and idAllied = ?1',
                  'bind' => array(0 => 'sms-finished', 1 => $idAllied)
      ));
      $data = new stdClass();
      if ($mail) {
        $data->fromName = $mail->fromName;
        $data->fromEmail = $mail->fromEmail;
        $data->from = array($mail->fromEmail => $mail->fromName);
        $data->subject = $mail->subject;

        $mail->content = str_replace("%NAME_SENT%", $sms->name, $mail->content);
        $mail->content = str_replace("%DATETIME_SENT%", $sms->startdate, $mail->content);
        $mail->content = str_replace("%TOTAL_SENT%", $sms->sent, $mail->content);
        $mail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLinkSms($sms->idSms), $mail->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($mail->content));
        $content = $editorObj->render();

        $data->html = str_replace("tmp-url", $url, $content);
        $data->plainText = $mail->plainText;
      } else {
        $data->fromEmail = (isset($sms->Subaccount->Account->Allied->email)) ? $sms->Subaccount->Account->Allied->email : "soporte@sigmamovil.com";
        $data->fromName = (isset($sms->Subaccount->Account->Allied->name)) ? $sms->Subaccount->Account->Allied->name : "soporte Sigmamovil";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificación de envío de SMS";
        $target = json_decode($sms->receiver);

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
                . 'Se le informa que se ha enviado SMS al lote correspondiete.'
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
        $data->plainText = "Se ha enviado un SMS.";
      }
      $to = [];
      if (isset($sms->email) && $sms->email != "") {
        $email = explode(",", trim($sms->email));
        foreach ($email as $key) {
          array_push($to, trim($key));
        }
      }
//      array_push($to, $sms->createdBy);
      $data->to = array_unique($to);

//      $data->to = array_unique($to);

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

  public function sendMailNotificationFailure($sms) {
    try {

      //Objeto que guardara la informacion de envio de correo
      $data = new \stdClass();

      //Datos del correo
      $data->fromEmail = "desarrollo@sigmamovil.com";
      $data->fromName = "Servicio Cancelacion - AIO";
      $data->from = array($data->fromEmail => $data->fromName);
      $data->subject = "Cancelacion de SMS";

      //Contenido del correo
      $content = '<table style="background-color: #E6E6E6; width: 100%;">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding: 20px;"><center>'
              . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
              . '<tbody>'
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
              . '<td style="word-break: break-word; padding: 15px 15px 0 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
              . 'Estimado equipo de Desarrollo:'
              . '</span></h3>'
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
              . '<td style="word-break: break-word; padding: 0px 15px 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
              . 'Se informa que la subcuenta <b>'.$sms->Subaccount->name.'</b>, de la cuenta <b>'.$sms->Subaccount->Account->name.'</b>, la campaña <b>'.$sms->idSms.'</b> con nombre  <b>'.$sms->name.'</b> se ha cancelado, esta campaña tiene </b>'.$sms->target.' destinatarios</b>.'
              . '</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Se ha enviado una notificacion de cancelacion de SMS.";

      $data->to = "desarrollo.tics@sigmamovil.com.co";

      $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification SMS balance: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function encodeLinkSms($idSms) {
    $src = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'downloadsms/download/' . $idSms;
    return $src;
  }

  private function sanitizeMessage($type) {
    if ($type == "csv" || $type = "lote") {
      return true;
    }
  }
  
  public function findBlockedPhone($idAccount){
    if($idAccount != 912 || $idAccount != "912"){
      $blocked = Blocked::find([array(
        "idAccount" => (int) $idAccount,
        "deleted" => 0,
        "phone" => ['$nin' => ["", null, "null",0,"0"]]
      )]);
      if($blocked != false){
        foreach ($blocked as $value){
          if($value->phone != ""){
            if(!in_array($value->phone, $this->arrayDataBlockedPhone)){
              $this->arrayDataBlockedPhone[] = (string) $value->phone;
            }  
          }
        }
      }
      unset($blocked);
    }
  }

}

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sender = new SmsSender();

$sender->verifyExist($id);
