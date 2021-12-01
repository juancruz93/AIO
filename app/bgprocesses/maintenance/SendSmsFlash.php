<?php
require_once(__DIR__ . "/../bootstrap/index.php");

$sendSmsFlash  = new SendSmsFlash();
$sendSmsFlash->start();


class SendSmsFlash {
  
  protected $db;
  
  public function __construct(){
	$this->db = \Phalcon\DI::getDefault()->get('db');
  }
  public function start() {
	$arrFinishArray = "";
	$idSmsFinish = "";
    try{
		$this->db->begin();
		
		$sms = \Sms::find(array(
                  "columns"=>"idSms",
                  "conditions" => "idSubaccount =  ?0 and status = ?1 and startdate > ?2",
                  "bind" => array(0 => 597, 1 => 'scheduled', 2 => '2017-11-14 00:00')
		))->toArray();
		if(count($sms) <= 0){
			exit();
		}
		$idSmsFinish = intval($sms[0]["idSms"]);
		foreach ($sms as $key => $value) {
			$arrFinishArray .= intval($value["idSms"]).",";
		}
		
		$arrFinishArray = substr ($arrFinishArray, 0, strlen($arrFinishArray) - 1);
		var_dump($idSmsFinish);
		$sql = "UPDATE sms SET status='paused' WHERE idSms in({$arrFinishArray});";
		var_dump($sql);
		$this->db->execute($sql);
		
		$sql1 = "UPDATE smslote SET idSms={$idSmsFinish} WHERE idSms IN({$arrFinishArray});";
		var_dump($sql1);
		$this->db->execute($sql1);
		
		
		
		$sql2 = "UPDATE sms SET status='sent' WHERE idSms in({$arrFinishArray}) and idSms <> {$idSmsFinish};";
		var_dump($sql2);
		$this->db->execute($sql2);
		
		$this->db->commit();
		
		exec("php /websites/aio/app/bgprocesses/sms/SmsSender.php {$idSmsFinish}");
	}catch(Exception $e){
		$this->db->rollback();
	}
    

  }
}