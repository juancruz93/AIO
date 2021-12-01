<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SmsScript
 *
 * @author juan.pinzon
 */
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/PrepareSms.php");
require_once(__DIR__ . "/../sender/ApisSms.php");

class SmsScript {

  public $mta;
  public $logger;
  public $arrayinfobitAnswerCharged;

  public function __construct() {
    $di = \Phalcon\DI\FactoryDefault::getDefault();
    $this->services = $di->get('services');
    $this->mta = $di->get('mta');
    $this->db = $di->get('db');
    $this->logger = $di->get('logger');
    $this->arrayinfobitAnswerCharged = \Phalcon\DI::getDefault()->get('infobitAnswersCharged')->toArray();
    $this->elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
    $this->elephant->initialize();
  }

  public function sendSms($adapter, $send, $sms) {        
    $from = "SIGMA-MOVIL";
    if($sms->international == 1 && !empty($sms->idcountry) ){
     $TFN = \Adxc::findFirst(["conditions" => "idCountry = ?0 AND idAdapter =?1 AND status = 1", "bind" => [0 =>(int) $sms->idcountry,1 =>(int)$adapter->idAdapter]]);
     //Obtenemos el Troll free number asignado por INFOBIP
     //El TFN es el codigo del $from 
     //Esto varia según el pais 
     $from = (string)$TFN->tollfreenumber;  
    }
    $apisms = new ApisSms(\Phalcon\DI::getDefault()->get('kannelProperties')->keyjwt);
    $fields = new stdClass();
    $fields->from =  $from;
    $fields->to = "{$send->indicative}{$send->phone}";
    $fields->text = $send->message;    
    $response = $apisms->apiInfobip($fields, $adapter);    
    unset($fields);
    return $response;
  }

  public function preparesmsKannel($send, $sms, $adapter) {
    $prepareSms = new PrepareSms();
    $prepareSms->setAdapter($adapter);
    $prepareSms->setPrefix($send->code);
    $prepareSms->setMovil($send->phone);
    $prepareSms->setText($send->message);
    $prepareSms->setSms($sms);

    return $prepareSms->startSend();
  }

  public function speedSms($amount) {
    return 60 / $amount;
  }

  public function recalculateSaxsBySms($idSubaccount, $totaluserresponse) {
    $count = Smsxc::count(array(
                "conditions" => array(
                    "idSubaccount" => (string) $idSubaccount,
                    "status" => "sent"
                )
    ));

    $cero = 0;

    $sql = "CALL updateCountersSmstwowaySaxs({$idSubaccount},{$cero},{$totaluserresponse})";
    $this->db->execute($sql);
  }

  public function saveSms($smsTwoWay) {
    if (!$smsTwoWay->save()) {
      foreach ($smsTwoWay->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $this->sendMessageNode($smsTwoWay);
  }

  public function getAdapter($id) {
    return \Adapter::findFirst(array("conditions" => "idAdapter = ?0", "bind" => array($id)));
  }

  public function sendMessageNode($smsTwoWay) {
    $this->elephant->emit('communication-php-node', ['callback' => "refresh-view-sms-two-way", "data" => ["idSmsTwoway" => $smsTwoWay->idSmsTwoway, "status" => $smsTwoWay->status]]);
  }

  public function closeNode() {
    $this->elephant->close();
  }

}
