<?php


require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../smstwoway/SmsScript.php");
require_once(__DIR__ . "/../sender/PrepareSmsSendingRule.php");

class SmsSender extends SmsScript {
	 public function __construct() {
        parent::__construct();
    }
	
	public function verifyExist($id) {
        try {
            $smsTwoWay = \Smstwoway::findFirst(array(
                        "conditions" => "idSmsTwoway = ?0",
                        "bind" => array(0 => $id)
            ));
            if (!$smsTwoWay) {
                return "El sms doble via que intenta de enviar no se encuentra registrado, por favor validar.";
            }
			if (($smsTwoWay->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) or ($smsTwoWay->status == \Phalcon\DI::getDefault()->get('statusSms')->paused)) {

                $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->sending;
                $this->saveSms($smsTwoWay);
				
				$this->sendMessageNode($smsTwoWay);

                
                $smsLoteTwoway = Smslotetwoway::find(array(
                            "conditions" => "idSmsTwoway = ?0",
                            "bind" => array($smsTwoWay->idSmsTwoway)
                ));
				
				foreach ($smsTwoWay->Subaccount->Account->accountConfig->detailConfig as $key) {
                    if ($key->idServices == 7) {
                        $detailConfig = $key;
                        $speed = $this->speedSms($key->speed);
                    }
                }

                foreach ($smsTwoWay->Subaccount->Saxs as $key) {
                    if ($key->idServices == 7) {
                        $saxs = $key;
                    }
                }
				
				$count = 0;
                $total = 0;
                
                if ($smsTwoWay->divide == 0) {
                    foreach ($smsLoteTwoway as $send) {
                        if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
                            if ($count == $saxs->amount) {
                                \Phalcon\DI::getDefault()->get('logger')->log("No tienes envios suficientes");
                                $this->sendMailNotificationFailure($smsTwoWay);
                                return "No tienes envios suficientes";
                            }
                            //echo "Hola zoquetes";
                            $response = $this->sendSms($this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way), $send, $smsTwoWay);
                           
                            $send->response = $response->messages[0]->status->name;
                            $send->status = "undelivered";
                            if ($send->response == "PENDING_ENROUTE") {
                                $send->messageId = $response->messages[0]->messageId;
                                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                                $send->idAdapter = $this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way)->idAdapter;
                                $send->updatedBy = "desarrollo@sigmamovil.com";
                                //var_dump($response->messages[0]->messageId);
                                $send->messageId = $response->messages[0]->messageId;
                                $count++;
                            }
                            $send->save();
                        }
                        $total++;
                    }
                    $smsTwoWay->sent = $count;
                    $smsTwoWay->total = $total;
                } else if ($smsTwoWay->divide == 1) {
                    $quantity = 0;
                    foreach ($smsLoteTwoway as $send) {
                        if ($send->status == \Phalcon\DI::getDefault()->get('statusSms')->scheduled) {
                            if ($count == $saxs->amount) {
                                \Phalcon\DI::getDefault()->get('logger')->log("No tienes envios suficientes");
                                $this->sendMailNotificationFailure($sms);
                                return "No tienes envios suficientes";
                            }

                            $response = $this->sendSms($this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way), $send, $smsTwoWay);
                            $send->response = $response->messages[0]->status->name;
                            $send->status = "undelivered";
                            if ($send->response == "PENDING_ENROUTE") {
                                $send->messageId = $response->messages[0]->messageId;
                                $send->status = \Phalcon\DI::getDefault()->get('statusSms')->sent;
                                $send->idAdapter = $this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way)->idAdapter;
                                $send->messageId = $response->messages[0]->messageId;
                                var_dump($response);
                                $send->updatedBy = "desarrollo@sigmamovil.com";
                                
                                
                                $count++;
                            }
                            $send->save();
                            $quantity++;
                            $total++;
                            if ($quantity == $smsTwoWay->quantity) {
                                break;
                            }
                        }
                    }
                    $smsTwoWay->sent += $count;
                    $smsTwoWay->total += $total;
                }
				
				$smsLoteTwoway = Smslotetwoway::find(array(
                            'columns' => 'messageId',
                            'bind' => array($smsTwoWay->messageId)
                ));
				
				if( isset(\Phalcon\DI::getDefault()->get('messageId')) && \Phalcon\DI::getDefault()->get('messageId') != "" ){
                  $idTemp = $this->sendSms($this->getAdapter(\Phalcon\DI::getDefault()->get('adapters')->sms_two_way), $send, $smsTwoWay);
                  $send->response = $response->messages[0]->status->name;
                }
                  
            } 
           
        } catch (\ValidateExistException $ex) {
            $this->registLog($ex);
        } catch (\InvalidArgumentException $ex) {
            $this->recalculateSaxsBySms($smsTwoWay->idSubaccount);
            $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
            $this->saveSms($smsTwoWay);
            $this->sendMessageNode($smsTwoWay);
            $this->registLog($ex);
        } catch (Exception $ex) {
            $this->recalculateSaxsBySms($smsTwoWay->idSubaccount);
            $smsTwoWay->status = \Phalcon\DI::getDefault()->get('statusSms')->canceled;
            $this->saveSms($smsTwoWay);
            $this->sendMessageNode($smsTwoWay);
            $this->registLog($ex);
        }
    }
}

var_dump("hola");
exit();
$id = 0;
if (isset($argv[1])) {
    $id = $argv[1];
}

$sender = new SmsSender();

$sender->verifyExist($id);