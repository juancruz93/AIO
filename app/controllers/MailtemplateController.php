<?php

class MailtemplateController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Plantillas de correo");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "mailtemplate");
  }

  public function listAction() {
    
  }

  public function createAction() {
    
  }

  public function editAction($idMailTemplate) {
    try {
      if (!isset($idMailTemplate)) {
        throw new InvalidArgumentException("Dato de plantilla inválido");
      }

      $mailtemplate = \MailTemplate::findFirst(array(
                  "conditions" => "idMailTemplate = ?0",
                  "bind" => array($idMailTemplate)
      ));

      if (!$mailtemplate) {
        throw new InvalidArgumentException("La plantilla que intenta editar no existe");
      }

      $mailtemplatecontent = MailTemplateContent::findFirst(array(
                  "conditions" => "idMailTemplate = ?0",
                  "bind" => array($mailtemplate->idMailTemplate)
      ));

      if (!$mailtemplatecontent) {
        throw new InvalidArgumentException("La plantilla que intenta editar no tiene contenido");
      }
      $this->view->setVar("mailtemplatecontent", $mailtemplatecontent);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log($ex->getMessage());
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("mailtemplate#/");
    } catch (Exception $ex) {
      $this->notification->log($ex->getMessage());
      return $this->response->redirect("mailtemplate#/");
    }
  }
  
  public function selectAction(){
    
  }

  public function selectautorespAction(){

  }
}
