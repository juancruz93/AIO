<?php

/*
 * Este objecto sirve para re calcular los sms enviados y que fueron cancelados por medio de NODE 
 */
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/PrepareSms.php");
require_once(__DIR__ . "/../sender/InterpreterTargetSms.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerSms.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sender = new CountSentSmsTwoway();
$sender->verifyExist($id);

class CountSentSmsTwoway {

  protected $smstwoway;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->services = $di->get('services');
    $this->mta = $di->get('mta');
    $this->db = $di->get('db');
  }

  public function verifyExist($id) {
    try {
      \Phalcon\DI::getDefault()->get('logger')->log("verificacion ");
      $this->smstwoway = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0", "bind" => array($id)));

      if (!$this->smstwoway) {
        throw new \InvalidArgumentException("El smstwoway con el id {$id} no se encuestra registrado, por favor validar.");
      }
      $smsLoteTwoway = Smslotetwoway::find(array(
                  "conditions" => "idSmsTwoway = ?0",
                  "bind" => array($this->smstwoway->idSmsTwoway)
      ));
      $this->recalculateSaxsBySms($this->smstwoway->idSubaccount,$smsLoteTwoway->totalUserResponse);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error[CountSentSms - verifyExist]: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error[CountSentSms - verifyExist]: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
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

}
