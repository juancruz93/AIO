<?php

namespace Sigmamovil\Wrapper;

require_once __DIR__ . "/../general/misc/forceutf8/src/ForceUTF8/Encoding.php";

class AutoResponderWrapper extends \BaseWrapper {
  
  public function getAllAutoresponder($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $name = (isset($filter->name) ? " AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

    $conditions = array(
        "conditions" => "idSubaccount = ?0 AND deleted = 0 {$name}",
        "bind" => array(\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $autoresponder = \Autoresponder::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \Autoresponder::count($conditions);

    $data = array();
    if (count($autoresponder) > 0) {
      foreach ($autoresponder as $key => $value) {
        $data[$key] = array(
            "idAutoresponder" => $value->idAutoresponder,
            "idSubaccount" => $value->idSubaccount,
            "nameSender" => $value->NameSender->name,
            "emailsender" => $value->Emailsender->email,
            "name" => $value->name,
            "type" => $value->type,
            "subject" => $value->subject,
            "replyTo" => $value->replyTo,
            "time" => $value->time,
            "days" => explode(',', $value->days),
            "status" => $value->status,
            "updated" => date("Y-m-d", $value->updated),
            "created" => date("Y-m-d", $value->created),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "type" => $value->class
        );
        if ($value->target) {
          $p = json_decode($value->target);
          if (isset($p->contactlists)) {
            $v = "Lista de contactos: ";
            for ($index = 0; $index < count($p->contactlists); $index++) {
              $v .= $p->contactlists[$index]->name . ", ";
            }
          } else if (isset($p->segment)) {
            $v = "Segmentos: ";
            for ($index = 0; $index < count($p->segment); $index++) {
              $v .= $p->segment[$index]->name . ", ";
            }
          }
          $v = substr($v, 0, -2);
          $data[$key]['target'] = $v;
        }
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }
    
  public function createAutoresponder($arrayData){
    
    $autoresponderForm = new \AutoresponderForm();
    //$class = $this->getClassAutoresponder($arrayData["class"]);
    $class = $this->getClassAutoresponder($arrayData["class"]);
    //$this->validateData($arrayData,$class);
    $this->validateTarget($arrayData);
    $this->validateTime($arrayData);
    $this->validateSenderName($arrayData);
    $this->validateSenderEmail($arrayData);
    $this->validateReplyTo($arrayData);
    $real_days = 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday';
    $arrayDays = explode(",", $real_days);
    $today = date('l');

    $autoresponder = new \Autoresponder();
    $autoresponder->idSubaccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
    $autoresponder->idNameSender = $arrayData['senderNameSelect'];
    $autoresponder->idEmailsender = $arrayData['senderMailSelect'];
    if (in_array($today, $arrayDays)) {
      $autoresponder->scheduleDate = (date('Y-m-d'));
    } else {
      $date = strtotime("next " . $arrayDays[0], strtotime(date('Y-m-d')));
      $autoresponder->scheduleDate = date("Y-m-d", $date);
    }
    $autoresponder->target = $this->targetAutoresponder($arrayData);
//    $autoresponder->target = $this->targetAutoresponder($arrayData['addressees']);
    $autoresponder->type = 'birthday';
    $autoresponder->days = $real_days;
    $autoresponder->replyTo = $arrayData['replyTo'];
    $autoresponder->time = $arrayData['time'];
    $autoresponder->quantitytarget = $arrayData["addressees"]['count'];
    $autoresponder->status = $arrayData['status'];
    $autoresponder->birthdate = 1;
    $autoresponder->class = $arrayData["class"];
    $autoresponder->optionAdvance = $arrayData['optionAdvance'];
    
    if(!empty($arrayData["customFields"])){
        
        //SI ENVIAN EL ARREGLO DE CUSTOMFIELDS CREAMOS UN OBJETO PARA ESTRUCTURAR EL JSON QUE SE ALMACENARA EN LA BD
        $objectCF = new \stdClass();
        $objectCF->customFields = "";

        for($i=0; $i < count($arrayData["customFields"]); $i++){
            
            $objectCF->customFields->$arrayData["customFields"][$i]["idCF"] = array(
            
                fontSize => $arrayData["customFields"][$i]["fontSize"],
                color => $arrayData["customFields"][$i]["color"],
                fontWeight => $arrayData["customFields"][$i]["fontWeight"],
                fontStyle => $arrayData["customFields"][$i]["fontStyle"],
                textDecoration => $arrayData["customFields"][$i]["textDecoration"],
                fontFamily => $arrayData["customFields"][$i]["fontFamily"]
                
            );
            
        }
        $objectCF->idCFmixed = $arrayData["idCFmix"];  
        $objectCF->textAlign = $arrayData["textAlign"];      
        $autoresponder->customFields = json_encode($objectCF);
    }  
    
    $autoresponderForm->bind($arrayData, $autoresponder);

    if (!$autoresponderForm->isValid()) {
      foreach ($autoresponderForm->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    if (!$autoresponder->save()) {
      foreach ($autoresponder->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    return $autoresponder;
  }
  
  public function targetAutoresponder($data2) {
    $data = $data2['addressees'];
    if ($data['showSegment'] == false) {
      $arr = array();
      $array = ["type" => "segment"];
      foreach ($data['selectdSegment'] as $key) {
        $obj = new \stdClass();
        $obj->idSegment = $key['idSegment'];
        $obj->name = $key['name'];
        array_push($arr, $obj);
      }
      $array["segment"] = $arr;
    } else if ($data['showContactlist'] == false) {
      $arr = array();
      $array = ["type" => "contactlist"];
      foreach ($data['selectdContactlis'] as $key) {
        $obj = new \stdClass();
        $obj->idContactlist = $key['idContactlist'];
        $obj->name = $key['name'];
        array_push($arr, $obj);
      }
      $this->getBalanceServiceMail($data['count']);
      $array["contactlists"] = $arr;
    }
//    if (count($data2['filters']) >= 1) {
//      $array["filters"] = $data2['filters'];
//      $array["condition"] = $data2['condition'];
//    }
    $target = "";
    if (count($array["contactlists"]) > 0 || count($array["segment"]) > 0) {
      $target = json_encode($array);
    }
    return $target;
  }

  public function createContentEditorAutoresponder($idAutoresponder, $data) {
    $autoresponderContent = new \AutoresponderContent();
    $forceUtf8 = new \ForceUTF8\Encoding();
    $content = $forceUtf8->fixUTF8($data['editor']);

    $autoresponderContent->idAutoresponder = $idAutoresponder;
    $autoresponderContent->content = $content;
    $autoresponderContent->type = 'Editor';

    if (!$autoresponderContent->save()) {
      foreach ($autoresponderContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    return $autoresponderContent;
  }
  
  public function editContentEditorAutoresponder($data, $autoresponderContent) {
    $forceUtf8 = new \ForceUTF8\Encoding();
    $content = $forceUtf8->fixUTF8($data['editor']);

    $autoresponderContent->content = $content;

    if (!$autoresponderContent->save()) {
      foreach ($autoresponderContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    return $autoresponderContent;
  }

  public function editAutoresponder($arrayData, $autoresponder) {
    $autoresponderForm = new \AutoresponderForm();

    if (count($arrayData["addressees"]['selectdContactlis']) == 0 && count($arrayData["addressees"]['selectdSegment']) == 0) {
      throw new \InvalidArgumentException("Debe seleccionar destinatarios para continuar");
    }
    if (empty($arrayData['time'])) {
      throw new \InvalidArgumentException("La Hora del envío es obigatoria");
    }
    if (!isset($arrayData['senderNameSelect'])) {
      throw new \InvalidArgumentException("El Nombre del remitente es obigatorio");
    }
    if (!isset($arrayData['senderMailSelect'])) {
      throw new \InvalidArgumentException("El Correo del remitente es obigatorio");
    }
    if (!empty($arrayData["replyTo"]) && !filter_var($arrayData["replyTo"], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido en el campo Responder a");
    }
    $real_days = 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday';
    $arrayDays = explode(",", $real_days);
    $today = date('l');
    
    $autoresponder->idNameSender = $arrayData['senderNameSelect'];
    $autoresponder->idEmailsender = $arrayData['senderMailSelect'];
    $autoresponder->target = $this->targetAutoresponder($arrayData);
    $autoresponder->replyTo = $arrayData['replyTo'];
    $autoresponder->time = $arrayData['time'];
    $autoresponder->quantitytarget = $arrayData["addressees"]['count'];
    $autoresponder->status = $arrayData['status'];
    $autoresponder->optionAdvance = $arrayData['optionAdvance'];
    
    if(!empty($arrayData["customFields"])){
        
        //SI ENVIAN EL ARREGLO DE CUSTOMFIELDS CREAMOS UN OBJETO PARA ESTRUCTURAR EL JSON QUE SE ALMACENARA EN LA BD
        $objectCF = new \stdClass();
        $objectCF->customFields = "";

        for($i=0; $i < count($arrayData["customFields"]); $i++){
            
            $objectCF->customFields->$arrayData["customFields"][$i]["idCF"] = array(
            
                fontSize => $arrayData["customFields"][$i]["fontSize"],
                color => $arrayData["customFields"][$i]["color"],
                fontWeight => $arrayData["customFields"][$i]["fontWeight"],
                fontStyle => $arrayData["customFields"][$i]["fontStyle"],
                textDecoration => $arrayData["customFields"][$i]["textDecoration"],
                fontFamily => $arrayData["customFields"][$i]["fontFamily"]
                
            );
            
        }
        $objectCF->idCFmixed = $arrayData["idCFmix"];  
        $objectCF->textAlign = $arrayData["textAlign"];      
        $autoresponder->customFields = json_encode($objectCF);
    }    

    if (in_array($today, $arrayDays)) {
      $autoresponder->scheduleDate = (date('Y-m-d'));
    } else {
      $date = strtotime("next " . $arrayDays[0], strtotime(date('Y-m-d')));
      $autoresponder->scheduleDate = date("Y-m-d", $date);
    }

    $autoresponderForm->bind($arrayData, $autoresponder);

    if (!$autoresponderForm->isValid()) {
      foreach ($autoresponderForm->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    if (!$autoresponder->save()) {
      foreach ($autoresponder->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    return $autoresponder;
  }

  public function deleteAutoresponder($data) {
    if (!isset($data)) {
      throw new \InvalidArgumentException("Dato de autorespuesta de cuenta inválido");
    }

    $autoresponder = \Autoresponder::findFirst(array(
                "conditions" => "idAutoresponder = ?0",
                "bind" => array($data)
    ));

    if (!$autoresponder) {
      throw new \InvalidArgumentException("La autorespuesta que intenta eliminar no existe");
    }

    $autoresponder->deleted = 1;

    if (!$autoresponder->update()) {
      foreach ($autoresponder->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  public function getAutoresponderExists($idAutoresponder) {
    $autoresponder = \Autoresponder::findFirst(["conditions" => "idAutoresponder = ?0 AND deleted = 0", "bind" => [0 => $idAutoresponder]]);
    return $autoresponder;
  }
  
  public function createAutorespdesms($arrayData){
    
         $autoresponderForm = new \AutoresponderForm();
         
         //validaciones
         $this->validateTarget($arrayData);
         $this->validateTime($arrayData); 
        //$this->validateDaysSelected(json_encode($arrayData['days'], true));
         $this->validateDaysSelected($arrayData['days'], true);
          if (empty($arrayData['scheduledate'])) { 
                throw new \InvalidArgumentException("El comienzo de la programacion es un dato obligatorio");                          
          }
          if (empty($arrayData['message'])) { 
              throw new \InvalidArgumentException("El mensaje con etiquetas son un dato obligatorio");                          
          }
          
          if ($arrayData['morecaracter'] == false) {
            if (strlen($arrayData['message']) > 160) {
                throw new \InvalidArgumentException("El campo mensaje debe tener máximo 160 caracteres");
            } 
          } 
          
          $this->validateDateProgrammed($arrayData);
          $this->validateidSmsCategory($arrayData);
        
          //asignando datos...
          $autoresponder = new \Autoresponder();
          $autoresponder->idSubaccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
          $autoresponder->scheduleDate = $arrayData['scheduledate'];
          $autoresponder->idSmsCategory = $arrayData['idSmsCategory'];
          $autoresponder->target = $this->targetAutoresponder($arrayData); //retirado por pruebas colocar ahora.
          $autoresponder->type = 'birthday';
          $autoresponder->days = $this->getDaysOfTheWeek($arrayData["days"]);
          $autoresponder->time = $arrayData['time'];
          $autoresponder->quantitytarget = $arrayData["addressees"]['results']['counts'];
          $autoresponder->status = $arrayData['status'];
          $autoresponder->birthdate = (($arrayData['birthdate']) ? 1 : 0);
          $autoresponder->class = $this->getClassAutoresponder($arrayData["class"]);
          
          if($arrayData['morecaracter'] == true){
            $autoresponder->morecaracter = 1;
          }else{
            $autoresponder->morecaracter = 0;   
          }

          $autoresponderForm->bind($arrayData, $autoresponder);
          
          if (!$autoresponderForm->isValid()) {
            foreach ($autoresponderForm->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
          if (!$autoresponder->save()) {
            foreach ($autoresponder->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
            
          $autoresponderContent = new \AutoresponderContent();
          $autoresponderContent->idAutoresponder = $autoresponder->idAutoresponder;
          $autoresponderContent->content = $arrayData['message'];
          $autoresponderContent->type = 'Editor';

          if(!$autoresponderContent->save()) {
            foreach ($autoresponderContent->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
          return $autoresponder;
  }
  
  public function editAutorespdesms($arrayData, $autoresponder){
         
         $autoresponderForm = new \AutoresponderForm();
         
         //validaciones
         $this->validateTarget($arrayData);
         $this->validateTime($arrayData); 
          $this->validateDaysSelected($arrayData['days']);
          if (empty($arrayData['scheduledate'])) { 
                throw new \InvalidArgumentException("El comienzo de la programacion es un dato obligatorio");                          
          }
          if (empty($arrayData['message'])) { 
        throw new \InvalidArgumentException("El mensaje con etiquetas son un dato obligatorio");                          
      } 
          $this->validateDateProgrammed($arrayData);
          $this->validateidSmsCategory($arrayData);
                  
          if ($arrayData['morecaracter'] == false) {
            if (strlen($arrayData['message']) > 160) {
                throw new \InvalidArgumentException("El campo mensaje debe tener máximo 160 caracteres");
            } 
          }           
          //asignando datos...
          $autoresponder->scheduleDate = $arrayData['scheduledate'];
          $autoresponder->idSmsCategory = $arrayData['idSmsCategory'];
          $autoresponder->target = $this->targetAutoresponder($arrayData); //retirado por pruebas colocar ahora.
          $autoresponder->type = 'birthday';
          $autoresponder->days = $this->getDaysOfTheWeek($arrayData["days"]); //para que convierta el json en array
          $autoresponder->time = $arrayData['time'];
          $autoresponder->quantitytarget = $arrayData["addressees"]['results']['counts'];
          $autoresponder->status = $arrayData['status'];
          $autoresponder->birthdate = (($arrayData['birthdate']) ? 1 : 0);
          $autoresponder->class = $this->getClassAutoresponder($arrayData["class"]);

          if($arrayData['morecaracter'] == true){
            $autoresponder->morecaracter = 1;
          }else{
            $autoresponder->morecaracter = 0;   
          }

          $autoresponderForm->bind($arrayData, $autoresponder);
          
           if (!$autoresponderForm->isValid()) {
            foreach ($autoresponderForm->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
          if (!$autoresponder->save()) {
            foreach ($autoresponder->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }

          $autoresponderContent = \AutoresponderContent::findFirst(array(
             "conditions" => "idAutoresponder = ?0",
             "bind" => array($arrayData['idAutoresponder']))
           );
          
          $autoresponderContent->content = $arrayData['message'];
          $autoresponderContent->updated = time();
          
           if(!$autoresponderContent->save()) {
            foreach ($autoresponderContent->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
          
          return $autoresponder;
  }
  
  public function getDataEdit($id){
    $autoresponder = \Autoresponder::findFirst(array(
        "conditions" => "idAutoresponder = ?0",
        "bind" => array($id))
    );
    
              //$autoresponder->idSmsCategory = "431";  //le asigno el valor quedamo donde deberia ir el idSmsCategory...
    
    $autoresponderContent = \AutoresponderContent::findFirst(array(
        "conditions" => "idAutoresponder = ?0",
        "bind" => array($autoresponder->idAutoresponder))
    );
    if (!$autoresponder) {return false;}
    $arr = array();
    $arr['idSmsCategory'] = $autoresponder->idSmsCategory; //quemado para pruebas, quitar cuando haya la columna...
    $arr['scheduledate'] = $autoresponder->scheduleDate;
    $arr['name'] = $autoresponder->name;
    $arr['target'] = json_decode($autoresponder->target);
    $arr['subject'] = $autoresponder->subject;
    $arr['time'] = $autoresponder->time;
    $arr['days'] = $this->deliverDaysToJson($autoresponder->days);
    $arr['birthdate'] = $autoresponder->birthdate;
    $arr['message'] = $autoresponderContent->content;
     
    if($autoresponder->morecaracter == 1){
        $arr['morecaracter'] = true;
    }else if ($autoresponder->morecaracter == 0){
        $arr['morecaracter'] = false; 
    }
    return $arr;
  }
  
  public function getClassAutoresponder($typeAutoresp){
     $tmpCls = "";  
     //si no esta declarando clase de autorespuesta
     if(!isset($typeAutoresp)){
       $tmp = 'mail';  //por defecto dejela en mail
     }else{ //de lo contrario...
       $tmp = $typeAutoresp; //devuelve la que ya tiene... sms.
     }
     return $typeAutoresp;
  }
  
  public function getDaysOfTheWeek($wkds){  
    $arrayDaysWeek = $wkds;
    $weekdaysConverted = "";
    foreach($arrayDaysWeek as $key => $value){ 
      if($value == 1) { //si es  uno que es igual a decir si es "true"
        $weekdaysConverted .= ($key.",");
      }
    }
    $weekdaysConverted = substr($weekdaysConverted, 0, -1);
    return $weekdaysConverted;
  }
  
  public function validateSenderName($senderName){
    if(!isset($senderName['senderNameSelect'])){
      throw new \InvalidArgumentException("El Nombre del remitente es obligatorio");
    }
  }  
      
  public function validateSenderEmail($senderMail){
    if (!isset($senderMail['senderMailSelect'])){
      throw new \InvalidArgumentException("El Correo del remitente es obligatorio");
    }
  } 
      
  public function validateReplyTo($reply){
    if (!empty($reply["replyTo"]) && !filter_var($reply["replyTo"], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido en el campo Responder a");
    }
  }
  
  public function validateidSmsCategory($category){
    if (empty($category['idSmsCategory'])) {
      throw new \InvalidArgumentException("La Categoria del Sms de Autorespuesta es obligatoria");
    }
  }
  
  public function validateTarget($target){
    if (count($target["addressees"]['selectdContactlis']) == 0 && count($target["addressees"]['selectdSegment']) == 0) {
      throw new \InvalidArgumentException("Debe seleccionar destinatarios para continuar");
    }
  }
  
  public function validateTime($time){
    if (empty($time['time'])) {
      throw new \InvalidArgumentException("La Hora del envío es obligatoria");
    }
  }
  
  public function deliverDaysToJson($var1){
    //$var1 = 'Monday,Tuesday,Wednesday,Friday';
    $var2 = explode(',', $var1);
    $var3 = array();
    foreach ($var2 as $array) {
      $var3[$array] = true;
    } 
    return $var3;
  }
  
  public function validateDaysSelected($arrDays){
       if (
              $arrDays['Monday']   == false && $arrDays['Tuesday'] == false &&$arrDays['Wednesday'] == false &&
              $arrDays['Thursday'] == false && $arrDays['Friday']     == false && $arrDays['Saturday']     == false &&
              $arrDays['Sunday']    == false
      ) { 
        throw new \InvalidArgumentException("Digite al menos un dia de la semana");                          
      }
  }
  
  public function validateDateProgrammed($arrayData){
    
    $scheduleDate = $arrayData['scheduledate'];
    $time = $arrayData['time'];
    
    //consulto la fecha actual para comparar
    $dateNow = date('Y-m-d H:i:s');
    //construyo una hora con los datos digitados
    $dateProgrammed = date($scheduleDate.' '.$time.':00');    
    //las comparo y vero si ya ha pasado la fecha y hora de programacion
     if($dateProgrammed < $dateNow ){ throw new \InvalidArgumentException("Ya ha pasado la fecha y hora de programacion, por favor cambie los datos"); }
     
  }
  
  public function getBalanceServiceMail($quantitytarget){

    $sending = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == 'sending') {
        $sending = true;
      }
    }
    if($sending){
      //Se realiza validaciones de los sms programados
      $balance = $this->validateBalanceMail();

      $target = 0;
      if($balance['mailFindPending']){
        foreach ($balance['mailFindPending'] as $value){
          $target = $target + $value['target'];
        }
      }
      $amount = $balance['balanceConsumedFind']['amount'];

      unset($balance);
      $totalTarget =  $amount - $target;
      $target = $target + $quantitytarget["count"];

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
  
  public function validateBalanceMail(){
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
      'conditions' => 'idSubaccount = ?0 and idServices = ?1 and accountingMode = ?2',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 2,
        2 => 'sending'
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

    $arrayMailFindPending = [];
    if($mailFindPending != false){
      $arrayMailFindPending = $mailFindPending->toArray();
    }
    $answer = ['mailFindPending'=>$arrayMailFindPending, 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];
    return $answer;
  }

  public function sendmailnotmailbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == 'sending') {
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
    //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de SMS
    $sendMailNot->sendMailNotification($this->arraySaxs);
    return true;
  }
  
    public function findcustomfields($idContactlist){
        //$customfields = \Customfield::find(["conditions" => "idContactlist = ?0", "bind" => [$idContactlist]]);
        $result = array();
        $result[0] = Array(
            "idCustomfield" => "0",
            "name" => "NOMBRE"
        );
        $result[1]=Array(
            "idCustomfield" => "1",
            "name" => "APELLIDO"
        );
        $result[2]=Array(
            "idCustomfield" => "2",
            "name" => "FECHA_NACIMIENTO"
        );
    
        $query = "select  idCustomfield, name, alternativename from  customfield "
                ." where idContactlist = ".$idContactlist
                ." and deleted = 0";
    
        $result1=$this->db->fetchAll($query);
        if (count($result1) > 0) {
            foreach ($result1 as $value) {
                array_push($result,$value);
            }
            return $result;
        }else{
            throw new \InvalidArgumentException("La lista seleccionada no tiene campos personalizados");
        }

    }
}
