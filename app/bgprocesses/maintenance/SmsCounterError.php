<?php

require_once(__DIR__ . "/../bootstrap/index.php");
$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}
$SmsCounterError = new SmsCounterError();
$SmsCounterError->CorrectionSms($id);

class SmsCounterError {

  public function CorrectionSms($id) {
    try {
           $sms = \Sms::find(array(
           					  "columns"=>"idSms,sent",
                              "conditions" => "idSubaccount= ?0 AND startdate BETWEEN ?1 AND ?2 AND status ='sent'",
                              "bind" => array(0 => $id, 1 => '2017-12-01 00:00', 2 => '2017-12-14 12:00')
                  ))->toArray();


      if (count($sms) > 0) {
      	foreach ($sms as $key => $value) {
          var_dump($value);
      		exit;

      	$contion = array(
             'conditions' => array(
                'idSms' => (String)563021
            ),
            'fields' => array(
                'totalSendDB' => 1,
                'totalSmsDB' => 1
        ));
        $queryEmailContact = \LogSmsSend::find($contion);

        var_dump($queryEmailContact);
      		exit;
      		// $queryEmailContact = \LogSmsSend::find([["idSms"=>(string)563021],["$project"=>['totalSendDB'=>1,'totalSmsDB'=>1]]]);
      	}
      }     
    } catch (Exception $exc) {
      \Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Error procesando los correos repetidos total procesados" . $contador . "id de Contacto ::" . $idContactoSet);
      \Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Error procesando los correos repetidos" . $exc);
    }
  }

}
