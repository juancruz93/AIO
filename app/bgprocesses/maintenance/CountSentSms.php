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

$sender = new CountSentSms();
$sender->verifyExist($id);

class CountSentSms {

  protected $sms;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->services = $di->get('services');
    $this->mta = $di->get('mta');
    $this->db = $di->get('db');
  }

  public function verifyExist($id) {
    try {
      $this->sms = \Sms::findFirst(array("conditions" => "idSms = ?0", "bind" => array($id)));
      if (!$this->sms) {
        throw new \InvalidArgumentException("El sms con el id {$id} no se encuestra registrado, por favor validar.");
      }
      $this->recalculateSaxsBySms($this->sms->idSubaccount);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error[CountSentSms - verifyExist]: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error[CountSentSms - verifyExist]: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  public function recalculateSaxsBySms($idSubaccount) {
    $count = Smsxc::count(array(
                "conditions" => array(
                    "idSubaccount" => (string) $idSubaccount,
                    "status" => "sent"
                )
    ));
    $sql = "CALL updateCountersSmsSaxs({$idSubaccount},{$count})";
    $this->db->execute($sql);
  }

}
