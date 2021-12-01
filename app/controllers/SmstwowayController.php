<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SmstwowayController extends ControllerBase {

  protected $hoursms;

  public function initialize() {
    $this->tag->setTitle("SMS doble vía");
    parent::initialize();
    $this->hoursms = new \stdClass();
    $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
    $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;

    $flag = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      echo $key->idServices;
      if ($key->idServices == 7 && $key->status ==1) {
        $flag = true;
      }
    }
    if ($flag == false ) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("marketing");
    }

  }

  //vista de opciones de envios de sms doble via
  public function toolstwowayAction() {
    $this->view->setVar("app_name", "swmstwoway");
  }

  //Vista donde va a estar ng-view
  public function createAction() {
    
  }
  
  //vista para listar de envio de sms doble via por lote
  public function listAction(){
    
  }

  //vista de creacion de envio de sms doble via por lote
  public function speedsentAction() {
    
  }

  //vista de creacion de envio de sms doble via por archivo csv
  public function createcsvAction() {
    
  }

  //vista de creacion de envio de sms doble via por archivo csv
  public function editspeedsentAction() {
  }

  //vista del listado de envios de SMS doble via (index)
  public function indexAction() {
    
  }
  
  //vista de informacion detallada del envio de SMS doble via
  public function showlotetwowayAction(){
    
  }

  public function validateDate($date, $timezone) {
    
    if (!isset($this->hoursms) || empty($this->hoursms)) {
      $this->hoursms = new \stdClass();
      $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
      $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;
    }
    
    $timezone = substr($timezone, 0, 3);
    //var_dump("si entro al validateDate y paso en primer if ");
    //var_dump("con un timezone: " . $timezone); 
    //var_dump("¿el substring es igual a cero?: ", ($timezone[1] == 0) );

    if ($timezone[1] == 0) {
      
      $typeGmt = substr($timezone, 0, 1);
      $timezone = substr($timezone, 2, 2);
      if ($typeGmt == "-") {
        if ($timezone > 5) {
          $timezone = $timezone - 5;
        } else {
          $typeGmt = "+";
          $timezone = 5 - $timezone;
        }
      } else if ($typeGmt == "+") {
        $timezone = 5 + $timezone;
      }
      
      $datenowstr = strtotime("{$typeGmt}{$timezone} hour", strtotime($date));
    
      $dateStart = date("Y-m-d H:i:s", $datenowstr);
      $hour = date("H", $datenowstr);

      if ($hour < $this->hoursms->startHour || $hour >= $this->hoursms->endHour) {
        throw new InvalidArgumentException("La hora de envio debe de ser entre las " . $this->hoursms->startHour . ":00  y las " . $this->hoursms->endHour . ":00 de acuerdo al GMT seleccionado");
      }
      
      return $dateStart;
      
    }
    
    //var_dump("paso todos los if anidado con exito");exit;

    
    
    
  }
  
  public function createdcontactAction(){
    
  }
  

}
