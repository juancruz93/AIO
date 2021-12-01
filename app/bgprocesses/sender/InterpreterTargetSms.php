<?php
ini_set('memory_limit', '768M');
class InterpreterTargetSms {

  protected $data = array();
  protected $sms;
  protected $idContactlist = array();
  protected $inIdcontact = array();
  protected $idSegment = array();
  protected $contact = array();
  protected $totalContacts;
  protected $bulk;
  protected $offset = 0;
  protected $limit = 10000;
  protected $flag = true;
  public $automatic = false,
          $automaticNoClic = false,
          $automaticCampaign,
          $automaticCampaignConfiguration,
          $beforeStep,
          $arrayDataBlockedPhone = array();

  function __construct() {
    
  }

  function setSms(Sms $sms) {
    $this->sms = $sms;
  }

  function setSmstwoway(Smstwoway $sms) {
    $this->sms = $sms;
  }

  public function searchTotalContacts() {
    var_dump(print_r("paso 4",true));
\Phalcon\DI::getDefault()->get('logger')->log("Entra searchTotalContacts Sms {$this->sms->idSms} {$this->sms->name}".json_encode($this->inIdcontact));
    while ($this->flag) {
      $this->bulk = new \MongoDB\Driver\BulkWrite;
      $target = json_decode($this->sms->receiver);
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
        \Phalcon\DI::getDefault()->get('logger')->log("Entra en findFilterSms --- Sms{$this->sms->idSms}");
        $idSmsSelected = [];
        foreach ($target->filterSms as $key) {
          if ($key->typeFilters == 1) {
            $idSmsSelected[] = (string) $key->smsSelected;
          }
        }
        $this->findFilterSms($idSmsSelected);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio en findFilterSms --- Sms{$this->sms->idSms}");
      }
      if (isset($target->filters) && count($target->filters) >= 1) {
        $this->getMxcByFilters($target);
      }
      if (isset($target->filtersOpen) && count($target->filtersOpen) >= 1) {
        \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro open --- Sms{$this->sms->idSms}");
        $this->findFilterOpen($target->filtersOpen);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro open --- Sms{$this->sms->idSms}");
      }  
      if (isset($target->filtersClic) && count($target->filtersClic) >= 1) {
        \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro clic --- Sms{$this->sms->idSms}");
        $this->findFilterClic($target->filtersClic);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro clic --- Sms{$this->sms->idSms}");
      }
      if (isset($target->filtersTime) && count($target->filtersTime) >= 1) {
        \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro time --- Sms{$this->sms->idSms}");
        $this->findFilterTime($target->filtersTime);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro time --- Sms{$this->sms->idSms}");
      }
      if (isset($target->filtersOPenClic) && count($target->filtersOPenClic) >= 1) {
        \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro open clic --- Sms{$this->sms->idSms}");
        $this->findFilterOpenClic($target->filtersOPenClic);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro open clic --- Sms{$this->sms->idSms}");
      }
      if (isset($target->filtersNoOPenClic) && count($target->filtersNoOPenClic) >= 1) {
        \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro no open clic --- Sms{$this->sms->idSms}");
        $this->findFilterNoOpenClic($target->filtersNoOPenClic);
        \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro no open clic --- Sms{$this->sms->idSms}");
      }
      if (isset($this->inIdcontact)) {
        $this->getAllContact();
      }
      
      $this->offset += $this->limit;
    }    
  }

  public function getIdSegment() {
    $target = json_decode($this->sms->receiver);
    if (isset($target->segment)) {
      foreach ($target->segment as $key) {
        $this->idSegment[] = $key->idSegment;
      }
    }
  }

  public function getAllIdContactSegment() {
    if(empty($this->sms->idAutomaticCampaign) || empty($this->sms->idAutomaticCampaign) == null){
        $segment = Sxc::find([["idSegment" => ['$in' => $this->idSegment], "deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0 ] , "limit" => $this->limit, "skip" => $this->offset]);
        unset($this->idSegment);
        foreach ($segment as $key) {
          $this->inIdcontact[] = (int) $key->idContact;
        }
        if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
          $this->flag = false;
        }
    }
  }

  public function getAllContact() {
    var_dump(print_r("paso 7",true));
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    /*$where = array(
        "conditions" => array(
            "idContact" => ['$in' => $this->inIdcontact],
            "phone" => ['$nin' => ["", null, "null"]],
            "indicative" => ['$nin' => ["", null, "null"]],
            "blockedPhone" => ['$in' => ["", null, "null"]]
        )
    );*/

    if($this->sms->singleSendContact == 1){
      var_dump(print_r("Es unico 8",true));
      $where = array(
                  "idContact" => ['$in' => $this->inIdcontact],
                  "phone" => ['$nin' => ["", null, "null"]],
                  "indicative" => ['$nin' => ["", null, "null"]],
                  "blockedPhone" => ['$in' => ["", null, "null"]]
          );

      if(isset($this->sms->idAutoresponder) && !empty($this->sms->idAutoresponder)){
        if($this->sms->Autoresponder->birthdate){
         $hyphenDateFormat = date('m-d');
         $stringHyphenDateFormat = new \MongoRegex("/{$hyphenDateFormat}/");
         $slashDateFormatM = date('m');
         $slashDateFormatD = date('d');
         $stringSlashDateFormat = new \MongoRegex("/{$slashDateFormatM}\/{$slashDateFormatD}/");
         //
         $where['$or'] = [
            ["birthdate" => ['$regex' => $stringHyphenDateFormat]],
            ["birthdate" => ['$regex' => $stringSlashDateFormat]]
          ];
        }
      }

      $command = new MongoDB\Driver\Command([
        'aggregate' => 'contact',
        'pipeline' => [
            ['$match' => $where],
            ['$group' => ['_id' => '$phone', 'data' => ['$first' => '$$ROOT']]],
          ],
        'allowDiskUse' => true,  
      ]);

      $this->contact = $manager->executeCommand('aio', $command)->toArray();
      $this->contact = $this->contact[0]->result;
      var_dump(print_r(count($this->contact),true));

      $this->modeldata();
      $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      $manager->executeBulkWrite('aio.smsxc', $this->bulk,$writeConcern);
      unset($this->inIdcontact);
      unset($this->bulk);
    }else{
//      var_dump(print_r($this->inIdcontact,true));exit;
    //for($i=0;$i<count($this->inIdcontact);$i++){
    foreach($this->inIdcontact as $v){
      $where = array(
        "conditions" => array(
            //"idContact" => $this->inIdcontact[$i],
            "idContact" => $v,
            "phone" => ['$nin' => ["", null, "null"]],
            "indicative" => ['$nin' => ["", null, "null"]],
            "blockedPhone" => ['$in' => ["", null, "null"]]
          )
      );
      if(isset($this->sms->idAutoresponder) && !empty($this->sms->idAutoresponder)){
        if($this->sms->Autoresponder->birthdate){
         $hyphenDateFormat = date('m-d');
         $stringHyphenDateFormat = new \MongoRegex("/{$hyphenDateFormat}/");
         $slashDateFormatM = date('m');
         $slashDateFormatD = date('d');
         $stringSlashDateFormat = new \MongoRegex("/{$slashDateFormatM}\/{$slashDateFormatD}/");
         /*$invertedHyphenDateFormat = date('d-m');
         $stringInvertedHyphenDateFormat = new \MongoRegex("/{$invertedHyphenDateFormat}/");
         $stringInvertedSlashDateFormat = new \MongoRegex("/{$slashDateFormatD}\/{$slashDateFormatM}/");*/  
         
         $where['conditions']['$or'] = [
            ["birthdate" => ['$regex' => $stringHyphenDateFormat]],
            ["birthdate" => ['$regex' => $stringSlashDateFormat]]
            /*["birthdate" => ['$regex' => $stringInvertedHyphenDateFormat]],
            ["birthdate" => ['$regex' => $stringInvertedSlashDateFormat]],*/
            ];
          }
        }
      $this->contact = \Contact::findFirst($where);
      $this->modeldata();
      }
      unset($this->inIdcontact);
      $manager->executeBulkWrite('aio.smsxc', $this->bulk);  
      unset($this->bulk);
    }
//    exit;
//    unset($this->inIdcontact);
    //$this->contact = \Contact::find($where);
    /*if (count($this->contact) <= 0) {
      throw new InvalidArgumentException("No existen contactos con numeros telefonicos asignados ");
    }*/
    //unset($where);
    //unset($this->bulk);
  }

  public function modeldata() {
    if($this->sms->singleSendContact == 0){
      $SmsFailed = SmsFailed::findFirst(array(
                  "conditions" => "idContact = ?0 and idSms = ?1",
                  "bind" => array(0 =>  (int)$this->contact->idContact, 1 => (int) $this->sms->idSms)
      ));

      /*$blocked = Blocked::findFirst([array(
        "phone" => $this->contact->phone ,
        "idAccount" => (string) \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount,
        "deleted" => 0
      )]);*/
      if (!$SmsFailed && !in_array($this->contact->phone, $this->arrayDataBlockedPhone) && /*!$blocked &&*/ $this->contact ) {
        $arr = array();
        $arr["idContact"] = $this->contact->idContact;
        if (isset($this->sms->idSms)) {
          $arr["idSms"] = $this->sms->idSms;
        } else {
          $arr["idSmsTwoway"] = $this->sms->idSmsTwoway;
        }
        $arr["idSubaccount"] = $this->sms->idSubaccount;
        $arr["smsName"] = $this->sms->name;
        $arr["scheduleDate"] = $this->sms->startdate;
        $arr["message"] = $this->sms->message;
        $arr["email"] = $this->contact->email;
        $arr["name"] = $this->contact->name;
        $arr["lastname"] = $this->contact->lastname;
        $arr["indicative"] = $this->contact->indicative;
        $arr["phone"] = $this->contact->phone;
        $arr["status"] = "scheduled";
        $arr["response"] = "";
        $arr["birthdate"] = $this->contact->birthdate;
        $customfield = $this->findCustomField($this->contact->idContact);
        $arr["customfield"] = $customfield;        
        if($this->sms->morecaracter == 1 && mb_strlen(trim($this->sms->message), 'UTF-8') > 160 ){
            $arr["messageCount"] = 2;
        }else{
            $arr["messageCount"] = 1;
        }
        $this->bulk->insert($arr);
      }
    }else{
      $contador = 0;
      foreach ($this->contact as $key) {
      //if($this->sms->singleSendContact == 1){
        $key = $key->data;                   
      //}                                      
      // se añade validacion para evitar el envio de sms cuando el numero esta incorrecto
      $SmsFailed = SmsFailed::findFirst(array(
                  "conditions" => "idContact = ?0 and idSms = ?1",
                  "bind" => array(0 => (int) $key->idContact, 1 => (int) $this->sms->idSms)
      ));

      /*$blocked = Blocked::findFirst(array(
                  "conditions" => array(
                      "field" => (string) $key->phone,
                      //"idAccount" => $idAcc,
                      //"deleted" => 0
                  )
      ));*/

      if (!$SmsFailed  && !in_array($key->phone, $this->arrayDataBlockedPhone)/*&& !$blocked*/ ) {
        $arr = array();
        $arr["idContact"] = $key->idContact;
        if (isset($this->sms->idSms)) {
          $arr["idSms"] = $this->sms->idSms;
        } else {
          $arr["idSmsTwoway"] = $this->sms->idSmsTwoway;
        }
        $arr["idSubaccount"] = $this->sms->idSubaccount;
        $arr["smsName"] = $this->sms->name;
        $arr["scheduleDate"] = $this->sms->startdate;
        $arr["message"] = $this->sms->message;
        $arr["email"] = $key->email;
        $arr["name"] = $key->name;
        $arr["lastname"] = $key->lastname;
        $arr["indicative"] = $key->indicative;
        $arr["phone"] = $key->phone;
        $arr["status"] = "scheduled";
        $arr["response"] = "";
        $arr["birthdate"] = $key->birthdate;
        $customfield = $this->findCustomField($key->idContact);
        $arr["customfield"] = $customfield;
        if($this->sms->morecaracter == 1 && mb_strlen(trim($this->sms->message), 'UTF-8') > 160 ){
          $arr["messageCount"] = 2;
        }else{
          $arr["messageCount"] = 1;
        }
                
        $this->bulk->insert($arr);
      }
      //$contador++;
      //var_dump(print_r($contador,true));
    }
  }
    
    unset($arr);
    unset($this->contact);
  }

  public function findCustomField($idContact) {
    $arr = array();
    $cxc = Cxc::findFirst(array(
                "conditions" => array(
                    "idContact" => (int) $idContact
                )
    ));

    if (!$cxc) {
      return $arr;
    }

    foreach ($cxc->idContactlist as $value) {
      array_push($arr, (object) array_filter($value));
    }
    
    return $arr;
  }

  public function getIdContaclist() {
    $target = json_decode($this->sms->receiver);
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
        var_dump(print_r("paso 5",true));
        break;
      case "segment":
        if (isset($target->segment)) {
          $this->getIdContactlistBySegments($target->segment);
        }
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
    if(empty($this->sms->idAutomaticCampaign) || empty($this->sms->idAutomaticCampaign) == null){
        $idContactlist = implode(",", $this->idContactlist);
        unset($this->idContactlist);
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
          $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
        };
    
        var_dump(print_r("paso 6",true));
        var_dump(print_r($this->limit,true));
        var_dump(print_r(count($this->inIdcontact),true));
    
        if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
          $this->flag = false;
        }
        unset($sql);
        unset($cxcl);
        //var_dump(print_r("paso 6",true));
        //var_dump(print_r(count($this->inIdcontact),true));
        //var_dump(print_r(count($this->limit),true));
    }
  }

  function getData() {
    return $this->data;
  }

  function getTotalContacts() {
    $mxc = Smsxc::count([["idSms" => (String) $this->sms->idSms]]);
    return $mxc;
  }
  
  public function findBlockedPhone($idAccount){
    $blocked = \Blocked::find([array(
      "idAccount" => (int) $idAccount,
      "deleted" => 0
    )]);
    if($blocked != false){
      foreach ($blocked as $value){
        if(!in_array($value->phone, $this->arrayDataBlockedPhone)){
          $this->arrayDataBlockedPhone[] = (string) $value->phone;
        }
      }
    }
    unset($blocked);
  }
  
  public function getMxcByFilters($target){
    \Phalcon\DI::getDefault()->get('logger')->log("Entra en getMxcByFilters --- Sms{$this->sms->idSms}");
    \Phalcon\DI::getDefault()->get('logger')->log("Este es el receiver {$this->sms->receiver} --- Sms{$this->sms->idSms}");
    if (isset($target->filters) && count($target->filters) >= 1) {
      $flag = 0;
      $idMailLink = [];
      $idMailSelected = [];
      $filter = $target->filters[0];
      if (isset($filter->inverted) && $filter->inverted == true) {
        $flag = 1;
        switch ($filter->typeFilters) {
          case 1:
            $idMailSelected[] = (string) $filter->mailSelected;
            $this->getFilters($flag,$idMailSelected);
            break;
          case 2:
            $idMailSelected[] = (string) $filter->mailSelected;
            $this->getFiltersOpen($flag,$idMailSelected);
            break;
          case 3:
            $idMailSelected[] = (string) $filter->mailSelected;
            $idMailLink[] = (string) $filter->linkSelected;
            $this->getFiltersClick($flag,$idMailSelected,$idMailLink);
            break;
        }
        
      } else {
        $flag = 2;
        switch ($filter->typeFilters) {
          case 1:
            $idMailSelected[] = (string) $filter->mailSelected;
            $this->getFilters($flag,$idMailSelected);
            break;
          case 2:
            $idMailSelected[] = (string) $filter->mailSelected;
             $this->getFiltersOpen($flag,$idMailSelected);
            break;
          case 3:
            $idMailSelected[] = (string) $filter->mailSelected;
            $idMailLink[] = (string) $filter->linkSelected;
            $this->getFiltersClick($flag,$idMailSelected,$idMailLink);
            break;
        }
      }
    }
    \Phalcon\DI::getDefault()->get('logger')->log("Salió de los filtros --- Sms{$this->sms->idSms}");
  }
  
  public function getFilters($flag,$idMailSelected){
    if($flag == 1){
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => array(
            '$in' => $idMailSelected
          )
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      $inIdcontact = array();
      foreach ($mxc as $value) {
        $inIdcontact[] = (int) $value->idContact;
      }
      unset($mxc);
      $this->inIdcontact = array_values(array_diff($this->inIdcontact, $inIdcontact));
      unset($inIdcontact);
    } else if ($flag == 2){
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => array(
            '$in' => $idMailSelected
          )
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      unset($this->inIdcontact);
      foreach ($mxc as $value) {
        $this->inIdcontact[] = (int) $value->idContact;
      }
      unset($mxc);
    }
  }
  
  public function getFiltersOpen($flag,$idMailSelected){
    if($flag == 1){
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => array(
            '$in' => $idMailSelected
          ),
          "open" => "0",
          "unsubscribed" => 0,
          "spam" => 0,
          "bounced" => 0,
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      unset($this->inIdcontact);
      foreach ($mxc as $value) {
        $this->inIdcontact[] = (int) $value->idContact;
      }
      unset($mxc);
    } else if ($flag == 2){
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
      unset($this->inIdcontact);
      foreach ($mxc as $value) {
        $this->inIdcontact[] = (int) $value->idContact;
      }
      unset($mxc);
    }
  }
  
  public function getFiltersClick($flag,$idMailSelected,$idMailLink){
    if($flag == 1){
        if(empty($this->sms->idAutomaticCampaign) || empty($this->sms->idAutomaticCampaign) == null){
            unset($this->inIdcontact);
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
          $idContacts = array();
          foreach ($mxc as $key => $value) {
            $idContacts[] = (int) $value->idContact;
            unset($mxc[$key]);
          }
          unset($mxc);
         $mxcxl = \Mxcxl::find(array(
            "conditions" => array(
                "idMail" => array(
                '$in' => $idMailSelected
              ),
              "idMailLink" => array(
                '$in' => $idMailLink
              ),
              "idContact" => array('$in' => $idContacts)
            ),
            "fields" => array(
              "idContact" => true,
            )
          ));  
          
          $inIdcontact = array();
          
          foreach ($mxcxl as $value) {
            $inIdcontact[] = (int) $value->idContact;
          }
          $this->inIdcontact = array_values(array_diff($idContacts, $inIdcontact));
          unset($inIdcontact);
          unset($mxcxl);
          \Phalcon\DI::getDefault()->get('logger')->log("*****DESPUES DE MXCXL INVERTIDO TRUE**** ".json_encode($this->inIdcontact));
        }else{
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
    } else if ($flag == 2){
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
      unset($this->inIdcontact);
      foreach ($mxcxl as $value) {
        $this->inIdcontact[] = (int) $value->idContact;
      }
      unset($mxcxl);
      \Phalcon\DI::getDefault()->get('logger')->log("*****DESPUES DE MXCXL INVERTIDO FALSE**** ".json_encode($this->inIdcontact));
    }
    \Phalcon\DI::getDefault()->get('logger')->log("*****retorno getFiltersClick**** ".json_encode($this->inIdcontact));
  }
  
  public function findFilterSms($idSmsSelected){
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

  public function findFilterOpen($target){
    $idMailSelected = "";
    $inverted = "";
    foreach ($target as $key) {
      switch ($key->typeFilters) {
        case 2:
          $idMailSelected = (string) $key->mailSelected;
          $inverted = $key->inverted;
          break;
      }
    }
    if ($inverted == "") {
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "open" => array(
            '$gte' => 1
          ),  
          "unsubscribed" => 0,
          "spam" => 0,
          "bounced" => 0,
          "email" => array(
            '$nin' => ["", null, "null"]
          )  
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      unset($this->inIdcontact);
      foreach ($mxc as $key => $value) {
        $this->inIdcontact[] = (int) $value->idContact;
        unset($mxc[$key]);
      }
      unset($mxc);
    } else {
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "open" => "0",  
          "unsubscribed" => 0,
          "spam" => 0,
          "bounced" => 0,
          "email" => array(
            '$nin' => ["", null, "null"]
          )  
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      unset($this->inIdcontact);
      foreach ($mxc as $key => $value) {
        $this->inIdcontact[] = (int) $value->idContact;
        unset($mxc[$key]);
      }
      unset($mxc);
    }
  }
  
  public function findFilterClic($target){
    $idMailSelected = "";
    $idLinkSelected = "";
    $inverted = "";
    foreach ($target as $key) {
      switch ($key->typeFilters) {
        case 3:
          $idMailSelected = (string) $key->mailSelected;
          $idLinkSelected = (string) $key->linkSelected;
          $inverted = $key->inverted;
          break;
      }
    }
    if ($inverted != "") {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "idMailLink" => $idLinkSelected
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      if ($mxcxl != false) {
        foreach ($mxcxl as $value) {
          $idContact = (int) $value->idContact;
          $clave = array_search($idContact, $this->inIdcontact);
          unset($this->inIdcontact[$clave]);
          $this->inIdcontact = array_values($this->inIdcontact);
        }      
        unset($mxcxl);
      }
    } else {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "idMailLink" => $idLinkSelected,
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      unset($this->inIdcontact);
      if ($mxcxl != false) {
        foreach ($mxcxl as $value) {
          $this->inIdcontact[] = (int) $value->idContact;
        }
        unset($mxcxl);
      }
    }
  }

  public function findFilterOPenClic($target){
    $idMailSelected = "";
    $idLinkSelected = "";
    $inverted = "";
    foreach ($target as $key) {
      switch ($key->typeFilters) {
        case 2:
          $idMailSelected = (string) $key->mailSelected;
          break;
        case 3:
          $idLinkSelected = (string) $key->linkSelected;
          $inverted = $key->inverted;
          break;
      }
    }
    $mxc = \Mxc::find(array(
      "conditions" => array(
        "idMail" => $idMailSelected,
        "open" => array(
          '$gte' => 1
        ),  
        "unsubscribed" => 0,
        "spam" => 0,
        "bounced" => 0,
        "email" => array(
          '$nin' => ["", null, "null"]
        )  
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    unset($this->inIdcontact);
    foreach ($mxc as $key => $value) {
      $this->inIdcontact[] = (int) $value->idContact;
      unset($mxc[$key]);
    }
    unset($mxc);
    if ($inverted != "") {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "idMailLink" => $idLinkSelected
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      if ($mxcxl != false) {
        foreach ($mxcxl as $value) {
          $idContact = (int) $value->idContact;
          $clave = array_search($idContact, $this->inIdcontact);
          unset($this->inIdcontact[$clave]);
          $this->inIdcontact = array_values($this->inIdcontact);
        }      
        unset($mxcxl);
      } else {
        unset($this->inIdcontact);
      }
    } else {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "idMailLink" => $idLinkSelected,
          "idContact" => array(
            '$in' => $this->inIdcontact
          ), 
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      unset($this->inIdcontact);
      if ($mxcxl != false) {
        foreach ($mxcxl as $value) {
          $this->inIdcontact[] = (int) $value->idContact;
        }
        unset($mxcxl);
      }
    }
  }

  public function findFilterNoOpenClic($target){
    $idMailSelected = "";
    $idLinkSelected = [];
    foreach ($target as $key) {
      switch ($key->typeFilters) {
        case 3:
          $idMailSelected = (string) $key->mailSelected;  
          $idLinkSelected[] = (string) $key->linkSelected;
          break;
      }
    }
    $mxc = \Mxc::find(array(
      "conditions" => array(
        "idMail" => $idMailSelected,
        "open" => array(
          '$gte' => 1
        ),  
        "unsubscribed" => 0,
        "spam" => 0,
        "bounced" => 0,
        "email" => array(
          '$nin' => ["", null, "null"]
        )  
      ),
      "fields" => array(
        "idContact" => true,
      )
    ));
    unset($this->inIdcontact);
    foreach ($mxc as $key => $value) {
      $this->inIdcontact[] = (int) $value->idContact;
      unset($mxc[$key]);
    }
    unset($mxc);
    //
    if ($idLinkSelected != "") {
      $mxcxl = \Mxcxl::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "idMailLink" => array(
            '$in' => $idLinkSelected
          ), 
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));  
      if ($mxcxl != false) {
        $inIdcontact = array();
        foreach ($mxcxl as $value) {
          $idContact = (int) $value->idContact;
          if(!in_array($idContact, $inIdcontact)){
            $inIdcontact[] = $idContact;
          }
        }
        $this->inIdcontact = array_values(array_diff($this->inIdcontact, $inIdcontact));
        unset($inIdcontact);   
        unset($mxcxl);
      }
    } else {
      unset($this->inIdcontact);
    }
  }

  public function findFilterTime($target){
    $idMailSelected = "";
    $idSmsSelected = "";
    foreach ($target as $key) {
      switch ($key->typeFilters) {
        case 1:
          $idMailSelected = (string) $key->mailSelected;
          $idSmsSelected = (string) $key->smsSelected;
          break;
      }
    }
    if ($idMailSelected != "") {
      $mxc = \Mxc::find(array(
        "conditions" => array(
          "idMail" => $idMailSelected,
          "status" => "sent",  
          "unsubscribed" => 0,
          "spam" => 0,
          "bounced" => 0,
          "email" => array(
            '$nin' => ["", null, "null"]
          )  
        ),
        "fields" => array(
          "idContact" => true,
        )
      ));
      unset($this->inIdcontact);
      foreach ($mxc as $key => $value) {
        $this->inIdcontact[] = (int) $value->idContact;
        unset($mxc[$key]);
      }
      unset($mxc);
    } else if ($idSmsSelected !=  "") {
      $smsxc = \Smsxc::find(array(
        "conditions" => array(
          "idSms" => $idSmsSelected,
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
  }
  
}