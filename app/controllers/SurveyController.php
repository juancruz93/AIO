<?php

class SurveyController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Encuestas");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar('app_name', "survey");
  }

  public function listAction() {
    
  }

  public function createAction() {
//    $this->view->setVar("app_name", "survey");
  }

  public function basicinformationAction() {
    $surveyForm = new SurveyForm();
    $this->view->setVar("surveyForm", $surveyForm);
  }

  public function surveyAction() {
    
  }

  public function confirmationAction() {
    
  }

  public function showsurveyAction($idSurvey, $idContact = 0) {
    if (!isset($idSurvey)) {
      $this->notification->error("Error enviando parametro de encuesta");
      $this->response->redirect("https://aio.sigmamovil.com/");
    }

    $survey = Survey::findfirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));

    if (!$survey) {
      $this->notification->error("Error enviando parametro de encuesta");
      $this->response->redirect("http://aio.sigmamovil.com/");
    }

    $startDate = TRUE;
    $endDate = TRUE;
    $status = TRUE;

    if (($survey->startDate > date("Y-m-d H:i:s", time()))) {
      $startDate = FALSE;
    }

    if (($survey->endDate < date("Y-m-d H:i:s", time()))) {
      $endDate = FALSE;
    }

    if ($survey->status == "draft") {
      $status = FALSE;
    }

    $this->view->setVars(array(
        "survey" => $survey,
        "urlBase" => \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true),
        "idContact" => $idContact,
        "startDate" => $startDate,
        "endDate" => $endDate,
        "status" => $status
    ));
  }

  public function congratulationsAction($idSurvey) {
    $survey = Survey::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));
    
    if (!$survey) {
      $this->view->setVar("msg", "La encuesta que realizó no existe");
    }
    $this->view->setVar("url", (empty($survey->url)? (empty($survey->Subaccount->Account->url)? Phalcon\DI\FactoryDefault::getDefault()->get('urlSurvey')->urlSurvey:$survey->Subaccount->Account->url) : $survey->url));
    $this->view->setVar("msg", $survey->messageFinal);
  }
  
  public function shareAction(){
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->view->setVar('idfb',$di->get('configFb')->idApp);
  }

}
