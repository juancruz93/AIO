<?php
ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sms/SmsScript.php");
require_once(__DIR__ . "/../sender/InterpreterTargetSms.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerSms.php");
require_once(__DIR__ . "/../sender/PrepareSmsSendingRule.php");

class SmsSenderContact extends SmsScript {
  
  public $arrayDataBlockedPhone = array();
  
  public function __construct() {
    parent::__construct();
    $this->setTypeSend("contact");
  }

  public function verifyExist($idSms) {
    try {
      $sms = \Sms::findFirst(array(
                  "conditions" => "idSms = ?0",
                  "bind" => array($idSms)
      ));
      $idSubaccount = $sms->idSubaccount;
      $this->setSms($sms);
      var_dump(print_r("paso 1",true));

      if (!$this->getSms()) {
        return "No existe el sms";
      }

      if (($this->getSms()->status == $this->getStatusSms()->scheduled) || ($this->getSms()->status == $this->getStatusSms()->sending)) {
        var_dump(print_r("paso 2",true));
        $this->getSms()->status = $this->getStatusSms()->sending;
        $this->saveSms($this->getSms());

        foreach ($this->getSms()->Subaccount->Saxs as $key) {
          if ($key->idServices == 1 && $key->status ==1) {
            $saxs = $key;
          }
        }
        $count = 0;

        $smsxcCount = Smsxc::count(array(
                    "conditions" => array(
                        "idSms" => (string) $this->getSms()->idSms
                    )
        ));
        
        //VALIDAR SI EL SMS ES DE UNA CAMPAÑA
        $target = json_decode($this->getSms()->receiver);
        $automaticCampaignStep = AutomaticCampaignStep::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $sms->idSms]]);
        if($automaticCampaignStep){
            if($automaticCampaignStep->beforeStep == "clic"){
                $filters = $target->filters;
                if($filters[0]->typeFilters == 3){
                    $idMailSelected = $filters[0]->mailSelected;
                    $linkSelected = $filters[0]->linkSelected;   
                    $link_ac = $filters[0]->link_ac;
                    if($idMailSelected != null){
                        if(empty($linkSelected) || is_null($linkSelected)){
                            $sql = "SELECT mail_link.idMail_link, mail_link.link FROM mxl "
                            ." LEFT JOIN mail_link ON mail_link.idMail_link = mxl.idMail_link WHERE idMail = {$idMailSelected}";
                            $mxl = $this->db->fetchAll($sql);
                            $arr = [];
                            foreach ($mxl as $key) {
                              $arr[$key['idMail_link']] = $key['link'];
                            }
                            //RECORREMOS EL ARREGLO PARA COMPARAR LINK_AC CON LOS LINKS DE LA CONSULTA
                            if(in_array($link_ac, $arr)){
                                //EXTRAEMOS EL KEY(idMail_link) EN LA POSICION QUE SE ENCUENTRA EL LINK
                                $idMail_link = array_search($link_ac, $arr);
                                //ELIMINAMOS EL ELEMENTO link_ac DEL ELEMENTO
                                unset($target->filters[0]->link_ac);
                                $target->filters[0]->linkSelected = (string) $idMail_link;
                                $target->filters[0]->links[0]->idMail_link = (string) $idMail_link;
                                //GUARDAMOS EL NUEVO RECEIVER EN EL JSON DE SMS
                                $this->getSms()->receiver = null;
                                $this->getSms()->receiver = json_encode($target);
                                $this->saveSms($this->getSms());
                            }
                        }
                    }
                }
            }
        } 
        //Si no hay ningún registro en smsxc lo crea, sino no crea
        if ($smsxcCount == 0) {
          var_dump(print_r("paso 3",true));
          $interpreter = new InterpreterTargetSms();
          $interpreter->setSms($this->getSms());
          $interpreter->searchTotalContacts();
        }

        $saxs = \Saxs::findFirst(array(
                    "conditions" => "idSubaccount = ?0 and idServices = ?1 and status =1",
                    "bind" => array($this->getSms()->idSubaccount, $this->services->sms)
        ));
        if (!$saxs) {
          throw new Sigmamovil\General\Exceptions\ValidateSaxsException('La subcuenta no tiene los servicios habilitados,por favor comunicarse a soporte.');
        }
        if($idSubaccount != "420" && $idSubaccount != 420){
            if ($smsxcCount > (Int) $saxs->amount) {
              \Phalcon\DI::getDefault()->get('logger')->log("tiene saldo negativo o cero, id ".$sms->idSms.", envio por contacto.");
              throw new Sigmamovil\General\Exceptions\ValidateSaxsException("No tiene saldo suficiente para realizar el envío, le invitamos a recargar el servicio.");
            }
        }

        /* REGISTRO EN EL ACTIVITY LOG */

        //$target = json_decode($this->getSms()->receiver);
        $listas = "";
        switch ($target->type) {
          case "contactlist":
            $lista = "las lista de contacto";
            if (isset($target->contactlists)) {
              foreach ($target->contactlists as $key) {
                $listas .= $key->name . "({$key->idContactlist}),";
              }
            }
            break;
          case "segment":
            $lista = "los Segmentos";
            if (isset($target->segment)) {
              foreach ($target->segment as $key) {
                $listas .= $key->name . "({$key->idSegment}),";
              }
            }
            break;
        }

        $activityLog = new \Sigmamovil\General\Misc\ActivityLogMisc();
        $user = \User::findFirst(array(
                    "conditions" => "email = ?0",
                    "bind" => array($this->getSms()->createdBy)
        ));
        $listas = substr($listas, 0, strlen($listas) - 1);

        $amount = $smsxcCount * -1;
        $service = $this->services->sms;
        $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$smsxcCount} SMS el dia " .
                date("Y-m-d H:i:s", time()) .
                ", con id de envío {$this->getSms()->idSms}, a {$lista}:{$listas} ";
        $activityLog->saveActivityLog($user, $service, $amount, $desc);
        /* FIN REGISTRO EN EL ACTIVITY LOG */
        $customfieldManagerSms = new CustomfieldManagerSms($this->getSms());
        $field = $customfieldManagerSms->searchCustomFields($this->getSms()->message);

        if ($this->getSms()->divide == 1) {
          $limit = 10000; $offset = 0; $flag = true;
          //Llamar funcion para obtener los blocked
          $this->findBlockedPhone($sms->Subaccount->idAccount);
          while ($flag) {
            $smsContact = Smsxc::find([["idSms" => $this->getSms()->idSms], "limit" => $limit, "skip" => $offset]);
            if($smsContact){
              foreach ($smsContact as $send) {
                if(in_array($send->phone, $this->arrayDataBlockedPhone)){
                  $send->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
                  $send->messageCount = 0;
                  $send->save();
                }
              }
              $offset += $limit;
            }else{
              $flag = false;
            }
            unset($smsContact);
          }
        }else if ($this->getSms()->divide == 0) {
          $smsContact = Smsxc::find([["idSms" => $this->getSms()->idSms]]);
          //Llamar funcion para obtener los blocked
          $this->findBlockedPhone($sms->Subaccount->idAccount);
          //
          foreach ($smsContact as $send) {
            if(in_array($send->phone, $this->arrayDataBlockedPhone)){
              $send->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
              $send->messageCount = 0;
              $send->save();
            }
          }
          unset($smsContact);
        }
        //
        if ($this->getSms()->divide == 0){
            $smsContact = Smsxc::find([["idSms" => $this->getSms()->idSms, "status" => "scheduled"]]);    
        }else{
            $smsContact = null;
        }
        
        //
        
        if($this->getSms()->idSms == 4069954 ||
           $this->getSms()->idSms == '4069954'){
          var_dump(print_r(count($smsContact),true));exit;
        }
        if ($this->getSms()->divide == 0){
         $this->setLastSmsSend(end($smsContact));   
        }        

        $preparessr = new PrepareSmsSendingRule($this->getSms());
        //Llamar funcion para obtener los blocked
        $this->findBlockedPhone($sms->Subaccount->idAccount);
        //
        if ($this->getSms()->divide == 0) {
          $total = 0;
          $sent = 0;
          foreach ($smsContact as $contact) {
            if ($contact->status == $this->getStatusSms()->scheduled) {
              $total++;

              if($this->getSms()->morecaracter == 1){
                $customfieldManagerSms->setFlagMore($this->getSms()->morecaracter);              
              }
              $customizedMessage = $customfieldManagerSms->processCustomFields($contact, $field, $this->getSms()->message);
              $contact->message = $customizedMessage;

              $preparessr->setFlag(TRUE);
              $preparessr->setOneSms($contact);
              $preparessr->ruleSelect();
              $preparessr->configRuleSelect();
              $preparessr->configRuleDefault($preparessr->getFlag());
              
              //Reemplaza caracteres especiales en el Mensaje

              $sanitize = new \Sigmamovil\General\Misc\SanitizeString($contact->message);
              $sanitize->strTrim();
              $sanitize->sanitizeAccents();
              $sanitize->sanitizeSpecials();  
              $sanitize->sanitizeSpecialsSms();
              $sanitize->nonPrintable();
              $contact->message = $sanitize->getString();
              
              //
              $response = $this->sendSms($preparessr->getConfigRuleSelected()->Adapter, $contact, $this->getSms());
              if ($response == "continue") {
                if ($this->getLastSmsSend()->getId() != $contact->getId()) {
                  continue;
                }
              } elseif (is_array($response)) {
                $sent += $response["amountsent"];
                $total += $response["amountsent"] + $response["amountnotsent"];
                continue;
              }
              $contact->response = $response;
              $contact->status = "undelivered";
              //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
              if (in_array($response, $this->arrayinfobitAnswerCharged)) {
                $sent++;
                $contact->status = $this->getStatusSms()->sent;
                $contact->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                $contact->updatedBy = "desarrollo@sigmamovil.com";
              }
              
              $total++;
              $contact->save();
            }
            if ((string) $this->getLastSmsSend()->getId() == (string) $contact->getId()) {
              if (count($this->objectBase->messages) > 0) {
                $adapter = Adapter::findFirst(array(
                            "conditions" => "idAdapter = ?0",
                            "bind" => array(3)//3 en producción
                ));
                $res = $this->prepareSmsInfobip($this->objectBase, $adapter);
                $response = $this->updateStatusSends($res, $adapter->idAdapter);
                $sent += $response["amountsent"];
                $total += $response["amountsent"] + $response["amountnotsent"];
              }
            }
          }
        } else if ($this->getSms()->divide == 1) {
          $total = 0;
          $sent = 0;
          $offset = 0;
          $limit = 25000;
          $flag = true;
          while($flag) {
            $smsContact = Smsxc::find([["idSms" => $this->getSms()->idSms, "status" => "scheduled"], "limit" => $limit]);
          
            if($smsContact){
              $this->setLastSmsSend(end($smsContact));
              foreach ($smsContact as $contact) { 
                if ($contact->status == $this->getStatusSms()->scheduled) {
                  $total++;
                  if($this->getSms()->morecaracter == 1){
                    $customfieldManagerSms->setFlagMore($this->getSms()->morecaracter);              
                  }
                  $customizedMessage = $customfieldManagerSms->processCustomFields($contact, $field, $this->getSms()->message);
                  $contact->message = $customizedMessage;

                  $preparessr->setFlag(TRUE);
                  $preparessr->setOneSms($contact);
                  $preparessr->ruleSelect();
                  $preparessr->configRuleSelect();
                  $preparessr->configRuleDefault($preparessr->getFlag());
                  //Reemplaza caracteres especiales en el Mensaje

                  $sanitize = new \Sigmamovil\General\Misc\SanitizeString($contact->message);
                  $sanitize->strTrim();
                  $sanitize->sanitizeAccents();
                  $sanitize->sanitizeSpecials();  
                  $sanitize->sanitizeSpecialsSms();
                  $sanitize->nonPrintable();
                  $contact->message = $sanitize->getString();
                  //
                  $response = $this->sendSms($preparessr->getConfigRuleSelected()->Adapter, $contact, $this->getSms());
                  if ($response == "continue") {
                    if ($this->getLastSmsSend()->getId() != $contact->getId()) {
                      continue;
                    }
                  } elseif (is_array($response)) {
                    $sent += $response["amountsent"];
                    $total += $response["amountsent"] + $response["amountnotsent"];
                    continue;
                  }
                  $contact->response = $response;
                  $contact->status = "undelivered";
                  //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
                  if (in_array($response, $this->arrayinfobitAnswerCharged)) {                               
                    $sent++;
                    $contact->status = $this->getStatusSms()->sent;
                    $contact->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                    $contact->updatedBy = "desarrollo@sigmamovil.com";
                  } else if ($this->getSms()->divide && !$this->getSms()->continueError) {
                    break;
                  }
                  
                  $total++;
                  $contact->save();
                }            
                if ((string) $this->getLastSmsSend()->getId() == (string) $contact->getId()) {
                  
                  if (count($this->objectBase->messages)>0) {
                    $adapter = Adapter::findFirst(array(
                      "conditions" => "idAdapter = ?0",
                      "bind" => array(3)//3 en producción
                    ));
                    $res = $this->prepareSmsInfobip($this->objectBase, $adapter); //AQUI ENVIA                
                    $response = $this->updateStatusSends($res, $adapter->idAdapter); //AQUI ACTUALIZA
                    $sent += $response["amountsent"];
                    $total += $response["amountsent"] + $response["amountnotsent"];  
                    $this->objectBase->bulkId = "AIO-SIGMA-SMS-";
                    $this->objectBase->messages = [];
                  }                            
                }
                //end for
              }

              $countvalidate = Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "scheduled"]]);
              if($countvalidate > 0){
                \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++ME DUERMO++++++++++++++");
                sleep(180);
                \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++ME DESPIERTO++++++++++++++");
              } else {
                \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++ME ME SALGO++++++++++++++");
                $flag = false;
              }
            } else {
              if (!isset($smsContact) || count($smsContact) < $limit) {
                $flag = false;              
              } 
            }          
            //end while 
          }
          //end divide
        }

//        $this->sms->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;

        $this->getSms()->total += $total;
        $this->getSms()->sent += $sent;

        $LogSmsSend = new \LogSmsSend();
        $LogSmsSend->idSms = $this->getSms()->idSms;
        $LogSmsSend->totalSendProccess = $this->getSms()->sent;
        $LogSmsSend->totalSmsProccess = $this->getSms()->total;

        $countSmsTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms]]);

        $countSendTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "sent"]]);

        $LogSmsSend->totalSendDB = $countSendTotal;
        $LogSmsSend->totalSmsDB = $countSmsTotal;
        $LogSmsSend->save();

        $this->getSms()->total = $countSmsTotal;
        $this->getSms()->sent = $countSendTotal;

        $this->getSms()->status = $this->getStatusSms()->sent;
        $smsContact = Smsxc::find([["idSms" => $this->getSms()->idSms, "status" => $this->getStatusSms()->scheduled]]);

        if (count($smsContact) > 0) {
          $this->getSms()->status = $this->getStatusSms()->sending;
        }

        if($countSendTotal == 0){
          $this->getSms()->status = $this->getStatusSms()->canceled;
        }

        if ($this->getSms()->status == $this->getStatusSms()->sending) {
          $more = "+ " . $this->getSms()->sendingTime . " " . $this->getSms()->timeFormat;
          $this->getSms()->startdate = date("Y-m-d G:i:s", strtotime($more, strtotime($this->getSms()->startdate)));
        } else {
          if ($this->getSms()->notification == 1) {
            $this->sendMailNotification($this->getSms());
          }
        }


        $this->saveSms($this->getSms());
        
        $automaticCampaignStep = \AutomaticCampaignStep::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $this->getSms()->idSms]]);
          
        if ((isset($automaticCampaignStep) && $automaticCampaignStep->beforeStep == 'Primary' ) || (!empty($automaticCampaignStep) && $automaticCampaignStep->beforeStep == 'Primary')) {
            
             $automaticCampaignStep->status = "sent";
             if (!$automaticCampaignStep->save()) {
                 foreach ($automaticCampaignStep->getMessages() as $message) {
                     throw new \InvalidArgumentException($message);
                 }
             }
        }
        
        $this->recalculateSaxsBySms($this->getSms()->idSubaccount);
        return "Se modificaron los estados";
      } else {
        return "El estado del envio es enviado";
      }
    } catch (Sigmamovil\General\Exceptions\ValidateSaxsException $ex) {
      $this->sendMailSaldo();
      $this->recalculateSaxsBySms($this->getSms()->idSubaccount);
      $this->getSms()->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->sendMailNotificationFailure($sms);
      $LogSmsSend = new \LogSmsSend();
      $LogSmsSend->idSms = $this->getSms()->idSms;
      $LogSmsSend->totalSendProccess = $this->getSms()->sent;
      $LogSmsSend->totalSmsProccess = $this->getSms()->total;

      $countSmsTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms]]);

      $countSendTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "sent"]]);

      $LogSmsSend->totalSendDB = $countSendTotal;
      $LogSmsSend->totalSmsDB = $countSmsTotal;
      $LogSmsSend->save();

      $this->getSms()->total += $countSmsTotal;
      $this->getSms()->sent += $countSendTotal;
      $this->saveSms($this->getSms());
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\InvalidArgumentException $ex) {
      $this->getSms()->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->sendMailNotificationFailure($sms);
      $LogSmsSend = new \LogSmsSend();
      $LogSmsSend->idSms = $this->getSms()->idSms;
      $LogSmsSend->totalSendProccess = $this->getSms()->sent;
      $LogSmsSend->totalSmsProccess = $this->getSms()->total;

      $countSmsTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms]]);

      $countSendTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "sent"]]);

      $LogSmsSend->totalSendDB = $countSendTotal;
      $LogSmsSend->totalSmsDB = $countSmsTotal;
      $LogSmsSend->save();

      $this->getSms()->total = $countSmsTotal;
      $this->getSms()->sent = $countSendTotal;
      $this->saveSms($this->getSms());
      $this->recalculateSaxsBySms($this->getSms()->idSubaccount);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $e) {
      $this->getSms()->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $LogSmsSend = new \LogSmsSend();
      $LogSmsSend->idSms = $this->getSms()->idSms;
      $LogSmsSend->totalSendProccess = $this->getSms()->sent;
      $LogSmsSend->totalSmsProccess = $this->getSms()->total;

      $countSmsTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms]]);

      $countSendTotal = \Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "sent"]]);

      $LogSmsSend->totalSendDB = $countSendTotal;
      $LogSmsSend->totalSmsDB = $countSmsTotal;
      $LogSmsSend->save();

      $this->getSms()->total = $countSmsTotal;
      $this->getSms()->sent = $countSendTotal;
      $this->saveSms($this->getSms());
      $this->recalculateSaxsBySms($this->getSms()->idSubaccount);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $e->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTrace());
    }
  }

  public function saveSaxs($saxs) {
    if (!$saxs->save()) {
      foreach ($saxs->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function reCountSaxs($idMail, $saxs) {
    $countSmsxc = \Smsxc::count([["idSms" => $this->getSms()->idSms, "status" => "scheduled"]]);
    if ($saxs != false) {
      $saxs->amount = $saxs->amount + $countSmsxc;
      $this->saveSaxs($saxs);
    }
  }

  public function sendMailSaldo() {
    $mail = Systemmail::findFirst(array(
                'conditions' => 'name = ?0',
                'bind' => array(0 => 'Insufficient-balance')
    ));

    if ($mail) {
      $data = new stdClass();
      $data->fromName = $mail->fromName;
      $data->fromEmail = $mail->fromEmail;
      $data->subject = $mail->subject;
//      $data->target = array($this->mail->createdBy);

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->assignContent(json_decode($mail->content));
      $content = $editorObj->render();

      $data->html = str_replace("tmp-url", $url, $content);
//      $plainText = str_replace("tmp-url", $url, $mail->plainText);
      $data->plainText = $mail->plainText;
    } else {
      $data = new stdClass();
      $data->fromEmail = "soporte@sigmamovil.com";
      $data->fromName = "Soporte Sigma Móvil";
      $data->subject = "Saldo Insuficiente";
//      $data->target = array($this->mail->createdBy);

      $content = '<table style="background-color: #E6E6E6; width: 100%;"><tbody><tr><td style="padding: 20px;"><center><table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0"><tbody><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">Estimado usuario:</span></h2></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">En el momento no tiene el saldo suficiente para realizar la campaña, por favor comunicarse con soporte.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Saldo Insuficiente para realizar la campaña.";
    }

    $data->to = array($this->sms->createdBy => $this->sms->createdBy);
    $data->from = array($data->fromEmail => $data->fromName);

    $mtaSender = new \Sigmamovil\General\Misc\MtaSender($this->mta->address, $this->mta->port);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  public function sendMailNotification($sms) {
    try {
      $idAllied = $sms->Subaccount->Account->idAllied;
      $mail = Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and deleted=0 and idAllied = ?1',
                  'bind' => array(0 => 'sms-finished', 1 => $idAllied)
      ));

      $data = new stdClass();
      if ($mail) {
        \Phalcon\DI::getDefault()->get('logger')->log("entro");
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

//        $data->html = str_replace("tmp-url", $url, $content);
        $data->html = str_replace("tmp-url", $url, $content);
        $data->plainText = $mail->plainText;
      } else {
        $data->fromEmail = (isset($sms->Subaccount->Account->Allied->email)) ? $sms->Subaccount->Account->Allied->email : "soporte@sigmamovil.com";
        $data->fromName = (isset($sms->Subaccount->Account->Allied->name)) ? $sms->Subaccount->Account->Allied->name : "soporte Sigmamovil";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificación de envío de SMS";

        $target = json_decode($sms->receiver);
        $listas = "";
        $ids = "";
        switch ($target->type) {
          case "contactlist":
            $lista = "las lista de contacto";
            if (isset($target->contactlists)) {
              foreach ($target->contactlists as $key) {
                $listas .= $key->name . "({$key->idContactlist}),";
//                $ids .= $key->idContactlist . ", ";
              }
            }
            break;
          case "segment":
            $lista = "los Segmentos";
            if (isset($target->segment)) {
              foreach ($target->segment as $key) {
                $listas .= $key->name . "({$key->idSegment}),";
//                $sxc = \Sxc::findFirst([["idSegment" => $key->idSegment]]);
//                if ($sxc) {
//                  $sql = "SELECT DISTINCT idContactlist from cxcl "
//                          . "where idContact = " . $sxc->idContact;
//                  $contactlist = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
//                  foreach ($contactlist as $cl) {
//                    $ids .= $cl->idContactlist . ", ";
//                  }
//                }
              }
            }
            break;
        }

//        $tags = $this->getAllTags(substr($where, 0, -2));

        $listas = substr($listas, 0, strlen($listas) - 1);

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
  
  public function findBlockedPhone($idAccount){
    $blocked = Blocked::find([array(
      "idAccount" => (int) $idAccount,
      "deleted" => 0
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

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sender = new SmsSenderContact();

$sender->verifyExist($id);
