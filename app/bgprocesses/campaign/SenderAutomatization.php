<?php

use \Sigmamovil\General\Misc\AutomaticCampaignObj;

require_once __DIR__ . '/../../../public/library/php-jwt-master/src/JWT.php';
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/http_load.php");
require_once(__DIR__ . "/../linkservice/LinkServiceAutomatization.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/AttachmentObject.php");
require_once(__DIR__ . "/../sender/InterpreterTarget.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerAutomatization.php");
require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");
require_once(__DIR__ . "/../../logic/PlainText.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerSms.php");



$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$mailSender = new SenderAutomatization();

$mailSender->startSender($id);

class SenderAutomatization {

  public $urlManager,
          $mta,
          $url,
          $adapter,
          $configurationObj,
          $automaticCampaignObj,
          $automaticCampaignStep,
          $automaticCampaignConfiguration,
          $asset,
          $arrayinfobitAnswerChaged;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->mta = \Phalcon\DI\FactoryDefault::getDefault()->get("mtadata");
    $this->asset = \Phalcon\DI\FactoryDefault::getDefault()->get("asset");
    $this->urlManager = $di->get('urlManager');
    $this->db = $di->get('db');
    $this->assetsrv = $di['asset'];
    $this->path = $di['path'];
    $this->kannelProperties = \Phalcon\DI::getDefault()->get('kannelProperties');
    $this->arrayinfobitAnswerChaged = \Phalcon\DI::getDefault()->get('infobitAnswersCharged')->toArray();
  }

  public function getNodeFromItemArray($idItem) {
    return $this->configurationObj->nodes[$idItem];
  }

  public function startSender($idAutomaticCampaignStep) {
    try {
      $this->automaticCampaignStep = AutomaticCampaignStep::findFirst(["conditions" => "idAutomaticCampaignStep = ?0", "bind" => [0 => $idAutomaticCampaignStep]]);
      $this->automaticCampaignConfiguration = AutomaticCampaignConfiguration::findFirst([
                  "conditions" => "idAutomaticCampaign = ?0",
                  "bind" => [
                      0 => $this->automaticCampaignStep->idAutomaticCampaign
                  ]
      ]);
      /*
       * Validaciones del envio 
       */
      if (!$this->automaticCampaignStep) {
        throw new InvalidArgumentException('La campaña enviada no existe');
      }

      $this->automaticCampaignObj = new AutomaticCampaignObj($this->automaticCampaignConfiguration->AutomaticCampaign, $this->automaticCampaignConfiguration);
      $this->configurationObj = json_decode($this->automaticCampaignConfiguration->configuration);

      $nodeNow = $this->automaticCampaignObj->getNode($this->automaticCampaignStep->idNode);
      //var_dump($nodeNow->method == "email");exit();	
      
      if ($nodeNow->method == "sms") {
        $this->automaticCampaignStep->statusSms = "sending";
        $this->save($this->automaticCampaignStep);
        $this->startSms();
//      $this->automaticCampaignObj->uptStatusStep($this->automaticCampaignStep, "delivered");
      } else if ($nodeNow->method == "email") {
//      $this->automaticCampaignObj->uptStatusStep($this->automaticCampaignStep, "sent");
        $this->startMail($this->automaticCampaignStep);
      } else {
        throw new Exception("Ha habido un error con el paso {$this->automaticCampaignStep->idAutomaticCampaignStep} enviada verifique la informacón enviada");
      }

      $connection = $this->automaticCampaignObj->searchConnection($nodeNow->id);

      if (isset($connection["dest"])) {
        $nextOperator = $this->automaticCampaignObj->getNode($connection["dest"]);
        $beforeStep = $this->automaticCampaignObj->getBeforeStep($nextOperator);
        if ($nextOperator->method == "actions") {
          $negation = json_decode($this->automaticCampaignObj->searchNegation($nextOperator->id));
          for ($i = 0; $i < count($negation); $i++) {
            if ($negation[$i]->dest->class == "negation") {
              $nodeNegation = new \stdClass();
              $nodeNegation->idNode = $negation[$i]->dest->idNode;
              $nodeNegation->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
              $nodeNegation->date = $this->automaticCampaignObj->getDataTime($negation[$i]->dest);
              $nodeNegation->beforeStep = $beforeStep;
            } else {
              if ($beforeStep == "no clic" || $beforeStep == "no open") {
                $nodeSuccess = new \stdClass();
                $nodeSuccess->idNode = $negation[$i]->dest->idNode;
                $nodeSuccess->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
                $nodeSuccess->date = $this->automaticCampaignObj->getDataTime($nextOperator);
                $nodeSuccess->beforeStep = $beforeStep;
              }
            }
          }
          if (isset($nodeSuccess) || isset($nodeNegation)) {
            if ($beforeStep == "no clic" || $beforeStep == "no open") {
              $this->automaticCampaignObj->insNewStep($this->automaticCampaignStep->idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
            } else {
              if (isset($nodeNegation)) {
                $this->automaticCampaignObj->insNewStep($this->automaticCampaignStep->idContact, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
              }
            }
          }
        } else if ($nextOperator->method == "time") {
          $connection = $this->automaticCampaignObj->searchConnection($nextOperator->id);
          $node = $this->automaticCampaignObj->getNode($connection["dest"]);
          $nodeSuccess = new \stdClass();
          $nodeSuccess->idNode = $connection["dest"];
          $nodeSuccess->node = $node;
          $nodeSuccess->date = $this->automaticCampaignObj->getDataTime($nextOperator);
          $nodeSuccess->beforeStep = $beforeStep;
          $this->automaticCampaignObj->insNewStep($this->automaticCampaignStep->idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
        }
      }
    } catch (InvalidArgumentException $ex) {
      //\Phalcon\DI::getDefault()->get('logger')->log("InvalidArgumentException SenderAutomatization: " . $ex->getMessage());
      //\Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $exc) {
      //\Phalcon\DI::getDefault()->get('logger')->log("Exception SenderAutomatization: " . $exc->getMessage());
      //\Phalcon\DI::getDefault()->get('logger')->log($exc->getTrace());
    }
  }

  public function startSms() {
    try {
      $account = $this->automaticCampaignStep->AutomaticCampaign->Subaccount->Account;

      /*
       * El adaptador se esta pasando el primero de la lista mientras se ve la forma de como seleccionarlo
       * Esta forma se encuentra en SenderAutomatization.php y SmsSender.php
       */
      foreach ($account->accountConfig->detailConfig as $key) {
        if ($key->idServices == 1) {
          $this->adapter = $key->dcxadapter[0]->Adapter;
        }
      }

      $smsTemplate = SmsTemplate::findFirst([
                  "conditions" => "idSmsTemplate = ?0",
                  "bind" => [0 => $this->automaticCampaignStep->idSmsTemplate]
      ]);

      $customfieldManagerSms = new CustomfieldManagerSms(null, $this->automaticCampaignStep->idContact);
      $field = $customfieldManagerSms->searchCustomfieldForContact($smsTemplate->content);

      $contact = Contact::findFirst([["idContact" => (int) $this->automaticCampaignStep->idContact]]);
      if (!isset($contact->phone) && !isset($smsTemplate->content)) {
        //EXCEPCION
      }
      $customfielContact = $customfieldManagerSms->findCustomField($contact->idContact);
      $contentSms = $customfieldManagerSms->processCustomFields($contact, $field, $smsTemplate->content, $customfielContact);
      
      $batch = array();
      $message = array(
        "from" => "SIGMA-MOVIL",
        "to" => "{$contact->indicative}{$contact->phone}",
        "text" => $contentSms,
      );
      
      
      $curl = curl_init();
   
      $data = json_encode($message);
      $key = base64_encode("SigmaMo22:Xls7sms71");
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.infobip.com/sms/1/single",
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{$data}",
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Content-Type: application/json"
        )
        ));
       
        $response = curl_exec($curl);
        $error = curl_error($curl);
       
        curl_close($curl);

        $res = json_decode($response);
        
        
//      $this->prepareUrl();
//      $this->createURL($contact->phone, $contentSms);
      $response = $res->messages[0]->status->name;
//var_dump($res->messages[0]->status->name);exit();
      $this->automaticCampaignStep->status = null;
      $this->automaticCampaignStep->responseSms = $response;
      //if ($response == "PENDING_ENROUTE") {
      if (in_array($response, $this->arrayinfobitAnswerChaged)) {
        $this->automaticCampaignStep->statusSms = "delivered";
      } else {
        $this->automaticCampaignStep->statusSms = "undelivered";
      }
      $this->save($this->automaticCampaignStep);
    } catch (InvalidArgumentException $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Invalidargumenexception sms automatic: " . $e->getMessage());
    } catch (Firebase\JWT\SignatureInvalidException $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("JWT sms automatic:  " . $e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception sms automatic: " . $e->getMessage());
      echo $e->getTraceAsString();
    }
  }

  public function startMail($automaticCampaignStep) {

//    $account = $automaticCampaignStep->AutomaticCampaign[0]->Subaccount->Account;
    $account = $automaticCampaignStep->AutomaticCampaign->Subaccount->Account;

    foreach ($account->accountConfig->detailConfig as $key) {
      if ($key->idServices == 2) {
        $domain = $key->dcxurldomain[0]->urldomain;
      }
    }

    /* Se busca el contenido del mail */
    $mailContent = MailTemplateContent::findFirst(array(
                'conditions' => 'idMailTemplate = ?0',
                'bind' => array(0 => $automaticCampaignStep->idMailTemplate)
    ));

    /*
     * Validaciones
     */
    if (trim($mailContent->content) === '') {
      throw new \InvalidArgumentException("El contenido de la campaña se encuentra vacio por"
      . " favor valide la información enviada");
    }

    $automaticConfiguration = AutomaticCampaignConfiguration::findFirst([
                "conditions" => "idAutomaticCampaign = ?0",
                "bind" => [0 => $automaticCampaignStep->idAutomaticCampaign]
    ]);
    $automaticObj = new Sigmamovil\General\Misc\AutomaticCampaignObj($automaticConfiguration->AutomaticCampaign, $automaticConfiguration);
    $nodo = $this->automaticCampaignObj->getNode($automaticCampaignStep->idNode);

    //CAMBIAR ESTADO A SENDING
    $this->automaticCampaignObj->uptStatusStep($automaticCampaignStep, "sending");
   
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
//    $automaticCampaignStep->status = 'sending';
//    $this->save($automaticCampaignStep);
//    $mailContent->url = "mail/contenteditor";
    $htmlObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $htmlObj->setAccount($account);
    $htmlObj->assignContent(json_decode($mailContent->content));
    $html = utf8_decode($htmlObj->replacespecialchars($htmlObj->render()));
    /* Se preparan los links y las imagenes del contenido del Mail */
//    $this->moveassetalliedtoassetaccount($automaticCampaignStep->idMailTemplate, $account->idAccount);
    $imageService = new ImageService($account, $domain, $this->urlManager);
    $linkService = new LinkServiceAutomatization($account, $automaticCampaignStep);
    $prepareMail = new PrepareMailContent($linkService, $imageService);
    list($contents, $links) = $prepareMail->processContent($html);
   
    /* Se valida que el contenido del mail tenga campos personalizado y si es asi se preparan y se reemplazan en el contenido del Mail */
    $FirstNode = $this->automaticCampaignObj->getNode(0);

 
 $customfieldManager = new CustomfieldManagerAutomatization($automaticCampaignStep, $this->urlManager, $automaticCampaignStep->AutomaticCampaign->Subaccount);

 $customfieldManager->transformTarget($FirstNode->sendData);
    
    $customfieldManager->setPlainTextObj(new PlainText());

    $customfieldManager->setPlainText($contents);
    $customfieldManager->setSubject($nodo->sendData->subject);
    //$content = $customfieldManager->prepareUpdatingForms($contents);
    $field = $customfieldManager->searchCustomFields();
    // Crear transport y mailer
    $transport = Swift_SmtpTransport::newInstance($this->mta->address, $this->mta->port);
    $swift = Swift_Mailer::newInstance($transport);


    if ($nodo->method == "email") {
      $emailSender = $nodo->sendData->senderEmail->email;
      $nameSender = $nodo->sendData->senderName->name;    
}
    
    $from = array($emailSender => $nameSender); 
     
    $trackingObj = new TrackingUrlObject();

    $mailclass = Mailclass::findFirstByIdMailClass($automaticCampaignStep->AutomaticCampaign->Subaccount->Account->Accountclassification->idMailClass);
   
    $prefixID = Phalcon\DI::getDefault()->get('instanceIDprefix')->prefix;
    
 if (!$prefixID || $prefixID == '') {
      $prefixID = '0ac';
    }
    
    $today = date('d');
    $month = date('m');
    $listID = 't' . $prefixID . $automaticCampaignStep->AutomaticCampaign->Subaccount->Account->idAccount;
    $sendID = $prefixID . $today . $month . $automaticCampaignStep->idAutomaticCampaign;
    //\Phalcon\DI::getDefault()->get('logger')->log("sendID: " . $sendID );
    // MTA a utilizar
    $mtaName = $automaticCampaignStep->AutomaticCampaign->Subaccount->Account->Accountclassification->Mta->name;
//    var_dump("mta " . $mtaName);
    $mta = ($mtaName == null || trim($mtaName) === '') ? 'CUST_SIGMA' : $mtaName;

    // Prefijo de tracking ID
    $trIDprefix = 'ac' . $automaticCampaignStep->idAutomaticCampaignStep . 'x';
    
    /* Se carga el asunto del correo */
    $subject = $nodo->sendData->subject;
//    $text = $mailContent->plaintext;
    $nameMailClass = $mailclass->name;
    $mailReplyto = $nodo->sendData->replyto;
    
    $contact = Contact::findFirst([[
            "idContact" => (int) $automaticCampaignStep->idContact
    ]]); 
    
    $contents = $customfieldManager->processCustomFields($contact, $field);

    $htmlWithTracking = $trackingObj->getTrackingUrlAutomatization($contents["html"], $automaticCampaignStep->idAutomaticCampaignStep, $contact->idContact, $links);

    $toNameT = trim($contact->name . ' ' . $contact->lastname);
    $toName = (!$toNameT || $toNameT == '') ? $contact->email : $toNameT;
    $to = array($contact->email => $toName);

    $message = new Swift_Message($subject);
    $message->setEncoder(Swift_Encoding::get8BitEncoding());
    $headers = $message->getHeaders();

    $trackingID = $trIDprefix . $contact->idContact . 'x' . $contact->idMail;
    $headers->addTextHeader('X-GreenArrow-MailClass', $nameMailClass);
    $headers->addTextHeader('X-GreenArrow-MtaID', $mta);
    $headers->addTextHeader('X-GreenArrow-InstanceID', $sendID);
    $headers->addTextHeader('X-GreenArrow-Click-Tracking-ID', $trackingID);
    $headers->addTextHeader('X-GreenArrow-ListID', $listID);
//    $headers->addTextHeader('List-Unsubscribe', $trackingObj->getUnsubscribeLink());
    $message->setFrom($from);
    /*
     * el contenido no se esta acomodando aun
     */
    $message->setBody($htmlWithTracking, 'text/html');
//    $message->setBody($htmlWithTracking, 'text/html');
    $message->setTo($to);
    /* Se asigna un responder a, solo si esta definido */
    if ($mailReplyto != null) {
      $message->setReplyTo($mailReplyto);
    }

    $recipients = $swift->send($message, $failures);
    //CAMBIAR ESTADO A SENT
    $automaticObj->uptStatusStep($automaticCampaignStep, "sent");
  }

  public function prepareUrl() {
    $pass = $this->jwtDecode($this->adapter->passw);
    $this->url['pre'] = $this->kannelProperties->baseUrl .
            'username=' . $this->adapter->uname .
            '&password=' . $pass .
            '&smsc=' . $this->adapter->fname .
            '&coding=' . $this->adapter->coding;
    if ($this->adapter->usedlr) {
      $this->url['pos'] = $this->kannelProperties->dlrURL;
    }
  }

  public function createURL($phone, $msg) {
    $url = "";
    $url .= '&from=' . urlencode($this->adapter->smscid);
    $url .= '&to=' . urlencode($phone);
    $url .= '&text=' . urlencode($msg);
//    if (isset($this->url['pos'])) {
//      $url .= '&dlr-mask=7&dlr-url=' . urlencode($this->url['pos'] . $this->idJobBatch);
//    }
    $this->url['pre'] .= $url;
  }

  public function sendSms($url) {
    echo 'Inicio del proceso de envío' . PHP_EOL;
    $urlRequest = $url;
    echo 'URL: ' . $urlRequest . PHP_EOL;
    /*     * * DO THE ACTUAL SEND ** */
    $resultXML = $this->doHTTPrequest($urlRequest, array());
    return $resultXML;
  }

  /**
   * This method sends the request to Kannel, and captures the results
   * @param $urlReq string
   * @param $urlPar array
   * @return string
   */
  private function doHTTPrequest($urlReq, $urlPar) {

    $response = http_load($urlReq, array('return_info' => true));

    echo 'Obteniendo respuesta' . PHP_EOL;

    print_r($response);

    if (key_exists('body', $response))
      return $response['body'];

    return null;
  }

  public function moveassetalliedtoassetaccount($idMailTemplate, $idAccount) {
    $mailTemplate = MailTemplate::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => [0 => $idMailTemplate]]);
    $imagens = \MailTemplateImage::find(array("conditions" => "idMailTemplate = ?0", "bind" => array($mailTemplate->idMailTemplate)));
    if (!$imagens) {
      return true;
    }

    //Falta la condicion si es de account
    if (isset($mailTemplate->idAllied)) {
      $baseDir = "../../" . $this->asset->dirAllied . $mailTemplate->mailTemplate->idAllied . "/images/";
    } else {
      $baseDir = "../../" . $this->asset->dirRoot . "/images/";
    }

    $baseDirAccount = "../../" . $this->asset->dir . $idAccount;
    $baseDirAccount2 = $baseDirAccount . "/images";
    if (!file_exists($baseDirAccount2)) {
      if (!mkdir($baseDirAccount2, 0755, true)) {
        throw new InvalidArgumentException("Ocurrio un problema creando la carpeta {$baseDirAccount2}");
      }
    }
    foreach ($imagens as $image) {
      $asset = new Asset();
      $asset->idAccount = $idAccount;
      $asset->name = $image->asset->name;
      $asset->size = $image->asset->size;
      $asset->type = $image->asset->type;
      $asset->contentType = $image->asset->contentType;
      $asset->dimensions = $image->asset->dimensions;
      $asset->extension = $image->asset->extension;
      if (!$asset->save()) {
        foreach ($asset->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      $dirAccount = $baseDirAccount . "/images/" . $asset->idAsset . "." . $asset->extension;

      $dir = $baseDir . $image->idAsset . "." . $image->asset->extension;

//      if (file_exists($dirAccount)) {
//        continue;
//      }
//
//      if (!copy($dir, $dirAccount)) {
//        throw new InvalidArgumentException("Ocurrio un error pasando la imagen {$dir} a {$dirAccount}");
//      }
//      
//      
//
//      $dirAccount2 = $baseDirAccount . "/images/" . $asset->idAsset . "_thumb.png";
//      $dir2 = $baseDir . $image->idAsset . "_thumb.png";
////
//      if (!copy($dir2, $dirAccount2)) {
//        throw new InvalidArgumentException("Ocurrio un error pasando la_thumb {$dir} a {$dirAccount2}");
//      }
    }
  }

  /**
   * Metodo encargado de desencriptar el password
   * 
   * @param String $payload
   * @return String
   */
  public function jwtDecode($payload) {
    $pass = \Firebase\JWT\JWT::decode($payload, $this->kannelProperties->keyjwt, ["HS256"]);
//    $pass = Firebase\JWT\JWT::decode($payload, $this->kannelProperties->keyjwt, ["HS256"]);
    return $pass;
  }

  public function save($obj) {
    if (!$obj->save()) {
      foreach ($obj->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

}
