<?php

/**
 * Description of ApilandingpagetemplateController
 *
 * @author juan.pinzon
 */
use Sigmamovil\Wrapper\LandingpagetemplateWrapper as lptw;

/**
 * @RoutePrefix("/api/lptemplate")
 */
class ApilandingpagetemplateController extends ControllerBase {

  /**
   * @Post("/getall/{page:[0-9]+}")
   */
  public function getalltemplatesAction($page) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);

      $wrapper = new lptw();

      return $this->set_json_response($wrapper->getAllLandingPageTemplate($page, $data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding landing page template ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create/{idlp}")
   */
  public function createlandingpagetemplateAction($idlp) {
    try {
      $json = $this->getRequestContent();
      $data = json_decode($json, TRUE);

      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new lptw();

      return $this->set_json_response($wrapper->createBasicInfoLPT($idlp, $data));
    } catch (Exception $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create landing page template ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getlpt/{idlpt:[0-9]+}")
   */
  public function getlandingpagetemplateAction($idlpt) {
    try {
      $wrapper = new lptw();
      return $this->set_json_response($wrapper->getLandinPageTemplate($idlpt));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getLandingPage landing page template ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getcontent/{idlpt:[0-9]+}")
   */
  public function getcontentlandingpagetemplateAction($idlpt) {
    try {
      $wrapper = new lptw();
      return $this->set_json_response($wrapper->getContentLandingPageTemplate($idlpt));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getcontent landing page template ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savecontent/{idlpt:[0-9]+}")
   */
  public function savecontentlandingpagetemplateAction($idlpt) {
    try {
      $data = $this->request->getPost();

      if (!isset($data)) {
        throw new InvalidArgumentException("El contenido no debe estar vacío");
      }

      $wrapper = new lptw();
      return $this->set_json_response($wrapper->saveContentLandingPageTemplate($idlpt, $data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create landing page template ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/uploadimage")
   */
  public function uploadimagetolandingpageAction() {
    try {
      if (!$this->request->hasFiles()) {
        throw new InvalidArgumentException("No ha cargado ningún archivo, por favor intente de nuevo");
      }

      $dataFile = $_FILES['imageFileField'];
      $space = $this->getSpaceUsedInAccount();
      $wrapper = new lptw();
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
   * @Get("/select/{idlp:[0-9]+}/{idlpt:[0-9]+}")
   */
  public function selectlandingpageAction($idLandingPage, $idLpTemplate) {
    try {
      $wrapper = new lptw();
      if ($wrapper->selectLandingPageTemplate($idLandingPage, $idLpTemplate)) {
        return $this->response->redirect("landingpage/pagebuilder/{$idLandingPage}");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("landingpage#/");
    } catch (Exception $ex) {
      $this->logger->log("Excepción mientras se guarda contenido de LandingPage ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("landingpage#/");
    }
  }

}
