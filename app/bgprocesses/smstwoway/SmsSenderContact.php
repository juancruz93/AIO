<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sms/SmsScript.php");
require_once(__DIR__ . "/../sender/InterpreterTargetSms.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerSms.php");
require_once(__DIR__ . "/../sender/PrepareSmsSendingRule.php");

class SmsSenderContact extends SmsScript {

  public $sms;

  public function __construct() {
    parent::__construct();
  }

  public function verifyExist($id) {
    try {

      $this->smstwoway = \Smstwoway::findFirst(array(
                  "conditions" => "idSmsTwoway = ?0",
                  "bind" => array(0 => $id)
      ));



      if (!$this->smstwoway) {
        return "No existe el smstwoway";
      }

      if (($this->smstwoway->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) or ( $this->smstwoway->status == \Phalcon\DI::getDefault()->get('statusSms')->sending)) {
        $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;

        $this->saveSms($this->smstwoway);

        foreach ($this->smstwoway->Subaccount->Account->accountConfig->detailConfig as $key) {
          if ($key->idServices == 1) {
            $detailConfig = $key;
            $speed = $this->speedSms($key->speed);
          }
        }
        foreach ($this->smstwoway->Subaccount->Saxs as $key) {
          if ($key->idServices == 1) {
            $saxs = $key;
          }
        }
        $count = 0;


        $smsxcCount = Smsxc::count([["idSmsTwoway" => $this->smstwoway->idSmsTwoway]]);

        //Si no hay ningún registro en smsxc lo crea, sino no crea

        if ($smsxcCount == 0) {
          $interpreter = new InterpreterTargetSms();
          $interpreter->setSmstwoway($this->smstwoway);
          $interpreter->searchTotalContacts();
        }
        

        $saxs = \Saxs::findFirst(array("conditions" => "idSubaccount = ?0 and idServices = ?1", "bind" => array($this->smstwoway->idSubaccount, $this->services->sms_two_way)));
        
       
        if (!$saxs) {
          throw new Sigmamovil\General\Exceptions\ValidateSaxsException('La subcuenta no tiene los servicios habilitados,por favor comunicarse a soporte.');
        }
        if ($smsxcCount > (Int) $saxs->amount) {
//        if ($smsxcCount > 1000) {
          throw new Sigmamovil\General\Exceptions\ValidateSaxsException("No tiene saldo suficiente para realizar el envío, le invitamos a recargar el servicio.");
        }

//        $saxs->amount = $saxs->amount - $smsxcCount;
//        $this->saveSaxs($saxs);
        /* REGISTRO EN EL ACTIVITY LOG */
        $activityLog = new \Sigmamovil\General\Misc\ActivityLogMisc();
        $user = \User::findFirst(array(
                    "conditions" => "email = ?0",
                    "bind" => array($this->smstwoway->createdBy)
        ));
        $target = json_decode($this->smstwoway->receiver);
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
        $listas = substr($listas, 0, strlen($listas) - 1);
        $amount = $smsxcCount * -1;
        $service = $this->services->sms_two_way;
        $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$smsxcCount} SMS el dia " .
                date("Y-m-d H:i:s", time()) .
                ", con id de envío {$this->smstwoway->idSmsTwoway}, a {$lista}:{$listas} ";
        $activityLog->saveActivityLog($user, $service, $amount, $desc);
        $customfieldManagerSms = new CustomfieldManagerSms($this->smstwoway);
        $field = $customfieldManagerSms->searchCustomFields($this->smstwoway->message);
        
        $smsContact = Smsxc::find([["idSmsTwoway" => $this->smstwoway->idSmsTwoway]]);

        $preparessr = new PrepareSmsSendingRule($this->smstwoway);

        if ($this->smstwoway->divide == 0) {

          $total = 0;
          $sent = 0;
          foreach ($smsContact as $contact) {
            if ($contact->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              $total++;

              $customizedMessage = $customfieldManagerSms->processCustomFields($contact, $field, $this->smstwoway->message);
              $contact->message = $customizedMessage;

              $preparessr->setFlag(TRUE);
              $preparessr->setOneSms($contact);
              $preparessr->ruleSelect();
              $preparessr->configRuleSelect();
              $preparessr->configRuleDefault($preparessr->getFlag());

              $response = $this->sendSms($preparessr->getConfigRuleSelected(), $contact, $this->smstwoway);

              $contact->response = $response;
              $contact->status = "undelivered";
              //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
              if (in_array($response, $this->arrayinfobitAnswerCharged)) {
                $sent++;
                $contact->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $contact->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                $contact->updatedBy = "desarrollo@sigmamovil.com";
              }
              $contact->save();
            }
          }
        } else if ($this->smstwoway->divide == 1) {
          $quantity = 0;
          $total = 0;
          $sent = 0;
          foreach ($smsContact as $contact) {
            if ($contact->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
              $total++;

              $customizedMessage = $customfieldManagerSms->processCustomFields($contact, $field, $this->smstwoway->message);
              $contact->message = $customizedMessage;

              $preparessr->setFlag(TRUE);
              $preparessr->setOneSms($contact);
              $preparessr->ruleSelect();
              $preparessr->configRuleSelect();
              $preparessr->configRuleDefault($preparessr->getFlag());

              $response = $this->sendSms($preparessr->getConfigRuleSelected(), $contact, $this->smstwoway);

              $contact->response = $response;
              $contact->status = "undelivered";
              //if ($response == "0: Accepted for delivery" || $response == "PENDING_ENROUTE") {
              if (in_array($response, $this->arrayinfobitAnswerCharged)) {
                $sent++;
                $contact->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                $contact->idAdapter = $preparessr->getConfigRuleSelected()->Adapter->idAdapter;
                $contact->updatedBy = "desarrollo@sigmamovil.com";
              }
              $contact->save();
              $quantity++;
              if ($quantity == $this->sms->quantity) {
                break;
              }
            }
          }
        }

//        $this->sms->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
        $this->smstwoway->total += $total;
        $this->smstwoway->sent += $sent;


        $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
        $smsContact = Smstwowayxc::find([["idSms" => $this->smstwoway->idSmsTwoway, "status" => \Phalcon\DI::getDefault()->get('statusSms')->scheduled]]);

        if (count($smsContact) > 0) {
          $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;
        }

        if ($this->smstwoway->status == \Phalcon\DI::getDefault()->get('statusSms')->sending) {
          $more = "+ " . $this->smstwoway->sendingTime . " " . $this->smstwoway->timeFormat;
          $this->smstwoway->startdate = date("Y-m-d G:i:s", strtotime($more, strtotime($this->smstwoway->startdate)));
        } else {
          if ($this->smstwoway->notification == 1) {
            $this->sendMailNotification($this->smstwoway);
          }
        }


        $this->saveSms($this->smstwoway);

        $this->recalculateSaxsBySms($this->smstwoway->idSubaccount);
        return "Se modificaron los estados";
      } else {
        return "El estado del envio es enviado";
      }
    } catch (Sigmamovil\General\Exceptions\ValidateSaxsException $ex) {
      $this->sendMailSaldo();
      $this->recalculateSaxsBySms($this->smstwoway->idSubaccount);
      $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->paused;
      $this->saveSms($this->mail);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\InvalidArgumentException $ex) {
      $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->saveSms($this->smstwoway);
      $this->recalculateSaxsBySms($this->smstwoway->idSubaccount);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $e) {
      $this->smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
      $this->saveSmstwoway($this->smstwoway);
      $this->recalculateSaxsBySms($this->smstwoway->idSubaccount);
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
    $countSmsxc = \Smsxc::count([["idSms" => $this->sms->idSms, "status" => "scheduled"]]);
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
        $data->fromName = $mail->fromName;
        $data->fromEmail = $mail->fromEmail;
        $data->from = array($mail->fromEmail => $mail->fromName);
        $data->subject = $mail->subject;

        $mail->content = str_replace("%NAME_SENT%", $sms->name, $mail->content);
        $mail->content = str_replace("%DATETIME_SENT%", $sms->startdate, $mail->content);
        $mail->content = str_replace("%TOTAL_SENT%", $sms->sent, $mail->content);

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
//      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating masteraccount: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
//      $this->notification->error($e->getMessage());
    }
  }

//
//  public function customizeMessage($message, $tags) {
//
//
//    for ($i = 0; $i < count($this->tags); $i++) {
//      $customizedMessage = str_replace($this->tags[$i]['tag'], $this->tags[$i]['name'], $message);
//    }
//
//    return $customizedMessage;
//  }
//
//  public function getAllTags($ids) {
//
//    $this->tags[0]['name'] = 'Nombre';
//    $this->tags[0]['tag'] = '%%NOMBRE%%';
//    $this->tags[1]['name'] = 'Apellido';
//    $this->tags[1]['tag'] = '%%APELLIDO%%';
//    $this->tags[2]['name'] = 'Fecha de nacimiento';
//    $this->tags[2]['tag'] = '%%FEC_NAC%%';
//    $this->tags[3]['name'] = 'Correo electrónico';
//    $this->tags[3]['tag'] = '%%CORREO%%';
//    $this->tags[4]['name'] = 'Indicativo';
//    $this->tags[4]['tag'] = '%%INDICATIVO%%';
//    $this->tags[5]['name'] = 'Móvil';
//    $this->tags[5]['tag'] = '%%MOVIL%%';
//
//    $sql = "SELECT name,alternativename from customfield "
//            . "WHERE idContactlist in (" . $ids . ")"
//            . "GROUP BY 1,2";
//    $customfields = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
//    $i = 6;
//    foreach ($customfields as $cf) {
//      $this->tags[$i]['name'] = $cf['name'];
//      $this->tags[$i]['tag'] = '%%' . strtoupper($cf['alternativename']) . '%%';
//      $i++;
//    }
//
//    return $this->tags;
//  }
}

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sender = new SmsSenderContact();

$sender->verifyExist($id);
