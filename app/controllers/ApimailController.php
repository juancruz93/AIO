<?php

/**
 * @RoutePrefix("/api/mail")
 */
class ApimailController extends ControllerBase {

  /**
   *
   * @Post("/newmail")
   */
  public function newmailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);  
        if (!empty($data)) {
        
        if(!isset($data['mail']['externalApi']) || empty($data['mail']['externalApi'])){
            throw new \InvalidArgumentException("El campo externalApi no esta definido");            
        }else{
            $this->validateExternalApi($data);
        }
        //verificar esta validacion
        $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
        //$mailWrapper->createNewMail($data);
        $this->trace("success", "Se ha creado el envío de mail");
        return $this->set_json_response(array('mail' => $mailWrapper->createNewMail($data), "message" => "Se ha creado el envío correctamente"), 200);
      } else {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  public function validateExternalApi($data) {

        $detailConfig = \DetailConfig::findFirst(["conditions" => "idAccountConfig = ?0 and idServices = 2",
                       "bind" => array(0 => (int)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->Account->AccountConfig->idAccountConfig)]);
        if($detailConfig->status == 0){
           throw new \InvalidArgumentException("El servicio de email de la cuenta se encuentra inactivo por favor comunicate con soporte");
        }
        $flag = false;
        foreach ($this->user->Usertype->subaccount->saxs as $key) {
            if ($key->idServices == 2 && $key->status ==1 ) {
                $flag = true;
            }
        }
        if ($flag == false) {
            throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
        }
       
        if (!empty($data['mail']["replyto"]) && !filter_var($data['mail']["replyto"], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Formato de correo invalido");
        }

        if (isset($data['mail']['name'])) {
            $mail = \Mail::findFirst(["conditions" => "name = ?0 and idSubaccount = ?1", "bind" => array($data['mail']['name'], $this->user->UserType->idSubaccount)]);
            if ($mail) {
                throw new \InvalidArgumentException("El nombre de la campaña ya se encuentra registrado.");
            }
        }
        
        if(!isset($data['mail']['singleMail']) || empty($data['mail']['singleMail'])){
            throw new \InvalidArgumentException("El campo singlemail no esta definido");    
        }
        
        if (isset($data['mail']['category'])) {
            $validatecategory = \MailCategory::findFirst(array(
                        "conditions" => "idAccount = ?0 and idMailCategory =?1 and deleted = 0",
                        "bind" => array(0 => (int) \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount, 1 => $data['mail']['category'][0])
            )); 
            
            if (empty($data['mail']['category'][0])) {
                throw new \InvalidArgumentException("El id de categoría esta vacio");
            }
            if(!$validatecategory || empty($validatecategory)){
               throw new \InvalidArgumentException("El id de categoría no pertenece a la cuenta");                 
            }
        }

        if (!empty($data['mail']['target'])) {

            $contactlistinvalid = array();
            $contactlistsincontactos = array();
            if ($data['mail']['target']['type'] == 'contactlist') {
                
                if(!isset($data['mail']['target']['contactlists'])){
                    throw new \InvalidArgumentException("La posición contactlists no esta definida");                    
                }
                
                foreach ($data['mail']['target']['contactlists'] as $value) {
                    
                    if(!is_array($value)){
                       throw new InvalidArgumentException("Las posiciones del contactlists deben ser de tipo array"); 
                    }

                    if (empty($value['idContactlist'])) {
                        throw new InvalidArgumentException("No ha enviado ninguna un idContactlist, por favor valide la información");
                    }

                    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => (int) $value['idContactlist'])));

                    if (empty($contactlist)) {
                        throw new InvalidArgumentException("El idContactlist {$value['idContactlist']} no esta registrado en la plataforma, por favor valide la información");
                    }

                    if ($value['name'] != $contactlist->name) {
                        throw new InvalidArgumentException("El nombre de la lista de contactos es invalido");
                    }

                    if ($contactlist->idSubaccount != \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idSubaccount) {
                        $contactlistinvalid[] = $contactlist->idContactlist;
                    }

                    if ($contactlist->cactive == 0 && $contactlist->ctotal == 0) {
                        $contactlistsincontactos[] = $contactlist->idContactlist;
                    } else if ($contactlist->cactive == 0 && $contactlist->ctotal <> 0) {
                        $contactlistsincontactos[] = $contactlist->idContactlist;
                    }
                }

                if (count($contactlistinvalid) > 0) {
                    $listId = implode(",", $contactlistinvalid);
                    throw new InvalidArgumentException("Los id {$listId} no pertenecen a la subcuenta");
                }

                if (count($contactlistsincontactos) == count($data['mail']['target']['contactlists'])) {
                    $listId = implode(",", $contactlistsincontactos);
                    throw new InvalidArgumentException("Los id {$listId} de lista de contactos no contienen contactos activos");
                }
            }else {
                if(!isset($data['mail']['target']['segment'])){
                    throw new \InvalidArgumentException("La posición segment no esta definida");                    
                }
                $segmentinvalid = array();
                $segmentsincontactos = array();
                foreach ($data['mail']['target']['segment'] as $value) {
                    
                    if (empty($value['idSegment'])) {
                        throw new InvalidArgumentException("No ha enviado ningún un idSegment, por favor valide la información");
                    }
                    
                    if(!is_numeric($value['idSegment'])){
                        throw new InvalidArgumentException("El idSegment debe ser de tipo númerico");
                    }
                    
                    $segment = \Segment::findFirst(array("conditions" => array("idSegment" => (int) $value['idSegment'])));
                    
                    if (empty($segment)) {
                        throw new InvalidArgumentException("El idSegment {$value['idSegment']} no esta registrado en la plataforma, por favor valide la información");
                    }
                                        
                    if ($value['name'] != $segment->name) {
                        throw new InvalidArgumentException("El nombre del segmento es invalido");
                    } 
                    
                    if ($segment->idSubaccount != \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idSubaccount) {
                        $segmentinvalid[] = $segment->idSegment;
                    }
                    
                    $sxctotal = \Sxc::count([["idSegment" => (int) $segment->idSegment, "deleted" => (int) 0]]);
                    
                    if($sxctotal == 0 || empty($sxctotal) || $sxctotal == null){
                        $segmentsincontactos[] = $segment->idSegment;
                    }else{
                        $sxcBlocked = \Sxc::count([["idSegment" => (int) $segment->idSegment, "deleted" => (int) 0,"blocked" => ['$nin' => ["", null, "null",0,"0"]]]]);
                        $sxcunsubscribed = \Sxc::count([["idSegment" => (int) $segment->idSegment, "deleted" => (int) 0, "unsubscribed" => ['$nin' => ["", null, "null",0,"0"]]]]);
                        
                        if($sxcBlocked == $sxctotal){
                            $segmentsincontactos[] = $segment->idSegment;
                        }else if($sxcunsubscribed == $sxctotal){
                            $segmentsincontactos[] = $segment->idSegment;
                        }
                    }                    
                }
                
                if (count($segmentinvalid) > 0) {
                    $listId = implode(",", $segmentinvalid);
                    throw new InvalidArgumentException("Los id {$listId} no pertenecen a la subcuenta");
                }
                
                if (count($segmentsincontactos) == count($data['mail']['target']['segment'])) {
                    $listId = implode(",", $segmentsincontactos);
                    throw new InvalidArgumentException("Los id {$listId} de segmento no contienen contactos activos");
                }
            }
        }

        if (empty($data['mail']['gmt']) || !isset($data['mail']['gmt'])) {
            throw new InvalidArgumentException("No ha enviado ninguna zona horaria");
        } else if ($data['mail']['gmt'] != '-0500') {
            throw new InvalidArgumentException("Ha enviado una zona horaria invalida");
        }
    }

    /**
   *
   * @Put("/editmail/{idMail:[0-9]+}")
   */
  public function editmailAction($idMail) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      $mail = \Mail::findFirst(array(
                'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                'bind' => array(0 => $idMail, 1 =>(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
                ));

      if (!$mail) {
        throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo");
      }
      if (!empty($data)) {
        $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
        $this->trace("success", "Se ha editado el envío de mail");

        return $this->set_json_response(array('mail' => $mailWrapper->editMail($idMail, $data), "message" => "Se ha editado el envío correctamente"), 200);
      } else {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Route("/cancelmail/{idMail:[0-9]+}", methods="DELETE")
   */
  public function cancelmailAction($idMail) {
    try {
      $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
      $this->trace("success", "Se ha cancelado el envío de mail");

      return $this->set_json_response(array('message' => $mailWrapper->cancelMail($idMail)), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/detailmail/{idMail:[0-9]+}")
   */
  public function detailmailAction($idMail) {
    try {
      $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response(array('mail' => $mailWrapper->detailMail($idMail)), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/preview/{id:[0-9]+}")
   */
  public function previewmailtemplateAction($id) {
    $mailtemplatecontent = MailContent::findFirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array($id)
    ));
    //Editor , html

    if ($mailtemplatecontent->typecontent == 'Editor') {
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->setAccount(((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null)));
      $editorObj->assignContent(json_decode($mailtemplatecontent->content));
      $html = $editorObj->render();
    } else if ($mailtemplatecontent->typecontent == 'html' or $mailtemplatecontent->typecontent == 'url') {
      $html = $mailtemplatecontent->content;
    }

    $htmlObj = new DOMDocument();
    @$htmlObj->loadHTML($html);

    $links = $htmlObj->getElementsByTagName('a');

    /*if (count($links) > 0) {
      $html = str_replace(array("<a", "a>"), array("<label", "label>"), $html);
    }*/


    return $this->set_json_response(array('template' => $html));
  }

  /**
   * 
   * @Post("/getmailcontentjson/{id:[0-9]+}")
   */
  public function getmailcontentjsonAction($id) {
    $mailcontent = MailContent::findFirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array($id)
    ));
    //Editor , html

    if ($mailcontent->typecontent != 'Editor') {
      throw new InvalidArgumentException("El correo no fue creado en Editor Avanzado");
    }

    return $this->set_json_response(array('content' => $mailcontent->content));
  }

  /**
   * @Post("/getmdgpublish/")
   */
  public function getmdgpublishAction() {

    $dataJson = $this->request->getRawBody();
    $arraydata = json_decode($dataJson);

    $md5 = "";
    if ($arraydata->facebook) {
      $dataMail = $this->urlManager->get_base_uri(true) . "1-" . $arraydata->idMail . "-public-Sigmamovil_Rules";
      $md5 = md5($dataMail);
    } else {
      $dataMail = $this->urlManager->get_base_uri(true) . "1-" . $arraydata->idMail . "-" . $this->user->Usertype->idSubaccount . "-Sigmamovil_Rules";
      $md5 = md5($dataMail);
    }
    return $this->set_json_response(array('data' => $md5));
  }

  public function validateBalance(){
    $date = date('Y-m-d h:i:s');
    $mailFindPending = \Mail::find(array(
      'conditions' => 'idSubaccount = ?0 and status = ?1 and scheduleDate >= ?2',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 'scheduled',
        2 => $date
      ),
      'columns' => 'idMail, quantitytarget AS target'  
    ));

    $balanceConsumedFind = \Saxs::findFirst(array(
      'conditions' => 'idSubaccount = ?0 and idServices = ?1 and accountingMode = ?2 and status= ?3 ',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 2,
        2 => 'sending',
        3 => 1
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

    $answer = ['mailFindPending'=>$mailFindPending->toArray(), 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];

    return $answer;
  }
  public function validateBalanceMail(){
    $flagSending = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == "sending" && $key->status==1) {
        $flagSending = true;
      }
    }
    if($flagSending){
      //Se realiza validaciones de los emails programados
      $balance = $this->validateBalance();
      $target = 0;
      if($balance['mailFindPending']){
        foreach ($balance['mailFindPending'] as $value){
          $target = $target + $value['target'];
        }
      }
      $amount = $balance['balanceConsumedFind']['amount'];

      unset($balance);
      $totalTarget =  $amount - $target;
      $target = $target + $mail->quantitytarget;

      if($target>$amount){
        $target = $target - $amount;
        if($totalTarget<=0){
          $tAvailable = (object) ["totalAvailable" => 0];
        } else {
          $tAvailable = (object) ["totalAvailable" => $totalTarget];
        }
        $this->sendmailnotmailbalance($tAvailable);

        throw new \InvalidArgumentException("No tiene saldo disponible para realizar esta campaña!, su saldo disponlble es {$totalTarget} envios, ya que existen campañas programadas pendientes por enviar.");
      }
      unset($target);
      unset($amount);
      unset($totalTarget);
      unset($tAvailable);
    }
  }

  public function sendmailnotmailbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == 'sending' && $key->status ==1) {
        $amount = $data->totalAvailable;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->user->Usertype->Subaccount->name;
        $accountName = $this->user->Usertype->Subaccount->Account->name;
        $this->arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName
        );
      }
    }
    $sendMailNot= new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
    //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de email
    $sendMailNot->sendMailNotification($this->arraySaxs);
    return true;
  }
  
    public function sendemailfromApiAction($data1) {
    try {

      $data = json_decode($data1, true);  
      if (!empty($data)) {
        $this->validateExternalApi($data);
        //verificar esta validacion
        $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
        return $this->set_json_response(array('mail' => $mailWrapper->createNewMail($data), "message" => "Se ha creado el envío correctamente"), 200);
      } else {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

    /**
   *
   * @Get("/setstatusmail/{idMail:[0-9]+}/{status:[0-9]+}")
   */
  public function setstatusmailAction($idMail,$status) {
    try {
      $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response(array('message' => $mailWrapper->setstatusMail($idMail,$status)), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
      /**
   *
   * @Post("/newsinglemail")
   */
  public function newsinglemailAction() {
    try {
      $json = $this->getRequestContent();
      if (base64_decode($json, true)) {
        $json = base64_decode($json, true);
      }
      $data = json_decode($json, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $this->validateData($data);
      $mailWrapper = new \Sigmamovil\Wrapper\MailWrapper();
      return $this->set_json_response(array($mailWrapper->newMailSingle($data)), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
    public function validateData($data) {
        $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
        $detailConfig = \DetailConfig::findFirst(["conditions" => "idAccountConfig = ?0 and idServices = 2",
                       "bind" => array(0 => (int)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->Account->AccountConfig->idAccountConfig)]);
        if($detailConfig->status == 0){
           throw new \InvalidArgumentException("El servicio de email de la cuenta se encuentra inactivo por favor comunicate con soporte");
        }       
        $flag = false;
        $flagsending = false;
        foreach ($this->user->Usertype->subaccount->saxs as $key) {
            if ($key->idServices == 2 && $key->status == 1  ) {
                $flag = true;
            }
            if($key->accountingMode == 'sending'){
                $flagsending = true;   
            }
        }
        if($flag == false) {
            throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
        }
        if($flagsending == false){
            throw new \InvalidArgumentException("No tienes asignado el servicio por modalidad de envios");
        }
        if (!empty($data['mail']["replyto"]) && !filter_var($data['mail']["replyto"], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Formato de correo invalido");
        }
        if(!isset($data['mail']['to'])|| empty($data['mail']['to'])){
          throw new \InvalidArgumentException("El correo destinatario no está definido");  
        }
        //Cambiar validacion cuando sea más de dos destinatarios
        /*if (!empty($data['mail']['from']) && $this->validateMail($data['mail']['from'])) {
            
            throw new \InvalidArgumentException("Formato del correo destino no es valido");
        }*/
        if (isset($data['mail']['name'])) {
            $mail = \Mail::findFirst(["conditions" => "name = ?0 and idSubaccount = ?1", "bind" => array($data['mail']['name'], $this->user->UserType->idSubaccount)]);
            if ($mail) {
                throw new \InvalidArgumentException("El nombre de la campaña ya existe.");
            }
        }        
    
        if (isset($data['mail']['category'])) {
            $validatecategory = \MailCategory::findFirst(array(
                        "conditions" => "idAccount = ?0 and idMailCategory =?1 and deleted = 0",
                        "bind" => array(0 => (int) \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount, 1 => $data['mail']['category'][0])
            ));             
            if (empty($data['mail']['category'][0])) {
                throw new \InvalidArgumentException("El id de categoría esta vacio");
            }
            if(!$validatecategory || empty($validatecategory)){
               throw new \InvalidArgumentException("El id de categoría no pertenece a la cuenta");                 
            }
        }
        $validateblocked = \Blocked::findFirst([["email" => $data['mail']['to'], "idAccount" => (int) $idAccount, "deleted" => 0]]);
        
        if($validateblocked){
          throw new \InvalidArgumentException("El correo ingresado se encuentra bloqueado");  
        }


    }
    public function validateMail($mail) {
        if (!empty($mail) && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
}
