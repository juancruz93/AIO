<?php

/**
 * Description of StartAutomaticCampaignSender
 *
 * @author jose.quinones
 */
use Sigmamovil\General\Automatic\TemplateMailManager;
use \Sigmamovil\General\Misc\AutomaticCampaignObj;

require_once(__DIR__ . "/../bootstrap/index.php");

if (isset($argv[1])) {
  $idAutomaticCampaign = $argv[1];
}

$startCampaign = new StartAutomaticCampaignSender();
$startCampaign->startAutomatic($idAutomaticCampaign);

class StartAutomaticCampaignSender {
  
  public $campaign;
  public $configuration;
  public $objCampaignConfiguration;
  public $validatorCampaign;
  public $templateMailManager;
  public $automaticCampaignObj;
  public $subAccount;
  public $TemplateMailManager;
  public $idContactlist = array();
  public $idSegment = array();
  public $inIdcontact = array();
  public $mail;
  public $sms;
  public $startDate;
  public $target;
  public $data = array();
  public $link;
  public $linkidNode; 

  public function startAutomatic($idAutomaticCampaign) {
    try {
      //Consultamos la configuracion de la campaña automatica
      $campaignConfiguration = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));
      if (!$campaignConfiguration) {
        throw new Exception("no existe la configuracion de la campaña automatica");
      }
      if($campaignConfiguration->AutomaticCampaign->status != "confirmed"){
        throw new Exception("la campaña no esta programada.");
      }
      $this->campaign = $campaignConfiguration->AutomaticCampaign;
      $this->campaign->status = "executing";
      $this->startDate = $this->campaign->startDate;
      //Cambiamos el estado
      $this->saveAutomatic($this->campaign);
      $this->configuration = json_decode($campaignConfiguration->configuration);
      //
      $this->objCampaignConfiguration = json_decode($campaignConfiguration->configuration);
      $this->validatorCampaign = new \CampaignValidator(json_decode($campaignConfiguration->configuration));
      $this->templateMailManager = new TemplateMailManager($this->campaign);
      $this->automaticCampaignObj = new AutomaticCampaignObj($this->campaign, $campaignConfiguration);
      $this->setSubAccount($this->campaign->Subaccount);
      //
      $this->CloneTemplate();
    } catch (InvalidArgumentException $ex) {
      $this->campaign->status = 'canceled';
      $this->saveAutomatic($this->campaign);
      echo $ex->getMessage();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error start1: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      $this->campaign->status = 'canceled';
      $this->saveAutomatic($this->campaign);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error start2: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }
  
  public function saveAutomatic($objSave) {
    if (!$objSave->save()) {
      foreach ($objSave->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function setSubAccount(\Subaccount $subAccount) {
    $this->subAccount = $subAccount;
    $this->templateMailManager->setSubAccount($subAccount);
  }
  
  public function CloneTemplate(){
    $this->validatorCampaign->setSubAccount($this->subAccount);
    $this->validatorCampaign->validate();
    $FirstNode = $this->automaticCampaignObj->getNode(0);
    $firstConnection = $this->automaticCampaignObj->searchConnection(0);
    $SecondNode = $this->automaticCampaignObj->getNode($firstConnection["dest"]);
    $target = $this->automaticCampaignObj->transformTarget($FirstNode->sendData);
    if ($SecondNode->method == "email") {
      $this->templateMailManager->setTarget($target);
      $this->templateMailManager->setDataMail($SecondNode->sendData);
      $this->templateMailManager->setContentTemplate();
      $target = json_decode($target);
      switch ($target->type) {
        case "contactlist":
          $this->getIdContaclist($target->contactlists);
          $this->getAllCxclMail();
          break;
        case "segment":
          $this->getIdSegment($target->segment);
          $this->getAllIdContactSegmentMail();
          break;
        default:
      }
      $this->target = json_encode($target);
      $this->templateMailManager->setInidContact($this->inIdcontact);
      $this->mail = $this->templateMailManager->cloneMail();
      //Se realiza el registro del Nodo Primario
      $automatic = new \AutomaticCampaignStep();
      $automatic->idNode = $SecondNode->id;
      $automatic->idAutomaticCampaign = $this->campaign->idAutomaticCampaign;
      $automatic->idMail = $this->mail->idMail;
      $automatic->status = "scheduled";
      $automatic->scheduleDate = $this->campaign->startDate;
      $automatic->negation = 0;
      $automatic->beforeStep = "Primary";
      $automatic->createdBy = $this->campaign->createdBy;
      if (!$automatic->save()) {
        foreach ($automatic->getMessages() as $msg) {
          throw new \InvalidArgumentException($msg);
        }
      }
      //
      $this->automaticCampaignObj->getNodeUpdate($automatic->idNode, $this->campaign->startDate, $automatic->idMail, '');
      $this->newNextStepCA($SecondNode);
    } else if ($SecondNode->method == "sms") {
      $this->templateMailManager->setTargetSms($target);
      $this->templateMailManager->setDataMail($SecondNode->sendData);
      $this->templateMailManager->setContentTemplateSms();
      $target = json_decode($target);
      switch ($target->type) {
        case "contactlist":
          $this->getIdContaclist($target->contactlists);
          $this->getAllCxclSms();
          break;
        case "segment":
          $this->getIdSegment($target->segment);
          $this->getAllIdContactSegmentSms();
          break;
        default:
      }
      $this->target = json_encode($target);
      $this->templateMailManager->setInidContact($this->inIdcontact);
      $this->sms = $this->templateMailManager->cloneSms();
      //Se realiza el registro del Nodo Primario
      $automatic = new \AutomaticCampaignStep();
      $automatic->idNode = $SecondNode->id;
      $automatic->idAutomaticCampaign = $this->campaign->idAutomaticCampaign;
      $automatic->idSms = $this->sms->idSms;
      $automatic->status = "scheduled";
      $automatic->scheduleDate = $this->campaign->startDate;
      $automatic->negation = 0;
      $automatic->beforeStep = "Primary";
      $automatic->createdBy = $this->campaign->createdBy;
      if (!$automatic->save()) {
        foreach ($automatic->getMessages() as $msg) {
          throw new \InvalidArgumentException($msg);
        }
      }
      //
      $this->automaticCampaignObj->getNodeUpdate($automatic->idNode, $this->campaign->startDate, '', $automatic->idSms);
      $this->newNextStepCA($SecondNode);
    }
    /*foreach ($configuration as $key => $value){
      if ($key == "nodes") {
        foreach ($value as $valueNode) {
          $this->validateNode($valueNode);
        }
      }
    }
    return true;*/
  }
    
  public function getIdContaclist($arrContactList){
    foreach($arrContactList as $contactList) {
      $idContactlist = $contactList->idContactlist;
      $consultContactList = \Contactlist::findFirst(array("conditions" => "idSubaccount = ?0 and idContactlist = ?1 and deleted =?2", "bind" => array(0 => $this->subAccount->idSubaccount, 1 => $idContactlist, 2 => 0)));
      if (!$consultContactList) {
        throw new \InvalidArgumentException("La lista de contacto '{$contactList->name}' ha sido eliminado por favor verifique la información.");
      }
      $this->idContactlist[] = $idContactlist;
    }
    unset($arrContactList);
    unset($idContactlist);
    unset($consultContactList);
  }
  
  public function getIdSegment($arrSegment){
    foreach($arrSegment as $segment) {
      $idSegment = (int) $segment->idSegment;
      //$consultSegment = \Segment::findFirst(array("conditions" => "idSubaccount = ?0 and idSegment = ?1 and deleted =?2", "bind" => array(0 => $this->subAccount->idSubaccount, 1 => $idSegment, 2 => 0)));
      $consultSegment = \Segment::findFirst([["idSubaccount" => $this->subAccount->idSubaccount, "idSegment" => $idSegment, "deleted" => 0]]);
      if (!$consultSegment) {
        throw new \InvalidArgumentException("La lista de contacto '{$segment->name}' ha sido eliminado por favor verifique la información.");
      }
      $this->idSegment[] = $idSegment;
    }
    unset($arrSegment);
    unset($idSegment);
    unset($consultSegment);
  }
  
  public function getAllCxclMail() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 "
      . " AND spam = 0 "
      . " AND bounced = 0 "
      . " AND blocked = 0 "
      . " AND singlePhone = 0";
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    };
    unset($sql);
    unset($cxcl);
  }
  
  public function getAllCxclSms() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 ";
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    };
    unset($sql);
    unset($cxcl);
  }
  
  public function getAllIdContactSegmentMail() {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new MongoDB\Driver\Command([
      'aggregate' => 'sxc',
      'pipeline' => [
          ['$match' => ['idSegment' => ['$in' => $this->idSegment],'idContact' => ['$in' => $this->inIdcontact],'email' => ['$nin' => ["", null, "null"]] ]],
          ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]]
      ],
      'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    unset($this->inIdcontact);
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      $this->inIdcontact[$i] = $segment[0]->result[$i]->_id;
    }
    unset($command);
    unset($segment);
  }
  
  public function getAllIdContactSegmentSms() {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new MongoDB\Driver\Command([
      'aggregate' => 'sxc',
      'pipeline' => [
          ['$match' => ['idSegment' => ['$in' => $this->idSegment],'idContact' => ['$in' => $this->inIdcontact],'phone' => ['$nin' => ["", null, "null"]] ]],
          ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]]
      ],
      'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    unset($this->inIdcontact);
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      $this->inIdcontact[$i] = $segment[0]->result[$i]->_id;
    }
    unset($command);
    unset($segment);
  }
  
  public function newNextStep($step) {
    /* Se preparan los contactos para realizar el envio */
    $connection = $this->automaticCampaignObj->searchConnection($step->id);
    $nextOperator = $this->automaticCampaignObj->getNode($connection["dest"]);
    $beforeStep = $this->automaticCampaignObj->getBeforeStep($nextOperator);

    // Se valida que tipo de metodo es

    if ($nextOperator->method == "primary") {
      $connectionPrimary = $this->automaticCampaignObj->searchConnection($nextOperator->id);
      $nextOperatorPrimary = $this->automaticCampaignObj->getNode($connectionPrimary["dest"]);
      //$this->newNextStep($nextOperatorPrimary);
    } else if ($nextOperator->method == "actions") {
      $date = $this->getDataTime($nextOperator);
      $this->data[$nextOperator->id] = ["startDate" => $date, "idMail" => $this->mail->idMail, "type" => $nextOperator->method];
      $negation = json_decode($this->automaticCampaignObj->searchNegation($nextOperator->id));
      //PREGUNTA LA CONDICION DE LA ACCION, SI EL SIGUIENTE NODO ESTA CONECTADO EN NO O EN SI
      for ($i = 0; $i < count($negation); $i++) {
        if ($negation[$i]->dest->class == "negation") {
          $nodeNegation = new \stdClass();
          $nodeNegation->idNode = $negation[$i]->dest->idNode;
          $nodeNegation->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeNegation->date = $date;
          if($beforeStep == 'clic'){
           $this->link = $nextOperator->sendData->linksTemplateSelected->name;
           $this->linkidNode = $nodeNegation->idNode; 
          }
          $nodeNegation->beforeStep = $beforeStep != "open clic" ? $beforeStep : "no open clic";
          $this->insNewStep($nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date,1);
          $this->newNextStep($nodeNegation->node);
        } else if ($negation[$i]->dest->class == "success") {
          $nodeSuccess = new \stdClass();
          $nodeSuccess->idNode = $negation[$i]->dest->idNode;
          $nodeSuccess->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeSuccess->date = $date;
          if($beforeStep == 'clic'){
           $this->link = $nextOperator->sendData->linksTemplateSelected->name;
           $this->linkidNode = $nodeSuccess->idNode;
          }
          $nodeSuccess->beforeStep = $beforeStep;
          if($nodeSuccess->node->method == "clicks") {
            $this->data[$nodeSuccess->idNode] = ["startDate" => $date, "idMail" => $this->mail->idMail, "type" => $nodeSuccess->node->method];
            //
      $connection = $this->automaticCampaignObj->searchConnection($nodeSuccess->idNode);
        $nextOperator = $this->automaticCampaignObj->getNode($connection["dest"]);
        //
        $nodeSuccess->idNode = $nextOperator->id;
            $nodeSuccess->node = $this->automaticCampaignObj->getNode($nextOperator->id);
            $nodeSuccess->beforeStep = "open clic";
      }
          $this->insNewStep($nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
          $this->newNextStep($nodeSuccess->node);
        } else {
          if ($beforeStep == "no clic" || $beforeStep == "no open") {
            $nodeSuccess = new \stdClass();
            $nodeSuccess->idNode = $negation[$i]->dest->idNode;
            $nodeSuccess->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
            $nodeSuccess->date = $date;
            $nodeSuccess->beforeStep = $beforeStep;
            $this->insNewStep($nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
          }
        }
      }
      if (isset($nodeSuccess) || isset($nodeNegation)) {
        if ($beforeStep == "no clic" || $beforeStep == "no open") {
          $this->insNewStep($nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
        }
      }
    } else if ($nextOperator->method == "time") {
      $connection = $this->automaticCampaignObj->searchConnection($nextOperator->id);
      $date = $this->getDataTime($nextOperator);
      $this->data[$nextOperator->id] = ["startDate" => $date, "idMail" => $this->mail->idMail, "type" => $nextOperator->method];
      $node = $this->automaticCampaignObj->getNode($connection["dest"]);
      $nodeSuccess = new \stdClass();
      $nodeSuccess->idNode = $connection["dest"];
      $nodeSuccess->node = $node;
      $nodeSuccess->date = $date;
      $nodeSuccess->beforeStep = $beforeStep;
      $this->startDate = $nodeSuccess->date;
      $this->insNewStep($nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
      $this->newNextStep($nodeSuccess->node);
    } else if ($nextOperator->method == "clicks") {
      $date = $this->getDataTime($nextOperator);
      $this->data[$nextOperator->id] = ["startDate" => $date, "idMail" => $this->mail->idMail, "type" => $nextOperator->method];
      $negation = json_decode($this->automaticCampaignObj->searchNegation($nextOperator->id));
      for ($i = 0; $i < count($negation); $i++) {
        if ($negation[$i]->dest->class == "success") {
          $nodeSuccess = new \stdClass();
          $nodeSuccess->idNode = $negation[$i]->dest->idNode;
          $nodeSuccess->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeSuccess->date = $date;
          if($beforeStep == 'links'){
           $this->link = $nextOperator->sendData->linksTemplateSelected->name;
           $this->linkidNode = $nodeSuccess->idNode;
          }
          $nodeSuccess->beforeStep = $beforeStep;
          $this->newNextStep($nodeNegation->node);
        } else if ($negation[$i]->dest->class == "negation") {
          $nodeNegation = new \stdClass();
          $nodeNegation->idNode = $negation[$i]->dest->idNode;
          $nodeNegation->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeNegation->date = $date;
          $nodeNegation->beforeStep = $beforeStep;
          $this->newNextStep($nodeNegation->node);
        }
      }
    } else if ($nextOperator->method == "links") {
      $date = $this->getDataTime($step);
      $negation = json_decode($this->automaticCampaignObj->searchNegation($nextOperator->id));
      for ($i = 0; $i < count($negation); $i++) {
        if ($negation[$i]->dest->class == "success") {
          $nodeSuccess = new \stdClass();
          $nodeSuccess->idNode = $negation[$i]->dest->idNode;
          $nodeSuccess->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeSuccess->date = $date;
          if($beforeStep == 'clic'){
           $this->link = $nextOperator->sendData->text;
           $this->linkidNode = $nodeSuccess->idNode;
          }
          $nodeSuccess->beforeStep = $beforeStep;
          $this->insNewStep($nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
        } else if ($negation[$i]->dest->class == "negation") {
          $nodeNegation = new \stdClass();
          $nodeNegation->idNode = $negation[$i]->dest->idNode;
          $nodeNegation->node = $this->automaticCampaignObj->getNode($negation[$i]->dest->idNode);
          $nodeNegation->date = $date;
          if($beforeStep == 'clic'){
           $this->link = $nextOperator->sendData->text;
           $this->linkidNode = $nodeNegation->idNode;
          }
          $nodeNegation->beforeStep = $beforeStep;
          $this->insNewStep($nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date,1);
          $this->newNextStep($nodeNegation->node);
        }
      }
    }
  }
  
  public function insNewStep($proxNode, $proxNodeObj, $beforeStep, $node, $negation = 0) {
    $automaticD = new \AutomaticCampaignStep();
    $automaticD->idNode = $proxNode;
    $automaticD->idAutomaticCampaign = $this->campaign->idAutomaticCampaign;
    if ($proxNodeObj->method == "email") {
      $mail = $this->cloneMailCA($this->campaign, $proxNodeObj, $beforeStep, $negation, $node, $proxNode);
      $automaticD->status = "scheduled";
      $automaticD->idMail = $mail->idMail;
      $this->mail = $mail;
      $automaticD->scheduleDate = $mail->scheduleDate;
      //
      $this->automaticCampaignObj->getNodeUpdate($automaticD->idNode, $automaticD->scheduleDate, $automaticD->idMail, '');
    } else if ($proxNodeObj->method == "sms") {
      $sms = $this->cloneSmsCA($this->campaign, $proxNodeObj, $beforeStep, $negation, $node, $proxNode);
      $automaticD->status = "scheduled";
      $automaticD->idSms = $sms->idSms;
      $this->sms = $sms;
      $automaticD->scheduleDate = $sms->startdate;
      //
      $this->automaticCampaignObj->getNodeUpdate($automaticD->idNode, $automaticD->scheduleDate, '', $automaticD->idSms);
    }
    $automaticD->status = "scheduled";
    $automaticD->negation = $negation;
    $automaticD->beforeStep = $beforeStep;
    $automaticD->createdBy = $this->campaign->createdBy;
    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function cloneMail($campaign,$proxNodeObj,$beforeStep,$date,$negation, $proxNode = null) {
    $stepId = $this->searchSourcesAndDest($proxNodeObj->id);
    if($this->data[$stepId]){
      $idMail = $this->data[$stepId]['idMail'];
    } else {
      $idMail = $this->mail->idMail;
    }
    $target = "";
    if($beforeStep == 'time'){
      if($this->mail->idMail){
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filters":[{"typeFilters":1,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":""}],"condition":"all"}';
      }
      if($this->sms->idSms){
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filterSms":[{"typeFilters":1,"smsSelected":"'.$this->sms->idSms.'"}],"condition":"all"}';
      }
    }
    if($beforeStep == 'open'){
      if($negation){
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filters":[{"typeFilters":2,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":true}],"condition":"all"}';
      } else {
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filters":[{"typeFilters":2,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":""}],"condition":"all"}';
      }
    }
    if($beforeStep == 'clic'){
        if($this->linkidNode === $proxNode){
            if($negation){
            $target = substr($this->target, 0, strlen($this->target) - 1);
            $target = $target.',"filters":[{"typeFilters":3,"mailSelected":"'.$idMail.'","linkSelected": "","link_ac":"'.$this->link.'","mail":[],"links":[{"idMail_link":"","link":"'.$this->link.'","idMail":"'.$idMail.'"}],"inverted":true}],"condition":"all"}';
          } else {
            $target = substr($this->target, 0, strlen($this->target) - 1);
            $target = $target.',"filters":[{"typeFilters":3,"mailSelected":"'.$idMail.'","linkSelected": "","link_ac":"'.$this->link.'","mail":[],"links":[{"idMail_link":"","link":"'.$this->link.'","idMail":"'.$idMail.'"}],"inverted":""}],"condition":"all"}';
          }
        }  
    }
    unset($this->link, $this->linkidNode);
    $mail = new \Mail();
    $mail->idSubaccount = $campaign->idSubaccount;
    $mail->idEmailsender = $proxNodeObj->sendData->senderEmail->idEmailsender;
    $mail->idNameSender = $proxNodeObj->sendData->senderName->idNameSender;
    $mail->idAutomaticCampaign = $campaign->idAutomaticCampaign;
    $mail->name = $proxNodeObj->sendData->textTitle;
    $mail->replyto = ($proxNodeObj->sendData->replyto == '') ? null : $proxNodeObj->sendData->replyto;
    $mail->subject = $proxNodeObj->sendData->subject;
    $mail->scheduleDate = $date;
    $mail->confirmationDate = $date;
    $mail->gmt = $campaign->gmt;
    $mail->target = $target;
    $mail->type = 'automatic';
    $mail->test = 0;
    $mail->status = 'scheduled';
    $mail->quantitytarget = 0;
    $mail->messagesSent = 0;
    $mail->sentprocessstatus = 'loading-target';
    $mail->singleMail = 1;
    $mail->createdBy = $campaign->createdBy;
    $mail->updatedBy = $campaign->updatedBy;
    //$mail->deleted = time();
    $mail->deleted = 0;
    if (!$mail->save()) {
      $this->db->rollback();
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    $idMailTemplate = $proxNodeObj->sendData->mailtemplate->idMailTemplate;
    $MailContent = \MailTemplateContent::findFirst(array("conditions" => "idMailTemplate = ?0  ", "bind" => array($idMailTemplate)));
    if (!$MailContent) {
      throw new \InvalidArgumentException("La plantilla de correo '{$proxNodeObj->sendData->mailtemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    $html = $this->templateMailManager->getContentHtml($MailContent->content);
    $plainText = new \PlainText();
    $mailPlainText = $plainText->getPlainText($html);
    $mailTemplate = $MailContent;
    //
    $contentMail = new \MailContent();
    $contentMail->idMail = $mail->idMail;
    $contentMail->typecontent = 'Editor';
    $contentMail->content = $mailTemplate->content;
    $contentMail->plaintext = $mailPlainText;
    $contentMail->createdBy = $campaign->createdBy;
    $contentMail->updatedBy = $campaign->updatedBy;
    if (!$contentMail->save()) {
      $this->db->rollback();
      foreach ($contentMail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    //$this->insNewStep($proxNode, $proxNodeObj, $beforeStep, $date);
    //Despues de crear el mail se lo manda al insNewStep idMailcf
    //\Phalcon\DI::getDefault()->get('db')->commit();
    $target = "";
    return $mail;
  }

  public function cloneSms($campaign,$proxNodeObj,$beforeStep,$date,$negation, $proxNode = null) {

    //\Phalcon\DI::getDefault()->get('db')->begin();
    $stepId = $this->searchSourcesAndDest($proxNodeObj->id);
    if($this->data[$stepId]){
      $idMail = $this->data[$stepId]['idMail'];
    } else {
      if($this->mail->idMail){
        $idMail = $this->mail->idMail;
      }
    }
    $target = "";
    if($beforeStep == 'time'){
      $target = $this->target;
      if($idMail){
      $target = substr($this->target, 0, strlen($this->target) - 1);
      $target = $target.',"filters":[{"typeFilters":1,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":""}],"condition":"all"}';
    }
      if($this->sms->idSms){
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filterSms":[{"typeFilters":1,"smsSelected":"'.$this->sms->idSms.'"}],"condition":"all"}';
      }
    }
    if($beforeStep == 'open'){
      if($negation){
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filters":[{"typeFilters":2,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":true}],"condition":"all"}';
      } else {
        $target = substr($this->target, 0, strlen($this->target) - 1);
        $target = $target.',"filters":[{"typeFilters":2,"mailSelected":"'.$idMail.'","linkSelected":[],"mail":[],"links":[],"inverted":""}],"condition":"all"}';
      }
    }
    if($beforeStep == 'clic'){
        if($this->linkidNode === $proxNode){
            if($negation){
                $target = substr($this->target, 0, strlen($this->target) - 1);
                $target = $target.',"filters":[{"typeFilters":3,"mailSelected":"'.$idMail.'","linkSelected": "","link_ac":"'.$this->link.'","mail":[],"links":[{"idMail_link":"","link":"'.$this->link.'","idMail":"'.$idMail.'"}],"inverted":true}],"condition":"all"}';
            } else {
                $target = substr($this->target, 0, strlen($this->target) - 1);
                $target = $target.',"filters":[{"typeFilters":3,"mailSelected":"'.$idMail.'","linkSelected": "","link_ac":"'.$this->link.'","mail":[],"links":[{"idMail_link":"","link":"'.$this->link.'","idMail":"'.$idMail.'"}],"inverted":""}],"condition":"all"}';
            }
        }  
    }
    unset($this->link, $this->linkidNode);
    //
    $amount = 0;
    foreach ($campaign->Subaccount->saxs as $key) {
      if ($key->idServices == 1 && $key->status ==1) {
        $amount = $key->amount;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->subAccount->name;
        $accountName = $this->subAccount->Account->name;
        $arraySaxs = array(
          "amount" => $amount,
          "totalAmount" => $totalAmount,
          "subaccountName" => $subaccountName,
          "accountName" => $accountName,
        );
      }
    }

    if ($amount <= 0) {
      $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      //$sendMailNot->sendSmsNotification($arraySaxs);
      throw new \InvalidArgumentException("No tienes saldo disponible para realizar envíos de SMS de campaña automatica");
    }                      

    $sms = new \Sms();
    $sms->idSmsCategory = $proxNodeObj->sendData->smscategory->idSmsCategory;
    $sms->name = $proxNodeObj->sendData->smstemplate->name;
    $sms->idSubaccount = $campaign->idSubaccount;
    $sms->startdate = $date;
    $sms->status = "scheduled";
    $sms->type = "automatic";
    $sms->receiver = $target;
    $sms->message = $proxNodeObj->sendData->smstemplate->content;
    $sms->target = 0;
    $sms->logicodeleted = 0;
    $sms->confirm = 1;
    $sms->createdBy = $campaign->createdBy;
    $sms->updatedBy = $campaign->updatedBy;
    $sms->idAutomaticCampaign = $campaign->idAutomaticCampaign;
    //$sms->logicodeleted = time();
    $sms->logicodeleted = 0;
    if (!$sms->save()) {
      $this->db->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //\Phalcon\DI::getDefault()->get('db')->commit();
    $target = "";
    return $sms;
  }

  public function getDataTime($node) {
    $nextStep = $this->typeInterval($node->sendData->time->id, $node->sendData->timetwo->id);
    $stepId = $this->searchSourcesAndDest($this->searchSourcesAndDest($node->id));
    if(isset($this->data[$stepId])){
      $date = date('Y-m-d H:i', strtotime($this->data[$stepId]['startDate']. $nextStep));
      $this->startDate = $date;
    } else {
      $date = date('Y-m-d H:i', strtotime($this->startDate. $nextStep));
      $this->startDate = $date;
    }
    return $date;
  }

  public function typeInterval($interval, $type) {
    $intervalReturn = "+ {$interval} ";
    switch ($type) {
      case 1:
        $intervalReturn .= "minute";
        break;
      case 2:
        $intervalReturn .= "hour";
        break;
      case 3:
        $intervalReturn .= "day";
        break;
      case 4:
        $intervalReturn .= "week";
        break;
      case 5:
        $intervalReturn .= "month";
        break;
    }
    return $intervalReturn;
  }

  public function searchSourcesAndDest($idNode) {
    $objReturn = array();
    for ($i = 0; $i < count($this->configuration->connections); $i++) {
      if ($this->configuration->connections[$i]->dest->nodeID == $idNode) {
        $objReturn = $this->configuration->connections[$i];
      }
    }
    return $objReturn;
  }

  public function newNextStepCA() {
    /* Se preparan los contactos para realizar el envio */
    $getAllData = $this->automaticCampaignObj->getAllNodes();
    foreach ($getAllData as $node) {
      if ($node->beforeStep == "open") {
        $nodeStep = new \stdClass();
        $nodeStep->idNode = $node->idNode;
        $nodeStep->node = $this->automaticCampaignObj->getNode($node->idNode);
        $nodeStep->beforeStep = $node->beforeStep;
        //
        if ($node->negation == 0) {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node, 1);
        } else {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node);
        }
      }
      if ($node->beforeStep == "clic") {
        $nodeStep = new \stdClass();
        $nodeStep->idNode = $node->idNode;
        $nodeStep->node = $this->automaticCampaignObj->getNode($node->idNode);
        $this->link = $node->link;
        $this->linkidNode = $node->idNode;
        $nodeStep->beforeStep = $node->beforeStep;
        //
        if ($node->negation == 0) {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep,  $node, 1);
        } else {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node);
        }
      }
      if ($node->beforeStep == "open clic") {
        $nodeStep = new \stdClass();
        $nodeStep->idNode = $node->idNode;
        $nodeStep->node = $this->automaticCampaignObj->getNode($node->idNode);
        $this->link = $node->link;
        $this->linkidNode = $node->idNode;
        $nodeStep->beforeStep = $node->beforeStep;
        //
        if ($node->negation == 0) {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node, 1);
        } else {
          $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node);
        }
      }
      if ($node->beforeStep == "no open clic") {
        $nodeStep = new \stdClass();
        $nodeStep->idNode = $node->idNode;
        $nodeStep->node = $this->automaticCampaignObj->getNode($node->idNode);
        $nodeStep->beforeStep = $node->beforeStep;
        $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node, 1);
      }
      if ($node->beforeStep == "time") {
        $nodeStep = new \stdClass();
        $nodeStep->idNode = $node->idNode;
        $nodeStep->node = $this->automaticCampaignObj->getNode($node->idNode);
        $nodeStep->beforeStep = $node->beforeStep;
        $this->insNewStep($nodeStep->idNode, $nodeStep->node, $nodeStep->beforeStep, $node);
      }
    }
  }

  public function getDataTimeNodes($node, $startDate) {
    $nextStep = $this->typeInterval($node->time->id, $node->timetwo->id);
    return date('Y-m-d H:i', strtotime($startDate. $nextStep));
  }

  public function cloneSmsCA($campaign,$proxNodeObj,$beforeStep,$negation, $node, $proxNode = null) {
    $stepNode = $this->searchSourcesAndDest($proxNodeObj->id);
    if ($beforeStep == "open" || $beforeStep == "clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "time") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $idSms = $getOneNodes->idSms;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "open clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
      $idMail = $getOneNodes->idMail;
      $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "no open clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    }
    $target = '';
    if ($beforeStep == "open"){
      $target = $this->automaticCampaignObj->transformTargetOpen($this->target, $idMail, $negation);
    }
    if($beforeStep == 'clic'){
      $target = $this->automaticCampaignObj->transformTargetClic($this->target, $idMail, $node->idMail_link, $negation);
    }
    if ($beforeStep == "open clic"){
      $target = $this->automaticCampaignObj->transformTargetOpenClic($this->target, $idMail, $node->idMail_link, $negation);
    }
    if ($beforeStep == "no open clic"){
      $target = $this->automaticCampaignObj->transformTargetNoOPenClic($this->target, $idMail, $node->link);
    }
    if($beforeStep == 'time'){
      $target = $this->automaticCampaignObj->transformTargetTime($this->target, $idMail, $idSms);
    }
    //\Phalcon\DI::getDefault()->get('db')->begin();
    //
    $amount = 0;
    foreach ($campaign->Subaccount->saxs as $key) {
      if ($key->idServices == 1 && $key->status ==1) {
        $amount = $key->amount;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->subAccount->name;
        $accountName = $this->subAccount->Account->name;
        $arraySaxs = array(
          "amount" => $amount,
          "totalAmount" => $totalAmount,
          "subaccountName" => $subaccountName,
          "accountName" => $accountName,
        );
      }
    }

    if ($amount <= 0) {
      $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      //$sendMailNot->sendSmsNotification($arraySaxs);
      throw new \InvalidArgumentException("No tienes saldo disponible para realizar envíos de SMS de campaña automatica");
    }                      

    $sms = new \Sms();
    $sms->idSmsCategory = $proxNodeObj->sendData->smscategory->idSmsCategory;
    $sms->name = $proxNodeObj->sendData->smstemplate->name;
    $sms->idSubaccount = $campaign->idSubaccount;
    $sms->startdate = $date;
    $sms->status = "scheduled";
    $sms->type = "automatic";
    $sms->receiver = $target;
    $sms->message = $proxNodeObj->sendData->smstemplate->content;
    $sms->target = 0;
    $sms->logicodeleted = 0;
    $sms->confirm = 1;
    $sms->createdBy = $campaign->createdBy;
    $sms->updatedBy = $campaign->updatedBy;
    $sms->idAutomaticCampaign = $campaign->idAutomaticCampaign;
    //$sms->logicodeleted = time();
    $sms->logicodeleted = 0;
    if (!$sms->save()) {
      $this->db->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //\Phalcon\DI::getDefault()->get('db')->commit();
    $target = "";
    return $sms;
  }

  public function cloneMailCA($campaign,$proxNodeObj,$beforeStep,$negation, $node, $proxNode = null) {
    $stepNode = $this->searchSourcesAndDest($proxNodeObj->id);
    if ($beforeStep == "open" || $beforeStep == "clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "time") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $idSms = $getOneNodes->idSms;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "open clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
      $idMail = $getOneNodes->idMail;
      $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    } else if ($beforeStep == "no open clic") {
      $stepNodeDest = $this->searchSourcesAndDest($stepNode->source->nodeID);
      $stepNodeDest = $this->searchSourcesAndDest($stepNodeDest->source->nodeID);
      $getOneNodes = $this->automaticCampaignObj->getOneNodes($stepNodeDest->source->nodeID);
      if (isset($getOneNodes)) {
        $idMail = $getOneNodes->idMail;
        $date = $this->getDataTimeNodes($stepNode->sendData, $getOneNodes->scheduleDate);
      }
    }
    $target = '';
    if ($beforeStep == "open"){
      $target = $this->automaticCampaignObj->transformTargetOpen($this->target, $idMail, $negation);
    }
    if($beforeStep == 'clic'){
      $target = $this->automaticCampaignObj->transformTargetClic($this->target, $idMail, $node->idMail_link, $negation);
    }
    if ($beforeStep == "open clic"){
      $target = $this->automaticCampaignObj->transformTargetOpenClic($this->target, $idMail, $node->idMail_link, $negation);
    }
    if ($beforeStep == "no open clic"){
      $target = $this->automaticCampaignObj->transformTargetNoOPenClic($this->target, $idMail, $node->link);
    }
    if($beforeStep == 'time'){
      $target = $this->automaticCampaignObj->transformTargetTime($this->target, $idMail, $idSms);
    }
    //
    $mail = new \Mail();
    $mail->idSubaccount = $campaign->idSubaccount;
    $mail->idEmailsender = $proxNodeObj->sendData->senderEmail->idEmailsender;
    $mail->idNameSender = $proxNodeObj->sendData->senderName->idNameSender;
    $mail->idAutomaticCampaign = $campaign->idAutomaticCampaign;
    $mail->name = $proxNodeObj->sendData->textTitle;
    $mail->replyto = ($proxNodeObj->sendData->replyto == '') ? null : $proxNodeObj->sendData->replyto;
    $mail->subject = $proxNodeObj->sendData->subject;
    $mail->scheduleDate = $date;
    $mail->confirmationDate = $date;
    $mail->gmt = $campaign->gmt;
    $mail->target = $target;
    $mail->type = 'automatic';
    $mail->test = 0;
    $mail->status = 'scheduled';
    $mail->quantitytarget = 0;
    $mail->messagesSent = 0;
    $mail->sentprocessstatus = 'loading-target';
    $mail->singleMail = 1;
    $mail->createdBy = $campaign->createdBy;
    $mail->updatedBy = $campaign->updatedBy;
    if(isset($proxNodeObj->sendData->idAssets)){
      $mail->attachment = 1;
    }
    //$mail->deleted = time();
    $mail->deleted = 0;
    if (!$mail->save()) {
      $this->db->rollback();
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    if($mail->attachment == 1){
      $assets = $proxNodeObj->sendData->idAssets;
      foreach($assets as $asset){
          $attachment = new Mailattachment();
          $attachment->idAsset = $asset->id;
          $attachment->idMail = $mail->idMail;
          $attachment->createdon = time();
          if (!$attachment->save()) {
              foreach ($attachment->getMessages() as $message) {
                throw new InvalidArgumentException($message);
              }
          }
      }
    }
    //
    $idMailTemplate = $proxNodeObj->sendData->mailtemplate->idMailTemplate;
    $MailContent = \MailTemplateContent::findFirst(array("conditions" => "idMailTemplate = ?0  ", "bind" => array($idMailTemplate)));
    if (!$MailContent) {
      throw new \InvalidArgumentException("La plantilla de correo '{$proxNodeObj->sendData->mailtemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    $html = $this->templateMailManager->getContentHtml($MailContent->content);
    $plainText = new \PlainText();
    $mailPlainText = $plainText->getPlainText($html);
    $mailTemplate = $MailContent;
    //
    $contentMail = new \MailContent();
    $contentMail->idMail = $mail->idMail;
    $contentMail->typecontent = 'Editor';
    $contentMail->content = $mailTemplate->content;
    $contentMail->plaintext = $mailPlainText;
    $contentMail->createdBy = $campaign->createdBy;
    $contentMail->updatedBy = $campaign->updatedBy;
    
    if (!$contentMail->save()) {
      $this->db->rollback();
      foreach ($contentMail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    //$this->insNewStep($proxNode, $proxNodeObj, $beforeStep, $date);
    //Despues de crear el mail se lo manda al insNewStep idMailcf
    //\Phalcon\DI::getDefault()->get('db')->commit();
    $target = "";
    return $mail;
  }

}