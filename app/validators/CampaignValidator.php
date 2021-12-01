<?php

use Sigmamovil\General\Misc\TemplatemailObject;
use Sigmamovil\Wrapper\MailtemplateWrapper;

class CampaignValidator {

  protected $configuration;
  protected $connections;
  protected $user;
  protected $Subaccount;

  public function __construct($configuration) {
    $this->configuration = $configuration;
  }

  protected function setConnection($connections) {
    $this->connections = $connections;
  }
  
  public function setSubAccount(\Subaccount $subAccount){
    $this->Subaccount = $subAccount;
  }
  
  public function validate() {
    if (count($this->configuration->nodes) <= 1) {
      return false;
    }
    $this->setConnection($this->configuration->connections);

    foreach ($this->configuration as $key => $value) {
      if ($key == "nodes") {
        foreach ($value as $v) {
          $this->validateNode($v);
        }
      }
    }

    return true;
  }

  protected function validateNode($valueNode) {
    //var_dump($valueNode->sendData);
    $sendData = $valueNode->sendData;
    if ($valueNode->method == "primary") {
      if ($sendData->list->id == 1) {
        $this->searchContactList($sendData->selecteds);
      } else {
        $this->searchSegment($sendData->selecteds);
      }
    }

    if ($valueNode->method == "email") {
      $this->searchEmail($sendData);
    }
    if ($valueNode->method == "sms") {
      $this->searchSms($sendData);
    }
    if ($valueNode->method == "actions") {
      $this->searchAction($sendData, $valueNode);
    }
    unset($sendData);
  }

  protected function searchContactList($arrContactList) {
    //$obj = (object) array('idContactlist' => '2', 'name' => 'asdadadasasd');
    //array_push($arrContactList, $obj);
    for ($i = 0; $i < count($arrContactList); $i++) {
      $idContactlist = $arrContactList[$i]->idContactlist;
      $consultContactList = \Contactlist::findFirst(array("conditions" => "idSubaccount = ?0 and idContactlist = ?1 and deleted =?2", "bind" => array(0 => $this->Subaccount->idSubaccount, 1 => $idContactlist, 2 => 0)));
      if (!$consultContactList) {
        throw new \InvalidArgumentException("La lista de contacto '{$arrContactList[$i]->name}' ha sido eliminado por favor verifique la información.");
      }
    }
    unset($idContactlist);
    unset($consultContactList);
  }

  protected function searchSegment($arrSegments) {
    for ($i = 0; $i < count($arrSegments); $i++) {
      $idSegment = $arrSegments[$i]->idSegment;
      $consultSegments = \Segment::findFirst([["idSubaccount" => $this->Subaccount->idSubaccount, "idSegment" => $idSegment, "deleted" => 0]]);
      if (!$consultSegments) {
        throw new \InvalidArgumentException("El Segmento '{$arrSegments[$i]->name}' ha sido eliminado por favor verifique la información.");
      }
    }
    unset($idSegment);
    unset($consultSegments);
  }

  protected function searchEmail($objEmail) {
//    var_dump($objEmail);
//    exit();
    
//    $mailTemplate = \MailTemplate::findFirst(array("conditions" => "idMailTemplate = ?0 and idAccount = ?1 and deleted = ?2", "bind" => array($objEmail->mailtemplate->idMailTemplate, $this->Subaccount->Account->idAccount, 0)));
    $mailTemplate = \MailTemplate::findFirst(array("conditions" => "idMailTemplate = ?0 and deleted = ?1", "bind" => array($objEmail->mailtemplate->idMailTemplate, 0)));
    if (!$mailTemplate) {
      throw new \InvalidArgumentException("La plantilla de correo '{$objEmail->mailtemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    $mailCategory = \MailCategory::findFirst(array("conditions" => "idMailCategory = ?0 and idAccount = ?1 and deleted = ?2", "bind" => array($objEmail->mailcategory->idMailCategory, $this->Subaccount->Account->idAccount, 0)));
    if (!$mailCategory) {
      throw new \InvalidArgumentException("La categoria de correo '{$objEmail->mailcategory->name}' ha sido eliminado por favor verifique la información.");
    }
    $senderName = \NameSender::findFirst(array("conditions" => "idNameSender = ?0 and idAccount = ?1 and status = ?2", "bind" => array($objEmail->senderName->idNameSender, $this->Subaccount->Account->idAccount, 1)));
    if (!$senderName) {
      throw new \InvalidArgumentException("El nombre del remitente '{$objEmail->senderName->name}' ha sido eliminado por favor verifique la información.");
    }
    $senderMail = \Emailsender::findFirst(array("conditions" => "idEmailsender = ?0 and idAccount = ?1 and status = ?2", "bind" => array($objEmail->senderEmail->idEmailsender, $this->Subaccount->Account->idAccount, 1)));
    if (!$senderMail) {
      throw new \InvalidArgumentException("El correo del remitente '{$objEmail->senderEmail->email}' ha sido eliminado por favor verifique la información.");
    }
    unset($mailTemplate);
    unset($mailCategory);
    unset($senderName);
    unset($senderMail);
  }

  protected function searchSms($objSms) {
    
    $idSmsTemplate = $objSms->smstemplate->idSmsTemplate;
    $smsTemplate = \SmsTemplate::findFirst(array("conditions" => "idAccount =?0 and idSmsTemplate =?1 and status =?2 and deleted = ?3", "bind" => array(0 => $this->Subaccount->Account->idAccount, 1 => $idSmsTemplate, 2 => 1, 3 => 0)));
    if (!$smsTemplate) {
      throw new \InvalidArgumentException("La plantilla de sms '{$objSms->smstemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    
    $idSmsCategory = $objSms->smscategory->idSmsCategory;
    $smsCategory = \SmsCategory::findFirst(array("conditions" => "idAccount =?0 and idSmsCategory =?1 and deleted = ?2", "bind" => array(0 => $this->Subaccount->Account->idAccount, 1 => $idSmsCategory, 2 => 0)));
    if (!$smsCategory) {
      throw new \InvalidArgumentException("La plantilla de sms '{$objSms->smstemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    
    unset($idSmsTemplate);
    unset($idSmsCategory);
    unset($smsTemplate);
    unset($smsCategory);
    
  }

  protected function searchAction($objAction, $valueNode) {
    if ($objAction->selectAction->id == 2 || $objAction->selectAction->id == 4) {
      $arrConection = $this->searchConnection($valueNode->id);
      $objNode = $this->getNode($arrConection['source']);
      //FUNCION PARA VALIDAR SI LA PLANTILLA ESTA ACTIVA
      $mailtemplate = \MailTemplate::findFirst(array(
                "conditions" => "idMailTemplate = ?0 AND deleted = 0",
                "bind" => array((int)$objNode->sendData->mailtemplate->idMailTemplate)
      ));
      
      if (!$mailtemplate) {
          throw new InvalidArgumentException("La plantilla {$idTemplate} ha sido eliminado por favor verifique la información.");
      }
      //SI LA PLANTILLA ESTA ACTIVA HACE LA CONSULTA PARA EXTRAER LOS LINKS DE LA MISMA
      $links = $this->getLinksTemplate($objNode->sendData->mailtemplate->idMailTemplate);
      
      if (!$this->compareArray($objAction->linksTemplateSelected->name, $links)) {
        throw new \InvalidArgumentException("Verificar los enlaces seleccionados de la plantilla de correo.");
      }
    }
  }

  protected function searchConnection($idNode) {
    $objReturn = array();
    for ($i = 0; $i < count($this->connections); $i++) {
      if ($this->connections[$i]->source->nodeID == $idNode) {
        $objReturn["dest"] = $this->connections[$i]->dest->nodeID;
      }
      if ($this->connections[$i]->dest->nodeID == $idNode) {
        $objReturn["source"] = $this->connections[$i]->source->nodeID;
      }
    }
    return $objReturn;
  }

  protected function getNode($idNode) {
    for ($i = 0; $i < count($this->configuration->nodes); $i++) {
      if ($this->configuration->nodes[$i]->id == $idNode) {
        return $this->configuration->nodes[$i];
      }
    }
  }

  protected function compareArray($arrCompare, $arrStatic) {
    
    
    if (count($arrCompare) > count($arrStatic)) {
      return false;
    }
    
    for ($i = 0; $i < count($arrCompare); $i++) {

      $search = array_search($arrCompare[$i], $arrStatic);
      if ($search == false && $search != 0) {
        return false;
      }
    }

    return true;
  }

  protected function getLinksTemplate($idTemplate) {
    //CONSULTA PARA TRAER LA PLANTILLA Y SEGUIDO EXTRAER EL CONTENIDO DE LA MISMA
    $contentTemplate = \MailTemplateContent::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($idTemplate)
    ));
    if (!$contentTemplate) {
      throw new InvalidArgumentException("La plantilla {$idTemplate} no tiene contenido.");
    }

    $editor = new Sigmamovil\Logic\Editor\HtmlObj();
    $editor->setAccount($this->Subaccount->Account);
    $editor->assignContent(json_decode($contentTemplate->content));
    $html = $editor->render();

    $TemplatemailObject = new Sigmamovil\General\Misc\TemplatemailObject($contentTemplate);
    $marks = $TemplatemailObject->getLinksTemplate($html);

    return $marks;
  }

}
