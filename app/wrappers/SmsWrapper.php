<?php

namespace Sigmamovil\Wrapper;

class SmsWrapper extends \BaseWrapper {

  /**
   * @author Juan Cruz 
   * @return arrayObject Retorna un array de objetos con la informacion devuelta
   */
   public $where;
  public function countsaxssms($data) {

    /* if ($data["datestart"] > $data["dateend"]){
      throw new \InvalidArgumentException("hola perritos");
      } */
    try {
      if ($data->datestart == "" || $data->dateend == "") {
        throw new \InvalidArgumentException("Por favor ingrese una fecha de inicio y una fecha final");
      } elseif (strtotime($data->datestart) > strtotime($data->dateend)) {
        throw new \InvalidArgumentException("La fecha inicial no puede ser mayor a la fecha final");
      }

      $idSubAccount = $this->user->Usertype->Subaccount->idSubaccount;
      $arrayReturn = array();
      $saxssql = "SELECT sum(sent) as sent, sum(total) as total
          FROM aio.sms WHERE idSubaccount = {$idSubAccount} AND status = 'sent' AND startdate BETWEEN '{$data->datestart}' AND '{$data->dateend}'
          GROUP BY idSubaccount";

      $saxs = $this->db->fetchAll($saxssql);

      if (count($saxs) > 0) {
        $arrayReturn['sent'] = $saxs[0]['sent'];
        $arrayReturn['total'] = $saxs[0]['total'];
      }

      return $arrayReturn;
    } catch (\InvalidArgumentException $ex) {
      return $ex->getMessage();
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  public function getCountContacts($data) {

    $count = 0;
    $arrIdContact = array();
    switch ($data->type) {
      case "contactlist":
        $where = " ";
        for ($i = 0; $i < count($data->contactlist); $i++) {
          $where .= $data->contactlist[$i]->idContactlist;
          if ($i != (count($data->contactlist) - 1)) {
            $where .= " , ";
          }
        }        
        $sql = "select DISTINCT idContact from cxcl "
                . "where idContactlist in ({$where}) "
                . " AND unsubscribed = 0 "
                . " AND deleted = 0 ";  
              
        $count = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
        foreach ($count as $key) {
//        array_push($arrIdContact, (int) $key['idContact']);
          $arrIdContact[] = (int) $key['idContact'];
        }
        $this->where = $where;
        $datas['counts'] = $this->getCountContactsValidate($arrIdContact,$data->switchrepeated);
        $datas['tags'] = $this->getAllTags($where);

        for ($i = 0; $i < count($data->contactlist); $i++) {
          $where .= $data->contactlist[$i]->idContactlist;
          $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->contactlist[$i]->idContactlist]]);
          if ($countContacts > 0) {
            $datas['contact'] = $this->getFisrtContact($data->contactlist[$i]->idContactlist, $data->type);
            break;
          }
        }

        return $datas;
        break;
      case "segment":
        $where = "";
        $count = 0;
        foreach ($data->segment as $key) {
          $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);

          if ($sxcs) {
            foreach ($sxcs as $sxc) {
              $count++;
//              array_push($arrIdContact, (int) $sxc->idContact
               $arrIdContact[] = (int) $sxc->idContact;

              $sql = "SELECT DISTINCT idContactlist from cxcl "
                      . "where idContact = " . $sxc->idContact;
              $contactlist = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
              foreach ($contactlist as $cl) {
                $where .= $cl['idContactlist'] . ", ";
              }
            }
          }
        }
        $datas['counts'] = $this->getCountContactsValidate($arrIdContact, $data->switchrepeated);
        foreach ($data->segment as $key) {
          $countSegments = \Sxc::count([["idSegment" => $key->idSegment]]);
          if ($countSegments > 0) {
            for ($i = 0; $i < count($data->contactlist); $i++) {
              $where .= $data->contactlist[$i]->idContactlist;
              $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->contactlist[$i]->idContactlist]]);
              if ($countContacts > 0) {
                $datas['contact'] = $this->getFisrtContact($data->contactlist[$i]->idContactlist, $data->type);
                break;
              }
            }
            break;
          }
        }

        //$datas['counts'] = $count;
        $datas['tags'] = $this->getAllTags(substr($where, 0, -2));
        $datas['contact'] = $this->getFisrtContact($data->segment[0]->idSegment, $data->type);


        return $datas;

        break;
      default:
        break;
    }
  }
  
  public function getCountContactsApi($data) {

    $count = 0;
    $arrIdContact = array();
    switch ($data->type) {
      case "contactlist":
        $where = " ";
        for ($i = 0; $i < count($data->contactlists); $i++) {
          $where .= $data->contactlists[$i]->idContactlist;
          if ($i != (count($data->contactlists) - 1)) {
            $where .= " , ";
          }
        }
        $sql = "select DISTINCT idContact from cxcl "
                . "where idContactlist in ({$where}) "
                . " AND unsubscribed = 0 "
                . " AND deleted = 0 ";
        $count = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
        foreach ($count as $key) {
          array_push($arrIdContact, (int) $key['idContact']);
        }
        $datas['counts'] = $this->getCountContactsValidate($arrIdContact);
        $datas['tags'] = $this->getAllTags($where);

        for ($i = 0; $i < count($data->contactlists); $i++) {
          $where .= $data->contactlists[$i]->idContactlist;
          $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->contactlists[$i]->idContactlist]]);
          if ($countContacts > 0) {
            $datas['contact'] = $this->getFisrtContact($data->contactlists[$i]->idContactlist, $data->type);
            break;
          }
        }

        return $datas;
        break;
      case "segment":
        $where = "";
        $count = 0;
        foreach ($data->segment as $key) {
          $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);

          if ($sxcs) {
            foreach ($sxcs as $sxc) {
              $count++;
              array_push($arrIdContact, (int) $sxc->idContact);

              $sql = "SELECT DISTINCT idContactlist from cxcl "
                      . "where idContact = " . $sxc->idContact;
              $contactlist = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
              foreach ($contactlist as $cl) {
                $where .= $cl['idContactlist'] . ", ";
              }
            }
          }
        }
        foreach ($data->segment as $key) {
          $countSegments = \Sxc::count([["idSegment" => $key->idSegment]]);
          if ($countSegments > 0) {
            for ($i = 0; $i < count($data->contactlists); $i++) {
              $where .= $data->contactlists[$i]->idContactlist;
              $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->contactlists[$i]->idContactlist]]);
              if ($countContacts > 0) {
                $datas['contact'] = $this->getFisrtContact($data->contactlists[$i]->idContactlist, $data->type);
                break;
              }
            }
            break;
          }
        }

        $datas['counts'] = $count;
        $datas['tags'] = $this->getAllTags(substr($where, 0, -2));
        $datas['contact'] = $this->getFisrtContact($data->segment[0]->idSegment, $data->type);


        return $datas;

        break;
      default:
        break;
    }
  }

  public function getAllTags($ids) {

    $this->tags[0]['name'] = 'Nombre';
    $this->tags[0]['tag'] = '%%NOMBRE%%';
    $this->tags[1]['name'] = 'Apellido';
    $this->tags[1]['tag'] = '%%APELLIDO%%';
    $this->tags[2]['name'] = 'Fecha de nacimiento';
    $this->tags[2]['tag'] = '%%FECHA_DE_NACIMIENTO%%';
    $this->tags[3]['name'] = 'Correo electrónico';
    $this->tags[3]['tag'] = '%%EMAIL%%';
    $this->tags[4]['name'] = 'Indicativo';
    $this->tags[4]['tag'] = '%%INDICATIVO%%';
    $this->tags[5]['name'] = 'Móvil';
    $this->tags[5]['tag'] = '%%TELEFONO%%';

    $sql = "SELECT name,alternativename from customfield "
            . "WHERE idContactlist in (" . $ids . ")"
            . "GROUP BY 1,2";
    $customfields = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
    $i = 6;
    foreach ($customfields as $cf) {
      $this->tags[$i]['name'] = $cf['name'];
      $this->tags[$i]['tag'] = '%%' . strtoupper($cf['alternativename']) . '%%';
      $i++;
    }

    return $this->tags;
  }

  public function findOneSms($idSms) {
    $this->sms = \Sms::findFirst(["conditions" => "idSms = ?0 and idSubaccount = ?1", "bind" => [0 => $idSms, 1 => $this->user->Usertype->idSubaccount]]);
    $this->modelOneSms();
  }

  public function modelOneSms() {

    $sms = array();
    $sms['idSms'] = $this->sms->idSms;
    $sms['idSmsCategory'] = $this->sms->idSmsCategory;
    $sms['idSubaccount'] = $this->sms->idSubaccount;
    $sms['logicodeleted'] = $this->sms->logicodeleted;
    $sms['notification'] = $this->sms->notification;
    $sms['email'] = $this->sms->email;
    $sms['name'] = $this->sms->name;
    $sms['startdate'] = $this->sms->startdate;
    $sms['massage'] = $this->sms->massage;
    $sms['confirm'] = $this->sms->confirm;
    $sms['target'] = $this->sms->target;
    $sms['type'] = $this->sms->type;
    $sms['created'] = $this->sms->created;
    $sms['updated'] = $this->sms->updated;
    $sms['status'] = $this->sms->status;
    $sms['receiver'] = $this->sms->receiver;
    $sms['sent'] = $this->sms->sent;
    $sms['total'] = $this->sms->total;
    $sms['advancedoptions'] = $this->sms->advancedoptions;
    $sms['divide'] = $this->sms->divide;
    $sms['sendingTime'] = $this->sms->sendingTime;
    $sms['quantity'] = $this->sms->quantity;
    $sms['timeFormat'] = $this->sms->timeFormat;
    $sms['dateNow'] = $this->sms->dateNow;
    $sms['gmt'] = $this->sms->gmt;
    $sms['originalDate'] = $this->sms->originalDate;
    $sms['sendpush'] = $this->sms->sendpush;
    $sms['createdBy'] = $this->sms->createdBy;
    $sms['updatedBy'] = $this->sms->updatedBy;
    if($this->sms->morecaracter == 1){
        $sms['morecaracter'] = true;    
    }else{
        $sms['morecaracter'] = false;
    }
    
    
    $this->sms = $sms;
  }

  public function getSms() {
    return $this->sms;
  }

  public function getCountContactsValidate($arrIdContact, $switchrepeated) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $idAccount = $this->user->Usertype->Subaccount->idAccount;
    $arrayAccounts = ['49',49,'101',101,'1387',1387];
    //if($idAccount == 49 || $idAccount == '49' || $idAccount == 101 || $idAccount == '101'){
    if(in_array($idAccount, $arrayAccounts)){
        $contact = 0;
        if(!empty($this->where)){            
         $ids = implode(',',$arrIdContact);          
         //$contact = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist IN ({$this->where}) AND Cxcl.idContact IN($idss)"]);
         $contact = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist IN ({$this->where}) "]); 
        }                        
    }else{
         if ($switchrepeated){
          $where = array("idContact" => ['$in' => $arrIdContact],
              "phone" => ['$nin' => ["", null, "null"]],
              "indicative" => ['$nin' => ["", null, "null"]],
              "blockedPhone" => ['$in' => ["", null, "null"]]);
          $command = new \MongoDB\Driver\Command([
              'aggregate' => 'contact',
              'pipeline' => [
                  ['$match' => $where],
                  ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
              ],
              'allowDiskUse' => true
          ]);
          $contact = $manager->executeCommand('aio', $command)->toArray();
          $contact = count($contact[0]->result);
        } else {
          $repeatedIdContact = array();
          $repeatedIdContactReport = array();
          $repeatedIdContact = array_count_values($arrIdContact);
            
          $totalRepeated= 0;
          foreach ($repeatedIdContact as $k=>$v) {
              while($v>1){
                $repeatedIdContactReport[] = $k; 
                $v--;
            }
          }
    
          $where = array("idContact" => ['$in' => $arrIdContact],
              "phone" => ['$nin' => ["", null, "null"]],
              "indicative" => ['$nin' => ["", null, "null"]],
              "blockedPhone" => ['$in' => ["", null, "null"]]);
          $contact = \Contact::count(array($where));
          $repContact = count($repeatedIdContactReport);
          $contact = $contact + $repContact ;
        }   
    }
    return $contact;
  }

  public function getCountContactsSegment($arrIdSegment) {

    $segment = \Segment::

            $where = array("idContact" => ['$in' => $arrIdContact], "phone" => ['$nin' => ["", null, "null"]], "indicative" => ['$nin' => ["", null, "null"]], "blockedPhone" => ['$in' => ["", null, "null"]]);
    $contact = \Contact::count(array($where));
    return $contact;
  }

  public function getFisrtContact($id, $type) {
    switch ($type) {
      case "contactlist":
        $cxcl = \Cxcl::findFirst(["conditions" => "idContactlist = ?0", "bind" => [0 => $id]]);
        if ($cxcl) {
          $contact = \Contact::find([["idContact" => (int) $cxcl->idContact]]);
        }
        $this->setContact($contact[0], $id);
        return $this->contact;
        break;
      case "segment":
        $segment = \Segment::find([["idSegment" => (int) $id]]);
        foreach ($segment[0]->contactlist as $contactlist) {
          
        }
        return $this->contact;
        break;
      default:
        break;
    }
  }

  public function setContact($cont, $idContactlist) {

    $customfields = \Cxc::find([["idContact" => (float) $cont->idContact]]);

    $this->contact['NOMBRE'] = $cont->name;
    $this->contact['APELLIDO'] = $cont->lastname;
    $this->contact['FEC_NAC'] = $cont->birthdate;
    $this->contact['CORREO'] = $cont->email;
    $this->contact['INDICATIVO'] = $cont->indicative;
    $this->contact['MOVIL'] = $cont->phone;
    if ($customfields) {
      foreach ($customfields[0]->idContactlist[$idContactlist] as $customfield) {
        $this->contact[strtoupper($customfield['name'])] = $customfield['value'];
      }
    }
  }

  public function createSmsSend($data) {
    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms && $key->status==1) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más sms");
    }

    $smsloteform = new \SmsloteForm();
    $sms = new \Sms();

    $datenow = $data['datenow'];
    $timezone = $data['timezone'];
    if ($datenow) {
      $data["datesend"] = date('Y-m-d G:i:s', time());
    }

    if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
      throw new \InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
    }

    $smsController = new \SmsController();
    $dateStart = $smsController->validateDate($data["datesend"], $timezone);

    if (isset($data["email"])) {
      $email = explode(",", trim($data["email"]));
      if (!$data["notification"]) {
        $email = [];
      }
      $sms->email = $email;
    }

    if (!isset($data['receiver'])) {
      throw new \InvalidArgumentException("Debes agregar al menos un destinatario");
    }
    $receiver = json_encode($data['receiver']);
    $receiver = json_decode($receiver);
    $target = $this->getCountContactsApi($receiver);

    if ($target['counts'] > $amount) {
      throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms si nesesitas más saldo contacta al administrador");
    }

    $smsloteform->bind($data, $sms);
    if (!$smsloteform->isValid()) {
      foreach ($smsloteform->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $sms->target = $target['counts'];
    $sms->idSubaccount = $this->user->Usertype->idSubaccount;
    $sms->idSmsCategory = $data['idSmsCategory'];
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = \Phalcon\DI::getDefault()->get("typeSms")->contact;
    $sms->startdate = $dateStart;
    $sms->receiver = json_encode($data['receiver']);
    $sms->message = $data['message'];
    $sms->sent = $target['counts'];

    if ($this->user->api == true) {
      $sms->externalApi = 1;
    }

    \Phalcon\DI::getDefault()->get("db")->begin();
    (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    \Phalcon\DI::getDefault()->get("db")->commit();
    return array("message" => "Se programo el envio de sms", "sms" => $sms);
  }

  public function editSmsSend($idSms, $data) {
    $sms = \Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      throw new \InvalidArgumentException("El envio de sms no se encontró, por favor valida la información.");
    }

    if ($sms->status != 'scheduled' && $sms->status != 'draft') {
      throw new \InvalidArgumentException("El envio de sms no se encuentra programado, por favor valida la información.");
    }

    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms && $key->status==1) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más sms");
    }

    $smsform = new \SmsloteForm($sms);

    $datenow = $data['datenow'];
    $timezone = $data['timezone'];
    if ($datenow) {
      $data["datesend"] = date('Y-m-d G:i:s', time());
    }

    if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
      throw new \InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
    }

    $smsController = new \SmsController();
    $dateStart = $smsController->validateDate($data["datesend"], $timezone);

    if (isset($data["email"])) {
      $email = explode(",", trim($data["email"]));
      if (!$data["notification"]) {
        $email = [];
      }
      $sms->email = $email;
    }

    if (!isset($data['receiver'])) {
      throw new \InvalidArgumentException("Debes agregar al menos un destinatario");
    }

    $receiver = json_encode($data['receiver']);
    $receiver = json_decode($receiver);
    $target = $this->getCountContacts($receiver);

    if ($target['counts'] > $amount) {
      throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms si nesesitas más saldo contacta al administrador");
    }
    $smsform->bind($data, $sms);
    if (!$smsform->isValid()) {
      foreach ($smsform->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $sms->target = $target['counts'];
    $sms->idSmsCategory = $data['idSmsCategory'];
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->type = \Phalcon\DI::getDefault()->get("typeSms")->contact;
    $sms->startdate = $dateStart;
    $sms->receiver = json_encode($data['receiver']);
    $sms->message = $data['message'];
    $sms->sent = $target['counts'];

    \Phalcon\DI::getDefault()->get("db")->begin();
    (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
    }

    \Phalcon\DI::getDefault()->get("db")->commit();
    return ["message" => "Se ha editado el envio de sms", "sms" => $sms];
  }

  public function smsCancelAction($idSms) {
    $sms = \Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$sms) {
      throw new \InvalidArgumentException("No se encontró el sms, por favor valida la información");
    }

    if ($sms->status != 'scheduled' && $sms->status != 'draft') {
      throw new \InvalidArgumentException("El envio de sms no se encuentra programado, por favor valida la información.");
    }

    \Phalcon\DI::getDefault()->get("db")->begin();
    $sms->status = 'canceled';
    if (!$sms->update()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    \Phalcon\DI::getDefault()->get("db")->commit();

    return ["message" => "Se ha cancelado con exito el envio de sms"];
  }

  public function createSmsLote($data) {
    $error = "";
    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 1 && ($key->status == 1 || $key->status == '1')) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    
    if ($flag == false) {
      $error .= " \n -No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte";
    }
    
    $idSubaccount = $this->user->Usertype->idSubaccount;
    if($idSubaccount != "420" || $idSubaccount != 420){
        if ($amount <= 0  && $flag == true) {
          $error .= " \n -No tienes capacidad para enviar más sms";
        }
        $scheduled = \Sms::find([
                "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                "bind" => [0 => $idSubaccount]
        ]);
        $countTargetScheduled = 0;
        foreach($scheduled  as $sc){
            $countTargetScheduled += $sc->target;
        }
        $rest = $amount - $countTargetScheduled;
        if($rest < 0){
            $error .= "No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS";  
        }
    }

    $smsloteform = new \SmsloteForm();
    $sms = new \Sms();
    $smsloteVerified = new \Smslote();


    $datenow = $data['datenow'];
    $timezone = $data['timezone'];
    if ($datenow) {
      $data["datesend"] = date('Y-m-d G:i:s', time());
    }

    if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
      $error .= " \n -No puedes asignar un envio con una fecha y hora del pasado";
    }

    $smsController = new \SmsController();
    if (!$datenow) {
      $data["datasend"] = $smsController->validateDate($data["datesend"], $timezone);
    }
    $dateStart = $data["datasend"];

    if (isset($data["email"])) {
      $email = explode(",", trim($data["email"]));
      if (!$data["notification"]) {
        $email = [];
      }
      $sms->email = $email;
    }


    $ampersand = "&&";
    if (strpos($data["receiver"], $ampersand) !== false) {
      $receiver = explode($ampersand, $data["receiver"]);
    } else {
      $receiver = explode("\n", $data["receiver"]);
    }
    
    if (empty($receiver[0])) {
      $error .= " \n -Debes agregar al menos un destinatario";
    }
    
    if($idSubaccount != "420" || $idSubaccount != 420){
        if ((count($receiver) > $amount)&& $flag == true) {
          if(abs($amount)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $amount];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          $error .= " \n -Solo puedes hacer " . $tAvailable->totalAvailable . " envío(s) de sms. Si necesitas más saldo contacta al administrador";
        }
    }
    
    //Se realiza validaciones de los sms programados
    $balance = $this->validateBalance();
    $target = 0;
    if($balance['smsFindPending']){
      foreach ($balance['smsFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $scheduled = $target;
    $amount = $balance['balanceConsumedFind'][0]['amount'];
    unset($balance);
    $totalTarget =  $amount - $target;
    $target = $target + count($receiver);
    if($idSubaccount != "420" || $idSubaccount != 420){
        if($target>$amount && $flag == true){
          $target = $target - $amount;
          if(abs($totalTarget)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $totalTarget];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          $error .= " \n -No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}";
        }
    }

    unset($target);
    unset($scheduled);
    unset($amount);
    unset($totalTarget);
    unset($tAvailable);
    
    $smsloteform->bind($data, $sms);
    if (!$smsloteform->isValid()) {
      foreach ($smsloteform->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $sms->target = count($receiver);
    $sms->idSubaccount = $this->user->Usertype->idSubaccount;
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = \Phalcon\DI::getDefault()->get("typeSms")->lote;
    $sms->sent = count($receiver);
    if (strpos($data["receiver"], $ampersand) !== false) {
      $sms->receiver = str_replace($ampersand, " ", $data["receiver"]);
    }

    if ($this->user->api == true) {
      $sms->externalApi = 1;
    }
    if(isset($data["idNotificationY"])){
        $sms->idntfyanaconas = $data["idNotificationY"];
    }else{  
       $sms->idntfyanaconas = 0;
    }
    if(isset($data['sendpush'])){
      $sms->sendpush = $data['sendpush'];
    }else{
      $sms->sendpush = 0;
    }
    if(isset($data["morecaracter"])){
        
      if(is_null($data["morecaracter"])){
        $error .= " \n -El campo morecaracter no esta definido";
      }
     
      if($data["morecaracter"] == true){
        $sms->morecaracter = 1;  
      }else if($data["morecaracter"] == false){
        $sms->morecaracter = 0;    
      }
         
    }else{
       $sms->morecaracter = 0;  
    }

    if(isset($sms->sendpush)){
      if(($sms->sendpush == 1 || $sms->sendpush == true) && ( $sms->morecaracter == 1 || $sms->morecaracter == true)){
        $error .= " \n -Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres."; 
      }  
    }

    \Phalcon\DI::getDefault()->get("db")->begin();
    (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

    if($error){
      return ["message" => $error];
      \Phalcon\DI::getDefault()->get("db")->rollback();
    }
      
    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    } 

    $count = 0;
    foreach ($receiver as $key) {

      $arr = explode(";", $key);
      $flag = true;

      if (strstr($arr[0], "+")) {
        $flag = false;
        $error .= " \n -El código de país(sin el símbolo '+')";
      }
      /* if (strlen(trim($arr[1])) != 10 || !is_numeric($arr[1])) {
        $flag = false;
        } */

      // valida si el indicativo que ingresa es correcto con el pais
      $Country = \Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $arr[0]]]);
      if (!$Country) {
        $error .= " \n -No se encuentra el indicativo del pais, por favor verifique.";
      }

      // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
      $phone = substr($arr[1], 0, 3);
      $PhonePrefix = \PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phone]]);
      if (!$PhonePrefix) {
        $error .= " \n -Verifique que el número " . $arr[1] . " sea valido, de acuerdo al indicativo del país.";
      }        
      
      if ($arr[0] == "57" && strlen(trim($arr[1])) != 10 || !is_numeric($arr[1])) {
        $flag = false;
        $error .= " \n -Recuerde que el movil solo debe contener números";
      }
      if (mb_strlen(trim($arr[2]), 'UTF-8') > 160 && $sms->morecaracter == 0) {
        $flag = false;
        $error .= " \n -Recuerde que el contenido del mensaje sólo debe contener 160 carácteres";
      }else if(mb_strlen(trim($arr[2]), 'UTF-8') > 300 && $sms->morecaracter == 1) {
        $flag = false;
        $error .= " \n -Recuerde que el contenido del mensaje sólo debe contener 300 carácteres";
      }

      $smslote = new \Smslote();
      $smslote->idSms = $sms->idSms;
      $smslote->idAdapter = 3;
      $smslote->indicative = $arr[0];
      $smslote->phone = trim($arr[1]);
      $smslote->message = trim($arr[2]);
      
      if (count($arr) == 3 && $flag) {
        $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
        if($sms->morecaracter == 1 && in_array(mb_strlen(trim($arr[2]), 'UTF-8'), range(160, 300))){
         $smslote->messageCount = 2; 
        }else{
          $smslote->messageCount = 1; 
        } 
        $count++;
      } else  {
        $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->canceled;
        $smslote->messageCount = 0; 
      } 
        
      if (!$smslote->save()) {
        \Phalcon\DI::getDefault()->get("db")->rollback();
        foreach ($smslote->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

    }
    $sms->sent = $count;
    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    if ($count == 0) {
      if($error){
        return ["message" => $error];
      }
      \Phalcon\DI::getDefault()->get("db")->rollback();
      throw new \InvalidArgumentException("El envío debe contener al menos un destinatario valido");
      if($error){
        return ["message" => $error];
      }
    }

    \Phalcon\DI::getDefault()->get("db")->commit();

    return ["message" => "Se ha creado el lote de sms!", "sms" => $sms, "error" => $error];
  }

  public function getallsms($page, $data) {

    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
    $where = " ";
    $wherePhone = " ";
    $stringRegexPhone = "";
    if (isset($data['phoneNumber'])) {
      $stringRegexPhone = new \MongoRegex("/^{$data['phoneNumber']}/i");
    }

    if (isset($data['name']) && $data['name'] != "") {
      $where .= " AND sms.name LIKE '%{$data['name']}%' ";
    }

    if (isset($data['phoneNumber']) && $data['phoneNumber'] != "") {
      $idsSmsContact = " ";
      
      $smsContact = \Smsxc::find([['phone' => ['$regex' => $stringRegexPhone], 'idSubaccount' => $idSubaccount]]);
      $length = count($smsContact);
      if ($length > 0) {
        for ($i = 0; $i < $length; $i++) {
//          $idsSmsContact .= intval($smsContact[$i]->idSms) . ",";
          $idsSmsContact .= (int) $smsContact[$i]->idSms . ",";
        }
        $idsSmsContact2 = substr($idsSmsContact, 0, strlen($idsSmsContact) - 1);
        $wherePhone .= " LEFT JOIN 
                          smslote ON smslote.idSms = sms.idSms 
                         AND smslote.phone LIKE '%{$data['phoneNumber']}%'
                         LEFT JOIN 
                         (Select sms.idSms, sms.status
                            from
                          sms 
                         WHERE
                          sms.idSms IN ({$idsSmsContact2})) "
                . "ss ON ss.idSms = sms.idSms ";
        $where .= "  AND (ss.idSms is not NULL "
                . " OR smslote.idSms is not NULL)";
      } else {
        $wherePhone .= "  RIGHT JOIN
                          aio.smslote ON sms.idSms = smslote.idSms 
                        AND smslote.phone LIKE '%{$data['phoneNumber']}%'";
      }
    }

    if (isset($data['category']) && count($data['category']) >= 1) {
      $arr = implode(",", $data['category']);
      $where .= "  AND sms.idSmsCategory IN ({$arr})";
    }
    
    if (isset($data["smsStatus"]) && $data["smsStatus"] != "") {
      if($data["smsStatus"] == "allStatuses"){
         $where .= " AND sms.status IN ('draft','scheduled','sending','sent','canceled','paused') ";
        }else{
          $smsStatus = $this->translateStatusSmsEn($data["smsStatus"]);
          $where .= " AND  sms.status = '{$smsStatus}' ";
        }
    }
    
    //$where .= " AND mail.type = 'manual'";  
    if (isset($data['dateinitial']) && isset($data['dateend'])) {
      if ($data['dateinitial'] != "" && $data['dateend'] != "") {
        if (strtotime($data['dateend']) < strtotime($data['dateinitial'])) {
          throw new \InvalidArgumentException("La fecha final no puede ser inferior a la inicial");
        } else {
          $where .= " AND startdate BETWEEN '{$data['dateinitial']} 00:00:00' AND '{$data['dateend']} 23:59:59'";
        }
      }
    }
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    /*Consulta anterior comentada debido a que no trae la cantidad de envío solo la cantidad del target de la campaña*/
    /*$sql = "SELECT sms.* from sms {$wherePhone} "
            . " WHERE sms.idSubaccount = {$idSubaccount} AND sms.logicodeleted = 0 {$where}"
            . " ORDER BY sms.created DESC "
            . "  LIMIT {$limit} "
            . " OFFSET {$page}";*/
    //Consulta general
    
    
    $sqlGeneral = "SELECT
                      sms.idSms,
                      sms.name,
                      sms.idSmsCategory,
                      sms.idSubaccount,
                      sms.idAutomaticCampaign,
                      sms.idAutoresponder,
                      sms.logicodeleted,
                      sms.notification,
                      sms.email,
                      sms.startdate,
                      sms.message,
                      sms.confirm,
                      sms.target as targetInitial,
                      sms.type,
                      sms.created,
                      sms.updated,
                      sms.createdBy,
                      sms.updatedBy,
                      sms.status,
                      sms.receiver,
                      sms.sent as targetSent,
                      sms.total,
                      sms.advancedoptions,
                      sms.divide,
                      sms.sendingTime,
                      sms.quantity,
                      sms.timeFormat,
                      sms.dateNow,
                      sms.gmt,
                      sms.originalDate,
                      sms_category.name as categoria,
                      sms.sendpush
                    FROM
                      sms 
                        INNER join sms_category on sms.idSmsCategory = sms_category.idSmsCategory {$wherePhone}
                    WHERE
                      sms.idSubaccount = {$idSubaccount} AND 
                      sms.logicodeleted = 0 
                      {$where}
                    ORDER BY 
                      sms.created DESC
                    LIMIT {$limit}
                    OFFSET {$page}";
    
    $dataGeneral = \Phalcon\DI::getDefault()->get('db')->fetchAll($sqlGeneral);
    $arrayIdContact = array();
    $arrayIdLote = array();
    $arrayIdNull = array();
    $arrayDataMain = array();
    
    foreach ($dataGeneral as $key => $value) {
      if($value['type']=="contact" || $value['type']=="automatic"){
        //array_push($arrayIdContact, $value);
        
        /*$arrayFind = array(
            'conditions' => array(
                'idSms' => (string) $value['idSms']
            )
        );
        $totalSmsContact = \Smsxc::find($arrayFind);*/
        $countTotalSentContact = $value['targetSent'];
        $countTotalSmsContact = $value['targetInitial'];
        /*foreach ($totalSmsContact as $key2 => $value2) {
          if($value2->status=='sent'){
            $countTotalSentContact++;
          }
          $countTotalSmsContact++;
        }*/
        $value['target'] = $countTotalSmsContact;
        $value['sent'] = $countTotalSentContact;
        array_push($arrayDataMain, $value);
      }
      /*else if($value['type']=="lote"||$value['type']=="csv"){
        array_push($arrayIdLote, $value);
      }*/
      else{
        //array_push($arrayIdLote, $value);
        $sqlSmsLote = "SELECT
                        smslote.idSmslote,
                        smslote.idSms,
                        smslote.status,
                        smslote.response,
                        smslote.messageCount
                      FROM
                          smslote
                      WHERE
                          smslote.idSms = {$value['idSms']} 
                          ORDER BY 
                            smslote.idSms, smslote.idSmslote";
        $dataSmsLote = \Phalcon\DI::getDefault()->get('db')->fetchAll($sqlSmsLote);
        $countTotalSentLote = 0;
        $countTotalSmsLote = 0;
        $sumaSmslote = 0;
        foreach ($dataSmsLote as $key2 => $value2) {
          if($value2['status']=='sent'){
            $sumaSmslote = $sumaSmslote + $value2['messageCount'];            
            $countTotalSentLote++;
          }
          $countTotalSmsLote++;
        }
        $totalSmsLote = $dataSmsLote[0];
        $value['target'] = $countTotalSmsLote;
        $value['sent'] = $countTotalSentLote;
        $value['messageCount'] = $sumaSmslote;
        unset($sumaSmslote);
        array_push($arrayDataMain, $value);
      }
    }
    //$arrayDataMain = array();
    //Validamos si los arrays de tipos tienen registros
    /*if(count($arrayIdContact)>0){
      $totalSmsContact = 0;
      foreach ($arrayIdContact as $key => $value) {
        $arrayFind = array(
            'conditions' => array(
                'idSms' => (string) $value['idSms']
            )
        );
        $totalSmsContact = \Smsxc::find($arrayFind);
        $countTotalSent = 0;
        $countTotalSms = 0;
        foreach ($totalSmsContact as $key2 => $value) {
          if($value->status=='sent'){
            $countTotalSent++;
          }
          $countTotalSms++;
        }
        $arrayIdContact[$key]['target'] = $countTotalSms;
        $arrayIdContact[$key]['sent'] = $countTotalSent;
        array_push($arrayDataMain, $arrayIdContact[$key]);
      }
    }*/
    /*if(count($arrayIdLote)>0){
      foreach ($arrayIdLote as $key => $value) {
        $sqlSmsLote = "SELECT
                        smslote.idSmslote,
                        smslote.idSms,
                        smslote.status,
                        smslote.response
                      FROM
                          smslote
                      WHERE
                          smslote.idSms = {$value['idSms']}";
        $dataSmsLote = \Phalcon\DI::getDefault()->get('db')->fetchAll($sqlSmsLote);
        $countTotalSent = 0;
        $countTotalSms = 0;
        foreach ($dataSmsLote as $key2 => $value) {
          if($value['status']=='sent'){
            $countTotalSent++;
          }
          $countTotalSms++;
        }
        $totalSmsLote = $dataSmsLote[0];
        $arrayIdLote[$key]['target'] = $countTotalSms;
        $arrayIdLote[$key]['sent'] = $countTotalSent;
        array_push($arrayDataMain, $arrayIdLote[$key]);
      }
    }*/

    $sql2 = "SELECT 
              count(sms.idSms) as total 
            FROM 
              sms {$wherePhone}
            WHERE 
              sms.idSubaccount = {$idSubaccount} AND 
              sms.logicodeleted = 0 
            {$where}";
//    $data1 = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    $data1 = $arrayDataMain;
    $totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql2);

    return $this->modelData($data1, $totals);
  }

  public function modelData($data, $totals) {
    $arrReturn = array("total" => (int) $totals[0]["total"], "total_pages" => ceil((int) $totals[0]["total"] / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($data as $key => $value) {
      if($value['type'] == "contact" || $value['type'] == "automatic"){
        $messageCount = $this->findMessageCount($value['idSms'], $value['type']);
      } else {
        if($value['status'] != 'sent'){
          $messageCount = 0;
        }else{
          $messageCount = $value['messageCount'];
        }
        
      }

      $arr[$key] = array("idSms" => $value['idSms'],
          "idSmsCategory" => $value['idSmsCategory'],
          "categoria" => $value['categoria'],
          "idSubaccount" => $value['idSubaccount'],
          "logicodeleted" => $value['logicodeleted'],
          "notification" => $value['notification'],
          "status" => $value['status'],
          "type" => $value['type'],
          "birthdate" => $this->isBirthdaySms($value['idSms']),
          "created" => date('d/m/Y g:i a', $value['created']),
          "updated" => date('d/m/Y g:i a', $value['updated']),
          "createdBy" => $value['createdBy'],
          "updatedBy" => $value['updatedBy'],
          "sent" => $value['sent'],
          "total" => $value['total'],
          "target" => $value['target'],
          "startdate" => $value['startdate'],
          "email" => $value['email'],
          "name" => $value['name'],
          "sendpush" => $value['sendpush'],
          "messageCount" => $messageCount);
      unset($data[$key]);
      array_values($data);
    }
    $arrReturn["items"] = $arr;

    return $arrReturn;
  }

  public function createSmsEncrypted($data) {
    $error = "";
    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 1 && ($key->status == 1 || $key->status == '1')) {
        $flag = true;
        $amount = $key->amount;
      }
    }

    if ($flag == false) {
      $error .= " \n -No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte";
    }
    
    $idSubaccount = $this->user->Usertype->idSubaccount;
    if($idSubaccount != "420" && $idSubaccount != 420){
        if ($amount <= 0 && $flag == true) {
          $error .= " \n -No tienes capacidad para enviar más sms";
        }
        $scheduled = \Sms::find([
                "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                "bind" => [0 => $idSubaccount]
        ]);
        $countTargetScheduled = 0;
        foreach($scheduled  as $sc){
            $countTargetScheduled += $sc->target;
        }
        $rest = $amount - $countTargetScheduled;
        if($rest < 0){
            $error .= "No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS";   
        }
    }

    $smsloteform = new \SmsloteForm();
    $sms = new \Sms();
    $smsloteValidate = new \Smslote();

    $datenow = $data['datenow'];
    $timezone = $data['timezone'];
    if ($datenow) {
      $data["datesend"] = date('Y-m-d G:i:s', time());
    }

    if (strtotime($data["datesend"]) < strtotime("now") && !$datenow) {
      $error .= " \n -No puedes asignar un envio con una fecha y hora del pasado";
    }

    /* $smsController = new \SmsController();
      $dateStart = $smsController->validateDate($data["datesend"], $timezone); */
    $dateStart = $data["datesend"];

    if (isset($data["email"])) {
      $email = explode(",", trim($data["email"]));
      if (!$data["notification"]) {
        $email = [];
      }
      $sms->email = $email;
    }

    $receiver = $smsloteValidate->validateApiReciver($data["receiver"], $data["indicative"]);

    if (empty($receiver)) {
      $error .= " \n -Debes agregar al menos un destinatario";
    }
    //$receiver = $this->validateReceiver($receiver);
    if($idSubaccount != "420" && $idSubaccount != 420){
        if ((count($receiver) > $amount)&& $flag == true) {
          if(abs($amount)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $amount];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          $error .= " \n -Solo puedes hacer " . $tAvailable->totalAvailable . " envío(s) de sms. Si nesesitas más saldo contacta al administrador";
        }
    }
    
    $smsloteform->remove("receiver");
    
    //Se realiza validaciones de los sms programados
    $balance = $this->validateBalance();
    $target = 0;
    if($balance['smsFindPending']){
      foreach ($balance['smsFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $scheduled = $target;
    $amount = $balance['balanceConsumedFind'][0]['amount'];
    unset($balance);
    $totalTarget =  $amount - $target;
    $target = $target + count($receiver);
    if($idSubaccount != "420" && $idSubaccount != 420){
        if(($target>$amount) && $flag == true){
          $target = $target - $amount;
          if(abs($totalTarget)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $totalTarget];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          $error .= " \n -No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}";
        }
    }
    unset($target);
    unset($scheduled);
    unset($amount);
    unset($totalTarget);
    unset($tAvailable);
    
    $smsloteform->bind($data, $sms);
    if (!$smsloteform->isValid()) {
      foreach ($smsloteform->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $sms->target = count($receiver);
    $sms->idSubaccount = $this->user->Usertype->idSubaccount;
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = \Phalcon\DI::getDefault()->get("typeSms")->encrypted;
    $sms->startdate = $dateStart;
    $sms->sent = count($receiver);

    if ($this->user->api == true) {
      $sms->externalApi = 1;
    }
    if(isset($data["morecaracter"])){
        
      if(is_null($data["morecaracter"])){
        $error .= " \n -El campo morecaracter no esta definido";
      }       
      if($data["morecaracter"] == true){
        $sms->morecaracter = 1;  
      }else if($data["morecaracter"] == false){
        $sms->morecaracter = 0;    
      }
         
    }else{
       $sms->morecaracter = 0;  
    }
    
    if(isset($data['sendpush'])){
      $sms->sendpush = $data['sendpush'];
    }else{
      $sms->sendpush = 0;
    }

    if(isset($sms->sendpush)){
      if(($sms->sendpush == 1 || $sms->sendpush == true) && ( $sms->morecaracter == 1 || $sms->morecaracter == true)){
        $error .= " \n -Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres."; 
      }  
    }
    
    \Phalcon\DI::getDefault()->get("db")->begin();
    (($datenow) ? $sms->startdate = date('Y-m-d H:i:s', time()) : "");

    if($error){
      return ["message" => $error];
      \Phalcon\DI::getDefault()->get("db")->rollback();
    }

    if (!$sms->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $count = 0;
    $error .= " -El envío debe contener al menos un destinatario valido";
    
    foreach ($receiver as $key) {
      $flag = true;

      // valida si el indicativo que ingresa es correcto con el pais
      $Country = \Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $key['indicative']]]);
      if (!$Country) {
        $error .= " \n -No se encuentra el indicativo del pais, por favor verifique.";
      }

      // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
      $phone = substr($key['phone'], 0, 3);
      $PhonePrefix = \PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phone]]);
      if (!$PhonePrefix) {
        $error .= " \n -Verifique que el número " . $key['phone'] . " sea valido, de acuerdo al indicativo del país.";
      }        

      if (strstr($data['indicative'], "+")) {
        $flag = false;
        $error .= "\n"." -El código de país(sin el símbolo '+')";
      }
      if ($data['indicative'] == "57" && strlen(trim($key['phone'])) != 10 || !is_numeric($key['phone'])) {
        $flag = false;
        $error .= "\n"." -Recuerde que el movil solo debe contener números";
      }
      if (mb_strlen(trim($key['message']), 'UTF-8') > 160 && $sms->morecaracter == 0) {
        $flag = false;
        $error .= "\n"." -Recuerde que el contenido del mensaje sólo debe contener 160 carácteres";
      }else if(mb_strlen(trim($key['message']), 'UTF-8') > 300 && $sms->morecaracter == 1) {
        $flag = false; 
        $error .= "\n"." -Recuerde que el contenido del mensaje sólo debe contener 300 carácteres";
      }

      if (count($key) == 2 && $flag) {
        $smslote = new \Smslote();
        $smslote->idSms = $sms->idSms;
        $smslote->idAdapter = 3;
        $smslote->indicative = $data['indicative'];
        $smslote->phone = trim($key['phone']);
        $smslote->message = trim($key['message']);
        $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
        if($sms->morecaracter == 1 && in_array(mb_strlen(trim($key["message"]), 'UTF-8'), range(160, 300))){
          $smslote->messageCount = 2; 
        }else{
          $smslote->messageCount = 1; 
        } 
        if (!$smslote->save()) {
          \Phalcon\DI::getDefault()->get("db")->rollback();
          foreach ($smslote->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $count++;
      }
    }
    if ($count == 0) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      if($error){
        return ["message" => $error];
      }
      throw new \InvalidArgumentException("El envío debe contener al menos un destinatario valido");
    }

    \Phalcon\DI::getDefault()->get("db")->commit();

    return ["message" => "Se ha creado el envió de sms!", "sms" => $sms];
  }

  public function getDetailSms($idSms) {
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $idSms]]);

    $sendTotal = null;
    $noSendTotal = null;
    $total = null;

    if ($sms->type == "encrypted" || $sms->type == "csv") {
      $sql = "SELECT count(*) AS sendTotal FROM smslote WHERE idSms = {$idSms} AND status = 'sent'";
      $sendTotal = $this->db->fetchAll($sql);
      $sql2 = "SELECT count(*) AS noSendTotal FROM smslote WHERE idSms = {$idSms} AND status = 'undelivered'";
      $noSendTotal = $this->db->fetchAll($sql2);
      $sql3 = "SELECT count(*) AS total FROM smslote WHERE idSms = {$idSms} ";
      $total = $this->db->fetchAll($sql3);
    }

    $result = array(
        "idSms" => $idSms,
        "sendSmsAmount" => $sendTotal[0]['sendTotal'],
        "notSendSmsAmount" => $noSendTotal[0]['noSendTotal'],
        "total" => $total[0]['total']
    );

    return $result;
  }

  public function getDetailSmsLote($data) {
    if(empty($data['idsSms']) || !isset($data['idsSms'])){
       throw new \InvalidArgumentException("No ha ingresado ningún idsms a consultar"); 
    }
    $sendTotal = null;
    $noSendTotal = null;

    $sql = "SELECT count(*) AS sendTotal FROM smslote WHERE idSms IN ({$data['idsSms']}) AND status = 'sent'";
    $sendTotal = $this->db->fetchAll($sql);
    $sql2 = "SELECT count(*) AS noSendTotal FROM smslote WHERE idSms IN ({$data['idsSms']}) AND status = 'undelivered'";
    $noSendTotal = $this->db->fetchAll($sql2);   

    $result = array(
        "sendSmsAmount" => $sendTotal[0]['sendTotal'],
        "notSendSmsAmount" => $noSendTotal[0]['noSendTotal']
    );
    unset($sendTotal);
    unset($noSendTotal);
    return $result;
  }
  /**
   * @author Felipe Garcia
   * @description: Verifica si la subcuenta tiene asignado el servicio de SMS doble-via
   * @return boolean
   * @throws \InvalidArgumentException
   */
  public function verifyServiceTwoway() {
    $flag = false;
    /*
     * Valida si la subcuenta tiene el servicio
     */
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms_two_way && $key->status==1) {
        $flag = true;
      }
    }
    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    return $flag;
  }

  public function changeStatus($idSms, $status) {
    $Sms = \Sms::findFirst(array(
                "conditions" => "idSms = ?0",
                "bind" => array(0 => $idSms)
    ));
    if (!$Sms) {
      throw new \InvalidArgumentException("El envio de sms no se encontró, por favor valida la información.");
    }
    if ($status == 'scheduled') {
      $Sms->confirm = 1;
    }
    $Sms->status = $status;
    if (!$Sms->save()) {
      foreach ($Sms->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se logro crear el smslote {$message}");
    }
    return array("message" => "Se cambio el estado correctamente.");
  }

    /**
   * metodo que me permite evaluar si el mensaje sms es creado 
   * apartir de una autorespuesta... para agregar el campo birthdate
   * en el json, en otra funcion declarada en la parte superior.
   * @param type $id
   * @return boolean
   */
  public function isBirthdaySms($id) {
    $result = false;
    $sms = \Sms::findFirst([
                "conditions" => "idSms = ?0",
                "bind" => [0 => $id]
    ]);
    if (isset($sms->idAutoresponder) && !($sms->idAutoresponder == null)) {
      $autoresponder = \Autoresponder::findFirst(array(
                  "conditions" => "idAutoresponder = ?0",
                  "bind" => array($sms->idAutoresponder))
      );
      $result = ($autoresponder->birthdate) ? true : false;
      return $result; //como es uno o cero dira si es verdadero o falso
    } else {
      return false;
    }
  }

  public function createSingleSms($data) {
    $saxs = null;
    $flag = true;
    //$this->logger->log("[INITWRAPPER]:" . date("H:i:s"));
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->Services->name == "Sms" && ($key->status == 1 || $key->status == '1')) {
        $saxs = $key;
      }
    }
    //$this->logger->log("[ISSERSAXS]:" . date("H:i:s"));
    if (!isset($saxs)) {
      //throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      $flag = false;
      return array("message" => "No tienes asignado el servicio de SMS, si lo deseas adquirir comunicate con soporte");
    }
    $idSubaccount = $this->user->Usertype->idSubaccount;
    //$this->logger->log("[ISSERSAXS-AMOUNT]:" . date("H:i:s"));
    if($idSubaccount != 420 || $idSubaccount != "420"){//SI LA SUBCUENTA ES GALIAS (420) PERMITE SEGUIR ASI NO TENGA SALDO
        if (($saxs->amount == 0 || $saxs->amount < 0) && $flag == true) {
          //throw new \InvalidArgumentException("No tienes capacidad para enviar más SMS");
          $flag = false;
          return array("message" => "No tienes capacidad para enviar más SMS, tu saldo actual es de ".$saxs->amount);
        }
        $scheduled = \Sms::find([
                "conditions" => "status in ('scheduled', 'sending') AND idSubaccount = ?0",
                "bind" => [0 => $idSubaccount]
        ]);
        $countTargetScheduled = 0;
        foreach($scheduled  as $sc){
            $countTargetScheduled += $sc->target;
        }
        $rest = $saxs->amount - $countTargetScheduled;
        if($rest < 0 && $flag == true){
            $flag = false;
            return array("message" => "No tienes saldo para realizar el envio, tienes envios programados por un total de ".$countTargetScheduled." SMS");   
        }
    }
    if($idSubaccount != 420 || $idSubaccount != "420"){//SI LA SUBCUENTA ES GALIAS (420) PERMITE SEGUIR ASI NO TENGA SALDO
    //verificamos que un single sea mayor a el amount disponible
        if ((count($data["receiver"]["phone"]) > $saxs->amount)) {
          if(abs($saxs->amount)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $saxs->amount];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          //throw new \InvalidArgumentException("Solo puedes hacer " . $tAvailable->totalAvailable . " envíos de sms si nesesitas más saldo contacta al administrador");
          $flag = false;
          return array("message" => "Solo puedes hacer ".$tAvailable->totalAvailable." envios de sms si necesitas mas saldo contacta al administrador");
        }
    }
    //Se re//Se realiza validaciones de los sms programados
    $balance = $this->validateBalance();
    $target = 0;
    if($balance['smsFindPending']){
      foreach ($balance['smsFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $scheduled = $target;
    $amount = $balance['balanceConsumedFind'][0]['amount'];
    unset($balance);
    $totalTarget =  $amount - $target;
    $target = $target + 1;
    if($idSubaccount != 420 || $idSubaccount != "420"){//SI LA SUBCUENTA ES GALIAS (420) PERMITE SEGUIR ASI NO TENGA SALDO
        if(($target>$amount) && $flag == true){
          $target = $target - $amount;
          if(abs($totalTarget)){
            $tAvailable = (object) ["totalAvailable" => 0];
          } else {
            $tAvailable = (object) ["totalAvailable" => $totalTarget];
          }
          $this->sendmailnotsmsbalance($tAvailable);
          //throw new \InvalidArgumentException("No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}");
          $flag = false;
          return array("message" => "No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}");
        }
    }
    unset($target);
    unset($scheduled);
    unset($amount);
    unset($totalTarget);
    unset($tAvailable);
    
     //Validamos Campos
    if(isset($data["morecaracter"])){
               
       if($data["morecaracter"] == true){
         $this->validatemore = true;
       }else if($data["morecaracter"] == false){
         $this->validatemore = false;
       }         
    }else{  
       $this->validatemore = false;
    }
    $validateResponse = $this->validateSingleSms($data);
    
    if ($validateResponse["response"] != 1) {
      return $validateResponse["response"];
      return;
    }
    
    //$this->logger->log("[CREATESMS]:" . date("H:i:s"));
    $sms = new \Sms();
    $smsform = new \SmsForm();
    $smsform->bind($data, $sms);

    $sms->idSubaccount = $this->user->Usertype->idSubaccount;
    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $sms->confirm = 1;
    $sms->logicodeleted = 0;
    $sms->type = "single";
    $sms->startdate = date('Y-m-d G:i:s', time());
    $sms->dateNow = 1;
    $sms->sent = 1;
    $sms->total = 1;
    $sms->target = 1;
    $sms->externalApi = 1;
    $sms->sendpush = 0;

    if (!isset($data["receiver"])) {
      //throw new \InvalidArgumentException("Debe haber un destinatario para el envío");
      $flag = false;
      return array("message" => "Debe haber un destinatario para el envío");
    }
    //$this->logger->log("[CREATESMS-RECEIVER]:" . date("H:i:s"));
    $sms->receiver = json_encode($data["receiver"]);
    if(isset($data["notification"])){
      if($data["notification"]==1 && $data["email"]){
        $sms->notification = $data["notification"];
        $sms->email =$data["email"];
      }else{
        $sms->notification =0;
        $sms->email ="";
      }
    }

    if(isset($data["morecaracter"])){
      if(is_null($data["morecaracter"])){
        //throw new \InvalidArgumentException("El campo morecaracter no esta definido");
        $flag = false;
        return array("message" => "El campo morecaracter no esta definido");
      }       
      if($data["morecaracter"] == true){
        $sms->morecaracter = 1;
      }else if($data["morecaracter"] == false){
        $sms->morecaracter = 0;
      }         
    }else{
      $sms->morecaracter = 0;  
    }

    if (!$smsform->valid() && !$sms->save()) {
      foreach ($sms->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //$this->logger->log("[CREATESMS-LOTE]:" . date("H:i:s"));
    $smslote = new \Smslote();
    $smslote->idSms = $sms->idSms;
    $smslote->idAdapter = 3;
    $smslote->indicative = $data["receiver"]["indicative"];
    $smslote->phone = trim($data["receiver"]["phone"]);  
    $smslote->message = trim($data["receiver"]["message"]);
    $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    if (!$smslote->save()) {
      foreach ($smslote->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //$this->logger->log("[PRE-SINGLEMESSAGE]:" . date("H:i:s"));
    $response = $this->sendSingleMessage($this->validReceiver($smslote));
    //$this->logger->log("[POST-SINGLEMESSAGE]:" . date("H:i:s"));
    if(isset($response->messages[0]->status->name)){
      $smslote->response = $response->messages[0]->status->name;
    }
    //$this->logger->log("[CREATESMS-VALIDATION]:" . date("H:i:s"));
    if ($smslote->response == "PENDING_ENROUTE") {
      $smslote->status = \Phalcon\DI::getDefault()->get("statusSms")->sent;
      //$sub = $this->user->Usertype->Subaccount->idSubaccount;
      //$arr = array("28","792");
      if($sms->morecaracter == 1 && in_array(mb_strlen(trim($data["receiver"]["message"]), 'UTF-8'), range(160, 300))){
      //if(in_array($sub,$arr) && strlen($data["receiver"]["message"]) > 160){
        $smslote->messageCount = 2;
      }else{
        $smslote->messageCount = 1;  
      } 
    } else {
      $smslote->status = "undelivered";
      $smslote->messageCount = 0; 
    }

    $sms->status = \Phalcon\DI::getDefault()->get("statusSms")->sent;
    //$this->logger->log("[CREATESMS-SAVE]:" . date("H:i:s"));
    $sms->update();
    $smslote->save();
    //$this->logger->log("[RECOUNT-SAXS]:" . date("H:i:s"));
    $saxs->amount = (Int) $saxs->amount - $smslote->messageCount;
    $saxs->save();
    
    if($sms->email){
      $sendMailNot= new \Sigmamovil\General\Misc\SmsEmailNotification();
      $sendMailNot->sendMailNotification($sms);
    }
    
    //$this->recalculateSaxsBySms($sms->idSubaccount);
    //$this->logger->log("[RECOUNT-SAXS-FINISH]:" . date("H:i:s"));
    return array(
        "idSms" => $sms->idSms,
        "messageId" => $smslote->idSmslote,
        "to" => "{$smslote->indicative}{$smslote->phone}",
        "status" => $smslote->status,
        "smsCount" => $smslote->messageCount
    );
  }
  
  public function validateSingleSms($data) {
    
    $flag = true;
    $arrResponse = array();
    if (!isset($data["name"]) || $data["name"] == "") {
      $arrResponse["validateName"] = "Nombre de campaña es obligatiorio";
      $flag = false;
    }
    if (!isset($data["idSmsCategory"]) || $data["idSmsCategory"] == "") {
      $arrResponse["validateIdSmsCategory"] = "IdSmsCategory es obligatiorio";
      $flag = false;
    }

    if (isset($data["receiver"]) || $data["receiver"] != "") {
      // valida si el indicativo que ingresa es correcto con el pais
      $Country = \Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $data['receiver']['indicative']]]);
      if (!$Country) {
        $arrResponse["validateReceiverIndicative"] = "No se encuentra el indicativo del pais, por favor verifique.";
        $flag = false;
      }
      // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
      $phone = substr($data['receiver']['phone'], 0, 3);
      $PhonePrefix = \PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phone]]);
      if (!$PhonePrefix) {
        $arrResponse["validateReceiverPhone"] = "Verifique que el número " . $data['receiver']['phone'] . " sea valido, de acuerdo al indicativo del país. ";
        $flag = false;
      }
      if (!isset($data["receiver"]["indicative"]) ||
              $data["receiver"]["indicative"] == "" ||
              strstr($data["receiver"]["indicative"], "+") ||
              !is_numeric($data["receiver"]["indicative"])) {
        $arrResponse["validateReceiverIndicative"] = "Indicativo no esta definido y-o sintaxis es incorrecta.";
        $flag = false;
      }
      if (!isset($data["receiver"]["phone"]) ||
              $data["receiver"]["phone"] == "" ||
              !is_numeric($data["receiver"]["phone"]) ||
              strlen(trim($data["receiver"]["phone"])) != 10
      ) {
        $arrResponse["validateReceiverPhone"] = "Telefono no esta definido y-o sintaxis es incorrecta ";
        $flag = false;
      }      
      if (strlen($data["receiver"]["message"]) > 160  && $this->validatemore == false){ 
        if (!isset($data["receiver"]["message"]) || $data["receiver"]["message"] == "" ) {
            $arrResponse["validateReceiverMessage"] = "Mensaje es obligatorio y-o Excede numero de caracteres ";
            $flag = false;
        }
      }else if(strlen($data["receiver"]["message"]) > 300  && $this->validatemore == true){
        if (!isset($data["receiver"]["message"]) || $data["receiver"]["message"] == "" ) {
            $arrResponse["validateReceiverMessage"] = "Mensaje es obligatorio y-o Excede numero de caracteres ";
            $flag = false;
        }   
      }
    } else {
      $arrResponse["validateReceiver"] = "Receiver es obligatorio ";
      $flag = false;
    }
    
    if(isset($data["notification"])){
      if($data["notification"] && (isset($data["email"]) || $data["email"] != "")){
        if(filter_var($data["email"], FILTER_VALIDATE_EMAIL)==false){
          $arrResponse["validateEmail"] = "Email tiene sintaxis es incorrecta ";
          $flag = false;
        } 
      }
    }

    if ($flag) {
      return [
          "response" => true
      ];
    }
    return [
        "response" => $arrResponse
    ];
  }

  /**
   * Para valida el Destinatario
   * @param type $receiver
   * @return type
   */
  private function validReceiver($receiver) {
    if(mb_strlen(trim($receiver->message), 'UTF-8') > 160 && $this->validatemore == false ){
      throw new \InvalidArgumentException("El mensaje excede los 160 caracteres");  
    }else if(mb_strlen(trim($receiver->message), 'UTF-8') > 300 && $this->validatemore == true) {
      throw new \InvalidArgumentException("El mensaje excede los 300 caracteres");   
    }
    return array(
        "from" => "AIO-SMS",
        "to" => "{$receiver->indicative}{$receiver->phone}",
        "text" => $receiver->message
    );
  }

  private function sendSingleMessage($receiver) {
    $adapter = \Adapter::findFirst(array(
                "conditions" => "fname = ?0",
                "bind" => array("INFOBIP(SINGLESMS)")
    ));

    $apiSms = new \Sigmamovil\General\Misc\ApisSms(\Phalcon\DI::getDefault()->get('general')->keyjwt);
    return $apiSms->apiInfobip($receiver, $adapter);
  }

  public function recalculateSaxsBySms($idSubaccount) {
    $count = \Smsxc::count(array(
                "conditions" => array(
                    "idSubaccount" => (string) $idSubaccount,
                    "status" => "sent"
                )
    ));
    $sql = "CALL updateCountersSmsSaxs({$idSubaccount},{$count})";
    $this->db->execute($sql);
  }
  
  /**
   * @param type $idSms
   * @return type 
   */
  public function deleteVariousSmsLotes($idSms){
      
     $lotes = \Smslote::findByIdSms($idSms);
     
      foreach ($lotes as $value) {
          if ($value->delete() === false) {
                 var_dump("No se pudo eliminar el lote: \n");
                   $messages = $robot->getMessages();
                   foreach ($messages as $message) {
                       echo $message, "\n";
                   }
          } else {
              echo "El mensaje del lote de sms fue eliminado correctamente.";
          }
          
      }
      
    // luego de haber borrado los mensajes de lote es necesario 
    // hacer un borrado logico de la campaña de sms
    // y ademas colocarla en cancelada.
   
     $sms = \Sms::findFirstByIdSms($idSms);
     $sms->logicodeleted = time();
     $sms->updated = time();
     $sms->status = 'canceled'; //se cancela por que ya no se necesita...
     
     if ($sms->save() === false) {    
             var_dump("No se pudo modificar el sms: \n");
               $messages = $sms->getMessages();
               foreach ($messages as $message) {
                   echo $message, "\n";
               }
     } else {
          echo "Genial el sms fue modificado correctamente.";
     }
     
     return ["message" => "Se elimino la informacion adjuntada anteriormente"];
     
  }
  
  public function getsmscampaigndetail($data){
    if (!isset($data->idSms) || $data->idSms==""){
     throw new \InvalidArgumentException("No hay Id de Campaña para consultar");
    }
    $idSubaccount = $this->user->usertype->subaccount->idSubaccount;
    $modelManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $dataSmsCampaign = $modelManager->createBuilder()
            ->columns(["Sms.status as CampaignStatus",
                "Sms.idSms AS IdSms",
                "Sms.startdate AS Date",
                "Sms.name AS Nombre",
                "count(Smslote.idSmslote) as Sent"])
            ->from('Smslote')
            ->innerJoin("Sms", "Sms.idSms = Smslote.idSms")
            ->where("Sms.idSubaccount = {$idSubaccount} and Sms.idSms = {$data->idSms}")
            ->orderBy("Date DESC")
            ->getQuery()
            ->execute();
    foreach ($dataSmsCampaign as $key => $value) {
      $arrayDataSmsCampaign[] = array(
          "CampaignStatus" => $this->translateStatusSms($value['CampaignStatus']),
          "IdSms" => $value['IdSms'],
          "Date" => $value["Date"],
          "Nombre" => $value["Nombre"],
          "Sent" => $value["Sent"]
      );
    }
    $dataSmsCampaignDetail = $modelManager->createBuilder()
            ->columns(["Smslote.idSmslote as IdSmslote",
                       "Smslote.status as recordStatus",
                       "Smslote.message as Message",
                       "Smslote.phone AS Phone",
                       "Smslote.updated AS Date",
                       ])
            ->from('Smslote')
            ->where("Smslote.idSms= {$arrayDataSmsCampaign[0]["IdSms"]}")
            ->getQuery()
            ->execute();
     foreach ($dataSmsCampaignDetail as $key => $value) {
      $arrayDataSmsCampaignDetail[] = array(
          "IdSmsLote" => $value["IdSmslote"],
          "recordStatus" => $this->translateStatusSms($value['recordStatus']),
          "Phone" => $value['Phone'],
          "Message" => $value['Message'],
          "Date" => date('Y-m-d H:i:s',$value["Date"])
      );
    }    
    $arrayDataSmsCampaign["cellphones"]= $arrayDataSmsCampaignDetail;
   return $arrayDataSmsCampaign;
  }
  
  public function translateStatusSms($status) {
  $statusSpanish = "";
    switch ($status) {
      case "sent":
        $statusSpanish = "Enviado";
        break;
      case "canceled":
        $statusSpanish = "Cancelado";
        break;
      case "scheduled":
        $statusSpanish = "Programado";
        break;
      case "sending":
        $statusSpanish = "En proceso de envío";
        break;
      case "draft":
        $statusSpanish = "Borrador";
        break;
      case "undelivered":
        $statusSpanish = "No entregado";
        break;
    }
    return $statusSpanish;
    }
    
  public function validateBalance(){
    
    $date = date('Y-m-d h:i:s');
    $smsFindPending = \Smslote::query()
        ->columns(['Sms.idSms, SUM(Smslote.messageCount) AS target'])
        ->leftJoin('Sms','Sms.idSms = Smslote.idSms')
        ->where("Sms.idSubaccount = {$this->user->Usertype->subaccount->idSubaccount} AND Sms.status = 'scheduled' AND Sms.startdate >= '{$date}' ")
        ->execute();
    
    $balanceConsumedFind = \Saxs::find(array(
        'conditions' => 'idSubaccount = ?0 and idServices = ?1 and status= ?2',
        'bind' => array(
            0 => $this->user->Usertype->subaccount->idSubaccount,
            1 => 1,
            2 => 1
        ),
        'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

//    $answer = ['respuest'=>$smsFindPending->toArray()];
    $answer = ['smsFindPending'=>$smsFindPending->toArray(), 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];
    return $answer;
  }
  
  public function sendmailnotsmsbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 1 && $key->status==1) {
        $flag = true;
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
      $sendMailNot->sendSmsNotification($this->arraySaxs);
      
      return true;
  }
  
  public function translateStatusSmsEn($status) {
    $statusEnglish = "";
    switch ($status) {
      case "Enviado":
        $statusEnglish = "sent";
        break;
      case "Borrador":
        $statusEnglish = "draft";
        break;
      case "En proceso de Envío":
        $statusEnglish = "sending";
        break;
      case "Programado":
        $statusEnglish = "scheduled";
        break;
      case "Pausado":
        $statusEnglish = "paused";
        break;
      case "Cancelado":
        $statusEnglish = "canceled";
        break;
    }
    return $statusEnglish;
  }
  
  public function findMessageCount($idSms, $type){
    /*if($type == "lote" || $type == "csv" || $type == "single" || $type == "encrypted"){
      $sql = "SELECT IFNULL(SUM(messageCount),0) AS totalMessage FROM smslote WHERE status = 'sent' AND idSms = ".$idSms;
      return $this->db->fetchAll($sql)[0]["totalMessage"];
    }*/
    if($type == "contact" || $type == "automatic"){
      $collectionSmsXc = [[ '$match' => ['idSms' => (string) $idSms] ],[ '$group' => ['_id' => '$idSms', 'messageCount' => ['$sum' => '$messageCount']]]];
      $count1 = \Smsxc::aggregate($collectionSmsXc);
      if(isset($count1['result'][0]['messageCount'])){
        return $count1['result'][0]['messageCount'];
      } else {
        return 0;
      }
    }
  }

  public function validateReceiverCreatelote($receiver) {
    $flagValidate = false;
    // recorre el array de destinatarios
    for ($i = 0; $i < count($receiver); $i++) {

      $sms = explode(";", $receiver[$i]);
      // valida si el el destinatario se encuentra separado por 3 partes con un (;     
      $count = count($sms);
      if ($count > 3) {
        $flagValidate = true;
      }
      if ($flagValidate) {
        throw new \InvalidArgumentException("Hay algún destinatario con el formato erróneo, por favor verifique.");
      }
      // valida si el indicativo que ingresa es correcto con el pais
      $Country = \Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $sms[0]]]);
      if (!$Country) {
        throw new \InvalidArgumentException("No se encuentra el indicativo del pais, por favor verifique.");
      }

      $phone = str_replace(' ', '', $sms[1]);

      //valida solo numeros del celular ingresado
      if (ctype_digit($phone)) {
        // valido para numeros
      } else {
        throw new \InvalidArgumentException("Existe un número que contiene letras (" . $phone . "), verifique.");
      }

      // valida que el numero de digitos sea correcto
      $valor = mb_strlen($phone, 'UTF-8');
      if ($valor != 10) {
        throw new \InvalidArgumentException("La cantidad de dígitos del número " . $sms[1] . " es incorrecto, verifique.");
      }

      // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
      $phone = substr($phone, 0, 3);
      $PhonePrefix = \PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phone]]);
      if (!$PhonePrefix) {
        throw new \InvalidArgumentException("Verifique que el número " . $sms[1] . " sea valido, de acuerdo al indicativo del país.");
      }
    }
  }
}