<?php

/**
 * Description of InterpreterTargetMail
 *
 * @author jose.quinones
 */

ini_set('memory_limit', '768M');

require_once(__DIR__ . "/../../general/misc/AutomaticCampaignObj.php");
require_once(__DIR__ . "/../../general/misc/ContactManager.php");
require_once(__DIR__ . "/ApiDataValidation.php");

class InterpreterTargetMail {
  protected $data = array();
  protected $mail;
  protected $idContactlist = array();
  protected $idSegment = array();
  protected $inIdcontact = array();
  protected $validateContactFilter = array();
  protected $contact = array();
  protected $totalContacts;
  protected $bulk;
  protected $offset = 0;
  protected $limit = 8000;
  protected $flag = true;
  public $emailUnique = array();
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
  public $arrayDataUnsubCat = array();
  public $arraycategories = array();
  public $idsContactlists = "";
  public $targetGlobal = "";

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
    \Phalcon\DI::getDefault()->get('logger')->log("Entra en el searchTotalContacts Mail{$this->mail->idMail}");
    $this->targetGlobal = json_decode($this->mail->target);
    $target = json_decode($this->mail->target);
    \Phalcon\DI::getDefault()->get('logger')->log("Este es el target {$this->mail->target} --- Mail{$this->mail->idMail}");
    $this->db->begin();
    \Phalcon\DI::getDefault()->get('logger')->log("Aquí comenzó la transacción --- Mail{$this->mail->idMail}");
    if (isset($target->filters) && count($target->filters) >= 1) {
      $filters = $target->filters;
      $idMail = $filters[0]->mailSelected;
      $idMailLink = $filters[0]->linkSelected;
      $type = $filters[0]->typeFilters;
      $inverted = $filters[0]->inverted;
      $key = $target->filters[0];
      
      while ($this->flag) {
        $this->bulk = new \MongoDB\Driver\BulkWrite;
        //Hace todos los filtros No invertidos
        if ($inverted == "") {
          switch ($type) {
            case 1:
              $mxc = \Mxc::find(array(
                "conditions" => array(
                  "idMail" => $idMail,
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
              foreach ($mxc as $key => $value) {
                $this->inIdcontact[] = (int) $value->idContact;
                unset($mxc[$key]);
              }
              unset($mxc);
            break;
            case 2:
              $mxc = \Mxc::find(array(
                "conditions" => array(
                  "idMail" => $idMail,
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
              foreach ($mxc as $key => $value) {
                $this->inIdcontact[] = (int) $value->idContact;
                unset($mxc[$key]);
              }
              unset($mxc);
            break;
            case 3:
              $mxcxl = \Mxcxl::find(array(
                "conditions" => array(
                  "idMail" => $idMail,
                  "idMailLink" => $idMailLink,    
                ),
                "fields" => array(
                  "idContact" => true,
                )
              ));
              foreach ($mxcxl as $key => $value) {
                $this->inIdcontact[] = (int) $value->idContact;
                if(!empty($this->mail->idAutomaticCampaign)){
                    $this->validateContactFilter[] = (int) $value->idContact;
                }
                unset($mxcxl[$key]);
              }
              unset($mxcxl);
            break;
          }
        }
        //Busca los contactos
        switch ($target->type) {
          case "contactlist":
            $this->getIdContaclist($target);
            $this->getAllCxclFilter();
          break;
          case "segment":
            $this->getIdSegment($target);
            $this->getAllIdContactSegmentFilter();
          break;
          default:
        }
        if(!empty($this->validateContactFilter)){
            unset($this->inIdcontact);
            foreach($this->validateContactFilter as $value){
                $this->inIdcontact[] = $value;
            }
        }
        //js
        //$this->getAllContact();
        //Hace todos los filtros Invertidos  
        if ($inverted == true){
          switch ($type) {
            case 1:
              $mxc = \Mxc::find(array(
                "conditions" => array(
                  "idMail" => $idMail,  
                ),
                "fields" => array(
                  "_id" => false,
                  "idContact" => true
                )
              ));
              foreach ($mxc as $key => $value) {
                $idContact = (int)$value->idContact;
                $search = array_search($idContact, $this->inIdcontact);
                if($search){
                  unset($this->inIdcontact[$search]);
                }
                unset($mxc[$key]);
              }
              $this->inIdcontact = array_values($this->inIdcontact);
              //$this->inIdcontact = array_values(array_unique(array_merge($this->inIdcontact, $inIdcontact)));
              unset($mxc);
            break;
            case 2:
              $mxc = \Mxc::find(array(
                "conditions" => array(
                  "idMail" => $idMail,
                  /*"idContact" => array(
                    '$in' => $this->inIdcontact
                  ), */
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
                ),
                "skip" => $this->offset,
                "limit" => $this->limit
              ));            
              unset($this->inIdcontact);
              foreach ($mxc as $key => $value) {
                $this->inIdcontact[] = (int) $value->idContact;
                unset($mxc[$key]);
              }
              unset($mxc);               
            break;
            case 3:
                if(!empty($this->mail->idAutomaticCampaign)){
                    //\Phalcon\DI::getDefault()->get('logger')->log("+++++++INVERTIDO CASE 3 CAMPAÑA ");
                    $mxc = \Mxc::find(array(
                    "conditions" => array(
                      "idMail" => $idMail,  
                      "unsubscribed" => 0,
                      "spam" => 0,
                      "bounced" => 0,
                      "email" => array(
                        '$nin' => ["", null, "null"]
                      )                 
                    ),
                    "fields" => array(
                      "_id" => false,
                      "idContact" => true
                    )
                  ));
                  unset($this->inIdcontact);
                  foreach ($mxc as $key => $value) {
                    $this->inIdcontact[] = (int) $value->idContact;
                    unset($mxc[$key]);
                  }
                  //\Phalcon\DI::getDefault()->get('logger')->log("***********MXC ".json_encode($this->inIdcontact));
                  $mxcxl = \Mxcxl::find(array(
                    "conditions" => array(
                      "idMail" => $idMail,
                      "idMailLink" => $idMailLink,
                      "idContact" => array(
                        '$in' => $this->inIdcontact
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
                  //\Phalcon\DI::getDefault()->get('logger')->log("***********ANTES DEL DIFF ".json_encode($this->inIdcontact));
                  $this->inIdcontact = array_values(array_diff($this->inIdcontact, $inIdcontact));
                  //\Phalcon\DI::getDefault()->get('logger')->log("***********DESPUES DEL DIFF ".json_encode($this->inIdcontact));
                  unset($inIdcontact);
                  unset($mxcxl);    
                }else{
                    $mxcxl = \Mxcxl::find(array(
                    "conditions" => array(
                      "idMail" => $idMail,
                      "idMailLink" => $idMailLink
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

            break;
          }
        }
        //\Phalcon\DI::getDefault()->get('logger')->log("***********ID CONTACT 311 TARGET MAIL ".json_encode($this->inIdcontact));
        $this->getAllContact();
        $this->offset += $this->limit;
      }
    } else {
      while ($this->flag) {
        $this->bulk = new \MongoDB\Driver\BulkWrite;
        $target = json_decode($this->mail->target);
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
        if (isset($target->filtersOpen) && count($target->filtersOpen) >= 1) {
          \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro open --- Mail{$this->mail->idMail}");
          $this->findFilterOpen($target->filtersOpen);
          $this->flag = false;
          \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro open --- Mail{$this->mail->idMail}");          
        }  
        if (isset($target->filtersClic) && count($target->filtersClic) >= 1) {
          \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro clic --- Mail{$this->mail->idMail}");
          $this->findFilterClic($target->filtersClic);
          $this->flag = false;
          \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro clic --- Mail{$this->mail->idMail}");          
        }
        if (isset($target->filtersTime) && count($target->filtersTime) >= 1) {
          \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro time --- Mail{$this->mail->idMail}");
          $this->findFilterTime($target->filtersTime);
          $this->flag = false;
          \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro time --- Mail{$this->mail->idMail}");
        }
        if (isset($target->filtersOPenClic) && count($target->filtersOPenClic) >= 1) {
          \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro open clic --- Mail{$this->mail->idMail}");
          $this->findFilterOpenClic($target->filtersOPenClic);
          \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro open clic --- Mail{$this->mail->idMail}");
        }
        if (isset($target->filtersNoOPenClic) && count($target->filtersNoOPenClic) >= 1) {
          \Phalcon\DI::getDefault()->get('logger')->log("Entro al filtro no open clic --- Mail{$this->mail->idMail}");
          $this->findFilterNoOpenClic($target->filtersNoOPenClic);
          $this->flag = false;
          \Phalcon\DI::getDefault()->get('logger')->log("Salio al filtro no open clic --- Mail{$this->mail->idMail}");
        }
        $this->getAllContact();
        $this->offset += $this->limit;
      }
    }
    
    \Phalcon\DI::getDefault()->get('logger')->log("Los procesó todos --- Mail{$this->mail->idMail}");
    $this->db->commit();
  }
  
  public function getIdContaclist($target) {
    if (isset($target->contactlists)) {
      foreach ($target->contactlists as $key) {
        $this->idContactlist[] = $key->idContactlist;
      }
    }
  }
  
  public function getIdSegment($target) {
    if (isset($target->segment)) {
      foreach ($target->segment as $key) {
        $this->idSegment[] = $key->idSegment;
      }
    }
  }
  
  public function getAllCxclFilter(){
    if(empty($this->mail->idAutomaticCampaign) || empty($this->mail->idAutomaticCampaign) == null){
    $idContactlist = implode(",", $this->idContactlist);
    $this->idsContactlists = $idContactlist;
    $where = "";
    if(isset($this->inIdcontact) && count($this->inIdcontact)>0){
      $idContact = implode(",", $this->inIdcontact);
      $where = "AND idContact IN ({$idContact})";
      unset($idContact);
    }
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " {$where}"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 "
      . " AND spam = 0 "
      . " AND bounced = 0 "
      . " AND blocked = 0 "
      . " AND singlePhone = 0"
      . " AND idContact > 0"
      . " ORDER BY updated DESC"
      . " LIMIT {$this->limit} OFFSET {$this->offset}";
    unset($idContactlist);
    unset($where);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    unset($this->inIdcontact);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    }
    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
      $this->flag = false;
    }
    unset($sql);
    unset($cxcl);
    }
  }
  
  public function getAllIdContactSegmentFilter() {
    if(empty($this->mail->idAutomaticCampaign) || empty($this->mail->idAutomaticCampaign) == null){        
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        $command = new MongoDB\Driver\Command([
          'aggregate' => 'sxc',
          'pipeline' => [
              ['$match' => ['idSegment' => ['$in' => $this->idSegment],"deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0,'email' => ['$nin' => ["", null, "null"]]]],
              ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]],
              ['$limit' => $this->offset + $this->limit],
              ['$skip'  => $this->offset]
          ],
          'allowDiskUse' => true,
        ]);
        $segment = $manager->executeCommand('aio', $command)->toArray();
    
        for ($i = 0; $i < count($segment[0]->result); $i++) {
          $this->inIdcontact[$i] = $segment[0]->result[$i]->_id;
        }
        if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
          $this->flag = false;
        }
        unset($command);
        unset($segment);
    }
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

    if(count($this->bulk)>0){
    \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++NO ESTA VACIO++++++++++++++");    
    $manager->executeBulkWrite('aio.mxc', $this->bulk);
    }
    
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
  
  private function saveContactMxc($idAccount, $idSubaccount, $flag, $dataValidation) {
    if(!$this->mail->alldb){
      $this->findBlocked($idAccount);
      $this->findBounced($idAccount);
    }
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
            } else if($this->mail->typeUnsuscribed != 0 || $this->targetGlobal->type != "segment"){
              if($this->findUnsubscribedContact($key->idContact,$idSubaccount,$email,$idAccount)){ 
                $arr["bounced"] = time();
                $arr["bouncedCode"] = "22";
              } else { 
                $arr["bounced"] = 0;
                $arr["bouncedCode"] = 0;
              }
            }else { 
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
      if($this->field){
        $customfield = $this->findCustomField($key->idContact);
        $arr["customfield"] = $customfield;
      } else {
        $arr["customfield"] = [];
      }
      $this->bulk->insert($arr);
      /*if ($this->automatic == true) {
        $this->automaticCampaignObj->insNewStep($key->idContact, $this->automaticCampaignConfiguration->idNode, $this->automaticCampaignConfiguration->node, $this->automaticCampaignConfiguration->beforeStep, $this->automaticCampaignConfiguration->date, $this->negation);
      }*/
    }
    unset($arr);
    unset($this->contact);
  }

  private function saveContactMxcSingleMail($idAccount, $idSubaccount, $flag, $dataValidation) {
    \Phalcon\DI::getDefault()->get('logger')->log("Entro a la condicion de single Mail Interpreter Target Mail saveContactMxcSingleMail");
    if(!$this->mail->alldb){
      $this->findBlocked($idAccount);
      $this->findBounced($idAccount);
    }
    
    foreach ($this->contact["result"] as $key) {
      $key["data"] = (object) $key["data"];
      
      $arr = array();
      if ($flag) {
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
            } else if($this->mail->typeUnsuscribed != 0 || $this->targetGlobal->type != "segment"){
              if($this->mail->typeUnsuscribed == 1 && $this->findUnsubscribedContact($key["data"]->idContact,$idSubaccount,$email,$idAccount)){ 
                $arr["bounced"] = time();
                $arr["bouncedCode"] = "22";
              } else { 
                $arr["bounced"] = 0;
                $arr["bouncedCode"] = 0;
              }
            }else { 
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
      if(!in_array($key["data"]->email, $this->emailUnique)){

          $this->emailUnique[] = $key["data"]->email;
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

    }
    unset($arr);
    unset($this->contact);
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
  
  public function getAllCxcl() {
    $idContactlist = implode(",", $this->idContactlist); 
    $this->idsContactlists = $idContactlist;
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 "
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
  
  public function customfield($field){ 
    if(count($field) > 0){
      $this->field = TRUE; 
    }    
  }
  
  public function findCustomField($idContact) {
    $arr = array();
    $cxc = Cxc::findFirst([["idContact" => $idContact]]);
    if(is_array($cxc) || is_object($cxc)){
    //-------------------------------------------------------------------------------------
    
    //Aqui debe ir la mejora:  Se debe cargar la variable $arr solo con los ids de listas de contacto del envio  

    //-------------------------------------------------------------------------------------
      if($this->mail->idMail == 51023 || $this->mail->idMail == '51023'){
        $idContactlist = '24122';
        if (isset($cxc->idContactlist[$idContactlist])) {
          $key = $cxc->idContactlist[$idContactlist];
          array_push($arr, $key);
        }
      } else {
        foreach ($cxc->idContactlist as $value) {
        array_push($arr, $value);
      }
      }
    }
    return $arr;
  }
  
  public function findUnsubscribedContact($idContact, $idSubaccount, $email, $idAccount){    
    $result = false;
    $sql = "SELECT idCategories"
          ." FROM unsubscribed"
          ." WHERE unsubscribed.idSubaccount = ".$idSubaccount
          //." AND unsubscribed.option = 'categorie' AND deleted = 0 AND unsubscribed.email = '{$email}' AND unsubscribed.idContact <> ".(int)$idContact;
          ." AND unsubscribed.option = 'categorie' AND deleted = 0 AND unsubscribed.email = '{$email}'";
    $unsub = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);

    $idCategories_Unsub = array();
    
    if(count($unsub)>0){
        $tmp = array();
        foreach($unsub as $key => $value ){
            $tmp = explode( ',',$value['idCategories']);
            foreach($tmp as $val){
                if (!in_array($val, $idCategories_Unsub)) {
                    $idCategories_Unsub[] = $val;
                }
            }
            $tmp = null;            
        }
        if($this->checkUnsub($idCategories_Unsub, $idContact, $idSubaccount, $email, $idAccount)){
            $query = " SELECT TRIM(TRAILING ',' FROM REPLACE(GROUP_CONCAT(DISTINCT cl.idContactlistCategory, ','),',,',',')) as idCategories from cxcl"
                    ." LEFT JOIN contactlist as cl"
                    ." on cl.idContactlist = cxcl.idContactlist"
                    ." where cxcl.idContactlist IN ({$this->idsContactlists})"
                    ." AND cxcl.unsubscribed = 0 "
                    ." AND cxcl.deleted = 0 "
                    ." AND cxcl.spam = 0 "
                    ." AND cxcl.bounced = 0 "
                    ." AND cxcl.blocked = 0 "
                    ." AND cxcl.singlePhone = 0"
                    ." AND cxcl.idContact = ".(int)$idContact;     
            $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($query); 

            $idCategories_cxcl = array();
            $idCategories_cxcl = explode( ',',$cxcl[0]['idCategories']);        
            $find = array();
            $notfind = array();
            foreach($idCategories_cxcl as $value){
                if(in_array($value, $idCategories_Unsub)){
                    $find[] = $value;
                }else{
                    $notfind[] = $value;           
                }            
            }
            
            if(count($find)>0 && count($notfind)>0){              
               $result = false; 
            }else if(count($notfind)>0 && count($find) == 0){
               $result = false;
            }else if(count($find)>0 && count($notfind) == 0){
               $result = true;
            }   
        }       
        //$this->addMoreContactUnsubscribed($idContact, $email, $idSubaccount, $idCategories_Unsub,$idAccount);        
    }     
   return $result;
  }
  
  public function addMoreContactUnsubscribed($idContact_primary, $email_primary, $idSubaccount, $idscat, $idAccount) {
      //No usar, no recuerdo porque  
      if($find){
            $contact = \Contact::find([["email" => (string) $email_primary, "idSubaccount" => ['$in' => [(string) $idSubaccount, (int) $idSubaccount]], "idAccount" => (string) $idAccount, "deleted" => 0, "idContact" => ['$nin' => [(int) $idContact_primary]]]]);
        if (!empty($contact)) {
            foreach ($contact as $value) {
                $idContact = (int) $value->idContact;
                $query = "SELECT idCategories, idUnsubscribed FROM unsubscribed WHERE idContact = {$idContact}"
                        . " AND deleted = 0 AND unsubscribed.option = 'categorie' AND idSubaccount = " . $idSubaccount;
                $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);

                if (count($c) > 0) {
                    $idUnsubscribed = array();
                    $idCat = array();
                    foreach ($c as $value) {
                        $idUnsubscribed[] = (int) $value['idUnsubscribed'];
                        $idCat = explode(',', $value['idCategories']);
                    }
                    unset($c);
                    foreach ($idscat as $key => $val) {
                        if (!in_array($val, $idCat)) {
                            $idCat[] = $val;
                        }
                    }
                    unset($idscat);
                    $query = "UPDATE unsubscribed SET updated = " . time() . ", idCategories = '" . implode(',', array_values(array_unique($idCat, SORT_REGULAR))) . "',"
                            . " created = " . time() . " WHERE idUnsubscribed IN (" . implode(",", $idUnsubscribed) . ")";
                    $this->db->execute($query);
                }else{
                    $unsubscribed = new \Unsubscribed();                    
                    $unsubscribed->idContact = $idContact;
                    $unsubscribed->motive = "Otro";
                    $unsubscribed->option = "categorie";
                    $unsubscribed->other = "Otro";
                    $unsubscribed->idCategories = implode(",",$idscat);
                    $unsubscribed->idSubaccount = (int) $idSubaccount;
                    if (!$unsubscribed->save()) {
                        foreach ($unsubscribed->getMessages() as $msg) {
                             throw new \InvalidArgumentException($msg);
                        }
                    }
                }
            }
        } 
      }
    }
    
    public function checkUnsub($idscat, $idContact, $idSubaccount, $email, $idAccount) {
    $result = false;
    $sql = "SELECT idCategories"
          ." FROM unsubscribed"
          ." WHERE unsubscribed.idSubaccount = ".$idSubaccount
          ." AND unsubscribed.option = 'categorie' AND deleted = 0 AND unsubscribed.email = '{$email}' AND unsubscribed.idContact = ".(int)$idContact;
          
    $unsub = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    
        if(count($unsub)>0){
            $result = true;
        }else{
            $unsubscribed = new \Unsubscribed();                    
            $unsubscribed->idContact = $idContact;
            $unsubscribed->motive = "Otro";
            $unsubscribed->option = "categorie";
            $unsubscribed->other = "Otro";
            $unsubscribed->idCategories = implode(",",$idscat);
            $unsubscribed->idSubaccount = (int) $idSubaccount;
            $unsubscribed->email = $email;
            if (!$unsubscribed->save()) {                
                foreach ($unsubscribed->getMessages() as $msg) {
                     throw new \InvalidArgumentException($msg);
                }
            }
            $result = true;  
        }
        return $result;    
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
