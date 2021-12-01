
<?php

ini_set('memory_limit', '768M');

require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");
require_once(__DIR__ . "/../../general/misc/ContactManager.php");
require_once(__DIR__ . "/ApiDataValidation.php");

class InterpreterTarget {

  protected $data = array();
  protected $mail;
  protected $idContactlist = array();
  protected $idSegment = array();
  protected $inIdcontact = array();
  protected $contact = array();
  protected $totalContacts;
  protected $bulk;
  protected $offset = 0;
  protected $limit = 8000;
  protected $flag = true;
  public $automatic = false,
          $automaticNoClic = false,
          $automaticCampaign,
          $automaticCampaignConfiguration,
          $beforeStep,
          $negation,
          $automaticCampaignObj,
          $db,
          $route,
          $file,
          $i = 1,
          $notSend = 0,
          $field = FALSE;
  private $dataValidation;
  public $arrayDataBlocked = array();
  public $arrayDataBounced = array();
            function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->db = $di->get('db');
    $this->dataValidation = Phalcon\DI\FactoryDefault::getDefault()->get('dataValidation');
  }

  public function setBeforeStep($beforeStep) {
    $this->beforeStep = $beforeStep;
    return $this;
  }

  function setMail(Mail $mail) {
    $this->mail = $mail;
  }

  function getMail() {
    return $this->mail;
  }

  public function setAutomaticNoClic($automaticNoClic) {
    $this->automaticNoClic = $automaticNoClic;
    return $this;
  }

  public function setAutomaticObj($automaticCampaignObj) {
    $this->automaticCampaignObj = $automaticCampaignObj;
    return $this;
  }

  public function setAutomatic($automatic) {
    $this->automatic = $automatic;
    return $this;
  }

  public function setNegation($negation) {
    $this->negation = $negation;
    return $this;
  }

  public function setAutomaticCampaignConfiguration($automaticCampaignConfiguration) {
    $this->automaticCampaignConfiguration = $automaticCampaignConfiguration;
    return $this;
  }

  public function setAutomaticCampaign($automaticCampaign) {
    $this->automaticCampaign = $automaticCampaign;
    return $this;
  }

  public function searchTotalContacts() {
//    $route = \Phalcon\DI::getDefault()->get('path')->path . "tmp/tmptableinterprete target.csv";
    \Phalcon\DI::getDefault()->get('logger')->log("Entra en el searchTotalContacts Mail{$this->mail->idMail}");
    $target = json_decode($this->mail->target);
    \Phalcon\DI::getDefault()->get('logger')->log("Este es el target {$this->mail->target} --- Mail{$this->mail->idMail}");
    $this->db->begin();
    \Phalcon\DI::getDefault()->get('logger')->log("Aquí comenzó la transacción --- Mail{$this->mail->idMail}");
    $this->route = \Phalcon\DI::getDefault()->get('path')->path . "tmp/tmptableinterpretetarget.csv";
    \Phalcon\DI::getDefault()->get('logger')->log("Esta es la ruta donde se guarda el archivo --- Mail{$this->mail->idMail}");
    $this->file = fopen($this->route, "w");
    if (isset($target->filters) && count($target->filters) >= 1) {
      $whereAll = " WHERE 1 = 1 ";
      $condition = " AND ";
      if ($target->condition == "some" && count($target->filters) > 1) {
        $condition = " OR ";
      }
      if($target->filters){
        $flag = 0;
        $case = 0;
        $idMailLink = [];
        $idMailSelected = [];
        foreach ($target->filters as $key) {
          if (isset($key->inverted) && $key->inverted == true) {
            switch ($key->typeFilters) {
              case 1:
                $this->notSend = 1;
                $idMailSelected[] = (string) $key->mailSelected;
                $whereAll .= " {$condition} (idMail = {$key->mailSelected}) ";
                break;
              case 2:
                $this->notSend = 1;
                $idMailSelected[] = (string) $key->mailSelected;
                $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND open = 0) ";
                break;
              case 3:
                $flag = 1;
                $idMailSelected[] = (string) $key->mailSelected;
                $idMailLink[] = (string) $key->linkSelected;
                $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND idMailLink != {$key->linkSelected}) ";
                break;
            }
          } else {
            switch ($key->typeFilters) {
              case 1:
                $flag = 2;
                $case = 1;
                $idMailSelected[] = (string) $key->mailSelected;
                break;
              case 2:
                $flag = 2;
                $case = 2;
                $idMailSelected[] = (string) $key->mailSelected;
                break;
              case 3:
                $flag = 2;
                $case = 3;
                $idMailSelected[] = (string) $key->mailSelected;
                $idMailLink[] = (string) $key->linkSelected;
                break;
            }
          }
        }
      }
    }
    \Phalcon\DI::getDefault()->get('logger')->log("Salió del if de los filtros --- Mail{$this->mail->idMail}");
    $this->createTableTemporary($this->mail->idMail);
    \Phalcon\DI::getDefault()->get('logger')->log("Creó una tabla temporal y va a comenzar un while(true) --- Mail{$this->mail->idMail}");
    while ($this->flag) {
      $this->bulk = new \MongoDB\Driver\BulkWrite;
      $target = json_decode($this->mail->target);
      if($flag == 2){
        if($case == 1){
          $this->findIdContactAll($idMailSelected);
        }
        if($case == 2){
          $this->findIdContactOpen($idMailSelected);
        }
        if($case == 3){
          $this->findIdContactLink($idMailSelected, $idMailLink);
        }
        switch ($target->type) {
          case "contactlist":
            $this->getIdContaclist();
            $this->getAllCxclFilter();
            break;
          case "segment":
            $this->getIdSegment();
            $this->getAllIdContactSegmentFilter();
            break;
          default:
        }
        $this->getAllContact();
      } else {
        switch ($target->type) {
          case "contactlist":
            $this->getIdContaclist();
            $this->getAllCxcl();
            break;
          case "segment":
            $this->getIdSegment();
            $this->getAllIdContactSegment();
            break;
          default:
        }
        if(isset($target->filterSms)){
          $this->findFilterSms($target);
        }
        if (isset($target->filters) && count($target->filters) >= 1) {
          $this->getMxcByFilters($flag, $idMailLink, $idMailSelected);
        } else {
          if (isset($this->inIdcontact)) {
            $this->getAllContact();
          }
        }
        if (isset($target->filters) && count($target->filters) >= 1) {
          $this->getByMxcByFilters($whereAll);
        }
      }
      //
      $this->offset += $this->limit;
    }
    \Phalcon\DI::getDefault()->get('logger')->log("Procesó los procesó todos --- Mail{$this->mail->idMail}");
    $tmpopen = fclose($this->file);
    if (!$tmpopen) {
      throw new Exception("No se ha generado el archivo temporal");
    }


    unlink($this->route);
    $this->db->commit();
  }

  public function getByMxcByFilters($whereAll) {
    $this->db->query("LOAD DATA INFILE '{$this->route}' IGNORE INTO TABLE tmp_table_target_{$this->mail->idMail} "
            . "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");
    $flagWhile = true;
    while ($flagWhile) {
      $this->bulk = new \MongoDB\Driver\BulkWrite;
      $sql = "SELECT  DISTINCT idContact  FROM tmp_table_target_{$this->mail->idMail} {$whereAll} LIMIT {$this->limit} OFFSET {$this->offset} ";
      $idsTmp = $this->db->fetchAll($sql);
      //
      if(count($idsTmp) > 0){
        unset($this->inIdcontact);
        $this->inIdcontact = [];
        foreach ($idsTmp as $key) {
          $this->inIdcontact[] = (int) $key['idContact'];
        }
        unset($idsTmp);
      } else {
        unset($this->inIdcontact);
      }
      if (!isset($this->inIdcontact) || count($this->inIdcontact) <= $this->limit) {
        $flagWhile = false;
      }
      $this->getAllContact();
      //$offset += $limit;
    }
  }

  public function getMxcByFilters($flag, $idMailLink, $idMailSelected) {
    if ($flag == 1) {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => array(
            '$in' => $idMailSelected
          ),
          "idMailLink" => array(
            '$in' => $idMailLink
          )
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      $inIdcontact = array();
      foreach ($mxcxl as $value) {
        $inIdcontact[] = (int) $value->idContact;
      }
      $this->inIdcontact = array_values(array_diff($this->inIdcontact, $inIdcontact));
      unset($inIdcontact);
      unset($mxcxl);
    }
    $p = \Mxc::find(array(
      "conditions" => array(
        "idContact" => array(
          '$in' => $this->inIdcontact
        ),
        "idMail" => array(
          '$in' => $idMailSelected
        ),
        "email" => array(
          '$nin' => ["", null, "null"]
        )
      ),
      "fields" => array(
        "idMail" => true,
        "idContact" => true,
        "open" => true
      )
    ));
    foreach ($p as $value) {
      fwrite($this->file, $this->i . " ,");
      fwrite($this->file, $value->idMail . " ,");
      fwrite($this->file, $value->idContact . " ,");
      fwrite($this->file, $value->open . " ,");
      fwrite($this->file, 0);
      fwrite($this->file, "\r\n");
      $this->i++;
    }
    unset($p);
  }

  public function getIdSegment() {
    $target = json_decode($this->mail->target);
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
          ['$match' => ['idSegment' => ['$in' => $this->idSegment], "deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0,'email' => ['$nin' => ["", null, "null"]]]],
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

  public function getAllContact() {
    $type = "find";
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $where = array(["idContact" => ['$in' => $this->inIdcontact], "email" => ['$nin' => ["", null, "null"]]]);
    if (isset($this->mail->idAutoresponder) && !empty($this->mail->idAutoresponder)) {
      if ($this->mail->Autoresponder->birthdate) {
        $hyphenDateFormat = date('m-d');
        $stringHyphenDateFormat = new \MongoRegex("/{$hyphenDateFormat}/");
        $slashDateFormatM = date('m');
        $slashDateFormatD = date('d');
        $stringSlashDateFormat = new \MongoRegex("/{$slashDateFormatM}\/{$slashDateFormatD}/");
        $where[0]['$or'] = [
          ["birthdate" => ['$regex' => $stringHyphenDateFormat]],
          ["birthdate" => ['$regex' => $stringSlashDateFormat]]
        ];
      }
    }

    if ($this->mail->singleMail) {
      $type = 'aggregate';
      unset($where);
      $where = array(
          array(
              '$match' => array(
                  'idContact' => array(
                      '$in' => $this->inIdcontact
                  ),
                  'email' => array(
                      '$nin' => array(
                          "", null, "null"
                      )
                  )
              )
          ),
          array(
              '$group' => array(
                  '_id' => '$email',
                  'data' => array(
                      '$first' => '$$ROOT'
                  )
              )
          )
      );
    }
    unset($this->inIdcontact);
    $this->contact = \Contact::{$type}($where);
    if (isset($this->mail->idAutoresponder) && !empty($this->mail->idAutoresponder)) {
      if ((count($this->contact) == 0) || ($this->contact == null)) {
        return true;
      }
    }
    unset($where);
    $this->modeldata();
    $manager->executeBulkWrite('aio.mxc', $this->bulk);
    unset($this->bulk);
  }

  public function modeldata() {
    $idAccount = (int) $this->mail->Subaccount->Account->idAccount;
    $idSubaccount = (int) $this->mail->Subaccount->idSubaccount;
    //$flag = (($idAccount == 49) ? false : true);
    $flag =  true;
    $dataValidation = new ApiDataValidation($this->dataValidation->apiRoot, $this->dataValidation->apiKey);

    if ($this->mail->singleMail) {
      $this->saveContactMxcSingleMail($idAccount, $idSubaccount, $flag, $dataValidation);
    } else {
      $this->saveContactMxc($idAccount, $idSubaccount, $flag, $dataValidation);
    }
  }

  public function findCustomField($idContact) {
    $arr = array();
    $cxc = Cxc::findFirst([["idContact" => $idContact]]);
    if(is_array($cxc) || is_object($cxc)){
      foreach ($cxc->idContactlist as $value) {
        array_push($arr, $value);
      }
    }
    return $arr;
  }

  public function getIdContaclist() {
    $target = json_decode($this->mail->target);
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
        break;
      case "segment":
//        if (isset($target->segment)) {
//          $this->getIdContactlistBySegments($target->segment);
//        }
        break;
      default:
        throw new Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
    }
  }

  public function getIdContactlistBySegments($listSegment) {
    foreach ($listSegment as $key) {
      $segment = Segment::findFirst([["idSegment" => $key->idSegment]]);
      foreach ($segment->contactlist as $k) {
        $this->idContactlist[] = $k["idContactlist"];
      }
      unset($segment);
    }
  }

  public function getAllCxcl() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist IN ({$idContactlist})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 "
            //  . " AND status = active "
            . " AND spam = 0 "
            . " AND bounced = 0 "
            . " AND blocked = 0 "
            . " AND singlePhone = 0"
            . " LIMIT {$this->limit} OFFSET {$this->offset}";
    //unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($sql);
    unset($cxcl);
  }

  function getData() {
    return $this->data;
  }

  function getTotalContacts() {
    $mxc = Mxc::count([["idMail" => $this->mail->idMail]]);
    return $mxc;
  }

  public function createTableTemporary($idMail) {
    if (!$this->db->execute("CREATE TEMPORARY TABLE IF NOT EXISTS tmp_table_target_{$idMail} LIKE tmp_table_target")) {
      throw new \InvalidArgumentException("Ha ocurrido un error creando el espacio temporal de la tabla tmp_table_target");
    }
    return true;
  }

  private function validateEmailDelivery($email, $idAccount) {
    $deliverableEmail = DeliverableEmail::findFirst(array(
                "conditions" => array(
                    "email" => (string) $email,
                    "idAccount" => $idAccount
                )
    ));
    if ($deliverableEmail) {
      unset($deliverableEmail);
      return true;
    }

    return false;
  }

  private function validateEmailInBlackList($email, $idAccount) {
    $bouncedMail = Bouncedmail::findFirst([array(
            "email" => $email,
            "idAccount" => $idAccount
    )]);

    if ($bouncedMail) {
      unset($bouncedMail);
      return true;
    }

    return false;
  }

  private function createDeliverableEmail($email, $grade, $idAccount) {
    //$de = DeliverableEmail;
    $de = DeliverableEmail::findFirst(array(
                "conditions" => array(
                    "email" => (string) $email
                )
    ));

    $flag = true;
    if ($de) {
      foreach ($de->idAccount as $idAcc) {
        if ($idAcc == $idAccount) {
          $flag = false;
          break;
        }
      }

      if ($flag) {
        array_push($de->idAccount, $idAccount);
        $de->save();
        unset($de);
      }
      return true;
    }


    $contactManager = new Sigmamovil\General\Misc\ContactManager();
    $nextIdAnswer = $contactManager->autoIncrementCollection("idDeliverableEmail");

    $deliverableEmail = new DeliverableEmail();
    $deliverableEmail->idMail = (int) $this->getMail()->idMail;
    $deliverableEmail->name = $this->getMail()->name;
    $deliverableEmail->idSubaccount = (int) $this->getMail()->idSubaccount;
    $deliverableEmail->idDeliverableEmail = $nextIdAnswer;
    $deliverableEmail->email = $email;
    $deliverableEmail->idAccount = [$idAccount];
    $deliverableEmail->dateTime = date("Y-m-d H:i:s", time());
    $deliverableEmail->score = (string) $grade;
    $deliverableEmail->source = 'Data Validation';
    $deliverableEmail->created = time();
    $deliverableEmail->updated = time();
    $deliverableEmail->createdBy = $this->getMail()->createdBy;
    $deliverableEmail->updatedBy = $this->getMail()->updatedBy;

    if (!$deliverableEmail->save()) {
      foreach ($deliverableEmail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    unset($deliverableEmail);
  }

  private function createBouncedMail($email, $grade, $idAccount, $idMail) {
    $bouncedemail = \Bouncedmail::findFirst([array(
            "email" => $email,
            "deleted" => 0
    )]);

    $flag = true;
    if ($bouncedemail != false) {
      foreach ($bouncedemail->idAccount as $idAcc) {
        if ($idAcc == $idAccount) {
          $flag = false;
          break;
        }
      }

      if ($flag) {
        array_push($bouncedemail->idAccount, $idAccount);
        array_push($bouncedemail->idMail, $idMail);
        $bouncedemail->save();
      }
      return true;
    }


    $bouncedmail = new Bouncedmail();
    $contactManager = new Sigmamovil\General\Misc\ContactManager();
    $nextIdAnswer = $contactManager->autoIncrementCollection("id_bouncedmail");

    $bouncedmail->idBouncedMail = $nextIdAnswer;
    $bouncedmail->idAccount = [$idAccount];
    $bouncedmail->datetime = date("Y-m-d H:i:s", time());
    $bouncedmail->idMail = [(int) $idMail];
    $bouncedmail->name = $this->getMail()->name;
    $bouncedmail->idSubaccount = (int) $this->getMail()->idSubaccount;
    $bouncedmail->email = (string) $email;
    $bouncedmail->source = "Data Validation";
    $bouncedmail->status = "blocked";
    $bouncedmail->type = 'Non deliverable';
    $bouncedmail->code = (string) $grade;
    $bouncedmail->description = 'Email non deliverable';
    $bouncedmail->created = time();
    $bouncedmail->updated = time();

    if (!$bouncedmail->save()) {
      foreach ($bouncedmail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if ($bouncedmail->code == 10 || $bouncedmail->code == 90 || $bouncedmail->code == 200) {
      $contact = \Contact::find([["email" => (string) $email, "idAccount" => $idAccount, "deleted" => 0]]);
      foreach ($contact as $value){
        $cxcl = \Cxcl::findFirst([["idContact" => $value->idContact, "deleted" => 0]]);
        $cxcl->unsubscribed = 0;
        $cxcl->spam = 0;
        $cxcl->blocked = 0;
        $cxcl->bounced = time();
        $cxcl->status = 'bounced';
        if (!$cxcl->save()) {
          foreach ($cxcl->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      }
      unset($contact);
    }
  }

  private function saveContactMxc($idAccount, $idSubaccount, $flag, $dataValidation) {
    $this->findBlocked($idAccount);
    $this->findBounced($idAccount);
    foreach ($this->contact as $key) {
      if (trim($key->email) == "ux.interaction.sas@gmail.com" || trim($key->email) == "agordillo@constructoranormandia.com" || trim($key->email) == "kevin.ramirez@sigmamovil.com" || trim($key->email) == "asanchez@hvr.com.co" || trim($key->email) == "marce.lizcano.b@gmail.com") {
        $arr["bounced"] = time();
        $arr["bouncedCode"] = "22";
      }
      $arr = array();

      if ($flag) {
        //if($this->mail->idMail!=31574 || $this->mail->idMail!='31574'){      
        if(!$this->mail->alldb){
          $email = $key->email;
          if(in_array($email, $this->arrayDataBlocked)){
            $arr["bounced"] = time();
            $arr["bouncedCode"] = "22";
          } else {
            if(in_array($email, $this->arrayDataBounced)){
              $arr["bounced"] = time();
              $arr["bouncedCode"] = "22";
            } else {
              $arr["bounced"] = 0;
              $arr["bouncedCode"] = 0;
            }
          }
        } else {
          $arr["bounced"] = 0;
          $arr["bouncedCode"] = 0;
        }
        //}
      } else {
        if (!$this->validateEmailDelivery($key->email, $idAccount)) {
          if ($this->validateEmailInBlackList($key->email, $idAccount)) {
            continue;
          } else {
            $grade = $dataValidation->realTimeCheck($key->email);
            if ($grade != 'F') {
              $this->createDeliverableEmail($key->email, $grade, $idAccount);
            } else {
              $this->createBouncedMail($key->email, $grade, $idAccount, $this->mail->idMail);
            }
          }
        }
      }
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idMail = $this->mail->idMail;
      $customLogger->idContact = $key->idContact;
      $customLogger->mailName = $this->mail->name;
      $customLogger->scheduleDate = $this->mail->scheduleDate;
      $customLogger->email = $key->email;
      $customLogger->name = $key->name;
      $customLogger->lastname = $key->lastname;
      $customLogger->birthdate = $key->birthdate;
      $customLogger->indicative = $key->indicative;
      $customLogger->phone = $key->phone;
      $customLogger->status = "scheduled";
      $customLogger->messagesSent = 0;
      $customLogger->typeName = "RegisterMailxContact";
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();

      //$arr["bounced"] = 0;
      //$arr["bouncedCode"] = 0;
      $arr["idContact"] = $key->idContact;
      $arr["idMail"] = $this->mail->idMail;
      $arr["mailName"] = $this->mail->name;
      $arr["scheduleDate"] = $this->mail->scheduleDate;
//      $arr["idMail"] = 1;
      $arr["email"] = $key->email;
      $arr["name"] = $key->name;
      $arr["lastname"] = $key->lastname;
      $arr["birthdate"] = $key->birthdate;
      $arr["indicative"] = $key->indicative;
      $arr["phone"] = $key->phone;
      $arr["status"] = "scheduled";
      $arr["open"] = "0";
      $arr["totalOpening"] = (int) 0;
//      $arr["click"] = 0;
      $arr["totalClicks"] = 0;
      $arr["uniqueClicks"] = "0";
      $arr["spam"] = 0;
      $arr["unsubscribed"] = 0;
      $arr["share_fb"] = 0;
      $arr["share_tw"] = 0;
      $arr["share_li"] = 0;
      $arr["share_gp"] = 0;
      $arr["open_fb"] = 0;
      $arr["open_tw"] = 0;
      $arr["open_li"] = 0;
      $arr["open_gp"] = 0;
      $customfield = $this->findCustomField($key->idContact);
      $arr["customfield"] = $customfield;
      $this->bulk->insert($arr);
      if ($this->automatic == true) {
        $this->automaticCampaignObj->insNewStep($key->idContact, $this->automaticCampaignConfiguration->idNode, $this->automaticCampaignConfiguration->node, $this->automaticCampaignConfiguration->beforeStep, $this->automaticCampaignConfiguration->date, $this->negation);
      }
    }
    unset($arr);
    unset($this->contact);
  }

  private function saveContactMxcSingleMail($idAccount, $idSubaccount, $flag, $dataValidation) {
    \Phalcon\DI::getDefault()->get('logger')->log("Entro a la condicion de single Mail");
    if(!$this->mail->alldb){
      $this->findBlocked($idAccount);
      $this->findBounced($idAccount);
    }
    foreach ($this->contact["result"] as $key) {
      $key["data"] = (object) $key["data"];
      if (trim($key["data"]->email) == "ux.interaction.sas@gmail.com" || trim($key["data"]->email) == "agordillo@constructoranormandia.com" || trim($key["data"]->email) == "kevin.ramirez@sigmamovil.com" || trim($key["data"]->email) == "asanchez@hvr.com.co" || trim($key["data"]->email) == "marce.lizcano.b@gmail.com") {
        $arr["bounced"] = time();
        $arr["bouncedCode"] = "22";
      }
      $arr = array();
      if ($flag) {
        $idContact = $key["data"]->idContact;
        $mxc = \Mxc::findFirst(["conditions" => ["idMail" => (string) $this->mail->idMail, "idContact" => (int) $idContact, "status" => "scheduled"],"fields" => ["idContact" => true]]);
        if($mxc != false){
          $mxc->delete();
        }
        //if($this->mail->idMail!=31574 || $this->mail->idMail!='31574'){
        if(!$this->mail->alldb){
          $email = $key["data"]->email;
          if(in_array($email, $this->arrayDataBlocked)){
            $arr["bounced"] = time();
            $arr["bouncedCode"] = "22";
          } else {
            if(in_array($email, $this->arrayDataBounced)){
              $arr["bounced"] = time();
              $arr["bouncedCode"] = "22";
            } else {
              $arr["bounced"] = 0;
              $arr["bouncedCode"] = 0;
            }
          } 
        } else {
          $arr["bounced"] = 0;
          $arr["bouncedCode"] = 0;
        }
        //}
      } else {
        if (!$this->validateEmailDelivery($key["data"]->email, $idAccount)) {
          if ($this->validateEmailInBlackList($key["data"]->email, $idAccount)) {
            continue;
          } else {
            $grade = $dataValidation->realTimeCheck($key["data"]->email);
            if ($grade != 'F') {
              $this->createDeliverableEmail($key["data"]->email, $grade, $idAccount);
            } else {
              $this->createBouncedMail($key["data"]->email, $grade, $idAccount, $this->mail->idMail);
            }
          }
        }
      }
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idMail = $this->mail->idMail;
      $customLogger->idContact = $key["data"]->idContact;
      $customLogger->mailName = $this->mail->name;
      $customLogger->scheduleDate = $this->mail->scheduleDate;
      $customLogger->email = $key["data"]->email;
      $customLogger->name = $key["data"]->name;
      $customLogger->lastname = $key["data"]->lastname;
      $customLogger->birthdate = $key["data"]->birthdate;
      $customLogger->indicative = $key["data"]->indicative;
      $customLogger->phone = $key["data"]->phone;
      $customLogger->status = "scheduled";
      $customLogger->messagesSent = 0;
      $customLogger->typeName = "RegisterMailxContact";
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();

      //$arr["bounced"] = 0;
      //$arr["bouncedCode"] = 0;
      $arr["idContact"] = $key["data"]->idContact;
      $arr["idMail"] = $this->mail->idMail;
      $arr["mailName"] = $this->mail->name;
      $arr["scheduleDate"] = $this->mail->scheduleDate;
//      $arr["idMail"] = 1;
      $arr["email"] = $key["data"]->email;
      $arr["name"] = $key["data"]->name;
      $arr["lastname"] = $key["data"]->lastname;
      $arr["birthdate"] = $key["data"]->birthdate;
      $arr["indicative"] = $key["data"]->indicative;
      $arr["phone"] = $key["data"]->phone;
      $arr["status"] = "scheduled";
      $arr["open"] = "0";
      $arr["totalOpening"] = (int) 0;
//      $arr["click"] = 0;
      $arr["totalClicks"] = 0;
      $arr["uniqueClicks"] = "0";
      $arr["spam"] = 0;
      $arr["unsubscribed"] = 0;
      $arr["share_fb"] = 0;
      $arr["share_tw"] = 0;
      $arr["share_li"] = 0;
      $arr["share_gp"] = 0;
      $arr["open_fb"] = 0;
      $arr["open_tw"] = 0;
      $arr["open_li"] = 0;
      $arr["open_gp"] = 0;
      if($this->field){
        $customfield = $this->findCustomField($key["data"]->idContact);
        $arr["customfield"] = $customfield;
      } else {
        $arr["customfield"] = [];
      }
      $this->bulk->insert($arr);
      /*if ($this->automatic == true) {
        $this->automaticCampaignObj->insNewStep($key["data"]->idContact, $this->automaticCampaignConfiguration->idNode, $this->automaticCampaignConfiguration->node, $this->automaticCampaignConfiguration->beforeStep, $this->automaticCampaignConfiguration->date, $this->negation);
      }*/
    }
    unset($arr);
    unset($this->contact);
  }

  public function filterNotSend(){
    $targetMail = json_decode($this->mail->target);
    $stringData = "";
    foreach ($targetMail->filters as $value) {
      $stringData .= (int) $value->mailSelected . ","; 
    }
    unset($targetMail);
    $stringData = trim($stringData, ','); 
    $findMail = \Mail::find(["conditions" => "idMail IN ({$stringData})"]);
    unset($stringData);
    $stringData = "";
    foreach ($findMail as $value) {
      $targetFilter = json_decode($value->target);
      foreach ($targetFilter->contactlists as $contactlist) {
        $stringData .= (int) $contactlist->idContactlist . ",";
      }
      unset($targetFilter);
    }
    unset($findMail);
    $stringData = trim($stringData, ','); 
    $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist IN ({$stringData})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 "
            . " AND spam = 0 "
            . " AND bounced = 0 "
            . " AND blocked = 0 "
            . " AND singlePhone = 0"
            . " LIMIT {$this->limit} OFFSET {$this->offset}";
    $findCxcl = $this->db->fetchAll($sql);
    unset($stringData);
    foreach ($findCxcl as $contact) {
      $this->inIdcontact[] = (int) $contact['idContact'];
    }
    unset($findCxcl);
  }
  
  public function findBlocked($idAccount){
    $blocked = Blocked::find([
      "conditions" => array(  
        "idAccount" => (int) $idAccount,
        "deleted" => 0,
      ),
      "fields" => array(
        "email" => true
      )
    ]);
    if($blocked != false){
      foreach ($blocked as $value){
        if(!in_array($value->email, $this->arrayDataBlocked)){
          $this->arrayDataBlocked[] = (string) $value->email;
        }
        /*for($i=0; $i<count($value->idContacts); $i++){
          if(count($value->idContacts)> 0){
            $idContact = $value->idContacts[$i];
            if(!in_array($idContact, $this->arrayDataBlocked)){
              $this->arrayDataBlocked[] = (int) $idContact;
            }
          }
        }*/
      }
    }
    unset($blocked);
    return $this->arrayDataBlocked;
  }
  
  public function findBounced($idAccount){
    $bounced = Bouncedmail::find([
      "conditions" => array(  
        "idAccount" => ['$in' => [(int) $idAccount]],
        "deleted" => 0,
        "code" => ['$in' => ["10", "90", "200"]]
      ),
      "fields" => array(
        "email" => true
      )  
    ]);
    if($bounced != false){
      foreach ($bounced as $value){
        if(!in_array($value->email, $this->arrayDataBounced)){
          $this->arrayDataBounced[] = (string) $value->email;
        }
      }
    }
    unset($bounced);
    return $this->arrayDataBounced;
  }
  
  public function findIdContactAll($idMailSelected){
    $mxc = \Mxc::find(array(
      "conditions" => array(
        "idMail" => array(
          '$in' => $idMailSelected
        ),
        "unsubscribed" => 0,
        "spam" => 0,
        "bounced" => 0,
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    foreach ($mxc as $value) {
      $this->inIdcontact[] = (int) $value->idContact;
    }
    unset($mxc);
  }

  public function findIdContactOpen($idMailSelected){
    $mxc = \Mxc::find(array(
      "conditions" => array(
        "idMail" => array(
          '$in' => $idMailSelected
        ),
        "open" => ['$gte' => 1],
        "unsubscribed" => 0,
        "spam" => 0,
        "bounced" => 0,
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    foreach ($mxc as $value) {
      $this->inIdcontact[] = (int) $value->idContact;
    }
    unset($mxc);
  }
  
  public function findIdContactLink($idMailSelected, $idMailLink){
    $mxcxl = \Mxcxl::find(array(
      "conditions" => array(
        "idMail" => array(
          '$in' => $idMailSelected
        ),
        "idMailLink" => array(
          '$in' => $idMailLink
        )
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    foreach ($mxcxl as $value) {
      $this->inIdcontact[] = (int) $value->idContact;
    }
    unset($mxcxl);
  }
  
  public function getAllCxclFilter(){
    $idContactlist = implode(",", $this->idContactlist);
    $idContact = implode(",", $this->inIdcontact);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist IN ({$idContactlist})"
            . " AND idContact IN ({$idContact})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 "
            . " AND spam = 0 "
            . " AND bounced = 0 "
            . " AND blocked = 0 "
            . " AND singlePhone = 0"
            . " LIMIT {$this->limit} OFFSET {$this->offset}";
    unset($idContactlist);
    unset($idContact);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    unset($this->inIdcontact);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    };
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($sql);
    unset($cxcl);
  }
  
  public function getAllIdContactSegmentFilter() {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new MongoDB\Driver\Command([
      'aggregate' => 'sxc',
      'pipeline' => [
          ['$match' => ['idSegment' => ['$in' => $this->idSegment],'idContact' => ['$in' => $this->inIdcontact],"deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0,'email' => ['$nin' => ["", null, "null"]]]],
          ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]],
          ['$limit' => $this->offset + $this->limit],
          ['$skip'  => $this->offset]
      ],
      'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    unset($this->inIdcontact);
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      $this->inIdcontact[$i] = $segment[0]->result[$i]->_id;
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($command);
    unset($segment);
  }
  
  public function findFilterSms($target){
    $idSmsSelected = [];
    foreach ($target->filterSms as $key) {
      switch ($key->typeFilters) {
        case 1:
          $idSmsSelected[] = (string) $key->smsSelected;
          break;
      }
    }
    $smsxc = \Smsxc::find(array(
      "conditions" => array(
        "idSms" => array(
          '$in' => $idSmsSelected
        ),
        "status" => "sent",
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    unset($this->inIdcontact);
    foreach ($smsxc as $value) {
      $this->inIdcontact[] = (int) $value->idContact;
    }
    unset($smsxc);
  }
  
  public function customfield($field){ 
    if(count($field) > 0){
      $this->field = TRUE; 
    }    
  }
  
}