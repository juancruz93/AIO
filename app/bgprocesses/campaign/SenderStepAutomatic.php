<?php

use \Sigmamovil\General\Misc\AutomaticCampaignObj;

require_once __DIR__ . '/../../../public/library/php-jwt-master/src/JWT.php';
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/http_load.php");
require_once(__DIR__ . "/../linkservice/LinkServiceAutomatization.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/AttachmentObject.php");
require_once(__DIR__ . "/../sender/InterpreterTarget.php");
require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerAutomatization.php");
require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");
require_once(__DIR__ . "/../../logic/PlainText.php");
require_once(__DIR__ . "/../sender/CustomfieldManagerSms.php");



$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$mailSender = new SenderStepAutomatic();

$mailSender->startSender($id);

class SenderStepAutomatic {

  public $urlManager,
          $mta,
          $url,
          $adapter,
          $configurationObj,
          $automaticCampaignObj,
          $automaticCampaignStep,
          $automaticCampaignConfiguration,
          $asset,
          $arrayinfobitAnswerChaged;

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->mta = \Phalcon\DI\FactoryDefault::getDefault()->get("mtadata");
    $this->asset = \Phalcon\DI\FactoryDefault::getDefault()->get("asset");
    $this->urlManager = $di->get('urlManager');
    $this->db = $di->get('db');
    $this->assetsrv = $di['asset'];
    $this->path = $di['path'];
    $this->kannelProperties = \Phalcon\DI::getDefault()->get('kannelProperties');
    $this->arrayinfobitAnswerChaged = \Phalcon\DI::getDefault()->get('infobitAnswersCharged')->toArray();
  }

  public function getNodeFromItemArray($idItem) {
    return $this->configurationObj->nodes[$idItem];
  }

  public function startSender($idAutomaticCampaignStep) {
    try {
      $automaticCampaignStep = \AutomaticCampaignStep::findFirst(["conditions" => "idAutomaticCampaignStep = ?0", "bind" => [0 => $idAutomaticCampaignStep]]);

      /*
       * Validaciones del envio 
       */
      if (!$automaticCampaignStep) {
        throw new InvalidArgumentException('El automaticCampaignStep enviado no existe');
      }
      
      if (isset($automaticCampaignStep->idSms)) {                            
        $sms = \Sms::findFirst(array(
                  "conditions" => "idSms = ?0 ",
                  "bind" => array(0 => $automaticCampaignStep->idSms)
        ));
        $automaticCampaignStep->status = $sms->status; 
        $sms->target = $sms->total;
        $this->save($automaticCampaignStep);
        $this->save($sms);
        unset($sms);

     } else if (isset($automaticCampaignStep->idMail)) {
        $mail = \Mail::findFirst(array(
                "conditions" => "idMail = ?0 ",
                "bind" => array(0 => $automaticCampaignStep->idMail)
        ));
        $automaticCampaignStep->status = $mail->status; 
        $this->save($automaticCampaignStep);
        unset($mail);
        
     }  

     $automaticCampaignObj = new AutomaticCampaignObj($automaticCampaignStep->AutomaticCampaign, $automaticCampaignStep->AutomaticCampaign->AutomaticCampaignConfiguration);
     $connection = $automaticCampaignObj->searchConnection($automaticCampaignStep->idNode);
    
     if(!isset($connection["dest"]) || empty($connection["dest"])){
	   $this->finishCampaign($automaticCampaignStep->AutomaticCampaign);	
     }   	
     
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("InvalidArgumentException SenderStepAutomatic: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $exc) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception SenderStepAutomatic: " . $exc->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($exc->getTrace());
    }
  }

 
 
  public function save($obj) {
    if (!$obj->save()) {
      foreach ($obj->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }
  
  public function finishCampaign($AutomaticCampaign){
      
      $sql = "SELECT acs.idAutomaticCampaignStep, acs.scheduleDate AS 'SCHEDULEDATE', m.`status` as 'STATUS', CONCAT(m.idMail, '-mail') as 'ID'"
                ." from automatic_campaign_step as acs left join mail as m on m.idMail = acs.idMail"
                ." where acs.idAutomaticCampaign = {$AutomaticCampaign->idAutomaticCampaign}"
                ." and acs.idSms is null and acs.idMail is not null UNION ALL "
            ."SELECT acs.idAutomaticCampaignStep, acs.scheduleDate AS 'SCHEDULEDATE',s.`status` as 'STATUS', CONCAT(s.idSms, '-sms') as 'ID'"
                ." from automatic_campaign_step as acs left join sms as s on s.idSms = acs.idSms"
                ." where acs.idAutomaticCampaign = {$AutomaticCampaign->idAutomaticCampaign}"
                ." and acs.idMail is null and acs.idSms is not null";

      $result = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
      $flag = false;

      if(!empty($result)){
        
        foreach($result as $value => $key){
             // \Phalcon\DI::getDefault()->get('logger')->log("*******************".$key['ID']."--".$key['STATUS']);
             
            if($key['STATUS'] == 'sent' && !empty($key['STATUS'])){               
                $flag = true;  
            }else if($key['STATUS'] == 'canceled' && !empty($key['STATUS'])){
                
                list($id, $servicio) = explode('-',$key['ID']);
                                
                if($servicio == 'mail' ){
                    
                    $mail = \Mail::findFirst(array(
                        "conditions" => "idMail = ?0 ",
                        "bind" => array(0 => $id)
                    ));
                    
                    if($mail->quantitytarget == 0 && $mail->messagesSent == 0){
                        $flag = true;   
                    }else{
                        $flag = false;
                        break;
                    }
                    
                }else{
                    
                    $sms = \Sms::findFirst(array(
                        "conditions" => "idSms = ?0 ",
                        "bind" => array(0 => $id)
                    ));
                    
                    if($sms->target == 0 && $sms->sent == 0){
                        $flag = true;   
                    }else{
                        $flag = false;
                        break;
                    }
                    
                }                
            }else{
                $flag = false;  
                break;
            }      
        } 
        
      }
      
      if($flag == true){
         $ac = \AutomaticCampaign::findFirst(["conditions" => "idAutomaticCampaign = ?0", "bind" => [0 => $AutomaticCampaign->idAutomaticCampaign]]); 
         $ac->status = 'completed';
         $ac->updated = time();
         $ac->endDate = date('Y-m-d H:i'); 
         $this->save($ac);         
      } 
      
      
      
  }
  
  

}
