<?php

use Phalcon\Logger\Adapter\File as FileAdapter;

class UnsubscribeController extends ControllerBase{
  
  public function contactAction($parameters){
    
    $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
    $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
    list($idLink, $idMail, $idContact) = $this->encoder->decodeLink('unsubscribe/contact', $parameters);
     //Logs de ContactAction
//        $logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
        $customLogger = new \TrackLog();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->idMail = $idMail;
        $customLogger->idContact = $idContact;
        $customLogger->idLink = $idLink;
        $customLogger->typeName = "ContactActionMethod";
        $customLogger->globalDescription = $parameters;
//        $customLogger->globalLogDescription = $logger->log("Parameters on ContactAction: {$parameters}");
//        $customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
//        $customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
    //
    $validateView = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);
    
    $this->view->setVar('idMail',$idMail);
    $this->view->setVar('typeView',$validateView->typeUnsuscribed);
    $this->view->setVar('idContact',$idContact);
  }
  
  public function contactautomaticAction($parameters){
    
    $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
    $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
    list($idLink, $idMail, $idContact) = $this->encoder->decodeLink('unsubscribe/contactautomatic', $parameters);
    
    $this->view->setVar('idMail',$idMail);
    $this->view->setVar('idContact',$idContact);
  }
  
  public function listAction() {    
  }
  
  public function createAction() {    
  }   
}


