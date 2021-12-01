<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/LinkService.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/AttachmentObject.php");
require_once(__DIR__ . "/../sender/InterpreterTarget.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/CustomfieldManager.php");
require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");



$ChangeStatusSmsr = new ChangeStatusSmsTwoway();

$ChangeStatusSmsr->changesms($argv);

class ChangeStatusSmsTwoway {

  protected $smstwoway;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->mta = \Phalcon\DI\FactoryDefault::getDefault()->get("mtadata");
    $this->urlManager = $di->get('urlManager');
    $this->db = $di->get('db');
    $this->assetsrv = $di['asset'];
    $this->path = $di['path'];
    $this->services = $di->get('services');
  }

  public function changesms($data) {
    \Phalcon\DI::getDefault()->get('logger')->log("hola aqui peerrrillas");
    $smstwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0",
                "bind" => array($data[1])
    ));

    if (!$smstwoway) {
      throw new \InvalidArgumentException("No se encontro el idSmsTwoway verifique la informacion.");
    }
     $status;
     
    if ($data[2] == 'pause') {
      $status = 'paused';
    } else if ($data[2] == 'canceled'){
      $status = 'canceled';
    }else{
      $status = 'scheduled';
    }
    $smstwoway->status = $status;

    if (!$smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    
//    $data["idSms"] = $smstwoway->idSmsTwoway;
//    $data["status"] = $smstwoway->status;
//    
//    socket.emit('refresh-view-sms-two-way',data);
    
    
  }

}
