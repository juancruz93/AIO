<?php

/**
 * @RoutePrefix("/api/survey")
 */
class ApisurveyController extends ControllerBase {

  /**
   * @Get("/getpublicsurvey")
   */
  public function getpublicsurveyAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->getPublicsurvey());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/list/{page:[0-9]+}")
   */
  public function listAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->listSurvey($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listcategory")
   */
  public function listcategoryAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->getAllSurveyCategories());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/createsurvey")
   */
  public function createsurveyAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      $survey = $wrapper->saveSurvey($arraydata);
      $this->trace("success", "Se ha guardado una encuesta");

      return $this->set_json_response(['message' => 'La encuesta ha sido creada con exito', 'survey' => $survey]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/findsurvey/{idSurvey:[0-9]+}")
   */
  public function findsurveyAction($idSurvey) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->findSurvey($idSurvey));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/editsurvey/{idSurvey:[0-9]+}")
   */
  public function editsurveyAction($idSurvey) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      $survey = $wrapper->editSurvey($arraydata, $idSurvey);
      $this->trace("success", "Se ha editado una encuesta");

      return $this->set_json_response(['message' => 'La encuesta ha editada con exito', 'survey' => $survey]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding survey ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savecontent/{idSurvey:[0-9]+}")
   */
  public function savecontentsurveyAction($idSurvey) {
    try {
      $survey = Survey::findFirst(array(
                  'conditions' => 'idSurvey = ?0',
                  'bind' => [$idSurvey]
      ));
      if (!$survey) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }
      
      if($survey->status == "draft"){
        
        $dataJson = $this->request->getRawBody();
          $arrayData = json_decode($dataJson, true);
    
          if (empty($arrayData)) {
            throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
          }
          $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
          $surveyContent = $survey->SurveyContent;
    
          $arrayData = $this->publicImg($arrayData);
    //      var_dump($arrayData);
    //      exit();
          if (!$surveyContent) {
            $surveyContent = $wrapper->createContentSurvey($idSurvey, $arrayData);
            $this->trace("success", "Se ha guardado el contenido de una encuesta");
    
            return $this->set_json_response(["message" => "El contenido de la encuesta ha sido creado exitosamente", "operation" => "create", "surveyContent" => $surveyContent]);
          } else {
            $surveyContent = $wrapper->editContentSurvey($arrayData, $surveyContent);
            $this->trace("success", "Se ha actualizado el contenido de una encuesta");
    
            return $this->set_json_response(["message" => "El contenido de la encuesta ha sido actualizado exitosamente", "operation" => "edit", "surveyContent" => $surveyContent]);
          }
        
      }
      
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcontent/{idSurvey:[0-9]+}")
   */
  public function getsurveycontentAction($idSurvey) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->getSurveyContent($idSurvey));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/saveconf")
   */
  public function saveconfirmationAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->saveConfirmation($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/linkgene/{idSurvey:[0-9]+}")
   */
  public function linkgeneratorAction($idSurvey) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->linkGenerator($idSurvey));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while generate link survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/saveanswer/{idSurvey:[0-9]+}/{idContact}")
   */
  public function saveanswerAction($idSurvey, $idContact) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      if ($wrapper->saveAnswerSurvey($idSurvey, $idContact, $data)) {
        //Se redirecciona a la página de "agradeciemiento" si la encuesta se guarda satisfactoriamente
        return $this->set_json_response(['message' => 'success', "url" => "{$this->urlManager->get_base_uri(true)}survey/congratulations/{$idSurvey}"]);
      }
      //return $this->set_json_response(['message' => 'La respuesta se ha guardado con exito']);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save answer survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/uploadimage")
   */
  public function uploadimageAction() {
    try {
      $space = $this->getSpaceInAccount();

      if (!$space) {
        return $this->set_json_response(array('error' => 'Ha sobrepasaso el limite de espacio en disco. para liberar espacio en disco elimine imágenes o archivos que considere innecesarios'), 401, 'Ha sobrepasado el limite de espacio en disco!');
      } else if (empty($_FILES['file']['name'])) {
        return $this->set_json_response(array('error' => 'No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información'), 400, 'Archivo vacio o incorrecto');
      } else {
        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        $type = $_FILES['file']['type'];
        $tmp_dir = $_FILES['file']['tmp_name'];
        $message = "Imagen cargada con exito";
        if ($size > $this->uploadConfig->imgAssetMin) {
          $message = "La imagen cargada puede ocasionar un tiempo de carga mayor a la hora de abrir el Correo electrónico";
        }
      }
      $account = $this->user->Usertype->Subaccount->Account;
      $assetObj = new \Sigmamovil\General\Misc\AssetObj($account);

      $assetObj->createImage($name, $type, $tmp_dir, $size);
      $idAsset = $assetObj->getIdAsset();

      $array = array(
          'filelink' => $assetObj->getImagePrivateUrl(),
          'thumb' => $assetObj->getThumbnailUrl(),
          'title' => $assetObj->getFileName(),
          'id' => $idAsset,
          'message' => $message
      );
      return $this->set_json_response($array);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding uploadimageAction(DASHBOARDCONFIG)... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding uploadimageAction(DASHBOARDCONFIG)... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/sendmail")
   */
  public function sendmailAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      $message = $wrapper->sendMail($data);
      $this->trace("success", "Se ha creado el correo de encuesta correctamente");
      return $this->set_json_response($message);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/changestatus/{idSurvey:[0-9]+}")
   */
  public function changestatusAction($idSurvey) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      if (!$wrapper->changeStatus($data, $idSurvey)) {
        throw new InvalidArgumentException("Ocurrio un error cambiando el estado de la encuesta, por favor cotacte al administrador.");
      }
      $this->trace("success", "Se ha actualizado el estado de la encuesta correctamente.");
      return $this->set_json_response(array('message' => "se ha actualizado el estado de la encuesta correctamente."));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/changetype/{idSurvey:[0-9]+}")
   */
  public function changetypeAction($idSurvey) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      if (!$wrapper->changeType($data, $idSurvey)) {
        throw new InvalidArgumentException("Ocurrio un error cambiando el tipo de la encuesta, por favor cotacte al administrador.");
      }
      $this->trace("success", "Se ha actualizado el tipo de la encuesta correctamente.");
      return $this->set_json_response(array('message' => "se ha actualizado el tipo de la encuesta correctamente."));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function getSpaceInAccount() {

    $account = $this->user->Usertype->Subaccount->Account;
    $phql = "SELECT SUM(Asset.size) cnt FROM Asset WHERE Asset.idAccount = :idAccount:";
    $result = $this->modelsManager->executeQuery($phql, array('idAccount' => $account->idAccount));

    $space = ($result->getFirst()->cnt / 1048576);

    if ($space >= $account->AccountConfig->diskSpace) {
      return false;
    }
    return true;
  }

  /**
   * @Get("/getcategory/{idCategory:[0-9]+}")
   */
  public function getcategoryAction($idCategory) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      return $this->set_json_response($wrapper->getCategory($idCategory));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while generate link survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  private function publicImg($data) {


    for ($i = 0; $i < count($data['content']); $i++) {
//      $content = $data['content'][$i];
      if (isset($data['content'][$i]['objExt']['srcImage'])) {
        if (preg_match('/http/', $data['content'][$i]['objExt']['srcImage'])) {
          break;
        }
        if (preg_match('/asset/i', $data['content'][$i]['objExt']['srcImage'])) {
          $idAsset = filter_var($data['content'][$i]['objExt']['srcImage'], FILTER_SANITIZE_NUMBER_INT);
          $data['content'][$i]['objExt']['srcImage'] = $this->getCompletePrivateImageSrc($idAsset);
//          array_splice($data['content'],$i,1,$content);
        }
      }
    }
    return $data;
  }

  private function getCompletePrivateImageSrc($idAsset) {

    $asset = Asset::findFirst(array(
                "conditions" => "idAsset = ?0",
                "bind" => array($idAsset)
    ));

    if (isset($asset->idAccount)) {
      $ext = pathinfo($asset->name, PATHINFO_EXTENSION);
      $img = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . "/" . $this->urlManager->get_url_asset() . "/" . $asset->idAccount . "/images/" . $asset->idAsset . "." . $ext;
//			$this->logger->log("Link final: {$img}");
      return $img;
    } else if (isset($asset->idAllied)) {
      $ext = pathinfo($asset->name, PATHINFO_EXTENSION);
      $img = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . "/" . $this->urlManager->get_url_asset_allied() . "/" . $asset->idAllied . "/images/" . $asset->idAsset . "." . $ext;
      return $img;
    } else {
//      $idAccount = $
      $ext = pathinfo($asset->name, PATHINFO_EXTENSION);
      $img = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . "/" . $this->urlManager->get_url_asset_root() . "/" . "/images/" . $asset->idAsset . "." . $ext;
//			$this->logger->log("Link final: {$img}");
      return $img;
    }
  }

  /**
   * @Post("/duplicatesurvey/{idSurvey:[0-9]+}")
   */
  public function duplicatesurveyAction() {
    try {

      $json = $this->request->getRawBody();
      $data = json_decode($json);

      $wrapper = new \Sigmamovil\Wrapper\SurveyWrapper();
      $this->db->begin();
      $wrapper->duplicateSurvey($data);
      $this->db->commit();
      $idSurveyDuplicate = $wrapper->setSurveyDuplicate();
      if (isset($idSurveyDuplicate) && !empty($idSurveyDuplicate)) {
        $this->notification->success("se ha duplicado correctamente la encuesta");
        return $this->set_json_response(array('IdSurveyDuplicate' => $idSurveyDuplicate));
      } else {
        $this->notification->error("No se ha duplicado correctamente la encuesta");
        return $this->set_json_response(array('IdSurveyDuplicate' => $idSurveyDuplicate));
      }
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
    /**
   * @Route("/deletesurvey/{idSurvey:[0-9]+}", methods="DELETE")
   */
  public function deletesurveyAction($idSurvey) {
    try {
      $survey = Survey::findFirst(array(
                  'conditions' => 'idSurvey = ?0',
                  'bind' => [$idSurvey]
      ));
      if (!$survey) {
        throw new InvalidArgumentException('No se encontró la encuesta solicitada, por favor valide la información');
      }
      $survey->deleted = time();
      if (!$survey->update()) {
        foreach ($survey->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      return $this->set_json_response(array("message" => "Se ha eliminado la encuesta {$survey->name} exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
