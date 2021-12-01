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



$ChangeStatusSmsr = new ChangeStatusSms();

$ChangeStatusSmsr->changesms($argv);

class ChangeStatusSms {

  protected $sms;

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
    $sms = \Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array($data[1])
    ));

    if (!$sms) {
      throw new \InvalidArgumentException("No se encontro el idSms verifique la informacion.");
    }

    $status;
    if ($data[2] == 'pause') {
      $status = 'paused';
    } else {
      $status = 'canceled';
    }

    $sms->status = $status;

    if (!$sms->save()) {
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

}
