<?php

class FooterController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Footer");
    parent::initialize();
  }

  public function indexAction() {

  }

  public function createAction() {
    $footerForm = new FooterForm();

    $this->view->setVar("footerForm", $footerForm);
  }

  public function previewAction($id)
  {
    $this->view->disable();
    $footer = Footer::findFirst(array(
        "conditions" => "idFooter = ?1",
        "bind" => array(1 => $id)
    ));

    return $this->set_json_response(array('preview' =>  $footer->content));
  }

  public function updateAction($idFooter) {

    $footer = Footer::findFirst(array(
        "conditions" => "idFooter = ?0",
        "bind" => array(0 => $idFooter)
    ));

    if (!$footer) {
      throw new InvalidArgumentException("El footer que desea editar no existe, por favor valide la información");
    }

    $footerForm = new FooterForm();

    $this->view->setVar("idFooter", $idFooter);
    $this->view->setVar("content", $footer->content);
    $this->view->setVar("footerForm", $footerForm);
  }

  public function deleteAction() {

  }

  public function previeweditorAction() {
    if ($this->request->isPost()) {
      $content = $this->request->getPost("editor");
      $this->session->remove('preview-template');
      $url = $this->url->get('mailpreview/createpreview');
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj(true, $url);
      $editorObj->assignContent(json_decode($content));
      $this->session->set('preview-template', $editorObj->render());

      return $this->set_json_response(array('status' => 'Success'), 201, 'Success');
    }
  }

  public function previewdataAction() {
    $htmlObj = $this->session->get('preview-template');
    $this->session->remove('preview-template');
    $this->view->disable();

    return $this->response->setContent($htmlObj);
  }

  public function previewindexAction($idFooter) {
    $footer = Footer::findFirst(array(
        "conditions" => "idFooter = ?0",
        "bind" => array(0 => $idFooter)
    ));

    if ($footer) {

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->assignContent(json_decode($footer->content));
      $response = $editorObj->render();

      return $this->set_json_response(array('preview' => $response));

    } else {
      return $this->set_json_response(array('status' => 'error'), 401, 'Error');
    }
  }

}