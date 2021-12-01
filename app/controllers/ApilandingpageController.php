<?php

use Sigmamovil\Wrapper\LandingpageWrapper as lpw;

/**
 * @RoutePrefix("/api/landingpage")
 */
class ApilandingpageController extends ControllerBase {

  /**
   * @Post("/listlanding/{page:[0-9]+}")
   */
  public function listlandingAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson);

      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();

      return $this->set_json_response($wrapper->listLanding($page, $arraydata));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getlandingcategory")
   */
  public function getlandingcategoryAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      return $this->set_json_response($wrapper->findlandingcategory(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing category... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/createlandingcategory")
   */
  public function createlandingcategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $this->trace("success", "Se ha guardado la categoría correctamente");

      return $this->set_json_response($wrapper->createLandingCategory($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create surveycategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/createlandingpage")
   */
  public function createlandingpageAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson, true);
      $this->db->begin();
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $landing = $wrapper->saveLanding($arraydata);
      $this->db->commit();
      $this->trace("success", "Se ha guardado la landing page correctamente");

      return $this->set_json_response(['message' => 'La landing page ha sido creada con exito', 'landing' => $landing]);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding landing page ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/findlanding/{idLandingpage:[0-9]+}")
   */
  public function findlandingAction($idLandingpage) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      return $this->set_json_response($wrapper->findLanding($idLandingpage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/findlandingcsc/{idLandingpage:[0-9]+}")
   */
  public function findlandingcscAction($idLandingpage) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      return $this->set_json_response($wrapper->findlandingCSC($idLandingpage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/editlandingpage/{idLanding:[0-9]+}")
   */
  public function editlandingpageAction($idLanding) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $landing = $wrapper->editLanding($arraydata, $idLanding);
      $this->trace("success", "Se ha editado la landing page correctamente");

      return $this->set_json_response(['message' => 'La landing page se edito con exito', 'landing' => $landing]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/createpublicview/{idLanding:[0-9]+}")
   */
  public function createpublicviewAction($idLanding) {
    try {
      $dataJson = $this->request->getRawBody();
      $arraydata = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $landing = $wrapper->editPublicView($arraydata, $idLanding);
      $this->trace("success", "Se ha guardado correctamente");

      return $this->set_json_response(['message' => 'Se ha guardo correctamente', 'landing' => $landing]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/findlandingcountview/{idLandingpage:[0-9]+}")
   */
  public function findlandingcountviewAction($idLandingpage) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      return $this->set_json_response($wrapper->findLandingCountView($idLandingpage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/deletelandingpage/{idLandingpage:[0-9]+}")
   */
  public function deletelandingpageAction($idLandingpage) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $landing = $wrapper->deletelandingpage($idLandingpage);
      return $this->set_json_response(['message' => 'Se ha eliminado correctamente', 'landing' => $landing]);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcontent/{idLandingPage:[0-9]+}")
   */
  public function getcontentlandingpageAction($idLandingPage) {
    try {
      $wrapper = new lpw();
      return $this->set_json_response($wrapper->getContentOfLandingPage($idLandingPage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras obtiene contenido de LandingPage ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savecontent/{idLandingPage:[0-9]+}")
   */
  public function savecontentlandingpageAction($idLandingPage) {
    try {
      $wrapper = new lpw();
      $data = $this->request->getPost();
      if (!isset($data)) {
        throw new InvalidArgumentException("El contenido no debe estar vacío");
      }
      return $this->set_json_response($wrapper->saveContentLandingPage($idLandingPage, $data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage(), "responseCode" => 0), 400);
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras se guarda contenido de LandingPage ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador', "responseCode" => 0), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/uploadimage")
   */
  public function uploadimagetolandingpageAction() {
    try {
      if (empty($_FILES)) {
        throw new InvalidArgumentException("No ha cargado ningún archivo, por favor intente de nuevo");
      }
      $dataFile = $_FILES['imageFileField'];
      $space = $this->getSpaceUsedInAccount();
      $wrapper = new lpw();
      $wrapper->setSpace($space);
      $wrapper->setIdAccount(((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL)));
      return $this->set_json_response($wrapper->uploadFileLandingPage($dataFile));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage(), "responseCode" => 0), 400);
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras se guarda contenido de LandingPage ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador', "responseCode" => 0), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/linkge/{idlandingpage:[0-9]+}")
   */
  public function linkgeneratorAction($idLandingPage) {
    try {
      $wrapper = new lpw();
      return $this->set_json_response($wrapper->linkGenerator($idLandingPage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras se obtiene el link para compartir ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador', "responseCode" => 0), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/sendmaillandingpage")
   */
  public function sendmaillandingpageAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      $message = $wrapper->sendMail($data);
      $this->trace("success", "Se ha creado el correo de la Landing Page correctamente");
      return $this->set_json_response($message);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save survey... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/hascontent/{idlp:[0-9]+}")
   */
  public function hascontentlandingpageAction($idlp) {
    try {
      $wrapper = new lpw();
      return $this->set_json_response($wrapper->hasContentLandingPage($idlp));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while verify has content landingpage... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/duplicate/{idlp:[0-9]+}")
   */
   public function duplicatelandingpageAction($idLandingPage) {
    try {
      $this->db->begin();
      $wrapper = new lpw();
      $response = $wrapper->duplicateLandingPage($idLandingPage);
      $this->db->commit();
      return $this->set_json_response($response);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while duplicate landingpage... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/changestatus/{idLanding:[0-9]+}")
   */
  public function changestatusAction($idLanding) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      if (!$wrapper->changeStatus($data, $idLanding)) {
        throw new InvalidArgumentException("Ocurrio un error cambiando el estado de la landing, por favor cotacte al administrador.");
      }
      $this->trace("success", "Se ha actualizado el estado de la encuesta correctamente.");
      return $this->set_json_response(array('message' => "se ha actualizado el estado de la landing correctamente."));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save landing... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/changetype/{idLanding:[0-9]+}")
   */
  public function changetypeAction($idLanding) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      if (empty($data)) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\LandingpageWrapper();
      if (!$wrapper->changeType($data, $idLanding)) {
        throw new InvalidArgumentException("Ocurrio un error cambiando el tipo de la landing, por favor cotacte al administrador.");
      }
      $this->trace("success", "Se ha actualizado el tipo de la encuesta correctamente.");
      return $this->set_json_response(array('message' => "se ha actualizado el tipo de la landing correctamente."));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save landing... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * @Get("/linkfb/{idlandingpage:[0-9]+}")
   */
  public function linkfbAction($idLandingPage) {
    try {
      $wrapper = new lpw();
      return $this->set_json_response($wrapper->linkfb($idLandingPage));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras se obtiene el link para compartir ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador', "responseCode" => 0), 500, 'Ha ocurrido un error');
    }
  }

}
