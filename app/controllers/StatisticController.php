<?php

class StatisticController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Estadísticas");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "statistic");
  }

  public function mailAction() {
    
  }
  
  public function nodoAction() {
    
  }

  public function smsAction() {
    
  }

  public function smsshareAction($params) {
    list($one, $idSms, $type, $code) = explode("-", $params);

//    echo "one: ".$one."<br>";
//    echo "type: ".$type."<br>";
//    echo "idSms: ".$idSms."<br>";
//    echo "code: ".$code."<br>";
//    exit;
    $this->view->setVar("type", $type);
    $this->view->setVar("idSms", $idSms);
    $this->view->setVar("app_name", "statistic");
  }

  public function shareAction($params) {
    list($one, $idMail, $idSubaccount, $type, $code) = explode("-", $params);
//    echo $one;
//    echo $idMail;
//    echo $idSubaccount;
//    echo $type;
//    echo $code;
//    exit;
    $this->view->setVar("idMail", $idMail);
    $this->view->setVar("type", $type);
    $this->view->setVar("app_name", "statistic");
//    echo $params;
  }

  public function downloadAction() {
    $this->view->disable();
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename=reporte.xlsx');
    header('Pragma: public');
    header('Expires: 0');
    header('Content-Type: application/download');
    $route = __DIR__ . "/../../tmp/reporte.xlsx";
//    $route = getcwd() . "/tmp/reporte.xlsx";//local
    readfile($route);
    unlink($route);
  }

  public function surveyAction() {
    
  }

  public function automaticcampaignAction() {
    $this->view->setVar("app_name", "statistic");
  }
  
  public function smstwowayAction(){}

  public function whatsappAction(){}
  
  public function downloadexcelAction($name) {
    $this->view->disable();
    $nameFull = str_replace(" ", "_", $name) . "_" . date('Y-m-d') . ".xlsx";
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$nameFull);
    header('Pragma: public');
    header('Expires: 0');
    header('Content-Type: application/download');
    $route = __DIR__ . "/../../tmp/".$nameFull;
//    $route = getcwd() . "/tmp/".$nameFull;//local
    $val = file_exists($route);
    if ($val) {
      $valRead = readfile($route);
      if($valRead){
        $valUnlink = unlink($route);
        return $valUnlink;
      }
    } else {
      echo "Ha ocurrido un problema con el servidor por favor vuelva a intentarlo o comuniquese con el administrador del sistema";
    }
  }
  
}
