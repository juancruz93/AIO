<?php

use Sigmamovil\General\Automatic\TemplateMailManager;
use \Sigmamovil\General\Misc\AutomaticCampaignObj;

require_once(__DIR__ . "/../bootstrap/index.php");


if (isset($argv[1])) {
  $idAutomaticCampaign = $argv[1];
}

$startCampaign = new StartAutomaticSender();
$startCampaign->startAutomatic($idAutomaticCampaign);

class StartAutomaticSender {

  protected $campaign;
  protected $campaignConfiguration;
  protected $validatorCampaign;
  protected $objCampaignConfiguration;
  protected $subAccount;
  protected $TemplateMailManager;
  protected $inIdcontact;
  protected $idContactlist;
  protected $automaticCampaignObj;

  public function __construct() {
    
  }

  public function setSubAccount(\Subaccount $subAccount) {
    $this->subAccount = $subAccount;
    $this->TemplateMailManager->setSubAccount($subAccount);
  }

  public function saveAutomatic($objSave) {
    if (!$objSave->save()) {
      foreach ($objSave->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function startAutomatic($idAutomaticCampaign) {
    try {
      //CONSULTAMOS LA CONDIGURACION AUTOMATICA
      $campaignConfiguration = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));
      if (!$campaignConfiguration) {
        throw new Exception("no existe la configuracion de la campaña automatica");
      }

      //SETEAMOS LAS VARIABLES 
      $this->campaign = $campaignConfiguration->AutomaticCampaign;
      if($this->campaign->status != "confirmed"){
        throw new Exception("la campaña no esta programada.");
      }
      $this->campaign->status = 'executing';

      $this->saveAutomatic($this->campaign);
      $campaignConfiguration->AutomaticCampaign->status = "executing";

      // SE CAMBIA EL STATUS DE LA CAMPAÑA
      $this->campaignConfiguration = $campaignConfiguration;
      $this->objCampaignConfiguration = json_decode($campaignConfiguration->configuration);
      $this->validatorCampaign = new \CampaignValidator(json_decode($campaignConfiguration->configuration));
      $this->TemplateMailManager = new TemplateMailManager($this->campaign);
      $this->automaticCampaignObj = new AutomaticCampaignObj($this->campaign, $this->campaignConfiguration);
      $this->setSubAccount($campaignConfiguration->AutomaticCampaign->Subaccount);

      //EJECUTAMOS A CLONAR
      $this->CloneTemplate();
    } catch (InvalidArgumentException $ex) {
      $this->campaign->status = 'canceled';
      $this->saveAutomatic($this->campaign);
      echo $ex->getMessage();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      $this->campaign->status = 'canceled';
      $this->saveAutomatic($this->campaign);
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  public function CloneTemplate() {
    $this->validatorCampaign->setSubAccount($this->subAccount);
    $this->validatorCampaign->validate();
    $FirstNode = $this->automaticCampaignObj->getNode(0);
    $firstConnection = $this->automaticCampaignObj->searchConnection(0);
    $SecondNode = $this->automaticCampaignObj->getNode($firstConnection["dest"]);
    $target = $this->automaticCampaignObj->transformTarget($FirstNode->sendData);
    if ($SecondNode->method == "email") {
      $this->TemplateMailManager->setDataMail($SecondNode->sendData);
      $this->TemplateMailManager->setTarget($target);
      $this->TemplateMailManager->setContentTemplate();
      $this->TemplateMailManager->cloneMail();
      $this->getIdContaclist($target);
      $this->getAllCxcl();
      $this->newNextStep($SecondNode);
     
    } else if ($SecondNode->method == "sms") {
      $this->TemplateMailManager->setTargetSms($FirstNode->sendData);
      $this->TemplateMailManager->setDataMail($SecondNode->sendData);
      $this->TemplateMailManager->setContentTemplateSms();
      $this->TemplateMailManager->cloneSms();
      $this->getIdContaclist($target);
      $this->getAllCxcl();
      $this->newNextStep($SecondNode);
    }
  }

  public function newNextStep($sms) {
var_dump("no debe pasar");exit();
    $connection = $this->automaticCampaignObj->searchConnection($sms->id);
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
		
				foreach ($this->inIdcontact as $key) {
                  $this->automaticCampaignObj->insNewStep($key, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
				}
            } else {
              if (isset($nodeNegation)) {
				foreach ($this->inIdcontact as $key) {
					$this->automaticCampaignObj->insNewStep($key, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
				}
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
    /*$node =  $this->automaticCampaignObj->getNode($connectionOperator["dest"]);
    $idNode = $connectionOperator["dest"];
    $date = $this->automaticCampaignObj->getDataTime($nextOperator);
    foreach ($this->inIdcontact as $key) {
      $this->automaticCampaignObj->insNewStep($key, $idNode, $node, "time", $date);
//      $this->automaticCampaignObj->insertNextStepNoOpen($key, "time");
    }*/
    unset($this->inIdcontact);
  }

  public function getIdContaclist($target) {
    $target = json_decode($target);
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
        break;
      case "segment":
        if (isset($target->segment)) {
          $this->getIdContactlistBySegments($target->segment);
        }
        break;
      default:
        throw new Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
    }
  }

  public function getIdContactlistBySegments($listSegment) {
    foreach ($listSegment as $key) {
      $segment = Segment::findFirst([["idSegment" => $key->idSegment]]);
      foreach ($segment->contactlist as $k) {
        $this->idContactlist[] = $k["idContactlist"];
      }
      unset($segment);
    }
  }

  public function getAllCxcl() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist IN ({$idContactlist})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 ";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    };
    unset($sql);
    unset($cxcl);
  }

  public function UpdateStatusCampaign($status) {
    $this->campaign->status = $status;
    if (!$this->campaign->save()) {
      foreach ($this->campaign->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function getNodeFormItemArray($idNode) {
    return $this->objCampaignConfiguration->nodes[$idNode];
  }

}
