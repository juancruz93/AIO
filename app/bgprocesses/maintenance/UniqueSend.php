<?php
require_once(__DIR__ . "/../bootstrap/index.php");

$uniquesend  = new UniqueSend();
$uniquesend->functionName();


class UniqueSend {

  public function functionName() {



    $sms = \Sms::find(array(
                  "columns"=>"idSms",
                  "conditions" => "idSubaccount =  ?0 and status = ?1 and startdate > ?2",
                  "bind" => array(0 => 597, 1 => 'paused', 2 => '2017-11-14 00:00')
      ))->toArray();
    foreach ($sms as $key => $value) {
		var_dump($value["idSms"]);
		exec("php /websites/aio/app/bgprocesses/sms/SmsSender.php {$value["idSms"]}");
     }

  }
}