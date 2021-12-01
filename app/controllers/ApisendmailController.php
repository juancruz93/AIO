<?php

//use Sigmamovil\bgprocesses\sender\TrackingUrlObject;

/**
 * @RoutePrefix("/api/sendmail")
 */
class ApisendmailController extends ControllerBase {

  /**
   *
   * @Get("/getcontactlist")
   */
  public function getcontactlistAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->getAllContanctList();
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getsegment")
   */
  public function getsegmentAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->getAllSegment();
      return $this->set_json_response($wrapper->getSegment(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/countcontact")
   */
  public function countcontactAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setIdMail($data->idMail);
//      $wrapper->getAllTags();
//      var_dump($wrapper->getAllTags());
      return $this->set_json_response($wrapper->getCountContacts($data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addaddressees")
   */
  public function addaddresseesAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      return $this->set_json_response($wrapper->getAddressees($data), 200);
    } catch (\Sigmamovil\General\Exceptions\ValidateTargetException $e) {
      return $this->set_json_response(array('message' => $e->getMessage(), "type" => 402), 402);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception add addressees... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findmail/{page:[0-9]+}")
   */
  public function findmailAction($idMail) {
    try {
      $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;

      $mail = Mail::findFirst(array(
                  "conditions" => "idMail = ?0 AND idSubaccount = ?1",
                  "bind" => array($idMail, $idSubaccount)
      ));
      if (!$mail) {
        throw new InvalidArgumentException("No se ha encontrado el envío de correo con id: {$idMail}");
//              return $this->set_json_response(array('message' => "No se ha encontrado el envío de correo con id: {$idMail}"), 404);
      }

      $status = array("scheduled", "draft");
      if (!in_array($mail->status, $status)) {
        throw new \InvalidArgumentException("El envío no se puede editar");
      }



      return $this->set_json_response($mail->getCleanMail(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findmailmessagessent/{page:[0-9]+}")
   */
  public function findmailmessagessentAction($idMail) {
    try {
      $sql = "SELECT messagesSent FROM mail WHERE idMail = {$idMail}";
      $mail = $this->db->fetchAll($sql);
      $mail = json_encode($mail[0]);
      return $this->set_json_response($mail, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findprocessedcontact/{idImportcontactfile:[0-9]+}")
   */
  public function findprocessedcontactAction($idImportcontactfile) {
    try {
      $sql = "SELECT processed FROM importcontactfile WHERE idImportcontactfile = {$idImportcontactfile}";
      $importcontactfile = $this->db->fetchAll($sql);
      $importcontactfile = json_encode($importcontactfile[0]);
      return $this->set_json_response($importcontactfile, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding importcontact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findemailsender/{idEmailsender:[0-9]+}")
   */
  public function findemailsenderAction($idEmailsender) {
    try {
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
      $sql = "SELECT * fROM emailsender WHERE idEmailsender = {$idEmailsender} AND idAccount = {$idAccount}";
      $emailsender = $this->db->fetchAll($sql);
      $emailsender = json_encode($emailsender[0]);
      return $this->set_json_response($emailsender, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findmailattachment/{idEmail:[0-9]+}")
   */
  public function findmailattachmentAction($idEmail) {
    try {
      $sql = "SELECT asset.idAsset, asset.name,asset.size FROM mail_attachment, asset WHERE mail_attachment.idMail = {$idEmail} AND mail_attachment.idAsset = asset.idAsset";
      $emailattachment = $this->db->fetchAll($sql);
      $emailattachment = json_encode($emailattachment);
      return $this->set_json_response($emailattachment, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findemailname/{idEmailname:[0-9]+}")
   */
  public function findemailnameAction($idEmailname) {
    try {
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

      $sql = "SELECT * FROM name_sender WHERE idNameSender = {$idEmailname} AND idAccount = {$idAccount}";
      $emailname = $this->db->fetchAll($sql);
      $emailname = json_encode($emailname[0]);
      return $this->set_json_response($emailname, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  /**
   *
   * @Get("/findreplyto/{idReplyto:[0-9]+}")
   */
  public function findreplytoAction($idReplyto) {
    try {
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

      $sql = "SELECT * FROM reply_tos WHERE idReplyTo = {$idReplyto} AND idAccount = {$idAccount}";
      $emailreplyto = $this->db->fetchAll($sql);
      $emailreplyto = json_encode($emailreplyto[0]);

      return $this->set_json_response($emailreplyto, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findmailcategory/{idMailcategory:[0-9]+}")
   */
  public function findmailcategoryAction($idMailcategory) {
    try {
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

      $sql = "SELECT mail_category.idMailCategory, mail_category.name FROM mxmc, mail_category WHERE mxmc.idMail = {$idMailcategory} AND mxmc.idMailCategory = mail_category.idMailCategory;";
      /* $mxmc = \Phalcon\DI::getDefault()->get('db')->fetchall($sql);

        $sql = "SELECT idMailCategory, name FROM mail_category WHERE idMailCategory = {$idMailcategory} AND idAccount = {$idAccount}"; */
      $emailname = $this->db->fetchAll($sql);
      $emailname = json_encode($emailname);
      return $this->set_json_response($emailname, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getcontentmail/{page:[0-9]+}")
   */
  public function getcontentmailAction($idMail) {
    try {
      $content = MailContent::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
      if ($content->typecontent == "Editor") {
        $content->url = "mail/contenteditor";
        $editor = new Sigmamovil\Logic\Editor\HtmlObj();
        $editor->setAccount($this->user->UserType->Subaccount->Account);
        $editor->assignContent(json_decode($content->content));
        $html = $editor->render();
      } else if ($content->typecontent == "html") {

        $content->url = "mail/htmlcontent";
        $footerObj = new Sigmamovil\General\Misc\FooterObj();
        $footerObj->setAccount($this->user->UserType->Subaccount->Account);
        $html = $footerObj->addFooterInHtml(html_entity_decode($content->content));
      } else if ($content->typecontent == "url") {
        $content->url = "mail/htmlcontent";
      }

      $datas = new stdClass();
      $googleAnalytics = Mailgoogleanalytics::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($idMail)
      ));
      if ($content) {
        $urlObj = new Sigmamovil\General\Misc\TrackingUrlObject();
        $links = $urlObj->getLinksFromContentMail($html, $content->plaintext);
        $content->links = array("links" => $links);
        if ($googleAnalytics) {
          $datas->googleAnalyticsData = array(
              "campaignName" => $googleAnalytics->name,
              "links" => json_decode($googleAnalytics->links)
          );
        }
      }
      $mail = Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
      $datas->content = $content;
      $datas->notificationEmails = $mail->notificationEmails;
      $datas->googleAnalytics = $mail->googleAnalytics;

      $msn = MailStatisticNotification::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
      if ($msn) {
        $datas->statisticsEmails = $msn->target;
        $datas->quantity = $msn->quantity;
        $datas->typeTime = $msn->typeTime;
      }
      if ($mail->postFacebook) {
        $post = Post::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
        if ($post) {
          if ($post->type == 'facebook') {
            $datas->facebook->idPage = $post->idPage;
            $datas->facebook->description = $post->description;
          }
        }
      }

////      $content->notificationEmails=$mail->notificationEmails;
//      $content->notificationEmails=$mail->notificationEmails;
      $cont = json_encode($datas);

      return $this->set_json_response($cont, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/getallmail/{page:[0-9]+}")
   */
  public function getallmailAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->findAllMail($page, $data);
      return $this->set_json_response($wrapper->getMail(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getmailcategory")
   */
  public function getmailcategoryAction() {
    try {

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $wrapper->getAllMailCategory();
      return $this->set_json_response($wrapper->getMailcategory(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getmailcategoryidmail/{idMail:[0-9]+}")
   */
  public function getmailcategorybyidmailAction($idMail) {
    try {

      $wrapper = new \Sigmamovil\Wrapper\MailcategoryWrapper();
      $result = $wrapper->getMailCategoryByidMail($idMail);
      return $this->set_json_response($result, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Delete("/deletemail/{idMail:[0-9]+}")
   */
  public function deletemailAction($idMail) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->deleteMail($idMail);
      $this->trace("success", "La eliminado el mail");

      return $this->set_json_response(["message" => "Se ha eliminado el mail"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Put("/updateplainttext")
   */
  public function updateplainttextAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->updatePlaintText($data);
      $this->trace("success", "La eliminado el mail");

      return $this->set_json_response(["message" => "Se ha eliminado el mail"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Put("/saveadvanceoptions/{idMail:[0-9]+}")
   */
  public function saveadvanceoptionsAction($idMail) {

    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->saveAdvanceOptions($idMail, $data);
      $this->trace("success", "Se ha editado el mail");

      return $this->set_json_response(["message" => "Se ha editado el mail"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while saving advanced options... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addgoogleanalitics")
   */
  public function insertgoogleanaliticsAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response($wrapper->addgoogleanalytics($data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addadjunt")
   */
  public function addadjuntAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      \Phalcon\DI::getDefault()->get('db')->begin();
      $mailAttachment = Mailattachment::find(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
      if (!$mailAttachment->delete()) {
        \Phalcon\DI::getDefault()->get('db')->rollback();
        foreach ($mailAttachment->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      $mail = Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
      $mail->attachment = 1;

      if (!$mail->save()) {
        \Phalcon\DI::getDefault()->get('db')->rollback();
        foreach ($mail->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      foreach ($data->file as $key) {

        $attachment = new Mailattachment();
        $attachment->idMail = $data->idMail;
        $attachment->idAsset = $key->idAsset;
        $attachment->createdon = time();
        if (!$attachment->save()) {
          \Phalcon\DI::getDefault()->get('db')->rollback();
          foreach ($attachment->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
      }
      \Phalcon\DI::getDefault()->get('db')->commit();
      return $this->set_json_response(["message" => "(y)"], 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/deleteAsset")
   */
  public function deleteAssetAction() {
    try{

      $contentsraw = $this->getRequestContent();
      $idAsset = json_decode($contentsraw);

      $asset = Asset::findFirst(array(
        'conditions' => 'idAsset = ?1',
        'bind' => array(1 => $idAsset)
      ));
      if (!$asset->delete()) {
        foreach ($asset->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getallattachment/{idMail:[0-9]+}")
   */
  public function getallattachmentAction($idMail) {
    try {

      $mailAttachment = $this->modelsManager->createBuilder()
              ->columns(["Asset.name", "Mailattachment.idMailattachment", "Asset.idAsset", "Asset.size"])
              ->FROM('Mailattachment')
              ->leftjoin('Asset', 'Mailattachment.idAsset = Asset.idAsset')
              ->WHERE("Mailattachment.idMail = {$idMail}")
              ->getQuery()
              ->execute();

      $arr = array();
      foreach ($mailAttachment as $value) {
        array_push($arr, $value);
      }
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
   *
   * @Post("/sendconfirmationmail")
   */
  public function sendconfirmationmailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response($wrapper->ConfirmationMail($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getassetmediagallery/{page:[0-9]+}")
   */
  public function getassetmediagalleryAction($page) {
    try {
      $arr = array();
      if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount) || isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Account->idAccount)) {
        $account = ((isset($this->user->Usertype->Subaccount->Account)) ? $this->user->Usertype->Subaccount->Account : $this->user->Usertype->Account);
        $assets = \Sigmamovil\General\Misc\AssetObj::findAllAssetsInAccountPagination($account, $page);
        if ($assets['total'] > 0) {
          foreach ($assets['items'] as $a) {
            $arr['items'][] = array('thumb' => $a->getThumbnailUrl(),
                'image' => $a->getImagePrivateUrl(),
                'title' => $a->getFileName(),
                'id' => $a->getIdAsset());
          }
        }

        $arr['total'] = $assets['total'];
        $arr['total_pages'] = $assets['total_pages'];
      } else {
        $assets = \Sigmamovil\General\Misc\AssetObj::findAllAssetsInAlliedPagination($this->user->Usertype->Allied, $page);
        if ($assets['total'] > 0) {
          foreach ($assets['items'] as $a) {
            $arr['items'][] = array('thumb' => $a->getThumbnailUrlAllied(),
                'image' => $a->getImagePrivateUrl(),
                'title' => $a->getFileName(),
                'id' => $a->getIdAsset());
          }
        }
        $arr['total'] = $assets['total'];
        $arr['total_pages'] = $assets['total_pages'];
      }
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
   *
   * @Get("/getmail/{page:[0-9]+}")
   */
  public function getmailAction($idMail) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $arr = array("mail" => $wrapper->getMailExits($idMail));
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
   *
   * @Post("/sendscheduledateemail")
   */
  public function sendscheduledateemailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response($wrapper->ScheduledateMail($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/testmail")
   */
  public function sendtestmailAction() {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();

      return $this->set_json_response($wrapper->sendtestmail($data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sendtestmail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * 
   * @Get("/deleteattached/{page:[0-9]+}")
   */
  public function deleteattachedAction($idMailattachment) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->setIdMailattachment($idMailattachment);
      return $this->set_json_response($wrapper->deletedMailattachment(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sendtestmail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * @Get("/getthumbnail/{idMail:[0-9]+}")
   */
  public function getthumbnailmailAction($idMail) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response(array("thumb" => $wrapper->getThumbnailMail($idMail)));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while findThumbnailMail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * @Get("/getcustomfield/{idCustomfield:[0-9]+}")
   */
  public function getcustomfieldAction($idCustomfield) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      return $this->set_json_response($wrapper->getCustomfield($idCustomfield));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while findThumbnailMail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * @Get("/getmailfilters")
   */
  public function getmailfiltersAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->findMailsFilters();
      return $this->set_json_response($wrapper->getMail());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while findThumbnailMail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * 
   * @Post("/changetest/{idMail:[0-9]+}")
   */
  public function changetestmailAction($idMail) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();

      return $this->set_json_response($wrapper->changeTestMail($idMail, $data));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sendtestmail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * 
   * @Post("/sendtester/{idMail:[0-9]+}")
   */
  public function sendtestermailAction($idMail) {
    try {
      $json = $this->request->getRawBody();
      $data = json_decode($json);
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();

      return $this->set_json_response($wrapper->sendtestermail($data, $idMail));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sendtestmail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * 
   * @Get("/getlinksbymail/{idMail:[0-9]+}")
   */
  public function getlinksbymailAction($idMail) {
    try {

      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $wrapper->setIdMail($idMail);
      return $this->set_json_response($wrapper->getLinksByMail());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sendtestmail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }

  /**
   * 
   * @Get("/downloadmailpreview/{idMail:[0-9]+}")
   */
  public function downloadmailpreviewAction($idMail) {
    try {
      $this->view->disable();
      $wrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $nameMailSubject = \Mail::findFirst(array(
                'conditions' => 'idMail = ?0',
                'bind' => array(0 => $idMail)
    ));

      $ruta = $wrapper->downloadmailprev($idMail);

      //Reemplaza caracteres especiales en el Asunto
      $sanitize = new \Sigmamovil\General\Misc\SanitizeString($nameMailSubject->subject);
      $sanitize->strTrim();
      $sanitize->sanitizeAccents();
      $sanitize->sanitizeSpecials();
      $sanitize->sanitizeSpecialsSms();
      $sanitize->nonPrintable();
      $nameMailSubjectSanitized = $sanitize->getString();

      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename=' . $nameMailSubjectSanitized . $idMail.".pdf");
      header('Pragma: public');
      header('Expires: 0');
      header('Content-Type: application/download');

      if (file_exists($ruta)) {
        $valRead = readfile($ruta);
        if ($valRead) {
          $valUnlink = unlink($ruta);
          return $valUnlink;
        }
      } else {
        echo "Ha ocurrido un problema con el servidor por favor vuelva a intentarlo o comuniquese con el administrador del sistema";
      }
      //exit;
      //return $this->set_json_response(array("thumb" => $wrapper->downloadmailprev($idMail)));
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array("message" => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while findThumbnailMail... {$ex}");
      return $this->set_json_response(array("message" => "Ha ocurrido un error, contacte con el administrador"), 500, "Ha ocurrido un error");
    }
  }
      
  /**
   *
   * @Post("/only")
   */
  public function onlyAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      return $this->set_json_response($wrapper->getOnly($data), 200);
    } catch (\Sigmamovil\General\Exceptions\ValidateTargetException $e) {
      return $this->set_json_response(array('message' => $e->getMessage(), "type" => 402), 402);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception add addressees... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   *
   * @Get("/getallattachmentpdf/{idMail:[0-9]+}")
   */
  public function getallattachmentpdfAction($idMail) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      return $this->set_json_response($wrapper->getAttachmentPdf($idMail), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding Apisendmail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
