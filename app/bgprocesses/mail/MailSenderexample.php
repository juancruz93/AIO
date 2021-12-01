<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/LinkService.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/AttachmentObject.php");
require_once(__DIR__ . "/../sender/InterpreterTarget.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/CustomfieldManager.php");
require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}


$mailSender = new MailSender();

$mailSender->startSender($id);

class MailSender {

  public $urlManager,
          $mta,
          $mail,
          $services,
          $detailConfig;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->mta = \Phalcon\DI\FactoryDefault::getDefault()->get("mtadata");
    $this->urlManager = $di->get('urlManager');
    $this->db = $di->get('db');
    $this->assetsrv = $di['asset'];
    $this->path = $di['path'];
    $this->services = $di->get('services');
  }

  /**
   * @param $idMail
   */
  public function startSender($idMail) {
    
    
    try {
      
      /* Se busca el mail */
      $this->mail = \Mail::findFirst(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));

      /* Se busca el contenido del mail */
      $mailContent = \MailContent::findFirst(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));

      /* Se valida que el mail y el contenido del mail exista */
      if (!$this->mail || !$mailContent) {
        throw new InvalidArgumentException('El Mail no existe, o el contenido  or The html content is incomplete or invalid!');
      }


      /* Se busca el account del mail */
      $account = $this->mail->Subaccount->Account;
      /* $this->mailLimit = $this->mail->Subaccount->Account->Accountclassification->mailLimit;
        $oldstatus = $this->mail->status; */

      /* Valida que el mail se escuentre programado o pausado */
      $this->checkMailStatus($this->mail);

      /* Cargamos el dominio correspondiente de la cuenta para el envio */
      foreach ($this->mail->Subaccount->Account->AccountConfig->DetailConfig as $key) {
        if ($key->idServices == $this->services->email_marketing) {
          $this->setDetailConfig($key);
        }
      }

      $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;
      /* Se crea una instancia de mongo para realizar operaciones de insercion, edicion, etc... */
      $manager = \Phalcon\DI::getDefault()->get('mongomanager');

      /* Se valida que el mail tenga contenido para enviar */
      if (trim($mailContent->content) === '') {
        throw new \InvalidArgumentException("Error mail's content is empty");
      }
      /* Guarda la cantidad de mensajes enviados */
      $messagesSent = $this->mail->messagesSent;

      /* Cambia el estado del mail */
      $this->mail->status = 'sending';
      $this->saveMail($this->mail);

      if ($this->mail->sentprocessstatus != 'finished') {

        /* Cambia el estado del proceso de envio del mail */
        $this->mail->sentprocessstatus = 'loading-target';
        $this->saveMail($this->mail);
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete(['idMail' => $idMail]);
        $manager->executeBulkWrite('aio.mxc', $bulk);

        /* Se preparan los contactos para realizar el envio */
        $interpreter = new InterpreterTarget();
        $interpreter->setMail($this->mail);
        if ($this->mail->type == "automatic") {
          $automaticCampaignConfiguration = AutomaticCampaignConfiguration::findFirst(["conditions" => "idAutomaticCampaign = ?0", "bind" => [$this->mail->idAutomaticCampaign]]);
          $automaticCampaignObj = new Sigmamovil\General\Misc\AutomaticCampaignObj($automaticCampaignConfiguration->AutomaticCampaign, $automaticCampaignConfiguration);
          $interpreter->setAutomaticObj($automaticCampaignObj);
          $FirstNode = $automaticCampaignObj->getNode(0);
          $firstConnection = $automaticCampaignObj->searchConnection(0);
          $SecondNode = $automaticCampaignObj->getNode($firstConnection["dest"]);
          $target = $automaticCampaignObj->transformTarget($FirstNode->sendData);
          $connection = $automaticCampaignObj->searchConnection($SecondNode->id);
          $nextOperator = $automaticCampaignObj->getNode($connection["dest"]);
          $beforeStep = $automaticCampaignObj->getBeforeStep($nextOperator);
          if ($nextOperator->method == "actions") {
            $negation = json_decode($automaticCampaignObj->searchNegation($nextOperator->id));
            for ($i = 0; $i < count($negation); $i++) {
              if ($negation[$i]->dest->class == "negation") {
                $nodeNegation = new \stdClass();
                $nodeNegation->idNode = $negation[$i]->dest->idNode;
                $nodeNegation->node = $automaticCampaignObj->getNode($negation[$i]->dest->idNode);
                $nodeNegation->date = $automaticCampaignObj->getDataTime($negation[$i]->dest);
                $nodeNegation->beforeStep = $beforeStep;
              } else {
                if ($beforeStep == "no clic" || $beforeStep == "no open") {
                  $nodeSuccess = new \stdClass();
                  $nodeSuccess->idNode = $negation[$i]->dest->idNode;
                  $nodeSuccess->node = $automaticCampaignObj->getNode($negation[$i]->dest->idNode);
                  $nodeSuccess->date = $automaticCampaignObj->getDataTime($nextOperator);
                  $nodeSuccess->beforeStep = $beforeStep;
                }
              }
            }

            if (isset($nodeSuccess) || isset($nodeNegation)) {
              $interpreter->setAutomatic(true);
              if ($beforeStep == "no clic" || $beforeStep == "no open") {
                $interpreter->setNegation(0);
                $interpreter->setAutomaticCampaignConfiguration($nodeSuccess);
              } else {
                if (isset($nodeNegation)) {
                  $interpreter->setNegation(1);
                  $interpreter->setAutomaticCampaignConfiguration($nodeNegation);
                }
              }
            }
          } else if ($nextOperator->method == "time") {
            $interpreter->setAutomatic(true);
            $connection = $automaticCampaignObj->searchConnection($nextOperator->id);
            $node = $automaticCampaignObj->getNode($connection["dest"]);
            $nodeSuccess = new \stdClass();
            $nodeSuccess->idNode = $connection["dest"];
            $nodeSuccess->node = $node;
            $nodeSuccess->date = $automaticCampaignObj->getDataTime($nextOperator);
            $nodeSuccess->beforeStep = $beforeStep;
            $interpreter->setNegation(0);
            $interpreter->setAutomaticCampaignConfiguration($nodeSuccess);
          }
        }
        //echo "Antes de insertar \n";
        $interpreter->searchTotalContacts();
        //echo "Después de insertar \n";
      }
      $mcxCount = Mxc::count([["idMail" => $this->mail->idMail]]);
      //echo "Insertó {$mcxCount}";
      //exit();
      /* Consultamos si tiene la capacidad para el correo el subaccount que realizo el envio */
      $saxs = \Saxs::findFirst(array("conditions" => "idSubaccount = ?0 and idServices = ?1", "bind" => array($this->mail->idSubaccount, $this->services->email_marketing)));

      if (!$saxs) {
        throw new Sigmamovil\General\Exceptions\ValidateSaxsException('La subcuenta no tiene los servicios habilitados,por favor comunicarse a soporte.');
      }

      if ($saxs->accountingMode == "sending") {
        if ($saxs->amount < $mcxCount) {
          throw new Sigmamovil\General\Exceptions\ValidateSaxsException("No tiene saldo suficiente para realizar el envío, le invitamos a recargar el servicio.");
        }
//        $saxs->amount = $saxs->amount - $mcxCount;
//        $this->saveSaxs($saxs);
      }

      $this->mail->sentprocessstatus = 'finished';
      $this->saveMail($this->mail);
      /* Se valida que el mail sea de tipo Editor o html */
      if ($mailContent->typecontent == 'Editor') {
        $mailContent->url = "mail/contenteditor";
        $htmlObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $htmlObj->setAccount($account);
        $htmlObj->assignContent(json_decode($mailContent->content));
        $html = utf8_decode($htmlObj->replacespecialchars($htmlObj->render()));
      } else if ($mailContent->typecontent == "html" || $mailContent->typecontent == "url") {
        $mailContent->url = "mail/htmlcontent";
        $footerObj = new \Sigmamovil\General\Misc\FooterObj();
        $footerObj->setAccount($account);
        $html = utf8_decode($footerObj->addFooterInHtml(html_entity_decode($mailContent->content, ENT_QUOTES)));
      }

      $urlManager = $this->urlManager;

      /* Se preparan los links y las imagenes del contenido del Mail */
      $imageService = new ImageService($account, $domain, $urlManager);
      $linkService = new LinkService($account, $this->mail);
      $prepareMail = new PrepareMailContent($linkService, $imageService);
      list($contents, $links) = $prepareMail->processContent($html, true, $this->mail);

      /* Se valida que el contenido del mail tenga campos personalizado y si es asi se preparan y se reemplazan en el contenido del Mail */
      $customfieldManager = new CustomfieldManager($mailContent, $urlManager);
      $content = $customfieldManager->prepareUpdatingForms($contents);
      $field = $customfieldManager->searchCustomFields($content);
//----------------------- BIEN
      /* Se encarga de recorrer los contactos a los que se les va a enviar el Mail */
      $contactIterator = new ContactIterator($this->mail, $manager);

      // Crear transport y mailer
      $transport = Swift_SmtpTransport::newInstance('34.204.240.48', 25);
      $swift = Swift_Mailer::newInstance($transport);

      //$sentContacts = array();

      $from = array($this->mail->Emailsender->email => $this->mail->NameSender->name);

      // Crear objeto que se encarga de insertar links para cada usuario
      $trackingObj = new TrackingUrlObject();

      // Consultar el mail class para inyectarselo en las cabeceras del correo con switmailer
      $mailclass = Mailclass::findFirstByIdMailClass($this->detailConfig->Dcxmailclass[0]->idMailClass);

      // Crear variables listID y sendID para inyectarlas a las cabeceras con swiftmailer
      $prefixID = Phalcon\DI::getDefault()->get('instanceIDprefix')->prefix;
      if (!$prefixID || $prefixID == '') {
        $prefixID = '0ai';
      }
      $listID = 't' . $prefixID . $this->mail->Subaccount->Account->idAccount;
      $sendID = $prefixID . $this->mail->idMail;

      // MTA a utilizar
      $mtaName = $this->detailConfig->Dcxmta[0]->Mta->name;
      $mta = 'MTA_GENERAL_1';

      // Prefijo de tracking ID
      $trIDprefix = 'ac' . $this->mail->idMail . 'x';
//      $trIDprefix = 'em' . $this->mail->idMail . 'x';

      /*
       * Comprobar si es un correo con adjuntos y si es asi, buscar los archivos y adjuntarlos
       */
      $attach = "";
      if ($this->mail->attachment == 1) {
        $attachment = new AttachmentObject($this->mail, $account, $this->path);
        $attachment->addAttachment();
        $attach = $attachment->getArratch();
      }

      /* Se carga el asunto del correo */
      $subject = $this->mail->subject;
      $text = $mailContent->plaintext;

      $bulk = new MongoDB\Driver\BulkWrite;
      $arrayIdContacts = array();
      $totalAttch = count($attach);

//      $nameMailClass = $mailclass->name;
      $nameMailClass = "default";
      $mailReplyto = $this->mail->replyto;

      /* REGISTRO EN EL ACTIVITY LOG */
      $activityLog = new \Sigmamovil\General\Misc\ActivityLogMisc();
      $user = \User::findFirst(array(
                  "conditions" => "email = ?0",
                  "bind" => array($this->mail->createdBy)
      ));
      $target = json_decode($this->mail->target);
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
      $amount = $mcxCount * -1;
      $service = $this->services->email_marketing;
      $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$mcxCount} CORREOS el dia " .
              date("Y-m-d H:i:s", time()) .
              ", con id de envío {$this->mail->idMail}, a {$lista}:{$listas} ";
      $activityLog->saveActivityLog($user, $service, $amount, $desc);

      $i = 0;
      /* Comienza la iteracion de los contactos a los que se les enviara el mail */
      foreach ($contactIterator as $contact) {
		  
        $i++;
        if ($contact->bounced > 0) {
          continue;
        }
        /* Se agragan los campos personalizados al mail */
        $contents = $customfieldManager->processCustomFields($contact, $field, $content);
        
        /* Objeto que se encarga de preparas el track de aperturas y de clics */  
        $htmlWithTracking = $trackingObj->getTrackingUrl($contents["html"], $idMail, $contact->idContact, $links, $this->mail->survey, $this->mail->landingpage);

        // El destinatario (cuando el nombre y apellido estan vacios, se asigna el correo)
//        var_dump($htmlWithTracking);
//        exit();
        $toNameT = trim($contact->name . ' ' . $contact->lastname);
        $toName = (!$toNameT || $toNameT == '') ? $contact->email : $toNameT;
        $to = array($contact->email => $toName);

        $message = new Swift_Message($contents["subject"]);

        $message->setEncoder(Swift_Encoding::get8BitEncoding());

        // Asignacion de headers del mensaje

        $headers = $message->getHeaders();
        $trackingID = $trIDprefix . $contact->idContact . 'x' . $contact->idMail;

        $headers->addTextHeader('X-GreenArrow-MailClass', $nameMailClass);
        $headers->addTextHeader('X-GreenArrow-MtaID', $mta);
        $headers->addTextHeader('X-GreenArrow-InstanceID', $sendID);
        $headers->addTextHeader('X-GreenArrow-Click-Tracking-ID', $trackingID);
        $headers->addTextHeader('X-GreenArrow-ListID', $listID);
        $headers->addTextHeader('List-Unsubscribe', $trackingObj->getUnsubscribeLink());
        $headers->addTextHeader('X-GreenArrow-Click-Tracking-Do', 1);
        $headers->addTextHeader('X-GreenArrow-Click-Tracking-ID', $trackingID);
        $headers->addTextHeader('X-GreenArrow-List-Unsubscribe-HTTP-URL', $trackingObj->getUnsubscribeLink());
        
        $message->setFrom($from);
        $message->setBody($htmlWithTracking, 'text/html');
        $message->setTo($to);

        /* Se asigna un responder a, solo si esta definido */
        if ($mailReplyto != null) {
          $message->setReplyTo($mailReplyto);
        }
        $message->addPart($contents['text'], 'text/plain');

        /* Se valida de que el mail tenga archivos adjuntos, si es asi se adjuntan al mail */
        if ($totalAttch > 0 && $this->mail->attachment == 1) {
          foreach ($attach as $at) {
            $message->attach(
                    Swift_Attachment::fromPath($at->path)->setFilename($at->name)
//                    Swift_Attachment::fromPath($at->path)->setFilename($this->mail->name)
            );
          }
        }

        //\Phalcon\DI::getDefault()->get('logger')->log($htmlWithTracking);
        /* si todos los pasos anteriores han sido correctos, se realiza el envio */
        $recipients = $swift->send($message, $failures);
		var_dump($recipients);
        if ($recipients) {
          //Acumula los id de los contactos para ir actualizando los estados de ellos
          $arrayIdContacts[] = $contact->idContact;
          $messagesSent = $messagesSent + 1;
        }

        /* $arrayIdContacts[] = $contact->idContact;
          $messagesSent = $messagesSent + 1; */
        /* Se valida que cada 20 contactos se actualicen los estados a sent de estos */
        if (count($arrayIdContacts) == 20) {
          $bulk->update(['idMail' => $idMail, 'idContact' => ['$in' => $arrayIdContacts]], ['$set' => ['status' => 'sent']], ['multi' => true]);
          $manager->executeBulkWrite('aio.mxc', $bulk);

          $this->mail->messagesSent = $messagesSent;
          $this->saveMail($this->mail);

          unset($bulk);
          unset($arrayIdContacts);
          $bulk = new MongoDB\Driver\BulkWrite;
          $arrayIdContacts = array();
        }
        //\Phalcon\DI::getDefault()->get('logger')->log('correo enviado ' . $recipients);
      }

      /* Se valida de que hayan contactos pendientes por cambio de estado, si es asi se actualizan los estados a sent */
      if (count($arrayIdContacts) > 0) {
        $bulk->update(['idMail' => $idMail, 'idContact' => ['$in' => $arrayIdContacts]], ['$set' => ['status' => 'sent']], ['multi' => true]);
        $manager->executeBulkWrite('aio.mxc', $bulk);

        $this->mail->messagesSent = $messagesSent;
        $this->saveMail($this->mail);

        unset($arrayIdContacts);
      }

      /* cuando se finaliza el envio, se cambia el estado del mail a sent */
      if (isset($this->mail->notificationEmails) && $this->mail->notificationEmails != "") {
        $this->sendMailNotification($this->mail);
      }
      $this->mail->quantitytarget = $i;
      $this->mail->status = 'sent';
      $this->saveMail($this->mail);
      $this->reCountSaxs($this->mail->idMail, $saxs);
      return true;
    } catch (Sigmamovil\General\Exceptions\ValidateSaxsException $ex) {
      $this->sendMailSaldo();
      $this->mail->status = "paused";
      $this->saveMail($this->mail);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (InvalidArgumentException $ex) {
      $this->mail->status = 'canceled';
      $this->saveMail($this->mail);
      $sendMailCanceled = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      $sendMailCanceled->sendMailNotificationCanceled($this->mail);
      if (isset($saxs)) {
        $this->reCountSaxs($this->mail->idMail, $saxs);
      }
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      $this->mail->status = 'canceled';
      $this->saveMail($this->mail);
      $sendMailCanceled = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      $sendMailCanceled->sendMailNotificationCanceled($this->mail);
      if (isset($saxs)) {
        $this->reCountSaxs($this->mail->idMail, $saxs);
      }
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage() . "=> \n ". $ex->getTraceAsString());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  /**
   * Funcion encargada de validar que el mail se encuentre en estado programado, pausado
   * @param $mail
   */
  public function checkMailStatus($mail) {
    if ($mail->status != 'paused' && $mail->status != 'scheduled') {
      throw new InvalidArgumentException('El correo no tiene estados Pausado o Programado. Estados no permitidos, en el Mail con ID ' . $mail->idMail . ' Con estado ' . $mail->status);
    }
  }

  public function saveMail($mail) {

    if (!$mail->save()) {
      foreach ($mail->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
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
//    $countMxc = \Mxc::count([["idMail" => $this->mail->idMail, "status" => "scheduled"]]);
    if ($saxs != false) {
      if ($saxs->accountingMode == "sending") {
        $sql = "CALL updateCountersSendingSaxs({$saxs->idSubaccount})";
        $this->db->execute($sql);
//        $saxs->amount = $saxs->amount + $countMxc;
//        $this->saveSaxs($saxs);
      }
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

    $data->to = array($this->mail->createdBy => $this->mail->createdBy);
    $data->from = array($data->fromEmail => $data->fromName);

//    $mailSender = new \Sigmamovil\General\Misc\MailSender();
//    $mailSender->setData($data);
//    $mailSender->setHtml($html);
//    $mailSender->setPlainText($plainText);
//    $mailSender->sendBasicMail();
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48',25);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  public function setDetailConfig($detail) {
    $this->detailConfig = $detail;
  }

  public function sendMailNotification($maildata) {
    try {
      $idAllied = $maildata->Subaccount->Account->idAllied;
      $systemMail = Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and idAllied = ?1',
                  'bind' => array(0 => 'mail-finished', 1 => $idAllied)
      ));
      $data = new stdClass();

      if ($systemMail) {
        $data->fromName = $systemMail->fromName;
        $data->fromEmail = $systemMail->fromEmail;
        $data->subject = $systemMail->subject;
        $systemMail->content = str_replace("%NAME_SENT%", $maildata->name, $systemMail->content);
        $systemMail->content = str_replace("%DATETIME_SENT%", $maildata->scheduleDate, $systemMail->content);
        $systemMail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLink($maildata->idMail, $msn->idSubaccount, "complete"), $systemMail->content);
        $systemMail->content = str_replace("%LINK_SUMMARY_SENT%", $this->encodeLink($maildata->idMail, $msn->idSubaccount, "summary"), $systemMail->content);
        $systemMail->content = str_replace("%TOTAL_SENT%", $maildata->messagesSent, $systemMail->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($systemMail->content));
        $content = $editorObj->render();

        $data->html = str_replace("tmp-url", $url, $content);
        $data->plainText = $systemMail->plainText;
        $data->from = array($systemMail->fromEmail => $systemMail->fromName);
      } 
      else {
        $data->fromEmail = $maildata->Subaccount->Account->Allied->email;
        $data->fromName = $maildata->Subaccount->Account->Allied->name;
        $data->from = array($maildata->Subaccount->Account->Allied->email => $maildata->Subaccount->Account->Allied->name);
        $data->subject = "Notificación de envío de correo electrónico";
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
                . 'Se le informa que se ha enviado el correo electrónico <b>' . $maildata->name . '</b> satisfactoriamente en la fecha <b>' . $maildata->scheduleDate . "h</b>"
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
        $data->plainText = "Se ha enviado un correo electronico.";
      }

      $email = explode(",", trim($maildata->notificationEmails));
      $to = [];
      foreach ($email as $key) {
        array_push($to, trim($key));
      }
      $data->to = $to;
      $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating sendMailNotificatio: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating sendMailNotificatio: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
    }
  }

  public function encodeLink($idMail, $idSubaccount, $type) {
    $src = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'statistic/share/1-' . $idMail . "-" . $idSubaccount . "-" . $type;
    return $src . '-' . md5($src . '-Sigmamovil_Rules');
  }

}
