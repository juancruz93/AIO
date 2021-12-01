<?php

namespace Sigmamovil\Wrapper;

class AutomaticcampaignWrapper extends \BaseWrapper {

  public $automaticCampaignConfiguration;

  public function setAutomaticCampaign($ac) {
    $this->automaticCampaignConfiguration = $ac;
  }

  public function listautomaticcampaign($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = $this->user->Usertype->idSubaccount; //((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $name = (isset($filter->name) ? "AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');
    $where = "";
    
    if (isset($filter->category) && count($filter->category) >= 1) {
      $arr = implode(",", $filter->category);
      $where .= "  AND idAutomaticCampaignCategory IN ({$arr})";
    }
    
    if (isset($filter->dateinitial) && isset($filter->dateend)) {
      if ($filter->dateinitial != "" && $filter->dateend != "") {
        $where .= " AND startDate BETWEEN '{$filter->dateinitial}' AND '{$filter->dateend}'";
      }
    }

    $conditions = array(
        "conditions" => "deleted = ?0 AND idSubaccount = ?1 {$name} $where",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC",
    );
        

    $autocampcateg = \AutomaticCampaign::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\AutomaticCampaign::find($conditions));

    $data = array();
    if (count($autocampcateg) > 0) {
      foreach ($autocampcateg as $key => $value) {
        $data[$key] = array(
            "configuration" => \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($value->idAutomaticCampaign))),
            "category" => $value->AutomaticCampaignCategory->name,
            "idAutomaticCampaign" => $value->idAutomaticCampaign,
            "idAccount" => $value->idSubaccount,
            "name" => $value->name,
            "description" => $value->description,
            "created" => date("Y-m-d", $value->created),
            "updated" => date("Y-m-d", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "status" => $value->status,
            "startDate" => $value->startDate,
            "endDate" => $value->endDate,
            "canceleduser" => $value->canceleduser,
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  public function saveautomaticcampaign($data) {
    $campaign = new \AutomaticCampaign();
    $campaign->name = $data->nameCampaign;
    $campaign->description = (isset($data->descriptionCampaign) ? $data->descriptionCampaign : 'Sin descripcion');
    $campaign->startDate = $data->startDate;
    $campaign->endDate = "0000-00-00 00:00";
    $dt = new \DateTime();
    $dt->setTimezone(new \DateTimeZone('America/Bogota'));
    if($campaign->startDate < date_format($dt, 'Y-m-d H:i')){
        throw new \InvalidArgumentException("La fecha de inicio no puede ser menor a la fecha actual ".date_format($dt, 'Y-m-d H:i'));
    }
    $campaign->idAutomaticCampaignCategory = $data->campaignCategory;
    $campaign->idSubaccount = $this->user->Usertype->idSubaccount;
    $campaign->status = 'confirmed';
    $campaign->gmt = $data->gmt;

    if (!$campaign->save()) {
      foreach ($campaign->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $campaign;
  }

  public function savecontentautomaticcampaign($data, $idAutomaticCampaign) {
    $campaignConfiguration = new \AutomaticCampaignConfiguration();
    $campaignConfiguration->idAutomaticCampaign = $idAutomaticCampaign;
    $campaignConfiguration->configuration = json_encode($data);
    if (!$campaignConfiguration->save()) {
      foreach ($campaignConfiguration->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function validateCampaignData() {
    $validator = new \CampaignValidator(json_decode($this->automaticCampaignConfiguration->configuration));
    if (!isset($this->user->Usertype->Subaccount)) {
      throw new \InvalidArgumentException("Es requerido el SubAccount");
    }
    $validator->setSubAccount($this->user->Usertype->Subaccount);
    $validator->validate();
  }

  public function getautomaticcapaign() {
    $arrayReturn = array("campaign" => array("nameCampaign" => $this->automaticCampaignConfiguration->AutomaticCampaign->name,
            "startDate" => $this->automaticCampaignConfiguration->AutomaticCampaign->startDate,
            "endDate" => $this->automaticCampaignConfiguration->AutomaticCampaign->endDate,
            "campaignCategory" => $this->automaticCampaignConfiguration->AutomaticCampaign->idAutomaticCampaignCategory,
            "descriptionCampaign" => $this->automaticCampaignConfiguration->AutomaticCampaign->description,
            "gmt" => $this->automaticCampaignConfiguration->AutomaticCampaign->gmt,
            "status" => $this->automaticCampaignConfiguration->AutomaticCampaign->status),
        "configuration" => json_decode($this->automaticCampaignConfiguration->configuration));

    return $arrayReturn;
  }

  public function updateautomaticcampaign($data, $idAutomaticCampaign) {
    $campaign = \AutomaticCampaign::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));
    if (!$campaign) {
      throw new \InvalidArgumentException("La campaña consultada no se encuentra registrada.");
    }
    $campaign->name = $data->nameCampaign;
    $campaign->description = (isset($data->descriptionCampaign) ? $data->descriptionCampaign : 'Sin descripcion');
    $campaign->startDate = $data->startDate;
    $dt = new \DateTime();
    $dt->setTimezone(new \DateTimeZone('America/Bogota'));
    if($campaign->startDate < date_format($dt, 'Y-m-d H:i')){
        throw new \InvalidArgumentException("La fecha de inicio no puede ser menor a la fecha actual ".date_format($dt, 'Y-m-d H:i'));
    }
    $campaign->endDate = "0000-00-00 00:00";
    $campaign->idAutomaticCampaignCategory = $data->campaignCategory;
    $campaign->gmt = $data->gmt;
    $campaign->status = 'confirmed';
//    $campaign->idSubaccount = $this->user->Usertype->idSubaccount;

    if (!$campaign->save()) {
      foreach ($campaign->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
//    var_dump($data);
//    exit();
    return $campaign;
  }

  public function updatecontentautomaticcampaign($data, $idAutomaticCampaign) {

    $campaignConfiguration = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));
    if (!$campaignConfiguration) {
      throw new \InvalidArgumentException("La campaña consultada no se encuentra registrada.");
    }
    $campaignConfiguration->idAutomaticCampaign = $idAutomaticCampaign;
    $campaignConfiguration->configuration = json_encode($data);
    if (!$campaignConfiguration->save()) {
      foreach ($campaignConfiguration->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function saveautomaticcampaigndraft() {
    $campaign = new \AutomaticCampaign();
    $date = date('Y-m-d H:i');
    $campaign->name = "Borrador {$date}";
    $campaign->startDate = $date;
    $campaign->idSubaccount = $this->user->Usertype->idSubaccount;
    $campaign->status = 'draft';

    if (!$campaign->save()) {
      foreach ($campaign->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $campaign;
  }

  public function updateStatusCampaign($status, $idautomaticcampaign) {
    $campaign = \AutomaticCampaign::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idautomaticcampaign)));
    if (!$campaign) {
      throw new \InvalidArgumentException("La campaña consultada no se encuentra registrada.");
    }
    if ($campaign->status == "executing") {
      throw new \InvalidArgumentException("La campaña ya se encuentra en ejecución, no se puede actualizar el estado.");
    }
    $campaign->status = $status;

    if (!$campaign->save()) {
      foreach ($campaign->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $campaign;
  }

  public function validateConfiguration($configuration) {
    $configuration = json_decode($configuration);
    $Var = [];
    for ($i = 0; $i < count($configuration->connections); $i++) {
      $objConnection = $this->getConnection($configuration, $configuration->connections[$i]->dest->nodeID);
      if (!isset($objConnection->dest)) {
        $node = $this->getNode($configuration, $configuration->connections[$i]->dest->nodeID);
        if ($node->theme == "operator") {
          throw new \InvalidArgumentException("No se puede terminar una raiz con un operador, por favor revisar.");
        }
      }
    }
    return true;
//    $objReturn = new \stdClass();
//    $objReturn->nodes = [];
//    $objReturn->connections = [];
//    if ($configuration->nodes[0]->method != "primary") {
//      array_push($objReturn->nodes, $this->getPrimary($configuration));
//    } else {
//      array_push($objReturn->nodes, $configuration->nodes[0]);
//    }
//    $connection = $this->getConnection($configuration, 0);
//    array_push($objReturn->connections, $connection[0]);
//
//    while (true) {
//      $lastConnection = $objReturn->connections[count($objReturn->connections)-1];
//      $node = $this->getNode($configuration,$lastConnection->dest->nodeID);
//      array_push($objReturn->nodes, $node);
//      $connection = $this->getConnection($configuration, $node->id);
//      if(count($connection)>1){
//        $this->createTree($connection,$objReturn);
//      }
//      
//      if(!isset($connection[0]->dest)){
//        break;
//      }
//      array_push($objReturn->connections, $connection[0]);
//    }
//    var_dump($objReturn);
//    throw new \InvalidArgumentException("final");
//    return $objReturn;
  }

  private function getPrimary($configuration) {
    for ($i = 0; $i < count($configuration->nodes); $i++) {
      if ($configuration->nodes[$i]->method == "primary") {
        return $configuration->nodes[$i];
      }
    }
  }

  private function getConnection($configuration, $idNode) {
    $objReturn = new \stdClass();
    $arrReturn = array();
    for ($i = 0; $i < count($configuration->connections); $i++) {
      if ($configuration->connections[$i]->source->nodeID == $idNode) {
        $objReturn->dest = $configuration->connections[$i]->dest;
//        $arrReturn[] = $configuration->connections[$i];
      }
      if ($configuration->connections[$i]->dest->nodeID == $idNode) {
        $objReturn->source = $configuration->connections[$i]->source;
      }
    }
    return $objReturn;
  }

  private function getNode($configuration, $idNode) {
    for ($i = 0; $i < count($configuration->nodes); $i++) {
      if ($configuration->nodes[$i]->id == $idNode) {
        return $configuration->nodes[$i];
      }
    }
  }

  public function cancelAutCamp($idAutomaticCampaign) {
    
    try {
      if (!$idAutomaticCampaign) {
        throw new \InvalidArgumentException("La campaña consultada no se encuentra registrada.");
      }
      if ($idAutomaticCampaign) {
        $auto_camp = \AutomaticCampaign::findFirst(array("conditions" => "idAutomaticCampaign = ?0 AND status != ?1 ", "bind" => array($idAutomaticCampaign,'completed')));
        if($auto_camp != false){
          $auto_camp_step = \AutomaticCampaignStep::find(array("conditions" => "idAutomaticCampaign = ?0 AND status != ?1 ", "bind" => array($idAutomaticCampaign,'sent')));
          if($auto_camp_step != false){   
            foreach ($auto_camp_step as $step){
              if(isset($step->idSms)){
                $this->cancelSms($step->idSms);
                $step->status = 'canceled';
                $step->update();
              }
              if(isset($step->idMail)){
                $this->cancelMail($step->idMail);
                $step->status = 'canceled';
                $step->update();
              }
            }
          }
          $auto_camp->status = 'canceled';
          $auto_camp->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
          $auto_camp->update();
        }
      }
      return ["message" => "La campaña automática se ha cancelado con exito!"];
    } catch (\InvalidArgumentException $ex) {
      return $ex->getMessage();
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }
  
  public function cancelSms($idSms){
    $sms = \Sms::findFirst(array(
      'conditions' => 'idSms = ?0',
      'bind' => array(0 => $idSms)
    ));
    if (!$sms) {
      return "No se encontró el sms, por favor valida la información";
    }
    $sms->status = 'canceled';
    if (!$sms->update()) {
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return "Se ha cancelado con exito el envio de sms";
  }

  public function cancelMail($idMail){
    $mail = \Mail::findFirst(array(
      'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
      'bind' => array(0 => $idMail, 1 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
    ));

    if (!$mail) {
      return "Ocurrio un error, no se encontro la informacion basica de  correo";
    }

    if ($mail->status == "sending" || $mail->status == "paused" || $mail->status == "scheduled") {
      if ($mail->status == "paused" || $mail->status == "scheduled") {
        $mail->status = "canceled";
        $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
        $mail->update();        
        //
        $customLogger = new \Logs();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->idMail = $mail->idMail;
        $customLogger->idUser = \Phalcon\DI::getDefault()->get("user")->idUser;
        $customLogger->name = \Phalcon\DI::getDefault()->get("user")->name;
        $customLogger->lastname = \Phalcon\DI::getDefault()->get("user")->lastname;
        $customLogger->email = \Phalcon\DI::getDefault()->get("user")->email;
        $customLogger->cellphone = \Phalcon\DI::getDefault()->get("user")->cellphone;
        $customLogger->status = "canceled";
        $customLogger->typeName = "RegisterMailCancelOnly";
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
      
        return "El envío se ha cancelado correctamente";
      } else if ($mail->status == "sending") {
        $mxc = \Mxc::count([["idMail" => $mail->idMail, "status" => 'sent']]);
        if($mxc == false || $mail->uniqueOpening = 0 || $mail->totalOpening = 0 || $mail->uniqueClicks = 0 ){
          $data = array(
            "idMail" => $mail->idMail,
            "nameFunc" => "cancel"
          );
          $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
          $elephant->initialize();
          $elephant->emit('cancel-send-mail', $data);
          $elephant->close();
          $mail->status = "canceled";
          $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
          $mail->update();           
          //
          $customLogger = new \Logs();
          $customLogger->registerDate = date("Y-m-d h:i:sa");
          $customLogger->idMail = $mail->idMail;
          $customLogger->idUser = \Phalcon\DI::getDefault()->get("user")->idUser;
          $customLogger->name = \Phalcon\DI::getDefault()->get("user")->name;
          $customLogger->lastname = \Phalcon\DI::getDefault()->get("user")->lastname;
          $customLogger->email = \Phalcon\DI::getDefault()->get("user")->email;
          $customLogger->cellphone = \Phalcon\DI::getDefault()->get("user")->cellphone;
          $customLogger->status = "canceled";
          $customLogger->typeName = "RegisterMailCancelAll";
          $customLogger->created = time();
          $customLogger->updated = time();
          $customLogger->save();
          
          return "El envío se ha cancelado correctamente";
        } else {
          return "Los correos están siendo entregados a sus respectivos destinatarios";
        }
      }
    } else {
      return "Los correos están siendo entregados a sus respectivos destinatarios.";
    }
  }

}
