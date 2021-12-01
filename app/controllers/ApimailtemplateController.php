<?php

use Sigmamovil\Wrapper\MailtemplateWrapper;

/**
 * @RoutePrefix("/api/mailtemplate")
 */
class ApimailtemplateController extends ControllerBase {

  /**
   * 
   * @Post("/listmailtemp/{page:[0-9]+}")
   */
  public function listmailtemplateAction($page) {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->listMailTemplate($page, $arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemailtemp")
   */
  public function savemailtemplateAction() {
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);
      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new MailtemplateWrapper();
      $idMailTemplate = $wrapper->saveMailTemplate($arrayData);
      $this->db->commit();
      $data = array(
          "message" => "Se ha guardado exitosamente la plantilla {$arrayData->nameMailTemplate}",
          "idMailTemplate" => $idMailTemplate
      );
      $this->trace("success", "Se ha guardado exitosamente la plantilla {$arrayData->nameMailTemplate} de mail");
      $wrapper->createThumbnailTemplate($idMailTemplate);
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while save mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getmailtemp/{id:[0-9]+}")
   */
  public function getmailtemplateAction($idMailTemplate) {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getMailtemplate($idMailTemplate));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while find one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/editmailtemp/{id:[0-9]+}")
   */
  public function editmailtemplateAction($idMailTemplate) {
    try {
      $this->db->begin();
      if (!isset($idMailTemplate)) {
        throw new InvalidArgumentException("Id de plantilla inválido");
      }
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new MailtemplateWrapper();
      $data = array();
      $idMailTemp = $wrapper->editMailTemplate($idMailTemplate, $arrayData);
      if ($idMailTemp) {
        $data = array(
            "message" => "Se ha guardado exitosamente la plantilla {$arrayData->nameMailTemplate}",
            "idMailTemplate" => $idMailTemp
        );
      }
//      $this->notification->success($data['message']);
      $this->trace("success", "Se ha editado exitosamente la plantilla {$arrayData->nameMailTemplate} de mail");
      $wrapper->createThumbnailTemplate($idMailTemp);
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while edit one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/preview/{id:[0-9]+}")
   */
  public function previewmailtemplateAction($id) {
    $mailtemplatecontent = MailTemplateContent::findFirst(array(
                "conditions" => "idMailTemplateContent = ?0",
                "bind" => array($id)
    ));

    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount(((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null)));
    $editorObj->assignContent(json_decode($mailtemplatecontent->content));
    $html = $editorObj->render();

    return $this->set_json_response(array('template' => $html));
  }

  /**
   * 
   * @Post("/deletemailtemp")
   */
  public function deletemailtemplateAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new MailtemplateWrapper();
      $data = array();
      if ($wrapper->deleteMailTemplate($arrayData)) {
        $data = array(
            "message" => "Se ha eliminado exitosamente la plantilla"
        );
      }
      $this->trace("success", "Se ha eliminado exitosamente la plantilla de mail");

      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/listmailtempbyacco/{id:[0-9]+}/{page:[0-9]+}")
   */
  public function listmailtemplatebyaccountAction($id, $page) {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->listMailTemplateByaccount($id, $page));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * flowchart - Library
   * @Get("/gettemplateautocomplete");
   */
  public function getmailtemplateautocompleteAction() {
    try {
      $filter = $_GET["q"];
      if (empty($filter)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getmailtemplateautocomplete($filter));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * flowchart - Library
   * @Get("/getlinkstemplate/{idtemplate:[0-9]+}");
   */
  public function getlinkstemplateAction($idTemplate) {
    try {

      if (empty($idTemplate)) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }
      $wrapper = new MailtemplateWrapper();
      $contentTemplate = $wrapper->getcontenttemplate($idTemplate);
      if (!$contentTemplate) {
        throw new InvalidArgumentException("El template no se encuentra registrado.");
      }
      $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
      if (empty($account)) {
        throw new InvalidArgumentException("El account no se encuentra registrado.");
      }

      //$domain = Urldomain::findFirst(array("conditions" => "idUrldomain = ?0", "bind" => array($account->accountclassification->idUrldomain)));

      $editor = new Sigmamovil\Logic\Editor\HtmlObj();
      $editor->setAccount($account);
      $editor->assignContent(json_decode($contentTemplate->content));
      $html = $editor->render();

      $TemplatemailObject = new Sigmamovil\General\Misc\TemplatemailObject($contentTemplate);
      $marks = $TemplatemailObject->getLinksTemplate($html);
      /*$marks = [
        "https://www.facebook.com/", "https://www.twitter.com/", "https://www.linkedin.com/"
      ];*/
      $links = array();
      foreach ($marks as $url) {
        $maillink = Maillink::findFirst(array(
          'colums' => 'idMail_link, name',
          'conditions' => 'link = ?0',
          'bind' => array($url)
        ));
        if ($maillink != false) {
          $links[] = [
            'id'    => $maillink->idMail_link, 
            'name'  => $maillink->link
          ];
        }
        unset($maillink);
      }
      return $this->set_json_response(array("links" => $links));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getallmailtemplatesurvey")
   */
  public function getallmailtemplatesurveyAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getallmailtemplatesurvey());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallmailtemplatesurveybyfilter")
   */
  public function getallmailtemplatesurveybyfilterAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $wrapper = new MailtemplateWrapper();
      if (isset($dataJson)) {
        $wrapper->setSearch($dataJson);
      }
      $wrapper->getAllMailTemplateByFilterSurvey();
      return $this->set_json_response($wrapper->getMailtemplates());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getallmailtemplate")
   */
  public function getallmailtemplateAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getallmailtemplate());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallmailtemplatebyfilter")
   */
  public function getallmailtemplatebyfilterAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $wrapper = new MailtemplateWrapper();
      if (isset($dataJson)) {
        $wrapper->setSearch($dataJson);
      }
      $wrapper->getAllMailTemplateByFilter();
      return $this->set_json_response($wrapper->getMailtemplates());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/accounts")
   */
  public function getaccountsforaliedAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getAccountsForAllied());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getmailtemplatecategory")
   */
  public function getmailtemplatecategoryAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      $wrapper->findMailTemplateCategories();
      return $this->set_json_response($wrapper->getMailTemplateCategories());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/gettemplatemail")
   */
  public function gettemplatemailAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getallmailtemplate());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/saveastemplatemailnew")
   */
  public function saveastemplatemailnewAction() {
    try {
      $this->db->begin();
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new MailtemplateWrapper();
      $idMailTemplate = $wrapper->saveAsMailtemplateNew($arrayData);


      $this->db->commit();
      $data = array(
          "message" => "Se ha guardado exitosamente la plantilla {$arrayData->nameMailTemplate}",
          "idMailTemplate" => $idMailTemplate
      );
      $this->trace("success", "Se ha guardado exitosamente la plantilla {$arrayData->nameMailTemplate} de mail");
      $wrapper->createThumbnailTemplate($idMailTemplate);
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while save mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getalltemplatemail")
   */
  public function getalltemplatemailAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      $wrapper = new MailtemplateWrapper();

      return $this->set_json_response($wrapper->getFortotalAllmailTemplate($arrayData));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $exc) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getallmailtemplatelandingpage")
   */
  public function getallmailtemplatelandingpageAction() {
    try {
      $wrapper = new MailtemplateWrapper();
      return $this->set_json_response($wrapper->getallmailtemplatelandingpage());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallmailtemplatelandingpagebyfilter")
   */
  public function getallmailtemplatelandingpagebyfilterAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $wrapper = new MailtemplateWrapper();
      if (isset($dataJson)) {
        $wrapper->setSearch($dataJson);
      }
      $wrapper->getAllMailTemplateByFilterLandingpage();
      return $this->set_json_response($wrapper->getMailtemplates());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while delete one mailtemplate ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
