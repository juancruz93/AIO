<?php

require_once(__DIR__ . "/../bootstrap/index.php");
use Sigmamovil\General\Misc\SanitizeString;

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$autoresponderSender = new AutoresponderSender();
$autoresponderSender->autoresponderStart($id);

class AutoresponderSender
{
  public $idContactlist = array(),
      $idSegment = array(),
      $inIdcontact = array(),
      $contact = array(),
      $contactlist,
      $db,
      $offset = 0,
      $limit = 8000,
      $flag = true,
      $count = 0,    
      $createdBy,
      $idAutoresponder=0;

  /**
   * AutoresponderSender constructor.
   * @param array $idContactlist
   */
  public function __construct()
  {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }


  public function autoresponderStart($idAutoresponder)
  {
    try {
       
      $this->idAutoresponder = $idAutoresponder;
      $autoresponder = Autoresponder::findFirst(array(
          'conditions' => 'idAutoresponder = ?0',
          'bind' => [$idAutoresponder]
      ));
      $this->db->begin();
      if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
      }
      if (empty($autoresponder->target)) {
        throw new InvalidArgumentException('La autorespuesta solicitada no contiene listas de contactos, por favor valide la información');
      }
      //NO SE EJECUTA EL ENVIO DE LA AUTORESPUESTA SI EL USUARIO NO HA SELE
      $autoresponderContent = \AutoresponderContent::findFirst(array(
         "conditions" => "idAutoresponder = ?0",
         "bind" => array($idAutoresponder))
      );
      if(!$autoresponderContent){
        throw new \InvalidArgumentException("La autorespuesta {$idAutoresponder} no tiene plantilla seleccionada.");
        return false;
      }
      $this->createdBy = $autoresponder->createdBy;
      $days = $autoresponder->days;
      \Phalcon\DI::getDefault()->get('logger')->log("Dias de la autorespuesta {$days} Autoresponder{$autoresponder->idAutoresponder}");
      $arrayDays = explode(",", $days);
      $today = date('l');//este es el dia

      if (in_array($today, $arrayDays)) {
        $keyToday = array_search($today, $arrayDays);

        if ($keyToday == (count($arrayDays) - 1)) {
          $date = strtotime("next " . $arrayDays[0], strtotime(date('Y-m-d')));
          $autoresponder->scheduleDate = (date('Y-m-d', $date));
        } else {
          $date = strtotime("next " . $arrayDays[$keyToday + 1], strtotime(date('Y-m-d')));
          $autoresponder->scheduleDate = (date('Y-m-d', $date));
        }
      }

      if (!$autoresponder->save()) {
        foreach ($autoresponder->getMessages() as $msg) {
          throw new \InvalidArgumentException($msg);
        }
      }
      
      \Phalcon\DI::getDefault()->get('logger')->log("Entra en el searchTotalContacts Mail Autoresponder{$autoresponder->idAutoresponder}");
      $target = json_decode($autoresponder->target);
      \Phalcon\DI::getDefault()->get('logger')->log("Este es el target {$autoresponder->target} Mail Autoresponder{$autoresponder->idAutoresponder}");
      while ($this->flag) {
        switch ($target->type) {
          case "contactlist":
            $this->getIdContaclist($target);
            $this->getAllCxcl();
            break;
          case "segment":
            $this->getIdSegment($target);
            $this->getAllIdContactSegment();
            break;
          default:
        }
        $this->getAllContact();
        $this->offset += $this->limit;
      }
      //$this->contactlist = $this->createContactList($autoresponder->idSubaccount, "Birthday-" . date('Y-m-d H:i:s'));

//      $target = json_decode($autoresponder->target);
//      
//      $idContactlist = $target->contactlists[0]->idContactlist;
//      $this->contactlist = Contactlist::findFirst(array(
//        'conditions' => 'idContactlist = ?0',
//        'bind' => [$idContactlist] //pasarle el id contact list que necesito
//      ));
      
//      $this->getContacts($target);
      
//      $newTarget = json_encode($this->generateTarget());
      $amount = 0;
      $sending = false;
      foreach ($this->subAccount->saxs as $key) {
        if ($key->idServices == 2 && $key->accountingMode == "sending" && $key->status==1) {
          $sending = true;
          $amount = $key->amount;
          $totalAmount = $key->totalAmount;
          $subaccountName = $this->subAccount->name;
          $accountName = $this->subAccount->Account->name;
          $arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName,
          );
        }
      }
      if($sending){
        if ($amount <= 0) {
          $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
          $sendMailNot->sendMailNotification($arraySaxs);
          throw new \InvalidArgumentException("No tienes saldo disponible para realizar envíos de Mail de campaña automatica");
        } else {
          $autoresponderMailManager = new \Sigmamovil\General\Misc\AutoresponderMailManager($autoresponder);
          $autoresponderMailManager->cloneAutoresponder();
          Phalcon\DI::getDefault()->get('logger')->log("Se realizo la creación exitosa de Mail Autoresponder{$autoresponder->idAutoresponder}");
        }                     

        if ($amount < $this->contact) {
          $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
          $sendMailNot->sendMailNotification($arraySaxs);
          throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de Mail de campaña automatica.");
        } else {
          $autoresponderMailManager = new \Sigmamovil\General\Misc\AutoresponderMailManager($autoresponder);
          $autoresponderMailManager->cloneAutoresponder();
          Phalcon\DI::getDefault()->get('logger')->log("Se realizo la creación exitosa de Mail Autoresponder{$autoresponder->idAutoresponder}");
        }    
      } else {
        if($this->contact > 0){
          
          $autoresponderMailManager = new \Sigmamovil\General\Misc\AutoresponderMailManager($autoresponder);
          $autoresponderMailManager->cloneAutoresponder();
          Phalcon\DI::getDefault()->get('logger')->log("Se realizo la creación exitosa de Mail Autoresponder{$autoresponder->idAutoresponder}");
        }
      }
      return true;
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (Exception $ex) {
      $this->db->rollback();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  public function createContactList($idSubaccount, $name)
  {
    $contactlist = new \Contactlist();
    $contactlist->idSubaccount = $idSubaccount;
    $contactlist->name = $name;
    $contactlist->createdBy = $this->createdBy;

    if (!$contactlist->save()) {
      foreach ($contactlist->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    return $contactlist;
  }

//  public function getContacts($target)
//  {
//    switch ($target->type) {
//      case "contactlist":
//        if (isset($target->contactlists)) {
//          
//          foreach ($target->contactlists as $key) {
//            $this->idContactlist[] = $key->idContactlist;
//          }
//        }
//        break;
//      case "segment":
//        if (isset($target->segment)) {
//          $this->getIdContactlistBySegments($target->segment);
//        }
//        break;
//      default:
//        throw new Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
//    }
//    
//    while ($this->flag) {
//      $this->getAllCxcl();
//      if (isset($this->inIdcontact)) {
//        $this->getAllContact();
//      }
//      $this->offset += $this->limit;
//    }
//  }
  public function getIdContaclist($target) {
    if (isset($target->contactlists)) {
      foreach ($target->contactlists as $key) {
        $this->idContactlist[] = $key->idContactlist;
      }
    }
  }
  
  public function getAllCxcl()
  {
    $idContactlist = implode(",", $this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
        . " WHERE idContactlist IN ({$idContactlist})"
        . " AND unsubscribed = 0 "
        . " AND deleted = 0 "
        . " AND spam = 0 "
        . " AND bounced = 0 "
        . " AND blocked = 0 "
        . " AND singlePhone = 0"        
        . " LIMIT {$this->limit} OFFSET {$this->offset}";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int)$cxcl[$i]['idContact'];
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($sql);
    unset($cxcl);
  }
  
  public function getIdSegment($target) {
    if (isset($target->segment)) {
      foreach ($target->segment as $key) {
        $this->idSegment[] = $key->idSegment;
      }
    }
  }

  public function getAllIdContactSegment() {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new MongoDB\Driver\Command([
      'aggregate' => 'sxc',
      'pipeline' => [
          ['$match' => ['idSegment' => ['$in' => $this->idSegment],'email' => ['$nin' => ["", null, "null"]]]],
          ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]],
          ['$limit' => $this->offset + $this->limit],
          ['$skip'  => $this->offset]
      ],
      'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      $this->inIdcontact[] = $segment[0]->result[$i]->_id;
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($command);
    unset($segment);
  }

  public function getAllContact()
  { 
    $hyphenDateFormat = date('m-d');
    $stringHyphenDateFormat = new \MongoRegex("/{$hyphenDateFormat}/");
    $slashDateFormatM = date('m');
    $slashDateFormatD = date('d');
    $stringSlashDateFormat = new \MongoRegex("/{$slashDateFormatM}\/{$slashDateFormatD}/");
    $where = ["idContact" => ['$in' => $this->inIdcontact]];
    $where['$or'] = [
      ["birthdate" => ['$regex' => $stringHyphenDateFormat]],
      ["birthdate" => ['$regex' => $stringSlashDateFormat]]
    ];
    
    //VALIDAMOS QUE OPTIONADVANCE DE LA AUTORESPUESTA SEA 1
    $autoresponder = Autoresponder::findFirst(array(
          'conditions' => 'idAutoresponder = ?0',
          'bind' => [$this->idAutoresponder]
    ));
    $this->db->begin();
    if (!$autoresponder) {
        throw new InvalidArgumentException('No se encontró la autorespuesta solicitada, por favor valide la información');
    }else{
        
        if($autoresponder->optionAdvance = 1){
            
            //GUARDAMOS EL ID DE LA LISTA DE CONTACTOS
            $idCL = $this->idContactlist[0];
                        
            //RECORREMOS CUSTOMFIELDS PARA SACAR EL ID DEL CF Y BUSCAR SU VALOR PARA ESTRUCTURAR EL HTML
            $arrCF = json_decode($autoresponder->customFields);
            $textAlign = "";
            $idCFmixed = 0;
            $html = "";
            $arrSpan = [];
            $span = "";
                   
            $contacts = \Contact::find(array($where));
            foreach ($contacts as $value1) {
              $idContact = $value1->idContact;
              //BUSCAMOS EN CXC CON LOS ID DE CONTACTO QUE NOS HAYAN RETORNADO
              
              foreach ($arrCF as $key2=>$value2){
                //EXTRAEMOS IDCFMIXED A VARIABLE GLOBAL DENTRO DE LA FUNCION
                if($key2 == "idCFmixed"){
                    $idCFmixed = $value2;
                }else if($key2 == "textAlign"){
                //EXTRAEMOS TEXTALIGN A VARIABLE GLOBAL DENTRO DE LA FUNCION
                    $textAlign = $value2;
                }else if($key2 == "customFields"){
                    //RECORREMOS EL JSON DE CUSTOMFIELDS
                    foreach($value2 as $key3 => $value3){
                        
                        //COMPARAMOS LOS CAMPOS ESTANDAR
                        $idCF = $key3;
                        if($idCF == "0"){
                            $valueCF = $value1->name;
                        }else if($idCF == "1"){
                            $valueCF = $value1->lastname;
                        }else if($idCF == "2"){
                            $valueCF = $value1->birthdate;
                        }else{
                            //SI NO ES UN CAMPO ESTANDAR LO BUSCAMOS EN CXC YA QUE ES CAMPO PERSONALIZADO
                            $cxc = \Cxc::findFirst([["idContact" => $idContact]]);
                            //
                            if($cxc){
                                //PREGUNTAMOS SI ESE CONTACTO TIENE LA LISTA DE CONTACTOS RELACIONADA EN CXC
                                if(isset($cxc->idContactlist[$idCL])){
                                    //PREGUNTAMOS SI LA LISTA DE CONTACTOS TIENE EL ID DE CAMPO PERSONALIZADO RELACIONADO
                                    if(isset($cxc->idContactlist[$idCL][$idCF])){
                                        //SI ESTA RELACIONADO EXTRAEMOS EL VALOR DEL CAMPO Y LO AGREGAMOS AL HTML
                                        $sanitizeString = new SanitizeString($cxc->idContactlist[$idCL][$idCF]["value"]);
                                        $sanitizeString->strTrim();
                                        //$sanitizeString->sanitizeBlanks();
                                        $sanitizeString->sanitizeAccents();
                                        $sanitizeString->sanitizeSpecials();
                                        //$sanitizeString->toLowerCase();
                                        $valueCF = $sanitizeString->getString();
                                    }else{
                                        throw new InvalidArgumentException("La lista de contacto {$idCL} del contacto {$idContact} no tiene relacionado el campo personalizado {$idCF}");
                                        return;
                                    }
                                }else{
                                    throw new InvalidArgumentException("El contacto {$value1->name} {$value1->lastname} no esta asociado en la lista de contacto {$idCL}");
                                    return;
                                }
                            }
                        }          

                        $span .= "<span style='font-size:{$value3->fontSize}px;color:{$value3->color};font-weight:{$value3->fontWeight};font-style:{$value3->fontStyle};text-decoration:{$value3->textDecoration};font-family:{$value3->fontFamily};'>{$valueCF} </span>";
                                  
                    }
                    //SETEAMOS EL SPAN AL ARREGLO DE SPAN Y VACIAMOS LA VARIABLE STRING
                    array_push($arrSpan,$span);
                    $span = "";
                }
                
              }
              
            }
            
            //RECORREMOS LOS SPAN CREADOS PARA CONCATEAR LOS H1
            for($x=0; $x < count($arrSpan); $x++){
                $html .= "<h1 style='text-align:{$textAlign};'>".$arrSpan[$x]."</h1>";
            }
            
            //CREAMOS EL OBJETO QUE CONTENDRA COMO VALOR EL HTML EN EL NUEVO REGISTRO DE CFMIXED
            $obj = array();
            //BUSCAMOS EL REGISTRO DE CF EN LA TABLA CUSTOMFIELD
            $customfield = \Customfield::findFirst([
            "conditions" => "idContactlist = ?0 AND idCustomfield=?1 AND deleted = 0", 
            "bind" => [0 => $idCL, 1=>$idCFmixed]
            ]);
            foreach ($contacts as $value1) {
              $idContact = $value1->idContact;
              $obj[$idCFmixed] = ["name" => $customfield->name, "value" => mb_convert_encoding($html, 'UTF-8', 'auto'), "type" => $customfield->type];
              $cxc = \Cxc::findFirst([["idContact" => $idContact]]);
              //EL CONTACTO ESTA CREADO EN CXC
              if($cxc){
                  //PREGUNTAMOS SI ESE CONTACTO TIENE LA LISTA DE CONTACTOS RELACIONADA EN CXC
                  if(isset($cxc->idContactlist[$idCL])){
                      //PREGUNTAMOS SI LA LISTA DE CONTACTOS TIENE EL ID DE CFMIXED RELACIONADO
                      if(isset($cxc->idContactlist[$idCL][$idCFmixed])){
                          //SI ESTA RELACIONADO EDITAMOS EL CONTENIDO DE CFMIXED
                          $cxc->idContactlist[$idCL][$idCFmixed]["value"] = "";
                          $cxc->idContactlist[$idCL][$idCFmixed]["value"] =  mb_convert_encoding($html, 'UTF-8', 'auto');
                          $cxc->save();
                      }else{
                        //SI NO ESTA REALCIONADO CREAMOS EL REGISTRO
                        $objDecode = (object) $obj;
                        $tmp = $cxc->idContactlist;
                        foreach($objDecode as $key2 => $value2){
                            $tmp[$idCL][$key2] = $value2;                          
                        }
                        $cxc->idContactlist = null;
                        $cxc->idContactlist= (object) $tmp;
                        $cxc->save();
                      }
                  }else{
                      throw new InvalidArgumentException("El contacto {$value1->name} {$value1->lastname} no esta asociado en la lista de contacto {$idCL}");
                      return;
                  }
              }else{
                /*$cxc = \Cxc::findFirst([["idContact" =>(int) $idContact]]);
            
                if(!empty($cxc)){             
                    $tmp = $cxc->idContactlist;
                    foreach($obj as $value => $key){
                        $tmp[$contactlist->idContactlist][$value] = $key;
                    }
                    $cxc->idContactlist = null;
                    $cxc->idContactlist= (object) $tmp;
                    $cxc->save();
                    unset($cxc);
                }*/
              }
              unset($cxc);
              
            }
            
        }
        
    }
    
    //$where = array("idContact" => ['$in' => $this->inIdcontact], 'birthdate' => ['$regex' => ".*$var.*"]);
    $this->contact = \Contact::count(array($where));
    unset($this->inIdcontact);
    unset($where);
    $this->count = $this->count + $this->contact;
    //$this->modeldata();
  }

  public function modeldata()
  {
    foreach ($this->contact as $value) {
      $cxcl = new Cxcl();
      $cxcl->idContactlist = $this->contactlist->idContactlist;
      $cxcl->idContact = $value->idContact;
      $cxcl->active = time();
      $cxcl->status = 'active';
      $cxcl->createdBy = $this->createdBy;

      if (!$cxcl->save()) {
        foreach ($cxcl->getMessages() as $msg) {
          throw new \InvalidArgumentException($msg);
        }
      }
    }

    unset($this->contact);
  }

  public function getIdContactlistBySegments($listSegment)
  {
    foreach ($listSegment as $key) {
      $segment = Segment::findFirst([["idSegment" => $key->idSegment]]);
      foreach ($segment->contactlist as $k) {
        $this->idContactlist[] = $k["idContactlist"];
      }
      unset($segment);
    }
  }

//  public function generateTarget()
//  {
//    $arr = array();
//    $arr['type'] = "contactlist";
//    $arr['contactlists'] = array(array(
//        'idContactlist' => $this->contactlist->idContactlist,
//        'name' => $this->contactlist->name
//    ));
//
//    return $arr;
//  }
  
}