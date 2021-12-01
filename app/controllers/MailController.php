<?php

require_once __DIR__ . "/../general/misc/forceutf8/src/ForceUTF8/Encoding.php";
require_once(__DIR__ . "/../bgprocesses/general/misc/PdfManager.php");

class MailController extends ControllerBase {
    
  public $idContactlist = [];
    
  public function initialize() {
    $this->tag->setTitle("Envíos de correos");
    parent::initialize();
  }

  public function indexAction() {
    $flag = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      echo $key->idServices;
      if ($key->idServices == $this->services->email_marketing && $key->status ==1) {
        $flag = true;
      }
    }
    if ($flag == false ) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    $this->view->setVar("app_name", "mail");
    
    //Traigo los Estados de las campañas de Mail
    $modelsManager = Phalcon\DI::getDefault()->get('modelsManager');
    $mailStatus = $modelsManager->createBuilder()
            ->columns("status")
            ->from("Mail")
            ->where("idSubaccount = {$this->user->Usertype->Subaccount->idSubaccount}")
            ->groupBy("status")
            ->getQuery()
            ->execute();
    foreach ($mailStatus as $key=>$value){
      $arrayMailStatus[] = $this->translateStatusMail($value["status"]);
      }
    $this->view->setVar("mailStatus", $arrayMailStatus);
    }

  private function getCustomfielForSegment($arrIds) {

    $arrid = explode(",", $arrIds);
    $whereids = array();
    foreach ($arrid as $key) {
      $whereids[] = $key;
    }
    $where = array("idSegment" => array('$in' => $whereids));
    $segment = \Segment::find($where);
    $arrIdContactList = array();

    foreach ($segment as $key => $val) {
      foreach ($val->contactlist as $key) {
        $arrIdContactList[] = (Int) $key['idContactlist'];
      }
    }

    $arrIdContactList = array_unique($arrIdContactList);

    return $this->getCustomfielForContaclist($arrIdContactList);
  }

  private function getCustomfielForContaclist($arrIds) {
    $arrid = implode(",", $arrIds);
    $phql = "SELECT name,alternativename FROM Customfield WHERE idContactlist IN ({$arrid}) Group By 1,2";
//    var_dump($phql);
//    exit();
    $modelsManager = Phalcon\DI::getDefault()->get('modelsManager');
    return $modelsManager->executeQuery($phql);
  }

  public function editor_frameAction($idMail = null) {

    if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount)) {

      $cfs = [];
      if ($idMail != null) {
        $mail = Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [$idMail]]);
        if ($mail) {
          if (isset($mail->target) && $mail->target != "") {
            $targetMail = json_decode($mail->target);
            if ($targetMail->type == "segment") {
              $arrSegment = $targetMail->segment;
              $countSegments = count($arrSegment);
              if ($countSegments > 0) {
                for ($i = 0; $i < $countSegments; $i++) {
                  $arrIds[] = $arrSegment[$i]->idSegment;
                }
              }
              $cfs = $this->getCustomfielForSegment($arrIds);
            } else {
              $arrContactList = $targetMail->contactlists;
              $countContactList = count($arrContactList);
              if ($countContactList > 0) {
                for ($i = 0; $i < $countContactList; $i++) {
                  $arrIds[] = $arrContactList[$i]->idContactlist;
                }
              }
              $cfs = $this->getCustomfielForContaclist($arrIds);
            }
          }
        }
      }
      if (count($cfs) <= 0) {
//        $cfs = Customfield::find(["conditions" => "idAccount = ?0", "bind" => [0 => \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount]]);
        $isAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
        $phql = "SELECT name,alternativename FROM Customfield WHERE idAccount = {$isAccount} Group By 1,2";
//    var_dump($phql);
//    exit();
        $modelsManager = Phalcon\DI::getDefault()->get('modelsManager');
        $cfs = $modelsManager->executeQuery($phql);
      }




      $arr = [];

      foreach ($cfs as $key) {
        $obj = new stdClass();
//        $obj->idCustomfield = $key->idCustomfield;
        $obj->name = $key->name;
        $obj->alternativename = strtoupper($key->alternativename);
        array_push($arr, $obj);
      }

      $this->view->setVar("cfs", $arr);
    }

    $arrayAssets = array();

    if (!$this->request->isPost()) {
      if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount) || isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Account->idAccount)) {
        $account = ((isset($this->user->Usertype->Subaccount->Account)) ? $this->user->Usertype->Subaccount->Account : $this->user->Usertype->Account);
        $assets = \Sigmamovil\General\Misc\AssetObj::findAllAssetsInAccountPagination($account);
        if ($assets['total'] > 0) {
          foreach ($assets['items'] as $a) {
            $arrayAssets[] = array('thumb' => $a->getThumbnailUrl(),
                'image' => $a->getImagePrivateUrl(),
                'title' => $a->getFileName(),
                'id' => $a->getIdAsset());
          }
        }

        $footer = Footer::findFirstByIdFooter($account->AccountConfig->idFooter);

        $content = json_decode($footer->content);
        $content = json_encode($content->dz->footer->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($footer->content));

        $objfooter = new stdClass();
        $objfooter->editor = $content;
        $objfooter->html = $editorObj->render();
        $objfooter->editable = $account->AccountConfig->footerEditable;

        $this->view->setVar('footer', $objfooter);
        $this->view->setVar('total', $assets['total']);
        $this->view->setVar('total_pages', $assets['total_pages']);
      } else {
        $assets = \Sigmamovil\General\Misc\AssetObj::findAllAssetsInAlliedPagination($this->user->Usertype->Allied);

        if ($assets['total'] > 0) {
          foreach ($assets['items'] as $a) {
            $arrayAssets[] = array('thumb' => $a->getThumbnailUrlAllied(),
                'image' => $a->getImagePrivateUrl(),
                'title' => $a->getFileName(),
                'id' => $a->getIdAsset());
          }
        }

        $objfooter = new stdClass();
        $objfooter->editor = "''";
        $objfooter->html = '';
        $objfooter->editable = 1;

        $this->view->setVar('footer', $objfooter);
        $this->view->setVar('total', $assets['total']);
        $this->view->setVar('total_pages', $assets['total_pages']);
      }
    }
    $this->view->setVar('assets', $arrayAssets);
  }

  public function createAction() {
    $msg = $this->session->get("msgContentEditor");
    if (isset($msg)) {
      $this->notification->success($msg);
      $this->session->remove("msgContentEditor");
    }
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->view->setVar('idfb', $di->get('configFb')->idApp);
  }

  public function basicinformationAction() {
    $mailForm = new MailForm();
    $this->view->setVar("mailForm", $mailForm);
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $this->db->begin();
      if (!empty($arrayData)) {

        if (!empty($arrayData["replyto"]) && !filter_var($arrayData["replyto"], FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException("Formato de correo invalido");
        }
        if (!isset($arrayData['category']) || empty($arrayData['category'])) {
          throw new \InvalidArgumentException("Debes seleccionar una categoría");
        }

        if (isset($arrayData['name'])) {
          $mail = \Mail::findFirst(["conditions" => "name = ?0 and idSubaccount = ?1", "bind" => array($arrayData['name'], $this->user->UserType->idSubaccount)]);
          if ($mail) {
            throw new \InvalidArgumentException("El nombre del correo ya se encuentra registrado.");
          }
        }

        $mail = new Mail();

        $mail->idSubaccount = $this->user->UserType->idSubaccount;
        $mail->idEmailsender = $arrayData["senderMailSelect"];
        $mail->idNameSender = $arrayData["senderNameSelect"];
        $mail->idReplyTo = $arrayData["replyToSelect"];
        $mail->type = "manual";
        $mail->notificationEmails = "";

        if (isset($arrayData['test']) && $arrayData['test']) {
          $mail->test = 1;
        } else {
          $mail->test = 0;
        }

        $mailForm->bind($arrayData, $mail);

        if (!$mailForm->isValid()) {
          foreach ($mailForm->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }


        if (!$mail->save()) {
          foreach ($mail->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }

        foreach ($arrayData['category'] as $category) {
          $mxmc = new Mxmc();
          $mxmc->idMail = $mail->idMail;
          $mxmc->idMailCategory = $category;

          if (!$mxmc->save()) {
            throw new InvalidArgumentException("Ocurrio un error");
          }
        }

        $this->db->commit();
        $resultArr = array("idMail" => $mail->idMail, "msg" => "Se ha guardado la información del envío de correo correctamente");

        return $this->set_json_response($resultArr, 200, "OK");
      }
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array("message" => $ex->getMessage()), 409, "FAIL");
    } catch (Exception $e) {
      $this->logger->log("Exception while creating Mail: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function addresseesAction() {
    
  }

  public function contentAction() {
    
  }

  public function advanceoptionsAction() {
    $detailConfig = $this->user->Usertype->Subaccount->Account->AccountConfig->DetailConfig;
    $attachment = false;
    $customizedpdf = false;
    foreach ($detailConfig as $item) {
      if ($item->idServices == $this->services->adjuntar_archivos) {
        $attachment = true;
      } else if ($item->idServices == $this->services->pdf_personalizado) {
        $customizedpdf = true;
      }
    }
    $this->view->setVar("attachment", $attachment);
    $this->view->setVar("customizedpdf", $customizedpdf);
  }

  public function shippingdateAction() {
    $detailConfig = $this->user->Usertype->Subaccount->Account->AccountConfig->DetailConfig;
    $mailTester = false;
    foreach ($detailConfig as $item) {
      if ($item->idServices == $this->services->mail_tester) {
        $mailTester = true;
      }
    }
    $this->view->setVar("mailTester", $mailTester);
  }

  public function timezoneAction() {
    $sql = "SELECT countries, gmt FROM timezone";
    $timezone = $this->db->fetchAll($sql);
    $jsontimezone = json_encode($timezone);

    return $this->set_json_response($jsontimezone, 200);
  }

  public function emailsenderAction() {
    $idaccount = $this->user->Usertype->Subaccount->idAccount;
    if (isset($idaccount)) {
      $sql = "SELECT idEmailsender, email FROM emailsender WHERE deleted=0 AND status=1 AND idAccount = " . $idaccount;
      $timezone = $this->db->fetchAll($sql);
      $jsontimezone = json_encode($timezone);

      return $this->set_json_response($jsontimezone, 200);
    } else {
      return $this->set_json_response("Error", 400);
    }
  }

  public function emailnameAction() {
    $idaccount = $this->user->Usertype->Subaccount->idAccount;
    if (isset($idaccount)) {
      $sql = "SELECT idNameSender, name FROM name_sender WHERE deleted=0 AND status=1 AND idAccount = " . $idaccount;
      $timezone = $this->db->fetchAll($sql);
      $jsontimezone = json_encode($timezone);

      return $this->set_json_response($jsontimezone, 200);
    } else {
      return $this->set_json_response("Error", 400);
    }
  }

  public function replytoAction() {
    $idaccount = $this->user->Usertype->Subaccount->idAccount;
    if (isset($idaccount)) {
      $sql = "SELECT idReplyTo, email FROM reply_tos WHERE deleted=0 AND status=1 AND idAccount = " . $idaccount;
      $timezone = $this->db->fetchAll($sql);
      $jsontimezone = json_encode($timezone);

      return $this->set_json_response($jsontimezone, 200);
    } else {
      return $this->set_json_response("Error", 400);
    }
  }

  public function htmlcontentAction($idMail) {
    try {
      if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount)) {
        $cfs = Customfield::find(["conditions" => "idAccount = ?0", "bind" => [0 => \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount]]);
        $arr = [];

        foreach ($cfs as $key) {
          $obj = new stdClass();
          $obj->idCustomfield = $key->idCustomfield;
          $obj->name = $key->name;
          $obj->alternativename = strtoupper($key->alternativename);
          array_push($arr, $obj);
        }

        $this->view->setVar("cfs", $arr);
      }

      $this->db->begin();

      $mail = Mail::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($idMail)
      ));

      if (!$mail) {
        $this->notification->error("El Mail que intenta editar no se encuentra registrado");
        return $this->response->redirect("mail/create#/content");
      }

      $mailcontent = MailContent::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($idMail)
      ));

      if (!$mailcontent) {
        $form = new HtmlContentForm();
      } else {
        $form = new HtmlContentForm($mailcontent);
        $content = html_entity_decode($mailcontent->content);
        $this->view->setVar("content", $content);
      }

      $footer = Footer::findFirstByIdFooter($this->user->Usertype->Subaccount->Account->AccountConfig->idFooter);

      $content = json_decode($footer->content);
      $content = json_encode($content->dz->footer->content);

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->assignContent(json_decode($footer->content));

      $objfooter = new stdClass();
      $objfooter->editor = $content;
      $objfooter->html = $editorObj->render();
      $objfooter->editable = $this->user->Usertype->Subaccount->Account->AccountConfig->footerEditable;

      $this->view->setVar('footer', $objfooter);

      $this->view->setVar("nameMail", $mail->name);
      $this->view->setVar("form", $form);
      $this->view->setVar("idMail", $idMail);
      if ($this->request->isPost()) {
        if (!$mailcontent) {
          $content = new MailContent();
          $form->bind($this->request->getPost(), $content);
          $text = new PlainText();
          $plainText = $text->getPlainText($this->request->getPost("content"));
          $mailcontent->plaintext = $plainText;
          $content->idMail = $idMail;
          $content->typecontent = "html";
          if (!$form->isValid() || !$content->save()) {
            foreach ($form->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
            foreach ($content->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
          }
        } else {
          $form->bind($this->request->getPost(), $mailcontent);
          $text = new PlainText();
          $plainText = $text->getPlainText($this->request->getPost("content"));
          $mailcontent->plaintext = $plainText;
          if (!$form->isValid() || !$mailcontent->update()) {
            foreach ($form->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
            foreach ($mailcontent->getMessages() as $message) {
              throw new InvalidArgumentException($message);
            }
          }
        }
        $this->db->commit();
        $this->createThumbnailMail($mail->idMail);
        $this->notification->success("Se ha Guardado el contenido del correo correctamente.");
        $resultArr = array("msg" => "Se ha Guardado el contenido del correo correctamente.");
        return $this->set_json_response($resultArr, 200, "OK");
      }
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->error("Ha ocurrido un problema guardado el contenido del correo..." . $ex->getMessage() . "----" . $ex->getTraceAsString());
      $this->notification->error("Ha ocurrido un problema guardado el contenido del correo, por favor comuníquese con soporte");
    }
  }

  public function editbasicinformationAction($idMail) {
    try {
      $mail = Mail::findFirst(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));

      $mxmc = Mxmc::find(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));


      if ($mail->idSubaccount != $this->user->Usertype->Subaccount->idSubaccount) {
        throw new \InvalidArgumentException("No se encontró el correo");
      }
      if (!$mail) {
        throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
      }
      $mailForm = new MailForm($mail);

      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $this->db->begin();
      if (!empty($arrayData)) {
        if (!empty($arrayData["replyto"]) && !filter_var($arrayData["replyto"], FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException("Formato de correo invalido");
        }
        if (!isset($arrayData['category']) || empty($arrayData['category'])) {
          throw new \InvalidArgumentException("Debes seleccionar una categoría");
        }

        $mail->idEmailsender = $arrayData["senderMailSelect"];
        $mail->idNameSender = $arrayData["senderNameSelect"];
        $mail->idReplyTo = $arrayData["replyToSelect"];

        if (isset($arrayData['test']) && $arrayData['test']) {
          $mail->test = 1;
        } else {
          $mail->test = 0;
        }
        $mailForm->bind($arrayData, $mail);

        if (!$mailForm->isValid()) {
          foreach ($mailForm->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        if (!$mail->save()) {
          foreach ($mail->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        foreach ($mxmc as $key) {
          if (!$key->delete()) {
            throw new InvalidArgumentException("No se pudo eliminar datos de mxmc");
          }
        }
        foreach ($arrayData['category'] as $category) {
          $mxmc = new Mxmc();
          $mxmc->idMail = $idMail;
          $mxmc->idMailCategory = $category;

          if (!$mxmc->save()) {
            throw new InvalidArgumentException("Ocurrio un error");
          }
        }
        $this->db->commit();
        $resultArr = array("idMail" => $mail->idMail, "msg" => "Se ha editado tu informacion!");
        return $this->set_json_response($resultArr, 200, "OK");
      }
    } catch (InvalidArgumentException $msg) {
      // $this->db->rollback();
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $e) {
      $this->logger->log("Exception while creating Mail: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function addemailnameAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $name = trim($arrayData['name']);

      if (!isset($name) || empty($name)) {
        throw new InvalidArgumentException("El campo Nombre del remitente es obigatorio");
      }
      if (is_numeric($name)) {
        throw new InvalidArgumentException("El campo Nombre del remitente no se permite solo numeros.");
      }
      $emailName = \NameSender::findFirst(["conditions" => "name = ?0 and idAccount = ?1", "bind" => array($name, $this->user->UserType->Subaccount->idAccount)]);

      if ($emailName) {
        throw new InvalidArgumentException("El nombre del remitente ya se encuentra registrado.");
      }
      if (!empty($arrayData)) {

        $emailName = new NameSender();
        $emailName->idAccount = $this->user->UserType->Subaccount->idAccount;
        $emailName->name = $name;
        $emailName->status = 1;
//        var_dump($emailName);
//      exit();
        if (!$emailName->save()) {
          foreach ($emailName->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
//        var_dump($emailName->idNameSender);
//        exit;
        return $this->set_json_response(array("idNameSender" => $emailName->idNameSender, "msg" => "Se ha guardado el nombre"), 200, "OK");
      }
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $ex) {
      $this->logger->log("Exception while creating emailName: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function addemailsenderAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $email = trim($arrayData['email']);
      $publicDomain = $this->user->UserType->Subaccount->Account->publicDomain;
      $domain = explode('@', $email);

      if (!isset($email) || empty($email)) {
        throw new InvalidArgumentException("El campo Correo del remitente es obigatorio");
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException("Formato de correo invalido");
      }
//      if ($publicDomain == 0) {
//        if (!$this->isAValidDomain($domain[1])) {
//          throw new InvalidArgumentException("Ha enviado una dirección de correo de remitente invalida, recuerde que no debe usar dominios de correo públicas como hotmail o gmail");
//        }
//      }
      $emailSender = \Emailsender::findFirst(["conditions" => "email = ?0 and idAccount = ?1", "bind" => array($email, $this->user->UserType->Subaccount->idAccount)]);

      if ($emailSender) {
        throw new InvalidArgumentException("El correo de remitente ya se encuentra registrado.");
      }
      if (!empty($arrayData)) {
        $emailSender = new Emailsender();
        $emailSender->idAccount = $this->user->UserType->Subaccount->idAccount;
        $emailSender->email = $email;
        $emailSender->status = 1;

        if (!$emailSender->save()) {
          foreach ($emailSender->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }

        return $this->set_json_response(array("idEmailsender" => $emailSender->idEmailsender, "msg" => "Se ha guardado el correo"), 200, "OK");
      }
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $ex) {
      $this->logger->log("Exception while creating emailSender: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function addreplytoAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);
      $email = trim($arrayData['email']);
      $publicDomain = $this->user->UserType->Subaccount->Account->publicDomain;
      $domain = explode('@', $email);

      if (!isset($email) || empty($email)) {
        throw new InvalidArgumentException("El campo de responder a es obigatorio");
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException("Formato de correo invalido");
      }

      $replyto = \ReplyTos::findFirst(["conditions" => "email = ?0 and idAccount = ?1", "bind" => array($email, $this->user->UserType->Subaccount->idAccount)]);

      if ($replyto) {
        throw new InvalidArgumentException("El correo de respuesta ya se encuentra registrado.");
      }

      if (!empty($arrayData)) {
        $replyto = new ReplyTos();
        $replyto->idAccount = $this->user->UserType->Subaccount->idAccount;
        $replyto->email = $email;
        $replyto->status = 1;

        if (!$replyto->save()) {
          foreach ($replyto->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }

        return $this->set_json_response(array("idReplyTo" => $replyto->idReplyTo, "msg" => "Se ha guardado el correo de respuesta"), 200, "OK");
      }
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $ex) {
      $this->logger->log("Exception while creating emailSender: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function isAValidDomain($domain) {
    $invalidDomains = $this->publicDomain;

    $d = explode('.', $domain);

    foreach ($invalidDomains as $invalidDomain) {
      if ($invalidDomain == $d[0]) {
        return false;
      }
    }
    return true;
  }

  public function contenteditorAction($idMail = null, $idMailStructure = null, $idTemplate = null) {
    $mail = Mail::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));

    $mail_content = MailContent::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));
    $condaccount = ($idAccount != NULL) ? "AND idAccount = {$idAccount}" : "AND idAccount IS NULL";
    $condallied = ($idAllied != NULL) ? "idAllied = {$idAllied}" : "idAllied IS NULL";
    $condition = (($idAccount != '') ? "OR" : "AND");

    $mailtempcateg = \MailTemplateCategory::find(array(
                "conditions" => "deleted = ?0 {$condaccount} {$condition} {$condallied}",
                "bind" => array(0),
                "order" => "created DESC"
    ));

    if ((isset($idMailStructure) || ($idMailStructure != null && $idMailStructure != 0) ) && !$mail_content) {
      $mail_content = \Mailstructure::findFirst(["conditions" => "idMailStructure = ?0", "bind" => [0 => $idMailStructure]]);
    }
    if ((isset($idTemplate) || ($idTemplate != null && $idTemplate != 0) ) && !$mail_content) {
      $mail_content = \MailTemplateContent::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => [0 => $idTemplate]]);
//      if ($mail_content->mailTemplate->global == 1) {
//        $this->moveassetalliedtoassetaccount($mail_content);
//      }
    }

    if (!$mail) {
      throw new InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
    }

    if ($mail_content) {
      $this->view->setVar("mail_content", $mail_content);
    } else {
      $mail_content = new MailContent();
    }

    $this->view->setVar('mail', $mail);
    $this->view->setVar("mailtempcat", $mailtempcateg);
    $this->view->setVar("app_name", "mail");

    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson, true);

      if (!empty($arrayData)) {
        
        $customLogger = new \Logs();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
        $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
        $customLogger->idMail = $idMail;
        $customLogger->typeName = "contentEditorMethod";
        $customLogger->detailedLogDescription = "Se ha guardado el contenido del correo exitosamente";
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
        unset($customLogger);
        
        $forceUtf8 = new \ForceUTF8\Encoding();
        $content = $forceUtf8->fixUTF8($arrayData['editor']);
//        $forceUtf8 = new \Sigmamovil\General\Misc\EscaperManager();
//        $content = $forceUtf8->toUtf8($arrayData['editor']);
        $mail_content->typecontent = "Editor";
        $mail_content->idMail = $idMail;
        $mail_content->content = $content;

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
        $editorObj->assignContent(json_decode($content));
        $contentmail = $editorObj->render();
        $text = new PlainText();
        $plainText = $text->getPlainText($contentmail);
        $mail_content->plaintext = $plainText;

        if ($mail_content->save()) {
          foreach ($mail_content->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->createThumbnailMail($mail_content->idMail);
        $this->session->set("msgContentEditor", "Se ha guardado el contenido del correo exitosamente");
        return $this->set_json_response(array("idMail" => $idMail, "msg" => "Se ha guardado el contenido del correo exitosamente"), 200, "Ok");
      }
    } catch (InvalidArgumentException $msg) {
      return $this->set_json_response(array("message" => $msg->getMessage()), 409, "FAIL");
    } catch (Exception $ex) {
      $this->logger->log("Exception while creating Content editor: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function moveassetalliedtoassetaccount($mailTemplate) {
    $imagens = \MailTemplateImage::find(array("conditions" => "idMailTemplate = ?0", "bind" => array($mailTemplate->idMailTemplate)));
    if (!$imagens) {
      return true;
    }


    //Falta la condicion si es de account
    if (isset($mailTemplate->idAllied)) {
      $baseDir = $this->asset->dirAllied . $mailTemplate->mailTemplate->idAllied . "/images/";
    } else {
      $baseDir = $this->asset->dirRoot . "/images/";
    }

    $baseDirAccount = $this->asset->dir . $this->user->userType->subAccount->idAccount;
    $baseDirAccount2 = $baseDirAccount . "/images";
    if (!file_exists($baseDirAccount2)) {
//      $aux = $baseDirAccount . "/images";
      if (!mkdir($baseDirAccount2, 0755, true)) {
        throw new InvalidArgumentException("Ocurrio un problema creando la carpeta {$baseDirAccount2}");
      }
    }
//echo $baseDirAccount;
//exit;
//    echo $mailTemplate->idMailTemplate;
//    exit;
    foreach ($imagens as $image) {
      $asset = new Asset();
      $asset->idAccount = $this->user->userType->subAccount->idAccount;
      $asset->name = $image->asset->name;
      $asset->size = $image->asset->size;
      $asset->type = $image->asset->type;
      $asset->contentType = $image->asset->contentType;
      $asset->dimensions = $image->asset->dimensions;
      $asset->extension = $image->asset->extension;
      if (!$asset->save()) {
        foreach ($asset->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }

      $dirAccount = $baseDirAccount . "/images/" . $asset->idAsset . "." . $asset->extension;
      $dir = $baseDir . $image->idAsset . "." . $image->asset->extension;

      if (file_exists($dirAccount)) {
        continue;
      }

      if (!copy($dir, $dirAccount)) {
        throw new InvalidArgumentException("Ocurrio un error pasando la imagen {$dir} a {$dirAccount}");
      }

      $dirAccount2 = $baseDirAccount . "/images/" . $asset->idAsset . "_thumb.png";
      $dir2 = $baseDir . $image->idAsset . "_thumb.png";

      if (!copy($dir2, $dirAccount2)) {
        throw new InvalidArgumentException("Ocurrio un error pasando la imagen {$dir} a {$dirAccount2}");
      }

      $dirAccount3 = $baseDirAccount . "/images/thumbnail_" . $asset->idAsset . ".png";
      $dir3 = $baseDir . $image->idAsset . "_thumb.png";

      if (!copy($dir3, $dirAccount3)) {
        throw new InvalidArgumentException("Ocurrio un error pasando la imagen {$dir} a {$dirAccount3}");
      }
    }
  }

  public function previewhtmlAction($idMail) {
    $html = $this->request->getPost("html");
    $this->session->remove('htmlObj');
    $footer = Footer::findFirstByIdFooter($this->user->Usertype->Subaccount->Account->AccountConfig->idFooter);
    if ($this->user->Usertype->Subaccount->Account->AccountConfig->footerEditable == 0) {

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      //$editorObj->setAccount($this->user->Usertype->Subaccount->Account);
      $editorObj->assignContent(json_decode($footer->content));

      $html = str_replace("</body></html>", $editorObj->render() . "</body></html>", $html);
    }
    if (trim($html) === '' || $html == null || empty($html)) {
      return $this->setJsonResponse(array('status' => 'Error'), 401, 'No hay html que previsualizar por favor verfique la informacion');
    }
    $url = $this->url->get('mail/previewmail');
    $script1 = '<head>
						<title>Preview</title>
						<script type="text/javascript" src="' . $this->url->get('js/html2canvas.js') . '"></script>
						<script type="text/javascript" src="' . $this->url->get('js/jquery-1.8.3.min.js') . '"></script>
						<script>
							function createPreviewImage(img) {
							$.ajax({
								url: "' . $url . '/' . $idMail . '",
								type: "POST",			
								data: { img: img},
								success: function(){}
								});
							}
						</script>';

    $script2 = '<script> 
						html2canvas(document.body, { 
							onrendered: function (c) { 
								c.getContext("2d");	
								createPreviewImage(c.toDataURL("image/png"));
							},
							height: 700
						});
				   </script></body>';

    $search = array('<head>', '</body>');
    $replace = array($script1, $script2);

    $htmlFinal = str_ireplace($search, $replace, $html);

    $this->session->set('htmlObj', $htmlFinal);

    return $this->set_json_response(array('status' => 'success'), 200); 
//		return $this->setJsonResponse(array('response' => $htmlFinal));
  }

  public function previeweditorAction($idMail) {
    $content = $this->request->getPost("editor");
    $this->session->remove('htmlObj');
    $url = $this->url->get('mail/previewmail');
    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj(true, $url, $idMail);
    $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
    $editorObj->assignContent(json_decode($content));
    $this->session->set('htmlObj', $editorObj->render());

    return $this->set_json_response(array('status' => 'Success'), 201, 'Success');
  }

  public function previewdataAction() {
    $htmlObj = $this->session->get('htmlObj');
    $this->session->remove('htmlObj');

    $this->view->disable();
    return $this->response->setContent($htmlObj);
  }

  public function previewmailAction($idMail) {
    $content = $this->request->getPost("imgData");
    //		$this->logger->log("Id: " . $idMail);
    //		$this->logger->log("Img: " . $content);
    $imgObj = new \Sigmamovil\General\Misc\ImageObject();
    $imgObj->createFromBase64($content);
    $imgObj->resizeImage(200, 250);
    $newImg = $imgObj->getImageBase64();

    /* $mail = Mail::findFirst(array(
      'conditions' => 'idMail = ?1 AND idSubaccount = ?2',
      'bind' => array(1 => $idMail,
      2 => $this->user->UserType->idSubaccount)
      ));

      $mail->previewData = $newImg;

      if (!$mail->save()) {
      $this->logger->log("Error while saving image base64");
      foreach ($mail->getMessages() as $msg) {
      $this->logger->log("Error: " . $msg);
      }
      } */
    //		$this->logger->log("NewImg: " . $newImg);
  }

  public function urleditorAction($idMail) {
    $account = $this->user->UserType->SubAccount->Account;
    $mail = Mail::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));
    $mail_content = MailContent::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));
    if (!$mail) {
      throw new InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
    }
    if ($mail_content) {
      $this->view->setVar("mail_content", $mail_content);
    } else {
      $mail_content = new MailContent();
    }
    $this->view->setVar('mail', $mail);
    $this->view->setVar("app_name", "mail");

    if ($this->request->isPost()) {

      $this->db->begin();
      $url = $this->request->getPost("url");
      $url = trim($url);
      $image = $this->request->getPost("image");

      $dir = $this->asset->dir . $account->idAccount . "/images";

      if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $this->logger->log("Error url no válida {$url}");
        return $this->set_json_response(array('error' => 'La url ingresada no es válida, por favor verifique la información'), 400);
      }

      if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
      }

      if ($image == true) {
        $image = "load";
      }
//      var_dump($image);
//      exit;
      try {
        $getHtml = new \LoadHtml();
        $content = $getHtml->gethtml($url, $image, $dir, $account);

        $search = array("\xe2\x80\x8b", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x9f", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9b", "á", "é", "í", "ó", "ú", "ñ", "Á", "É", "Í", "Ó", "Ú", "Ñ", "&nbsp;");
        $replace = array('', '"', '"', '"', "'", "'", "'", "á", "é", "í", "ó", "ú", "ñ", "Á", "É", "Í", "Ó", "Ú", "Ñ", "");
//        $html = htmlentities(str_replace($search, $replace, $content));
        $html = str_replace($search, $replace, $content);
        $forceUtf8 = new \ForceUTF8\Encoding();
        $html = $forceUtf8->fixUTF8($html);
//        $this->logger->log("Content: {$html}");

        $contentmail = MailContent::findFirst(array(
                    'conditions' => 'idMail = ?1',
                    'bind' => array(1 => $mail->idMail)
        ));

        if (!$contentmail) {
          $contentmail = new MailContent();
          $contentmail->idMail = $mail->idMail;
        }
        $text = new PlainText();
        $plainText = $text->getPlainText($html);
        $contentmail->plaintext = $plainText;
        $contentmail->typecontent = "url";
        $contentmail->content = $html;

        if (!$contentmail->save()) {
          foreach ($contentmail->getMessages() as $msg) {
            $this->logger->log("Error while saving content mail {$msg}");
          }
          throw new Exception('Error while saving content mail');
        }

        $this->db->commit();
        return $this->set_json_response(array('status' => 'success'), 200);
      } catch (\InvalidArgumentException $e) {
        $this->db->rollback();
        $this->logger->log("Exception {$e}");
        return $this->set_json_response(array('error' => 'Ha ocurrido un error, contacte al administrador'), 500);
      } catch (Exception $e) {
        $this->db->rollback();
        $this->logger->log("Exception {$e}");
        return $this->set_json_response(array('error' => 'El enlace proporcionado no es valido, por favor valide la información'), 500);
      }
    }
  }

  public function uploadfilesAction($idMail) {
    
  }

  public function mailstructureeditorAction($idMail) {
    $mail = Mail::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));
    $mail_content = MailContent::findfirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array(0 => $idMail)
    ));
    if (!$mail) {
      throw new InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
    }
    if ($mail_content) {
      $this->view->setVar("mail_content", $mail_content);
    } else {
      $mail_content = new MailContent();
    }
    $this->view->setVar('mail', $mail);
    $this->view->setVar("app_name", "mail");
  }

  public function previewAction($idMail) {
    $this->view->disable();
    $MailContent = MailContent::findFirst(array(
                "conditions" => "idMail = ?1",
                "bind" => array(1 => $idMail)
    ));

    if (!$MailContent) {
      return $this->set_json_response(array('status' => 'error'), 401, 'Error');
    }

    $response = "";
    if ($MailContent->typecontent == 'Editor') {
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
      $editorObj->assignContent(json_decode($MailContent->content));
      $response = $editorObj->render();
    } else if ($MailContent->typecontent == 'html' or $MailContent->typecontent == 'url') {
      $response = $MailContent->content;
    }

    return $this->set_json_response(array('preview' => $response));
  }

  public function clonemailAction($idMail) {
    try {
      $this->db->begin();
      $this->view->disable();
      if (!isset($idMail)) {
        $this->notification->error("Dato de correo inválido, por favor verifique la información");
        return $this->response->redirect("mail");
      }

      $idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
      $mail = Mail::findFirst(array(
                  "conditions" => "idMail = ?0 AND idSubaccount = ?1",
                  "bind" => array($idMail, $idSubaccount)
      ));

      if (!$mail) {
        $this->notification->error("El correo que intenta duplicar no existe");
        return $this->response->redirect("mail");
      }


      /**
       * Clonation of Mail
       */
      $mailClone = new Mail();
      $mailClone->idSubaccount = $mail->idSubaccount;
      $mailClone->idEmailsender = $mail->idEmailsender;
      $mailClone->idNameSender = $mail->idNameSender;
      $mailClone->categorycampaign = $mail->categorycampaign;
      $mailClone->name = $mail->name . " (copia)";
      $mailClone->sender = $mail->sender;
      $mailClone->replyto = $mail->replyto;
      $mailClone->subject = $mail->subject;
      $mailClone->gmt = $mail->gmt;
      $mailClone->attachment = $mail->attachment;
      $mailClone->status = "draft";
      $mailClone->quantitytarget = 0;
      $mailClone->test = $mail->test;
      $mailClone->uniqueOpening = 0;
      $mailClone->deleted = 0;
      $mailClone->totalOpening = 0;
      $mailClone->uniqueClicks = 0;
      $mailClone->previewData = $mail->previewData;
      $mailClone->bounced = 0;
      $mailClone->totalCliks = 0;
      $mailClone->spam = 0;
      $mailClone->messagesSent = 0;
      $mailClone->sentprocessstatus = null;
      $mailClone->type = 'manual';

      if (!$mailClone->save()) {
        foreach ($mailClone->getMessages() as $msg) {
          throw new InvalidArgumentException($msg);
        }
      }

      $mailattchment = Mailattachment::find(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($mail->idMail)
      ));

      if (count($mailattchment) > 0) {
        foreach ($mailattchment as $value) {
          $mailattchmentClone = new Mailattachment();
          $mailattchmentClone->idMail = $mailClone->idMail;
          $mailattchmentClone->idAsset = $value->idAsset;

          if (!$mailattchmentClone->save()) {
            foreach ($mailattchmentClone->getMessages() as $msg) {
              throw new InvalidArgumentException($msg);
            }
          }
        }
      }

      $mxmc = Mxmc::find(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($mail->idMail)
      ));

      if (count($mxmc) > 0) {
        foreach ($mxmc as $value) {
          $mxmcClone = new Mxmc();
          $mxmcClone->idMail = $mailClone->idMail;
          $mxmcClone->idMailCategory = $value->idMailCategory;

          if (!$mxmcClone->save()) {
            foreach ($mxmcClone->getMessages() as $msg) {
              throw new InvalidArgumentException($msg);
            }
          }
        }
      }

      $mailcontent = MailContent::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($mail->idMail)
      ));

      if ($mailcontent) {
        $mailcontentClone = new MailContent();
        $mailcontentClone->idMail = $mailClone->idMail;
        $mailcontentClone->typecontent = $mailcontent->typecontent;
        $mailcontentClone->content = $mailcontent->content;

        if (!$mailcontentClone->save()) {
          foreach ($mailcontentClone->getMessages() as $msg) {
            throw new InvalidArgumentException($msg);
          }
        }
      }

      $mailgoogleanalytics = Mailgoogleanalytics::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($mail->idMail)
      ));
      if ($mailgoogleanalytics) {
        $mailgoogleanalyticsClone = new Mailgoogleanalytics();
        $mailgoogleanalyticsClone->idMail = $mailClone->idMail;
        $mailgoogleanalyticsClone->name = $mailgoogleanalytics->name;
        $mailgoogleanalyticsClone->links = $mailgoogleanalytics->links;

        if (!$mailgoogleanalyticsClone->save()) {
          foreach ($mailgoogleanalyticsClone->getMessages() as $msg) {
            throw new InvalidArgumentException($msg);
          }
        }
      }

      $this->db->commit();
      $this->createThumbnailMail($mailClone->idMail);
      $this->notification->success("El correo ha sido duplicado exitosamente");
      return $this->response->redirect("mail/create#/basicinformation/" . $mailClone->idMail);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("mail");
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Error cloning... {$ex->getMessage()} ------- {$ex->getTraceAsString()}");
      $this->notification->error("Ha ocurrido un error, por favor comuniquese con soporte");
      return $this->response->redirect("mail");
    }
  }

  public function deleteattachedAction() {
    
  }

  public function createThumbnailMail($idMail) {
    $idAccount = ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL);
    $dirAcc = getcwd() . "/assets/";
    $dir = "{$dirAcc}{$idAccount}/images/mails/";

    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }

    $dir .= "{$idMail}_thumbnail.png";
    $domain = $this->urlManager->get_base_uri(true);
    exec("wkhtmltoimage --quality 25 --zoom 0.2 --width 178 --height 248 {$domain}thumbnail/mailshow/{$idMail} {$dir}");
  }
  
  public function translateStatusMail($status) {
    $statusSpanish = "";
    switch ($status) {
      case "sent":
        $statusSpanish = "Enviado";
        break;
      case "draft":
        $statusSpanish = "Borrador";
        break;
      case "sending":
        $statusSpanish = "En proceso de Envío";
        break;
      case "scheduled":
        $statusSpanish = "Programado";
        break;
      case "paused":
        $statusSpanish = "Pausado";
        break;
      case "canceled":
        $statusSpanish = "Cancelado";
        break;
    }
    return $statusSpanish;
}
  
  public function loadpdfAction($idMail){
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    //$mail = Mail::findFirst([["idSubaccount" => $idSubaccount, "idMail"  => $idMail, "status" => 'draft']]);
    $mail = \Mail::findFirst( array( 
                "conditions" => "idMail = ?0 AND idSubaccount = ?1 AND deleted = ?2",
                "bind" => [ $idMail , $idSubaccount , 0 ] 
            ));
    
    if (!$mail) {
        $this->response->redirect("error");
    }
        
    $mail->pdf = 1;
    if (!$mail->save()) {
      foreach ($mail->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
    }
    
    if ($this->request->isPost()) {
        if ($_FILES["file"]["error"]) {
            return $this->setJsonResponse(array(
                'error' => 'No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, contacte al administrador para más información')
                , 400 , 'Archivo vacio o incorrecto');
        }
        if (empty($_FILES['file']['name'])) {
            return $this->setJsonResponse(array(
                'error' => 'No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información')
                , 400 , 'Archivo vacio o incorrecto');
        }
        else {  
            $data = new stdClass();
            $data->originalName = $_FILES['file']['name'];
            $data->name = $_FILES['file']['name'];
            $data->size = $_FILES['file']['size'];
            $data->type = $_FILES['file']['type'];
            $data->tmp_dir = $_FILES['file']['tmp_name'];

            $ext = array('zip', 'ZIP');

            try {
                $dir = "{$this->asset->dir}{$idAccount}/pdf/{$mail->idMail}/";

                $uploader = new Sigmamovil\General\Misc\Uploader();
                $uploader->setAccount($idAccount);
                $uploader->setMail($mail);
                $uploader->setData($data);
                $uploader->validateExt($ext);
                $uploader->validateSizeObj(512000);
                $uploader->uploadFile($dir);
        
                $pdfmanager = new PdfManager();
                $pdfmanager->setMail($mail); //so far - so good...
                $pdfmanager->setSource($uploader->getSource());
                $pdfmanager->setDestination($uploader->getFolder());
                $pdfmanager->extract();
                $pdfmanager->searchIdContacts();
		
                //var_dump($pdfmanager->validateAllCustomField());die;
                if ($pdfmanager->validateAllCustomField() == TRUE) { $pdfmanager->save(); }
                /*else{ 
		  
		  return $this->set_json_response(array("error" => "no se pudo cargar el archivo"), 500); 
		  }*/
		//echo "aqui llegue yo: ".__LINE__; die;
                $total = $pdfmanager->getTotal();
                
                return $this->set_json_response(array("success" => "Se han cargado {$total} archivo(s) exitosamente"), 200);
            } 
            catch (InvalidArgumentException $e) {
                $this->db->rollback();
                $this->logger->log("Exception: Error while uplodaing pdf {$e}");
                return $this->setJsonResponse(array('error' => $e->getMessage()), 400 , 'Error en archivo!');
            }
            catch (Exception $e) {
                $this->db->rollback();
                $this->logger->log("Exception: Error while uplodaing pdf {$e}");
                return $this->setJsonResponse(array('error' => "Ha ocurrido un error, por favor contacte al administrador"), 500 , 'Error interno!');
            }
        }
    }

    $this->view->setVar('mail', $mail);
  }
  
  public function composeAction($idMail = null){
    $subaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount;

    if($idMail != null) {
      $mail = Mail::findFirst(array(
          'conditions' => "idSubaccount = ?1 AND idMail = ?2 AND status = 'Draft' AND pdf = 1",
          'bind' => array(1 => $subaccount->idSubaccount,
                          2 => $idMail)
      ));
      $mailcontent = Mailcontent::findFirst(array(
        'conditions' => 'idMail = ?1',
        'bind' => array(1 => $idMail)
      ));

      if ($mailcontent) {
        switch ($mail->type) {
          case 'Html':
            $footerObj = new FooterObj();
            $footerObj->setAccount($this->user->account);
            $html = $footerObj->addFooterInHtml(html_entity_decode($mailcontent->content)); 
            break;

          case 'Editor':
            $editor = new HtmlObj();
            $editor->setAccount($this->user->account);
            $editor->assignContent(json_decode($mailcontent->content));
            $html = $editor->render();
            break;
        }
      }

      $this->view->setVar('mail', $mail);
    }
  }
  
  public function structurenameAction($idMail) {
    $subaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount;
    $mail = Mail::findFirst(array(
      'conditions' => 'idSubaccount = ?0 AND idMail = ?1 AND pdf = 1',
      'bind' => [0 => $subaccount->idSubaccount,  1 => $idMail]
    ));    
    if (!$mail) {
      $this->response->redirect("error");
    }
    if (!empty($mail->pdfstructure)) {
      $this->view->setVar('structure', $mail->pdfstructure);
    }
    if ($this->request->isGet()) {
      try {
        $contacts = $this->findContacts($mail);
        $totalContacts = count($contacts);
        
        $sql = "SELECT DISTINCT idContact FROM pdfmail WHERE"
          . " idMail = {$mail->idMail} "
          . " AND status = 1 ";
        $totalpdfmail = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
        $dir = "{$this->asset->dir}{$subaccount->idAccount}/pdf/{$mail->idMail}/";
        $ArrayFileNames = $this->getFileNamesInFolder($dir, $idMail);
        $totalNumberOfFiles = $this->getTotalFilesInFolder($dir);
        if($this->validateAllCustomField($dir)){
          $result = array();
          $result['files'] = $ArrayFileNames;	
          $result['total'] = $totalNumberOfFiles;
            $result['totalfiles'] = $totalNumberOfFiles;
            $result['totalfilematch'] = count($totalpdfmail);
            $result['totalcontacts'] = $totalContacts;
            $result['totalcontactsmatch'] = count($totalpdfmail); // momentaneo
          $result['uploadstatus'] = "success"; //esto se coloca para que pueda mostrar notificacion de error
          $result['enunciadoFinal'] = "Se han cargado los PDF's exitosamente, para continuar con el proceso haga clic en continuar";
        }else{
          foreach (glob($dir."*.*") as $filename) {
            unlink($filename);
          }
          $result = array();
          $result['files'] = $ArrayFileNames;	
          $result['total'] = $totalNumberOfFiles;
             $result['totalfiles'] = $totalNumberOfFiles;
             $result['totalfilematch'] = 0;
             $result['totalcontacts'] = $totalContacts;
             $result['totalcontactsmatch'] = 0; // momentaneo
          $result['uploadstatus'] = "error"; //esto se coloca para que pueda mostrar notificacion de error
          $result['enunciadoFinal'] = "El archivo que intentas subir presenta inconsistencias"; // momentaneo
        }
        return $this->set_json_response( $result , 200, "OK");

      } 
      catch (Exception $ex) {
        $this->logger->log("Exception while structuturating pdf: " . $ex->getMessage());
        $this->logger->log($ex->getTraceAsString());
      }
    }
    $this->view->setVar('mail', $mail);
  } 

  private function validateAllCustomField($dir){
    $arrayNms = $this->getFileNamesInFolder($dir);
    if( count( explode( '_', $arrayNms[0] ) ) != 1){  //si el numero de items que arroja el explode es diferente de 1 entonces no hay guion bajo...
      //es decir no tendra campo personalizado al principio del nombre ya que es necesario que tenga guion... para que el explode saque el campo Personlizado
      $campoPrzdBase = explode( '_', $arrayNms[0] )[0];  //para compararlo luego.
      foreach ( $arrayNms  as $value ) {
        $campoTemporal = explode('_', $value)[0];  //--> tomo el campo Personalizado
        //var_dump(strtoupper($campoTemporal));
        //die;
        //si al compararlos todos los campos existe alguno que no sea igual al primer campo personalizado devuelva falso.
        if (($campoPrzdBase === $campoTemporal) == FALSE )  { return FALSE; }
      } //die;
      //return 'TRUE';
    }
    return TRUE;  
  }  

  private function getTotalFilesInFolder($dir){
    //Obtenemos las cantidad de archivos pdf en el directorio
    return count(glob($dir . "{*.pdf}",GLOB_BRACE));
  }

  public function getFileNamesInFolder($dir, $idMail){
    $arrayFiles = glob($dir . "{*.pdf}", GLOB_BRACE);
    $arrayFilesName = array();
    // hago una iteracion sobre el arreglo de ubicaciones de archivos
    foreach ($arrayFiles as $file) {
    $path_parts = pathinfo($file);
      $basename = $path_parts['basename'];  //solamente saco el basename
      //$arrayFilesName[] =explode( '.', $basename )[0];  // y luego el nombre sin el ".pdf"
      $findFirst  = Pdfmail::findFirst(array(
        'conditions' => 'idMail = ?0 AND name = ?1 AND status = ?2',
        'bind' => array(0 => $idMail, 1 => $basename, 2 => 1)
      ));
      $arrayFilesName[] = ['id' => $findFirst->idPdfmail, 'name' => $basename];  // y luego el nombre sin el ".pdf"
    }
    return $arrayFilesName;
  }
  
   private function getFilesThatMatch($dir, $structure){
    /* obtenemos los nombres de los archivos PDF */
    $files = glob($dir . "{*.pdf}",GLOB_BRACE);
    $matches = array();

    // Recorremos los nombres encontrados y buscamos el texto de la cedula
    $contador = 0;
    foreach ($files as $file) {
            $filep = explode('/', $file) ;
            $f = $filep[count($filep)-1];

            preg_match_all($this->pdf_valid_names[$structure], $f, $result);
            if (!empty($result[1]) || !empty($result[0])) {
                    $matches[] =  $result;
                    $contador++;
            }
    }

    $result = new stdClass();
    $result->total = $contador;
    $result->matches = $matches;

    return $result;
  }
  
  public function findContacts($mail){       
    $target = json_decode($mail->target);
    if(isset($target->type)){
      switch ($target->type) {
        case "contactlist":
          $this->getIdContaclist($mail);
          $contacts = $this->getAllCxcl(); 
          break;
        case "segment":
          $this->getIdSegment();
          $this->getAllIdContactSegment();
          break;
        default:
      }
    }
    return $contacts;      
   }
      
  public function getAllCxcl() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist IN ({$idContactlist})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 "
            . " AND spam = 0 "
            . " AND bounced = 0 "
            . " AND blocked = 0"
            . " AND singlePhone = 0";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    foreach ($cxcl as $value) { $arrayIdsContact[] = (int) $value['idContact']; }
    return $arrayIdsContact;       
  }
  
  public function getIdSegment() {
    $target = json_decode($this->mail->target);
    if (isset($target->segment)) {
      foreach ($target->segment as $key) {
        $this->idSegment[] = $key->idSegment;
      }
    }
  }
  
  public function getAllIdContactSegment() {
    $segment = Sxc::find([["idSegment" => ['$in' => $this->idSegment]], "limit" => $this->limit, "skip" => $this->offset]);
    unset($this->idSegment);
    foreach ($segment as $key) {
      $this->inIdcontact[] = (int) $key->idContact;
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
  }
  
  public function deletedallAction($idMail){
    $findpdfmail = Pdfmail::find(array(
      'conditions' => 'idMail = ?0 AND status = ?1',
      'bind' => array(0 => $idMail, 1 => 1)
    ));
    foreach ($findpdfmail as $value) {
      $value->status = 0;
      $value->update();
    }
    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    exec("rm -rf /websites/aio/public/assets/{$idAccount}/pdf/{$idMail}");
  }

    
  public function getIdContaclist($mail) {
    $target = json_decode($mail->target);
    
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
        break;
      case "segment":
//        if (isset($target->segment)) {
//          $this->getIdContactlistBySegments($target->segment);
//        }
        break;
      default:
        throw new Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
    }
  }
  
 }