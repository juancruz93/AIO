<?php
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
  public $batch;
  public $apisms;
  public $objectBase;
  public $db;
  public $services;
  public $totalSms;
  public $lastSmsSend;
  public $typeSend;
  private $statusSms;
  private $sms;
  public $arrayinfobitAnswerCharged;
  
  const ROWS_LIMIT = 1;
  const ROWS_OFFSET = 0;

  public function __construct() {
    $di = \Phalcon\DI\FactoryDefault::getDefault();
    $this->services = $di->get('services');
    $this->mta = $di->get('mta');
    $this->db = $di->get('db');
    $this->logger = $di->get('logger');
    $this->statusSms = \Phalcon\DI::getDefault()->get('statusSms');
    $this->arrayinfobitAnswerCharged = \Phalcon\DI::getDefault()->get('infobitAnswersCharged')->toArray();
    $this->constructObjectBase();
  }

  public function getStatusSms() {
    return $this->statusSms;
  }

  public function getSms() {
    return $this->sms;
  }

  public function setSms($sms) {
    $this->sms = $sms;
  }
  
  public function sendSms($adapter, $send, $sms) {
    if ($adapter->international == 0) {
      return $this->prepareSmsKannel($send, $sms, $adapter);
    } elseif ($adapter->international == 1) {
      $this->constructMessage($send, $adapter->prefix, $sms);
      if (count($this->objectBase->messages) == 100) {
        $this->objectBase->bulkId .= $sms->idSms;
        $response = $this->prepareSmsInfobip($this->objectBase, $adapter);
        $this->objectBase->bulkId = "AIO-SIGMA-SMS-";
        $this->objectBase->messages = [];
        /*$this->objectBase->tracking = [
          "track" => "URL",
          "type"  => "SOCIAL_INVITES"
        ];*/
        return $this->updateStatusSends($response, $adapter->idAdapter);
      }
      return "continue";
    }
  }

  public function prepareSmsKannel($send, $sms, $adapter) {
    $prepareSms = new PrepareSms();
    $prepareSms->setAdapter($adapter);
    $prepareSms->setPrefix($send->indicative);
    $prepareSms->setMovil($send->phone);
    $prepareSms->setText($send->message);
    $prepareSms->setSms($sms);

    return $prepareSms->startSend();
  }

  public function prepareSmsInfobip($batch, $adapter) {
    $this->apisms = new ApisSms(\Phalcon\DI::getDefault()->get('kannelProperties')->keyjwt);
    $response = $this->apisms->apiInfobip($batch, $adapter);
    return $response->messages;
  }

  public function constructObjectBase() {
    $this->objectBase = new stdClass();
    $this->objectBase->bulkId = "AIO-SIGMA-SMS-";
    $this->objectBase->messages = [];
    /*$this->objectBase->tracking = [
      "track" => "URL",
      "type"  => "SOCIAL_INVITES"
    ];*/
  }

  public function constructMessage($send, $type, $sms) {
    (($this->getTypeSend() == "normal") ? $idSend = $send->idSmslote : $idSend = (string) $send->getId());
    if($send->phone != '3014039060'){
      $message = array(
        "from" => "SIGMA-MOVIL",
        "destinations" => array(
            ["to" => "{$send->indicative}{$send->phone}"]
        ),
        "text" => $send->message,
        "flash" => $sms->sendpush != 0 ? TRUE : FALSE,
        "callbackData" => "AIO-{$idSend}",
        "intermediateReport" => false,
        "notifyUrl" => "",
        "notifyContentType" => "",
        "validityPeriod" => 720
      );
    }

    (($type == "single") ? $message["destinations"][0]["messageId"] = $idSend : true);

    array_push($this->objectBase->messages, $message);
  }

  public function updateStatusSends($response, $idAdapter) {
    $amountSent = 0;
    $amountNotSent = 0;
    if ($this->getTypeSend() == "normal") {
      foreach ($response as $res) {
        if(!in_array($res->status->name, $this->arrayinfobitAnswerCharged)){
          $sql = "UPDATE smslote "
            . "SET idAdapter = {$idAdapter}, "
            . "status = 'undelivered', "
            . "response = '{$res->status->name}', "
            . "updatedBy = 'desarrollo@sigmamovil.com', "
            . "updated = " . time() . ", "
            . "messageCount = 0 "
            . "WHERE idSmslote = {$res->messageId}";
        }else{
          $sql = "UPDATE smslote "
            . "SET idAdapter = {$idAdapter}, "
            . "status = 'sent', "
            . "response = '{$res->status->name}', "
            . "updatedBy = 'desarrollo@sigmamovil.com', "
            . "updated = " . time() . ", "
            . "messageCount = IF(CHAR_LENGTH(message) <= 160,1,2) "
            . "WHERE idSmslote = {$res->messageId}";
        }
        $executeQuery = $this->db->query($sql);
//        (($executeQuery->numRows() > 0) ? ($res->status->name == "PENDING_ENROUTE") ? $amountSent++ : $amountNotSent++ : true);
        (($executeQuery->numRows() > 0) ? (in_array($res->status->name, $this->arrayinfobitAnswerCharged)) ? $amountSent++ : $amountNotSent++ : true);
      }
    } else {
      foreach ($response as $res) {
        if(!in_array($res->status->name, $this->arrayinfobitAnswerCharged)){
          $sendStatus = "undelivered";
        }else{
          $sendStatus = "sent";
        }
        $smsxc = Smsxc::findById($res->messageId);
        $smsxc->idAdapter = (string) $idAdapter;
        if($sendStatus == "undelivered"){
         $smsxc->messageCount = 0;   
        }
        $smsxc->status = /*(string) \Phalcon\DI::getDefault()->get('statusSms')->sent*/ $sendStatus;
        $smsxc->response = (string) $res->status->name;
        $smsxc->updatedBy = "desarrollo@sigmamovil.com";
        $smsxc->updated = (int) time();

        $smsxc->save();

//        (($res->status->name == "PENDING_ENROUTE") ? $amountSent++ : $amountNotSent++);
        ((in_array($res->status->name, $this->arrayinfobitAnswerCharged)) ? $amountSent++ : $amountNotSent++);
      }
    }

    return ["amountsent" => $amountSent, "amountnotsent" => $amountNotSent];
  }

  public function speedSms($amount) {
    return 60 / $amount;
  }

  public function recalculateSaxsBySms($idSubaccount) {
    //$user = \Phalcon\DI::getDefault()->get('user');
    /*$count = Smsxc::count(array(
                "conditions" => array(
                    "idSubaccount" => (string) $idSubaccount,
                    "status" => "sent"
                )
    )); */
    //$array = ['28','148','420','1367','1406'];

    $subaccount = Subaccount::findFirst(array(
        'conditions' => 'idSubaccount = ?1',
        'bind' => array(1 => (int) $idSubaccount)
    ));

    if (!$subaccount) {
      $this->notification->error("La subcuenta que intenta modificar no existe, por favor verifique la información");
    }

    if($subaccount->Account->registerType == "form" || $subaccount->Account->registerType == "facebook" || $subaccount->Account->registerType == "google"){
      $initial = date('Y-m-01')." 00:00:00";
      $end  = date('Y-m-t')." 23:59:59";
      $startDate = " AND type='contact' AND startdate BETWEEN '{$initial}' AND '{$end}'";
      $conditions = array(
          "conditions" => "idSubaccount = ?0 {$startDate}",
          "bind" => array((int) $idSubaccount),
          "order" => "idSms DESC"
      );

      $sms = \Sms::find($conditions);
      $count = 0;
      $idSmsArray = [];
      if(count($sms) > 0){
        foreach ($sms as $value) {
          $idSmsArray[] = (string) $value->idSms;
        }
        $collectionSmsXc = [[ '$match' => ['idSubaccount' => (string) $idSubaccount,'idSms' => ['$in' => $idSmsArray]]],[ '$group' => ['_id' => '$idSubaccount', 'messageCount' => ['$sum' => '$messageCount']]]];
        $count1 = \Smsxc::aggregate($collectionSmsXc);
        
        if(isset($count1['result'][0]['messageCount'])){
          $count = $count1['result'][0]['messageCount'];
        }
        unset($count1);    
      }
      $sql = "CALL updateCountersSmsSaxs({$idSubaccount},{$count})";                                                                     
      $this->db->execute($sql);

    }else{
      $inSms = Sms::count(array(
      "conditions" => array(
        "idSubaccount" => (int) $idSubaccount,
        "status"  => "sent",
        "logicodeleted" => 0,
        "type" => "contact"
        ),
        "colums" => "idSms"
      ));
      $count = 0;
      if($inSms > 0){
        $collectionSmsXc = [[ '$match' => ['idSubaccount' => (string) $idSubaccount] ],[ '$group' => ['_id' => '$idSubaccount', 'messageCount' => ['$sum' => '$messageCount']]]];
        $count1 = \Smsxc::aggregate($collectionSmsXc);
        
        if(isset($count1['result'][0]['messageCount'])){
          $count = $count1['result'][0]['messageCount'];
        }
        unset($count1);    
      }
      $sql = "CALL updateCountersSmsSaxs({$idSubaccount},{$count})";                                                                     
      $this->db->execute($sql);
    }

  }

  public function saveSms($sms) {
    if (!$sms->save()) {
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  function getLastSmsSend() {
    return $this->lastSmsSend;
  }

  function setLastSmsSend($lastSmsSend) {
    $this->lastSmsSend = $lastSmsSend;
  }

  function getTypeSend() {
    return $this->typeSend;
  }

  function setTypeSend($typeSend) {
    $this->typeSend = $typeSend;
  }

}
