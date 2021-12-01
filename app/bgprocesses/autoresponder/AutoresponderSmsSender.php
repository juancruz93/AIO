<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$autoresponderSmsSender = new AutoresponderSmsSender();
$autoresponderSmsSender->autoresponderSmsStart($id);

class AutoresponderSmsSender
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
      $createdBy;

  /**
   * AutoresponderSmsSender constructor.
   * @param array $idContactlist
   */
      public function __construct()
  {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }


  public function autoresponderSmsStart($idAutoresponder)
  {
    
    try {
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
      $this->createdBy = $autoresponder->createdBy;
      $days = $autoresponder->days;
      \Phalcon\DI::getDefault()->get('logger')->log("Dias de la autorespuesta {$days} Autoresponder{$autoresponder->idAutoresponder}");
      $arrayDays = explode(",", $days);
      $today = date('l');

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
      
      \Phalcon\DI::getDefault()->get('logger')->log("Entra en el searchTotalContacts Sms Autoresponder{$autoresponder->idAutoresponder}");
      $target = json_decode($autoresponder->target);
      \Phalcon\DI::getDefault()->get('logger')->log("Este es el target {$autoresponder->target} Sms Autoresponder{$autoresponder->idAutoresponder}");
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
      if($this->count > 0){
        
        $autoresponderSmsManager = new \Sigmamovil\General\Misc\AutoresponderSmsManager();
//      $autoresponderMailManager->setTargetMail($newTarget);
        $autoresponderSmsManager->insertSmsFromAutoresponder($autoresponder);
        Phalcon\DI::getDefault()->get('logger')->log("Se realizo la creación exitosa de Sms Autoresponder{$autoresponder->idAutoresponder}");
//      $this->db->commit();
//      $sql = "CALL updateCounters({$this->contactlist->idContactlist})";
//      $this->db->execute($sql);
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