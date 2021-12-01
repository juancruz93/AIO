<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/LinkService.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/AttachmentObject.php");
require_once(__DIR__ . "/../sender/InterpreterTargetMail.php");
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
          $validatefailsaxs = false,
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
        "conditions" => "idMail = ?0 AND status IN ('scheduled', 'paused') ",
        "bind" => array(0 => $idMail)
      ));
      \Phalcon\DI::getDefault()->get('logger')->log("Entra al startSender del Mail {$this->mail->idMail}");
      /* Se busca el contenido del mail */
      $mailContent = \MailContent::findFirst(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));

      /* Se valida que el mail y el contenido del mail exista */
      if (!$this->mail || !$mailContent) {
        throw new InvalidArgumentException('El Mail no existe o ya se esta entregando los correos');
      }


      /* Se busca el account del mail */
      $account = $this->mail->Subaccount->Account;
      /* $this->mailLimit = $this->mail->Subaccount->Account->Accountclassification->mailLimit;
        $oldstatus = $this->mail->status; */

      /* Valida que el mail se escuentre programado o pausado */
      $this->checkMailStatus($this->mail);

      /* Cargamos el dominio correspondiente de la cuenta para el envio */
      foreach ($this->mail->Subaccount->Account->AccountConfig->DetailConfig as $key) {
        if ($key->idServices == $this->services->email_marketing && ($key->status == 1 || $key->status == '1')) {
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
      \Phalcon\DI::getDefault()->get('logger')->log("El estado es {$this->mail->status} Mail {$this->mail->idMail}");      
      /* Cambia el estado del mail */
      $this->mail->status = 'sending';
      $this->saveMail($this->mail);
      
      $mxcCount = Mxc::count([["idMail" => $this->mail->idMail]]);
            
      
      /* Consultamos si tiene la capacidad para el correo el subaccount que realizo el envio */
      $saxs = \Saxs::findFirst(array("conditions" => "idSubaccount = ?0 and idServices = ?1 and status =1", "bind" => array($this->mail->idSubaccount, $this->services->email_marketing)));

      if (!$saxs) {
        $this->validatefailsaxs = true;
        throw new Sigmamovil\General\Exceptions\ValidateSaxsException('La subcuenta no tiene los servicios habilitados,por favor comunicarse a soporte.');
      }

      if ($saxs->accountingMode == "sending") {
        if ($saxs->amount < $mxcCount) {
          $this->mail->status = 'canceled';
          $this->mail->canceleduser = 'Saldo Insuficiente';
          $this->saveMail($this->mail);
          throw new Sigmamovil\General\Exceptions\ValidateSaxsException("No tiene saldo suficiente para realizar esta campaña, le invitamos a recargar el servicio.");
        }
        $this->getBalanceServiceMail($this->mail->quantitytarget);
      }
    
    //VALIDAR SI EL MAIL ES DE UNA CAMPAÑA AUTOMATICA
    /*$target = json_decode($this->mail->target);
    $automaticCampaignStep = AutomaticCampaignStep::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $this->mail->idMail]]);
    if($automaticCampaignStep){
        if($automaticCampaignStep->beforeStep == "clic"){
            $filters = $target->filters;
            if($filters[0]->typeFilters == 3){
                $idMailSelected = $filters[0]->mailSelected;
                $linkSelected = $filters[0]->linkSelected;   
                $link_ac = $filters[0]->link_ac;
                if($idMailSelected != null){
                    if(empty($linkSelected) || is_null($linkSelected)){
                        //HACER LA CONSULTA PARA TRAER TODOS LOS LINKS DEL MAIL SELECCIONADO
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
                            //GUARDAMOS EL NUEVO TARGET EN EL JSON DE MAIL
                            $this->mail->target = null;
                            $this->mail->target = json_encode($target);
                            $this->saveMail($this->mail);
                        } 
                    }
                }             
            }
        }
    }*/
    
      /* Se valida que el mail sea de tipo Editor o html */
      if ($mailContent->typecontent == 'Editor') {
        $mailContent->url = "mail/contenteditor";
        $htmlObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $htmlObj->setAccount($account);
        $htmlObj->assignContent(json_decode($mailContent->content));
        $html = utf8_decode($htmlObj->replacespecialchars($htmlObj->render()));
      } else if ($mailContent->typecontent == "html" || $mailContent->typecontent == "url") {
        $mailContent->url = "mail/htmlcontent";
        $_POST['typecontent'] = $mailContent->url;
        $footerObj = new \Sigmamovil\General\Misc\FooterObj();
        $footerObj->setAccount($account);
        
        $html = utf8_decode($footerObj->addFooterInHtml(html_entity_decode($mailContent->content, ENT_QUOTES)));
        unset($_POST['typecontent']);
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

      if ($this->mail->sentprocessstatus != 'finished') {

        /* Cambia el estado del proceso de envio del mail */
        $this->mail->sentprocessstatus = 'loading-target';
        $this->saveMail($this->mail);
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete(['idMail' => $idMail]);
        $manager->executeBulkWrite('aio.mxc', $bulk);
        

        $interpreter = new InterpreterTargetMail();
        $interpreter->setMail($this->mail);
        $interpreter->customfield($field);
        $interpreter->searchTotalContacts();

      }

      $this->mail->sentprocessstatus = 'finished';
      $this->saveMail($this->mail);

      $mxcCount2 = Mxc::count([["idMail" => $this->mail->idMail]]);
      
      //pregunta si existen contactos a los que se le envio el correo o si el contacto no es nulo.
      if(isset($this->mail->idAutoresponder) && !empty($this->mail->idAutoresponder)){
        if((count($mxcCount2) == 0) || ($mxcCount2 == null)){
          $this->mail->deleted = time();
          $this->saveMail($this->mail);
        }
      }

      
//----------------------- BIEN
      /* Se encarga de recorrer los contactos a los que se les va a enviar el Mail */
      $contactIterator = new ContactIterator($this->mail, $manager);
      
      // Crear transport y mailer
      $transport = Swift_SmtpTransport::newInstance($this->mta->address, $this->mta->port);
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
      $mta = ($mtaName == null || trim($mtaName) === '') ? 'MTA_GENERAL_1' : $mtaName;
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
      if ($this->mail->pdf == 1) {
        $attachment = new AttachmentObject($this->mail, $account, $this->path);
        $attachment->addAttachmentPdf();
        $attach = $attachment->getArratch();
      }

      /* Se carga el asunto del correo */
      $subject = $this->mail->subject;
      $text = $mailContent->plaintext;

      $bulk = new MongoDB\Driver\BulkWrite;
      $arrayIdContacts = array();

//      $nameMailClass = $mailclass->name;
//      if($account->idAccount == 902 || $account->idAccount == 890 || $account->idAccount == 91 || $account->idAccount == 678 || $account->idAccount == 105 || $account->idAccount == 112 || $account->idAccount == 870 || $account->idAccount == 408 || $account->idAccount == 646 || $account->idAccount == 49 || $account->idAccount == 663 || $account->idAccount == 692 || $account->idAccount == 108 || $account->idAccount == 254 || $account->idAccount == 305 || $account->idAccount == 629 || $account->idAccount == 365 || $account->idAccount == 630 || $account->idAccount == 292 || $account->idAccount == 263 || $account->idAccount == 656 || $account->idAccount == 912 || $account->idAccount == 929 || $account->idAccount == 647 || $account->idAccount == 914){
      $nameMailClass = "default";
      $mailReplyto = ((isset($this->mail->idReplyTo))?$this->mail->ReplyTos->email:((isset($this->mail->replyto))?$this->mail->replyto:null));

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
      $amount = $mxcCount * -1;
      $service = $this->services->email_marketing;
      $desc = "El usuario {$user->email} de role {$user->Role->name} con id {$user->idUser}, envió {$mxcCount} CORREOS el dia " .
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
        $htmlWithTracking = $trackingObj->getTrackingUrl($contents["html"], $idMail, $contact->idContact, $links, $this->mail->survey, $idMail, $this->mail->landingpage);

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
        $headers->addTextHeader('Feedback-ID',$trackingID.':'.$contact->idContact.':'.$trIDprefix.':'.$contact->idContact . 'x' . $contact->idMail);
        
        //lo siguiente reemplaza algunas urls que empezaban con 
        //"Https" las cuales eran erroneas por las correctas con minuscula.
        $htmlWithTracking = ereg_replace("Https", "https", $htmlWithTracking);  
        //esto me permite visualizar las imagenes en los gestores de correo.
        
        $message->setFrom($from);
        $message->setBody($htmlWithTracking, 'text/html');
        $message->setTo($to);

        /* Se asigna un responder a, solo si esta definido */
        if ($mailReplyto != null) {
          $message->setReplyTo($mailReplyto);
        }
        $message->addPart($contents['text'], 'text/plain');

        /* Se valida de que el mail tenga archivos adjuntos, si es asi se adjuntan al mail */
        if (is_array($attach) || is_object($attach)){
          foreach ($attach as $key => $at) {
            if($this->mail->pdf == 1){
              if($at->idContact == $contact->idContact){
                $message->attach(
                        Swift_Attachment::fromPath($at->path)->setFilename($at->name)
    //                    Swift_Attachment::fromPath($at->path)->setFilename($this->mail->name)
                );
                unset($attach[$key]);
                $attach = array_values($attach);
              }
            } else if ($this->mail->attachment == 1) {
              $message->attach(
                      Swift_Attachment::fromPath($at->path)->setFilename($at->name)
  //                    Swift_Attachment::fromPath($at->path)->setFilename($this->mail->name)
              );
            }
          }
        }

        //\Phalcon\DI::getDefault()->get('logger')->log($htmlWithTracking);
        /* si todos los pasos anteriores han sido correctos, se realiza el envio */
        $recipients = $swift->send($message, $failures);
        if ($recipients) {
          //Acumula los id de los contactos para ir actualizando los estados de ellos
          $arrayIdContacts[] = $contact->idContact;
          $messagesSent = $messagesSent + 1;
        }

        /* $arrayIdContacts[] = $contact->idContact;
          $messagesSent = $messagesSent + 1; */
        /* Se valida que cada 20 contactos se actualicen los estados a sent de estos */
        if (count($arrayIdContacts) == 500) {
          //$customLogger = \Logs::findFirst([['idMail' => $idMail, 'idContact' => ['$in' => $arrayIdContacts]]]);
          //$customLogger->registerDates = date("Y-m-d h:i:sa");
          //$customLogger->status = 'sent';
          //$customLogger->messagesSent = $messagesSent;
          //$customLogger->save();
          //
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
      \Phalcon\DI::getDefault()->get('logger')->log("Salio del contactIterator Mail {$this->mail->idMail}");
      $count = count($arrayIdContacts);
      \Phalcon\DI::getDefault()->get('logger')->log("Cantidad de arrayIdContacts {$count} Mail {$this->mail->idMail}");
      \Phalcon\DI::getDefault()->get('logger')->log("Cantidad de messagesSent {$messagesSent} Mail {$this->mail->idMail}");

      /* Se valida de que hayan contactos pendientes por cambio de estado, si es asi se actualizan los estados a sent */
      if (count($arrayIdContacts) > 0) {
        //$customLogger = \Logs::findFirst([['idMail' => $idMail, 'idContact' => ['$in' => $arrayIdContacts]]]);
        //$customLogger->registerDates = date("Y-m-d h:i:sa");
        //$customLogger->status = 'sent';
        //$customLogger->messagesSent = $messagesSent;
        //$customLogger->save();
        $bulk->update(['idMail' => $idMail, 'idContact' => ['$in' => $arrayIdContacts]], ['$set' => ['status' => 'sent']], ['multi' => true]);
        $manager->executeBulkWrite('aio.mxc', $bulk);
        $this->mail->quantitytarget = $messagesSent;
        $this->mail->messagesSent = $messagesSent;
        $this->saveMail($this->mail);

        unset($arrayIdContacts);
      }

      /* cuando se finaliza el envio, se cambia el estado del mail a sent */
      if (isset($this->mail->notificationEmails) && $this->mail->notificationEmails != "") {
        $this->sendMailNotification($this->mail);
      }
      $this->mail->quantitytarget = $messagesSent;
      $this->mail->messagesSent = $messagesSent;
      
      if($this->mail->messagesSent == 0){
        throw new \InvalidArgumentException("Ha ocurrido un error: No se entrego ningún correo a sus destinatarios");
      }else{
        $this->mail->status = 'sent'; 
      }
      
      $this->saveMail($this->mail);
      $this->reCountSaxs($this->mail->idMail, $saxs);
      
      $automaticCampaignStep = AutomaticCampaignStep::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $this->mail->idMail]]);
       
      if ($automaticCampaignStep != false){
        if($automaticCampaignStep->beforeStep == 'Primary' || (!empty($automaticCampaignStep) && $automaticCampaignStep->beforeStep == 'Primary')) {
            
          $automaticCampaignStep->status = "sent";
          if (!$automaticCampaignStep->save()) {
              foreach ($automaticCampaignStep->getMessages() as $message) {
                  throw new \InvalidArgumentException($message);
              }
          }
        }
      }      
      return true;
    } catch (Sigmamovil\General\Exceptions\ValidateSaxsException $ex) {
      if($this->validatefailsaxs == false){
          $this->sendMailSaldo();
          $this->mail->status = "paused";
          $this->saveMail($this->mail);
          
          \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
          \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());      
      }else{
          $this->mail->status = "canceled";
          $this->saveMail($this->mail);
          
          \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error {$this->mail->idMail}: " . $ex->getMessage());
          \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
      }

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
    //Se crea una validacion para que los envios lleguen a Green Arrow de Amazon para el Cliete Camara de Comercio.
    $account = $this->mail->Subaccount->Account;
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender($this->mta->address, $this->mta->port);
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
        $systemMail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLink($maildata->idMail, $maildata->idSubaccount, "complete"), $systemMail->content);
        $systemMail->content = str_replace("%LINK_SUMMARY_SENT%", $this->encodeLink($maildata->idMail, $maildata->idSubaccount, "summary"), $systemMail->content);
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
  
  public function getBalanceServiceMail($quantitytarget){
    
    //Se realiza validaciones de los sms programados
    $balance = $this->validateBalanceMail();
    $target = 0;
    if($balance['mailFindPending']){
      foreach ($balance['mailFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $amount = $balance['balanceConsumedFind']['amount'];

    unset($balance);
    $totalTarget =  $amount - $target;
    $target = $target + $quantitytarget;

    if($target>$amount){
      $target = $target - $amount;
      if($totalTarget<=0){
        $tAvailable = (object) ["totalAvailable" => 0];
      } else {
        $tAvailable = (object) ["totalAvailable" => $totalTarget];
      }
      $this->sendmailnotmailbalance($tAvailable);
      $this->mail->status = 'canceled';
      $this->mail->canceleduser = 'Saldo Insuficiente';
      $this->saveMail($this->mail);
      throw new \InvalidArgumentException("No tiene saldo disponible para realizar esta campaña!, su saldo disponlble es {$totalTarget} envios, ya que existen campañas programadas pendientes por enviar.");
    }
    unset($target);
    unset($amount);
    unset($totalTarget);
    unset($tAvailable);
  }
  
  public function validateBalanceMail(){
    $date = date('Y-m-d h:i:s');
    $mailFindPending = \Mail::find(array(
      'conditions' => 'idSubaccount = ?0 and status = ?1 and scheduleDate >= ?2',
      'bind' => array(
        0 => $this->mail->idSubaccount,
        1 => 'scheduled',
        2 => $date
      ),
      'columns' => 'idMail, quantitytarget AS target'  
    ));

    $balanceConsumedFind = \Saxs::findFirst(array(
      'conditions' => 'idSubaccount = ?0 and idServices = ?1 and accountingMode = ?2',
      'bind' => array(
        0 => $this->mail->idSubaccount,
        1 => 2,
        2 => 'sending'
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

    $arrayMailFindPending = [];
    if($mailFindPending != false){
      $arrayMailFindPending = $mailFindPending->toArray();
    }
    $answer = ['mailFindPending'=>$arrayMailFindPending, 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];
    return $answer;
  }
  
  
  public function sendmailnotmailbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == 'sending' && $key->status ==1) {
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
    $sendMailNot->sendMailNotification($this->arraySaxs);
    return true;
  }

}