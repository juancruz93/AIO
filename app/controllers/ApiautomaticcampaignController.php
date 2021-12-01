<?php

use Sigmamovil\Wrapper\AutomaticcampaignWrapper as ac;

/**
 * @RoutePrefix("/api/automacamp")
 */
class ApiautomaticcampaignController extends ControllerBase {

   
    
  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listautocampAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);

      $wrapper = new ac();
      return $this->set_json_response($wrapper->listautomaticcampaign($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/save")
   */
  public function saveautomaticcampaignAction() {
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new ac();

      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }
      if (!$wrapper->validateConfiguration($arraydata->objCampaign)) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }
      $campaignAutomatic = $wrapper->saveautomaticcampaign($arraydata->formCampaign);
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      if (!$wrapper->savecontentautomaticcampaign($arraydata->objCampaign, $campaignAutomatic->idAutomaticCampaign)) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }
      $arrayResponse = array("message" => "La automatización de campaña ha sido guardada exitosamente");
      $this->db->commit();

      $this->trace("success", "La automatización de campaña ha sido guardada");
      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savedraft")
   */
  public function savedraftautomaticcampaignAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new ac();

      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }

      if (!$wrapper->validateConfiguration(json_encode($arraydata))) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      $campaignAutomatic = $wrapper->saveautomaticcampaigndraft();
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      if (!$wrapper->savecontentautomaticcampaign($arraydata, $campaignAutomatic->idAutomaticCampaign)) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      $arrayResponse = array("message" => "La automatización de campaña ha sido guardada exitosamente", "idCampaign" => $campaignAutomatic->idAutomaticCampaign);
      $this->trace("success", "La automatización de campaña ha sido guardada");

      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getautomaticcampaign/{idautomaticcampaign:[0-9]+}")
   */
  public function getautomaticcampaignAction($idautomaticcampaign) {
    $wrapper = new ac();
    try {
      $campaign = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idautomaticcampaign)));

      if (!$campaign) {
        throw new \InvalidArgumentException("No se encontro en resgistrado la automatizacion de la campaña enviada.");
      }
      if ($campaign->AutomaticCampaign->status != 'draft' && $campaign->AutomaticCampaign->status != 'confirmed') {
        throw new \InvalidArgumentException("Validar el estado de la campaña con el id {$idautomaticcampaign}.");
      }
      if ($campaign->AutomaticCampaign->deleted != 0) {
        throw new \InvalidArgumentException("La campaña se encuentra eliminada.");
      }
      $wrapper->setAutomaticCampaign($campaign);
      $wrapper->validateCampaignData();
      return $this->set_json_response(array('data' => $wrapper->getautomaticcapaign($idautomaticcampaign)), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array(
                  'message' => $ex->getMessage(),
                  'data' => $wrapper->getautomaticcapaign($idautomaticcampaign)
                      ), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/updatecampaign/{idautomaticcampaign:[0-9]+}")
   */
  public function updateautomaticcampaignAction($idautomaticcampaign) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new ac();

      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }

      $campaignAutomatic = $wrapper->updateautomaticcampaign($arraydata, $idautomaticcampaign);
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando la campaña");
      }
      $arrayResponse = array("message" => "La automatización de campaña ha sido actualizado exitosamente");
      $this->trace("success", "La automatización de campaña ha sido actualizada");
      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/updatecampaignconfiguration/{idautomaticcampaign:[0-9]+}")
   */
  public function updateautomaticcampaignconfigurationAction($idautomaticcampaign) {
    try {
      $dataJson = $this->request->getRawBody();
      $objConfiguration = new stdClass();

      $arraydata = json_decode($dataJson);
      $objConfiguration->configuration = json_encode($arraydata);
      $wrapper = new ac();
      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }

      $wrapper->setAutomaticCampaign($objConfiguration);
      $wrapper->validateCampaignData();
      $campaignAutomatic = $wrapper->updatecontentautomaticcampaign($arraydata, $idautomaticcampaign);
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando correctamente el contenido de la campaña");
      }
      $arrayResponse = array("message" => "La automatización de campaña ha sido actualizado exitosamente");
      $this->trace("success", "La automatización de campaña ha sido actualizada");

      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/updatecampaignall/{idautomaticcampaign:[0-9]+}")
   */
  public function updatecampaignallAction($idautomaticcampaign) {
    try {
      $this->db->begin();
      $objConfiguration = new stdClass();
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new ac();
      $objConfiguration->configuration = json_encode($arraydata->objCampaign);

      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }

      $wrapper->setAutomaticCampaign($objConfiguration);
      $wrapper->validateCampaignData();

      $campaignAutomatic = $wrapper->updateautomaticcampaign($arraydata->formCampaign, $idautomaticcampaign);
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      if (!$wrapper->updatecontentautomaticcampaign($arraydata->objCampaign, $campaignAutomatic->idAutomaticCampaign)) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }
      $arrayResponse = array("message" => "La automatización de campaña ha sido guardada exitosamente");
      $this->trace("success", "La automatización de campaña ha sido guardada");

      $this->db->commit();
      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/updatestatus/{idautomaticcampaign:[0-9]+}")
   */
  public function updatestatusAction($idautomaticcampaign) {
    try {

      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new ac();
      if (empty($arraydata)) {
        throw new \InvalidArgumentException("Verificar la informacion enviada");
      }

      $campaignAutomatic = $wrapper->updateStatusCampaign($arraydata->status, $idautomaticcampaign);
      if (!$campaignAutomatic) {
        throw new \InvalidArgumentException("Ocurrio un error guardando en la base de datos la campaña");
      }

      $arrayResponse = array("message" => "El estado de la campaña se actualizo correctamente.");
      $this->trace("success", "La estado de la campaña ha sido actualizado");

      return $this->set_json_response($arrayResponse);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getscheme/{id:[0-9]+}")
   */
  public function getschemeautomaticcampaignAction($idAutomaticCampaign) {
    try {
      $campaign = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));
      $wrapper = new ac();
      $wrapper->setAutomaticCampaign($campaign);
      $wrapper->validateCampaignData();
      return $this->set_json_response(array('data' => $wrapper->getautomaticcapaign($idAutomaticCampaign)), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding content for scheme ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Post("/cancelautomaticcampaign")
   */
  
  public function cancelautomaticcampaignAction() {
    
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
       if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      
      $wraper = new \Sigmamovil\Wrapper\AutomaticcampaignWrapper();
      return $this->set_json_response($wraper->cancelAutCamp($data), 200);
    }catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    }catch (Exception $ex) {
      $this->logger->log("Exception while finding content for scheme ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
   }
  }

  public function clonecaAction($idAutomaticCampaign) {
    try {

      $campaign = \AutomaticCampaign::findFirst(array(
        "conditions" => "idAutomaticCampaign = ?0", 
        "bind" => array($idAutomaticCampaign)
      ));
      if (!$campaign) {
        throw new \InvalidArgumentException("La campaña consultada no se encuentra registrada.");
      }

      $date = new DateTime();
      $date->setTimezone(new DateTimeZone('America/Bogota'));

      $newCampaign = new \AutomaticCampaign();
      $newCampaign->name = $campaign->name." (copia)";
      $newCampaign->idAutomaticCampaignCategory = $campaign->idAutomaticCampaignCategory;
      $newCampaign->startDate = $date->format('Y-m-d H:i');
      $newCampaign->endDate = "0000-00-00 00:00";
      $newCampaign->description = $campaign->description;
      $newCampaign->gmt = $campaign->gmt;
      $newCampaign->created =  time();
      $newCampaign->updated = time();
      $newCampaign->createdBy = $campaign->createdBy;
      $newCampaign->updatedBy = $campaign->updatedBy;
      $newCampaign->idSubaccount = $this->user->Usertype->idSubaccount;
      $newCampaign->status = 'draft';

      if (!$newCampaign->save()) {
        foreach ($newCampaign->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }

      $campaignConfiguration = \AutomaticCampaignConfiguration::findFirst(array(
        "conditions" => "idAutomaticCampaign = ?0",
        "bind" => array($idAutomaticCampaign)
      ));
      if (!$campaignConfiguration) {
        throw new Exception("no existe la configuracion de la campaña automatica");
      }
      
      $oldConfiguration = json_decode($campaignConfiguration->configuration);
      
      foreach($oldConfiguration->nodes as $key => $value){
        foreach($value as $key1 => $value1){
            if($key1 == "sentData"){
                if($value1->idAssets){
                    $sentDataTMP = [];
                    $value1->idAssets = $sentDataTMP;
                }
            }
            if($key1 == "sendData"){
                if($value1->idAssets){
                    $sendDataTMP = [];
                    $value1->idAssets = $sendDataTMP;
                }
            }
            if($key1 == "dataForm"){
                if($value1->idAssets){
                    $dataFormTMP = [];
                    $value1->idAssets = $dataFormTMP;
                }
            }
        }
      }

      $newCampaignConfiguration = new \AutomaticCampaignConfiguration();
      $newCampaignConfiguration->idAutomaticCampaign = $newCampaign->idAutomaticCampaign;
      $newCampaignConfiguration->created =  time();
      $newCampaignConfiguration->updated = time();
      $newCampaignConfiguration->createdBy = $campaignConfiguration->createdBy;
      $newCampaignConfiguration->updatedBy = $campaignConfiguration->updatedBy;
      $newCampaignConfiguration->configuration = json_encode($oldConfiguration);
      if (!$newCampaignConfiguration->save()) {
        foreach ($newCampaignConfiguration->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }

      return $this->response->redirect("automaticcampaign#/edit/{$newCampaign->idAutomaticCampaign}");

    }catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    }catch (Exception $ex) {
      $this->logger->log("Exception while finding content for scheme ApiautomaticcampaignController ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
   }
  }

}
