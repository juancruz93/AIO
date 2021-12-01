<?php

use Sigmamovil\Wrapper\WpptemplateWrapper;

/**
 * @RoutePrefix("/api/wpptemplate")
 */
class ApiwpptemplateController extends ControllerBase {

    /**
   * @Post("/listwpptemplate/{page:[0-9]+}")
   */
  public function listwpptemplateAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new WpptemplateWrapper();
      return $this->set_json_response($wrapper->listWppTemplate($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/listwpptempcategory/")
   */
  public function listwpptempcategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new WpptemplateWrapper();
      return $this->set_json_response($wrapper->listWppTemplateCat($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savewpptemplate")
   */
  public function savewpptemplateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new WpptemplateWrapper();
      return $this->set_json_response($wrapper->saveWppTemplate($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/editwpptemplate")
   */
  public function editwpptemplateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new WpptemplateWrapper();
      return $this->set_json_response($wrapper->editWppTemplate($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save smstemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/deletewpptemplate/{idWppTemplate:[0-9]+}", methods="DELETE")
   */
  public function deletewpptemplateAction($idWppTemplate) {
    try {
        $wrapper = new WpptemplateWrapper();
        return $this->set_json_response($wrapper->deleteWpptemplate($idWppTemplate));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}