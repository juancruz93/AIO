<?php

class AutoresponderController extends ControllerBase
{
  
  public function initialize() {
    $this->tag->setTitle("Autorespuestas");
    parent::initialize();
  }

  public function indexAction()
  {
    $this->view->setVar("app_name", "autoresponder");
  }
  
  //vista de opciones de creacion de autorespuestas 
  public function toolsAction()
  {
    $this->view->setVar("app_name", "autoresponder");
  }
  
  public function birthdayAction()
  {
    $autoresponderForm = new AutoresponderForm();
    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    $this->view->setVar("autoresponderForm", $autoresponderForm);
    $this->view->setVar("idAccount", $idAccount);
  }
  
  public function birthdaysmsAction()
  {
    $autoresponderForm = new AutoresponderForm();
    $this->view->setVar("autoresponderForm", $autoresponderForm);
  }

  public function listAction()
  {
  }

  public function contenteditorAction($idAutoresponder = null, $idTemplate = null)
  {
    try {
      $autoresponder = Autoresponder::findFirst(array(
          'conditions' => 'idAutoresponder = ?0',
          'bind' => [$idAutoresponder]
      ));
      if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }

      $autoresponderContent = $autoresponder->AutoresponderContent;

      if (!$autoresponderContent) {
        if ((isset($idTemplate) || ($idTemplate != null && $idTemplate != 0) ) && !$autoresponderContent) {
          $autoresponderContent = \MailTemplateContent::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => [0 => $idTemplate]]);
        }
        $this->view->setVar('autoresponder_content', $autoresponderContent);
        $this->view->setVar('autoresponder', $autoresponder);
        $this->view->setVar("app_name", "autoresponder");
      } else {
        $this->view->setVar('autoresponder_content', $autoresponderContent);
        $this->view->setVar('autoresponder', $autoresponder);
        $this->view->setVar("app_name", "autoresponder");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      return $this->response->redirect('autoresponder/#/');
    } catch (Exception $ex) {
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function contenthtmlAction($idAutoresponder = null)
  {
    try {
      $autoresponder = Autoresponder::findFirst(array(
          'conditions' => 'idAutoresponder = ?0',
          'bind' => [$idAutoresponder]
      ));
      if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }

      $autoresponderContent = $autoresponder->AutoresponderContent;
      if (!$autoresponderContent) {
        $form = new HtmlContentForm();

        $this->view->setVar('autoresponder', $autoresponder);
      } else {
        $form = new HtmlContentForm($autoresponderContent);
        $content = html_entity_decode($autoresponderContent->content);
        $this->view->setVar("content", $content);

        $this->view->setVar('autoresponder', $autoresponder);
      }

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

      $footer = Footer::findFirstByIdFooter($this->user->Usertype->Subaccount->Account->AccountConfig->idFooter);

      $content = json_decode($footer->content);
      $content = json_encode($content->dz->footer->content);

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->assignContent(json_decode($footer->content));

      $objfooter = new stdClass();
      $objfooter->editor = $content;
      $objfooter->html = $editorObj->render();
      $objfooter->editable = $this->user->Usertype->Subaccount->Account->AccountConfig->footerEditable;

      $this->view->setVar("form", $form);
      $this->view->setVar('footer', $objfooter);
      $this->view->setVar("idAutoresponder", $idAutoresponder);

      if ($this->request->isPost()) {
        if (!$autoresponderContent) {
          $autoresponderContent = new \AutoresponderContent();

          $autoresponderContent->idAutoresponder = $idAutoresponder;
          $autoresponderContent->content = $this->request->getPost("content");
          $autoresponderContent->type = 'html';

          if (!$autoresponderContent->save()) {
            foreach ($autoresponderContent->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }

        } else {
          $autoresponderContent->content = $this->request->getPost("content");

          if (!$autoresponderContent->save()) {
            foreach ($autoresponderContent->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
        }

        $this->notification->success("Se ha Guardado el contenido del correo correctamente.");
        $resultArr = array("msg" => "Se ha Guardado el contenido del correo correctamente.");
        return $this->set_json_response($resultArr, 200, "OK");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      return $this->response->redirect('autoresponder/#/');
    } catch (Exception $ex) {
      $this->logger->log("Exception while save autoresponder... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function previeweditorAction()
  {
    if ($this->request->isPost()) {
      $content = $this->request->getPost("editor");
      $this->session->remove('preview-template');
      $url = $this->url->get('mailpreview/createpreview');
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj(true, $url);
      $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
      $editorObj->assignContent(json_decode($content));
      $this->session->set('preview-template', $editorObj->render());

      return $this->set_json_response(array('status' => 'Success'), 201, 'Success');
    }
  }

  public function previewdataAction()
  {
    $htmlObj = $this->session->get('preview-template');
    $this->session->remove('preview-template');
    $this->view->disable();

    return $this->response->setContent($htmlObj);
  }

  public function previewAction($idAutoresponder)
  {
    $this->view->disable();
    $autoresponderContent = AutoresponderContent::findFirst(array(
        'conditions' => 'idAutoresponder = ?0',
        'bind' => [$idAutoresponder]
    ));

    if (!$autoresponderContent) {
      return $this->set_json_response(array('status' => 'error'), 401, 'Error');
    }
    $response = "";
    if ($autoresponderContent->type == 'Editor') {
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
      $editorObj->assignContent(json_decode($autoresponderContent->content));
      $response = $editorObj->render();
    } else if ($autoresponderContent->type == 'html' or $autoresponderContent->type == 'url') {
      $response = $autoresponderContent->content;
    }
    return $this->set_json_response(array('preview' => $response));
  }

  public function previewhtmlAction($idAutoresponder)
  {
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
								url: "' . $url . '/' . $idAutoresponder . '",
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
  }
}
