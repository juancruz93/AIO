<?php

/**
 * @RoutePrefix("/api/autoresponder")
 */
class ApiautoresponderController extends ControllerBase {

  /**
   * @Post("/save/{idAutoresponder:[0-9]+}")
   */
  public function saveautoresponderAction($idAutoresponder) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (empty($arrayData)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
      if($idAccount == 1387 || $idAccount == 49 && $arrayData["optionAdvance"] == 1){
        $date = new DateTime("now", new DateTimeZone('America/Bogota') );
          if($arrayData["time"] < $date->format('h:i')){
            throw new InvalidArgumentException("La hora de envío de la autorespuesta {$idAutoresponder} es menor a la actual.");
          }
      }
      if ($idAutoresponder == 0) {
        $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
        $autoresponder = $wrapper->createAutoresponder($arrayData);
        $this->trace("success", "La autorespuesta ha sido creada");

        return $this->set_json_response(["message" => "La autorespuesta ha sido creada exitosamente", "autoresponder" => $autoresponder]);
      } else {
        $autoresponder = Autoresponder::findFirst(array(
                    'conditions' => 'idAutoresponder = ?0',
                    'bind' => [$idAutoresponder]
        ));
        if (!$autoresponder) {
          throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
        }
        $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
        $autoresponder = $wrapper->editAutoresponder($arrayData, $autoresponder);
        $this->trace("success", "La autorespuesta ha sido editada");

        return $this->set_json_response(["message" => "La autorespuesta ha sido editada exitosamente", "autoresponder" => $autoresponder]);
      }
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  
  /**
   * @Post("/saveautosms/{idAutoresponder:[0-9]+}")
   */
  public function saveautorespdesmsAction($idAutoresponder) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      if (empty($arrayData)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
  
      if ($idAutoresponder == 0) {
        $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
        //$autoresponder = $wrapper->createAutoresponder($arrayData);
        $autoresponder = $wrapper->createAutorespdesms($arrayData);
        $this->trace("success", "La autorespuesta ha sido creada");

        return $this->set_json_response(["message" => "La autorespuesta ha sido creada exitosamente", "autoresponder" => $autoresponder]);
      } else {
        $autoresponder = Autoresponder::findFirst(array(
                    'conditions' => 'idAutoresponder = ?0',
                    'bind' => [$idAutoresponder]
        ));
        if (!$autoresponder) {
          throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
        }
        $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
        $autoresponder = $wrapper->editAutorespdesms($arrayData, $autoresponder);
        $this->trace("success", "La autorespuesta ha sido editada");

        return $this->set_json_response(["message" => "La autorespuesta ha sido editada exitosamente", "autoresponder" => $autoresponder]);
      }
    } catch (InvalidArgumentException $ex) {
      //$this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  

  /**
   * @Post("/savecontenteditor/{idAutoresponder:[0-9]+}")
   */
  public function savecontentautoresponderAction($idAutoresponder) {
    try {
      $autoresponder = Autoresponder::findFirst(array(
                  'conditions' => 'idAutoresponder = ?0',
                  'bind' => [$idAutoresponder]
      ));
      if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (empty($arrayData)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      $autoresponderContent = $autoresponder->AutoresponderContent;
      if (!$autoresponderContent) {
        $autoresponderContent = $wrapper->createContentEditorAutoresponder($idAutoresponder, $arrayData);
        $this->trace("success", "El contenido de la autorespuesta ha sido creado");

        return $this->set_json_response(["message" => "El contenido de la autorespuesta ha sido creado exitosamente", "operation" => "create", "autoresponderContent" => $autoresponderContent]);
      } else {
        $autoresponderContent = $wrapper->editContentEditorAutoresponder($arrayData, $autoresponderContent);
        $this->trace("success", "El contenido de la autorespuesta ha sido actualizado");

        return $this->set_json_response(["message" => "El contenido de la autorespuesta ha sido actualizado exitosamente", "operation" => "edit", "autoresponderContent" => $autoresponderContent]);
      }
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getautoresponse/{idAutoresponder:[0-9]+}")
   */
  public function getautoresponseAction($idAutoresponder) {
    try {
      $autoresponder = Autoresponder::findFirst(array(
                  'conditions' => 'idAutoresponder = ?0',
                  'bind' => [$idAutoresponder]
      ));
      if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }
      if ($autoresponder->AutoresponderContent->type == "Editor") {
        $autoresponder->AutoresponderContent->url = "autoresponder/contenteditor/";
      } else if ($autoresponder->AutoresponderContent->type == "html") {
        $autoresponder->AutoresponderContent->url = "autoresponder/contenthtml/";
      }
      if($autoresponder->morecaracter == 0){
         $autoresponder->morecaracter = false;
      }else{
         $autoresponder->morecaracter = true; 
      }

      return $this->set_json_response(["autoresponder" => $autoresponder]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallautoresponder/{page:[0-9]+}")
   */
  public function getallautoresponderAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      return $this->set_json_response($wrapper->getAllAutoresponder($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{id:[0-9]+}", methods="DELETE")
   */
  public function deleteautoresponderAction($idAutoresponder) {
    try {
      if (empty($idAutoresponder)) {
        throw new InvalidArgumentException("No ha seleccionado una autorespuesta a eliminar, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      $wrapper->deleteAutoresponder($idAutoresponder);
      $this->trace("success", "La autorespuesta ha sido eliminada");

      return $this->set_json_response(["message" => "La autorespuesta ha sido eliminada exitosamente"]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete autoresponder ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getautoresponder/{idAutoresponder:[0-9]+}")
   */
  public function getautoresponderAction($idAutoresponder) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      $arr = array("autoresponder" => $wrapper->getAutoresponderExists($idAutoresponder));
      return $this->set_json_response($arr, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Post("/getalledit/")
   */
  public function getalleditAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      //$wrapper = new \Sigmamovil\Wrapper\SmstwowayWrapper();
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      return $this->set_json_response($wrapper->getDataEdit($data));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    /**
   * @Get("/getallcustomfield/{idContactlist:[0-9]+}")
   */
  public function getallCustomFieldAction($idContactlist) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\AutoResponderWrapper();
      return $this->set_json_response($wrapper->findcustomfields($idContactlist));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding automaticcampaigncategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/addcustomfield")
   */
  public function addcustomfieldAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $id = $data["id"];
      $name = str_replace("%","",$data["name"]);
      $idContactList = $data["idContactList"];

      if($id == 0){
        //CREAMOS EL  REGISTRO DE CAMPO PERSONALIZADO COMBINADO
        $customfield = new \Customfield();
        $customfield->idContactlist = $idContactList;
        $customfield->idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
        $customfield->name = str_replace("&","-",$name);      
        $customfield->alternativename = strtolower($name);
        $customfield->type = "Text";
        $customfield->deleted = 0;
        $this->db->begin();
        if (!$customfield->save()) {
          $this->db->rollback();
          foreach ($customfield->getMessages() as $msg) {
            $this->logger->log("Message: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
        $this->db->commit();
        $customfield->idCustomfield = $customfield->idCustomfield;
        return $this->set_json_response(array("message" => "Se ha CREADO el campo personalizado ".$name." exitosamente", "customfield" => $customfield), 200);
      }
      //SI EL ID ENVIADO ES DIFERENTE A CERO EDITAMOS EL CAMPO PERSONALIZADO COMBINADO
      $customfield = Customfield::findFirst(array(
        'conditions' => "idCustomfield = ?0", 
        'bind' => array($id)
      ));
      
      if(!$customfield){
        $this->logger->log("Message: No se ha encontrado el campo personalziado con ID {$id}");
        throw new \InvalidArgumentException("Message: No se ha encontrado el campo personalziado con ID {$id}");
      }
      
      $customfield->idContactlist = $idContactList;
      $customfield->idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
      $customfield->name = str_replace("&","-",$name);      
      $customfield->alternativename = strtolower($name);
      $customfield->type = "Text";
      $customfield->deleted = 0;
      $this->db->begin();
      if (!$customfield->save()) {
        $this->db->rollback();
        foreach ($customfield->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      $this->db->commit();
      $customfield->idCustomfield = $customfield->idCustomfield;
      
      return $this->set_json_response(array("message" => "Se ha EDITADO el campo personalizado ".$name." exitosamente", "customfield" => $customfield), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
}
