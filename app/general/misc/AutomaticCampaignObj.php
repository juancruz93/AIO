<?php

namespace Sigmamovil\General\Misc;

class AutomaticCampaignObj {

  protected $automatic,
          $automatic_configuration,
          $configurationObj,
          $connections,
          $time = array(1 => "Minuto(s)", 2 => "Hora(s)", 3 => "Día(s)", 4 => "Semana(s)", 5 => "Mes(es)"),
          $mail,
          $interval,
          $lastNode,
          $linkSelecteds;

  public function __construct(\AutomaticCampaign $automatic, \AutomaticCampaignConfiguration $automaticConfiguration, \Mail $mail = null) {
    $this->automatic = $automatic;
    $this->automatic_configuration = $automaticConfiguration;
    $this->configurationObj = json_decode($automaticConfiguration->configuration);
    $this->connections = $this->configurationObj->connections;
    $this->mail = $mail;
    $this->lastNode = count($this->configurationObj->nodes) - 1;
  }

  public function getDataActions($node) {
    $nextStep = $this->typeInterval($node->sendData->time->id, $node->sendData->timetwo->id);
    $date = date('Y-m-d H:i', strtotime($nextStep));
    if ($node->sendData->selectAction->id == 2 || $node->sendData->selectAction->id == 4) {
      $array = array();
      for ($i = 0; $i < count($node->sendData->linksTemplateSelected); $i++) {
        $array[] = $node->sendData->linksTemplateSelected[$i]->name;
      }
      $this->linkSelecteds = $array;
    }
    return $date;
  }

  public function getDataTime($node) {
    $nextStep = $this->typeInterval($node->sendData->time->id, $node->sendData->timetwo->id);
    $date = date('Y-m-d H:i', strtotime($nextStep));
    return $date;
  }

  public function getNodeFromItemArray($idItem) {
    return $this->configurationObj->nodes[$idItem];
  }

  public function searchConnection($idNode) {
    $objReturn = array();
    for ($i = 0; $i < count($this->connections); $i++) {
      if ($this->connections[$i]->source->nodeID == $idNode) {
        $objReturn["dest"] = $this->connections[$i]->dest->nodeID;
        $objReturn["sentData"] = $this->connections[$i]->sentData;
      }
    }
    return $objReturn;
  }

  public function getNode($idNode) {
    for ($i = 0; $i < count($this->configurationObj->nodes); $i++) {
      if ($this->configurationObj->nodes[$i]->id == $idNode) {
        return $this->configurationObj->nodes[$i];
      }
    }
  }

  public function getAllNodes(){
    $objReturn = [];
    for ($i = 0; $i < count($this->connections); $i++) {
      // poner en orden open open clic no open clic
      if (isset($this->connections[$i]->sentData->idNode)) {
        $objReturn[] = $this->connections[$i]->sentData;
      }
    }
    return $objReturn;
  }

  public function getIndexArray($idNode) {
    for ($i = 0; $i < count($this->configurationObj->nodes); $i++) {
      if ($this->configurationObj->nodes[$i]->id == $idNode) {
        return $i;
      }
    }
  }

  public function insertNextStepNoOpen($idContact, $beforeStep) {
    $nodeTime = $this->getNodeFromItemArray(2);
    $proxNodeObj = $this->getNodeFromItemArray(3);
    $automaticD = new \AutomaticCampaignStep();
    if ($proxNodeObj->method == "email") {
      $automaticD->idMailTemplate = $proxNodeObj->sendData->mailtemplate->idMailTemplate;
      $automaticD->status = "scheduled";
    } else {
      $automaticD->statusSms = "scheduled";
      $automaticD->idSmsTemplate = $proxNodeObj->sendData->smstemplate->idSmsTemplate;
    }

    $automaticD->beforeStep = $beforeStep;
    $automaticD->idContact = $idContact;
    $automaticD->step = 3;
    $automaticD->createdBy = "indefinido";
    $automaticD->idAutomaticCampaign = $this->automatic->idAutomaticCampaign;
//    $automaticD->idMailTemplate = $nodeEmail->sendData->mailtemplate->idMailtemplate;
    $time = $nodeTime->sendData->time->id;
    $timetwo = $nodeTime->sendData->timetwo->id;
    $nuevafecha = strtotime($this->typeInterval($time, $timetwo), time());
    $nuevafecha = date('Y-m-d H:i', $nuevafecha);
    $automaticD->scheduleDate = $nuevafecha;
    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
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

  public function setInterval($interval) {
    $this->interval = $interval;
    return $this;
  }

  public function insNewStep($idContact, $proxNode, $proxNodeObj, $beforeStep, $date, $negation = 0) {

    $automaticD = new \AutomaticCampaignStep();
    $automaticD->idContact = $idContact;
    //$automaticD->step = $proxNode;
    $automaticD->idNode = $proxNode;
    $automaticD->idAutomaticCampaign = $this->automatic->idAutomaticCampaign;
    if ($proxNodeObj->method == "email") {
      $automaticD->status = "scheduled";
      $automaticD->idMailTemplate = $proxNodeObj->sendData->mailtemplate->idMailTemplate;
    } else {
      $automaticD->statusSms = "scheduled";
      $automaticD->idSmsTemplate = $proxNodeObj->sendData->smstemplate->idSmsTemplate;
    }
    $automaticD->scheduleDate = $date;
    $automaticD->negation = $negation;
    $automaticD->beforeStep = $beforeStep;
    $automaticD->createdBy = $this->automatic->createdBy;
    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function uptStatusStep(\AutomaticCampaignStep $automaticD, $status) {
    if (!empty($automaticD->idMailTemplate)) {
      $automaticD->status = $status;
    } else {
      $automaticD->statusSms = $status;
    }

    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function validateDateCampaign() {
    $now = date('Y-m-d H:i');
    $dateEnd = $this->automatic->endDate;
    if ($now > $dateEnd) {
      return false;
    }
    return true;
  }

  public function getLastNode() {
    return $this->lastNode;
  }

  public function getBeforeStep($node) {
    $beforeStep = "";
    if ($node->method == "actions") {
      switch ($node->sendData->selectAction->id) {
        case 1:
          $beforeStep = "open";
          break;
        case 2:
          $beforeStep = "clic";
          break;
        case 3:
          $beforeStep = "no open";
          break;
        case 4:
          $beforeStep = "no clic";
          break;
      }
    } else if ($node->method == "clicks" || $node->method == "links") {
      $beforeStep = "open clic";
    } else {
      $beforeStep = "time";
    }
    return $beforeStep;
  }

  public function insCountClic(\AutomaticCampaignStep $automaticD) {
    $automaticD->totalClicks = $automaticD->totalClicks + 1;
    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function uptUniqueClic(\AutomaticCampaignStep $automaticD) {
    $automaticD->uniqueClicks = time();
    if (!$automaticD->save()) {
      foreach ($automaticD->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function openAutomatization(\AutomaticCampaignStep $campaingStep, $new = true) {
    if ($new) {
      $campaingStep->open = time();
      $campaingStep->totalOpening += 1;
    } else {
      $campaingStep->totalOpening += 1;
    }
    if (!$campaingStep->save()) {
      foreach ($campaingStep->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function clickAutomatization(\AutomaticCampaignStep $campaingStep) {

    if ($campaingStep->uniqueClicks != 0) {
      $campaingStep->totalClicks += 1;
    } else {
      $campaingStep->uniqueClicks = time();
      $campaingStep->totalClicks += 1;
    }

    if (!$campaingStep->save()) {
      foreach ($campaingStep->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function getobjmaillink($idLink) {
    return \Maillink::findFirst(array("conditions" => "idMail_link =?0", "bind" => array($idLink)));
  }

  public function clickAcxl($idMailLink, $idAutomaticStep) {
    $acxl = \Acxl::findFirst(array("conditions" => "idMail_link = ?0 and idAutomaticCampaignStep = ?1 ", "bind" => array($idMailLink, $idAutomaticStep)));

    if ($acxl->uniqueClicks != 0) {
      $acxl->totalClicks = $acxl->totalClicks + 1;
    } else {
      $acxl->totalClicks = $acxl->totalClicks + 1;
      $acxl->uniqueClicks = time();
    }

    if (!$acxl->save()) {
      foreach ($acxl->getMessages() as $msg) {
        var_dump($acxl);
        exit;
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function searchLink($link) {
    return array_search($link, $this->linkSelecteds);
  }

  public function getAutomaticStep($idAutomaticCampaign) {
    return \AutomaticCampaignStep::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign), "order" => "idAutomaticCampaignStep ASC"));
  }

  public function getAutomaticStepLast($idAutomaticCampaign) {
    return \AutomaticCampaignStep::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign), "order" => "idAutomaticCampaignStep DESC"));
  }

  public function getAutomaticStepNode($idAutomaticCampaign, $idNode) {
    return \AutomaticCampaignStep::findFirst(array("conditions" => "idAutomaticCampaign = ?0 and idNode = ?1", "bind" => array($idAutomaticCampaign, $idNode), "order" => "idAutomaticCampaignStep DESC"));
  }

  public function getStatusStep(\AutomaticCampaignStep $campaingStep) {
    $status = false;
    if (!empty($campaingStep->idMailTemplate)) {
      $status = $campaingStep->status;
    } else {
      $status = $campaingStep->statusSms;
    }
    return $status;
  }

  /* FUNCIONES PARA AUTOMATIZACION DE RAIZ */

  public function transformTarget($sendData) {
    $objReturn = new \stdClass();
    if ($sendData->list->id == 1) {
      $objReturn->type = "contactlist";
      $objReturn->contactlists = $sendData->selecteds;
    } else {
      $objReturn->type = "segment";
      $objReturn->segment = $sendData->selecteds;
    }
    return json_encode($objReturn);
  }

  public function searchNegation($idNode) {
    $objReturn = array();
    for ($i = 0; $i < count($this->connections); $i++) {
      if ($this->connections[$i]->source->nodeID == $idNode) {
        $arryAux = array();
//        $arryAux["dest"]["idNode"] = (isset($this->connections[$i]->dest)) ? $this->connections[$i]->dest->nodeID : $this->connections[$i]->source->nodeID;
        $arryAux["dest"]["idNode"] = $this->connections[$i]->dest->nodeID;
        $arryAux["dest"]["sendData"] = $this->connections[$i]->sendData;
        $arryAux["dest"]["class"] = $this->connections[$i]->class;
        array_push($objReturn, $arryAux);
      }
//      if ($this->connections[$i]->dest->nodeID == $idNode) {
//        $arryAux = array();
//        $arryAux["source"]["idNode"] = $this->connections[$i]->source->nodeID;
//        $arryAux["source"]["sendData"] = $this->connections[$i]->sendData;
//        $arryAux["source"]["class"] = $this->connections[$i]->class;
//        array_push($objReturn, $arryAux);
//      }
    }
    return json_encode($objReturn);
  }

  public function firstServices() {
    return $this->getNode($this->searchConnection(0)["dest"]);
  }

  public function getNodeUpdate($idNode, $scheduleDate, $idMail, $idSms) {
    for ($i = 0; $i < count($this->connections); $i++) {
      // poner en orden open open clic no open clic
      if (isset($this->connections[$i]->sentData->idNode) && $this->connections[$i]->sentData->idNode == $idNode) {
        $this->connections[$i]->sentData->scheduleDate  = $scheduleDate;
        $this->connections[$i]->sentData->idMail        = $idMail;
        $this->connections[$i]->sentData->idSms         = $idSms;
      }
    }
  }

  public function getOneNodes($idNode){
    $objReturn = [];
    $connections = $this->getAllNodes();
    for ($i = 0; $i < count($connections); $i++) {
      // poner en orden open open clic no open clic
      if ($connections[$i]->idNode == $idNode) {
        $objReturn = $connections[$i];
      }
    }
    unset($connections);
    return $objReturn;
  }

  public function transformTargetOPen($target, $idMail, $negation) {
    $objReturn = json_decode($target);
    $objReturn->filtersOpen[] = [
      "typeFilters" => 2,
      "mailSelected" => $idMail,
      "linkSelected" => "",
      "inverted" => $negation != 0 ? true : ""
    ];
    return json_encode($objReturn);
  }

  public function transformTargetClic($target, $idMail, $idMail_link, $negation) {
    $objReturn = json_decode($target);
    $objReturn->filtersClic[] = [
      "typeFilters" => 3,
      "mailSelected" => $idMail,
      "linkSelected" => $idMail_link,
      "inverted" => $negation != 0 ? true : ""
    ];
    return json_encode($objReturn);
  }

  public function transformTargetOPenClic($target, $idMail, $idMail_link, $negation) {
    $objReturn = json_decode($target);
    $objReturn->filtersOPenClic[] = [
      "typeFilters" => 2,
      "mailSelected" => $idMail,
      "linkSelected" => "",
      "inverted" => ""
    ];
    $objReturn->filtersOPenClic[] = [
      "typeFilters" => 3,
      "mailSelected" => $idMail,
      "linkSelected" => $idMail_link,
      "inverted" => $negation != 0 ? true : ""
    ];
    return json_encode($objReturn);
  }

  public function transformTargetNoOPenClic($target, $idMail, $links) {
    $objReturn = json_decode($target);
    $objReturn->filtersNoOPenClic[] = [
      "typeFilters" => 2,
      "mailSelected" => $idMail,
      "linkSelected" => "",
      "inverted" => ""
    ];
    foreach ($links as $link) {
      $objReturn->filtersNoOPenClic[] = [
        "typeFilters" => 3,
        "mailSelected" => $idMail,
        "linkSelected" => $link->id,
        "inverted" => true
      ];
    }
    return json_encode($objReturn);
  }

  public function transformTargetTime($target, $idMail, $idSms) {
    $objReturn = json_decode($target);
    if($idMail){
      $objReturn->filtersTime[] = [
        "typeFilters" => 1,
        "mailSelected" => $idMail,
        "smsSelected" => "",
      ];
    }
    if($idSms){
      $objReturn->filtersTime[] = [
        "typeFilters" => 1,
        "mailSelected" => "",
        "smsSelected" => $idSms,
      ];
    }  
    return json_encode($objReturn);
  }

}
