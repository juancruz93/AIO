<?php

/**
 * Description of FormsController
 *
 * @author desarrollo3
 */
class FormsController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Formularios");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "forms");
  }

  public function createAction() {
    $this->view->setVar("app_name", "forms");
  }

  public function listAction() {
    
  }

  public function basicinformationAction() {
    
  }

  public function formsAction() {
    
  }

  public function contactsAction($idForm) {

//    $form = Form::findFirst([
//                'conditions' => 'idForm = ?0',
//                'bind' => [$idForm]
//    ]);
//
//    if (!$form) {
//      $this->notification->error('No se encontró el formulario, por favor valide la información');
//      $this->response->redirect('forms/');
//    }
//
//    $this->view->setVar("app_name", "forms");
//    $this->view->setVar("form", $form);
  }

  public function structureformAction($idForm) {
    $form = Form::findFirst([
                'conditions' => 'idForm = ?0',
                'bind' => [$idForm]
    ]);

    if (!$form) {
      $this->logger->log("el formulario no fue encontrado {$idForm}");
    }
    
    $Habeasdata = \Phalcon\DI::getDefault()->get('habeasData')->habeasData;
    if(!empty($form->habeasData)){
      $Habeasdata = $form->habeasData;
    }else if(!empty($form->Subaccount->Account->habeasData)){
      $Habeasdata = $form->Subaccount->Account->habeasData;
    }
    
    $view = new $this->view();
    $structureForm = file_get_contents(__DIR__ . "/../views/forms/structureform.volt", true);
    $view->setContent(str_replace(array("%%IDFORM%%","%%urlBase%%","%%HB%%"), array($idForm,\Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true),$Habeasdata), $structureForm));
    
    echo $view->getContent();
    $this->view->disable();
  }
  
  public function reportAction() {
    $this->view->setVar("app_name", "forms");
  }
  
  public function editAction() {
    $this->view->setVar("app_name", "forms");
  }

}
