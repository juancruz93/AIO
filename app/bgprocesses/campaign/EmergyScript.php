<?php

ini_set("memory_limit","512");
require_once(__DIR__ . "/../bootstrap/index.php");
require_once("SenderAutomatization.php");


$emergyscript = new EmergyScript();
$emergyscript->execute();

class EmergyScript{
  
  protected $flag,$offSet,$limit;
  
  public function __construct() {
    $this->flag = true;
    $this->limit = 1000;
  }
  
  public function execute(){
    while($this->flag){
//      $sql = "SELECT idAutomaticCampaignStep FROM next_step"
//           . " LIMIT {$this->limit}";
      $sql = "SELECT idAutomaticCampaignStep FROM `automatic_campaign_step` where status = 'paused' and idAutomaticCampaign = 110 limit {$this->limit}";
      $nextStep = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
      var_dump($nextStep);
      exit();
      unset($sql);
      if(count($nextStep)<$this->limit){
        $this->flag = false;
      }
      
//      foreach ($nextStep as $key){
//        $senderAutomatization = new SenderAutomatization();
//        $senderAutomatization->startSender($key["idAutomaticCampaignStep"]);
//      }	
      unset($nextStep);
    }
  }
}

