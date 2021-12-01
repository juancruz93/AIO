<?php

namespace Sigmamovil\Wrapper;

use MongoDB\Driver\Query;
use Psr\Log\InvalidArgumentException;

require_once(__DIR__ . "/../bgprocesses/sender/CustomfieldManagerSms.php");
require_once(__DIR__ . "/../bgprocesses/sender/ImageService.php");
require_once(__DIR__ . "/../bgprocesses/sender/PrepareMailContent.php");

ini_set('memory_limit', '1000M');

include_once( "../app/library/phpexcel/Classes/PHPExcel.php");
include_once( "../app/library/phpexcel/Classes/PHPExcel/Writer/Excel2007.php");

class ContactWrapper extends \BaseWrapper {
  
  function __construct() {
    parent::__construct();
  }
  
  private $contact = array();
  private $contactError = array();
  private $totals;
  private $contactlist;
  private $batchcontact;
  private $cxcl = array();
  private $resultEmail;
  private $countEmail = 0;
  private $resultPhone = 0;
  private $countPhone = 0;
  private $idAccount;
  private $account;
  private $detailConfig;
  private $offsetC = 0;
  private $limitC = 5000;
  private $flagC = true;
  private $dataUnsubscribe;
  public $modelsManager;
  private $dataUpdate = 0;
  private $idContact = 0;
  private $singlePhone = null;
  private $whereTypeExport;
  private $idCustomFields = array();
  private $idContactlist = null;

//  public function findContact($page, $idContactlist, $stringSearch) {
//    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
//    $emailSearch = $stringSearch;
//    if($stringSearch != -1){
//      if($stringSearch != ""){
//        
//      }
//    }
//  }

  public function setTypeExport($type){
    
    switch ($type) {
    case 0:
        $this->whereTypeExport = "";
        break;
    case 1:
        $this->whereTypeExport = "AND status = 'active'";
        break;
    case 2:
        $this->whereTypeExport = "AND status = 'unsubscribed'";
        break;
    case 3:
        $this->whereTypeExport = "AND status = 'bounced'";
        break;
    case 4:
        $this->whereTypeExport = "AND status = 'spam'";
        break;
    case 5:
        $this->whereTypeExport = "AND status = 'blocked'";
        break;                
    }
  }

  public function findContact($page, $idContactlist, $data) {
    
    $stringSearch = ""; 

    if(isset($data)) $stringSearch = $data->stringsearch;

    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $fields = array("name", "email", "phone", "lastname");
    //Solo se busca por las opciones anteriores no por campos personaolizados
    foreach ($customfield as $key) {
        array_push($this->idCustomFields, $key->idCustomfield);
    }

    if ($stringSearch != -1) {
      $where = [];
      if ($stringSearch != "") {
        $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
        $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
        $stringSearch = explode(",", $stringSearch);
        $string = trim($stringSearch[0]);
        if(filter_var($string, FILTER_VALIDATE_EMAIL)){
          $where = ["email" => strtolower($string)];
        } else if(is_numeric($string)){
          $where = ["phone" => $string];
        } else {
          $where = ['idAccount' => (string) $idAccount, 'deleted' => 0];
          $arr[] = ['name' => ['$regex' => ".*$string.*", '$options' => "i"]];
          $arr[] = ['lastname' => ['$regex' => ".*$string.*", '$options' => "i"]];
          $where['$or'] = $arr;
        }

        $this->totals = \Contact::find([$where]);
        $ids = "";
        foreach ($this->totals as $key) {
          if($key->idAccount == (string)$idAccount && $key->deleted == 0){
            $ids .= $key->idContact . ",";
          }          
        }
        $ids = trim($ids, ',');
        $this->totals = 0;
        $this->data = [];
        if (!empty($ids)) {
          $statusgeneral = '';
          if ($data->stateend == '') {
            $oo = \Cxcl::find(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC"]);
          } else {
            if ($data->stateend == 'Activos') {
              $statusgeneral = 'active';
              $oo = \Cxcl::find(["Cxcl.active<>0 AND Cxcl.status='active' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC"]);
            } elseif ($data->stateend == 'Desuscritos') {
              $statusgeneral = 'unsubscribed';
              $oo = \Cxcl::find(["Cxcl.unsubscribed<>0 AND status='unsubscribed' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC "]);
            } elseif ($data->stateend == 'Rebotados') {
              $statusgeneral = 'bounced';
              $oo = \Cxcl::find(["Cxcl.bounced<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC"]);
            } elseif ($data->stateend == 'Spam') {
              $statusgeneral = 'spam';
              $oo = \Cxcl::find(["Cxcl.spam<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC"]);
            } elseif ($data->stateend == 'Bloqueados') {
              $statusgeneral = 'blocked';
              $oo = \Cxcl::find(["Cxcl.blocked<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact IN ({$ids}) ORDER BY Cxcl.updated DESC"]);
            }
            
          }

          $where = [];
          $where["idAccount"] = (string) $idAccount;
          $where["deleted"] = 0;
          $in = array();
          for ($i = 0; $i < count($oo); $i++) {
            $in[$i] = (int) $oo[$i]->idContact;
          }
          $where = array("idContact" => ['$in' => $in], "deleted" => 0);
          $this->totals = \Contact::find([$where]);
          $this->totals = count($this->totals);
          $this->data = \Contact::find(array($where, 'sort' => ["updated" => -1], 'limit' => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, 'skip' => $page));
        }
      } else {
        
        $sql = "SELECT DISTINCT idContact FROM cxcl WHERE idContactlist = {$idContactlist} AND deleted = 0";
        $oo = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
        for ($i = 0; $i < count($oo); $i++) {
          $in[$i] = (int) $oo[$i]['idContact'];
        }
        unset($oo);
        $where = array("idContact" => ['$in' => $in], "deleted" => 0);
        $this->data = \Contact::find(array($where, 'sort' => ["updated" => -1], 'limit' => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, 'skip' => $page));

        $sql = "SELECT COUNT(DISTINCT(idContact)) AS count FROM cxcl WHERE idContactlist = {$idContactlist} AND deleted = 0";
        $this->totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql)[0]['count'];
      }
    } else {
      $statusgeneral = '';
      if ($data->stateend == '') {
        $this->cxcl = $this->modelsManager->createBuilder()
                ->from('Cxcl')
                ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                ->getQuery()
                ->execute();
        $this->totals = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
      } else {
        if ($data->stateend == 'Activos') {

          $this->cxcl = $this->modelsManager->createBuilder()
                  ->from('Cxcl')
                  ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                  ->where("Cxcl.active<>0 AND Cxcl.status='active' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                  ->getQuery()
                  ->execute();
          $this->totals = \Cxcl::count(["Cxcl.active<>0 AND Cxcl.status='active' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
        } elseif ($data->stateend == 'Desuscritos') {

          $this->cxcl = $this->modelsManager->createBuilder()
                  ->from('Cxcl')
                  ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                  ->where("Cxcl.unsubscribed<>0 AND status='unsubscribed' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                  ->getQuery()
                  ->execute();
          $this->totals = \Cxcl::count(["Cxcl.unsubscribed<>0 AND status='unsubscribed' AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
        } elseif ($data->stateend == 'Rebotados') {

          $this->cxcl = $this->modelsManager->createBuilder()
                  ->from('Cxcl')
                  ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                  ->where("Cxcl.bounced<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                  ->getQuery()
                  ->execute();
          $this->totals = \Cxcl::count(["Cxcl.bounced<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
        } elseif ($data->stateend == 'Spam') {

          $this->cxcl = $this->modelsManager->createBuilder()
                  ->from('Cxcl')
                  ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                  ->where("Cxcl.spam<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                  ->getQuery()
                  ->execute();
          $this->totals = \Cxcl::count(["Cxcl.spam<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
        } elseif ($data->stateend == 'Bloqueados') {

          $this->cxcl = $this->modelsManager->createBuilder()
                  ->from('Cxcl')
                  ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
                  ->where("Cxcl.blocked<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ORDER BY Cxcl.updated DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
                  ->getQuery()
                  ->execute();
          $this->totals = \Cxcl::count(["Cxcl.blocked<>0 AND Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
        }
      }


      $in = array();
      for ($i = 0; $i < count($this->cxcl); $i++) {
        $in[$i] = (int) $this->cxcl[$i]->idContact;
      }
      $where = array("idContact" => ['$in' => $in], "deleted" => 0);
      $this->data = \Contact::find(array($where, 'sort' => ["updated" => -1]));
    }

    unset($arr);
    unset($fields);
    unset($customfield);


//    echo count($this->data);
//    echo  $this->totals;
//    exit;
//    $this->contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $idContactlist)));
    unset($where);
    unset($page);
    $this->modelData($idContactlist, $in);
  }

  public function findAllContacts($idContactlist) {
    $this->dataallcontactxcl = \Cxcl::find([
                "columns" => "idContact",
                "conditions" => "idContactlist=?0 and deleted=0",
                "bind" => [0 => $idContactlist]
    ]);
    $this->modelAllContacts();
  }

  public function modelAllContacts() {
    $this->allcontacts = array();

    foreach ($this->dataallcontactxcl as $data) {
      $this->allcontacts[] = (int) $data->idContact;
    }
  }

  public function findContactAccount($page, $stringSearch) {
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

    $this->cxcl = $this->modelsManager->createBuilder()
            ->columns('Cxcl.*')
            ->from('Cxcl')
            ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
            ->join('Subaccount', 'Contactlist.idSubaccount = Subaccount.idSubaccount')
            ->where("Contactlist.deleted = 0 AND Subaccount.idAccount  = {$idAccount}")
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, $page)
            ->getQuery()
            ->execute();

    $in = array();
    for ($i = 0; $i < count($this->cxcl); $i++) {
      $in[$i] = (int) $this->cxcl[$i]->idContact;
    };
    $fields = array("email", "phone");
    /* $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = ?0", "bind" => [0 => $idContactlist]]);
      foreach ($customfield as $key) {
      array_push($fields, $key->idCustomfield);
      } */
    $where = array("idContact" => ['$in' => $in]);
    unset($in);
    if ($stringSearch != -1) {
      $stringSearch = explode(",", $stringSearch);
      foreach ($fields as $value) {
        foreach ($stringSearch as $key) {
          if ($key or $key != "" or ! empty($key)) {
            $key = trim($key);
            $arr[] = [$value => ['$regex' => ".*$key.*"]];
            $where['$or'] = $arr;
          }
        }
      }
    }
    unset($arr);
    unset($fields);
    unset($customfield);
    $this->data = \Contact::find(array($where));

    $arr = array();

    foreach ($this->data As $value) {
      unset($value->_id);
      unset($value->idSubaccount);
      
      $value = json_encode($value);
      $value = json_decode($value, true);

      $arr[] = $value;
    }

    $this->totals = $this->modelsManager->createBuilder()
            ->columns(["Count(*) As count"])
            ->from('Cxcl')
            ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
            ->join('Subaccount', 'Contactlist.idSubaccount = Subaccount.idSubaccount')
            ->where("Contactlist.deleted = 0 AND Subaccount.idAccount  = {$idAccount}")
            ->getQuery()
            ->execute();

    unset($where);
    unset($page);
    $this->contact = array("total" => $this->totals[0]['count'],
        "total_pages" => ceil($this->totals[0]['count'] / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT),
        "items" => $arr);
  }

  public function modelData($idContactlist, $in) {
    $this->contact = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "idscontacts" => $this->allcontacts);
    $arr = array();
    foreach ($this->data as $key => $val) {
      $cxc = \Cxc::findFirst([["idContact" => $val->idContact]]);
      $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                  "bind" => array(0 => $val->idContact, 1 => $idContactlist)));
      $this->data[$key]->unsubscribed = $thiscontact->unsubscribed;
      unset($val->_id);
      unset($val->idSubaccount);
      $val = json_encode($val);
      $val = json_decode($val, true);
      if($thiscontact->blocked > 0){
        if($val->email != "" && $val->phone == ""){
          $val['blockedEmail'] = $thiscontact->blocked;
          $val['blockedPhone'] = "";
        } else if($val->email == "" && $val->phone != ""){
          $val['blockedEmail'] = "";
          $val['blockedPhone'] = $thiscontact->blocked;
        } else if($val->email != "" && $val->phone != ""){
          $val['blockedEmail'] = $thiscontact->blocked;
          $val['blockedPhone'] = $thiscontact->blocked;
        }
      }
        
      $val["status"] = $thiscontact->status;

      if (isset($cxc->idContactlist[$idContactlist])) {
        foreach ($cxc->idContactlist[$idContactlist] as $p => $v) {
          if ($v != null) {            
            if( in_array($p, $this->idCustomFields) ){
               $customfield = ["value" => $v["value"], "type" => $v["type"], "idCustomfield" => $p];
                $val[$v["name"]] = $customfield; 
            }
          }
        }
      }

      $sql = "SELECT contactlist.idContactlist, contactlist.name FROM cxcl JOIN  contactlist ON cxcl.idContactlist = contactlist.idContactlist"
              . " WHERE cxcl.deleted = 0 AND cxcl.idContact  = {$val['idContact']} "
              . " AND contactlist.idSubaccount = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
      $val["contactlist"] = $this->db->fetchAll($sql);

      array_push($arr, $val);
    }
    array_push($this->contact, array("items" => $arr));
    unset($arr);
//    unset($newArray);
  }

  public function getContact() {
    return $this->contact;
  }

  public function getContactError() {
    return $this->contactError;
  }

  public function setDataBatch($contact) {
    return $this->batchcontact = $contact;
  }

  public function validateBlockedEmailAndPhone() {
    $contact->blockedPhone = "";
    $contact->blockedEmail = "";
    $status = "active";
    if (count($oldcontacts) == 0) {
      if (isset($this->data->email) and $this->isEmailBlocked($this->data->email)) {
        $contact->blockedEmail = time();
        $status = "blocked";
      }

      if (isset($this->data->phone) and $this->isPhoneBlocked($this->data->phone, $this->data->indicative)) {
        $contact->blockedPhone = time();
        $status = "blocked";
      }
    }
  }

  public function validateContactBatch($idContactlist) {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    foreach ($this->batchcontact as $key) {
      $flagError = false;
      $contact = new \Contact();
      $contact->email = ((isset($key->email) && trim($key->email) != "") ? trim($key->email) : "");
      $country = \Country::findFirst(array(
        "conditions" => "idCountry = ?0",
        "bind" => array($key->indicative)
      ));
      $contact->indicative = ((isset($country->phoneCode) && trim($country->phoneCode) != "") ? trim($country->phoneCode) : "");      $contact->phone = ((isset($key->phone) && trim($key->phone) != "") ? trim($key->phone) : "");
      $contact->name = ((isset($key->name) && trim($key->name) != "") ? trim($key->name) : "");
      $contact->lastname = ((isset($key->lastname) && trim($key->lastname) != "") ? trim($key->lastname) : "");
      $contact->birthdate = ((isset($key->birthdate) && trim($key->birthdate) != "") ? trim($key->birthdate) : "");
      $contact->error = "";

      $oldcontacts = $this->getOneContact($contact,$idAccount);
      if ($oldcontacts != false) {
        $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $oldcontacts->idContact, 1 => $idContactlist]]);
        if ($cxcl) {
          $flagError = true;
          $contact->error .= "Ya existe un contacto asociado con el telefono o email ingresado y No se encuentra eliminado, "
            . " si continua la información del contacto se actualizara con la ingresada.";
        } else {
          $flagError = true;
          $contact->error .= "Ya existe un contacto asociado con el telefono o email ingresado y No se encuentra eliminado, "
            . " si continua la información del contacto se actualizara con la ingresada.";
        }
      }
      if ($contact->phone == "" && $contact->email == "" && $contact->indicative == "") {
        unset($contact->_id);
        $flagError = true;
        $contact->error .= "Debe indicar aunque sea el un correo o un telÃƒÂ©fono. ";
      }
      if (!$this->validateDate($contact->birthdate) && trim($contact->birthdate) != "") {
        $flagError = true;
        $contact->error .= "Error en la fecha. ";
      }
      if (!$this->validateIndicative($contact->indicative)) {
        $flagError = true;
        $contact->error .= "Error en el indicativo. ";
      }
      if ($contact->email != '' and ( filter_var($contact->email, FILTER_VALIDATE_EMAIL) == false)) {
        $flagError = true;
        $contact->error .= "Error en el email. ";
      }
      if ($flagError) {
        $this->contactError[] = $contact;
      }
    }
  }

  public function saveContactBatch($idContactlist) {

    $contactlist = \Contactlist::findFirst(array(
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $idContactlist)
    ));
    if (!$contactlist) {
      throw new \InvalidArgumentException("No se encontrÃƒÂ³ la lista de contactos.");
    }
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->UserType->idSubaccount;
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $saxs = null;
    foreach ($contactlist->Subaccount->Saxs as $value) {
      if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
        foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $item) {
          if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
            $saxs = $item;
            break;
          }
        }
      }
    }
    $data = json_encode($this->batchcontact);
    $data = json_decode($data, true);
    if (isset($saxs)) {
      if (($saxs->amount == 0) || count($data) > $saxs->amount) {
        throw new InvalidArgumentException("No cuenta con la capacidad suficiente para crear mÃƒÂ¡s contactos, por favor contacte a su administrador.");
      }
    }
    foreach ($this->batchcontact as $key) {
      $flagError = false;
      $contact = new \Contact();
      $contactManger = new \Sigmamovil\General\Misc\ContactManager();
      $nextIdContact = $contactManger->autoIncrementCollection("id_contact");
      $contact->idContact = $nextIdContact;

      $contact->email = ((isset($key->email) && trim($key->email) != "") ? trim($key->email) : "");
      $contact->phone = ((isset($key->phone) && trim($key->phone) != "") ? trim($key->phone) : "");
      if(isset($key->indicative)){
        $country = \Country::findFirst(array(
          "conditions" => "idCountry = ?0",
          "bind" => array($key->indicative)
        ));
        $contact->indicative = $country->phoneCode;
        $cantdigits = strlen($contact->phone);
        if ($cantdigits < $country->minDigits || $cantdigits > $country->maxDigits) {
          throw new \InvalidArgumentException("El nÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Âºmero telefÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³nico no cumple con la cantidad de digitos mÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­nimos y mÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ximos de acuerdo al paÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­s");
        }
      } else {
        $contact->indicative = "";
      }
      $contact->name = ((isset($key->name) && trim($key->name) != "") ? trim($key->name) : "");
      $contact->lastname = ((isset($key->lastname) && trim($key->lastname) != "") ? trim($key->lastname) : "");
      $contact->birthdate = ((isset($key->birthdate) && trim($key->birthdate) != "") ? trim($key->birthdate) : "");
      $contact->deleted = (int) 0;
      
      $contact->blockedPhone = "";
      $contact->blockedEmail = "";

      if ($contact->phone == "" && $contact->email == "" && $contact->indicative == "") {
        unset($contact->_id);
        $flagError = true;
      }

      if (isset($key->birthdate)) {
        if (!$this->validateDate($key->birthdate) && $key->birthdate != "") {
          $flagError = true;
        }
      }

      if (!$this->validateIndicative($contact->indicative)) {
        $flagError = true;
      }

      if ($contact->email != '' and ( filter_var($contact->email, FILTER_VALIDATE_EMAIL) == false)) {
        $flagError = true;
      }
      $singlePhone = 0;
      if (!$flagError) {
        $contact->idSubaccount = $idSubaccount;
        $contact->deleted = (int) 0;
        $contact->idAccount = $idAccount; 
        $status = "active";
        if($contact->email != ""  && $contact->phone != "" && $this->isEmailPhoneBlocked($contact->email, $contact->phone, $contact->indicative)){
          $contact->blockedEmail = time();
          $contact->blockedPhone = time();
          $status = "blocked";
        } else if ($contact->email != ""  && $this->isEmailBlocked($contact->email) ) {
          $contact->blockedEmail = time();
          $status = "blocked";
        } else if ($contact->phone != "" && $this->isPhoneBlocked($contact->phone, $contact->indicative)) {
          $contact->blockedPhone = time();
          $status = "blocked";
        }
        $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
        $obj = array();
        foreach ($customfield as $keycustomfield) {
          $name = $keycustomfield->alternativename;
          if (isset($this->data->$name)) {
            $value = $this->data->$name;
            if ($keycustomfield->type != "Multiselect") {
              $value = trim($value);
            }
            $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => $value, "type" => $keycustomfield->type];
          } else {
            $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => "", "type" => $keycustomfield->type];
          }
        }
        unset($customfield);
        if ($contact->email != "" && $contact->indicative == "" && $contact->phone == "") {
          $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => "", "phone" => "", "deleted" => 0];
          $contactValidate = \Contact::findFirst([$where]);
          unset($where);
          if($contactValidate){
            $idContact = $contactValidate->idContact;
            $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
            if ($cxcl) {
              $this->dataUpdate = 0;
            } else {
              $this->dataUpdate = 1;
            }
            unset($cxcl);
          }
        } else if ($contact->email == "" && $contact->indicative != "" && $contact->phone != ""){
          $singlePhone = time();
          $where = ["idAccount" => $idAccount, "email" => "", "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
          $contactValidate = \Contact::findFirst([$where]);
          unset($where);
          if($contactValidate){
            $idContact = $contactValidate->idContact;
            $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
            if ($cxcl) {
              $this->dataUpdate = 0;
            } else {
              $this->dataUpdate = 1;
            }
            unset($cxcl);
          }
        } else if ($contact->email != "" && $contact->indicative != "" && $contact->phone != "") {
          $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
          $contactValidate = \Contact::findFirst([$where]);
          unset($where);
          if($contactValidate){
            $idContact = $contactValidate->idContact;
            $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
            if ($cxcl) {
              $this->dataUpdate = 0;
            } else {
              $this->dataUpdate = 1;
            }
            unset($cxcl);
          }
        }
        //
        if ($contactValidate != false){
          if($this->dataUpdate == 1){            
            
            $cxc = \Cxc::findFirst([["idContact" =>(int) $idContact]]);
            
            if(!empty($cxc)){             
                $tmp = $cxc->idContactlist;
                foreach($obj as $value => $key){
                    $tmp[$contactlist->idContactlist][$value] = $key;
                }
                $cxc->idContactlist = null;
                $cxc->idContactlist= (object) $tmp;
                $cxc->save();
                unset($cxc);
            } 
            //
            $cxcl = new \Cxcl();
            $cxcl->idContact = $idContact;
            $cxcl->idContactlist = $idContactlist;
            $cxcl->unsubscribed = 0;
            $cxcl->$status = time();
            $cxcl->status = $status;
            $cxcl->singlePhone = $singlePhone;

            if (!$cxcl->save()) {
              foreach ($cxcl->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
              }
            }
            unset($cxcl);
          }
          $contactValidate->name = $contact->name;
          $contactValidate->lastname = $contact->lastname;
          $contactValidate->birthdate = $contact->birthdate;
          $contactValidate->updated = time();
          $contactValidate->idSubaccount = $idSubaccount;
          if (!$contactValidate->save()) {
            foreach ($contactValidate->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
          unset($contactValidate);
          $this->addBlockedContactExist($idContact); 
        } else {
          if ($contact->save()) {
              
            $cxc = new \Cxc();
            $cxc->idContact = $contact->idContact;
            $cxc->idContactlist = [$idContactlist => $obj];
            $cxc->save();
            unset($cxc);              
            //    
            $cxcl = new \Cxcl();
            $cxcl->idContact = $contact->idContact;
            $cxcl->idContactlist = $idContactlist;
            $cxcl->unsubscribed = 0;
            $cxcl->singlePhone = $this->singlePhone;
            $cxcl->$status = time();
            $cxcl->status = $status;
            $cxcl->singlePhone = $singlePhone;
            $this->setAccountants($idContactlist);

            if (!$cxcl->save()) {
              $this->db->rollback();
              foreach ($cxcl->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
              }
            }
            $this->addBlockedContactExist($contact->idContact);
            $validateEmail = $this->validateEmail($contact->email);

            if (!$validateEmail) {
              if (isset($contact->email)) {
                $domainEmail = $this->extractDomainEmail($contact->email);
                $domain = \Domain::findFirst([["domain" => $domainEmail]]);
                if (!$domain) {
                  $domain = $this->createDomain($domainEmail);
                }
                $email->idDomain = $domain->idDomain;
                $email = new \Email();
                $email->email = $contact->email;
                $email->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
                if (!$email->save()) {
                  foreach ($email->getMessages() as $message) {
    //                throw new \InvalidArgumentException($message);
                  }
                  $this->trace("fail", "No se logro crear una cuenta");
                }
              }
            }
            $validatePhone = $this->validatePhone($contact->phone);
            if (!$validatePhone) {
              if (isset($contact->phone)) {
                $phone = new \Phone();
                $phone->indicative = $contact->indicative;
                $phone->phone = $contact->phone;
                $phone->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
                if (!$phone->save()) {
                  foreach ($phone->getMessages() as $message) {
    //                throw new \InvalidArgumentException($message);
                  }
                  $this->trace("fail", "No se logro crear una cuenta");
                }
              }
            }
          } else {
            throw new \Exception("No se pudo guardar el registro si el problema persiste contacte con el administrador");
          }
        }
      }
    }
    $this->setAccountants($idContactlist);
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);
  }

  public function validateEmail($email) {
    $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $email = \Email::findFirst([["email" => $email, "idAccount" => $idAccount]]);
    if ($email) {
      unset($idAccount);
      unset($email);
      return true;
    }
    unset($idAccount);
    unset($email);
    return false;
  }

  public function validatePhone($phone) {
    $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $phone = \Phone::findFirst([["phone" => $phone, "idAccount" => $idAccount]]);
    if ($phone) {
      unset($idAccount);
      unset($phone);
      return true;
    }
    unset($idAccount);
    unset($phone);
    return false;
  }

  public function validateIndicative($indicative) {
    $indicative = \Indicative::findFirst(["conditions" => "phonecode = ?0", "bind" => [0 => $indicative]]);
    if ($indicative) {
      return true;
    }
    return false;
  }

  public function saveContact($idContactlist) {
    $idAccount = (string) \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $idSubaccount = (string) \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idSubaccount;
    $contactlist = \Contactlist::findFirst(array(
      "conditions" => "idContactlist = ?0",
      "bind" => array(0 => $idContactlist)
    ));

    if (!$contactlist) {
      throw new \InvalidArgumentException("No se encontró la lista de contactos.");
    }
    $saxs = null;
    foreach ($contactlist->Subaccount->Saxs as $value) {
      if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
        foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $item) {
          if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
            $saxs = $item;
          }
        }
      }
    }
    if (isset($saxs) && $saxs->amount <= 0) {
      throw new InvalidArgumentException("No cuenta con la capacidad suficiente para importar más contactos, por favor contacte a su administrador.");
    }

    $flagEmail = true;
    $flagPhone = true;

    if (!isset($this->data->email) || filter_var($this->data->email, FILTER_VALIDATE_EMAIL) == false) {
      $flagEmail = false;
      $cemail = "-";
    } else {
      $cemail = $this->data->email;
    }
    if (!isset($this->data->phone) || !isset($this->data->indicative) || !is_numeric($this->data->phone) || !is_numeric($this->data->indicative)) {
      $flagPhone = false;
      $cphone = "-";
      $cindicative = "-";
    } else {
      $cphone = $this->data->phone;
    
      if(isset($this->data->fromApi)){
        $country = \Country::findFirst(array(
                 "conditions" => "phoneCode = ?0",
                 "bind" => array($this->data->indicative)
                 ));                   
      }else{
              $country = \Country::findFirst(array(
                 "conditions" => "idCountry = ?0",
                 "bind" => array($this->data->indicative)
                 )); 
      }
      $cantdigits = strlen($cphone);
      if ($cantdigits < $country->minDigits || $cantdigits > $country->maxDigits) {
        throw new \InvalidArgumentException("El número telefónico no cumple con la cantidad de digitos mínimos y mÃƒÂ¡ximos de acuerdo al paÃƒÂ­s");
      }
      $this->data->indicative = $country->phoneCode;
      $cindicative = $this->data->indicative;
    }
    if (!$flagEmail && !$flagPhone) {
      throw new \InvalidArgumentException("El contacto debe contener al menos el correo electrónico o el nÃƒÂºmero del mÃƒÂ³vil con su respectivo indicativo");
    }

    if (isset($this->data->birthdate) && strtotime($this->data->birthdate) > time()) {
      throw new \InvalidArgumentException("La fecha de nacimiento no puede superior a la fecha actual");
    }

    $contact = new \Contact();
    $contactManger = new \Sigmamovil\General\Misc\ContactManager();
    $nextIdContact = $contactManger->autoIncrementCollection("id_contact");
    $contact->idContact = $nextIdContact;
    $contact->blockedPhone = "";
    $contact->blockedEmail = "";
    $contact->phone = "";
    $contact->indicative = "";
    $contact->email = "";
    $contact->name = "";
    $contact->lastname = "";
    $contact->birthdate = "";

    $contact->idSubaccount = $idSubaccount;
    $contact->idAccount = $idAccount;
    $contact->deleted = 0;

    foreach ($this->data as $key => $value) {
      if ($value != "" && $value != null && !empty($value)) {
        if ($key == "indicative" || $key == "phone") {
          if ($flagPhone) {
            $contact->$key = $value;
          }
        } else if ($key == "email") {
          if ($flagEmail) {
            if(isset($this->data->fromApi)){
                $contact->$key = strtolower($value);   
            }else{
                $contact->$key = $value;   
            }            
          }
        } else {
          if ($key == "name" || $key == "lastname" || $key == "birthdate") {
            $contact->$key = $value;
          }
        }
      }
    }
    
    $status = "active";
    if($contact->email != ""  && $contact->phone != "" && $this->isEmailPhoneBlocked($contact->email,$contact->phone, $contact->indicative)){
      $contact->blockedEmail = time();
      $contact->blockedPhone = time();
      $status = "blocked";
    } else if ($contact->email != ""  && $this->isEmailBlocked($contact->email) ) {
      $contact->blockedEmail = time();
      $status = "blocked";
    } else if ($contact->phone != "" && $this->isPhoneBlocked($contact->phone, $contact->indicative)) {
      $contact->blockedPhone = time();
      $status = "blocked";
    }

    if (!isset($this->data->valid)) {
      $this->data->valid = false;
    }
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $obj = array();
    foreach ($customfield as $keycustomfield) {
      $name = $keycustomfield->alternativename;
      if (isset($this->data->$name)) {
        $value = $this->data->$name;
        if ($keycustomfield->type != "Multiselect") {
          $value = trim($value);
        }
        $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => $value, "type" => $keycustomfield->type];
      } else {
        $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => "", "type" => $keycustomfield->type];
      }
    }
    unset($customfield);
    $singlePhone = 0;
    if (!$this->data->valid) {
      if ($contact->email != "" && $contact->indicative == "" && $contact->phone == "") {
        $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => "", "phone" => "", "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
            if(isset($this->data->fromApi)){
             $this->data->validateConfirm = true;   
            }
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate != false && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      } else if ($contact->email == "" && $contact->indicative != "" && $contact->phone != ""){
        $singlePhone = time();
        $where = ["idAccount" => $idAccount, "email" => "", "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
              if(isset($this->data->fromApi)){
             $this->data->validateConfirm = true;   
            }
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      } else if ($contact->email != "" && $contact->indicative != "" && $contact->phone != "") {
        $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
              if(isset($this->data->fromApi)){
             $this->data->validateConfirm = true;   
            }
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      }
    }

    if ($this->data->validateConfirm) {
      if($this->dataUpdate == 1){
      
    $cxc = \Cxc::findFirst([["idContact" =>(int) $idContact]]);
    
    if(!empty($cxc)){             
      $tmp = $cxc->idContactlist;
      foreach($obj as $value => $key){
        $tmp[$contactlist->idContactlist][$value] = $key;
      }
      $cxc->idContactlist = null;
      $cxc->idContactlist= (object) $tmp;
      $cxc->save();
      unset($cxc);
    }               
        //
        $cxcl = new \Cxcl();
        $cxcl->idContact = $idContact;
        $cxcl->idContactlist = $idContactlist;
        $cxcl->unsubscribed = 0;
        $cxcl->$status = time();
        $cxcl->status = $status;
        $cxcl->singlePhone = $singlePhone;

        if (!$cxcl->save()) {
          foreach ($cxcl->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        unset($cxcl);
      }
      $contactValidate->name = $contact->name;
      $contactValidate->lastname = $contact->lastname;
      $contactValidate->birthdate = $contact->birthdate;
      $contactValidate->updated = time();
      $contactValidate->idSubaccount = $idSubaccount;
      if (!$contactValidate->save()) {
        foreach ($contactValidate->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      if(isset($this->data->fromApi)){
        $this->idContact = $idContact;   
      } 
      unset($contactValidate);
      $this->addBlockedContactExist($idContact); 
      
    } else {
      if ($contact->save()) {
      
    $cxc = new \Cxc();
    $cxc->idContact = $contact->idContact;
    $cxc->idContactlist = [$idContactlist => $obj];
    $cxc->save();
    unset($cxc); 
    //
        $cxcl = new \Cxcl();
        $cxcl->idContact = $contact->idContact;
        $cxcl->idContactlist = $idContactlist;
        $cxcl->unsubscribed = 0;
        $cxcl->$status = time();
        $cxcl->status = $status;
        $cxcl->singlePhone = $singlePhone;

        if (!$cxcl->save()) {
          $contact->delete();
          foreach ($cxcl->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $this->addBlockedContactExist($contact->idContact);         
        $validateEmail = $this->validateEmail($contact->email);
        if (!$validateEmail) {

          if (isset($contact->email)) {
            $email = \Email::findFirst([["email" => $contact->email, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);

            if (!$email) {
              $email = new \Email();
            }
            $email->email = $contact->email;
            $domainEmail = $this->extractDomainEmail($contact->email);
            $domain = \Domain::findFirst([["domain" => $domainEmail]]);
            if (!$domain) {
              $domain = $this->createDomain($domainEmail);
            }
            $email->idDomain = $domain->idDomain;
            $email->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
            $email->deleted = 0;
            if (!$email->save()) {
              foreach ($email->getMessages() as $message) {

              }
              $this->trace("fail", "No se logro crear una cuenta");
            }
          }
        } else {
          $email = \Email::findFirst([["email" => $contact->email, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
          $email->deleted = 0;
          if (!$email->save()) {
            foreach ($email->getMessages() as $message) {

            }
            $this->trace("fail", "No se logro crear una cuenta");
          }
        }
        $validatePhone = $this->validatePhone($contact->phone);
        if (!$validatePhone) {
          if (isset($contact->phone)) {
            $phone = \Phone::findFirst([["phone" => $contact->email, "indicative" => $contact->indicative, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
            if (!$phone) {
              $phone = new \Phone();
            }
            $phone->indicative = $contact->indicative;
            $phone->phone = $contact->phone;
            $phone->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
            $phone->deleted = 0;
            if (!$phone->save()) {
              foreach ($phone->getMessages() as $message) {
  //                throw new \InvalidArgumentException($message);
              }
              $this->trace("fail", "No se logro crear una cuenta");
            }
          }
        }
      
        $this->setAccountants($idContactlist);
        $sql = "CALL updateCountersAccount({$contactlist->Subaccount->idAccount})";
        $this->db->fetchAll($sql);
        if(isset($this->data->fromApi)){
            $this->idContact = $contact->idContact;   
        } 
        $segmentmanager = new \Sigmamovil\General\Misc\SegmentManager();
        $segmentmanager->addOneContact($contact->idContact, $idContactlist);
      } else {
        throw new \Exception("No se pudo guardar el registro si el problema persiste contacte con el administrador");
      }
    }
  }

  public function deleteContact($data) {
    $msg = array();
    $idAccount = $this->user->Usertype->Subaccount->idAccount;
    if ($data->type == "deletedOnly") {
      
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
      $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
      $customLogger->idContactlist = $data->idContactlist;
      $customLogger->idContact = $data->idContact;
      $customLogger->email = $data->email;
      $customLogger->indicative = $data->indicative;
      $customLogger->phone = $data->phone;
      $customLogger->typeName = "deletedOnlyMethod";
      $customLogger->detailedLogDescription = "El contacto en esta lista se ha eliminado correctamente";
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
      unset($customLogger);

      $dataContact = $this->getAllContact($data,$idAccount);
      $dataID = array();
      foreach ($dataContact as $value) {
        $dataID[] = $value->idContact;
      }
      unset($dataContact);
      $listId = implode(",", $dataID);
      
      foreach ($dataID as $value) {
        
        $contactsegment = \Sxc::find([["idContact" => $value]]);
        
        if( $contactsegment != false ){
            
            foreach( $contactsegment as $value2){
                $value2->deleted = time();
                $value2->save();
            }            
        }
        unset($contactsegment);          
      }
      
      unset($dataID);
      $cxcl = \Cxcl::find(array(
        "conditions" => "idContact IN ({$listId}) and idContactlist = ?1 and deleted = 0",
        "bind" => array(1 => $data->idContactlist)
      ));
      unset($listId);
      foreach ($cxcl as $value) {
        $value->deleted = time();
        $value->active = 0;

        if (!$value->save()) {
          throw new \InvalidArgumentException("No se pudo eliminar el contacto");
        }
      }
      unset($cxcl);
      $this->setAccountants($data->idContactlist);
      $sql = "CALL updateCountersAccount({$idAccount})";
      $this->db->fetchAll($sql);
      unset($sql);
      $msg['message'] = "El contacto en esta lista se ha eliminado correctamente";
      
    } else if ($data->type == "deletedAll") {
      
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
      $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
      $customLogger->idContactlist = $data->idContactlist;
      $customLogger->idContact = $data->idContact;
      $customLogger->email = $data->email;
      $customLogger->indicative = $data->indicative;
      $customLogger->phone = $data->phone;
      $customLogger->typeName = "deletedAllMethod";
      $customLogger->detailedLogDescription = "El contacto en todas las lista se ha eliminado correctamente";
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
      unset($customLogger);
      
      $dataContact = $this->getAllContact($data,$idAccount);
      unset($data);
      $dataContactlist = array();
      $DataidContact = array();
      foreach ($dataContact as $value) {
        $DataidContact[] = $value->idContact;
        $value->deleted = time();
        $value->updated = time();
        if (!$value->save()) {
          throw new \InvalidArgumentException("No se pudo eliminar el contacto");
        }
      }
      unset($dataContact);
      $listId = implode(",", $DataidContact);

      foreach ($DataidContact as $value) {
        
        $contactsegment = \Sxc::find([["idContact" => $value]]);
        
        if( $contactsegment != false ){
            
            foreach( $contactsegment as $value2){
                $value2->deleted = time();
                $value2->save();
            }            
        }
        unset($contactsegment);          
      }

      unset($DataidContact);
      $cxclList = $this->getAllContactxCxcl($listId, $idAccount);
      unset($listId);
      if (count($cxclList) > 0) {
        foreach ($cxclList as $value) {
          if (empty($value->idContactlist)) {
            $dataContactlist[$value->idContactlist] = $value->idContactlist;
          }
          $value->active = 0;
          $value->deleted = time();
          if (!$value->save()) {
            throw new \InvalidArgumentException("No se pudo eliminar el contacto");
          }
        }
      } else {
        throw new \InvalidArgumentException("No se pudo eliminar el contacto");
      }
      unset($cxclList);
      foreach ($dataContactlist as $value) {
        $this->setAccountants($value);
      }
      unset($dataContactlist);
      $sql = "CALL updateCountersAccount({$idAccount})";
      $this->db->fetchAll($sql);
      unset($sql);
      $msg['message'] = "El contacto en todas las lista se ha eliminado correctamente";
    }
    return $msg;
  }

  public function extractDomainEmail($email) {
    $arroba = strpos($email, "@");
    $domainEmail = substr($email, $arroba + 1, 50);
    return $domainEmail;
  }

  public function createDomain($domainEmail) {
    $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $contactManger = new \Sigmamovil\General\Misc\ContactManager();
    $nextIdContact = $contactManger->autoIncrementCollection("id_domain");
    $domain = new \Domain();
    $domain->idDomain = $nextIdContact;
    $domain->idAccount = $idAccount;
    $domain->domain = $domainEmail;
    $domain->deleted = 0;
    $domain->status = 1;

    if (!$domain->save()) {
      throw new \InvalidArgumentException("No se ha podido crear el dominio");
    }
    return $domain;
  }

  public function findActiveContacts($idAccount) {
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0",
                "bind" => array(0 => $idAccount)
    ));

    if (!$account) {
      throw new \InvalidArgumentException("No se encontró la cuenta que ingreso, por favor valide la información.");
    }

    /* $subaccounts = $this->modelsManager->createBuilder()
      ->columns(["Subaccount.idSubaccount"])
      ->from('Subaccount')
      ->where("Subaccount.idAccount  = {$idAccount}")
      ->getQuery()
      ->execute();

      $subaccounts = $this->fixArraySubaccount($subaccounts); */

    /**
     * Instacia para ejecutar los servicios de MongoDB
     */
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $acuMail = 0;
    $acuPhone = 0;
    $acuPhoneEmail = 0;

    //$queryInContact = array('idSubaccount' => ['$in' => $subaccounts]);
    $countContact = \Contact::count([['idAccount' => $idAccount]]);
    $countEmail = \Email::count([['idAccount' => $idAccount]]);
    $countPhone = \Phone::count([['idAccount' => $idAccount]]);
    $countTotal = $countEmail + $countPhone;

    /* while ($this->limitEmail($manager, $this->countEmail, $idAccount)) {

      $emails = $this->fixArrayEmail($this->resultEmail);
      unset($this->resultEmail);

      $queryInEmail = array('email' => ['$in' => $emails]);
      $queryInPhone = array('email' => ['$in' => $emails], 'phone' => ['$ne' => ""]);

      $acuMail += \Contact::count([$queryInEmail]);
      $acuPhoneEmail += \Contact::count([$queryInPhone]);
      }

      while ($this->limitPhone($manager, $this->countPhone, $idAccount)) {

      $phones = $this->fixArrayPhone($this->resultPhone);
      unset($this->resultPhone);

      $queryInPhone = array('phone' => ['$in' => $phones], 'email' => "");

      $acuPhone += \Contact::count([$queryInPhone]);
      } */

    $result = ['totalContactos' => $countContact, 'activosEmail' => $countEmail, 'activosSms' => $countPhone, 'activosTotales' => $countTotal];
    //$result = ['totalContactos' => $countContact, 'activosEmail' => $acuMail, 'activosSms' => $acuPhone, 'activosTotales' => $acuPhoneEmail];
    return $result;
  }

  public function fixArrayEmail($array) {
    $arrayFix = array();
    foreach ($array as $value) {
      $arrayFix[] = $value->email;
      $this->countEmail++;
    }
    return $arrayFix;
  }

  public function fixArrayPhone($array) {
    $arrayFix = array();
    foreach ($array as $value) {
      $arrayFix[] = $value->phone;
      $this->countPhone++;
    }
    return $arrayFix;
  }

  public function fixArraySubaccount($array) {
    $arrayFix = array();
    foreach ($array as $value) {
      $arrayFix[] = (int) $value->idSubaccount;
    }
    return $arrayFix;
  }

  public function limitEmail($manager, $count, $idAccount) {

    $optionsEmail = array(
        'limit' => 1000,
        'skip' => 0 + $count,
        'projection' => array('_id' => 0, 'email' => 1),
    );

    $query = array('spam' => ['$exists' => false], 'bounced' => ['$exists' => false], 'idAccount' => $idAccount);

    $driverQuery = new \MongoDB\Driver\Query($query, $optionsEmail);
    $this->resultEmail = $manager->executeQuery("aio.email", $driverQuery)->toArray();

    unset($optionsEmail);
    unset($query);
    unset($driverQuery);

    if (count($this->resultEmail) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function limitPhone($manager, $count, $idAccount) {

    $optionsPhone = array(
        'limit' => 1000,
        'skip' => 0 + $count,
        'projection' => array('_id' => 0, 'phone' => 1),
    );

    $query = array('idAccount' => $idAccount);

    $driverQuery = new \MongoDB\Driver\Query($query, $optionsPhone);
    $this->resultPhone = $manager->executeQuery("aio.phone", $driverQuery)->toArray();

    unset($optionsPhone);
    unset($query);
    unset($driverQuery);

    if (count($this->resultPhone) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function searchContact($idContact) {

    $arrayResult = array();
    $contact = \Contact::findFirst([[
            "idContact" => $idContact
    ]]);

    $contact = json_encode($contact);
    $contact = json_decode($contact, true);
    unset($contact['_id']);

    $updated = date("Y-m-d H:i:s", $contact['updated']);
    $created = date("Y-m-d H:i:s", $contact['created']);

    $contact['updated'] = $updated;
    $contact['created'] = $created;

    $arrayResult['contact'] = $contact;

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    $queryCxc = ["idContact" => $idContact];
    $driverCxc = new \MongoDB\Driver\Query($queryCxc);
    $resultCxc = $manager->executeQuery("aio.cxc", $driverCxc)->toArray();

    $resultCxc = json_encode($resultCxc);
    $resultCxc = json_decode($resultCxc, true);

    foreach ($resultCxc[0]['idContactlist'] as $key => $idContactList) {

      $contactList = $this->modelsManager->createBuilder()
              ->columns(["Contactlist.idContactlist", "Contactlist.name"])
              ->from('Contactlist')
              ->where("Contactlist.deleted = 0 AND Contactlist.idContactlist  = {$key} ")
              ->getQuery()
              ->execute();
      if (count($contactList) > 0) {
        $contactList = json_encode($contactList[0]);
        $contactList = json_decode($contactList, true);
        $arrayCustom = array();
        foreach ($idContactList as $keyCus => $custom) {
          $custom['idCustomfield'] = $keyCus;
          $arrayCustom[] = $custom;
        }
        $contactList['custumfields'] = $arrayCustom;
        $arrayResult['contactlist'][] = $contactList;
      }
    }

    $optionsMxc = array(
        'projection' => array('_id' => 0, 'idMail' => 1, 'mailName' => 1, 'scheduleDate' => 1, 'email' => 1, 'open' => 1,
            'totalClicks' => 1, 'uniqueClicks' => 1, 'bounced' => 1, 'bouncedCode' => 1, 'spam' => 1, 'unsubscribed' => 1,
            'share_fb' => 1, 'share_tw' => 1, 'share_li' => 1, 'share_gp' => 1, 'open_fb' => 1, 'open_tw' => 1,
            'open_li' => 1, 'open_gp' => 1),
    );

    $queryMxc = ["idContact" => $idContact];
    $driverMxc = new \MongoDB\Driver\Query($queryMxc, $optionsMxc);
    $resultMxc = $manager->executeQuery("aio.mxc", $driverMxc)->toArray();

    $resultMxc = json_encode($resultMxc);
    $resultMxc = json_decode($resultMxc, true);

    $arrayResult['mails'] = $resultMxc;

    return $arrayResult;
  }

  public function getUnsubscribed($idContact, $idCOntactlist) {
    $unsubscribed = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1", "bind" => [0 => $idContact, 1 => $idCOntactlist]]);
//    var_dump($idContact);
//    var_dump($idCOntactlist);
//    var_dump($unsubscribed->unsubscribed);
//    exit;
    return $unsubscribed->unsubscribed;
  }

  public function setAccountants($idContactlist) {
    $sql = "CALL updateCounters({$idContactlist})";
    $this->db->execute($sql);
  }

//  public function addAmountContact($idContactlist) {
//    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0 and deleted = 0", "bind" => array(0 => $idContactlist)));
//    if ($contactlist) {
//      foreach ($contactlist->Subaccount->Saxs as $value) {
//        if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
//          $value->amount++;
//          $value->save();
//        }
//      }
//    }
//  }
//  public function reduceAmountContact($idContactlist) {
//    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0 and deleted = 0", "bind" => array(0 => $idContactlist)));
//    if ($contactlist) {
//      foreach ($contactlist->Subaccount->Saxs as $value) {
//        if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
//          $value->amount--;
//          $value->save();
//        }
//      }
//    }
//  }

  public function isEmailBlocked($email) {
    $flag = false;
    $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $result = \Blocked::find([["email" => $email, "phone" => ['$nin' => ["", null, "null",0,"0"]], "indicative" => ['$nin' => ["", null, "null",0,"0"]], "deleted" => 0, "idAccount" => (int) $idAccount]]);
    $result2 = \Blocked::find([["email" => $email, "phone" => ['$in' => ["", null, "null",0,"0"]], "indicative" => ['$in' => ["", null, "null",0,"0"]], "deleted" => 0, "idAccount" => (int) $idAccount]]);
    if ($result) {
      $flag = true;
    }else if($result2){
        $flag = true;
    }
    return $flag;
  }

  public function isPhoneBlocked($phone, $indicative) {
    $flag = false;
    $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $result = \Blocked::find([["email" => "", "phone" => $phone, "indicative" => $indicative, "deleted" => 0, "idAccount" => (int) $idAccount]]);
    if ($result) {
      $flag = true;
    }
    return $flag;
  }

  public function isEmailPhoneBlocked($email, $phone, $indicative) {
    $flag = false;
    $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $result = \Blocked::find([["email" => $email, "phone" => $phone, "indicative" => $indicative, "deleted" => 0, "idAccount" => (int) $idAccount]]);
    if ($result) {
      $flag = true;
    }
    return $flag;
  }
  
  public function addBlockedContactExist($idContact){
   $contact = \Contact::findFirst([["idContact" => (int) $idContact,"deleted" => 0]]);
   $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
   $contactBlocked = \Blocked::findFirst([["email" => $contact->email, "phone" => $contact->phone, "indicative" => $contact->indicative, "deleted" => 0, "idAccount" => (int) $idAccount]]);
   
   if($contactBlocked){
    $flag = false;
    $tmp = $contactBlocked->idContacts;
    foreach ($tmp as $key => $value) {

        if($value == (int)$contact->idContact){
           $flag = true;
           break;
        }
    }
    if($flag == false){
        array_push($contactBlocked->idContacts,(int)$contact->idContact);    
    }
    
    if (!$contactBlocked->save()) {
        foreach ($contactBlocked->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
        }
    }
   }
  }  

  public function saveContactForm($form) {
    $idContactlist = $form->idContactlist;
    $idForm = $form->idForm;
    $contactlist = \Contactlist::findFirst(array(
      "conditions" => "idContactlist = ?0",
      "bind" => array(0 => $form->idContactlist)
    ));
    if (!$contactlist) {
      throw new \InvalidArgumentException("No se encontrÃƒÂ³ la lista de contactos.");
    }
    $idSubaccount = $contactlist->idSubaccount;
    $this->account = $contactlist->Subaccount->account;
    $this->idAccount = $contactlist->Subaccount->idAccount;
    $this->data->validateConfirm = false;
    $saxs = null;

    foreach ($contactlist->Subaccount->Saxs as $value) {
      if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
        foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $item) {
          if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
            $saxs = $item;
          }
        }
      }
    }
    foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $value) {
      if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
        $this->detailConfig = $value;
      }
    }
    if (isset($saxs) && $saxs->amount == 0) {
      throw new InvalidArgumentException("No cuenta con la capacidad suficiente para importar mÃƒÂ¡s contactos, por favor contacte a su administrador.");
    }
    $cxclAll = \Cxcl::find([
      "conditions" => "idContactlist = ?0 AND deleted = 0",
      "bind" => [$idContactlist]
    ]);
    //
    $arrayData = [];
    if($cxclAll != false){
      foreach ($cxclAll as $value) {
        $arrayData[] = (int) $value->idContact;
      }
    }
    $flagEmail = true;
    $flagPhone = true;
    if (!isset($this->data->email) || filter_var($this->data->email, FILTER_VALIDATE_EMAIL) == false) {
      $flagEmail = false;
      $cemail = "";
    } else {
      $cemail = $this->data->email;
    }
    if (!isset($this->data->phone) || !isset($this->data->indicative) || !is_numeric($this->data->phone) || !is_numeric($this->data->indicative)) {
      $flagPhone = false;
      $cphone = "";
      $cindicative = "";
    } else {
      $cphone = $this->data->phone;
      $cindicative = $this->data->indicative;
    }
    if (!$flagEmail && !$flagPhone) {
      throw new \InvalidArgumentException("El contacto debe contener al menos el correo electrÃƒÂ³nico o el nÃƒÂºmero del mÃƒÂ³vil con su respectivo indicativo");
    }
    if(!empty($cemail) && !empty($cphone) && !empty($cindicative)){
      $where = ["idAccount" => (string) $this->idAccount, 'idContact' => ['$in' => $arrayData], "email" => $cemail, "indicative" => (string)$cindicative, "phone" => $cphone, "deleted" => 0];
    } else if(!empty($cemail) && empty($cphone) && empty($cindicative)){
      $where = ["idAccount" => (string) $this->idAccount, 'idContact' => ['$in' => $arrayData], "email" => $cemail, "indicative" => "", "phone" => "", "deleted" => 0];
    } else if(empty($cemail) && !empty($cphone) && !empty($cindicative)){
      $where = ["idAccount" => (string) $this->idAccount, 'idContact' => ['$in' => $arrayData], "email" => "", "indicative" =>(string) $cindicative, "phone" => $cphone, "deleted" => 0];
    }
    $oldcontacts = \Contact::find([$where]);
    $flag = true;
    if($oldcontacts != false){
      $flag = false;
      foreach ($oldcontacts as $key => $contact) {
        $where = ["idContactlist" => (int) $idContactlist, "idContact" => (int) $contact->idContact, "deleted" => 0, "spam" => 0, "bounced" => 0, "blocked" => 0];
        $cxcl = \Cxcl::findFirst([$where]);
        if($cxcl != false){
          $contact->name = $this->data->name;
          $contact->lastname = $this->data->lastname;
          $contact->birthdate = $this->data->birthdate;
          $contact->updated = time();
          $contact->idSubaccount = $idSubaccount;
          if (!$contact->save()) {
            foreach ($contact->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
          $obj = array();
          $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
          if($customfield != false){
            foreach ($customfield as $keycustomfield) {
              $name = $keycustomfield->alternativename;
              if (isset($this->data->$name)) {
                $value = $this->data->$name;
                if ($keycustomfield->type != "Multiselect") {
                  $value = trim($value);
                }
                $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => $value, "type" => $keycustomfield->type];
              } else {
                $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => "", "type" => $keycustomfield->type];
              }
            }
            $cxc = \Cxc::findFirst([["idContact" =>(int) $contact->idContact]]);
            
            if(!empty($cxc)){             
                $tmp = $cxc->idContactlist;
                foreach($obj as $value => $key){
                    $tmp[$idContactlist][$value] = $key;
                }
                $cxc->idContactlist = null;
                $cxc->idContactlist= (object) $tmp;
                $cxc->save();
                unset($cxc);
            }else{
                $cxc = new \Cxc();
                $cxc->idContact = $contact->idContact;
                $cxc->idContactlist = [$idContactlist => $obj];
                $cxc->save();    
            }
            
            //
            $cxcl->idForm = $idForm;
            $cxcl->save();
          }
        } else {
          $flag = true;
        }
      }
    }
    
    if($flag){
      $contact = new \Contact();
      $contactManger = new \Sigmamovil\General\Misc\ContactManager();
      $nextIdContact = $contactManger->autoIncrementCollection("id_contact");
      $contact->idContact = $nextIdContact;
      $contact->blockedPhone = "";
      $contact->blockedEmail = "";
      $contact->phone = "";
      $contact->indicative = "";
      $contact->email = "";
      $contact->name = "";
      $contact->lastname = "";
      $contact->birthdate = "";
      $contact->ipAddress = $this->getRealIP();
      $contact->browser = $this->getBrowser();
      $contact->idSubaccount = (int) $idSubaccount;
      $contact->idAccount = (string) $this->idAccount;
      $contact->deleted = 0;
      $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
      foreach ($this->data as $key => $value) {
        if ($value != "" && $value != null && !empty($value)) {
          if ($key == "indicative" || $key == "phone") {
            if ($flagPhone) {
              $contact->$key = $value;
            }
          } else if ($key == "email") {
            if ($flagEmail) {
              $contact->$key = $value;
            }
          } else {
            if ($key == "name" || $key == "lastname" || $key == "birthdate") {
              $contact->$key = $value;
            }
          }
        }
      }
      $status = "active";
      if ($form->optin != 0) {
        $status = "unsubscribed";
      }
      if (count($oldcontacts) == 0) {
        if(isset($this->data->email) && isset($this->data->phone) && $this->isEmailPhoneBlocked($this->data->email)){
          $contact->blockedEmail = time();
          $contact->blockedPhone = time();
          $status = "blocked";
        } else if (isset($this->data->email) && $this->isEmailBlocked($this->data->email)) {
          $contact->blockedEmail = time();
          $status = "blocked";
        } else if (isset($this->data->phone) && $this->isPhoneBlocked($this->data->phone, $this->data->indicative)) {
          $contact->blockedPhone = time();
          $status = "blocked";
        }
      }
      
      if (!isset($this->data->valid)) {
        $this->data->valid = false;
      }
      if (!$this->data->valid) {
        $emailValidate = \Email::findFirst([[
          "email" => $contact->email, 
          "deleted" => 0,
          "idAccount" => $this->idAccount
        ]]);

        $subAccount = \Subaccount::find(["conditions" => "idAccount = ?0", "bind" => [0 => $this->idAccount]]);
        $ids = array();

        for ($index = 0; $index < count($subAccount); $index++) {
          $ids[$index] = $subAccount[$index]->idSubaccount;
        }

        $where = ["idSubaccount" => ['$in' => $ids], ["email" => $contact->email, "deleted" => 0]];
        $contactValidate = \Contact::findFirst($where);

        if ($contact->email == "") {
          $contactValidate = false;
          $emailValidate = false;
        }

        if ($emailValidate && $contactValidate && !$this->data->validateConfirm) {
          $this->data->validateConfirm = true;
        }

        $phoneValidate = \Phone::findFirst([["phone" => $contact->phone,
                "idAccount" => $this->idAccount]]);

        $where = ["idSubaccount" => ['$in' => $ids], ["phone" => $contact->phone, "deleted" => 0]];
        $contactValidatePhone = \Contact::findFirst($where);

        if ($contact->phone == "") {
          $contactValidatePhone = false;
          $phoneValidate = false;
        }
        if ($contactValidatePhone) {
          $idContact = $contactValidatePhone->idContact;
        } else if ($contactValidate) {
          $idContact = $contactValidate->idContact;
        }
        if (($contactValidate || $contactValidatePhone) && !$this->data->validateConfirm) {
          $this->data->validateConfirm = true;
        }
      }

      $obj = new \Cxcl;
      $obj->idContact = $contact->idContact;
      $obj->idContactlist = $idContactlist;
      $obj->idForm = $idForm;
      $obj->$status = time();
      $obj->status = $status;
      if (!$obj->save()) {
        foreach ($key->getMessages() as $message) {
          throw new Exception($message);
        }
      }
      if ($contact->save()) {
        $obj = array();
        foreach ($customfield as $keycustomfield) {
          $name = $keycustomfield->alternativename;
          if (isset($this->data->$name)) {
            $value = $this->data->$name;
            if ($keycustomfield->type != "Multiselect") {
              $value = trim($value);
            }
            $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => $value, "type" => $keycustomfield->type];
          } else {
            $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => "", "type" => $keycustomfield->type];
          }
        }
        $cxc = new \Cxc();
        $cxc->idContact = $contact->idContact;
        //INICIO CODIGO PARA PONER CP HABEAS DATA EN SI
        if($idForm == "303" || $idForm == 303){
          foreach ($obj as $key => $value) {
            if($key == "43930" || $key == 43930){
              $obj[$key]["value"] = "SI";
            }
          }
        }
        //FIN CODIGO PARA PONER CP HABEAS DATA EN SI
        $cxc->idContactlist = [$idContactlist => $obj];
        $cxc->save();
        $validateEmail = $this->validateEmail($contact->email);
        if (!$validateEmail) {
          if (isset($contact->email)) {
            $email = \Email::findFirst([["email" => $contact->email, "idAccount" => $this->idAccount]]);
            if (!$email) {
              $email = new \Email();
            }
            $email->email = $contact->email;
            $domainEmail = $this->extractDomainEmail($contact->email);
            $domain = \Domain::findFirst([["domain" => $domainEmail]]);
            if (!$domain) {
              $domain = $this->createDomain($domainEmail);
            }
            $email->idDomain = $domain->idDomain;
            $email->idAccount = $this->idAccount;
            $email->deleted = 0;
            if (!$email->save()) {
              foreach ($email->getMessages() as $message) {

              }
              $this->trace("fail", "No se logro crear una cuenta");
            }
          }
        } else {
          $email = \Email::findFirst([["email" => $contact->email, "idAccount" => $this->idAccount]]);
          $email->deleted = 0;
          if (!$email->save()) {
            foreach ($email->getMessages() as $message) {

            }
            $this->trace("fail", "No se logro crear una cuenta");
          }
        }
        $validatePhone = $this->validatePhone($contact->phone);
        if (!$validatePhone) {
          if (isset($contact->phone)) {
            $phone = \Phone::findFirst([["phone" => $contact->email, "indicative" => $contact->indicative, "idAccount" => $this->idAccount]]);
            if (!$phone) {
              $phone = new \Phone();
            }
            $phone->indicative = $contact->indicative;
            $phone->phone = $contact->phone;
            $phone->idAccount = $this->idAccount;
            $phone->deleted = 0;
            if (!$phone->save()) {
              foreach ($phone->getMessages() as $message) {
  //                throw new \InvalidArgumentException($message);
              }
              $this->trace("fail", "No se logro crear una cuenta");
            }
          }
        }
        if ($form->optin == 1) {
          $this->formOptinOption($form, $contact);
        } else {
          if ($form->welcomeMail == 1) {
            $this->formWelcomeMailOption($form, $contact);
          }
          if ($form->notificationMail == 1) {
            $this->formNotificationMailOption($form, $contact);
          } 
        }
      }
    }
    
    $this->setAccountants($idContactlist);

    $sql = "CALL updateCountersAccount({$contactlist->Subaccount->idAccount})";
    $this->db->fetchAll($sql);

    $segmentmanager = new \Sigmamovil\General\Misc\SegmentManager();
    $segmentmanager->addOneContact($contact->idContact, $idContactlist);
  }

  public function findContactForm($page, $idContactlist, $idForm, $stringSearch) {
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $this->cxcl = $this->modelsManager->createBuilder()
            ->from('Cxcl')
            ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
//            ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ")
            ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist = {$idContactlist} AND Cxcl.idForm = {$idForm} LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
            ->getQuery()
            ->execute();
    $in = array();
    for ($i = 0; $i < count($this->cxcl); $i++) {
      $in[$i] = (int) $this->cxcl[$i]->idContact;
    };
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $fields = array("name", "email", "phone");
    foreach ($customfield as $key) {
      array_push($fields, $key->idCustomfield);
    }
    $where = array("idContact" => ['$in' => $in]);
    unset($in);
    if ($stringSearch != -1) {
      if ($stringSearch != "") {
        $stringSearch = explode(",", $stringSearch);
        foreach ($fields as $value) {
          foreach ($stringSearch as $key) {
            if ($key or $key != "" or ! empty($key)) {
              $key = trim($key);
              $arr[] = [$value => ['$regex' => ".*$key.*"]];
              $where['$or'] = $arr;
            }
          }
        }
        $this->totals = \Contact::count([$where]);
      } else {
        $this->totals = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idForm = {$idForm}"]);
      }
    } else {
      $this->totals = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idForm = {$idForm}"]);
    }
    unset($arr);
    unset($fields);
    unset($customfield);
    $this->data = \Contact::find(array($where));
//    $this->contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $idContactlist)));
    unset($where);
    unset($page);
    $this->modelData($idContactlist);
  }

  private function formOptinOption($form, $contact) {
    $optinForm = \FormOptin::findFirst(["conditions" => "idForm = ?0", "bind" => array($form->idForm)]);
    if (!$optinForm) {
      throw new \InvalidArgumentException("No se encuentra registrada la option optin del formulario {$form->idForm}");
    }
    $templateMail = \MailTemplateContent::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => array($optinForm->idMailTemplate)]);
    if (!$templateMail) {
      throw new \InvalidArgumentException("No se encuentra registrada la plantilla {$optinForm->idMailTemplate}");
    }
    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount(NULL);
    $editorObj->assignContent(json_decode($templateMail->content));
    $content = utf8_decode($editorObj->render());

    $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;

    $urlManager = \Phalcon\DI::getDefault()->get('urlManager');

    $imageService = new \ImageService($this->account, $domain, $urlManager);
    $prepareMail = new \PrepareMailContent(false, $imageService, false, $form);
    list($contents, $links) = $prepareMail->processContent($content);

    $trackingObj = new \Sigmamovil\General\Misc\TrackingUrlObject();

    $customfield = new \CustomfieldManagerSms(null, $contact->idContact, false);
    $field = $customfield->searchCustomfieldForContact($contents);
    $contentHtml = $contents;
    if($field){
      $customfielContact = $customfield->findCustomField($contact->idContact);
      $contentHtml = $customfield->processCustomFields($contact, $field, $contents, $customfielContact);
    }
    
    $contentHtml = $trackingObj->getTrackingUrlForm($contentHtml, $form, $contact->idContact);
    
    //lo siguiente reemplaza algunas urls que empezaban con 
    //"Https" las cuales eran erroneas por las correctas con minuscula.
    $contentHtml = ereg_replace("Https", "https", $contentHtml);  
    //esto me permite visualizar las imagenes en los gestores de correo.

    $data->fromName = $optinForm->nameSender;
    $data->fromEmail = $optinForm->emailSender;
    $data->subject = $optinForm->subject;
    $data->html = $contentHtml;
    $data->to = $contact->email;
    $data->replyTo = $optinForm->replyTo;
    $data->from = array($data->fromEmail => $data->fromName);
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  private function formWelcomeMailOption($form, $contact) {
    $welcomeForm = \FormWelcomeMail::findFirst(["conditions" => "idForm = ?0", "bind" => array($form->idForm)]);

    if (!$welcomeForm) {
      throw new \InvalidArgumentException("No se encuentra registrada la option optin del formulario {$form->idForm}");
    }
    
    $templateMail = \MailTemplateContent::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => array($welcomeForm->idMailTemplate)]);
    if (!$templateMail) {
      throw new \InvalidArgumentException("No se encuentra registrada la plantilla {$welcomeForm->idMailTemplate}");
    }
    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount(NULL);
    $editorObj->assignContent(json_decode($templateMail->content));
    $content = utf8_decode($editorObj->render());

    $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;

    $urlManager = \Phalcon\DI::getDefault()->get('urlManager');

    $imageService = new \ImageService($this->account, $domain, $urlManager);
    $prepareMail = new \PrepareMailContent(false, $imageService, false, $form);
    list($contents, $links) = $prepareMail->processContent($content);

    $trackingObj = new \Sigmamovil\General\Misc\TrackingUrlObject();

    $customfield = new \CustomfieldManagerSms(null, $contact->idContact, false);
    $field = $customfield->searchCustomfieldForContact($contents);
    $contentHtml = $contents;
    if($field){
      $customfielContact = $customfield->findCustomField($contact->idContact);
      $contentHtml = $customfield->processCustomFields($contact, $field, $contents, $customfielContact);
    }
    
    $contentHtml = $trackingObj->getTrackingUrlForm($contentHtml, $form, $contact->idContact);
    
    //lo siguiente reemplaza algunas urls que empezaban con 
    //"Https" las cuales eran erroneas por las correctas con minuscula.
    $contentHtml = ereg_replace("Https", "https", $contentHtml);  
    //esto me permite visualizar las imagenes en los gestores de correo.

    $data->fromName = $welcomeForm->nameSender;
    $data->fromEmail = $welcomeForm->emailSender;
    $data->subject = $welcomeForm->subject;
    $data->html = $contentHtml;
    $data->to = $contact->email;
    $data->replyTo = $welcomeForm->replyTo;
    $data->from = array($data->fromEmail => $data->fromName);
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  private function formNotificationMailOption($form, $contact) {

    $notificationMail = \FormNotificationMail::findFirst(["conditions" => "idForm = ?0", "bind" => array($form->idForm)]);

    if (!$notificationMail) {
      throw new \InvalidArgumentException("No se encuentra registrada la option optin del formulario {$form->idForm}");
    }

    $templateMail = \MailTemplateContent::findFirst(["conditions" => "idMailTemplate = ?0", "bind" => array($notificationMail->idMailTemplate)]);
    if (!$templateMail) {
      throw new \InvalidArgumentException("No se encuentra registrada la plantilla {$notificationMail->idMailTemplate}");
    }

    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount(NULL);
    $editorObj->assignContent(json_decode($templateMail->content));
    $content = $editorObj->render();

    $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;

    $urlManager = \Phalcon\DI::getDefault()->get('urlManager');


    $imageService = new \ImageService($this->account, $domain, $urlManager);
    $prepareMail = new \PrepareMailContent(false, $imageService, false);
    list($contents, $links) = $prepareMail->processContent($content);
    $customfield = new \CustomfieldManagerSms(null, $contact->idContact, false);
    $field = $customfield->searchCustomfieldForContact($contents);
    $customfielContact = $customfield->findCustomField($contact->idContact);
    $contentHtml = $customfield->processCustomFields($contact, $field, $contents, $customfielContact);

    $data = new \stdClass();
    $data->fromName = $notificationMail->nameSender;
    $data->fromEmail = $notificationMail->emailSender;
    $data->subject = $notificationMail->subject;
    $data->html = $contentHtml;
    $data->replyTo = $notificationMail->replyTo;
    $email = explode(",", trim($notificationMail->emails));
    $to = [];
    foreach ($email as $key) {
      array_push($to, trim($key));
    }
    $data->to = $to;
    $data->from = array($data->fromEmail => $data->fromName);
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {//Verificar la ip compartida de internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {//verificar si la ip fue provista por un proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

  function getBrowser() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($user_agent, 'MSIE') !== FALSE)
      return 'Internet explorer';
    elseif (strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
      return 'Microsoft Edge';
    elseif (strpos($user_agent, 'Trident') !== FALSE) //IE 11
      return 'Internet explorer';
    elseif (strpos($user_agent, 'Opera Mini') !== FALSE)
      return "Opera Mini";
    elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
      return "Opera";
    elseif (strpos($user_agent, 'Firefox') !== FALSE)
      return 'Mozilla Firefox';
    elseif (strpos($user_agent, 'Chrome') !== FALSE)
      return 'Google Chrome';
    elseif (strpos($user_agent, 'Safari') !== FALSE)
      return "Safari";
    else
      return 'No hemos podido detectar su navegador';
  }

  function deleteSelected($idsContact, $idContactlist) {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $ids = [];
    //foreach ($idsContact as $id) {
    $deletedcontact = \Contact::find([['idContact' => ['$in' => $idsContact], "idAccount" => $idAccount, "deleted" => 0]]);
    foreach ($deletedcontact as $value) {
      /*if (!isset($value->idContact)) {
        throw new \InvalidArgumentException("Uno de los contactos no se ha encontrado");
      }*/
      $value->deleted = time();
      $value->save();
      $ids[] = (int) $value->idContact;
    }
    //$ids = substr($ids, 0, -1);
    $listId = implode(",", $ids);

//    $sql = "UPDATE cxcl SET deleted = " . time() . " WHERE idContact IN ({$ids}) AND idContactlist = {$idContactlist}";
    $sql = "UPDATE cxcl SET deleted = " . time() . ", active=0 WHERE idContact IN ({$listId})";
    $this->db->execute($sql);
    //$sql = "CALL updateCountersGlobal()";
    $sql = "CALL updateCounters({$idContactlist})";
    $this->db->execute($sql);
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);;
    return true;
  }

  function findContactlist($idContactlist) {
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->UserType->idSubaccount;
    $this->datacontactlists = \Contactlist::find(["conditions" => "idSubaccount = ?0 AND deleted=0 AND idContactlist!=?1", "bind" => [0 => $idSubaccount, 1 => $idContactlist]]);
    $this->modelContactlist();
  }

  public function modelContactlist() {
    $this->contactlists = array();
    foreach ($this->datacontactlists as $data) {
      $contactlist = new \stdClass();
      $contactlist->idContactlist = $data->idContactlist;
      $contactlist->idSubaccount = $data->idSubaccount;
      $contactlist->created = date("d/m/Y  H:ia", $data->created);
      $contactlist->updated = date("d/m/Y  H:ia", $data->updated);
      $contactlist->deleted = $data->deleted;
      $contactlist->createdBy = $data->createdBy;
      $contactlist->updatedBy = $data->updatedBy;
      $contactlist->name = $data->name;
      $contactlist->description = $data->description;

      $this->contactlists[] = $contactlist;
    }
  }

  function getContactlists() {
    return $this->contactlists;
  }

  function moveContacts($idNewContactlist, $idsContacts, $idContaclistfrom) {
    foreach ($idsContacts as $idContact) {
      $newcxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idNewContactlist)]);
      if (!$newcxcl) {
        $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idContaclistfrom)]);
        $cxcl->idContactlist = $idNewContactlist;
        if (!$cxcl->save()) {
          foreach ($cxcl->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    $sql = "CALL updateCountersGlobal()";
    $this->db->execute($sql);
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);
  }

  function copyContacts($idNewContactlist, $idsContacts, $idContaclistfrom) {
    foreach ($idsContacts as $idContact) {
      $newcxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idNewContactlist)]);
      if (!$newcxcl) {
        $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idContaclistfrom)]);
        $newCxcl = new \Cxcl();
        $newCxcl->idContactlist = $idNewContactlist;
        $newCxcl->idContact = $cxcl->idContact;
        $newCxcl->created = $cxcl->created;
        $newCxcl->updated = $cxcl->updated;
        $newCxcl->createdBy = $cxcl->createdBy;
        $newCxcl->updatedBy = $cxcl->updatedBy;
        $newCxcl->unsubscribed = $cxcl->unsubscribed;
        $newCxcl->deleted = $cxcl->deleted;
        $newCxcl->status = $cxcl->status;
        $newCxcl->spam = $cxcl->spam;
        $newCxcl->bounced = $cxcl->bounced;
        $newCxcl->blocked = $cxcl->blocked;
        $newCxcl->active = $cxcl->active;
        if (!$newCxcl->save()) {
          foreach ($newCxcl->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    $sql = "CALL updateCountersGlobal()";
    $this->db->execute($sql);
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);
  }

  function changeSuscribeSelected($valueSuscribe, $idsContacts, $idContaclistfrom) {
    foreach ($idsContacts as $idContact) {
      $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idContaclistfrom)]);
      if (!$cxcl) {
        throw new \InvalidArgumentException("Uno o varios contactos no existen en la lista");
      }
      if ($valueSuscribe) {
        $cxcl->active = time();
        $cxcl->unsubscribed = 0;
        if (!($cxcl->status == 'blocked' or $cxcl->status == 'spam' or $cxcl->status == 'bounced')) {
          $cxcl->status = 'active';
        }
      } else {
        $cxcl->unsubscribed = time();
        $cxcl->active = 0;
        if (!($cxcl->status == 'blocked' or $cxcl->status == 'spam' or $cxcl->status == 'bounced')) {
          $cxcl->status = 'unsubscribed';
        }
      }
      if (!$cxcl->save()) {
        foreach ($cxcl->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }

    $sql = "CALL updateCountersGlobal()";
    $this->db->execute($sql);
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);
  }

  function validateCopyContacts($idNewContactlist, $idsContacts, $idContaclistfrom) {

    $this->idsRepited = array();
    $this->response = "noError";
    $contactlist = \Contactlist::findFirst(["conditions" => "idContactlist = ?0 and deleted = 0", "bind" => [0 => $idNewContactlist]]);
    if (!$contactlist) {
      throw new \InvalidArgumentException("La lista de contactos de destino no existe");
    }

    foreach ($idsContacts as $idContact) {

      $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idContaclistfrom)]);
      if (!$cxcl) {
        throw new \InvalidArgumentException("El contacto no existe en esta lista");
      }

      $newcxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array(0 => $idContact, 1 => $idNewContactlist)]);

      if ($newcxcl) {
        $this->response = "isError";
        array_push($this->idsRepited, $cxcl->idContact);
      }
    }
    $this->modelContacts();
  }

  function modelContacts() {
    $this->repitedContacts = array();
    $cont = array();
    foreach ($this->idsRepited as $idContact) {

      $contact = \Contact::find([["idContact" => (int) $idContact]]);
      $cont['name'] = $contact[0]->name;
      $cont['lastname'] = $contact[0]->lastname;
      $cont['phone'] = $contact[0]->phone;
      $cont['email'] = $contact[0]->email;
      array_push($this->repitedContacts, $cont);
    }
  }

  function getResponse() {
    return $this->response;
  }

  function getRepitedContacts() {
    return $this->repitedContacts;
  }

  public function subcribedContact($idContactlist, $idContact, $form) {
    $cxcl = \Cxcl::findFirst(array("conditions" => "idContact = ?0 and idContactlist=?1 AND deleted = 0", "bind" => array($idContact, $idContactlist)));
    if ($cxcl) {
      $cxcl->status = "active";
      $cxcl->unsubscribed = 0;
      $cxcl->active = time();
      if ($cxcl->save()) {
        foreach ($cxcl->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      $contact = \Contact::findFirst([["idContact" => (int) $idContact]]);
      //
      $this->account = $cxcl->Contactlist->Subaccount->Account;
      //
      foreach ($cxcl->Contactlist->Subaccount->Account->AccountConfig->DetailConfig as $value) {
        if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
          $this->detailConfig = $value;
        }
      }
      //
      if($form->welcomeMail == 1){
        $this->formWelcomeMailOption($form, $contact);
      }
      //
      if($form->notificationMail == 1){
        $this->formNotificationMailOption($form, $contact);
      }
    }
  }

  public function findOneContact($idContact) {
    $contact = \Contact::find([["idContact" => (int) $idContact]]);
    $cont['name'] = $contact[0]->name;
    $cont['lastname'] = $contact[0]->lastname;
    $cont['phone'] = $contact[0]->phone;
    $cont['email'] = $contact[0]->email;

    $this->contact = $cont;
  }

  public function findSmsxc($idContact, $page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1 ) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }
    $where = ["idContact" => (integer) $idContact, "status" => "sent"];
    if ($name != 1) {
      $where['$and'] = [["smsName" => ['$regex' => ".*$name.*"], "idContact" => (integer) $idContact, "status" => "sent"]];
    }

//    $this->dataSmsxc = \Smsxc::find([$where, "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, "skip" => $page]);
    $this->dataSmsxc = \Smsxc::find([$where]);
    $this->totals = $this->dataSmsxc;
    $this->modelDataSmsxc();
  }

  public function findMxc($idContact, $page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1 ) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }
    $where = ["idContact" => (integer) $idContact, "status" => "sent"];
    if ($name != 1) {
      $where['$and'] = [["mailName" => ['$regex' => ".*$name.*"], "idContact" => (integer) $idContact, "status" => "sent"]];
    }

//    $this->dataSmsxc = \Smsxc::find([$where, "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, "skip" => $page]);
    $this->dataMxc = \Mxc::find([$where]);
    $this->totals = $this->dataMxc;
    $this->modelDataMxc();
  }

  public function modelDataSmsxc() {
    $this->smsxc = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->dataSmsxc as $data) {
      $smsxc = new \stdClass();
      $smsxc->idSmsxc = $data->idSmsxc;
      $smsxc->idSms = $data->idSms;
      $smsxc->response = $data->response;
      $smsxc->idContact = $data->idContact;
      $smsxc->smsName = $data->smsName;
      $smsxc->scheduleDate = $data->scheduleDate;
      $smsxc->status = $data->status;
      $smsxc->message = $data->message;
      $smsxc->indicative = $data->indicative;
      $smsxc->phone = $data->phone;

      array_push($arr, $smsxc);
    }
    array_push($this->smsxc, array("items" => $arr));
  }

  public function getSmsxc() {
    return $this->smsxc;
  }

  public function modelDataMxc() {
    $this->mxc = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->dataMxc as $data) {
      $mxc = new \stdClass();
      $mxc->idMail = $data->idMail;
      $mxc->idContact = $data->idContact;
      $mxc->mailName = $data->mailName;
      $mxc->scheduleDate = $data->scheduleDate;
      $mxc->status = $data->status;
      $mxc->email = $data->email;
      $mxc->open = $data->open;
      $mxc->bounced = $data->bounced;
      $mxc->spam = $data->spam;
      $mxc->unsubscribed = $data->unsubscribed;
      $mxc->response = $this->getResponseMxc($data->open, $data->bounced, $data->spam, $data->unsubscribed);

      array_push($arr, $mxc);
    }
    array_push($this->mxc, array("items" => $arr));
  }

  public function getResponseMxc($open, $bounced, $spam, $unsubscribed) {
    if ($open == 0 and $bounced == 0 and $spam == 0 and $unsubscribed == 0) {
      $response = "No lo ha abierto";
    }
    if ($open > 0) {
      $response = "Lo abriÃƒÂ³";
    }
    if ($unsubscribed > 0) {
      $response = "Se desuscribiÃƒÂ³";
    }
    if ($spam > 0) {
      $response = "Lo marcÃƒÂ³ como SPAM";
    }
    return $response;
  }

  public function getMxc() {
    return $this->mxc;
  }

  /**
   * 
   * @param int $idContactlist
   * @return obj
   */
  public function findExport($idContactlist) {
    try {
      $this->idContactlist = $idContactlist;
      //Consulta para traer la informacion de la lista de contactos
      $contactlist = \Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      //Instaciamos la Clase PHPExcel
      $objPHPExcel = new \PHPExcel();
      $objPHPExcel->getActiveSheet()->setTitle("Lista de Contactos");
      //Creamos un array desde donde va a comenzar a parecer la informacion 
      $array = array("B17", "C17", "D17", "E17", "F17", "G17", "H17", "I17", "J17", "K17", "L17", "M17", "N17", "O17", "P17", "Q17", "R17", "S17", "T17", "U17", "V17", "W17", "X17", "Y17", "Z17","AA17","AB17","AC17","AD17","AE17","AF17","AG17","AH17","AI17");
      //
      $this->phpExcelWorksheet($objPHPExcel);
      //Creamos este for para que recorra la cantidad de celdas de acuerdo a los campos personalizados
      for ($i = 65; $i < 72; $i++) {
        //Deacuerdo a las cantidad de campos personalizados calcula el ancho de la columna 
        $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
      }
      //Llamamos a la function findCells y le pasamos los parametros de $idContactlist
      $contacsTotal = $this->findIdContact($idContactlist);
	  $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
	   if($idAccount == 49 || $idAccount == 325 ){
  		foreach($contacsTotal as $value){
  			$w["idContact"] = $value->idContact;
  			$p = \Mxc::findfirst([$w,'sort' => ["scheduleDate" => -1]]);
  			//$contacsTotal[]=$p->scheduleDate;
  			$value->scheduleDate = $p->scheduleDate;
  		}  
	   }           
      //Llamamos a la function findCells y le pasamos los parametros de $objPHPExcel,$customfielsTotal,$array
      $this->findCells($objPHPExcel, $idContactlist, $array);
      //Llamamos a la function findCellsContact y le pasamos los parametros de $objContactlist,$objPHPExcel
      $this->findCellsContact($contacsTotal, $objPHPExcel);
      //
      unset($contacsTotal);
      //Llamamos a la function countContactlist y le pasamos los parametros de $idContactlist,$objPHPExcel
      $this->countContactlist($idContactlist, $objPHPExcel);
      //retornamos el llamado a la funcion que contiene los datos para descargar el archivo en Excel
      return $this->download($contactlist, $objPHPExcel);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @param type $objPHPExcel
   * @return \PHPExcel_Worksheet_Drawing
   */
  public function phpExcelWorksheet($objPHPExcel) {
    //Instaciamos la Clase PHPExcel_Worksheet_Drawing
    $objDrawing = new \PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('aio');
    $objDrawing->setDescription('aio');
    //Colocamos la imagen institucional
    $objDrawing->setPath('./images/sigma-logo.png');
    //Esta es la celda donde la imagen va aparecer
    $objDrawing->setCoordinates('A1');
    $objDrawing->getShadow()->setVisible(true);
    $objDrawing->getShadow()->setDirection(45);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    //Retornamos la Clase PHPExcel_Worksheet_Drawing
    return $objDrawing;
  }

  /**
   * 
   * @param obj $objPHPExcel
   * @param int $idContactlist
   * @param array $array
   * @return PHPExcel
   */
  public function findCells($objPHPExcel, $idContactlist, $array) {
    //Colocar en negrilla los titulos
     $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $objPHPExcel->getActiveSheet()->getStyle("B17:Z17")->getFont()->setBold(true);
    //Colocamos el titulo de las celdas de acuerdo a la informacion de los Contactos
    $objPHPExcel->getActiveSheet()->setCellValue("B17", "Correo");
    $objPHPExcel->getActiveSheet()->setCellValue("C17", "Nombre(s) y apellido(s)");
    $objPHPExcel->getActiveSheet()->setCellValue("D17", "Teléfono");
    $objPHPExcel->getActiveSheet()->setCellValue("E17", "Fecha de Nacimiento");
    $objPHPExcel->getActiveSheet()->setCellValue("F17", "Estado");
    //en produccion se cambiara al id de la cuenta de MCC 325
    if($idAccount == 49 || $idAccount == 325 ){
        $objPHPExcel->getActiveSheet()->setCellValue("G17", "Ultimo Email enviado");
    }
        
    
    //Colocamos el titulo de los estados de los Contactos
    $objPHPExcel->getActiveSheet()->setCellValue("A7", "Nombre de la lista:");
    $objPHPExcel->getActiveSheet()->setCellValue("A8", "Fecha de Creación de la lista:");
    $objPHPExcel->getActiveSheet()->setCellValue("A9", "Hora de Creación de la lista:");
    $objPHPExcel->getActiveSheet()->setCellValue("A10", "Categoría");
    $objPHPExcel->getActiveSheet()->setCellValue("A11", "Creado por:");
    $objPHPExcel->getActiveSheet()->setCellValue("A12", "Actualizado por:");
    $objPHPExcel->getActiveSheet()->setCellValue("A13", "Cantidad de Contactos:");
    $objPHPExcel->getActiveSheet()->getStyle("A15")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue("A15", "Información  de Contactos");
    //Hacemos una consulta para traer todos los campos personalizados que estan es su respetiva contactlist
    $customfielsTotal = \Customfield::find(array("conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => array(0 => $idContactlist)));
    //Declaramos esta variable el numero 70 que hace referencia a la letra (F)
    $number = 72;
    //Verificamos que tenga la variable $customfielsTotal tenga informacion
    if ($customfielsTotal) {
      //Creamos este for para que recorra la cantidad de celdas de acuerdo a los campos personalizados
      for ($i = $number; $i <($number + count($customfielsTotal)); $i++) {
        //Deacuerdo a las cantidad de campos personalizados calcula el ancho de la columna 
        $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
      }
      //Colocamos el titulo de los Campos personalizados por nombre
      //en produccion se cambiara al id de la cuenta de MCC 325
      if($idAccount == 49 || $idAccount == 325){
         foreach ($customfielsTotal as $r => $value) {
          //Colocar en negrilla los titulos
          $objPHPExcel->getActiveSheet()->getStyle($array[$r + 6])->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->setCellValue($array[$r + 6], $value->name);
          unset($customfielsTotal);
        } 
      }else{
  		  foreach ($customfielsTotal as $r => $value) {
    			//Colocar en negrilla los titulos
    			$objPHPExcel->getActiveSheet()->getStyle($array[$r + 5])->getFont()->setBold(true);
    			$objPHPExcel->getActiveSheet()->setCellValue($array[$r + 5], $value->name);
  		  }
        unset($customfielsTotal);
  	  }
    }
    unset($idContactlist);
    unset($array);
    //Retornamos el objecto PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @param int $idContactlist
   * @return query
   */
  public function findIdContact($idContactlist) {
    //Consulta para traer el IdContact de la tabla cxcl
    $conditions = array("columns" => "idContact", "conditions" => "idContactlist = ?0 AND deleted = ?1 ".$this->whereTypeExport, "bind" => array($idContactlist, 0));
    $contacts = \Cxcl::find($conditions);
    unset($conditions);
    //Creamos un Array
    $arrayIdContacts = [];
    //recorremos nuestro Array vacio que le asiganos el Array donde este el campo idContact
    foreach ($contacts as $contact) {
      $arrayIdContacts[] = (int) $contact['idContact'];
      unset($contacts);
    }
    //Hacemos una consulta para traer todos los contactos que estan es su respetiva contactlist
    $contacsTotal = \Contact::find([array(
      "idContact" => ['$in' => $arrayIdContacts],
      "deleted" => 0,
      ),
      "fields" => array(
        "idContact" => true,
        "email" => true,
        "name" => true,
        "lastname" => true,
        "indicative" => true,
        "phone" => true,
        "birthdate" => true
      )
    ]);
    //Le asignamos a la variable la function findContact y pasamos los datos del contacto y el id de Contactlist
    $contact = $contacsTotal;
    unset($contacsTotal);
    unset($idContactlist);;
    //Retornamos un query de Contact
    return $contact;
  }

  /**
   * 
   * @param obj $contacsTotal
   * @param int $idContactlist
   * @return array
   */
  public function findClasscontact($contacsTotal, $idContactlist) {
    $customfield = \Customfield::findFirst([
      "conditions" => "idContactlist = ?0 AND deleted = 0", 
      "bind" => [0 => (int) $idContactlist]
    ]);
    $array = [];
    foreach ($contacsTotal as $data) {
      $contactlist = new \stdClass();
      $contactlist->idContact = $data->idContact;
      $contactlist->email = $data->email;
      $contactlist->name = $data->name;
      $contactlist->lastname = $data->lastname;
      $contactlist->phone = $data->phone;
      $contactlist->birthdate = $data->birthdate;
      $cxcl = \Cxcl::findFirst(array(
        "conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
        "bind" => array(0 => $data->idContact, 1 => $idContactlist),
        "columns" => "status"
      ));
      $contactlist->status = $cxcl->status;
      
      unset($cxcl);
      if($customfield){
        $contactlist->Customfield = $this->findContacts($data->idContact, $idContactlist);
      }else{
        $contactlist->Customfield = null;
      }
      array_push($array, $contactlist);
      unset($contacsTotal);
    }
    return $array;
  }

  /**
   * 
   * @param int $idContact
   * @param int $idContactlist
   * @return array
   */
  public function findContacts($idContact, $idContactlist) {
    //declaramos una variable Como Array.
    $array = array();
    //Asignamos la informacion del Model y la consulta de todos los Contactos a la variable.
    $cxc = \Cxc::findFirst([["idContact" => (Int) $idContact]]);
    //Por cada Contacto varificamos que tenga Campos Personalizados.
    if (isset($cxc->idContactlist[$idContactlist])) {
      //Recorremos el Customfield por cada Contacto.
      $prueba = array_filter($cxc->idContactlist[$idContactlist]);
      unset($cxc);
      foreach ($prueba as $key => $value) {
        //verificamos que no sea Null
        if ($value != null) {
          //Al Array vacio le asignamos el array que creamos con la data de los Campos Personalizados.
          $array[$key] = ["value" => $value["value"]];
        }
        unset($prueba);
      }
    } else {
      $array[] = 0;
    }
    unset($cxc);
    //Retornamos el Array que contiene toda la data de los Campos Personalizados .
    return $array;
  }

  /**
   * 
   * @param obj $objContactlist
   * @param obj $objPHPExcel
   * @return PHPExcel
   */
  public function findCellsContact($objContactlist, $objPHPExcel) {
    //Creamos una variable y le asignamos la cantidad de filas donde van a aparecer los contactos
    $rowContacs = 18;
    $variableContador = 0;
    //Recorremos la data de los Contactos
    $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $cf = \Customfield::findFirst([
      "conditions" => "idContactlist = ?0 AND deleted = 0", 
      "bind" => [0 => (int) $this->idContactlist]
    ]);
    foreach ($objContactlist as $contacs) {
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowContacs, $contacs->email);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowContacs, $contacs->name . ' ' . $contacs->lastname);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowContacs, $contacs->phone);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowContacs, $contacs->birthdate);

      $cxcl = \Cxcl::findFirst(array(
        "conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
        "bind" => array(0 => $contacs->idContact, 1 => $this->idContactlist),
        "columns" => "status"
      ));
      $contacs->status = $cxcl->status;
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowContacs, $contacs->status);
      if($cf){
        $contacs->Customfield = $this->findContacts($contacs->idContact, $this->idContactlist);
      }else{
        $contacs->Customfield = null;
      }
      //en produccion se cambiara al id de la cuenta de MCC 325
      if($idAccount == 49 || $idAccount == 325){     
        if(empty($contacs->scheduleDate) || is_null($contacs->scheduleDate)){
          $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowContacs,"sin envios");			
        }else{
			    $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowContacs, $contacs->scheduleDate);			 
		    }
        $number = 72;      
      }else{
        $number = 71;
      }  
      
      $chrValidator = $number + count($contacs->Customfield);
      if($chrValidator > 90){
        $letras = array();//Creamos un arreglo vacio el cual le vamos a asignar las letras de la A a la Z
        $flagArray = false;
        $concatCHR = 64;//cada vez que llegue a la Z concatenara a la siguienre letra de excel ej AZ -> BA
        foreach ($contacs->Customfield as $value) {
          if($number <= 90){
            if($flagArray){
              $letras[] = chr($concatCHR)."".chr($number);
            }else{
              $letras[] = chr($number);
            }
            $number++;
          }else{
            $number = 65;
            $concatCHR++;
            $letras[] = chr($concatCHR)."".chr($number);
            $number++;
            $flagArray = true;
          }
        }
      }else{
        //Creamos un arreglo vacio el cual le vamos a asignar las letras de la A a la Z
        $letras = array();
        for ($l = $number; $l <($number + count($contacs->Customfield)); $l++) {
          $letras[] = chr($l);
        }
      }

      $i = 0;
      foreach ($contacs->Customfield as $customfield) {
        //$objPHPExcel->getActiveSheet()->setCellValue($letras[$i] . $rowContacs, $customfield['value']);
        if(is_array($customfield['value'])){
            $tmpImplode = implode(", ",$customfield['value']);
            $objPHPExcel->getActiveSheet()->setCellValue($letras[$i] . $rowContacs, $tmpImplode);
        }else{
            \Phalcon\DI::getDefault()->get('logger')->log("*****setCellValue {$i} - {$letras[$i]} ".$customfield['value']);
            $objPHPExcel->getActiveSheet()->setCellValue($letras[$i] . $rowContacs, $customfield['value']);
        }
        $i++;
        
        unset($contacs->Customfield);
      }
      $rowContacs++;
      $variableContador++;
      unset($objContactlist);
    }	
    //Retornamos el objecto PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @param int $idContactlist
   * @param obj $objPHPExcel
   * @return PHPExcel
   */
  public function countContactlist($idContactlist, $objPHPExcel) {
    //Consulta para traer la informacion de la lista de contactos
    $contactlist = \Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
    //Contamos cuantos Contactos Existen, estan Activos, estan Desuscritos, Rebotados, son Span, estan Bloqueados
    $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?0 AND deleted = 0 ".$this->whereTypeExport, "bind" => array($contactlist->idContactlist)));
    //Consultamos para traer la categoria de la lista de contactos
    $category = \ContactlistCategory::findFirst(array('conditions' => "idContactlistCategory = ?0", 'bind' => array($contactlist->idContactlistCategory)));
    //Recorremos la fila B con la data del Contactlist.
    $objPHPExcel->getActiveSheet()->setCellValue("B7", $contactlist->name);
    $objPHPExcel->getActiveSheet()->setCellValue("B8", date('Y-m-d', $contactlist->created));
    $objPHPExcel->getActiveSheet()->setCellValue("B9", date('H:i:s', $contactlist->created));
    $objPHPExcel->getActiveSheet()->setCellValue("B10", $category->name != Null ? $category->name : "Sin categoría");
    $objPHPExcel->getActiveSheet()->setCellValue("B11", $contactlist->createdBy);
    $objPHPExcel->getActiveSheet()->setCellValue("B12", $contactlist->updatedBy);
    $objPHPExcel->getActiveSheet()->setCellValue("B13", $contactlist->ctotal);
    $objPHPExcel->getActiveSheet()->setCellValue("C3", "LISTA DE CONTACTO " . $contactlist->name);
    //
    unset($contactlist);
    //Retornamos la variable que contiene el objecto de PhpExcel con la informacion de Contactlist
    return $objPHPExcel;
  }

  /**
   * 
   * @param obj $contactlist
   * @param obj $objPHPExcel
   * @return \Phalcon\Http\Response
   */
  public function download($contactlist, $objPHPExcel) {
      //Asignamos el nombre del Contactlist, la fecha e hora y el de documento.
      $name = $contactlist->name . " " . date('Y-m-d') . ".xlsx";
      $temp_file = $contactlist->name;
      //Instanciamos la clase PHPExcel_Writer_Excel2007
      $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
      $objWriter->save($temp_file);
      //Instanciamos la clase Response
      $response = new \Phalcon\Http\Response();
      $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      $response->setHeader('Content-Disposition', 'attachment;filename="' . $name . '"');
      $response->setHeader('Cache-Control', 'max-age=0');
      $response->setHeader('Cache-Control', 'max-age=1');
      $response->setContent(file_get_contents($temp_file));
      unlink($temp_file);

    //
    unset($contactlist);
    //Retornamos el objecto de clase Response
    return $response;
  }

  /**
   * @name Jordan Zapata mora
   * @param type update Actualizacion de estados del contacto 
   * @return boolean
   */
  public function unsubscribeContact($data) {
    $msg = array();
    $idAccount = $this->user->Usertype->Subaccount->idAccount;
    
    if ($data->type == "unsubscribeOnly") {
      
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
      $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
      $customLogger->idContactlist = $data->idContactlist;
      $customLogger->idContact = $data->idContact;
      $customLogger->email = $data->email;
      $customLogger->indicative = $data->indicative;
      $customLogger->phone = $data->phone;
      $customLogger->typeName = "unsubscribeOnlyMethod";
      if ($data->status) {
        $customLogger->detailedLogDescription = "Se ha desuscrito el contacto de esta lista";
      } else if (!$data->status) {
        $customLogger->detailedLogDescription = "Se ha suscrito el contacto de esta lista";
      }
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
      unset($customLogger);
      
      $dataContact = $this->getAllContact($data,$idAccount);
      $dataID = array();
      foreach ($dataContact as $value) {
        $dataID[] = $value->idContact;
      }      
      unset($dataContact);
      $listId = implode(",", $dataID);
      
      foreach ($dataID as $value) {
        
        $contactsegment = \Sxc::find([["idContact" => $value]]);
        
        if( $contactsegment != false ){
            
            foreach( $contactsegment as $value2 ){
                if ($data->status) {
                    $value2->unsubscribed = time();
                    $value2->save();
                }else if (!$data->status) {
                    $value2->unsubscribed = 0;
                    $value2->save();
                }
            }            
        }
        unset($contactsegment);          
      }
      
      unset($dataID);

      $cxcl = \Cxcl::find(array(
        "conditions" => "idContact IN ({$listId}) and idContactlist = ?1 and deleted = 0",
        "bind" => array(1 => $data->idContactlist)
      ));
      unset($listId);
      foreach ($cxcl as $value) {
        if ($data->status) {
          $msg['menssage'] = "Se ha desuscrito el contacto";
          $value->unsubscribed = time();
          $value->active = 0;
          if (!($value->status == 'blocked' or $value->status == 'spam' or $value->status == 'bounced')) {
            $value->status = 'unsubscribed';
          }
        } else if (!$data->status) {
          $msg['menssage'] = "Se ha suscrito el contacto";
          $value->active = time();
          $value->unsubscribed = 0;
          if (!($value->status == 'blocked' or $value->status == 'spam' or $value->status == 'bounced')) {
            $value->status = 'active';
          }
        }
        if (!$value->save()) {
          foreach ($value->getMessages() as $message) {
            $this->trace("fail", "No se desuscribio el contacto {$message}");
          }
        }
      }
      unset($cxcl);
      $this->setAccountants($data->idContactlist);
      $sql = "CALL updateCountersAccount({$idAccount})";
      $this->db->fetchAll($sql);
      unset($sql);
      unset($data);
    } else if ($data->type == "unsubscribeAll") {
      
      $customLogger = new \Logs();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
      $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
      $customLogger->idContactlist = $data->idContactlist;
      $customLogger->idContact = $data->idContact;
      $customLogger->email = $data->email;
      $customLogger->indicative = $data->indicative;
      $customLogger->phone = $data->phone;
      $customLogger->typeName = "unsubscribeAllMethod";
      if ($data->status) {
        $customLogger->detailedLogDescription = "Se ha desuscrito el contacto de todas las lista";
      } else if (!$data->status) {
        $customLogger->detailedLogDescription = "Se ha suscrito el contacto de todas las lista";
      }
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
      unset($customLogger);
      
      $dataContact = $this->getAllContact($data,$idAccount);
      $DataidContact = array();
      foreach ($dataContact as $value) {
        $DataidContact[] = $value->idContact;
      }
      unset($dataContact);
      $listId = implode(",", $DataidContact);
      
      foreach ($DataidContact as $value) {
        
        $contactsegment = \Sxc::find([["idContact" => $value]]);
        
        if( $contactsegment != false ){
            
            foreach( $contactsegment as $value2 ){
                if ($data->status) {
                    $value2->unsubscribed = time();
                    $value2->save();
                }else if (!$data->status) {
                    $value2->unsubscribed = 0;
                    $value2->save();
                }
            }            
        }
        unset($contactsegment);          
      }
      
      unset($DataidContact);
      $cxcl = $this->getAllContactxCxcl($listId, $idAccount);
      unset($listId);
      $dataIdContactlist = array();
      foreach ($cxcl as $value) {
        if (empty($dataIdContactlist[$value->idContactlist])) {
          $dataIdContactlist[$value->idContactlist] = $value->idContactlist;
        }
        $time = time();
        $sql = "";
        if ($data->status) {
          if (!($value->status == 'blocked' or $value->status == 'spam' or $value->status == 'bounced')) {
            $sql = "UPDATE  cxcl SET unsubscribed = {$time}, active=0, `status` = 'unsubscribed' WHERE idCxcl = {$value->idCxcl};";
          } else {
            $sql = "UPDATE  cxcl SET unsubscribed = {$time}, active=0 WHERE idCxcl = {$value->idCxcl};";
          }
          $msg['menssage'] = "Se ha desuscrito el contacto en todas las listas";
        } else if (!$data->status) {
          if (!($value->status == 'blocked' or $value->status == 'spam' or $value->status == 'bounced')) {
            $sql = "UPDATE  cxcl SET unsubscribed = 0, active={$time}, `status` = 'active' WHERE idCxcl = {$value->idCxcl};";
          } else {
            $sql = "UPDATE  cxcl SET unsubscribed = 0, active={$time} WHERE idCxcl = {$value->idCxcl};";
          }
          $msg['menssage'] = "Se ha suscrito el contacto en todas las listas";
        }
        $this->db->query($sql);
      }
      unset($cxcl);
      foreach ($dataIdContactlist as $key) {
        $this->setAccountants($key);
      }
      unset($dataIdContactlist);
      $this->setAccountCounters($idAccount);
    }
    return $msg;
  }

  public function setAccountCounters($idAccount) {
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->execute($sql);
  }
  
  public function getAllContact($data,$idAccount){
    if (($data->email != "") && ($data->indicative != "") && ($data->phone != "")) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => (string) $data->email,
          'indicative' => (string) $data->indicative,
          'phone' => ['$in' => array((string) $data->phone, (int) $data->phone)],
          'deleted' => 0
        )
      );
    } elseif (($data->email == "") && ($data->indicative != "") && ($data->phone != "")) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => "",
          'indicative' => (string) $data->indicative,
          'phone' => (string) $data->phone,
          'deleted' => 0
        )
      );
    } elseif (($data->email != "") && (($data->indicative == "") && ($data->phone == ""))) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => (string) $data->email,
          'indicative' => "",
          'phone' => "",
          'deleted' => 0
        )
      );
    }
    unset($data);
    unset($idAccount);
    $dataContact = \Contact::find($query);
    unset($query);
    return $dataContact;
  }
  
  public function getAllContactlistxContact($idContact, $idAccount) {
    $builder = $this->getAllContactxContactList($idContact, $idAccount);
    for ($index = 0; $index < count($builder); $index++) {
      $string .= $builder[$index]->name . ((count($builder) == $index) ? " ," : " ");
    }
    unset($builder);
    if($string) {
      $string = "Ya existe un contacto asociado con el telefono o email ingresado y se encuentra en lista de contatos"
             . " '{$string}' si continua la informaciÃƒÂ³n del contacto se actualizara con la ingresada.";
    } else {
      $this->dataUpdate();
      $string = "Ya existe un contacto asociado con el telefono o email ingresado y No se encuentra eliminado, "
            . " si continua la informaciÃƒÂ³n del contacto se actualizara con la ingresada.";
    }
    return $string;
  }
  
  public function getAllContactxContactList($idContact, $idAccount){
    $builder = $this->modelsManager->createBuilder()
      ->columns('Contactlist.*')
      ->from('Subaccount')
      ->join('Contactlist', 'Contactlist.idSubaccount = Subaccount.idSubaccount')
      ->join('Cxcl', 'Cxcl.idContactlist = Contactlist.idContactlist')
      ->where("Cxcl.idContact IN ({$idContact}) AND Subaccount.idAccount = {$idAccount} AND Cxcl.deleted = 0")
      ->getQuery()
      ->execute();
    unset($idContact);
    unset($idAccount);
    return $builder;
  }
  
  public function getAllContactxCxcl($idContact, $idAccount){
    $builder = $this->modelsManager->createBuilder()
      ->columns('Cxcl.*')
      ->from('Subaccount')
      ->join('Contactlist', 'Contactlist.idSubaccount = Subaccount.idSubaccount')
      ->join('Cxcl', 'Cxcl.idContactlist = Contactlist.idContactlist')
      ->where("Cxcl.idContact IN ({$idContact}) AND Subaccount.idAccount = {$idAccount} AND Cxcl.deleted = 0")
      ->getQuery()
      ->execute();
    unset($idContact);
    unset($idAccount);
    return $builder;
  }

  public function dataUpdate(){
    $this->dataUpdate = 1;
  }
  
  public function getOneContact($data,$idAccount){
    if (($data->email != "") && ($data->indicative != "") && ($data->phone != "")) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => (string) $data->email,
          'indicative' => (string) $data->indicative,
          'phone' => (string) $data->phone,
          'deleted' => 0
        )
      );
    } elseif (($data->email == "") && ($data->indicative != "") && ($data->phone != "")) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => "",
          'indicative' => (string) $data->indicative,
          'phone' => (string) $data->phone,
          'deleted' => 0
        )
      );
      $this->singlePhone = time();      
    } elseif (($data->email != "") && (($data->indicative == "") && ($data->phone == ""))) {
      $query = array(
        'conditions' => array(
          'idAccount' => (String) $idAccount,
          'email' => (string) $data->email,
          'indicative' => "",
          'phone' => "",
          'deleted' => 0
        )
      );
    }
    unset($data);
    unset($idAccount);
    $dataContact = \Contact::findFirst($query);
    unset($query);
    return $dataContact;
  }
  
  public function saveContactbyform($idContactlist) {
    $idContactReturn = null;
    $msg = array();    
    $contactlist = \Contactlist::findFirst(array(
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $idContactlist)
    ));
    if (!$contactlist) {
      throw new \InvalidArgumentException("No se encontrÃ³ la lista de contactos.");
    }
    $idAccount = (string) \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $idSubaccount = (string) \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idSubaccount;

    $saxs = null;
    foreach ($contactlist->Subaccount->Saxs as $value) {
      if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
        foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $item) {
          if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
            $saxs = $item;
          }
        }
      }
    }
    if (isset($saxs) && $saxs->amount <= 0) {
      throw new InvalidArgumentException("No cuenta con la capacidad suficiente para crear más contactos, contacte a su administrador.");
    }

    $flagEmail = true;
    $flagPhone = true;

    if (!isset($this->data->email) || filter_var($this->data->email, FILTER_VALIDATE_EMAIL) == false) {
      $flagEmail = false;
      $cemail = "-";
    } else {
      $cemail = $this->data->email;
    }
    if (!isset($this->data->phone) || !isset($this->data->indicative) || !is_numeric($this->data->phone) || !is_numeric($this->data->indicative)) {
      $flagPhone = false;
      $cphone = "-";
      $cindicative = "-";
    } else {
      $cphone = $this->data->phone; 
        $country = \Country::findFirst(array(
                 "conditions" => "phoneCode = ?0",
                 "bind" => array($this->data->indicative)
                 ));  
    
      $cantdigits = strlen($cphone);
      if ($cantdigits < $country->minDigits || $cantdigits > $country->maxDigits) {
        throw new \InvalidArgumentException("El número telefónico no cumple con la cantidad de digitos mínimos y máximos de acuerdo a lo permitido");
      }
      $this->data->indicative = $country->phoneCode;
      $cindicative = $this->data->indicative;
    }
    if (!$flagEmail && !$flagPhone) {
      throw new \InvalidArgumentException("El contacto debe contener al menos el correo electrónico o el número del móvil con su respectivo indicativo");
    }

    if (isset($this->data->birthdate) && strtotime($this->data->birthdate) > time()) {
      throw new \InvalidArgumentException("La fecha de nacimiento no puede superior a la fecha actual");
    }
   
    $contact = new \Contact();
    $contactManger = new \Sigmamovil\General\Misc\ContactManager();
    $nextIdContact = $contactManger->autoIncrementCollection("id_contact");
    $contact->idContact = $nextIdContact;
    $contact->blockedPhone = "";
    $contact->blockedEmail = "";
    $contact->phone = "";
    $contact->indicative = "";
    $contact->email = "";
    $contact->name = "";
    $contact->lastname = "";
    $contact->birthdate = "";

    $contact->idSubaccount = $idSubaccount;
    $contact->idAccount = $idAccount;
    $contact->deleted = 0;

    foreach ($this->data as $key => $value) {
      if ($value != "" && $value != null && !empty($value)) {
        if ($key == "indicative" || $key == "phone") {
          if ($flagPhone) {
            $contact->$key = $value;
          }
        } else if ($key == "email") {
          if ($flagEmail) {
            $contact->$key = strtolower($value);
          }
        } else {
          if ($key == "name" || $key == "lastname" || $key == "birthdate") {
            $contact->$key = $value;
          }
        }
      }
    }
    
    $status = "active";
    if($contact->email != ""  && $contact->phone != "" && $this->isEmailPhoneBlocked($contact->email,$contact->phone, $contact->indicative)){
      $contact->blockedEmail = time();
      $contact->blockedPhone = time();
      $status = "blocked";
    } else if ($contact->email != ""  && $this->isEmailBlocked($contact->email) ) {
      $contact->blockedEmail = time();
      $status = "blocked";
    } else if ($contact->phone != "" && $this->isPhoneBlocked($contact->phone, $contact->indicative)) {
      $contact->blockedPhone = time();
      $status = "blocked";
    }

    if (!isset($this->data->valid)) {
      $this->data->valid = false;
    }
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $obj = array();
    foreach ($customfield as $keycustomfield) {
      $name = $keycustomfield->alternativename;
      if (isset($this->data->$name)) {
        $value = $this->data->$name;
        if ($keycustomfield->type != "Multiselect") {
          $value = trim($value);
        }
        $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => $value, "type" => $keycustomfield->type];
      } else {
        $obj[$keycustomfield->idCustomfield] = ["name" => $keycustomfield->name, "value" => "", "type" => $keycustomfield->type];
      }
    }
    unset($customfield);
    $singlePhone = 0;
    if (!$this->data->valid) {

      if ($contact->email != "" && $contact->indicative == "" && $contact->phone == "") {
        $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => "", "phone" => "", "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
          $this->data->validateConfirm = true;
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate != false && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      } else if ($contact->email == "" && $contact->indicative != "" && $contact->phone != ""){
        $singlePhone = time();
        $where = ["idAccount" => $idAccount, "email" => "", "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
          $this->data->validateConfirm = true;
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      } else if ($contact->email != "" && $contact->indicative != "" && $contact->phone != "") {
        $where = ["idAccount" => $idAccount, "email" => $contact->email, "indicative" => $contact->indicative, "phone" => $contact->phone, "deleted" => 0];
        $contactValidate = \Contact::findFirst([$where]);
        unset($where);
        if($contactValidate){
          $this->data->validateConfirm = true;
          $idContact = $contactValidate->idContact;
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1 AND deleted = 0", "bind" => [0 => $idContact, 1 => $idContactlist]]);
          if ($cxcl) {
            $this->dataUpdate = 0;
          } else {
            $this->dataUpdate = 1;
          }
          unset($cxcl);
        }
        if ($contactValidate && !$this->data->validateConfirm) {
          $string = $this->getAllContactlistxContact($idContact, $idAccount);
          throw new \Sigmamovil\General\Exceptions\ValidateEmailException($string, 409);
          unset($string);
        }
      }
    }

    if ($this->data->validateConfirm) {
      if($this->dataUpdate == 1){
      
    $cxc = \Cxc::findFirst([["idContact" =>(int) $idContact]]);
    
    if(!empty($cxc)){
      if (!isset($cxc->idContactlist[$idContactlist])) {
          $tmp = $cxc->idContactlist;
          foreach($obj as $value => $key){
            $tmp[$contactlist->idContactlist][$value] = $key;
          }
          $cxc->idContactlist = null;
          $cxc->idContactlist= (object) $tmp;
          $cxc->save();
          unset($cxc); 
      }  
    }               
        //
        $cxcl = new \Cxcl();
        $cxcl->idContact = $idContact;
        $cxcl->idContactlist = $idContactlist;
        $cxcl->unsubscribed = 0;
        $cxcl->$status = time();
        $cxcl->status = $status;
        $cxcl->singlePhone = $singlePhone;

        if (!$cxcl->save()) {
          foreach ($cxcl->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        unset($cxcl);
      }
      $contactValidate->name = $contact->name;
      $contactValidate->lastname = $contact->lastname;
      $contactValidate->birthdate = $contact->birthdate;
      $contactValidate->updated = time();
      $contactValidate->idSubaccount = $idSubaccount;
      if (!$contactValidate->save()) {
        foreach ($contactValidate->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      unset($contactValidate);
      $this->addBlockedContactExist($idContact); 
      $idContactReturn = $idContact;
      $msg['menssage'] = "Se ha actualizado el contacto";
      $msg['idContact'] = $idContactReturn;
    } else {
      if ($contact->save()) {
        $idContactReturn = $contact->idContact;
        $cxc = new \Cxc();
        $cxc->idContact = $contact->idContact;
        $cxc->idContactlist = [$idContactlist => $obj];
        $cxc->save();
        unset($cxc); 
        //
            $cxcl = new \Cxcl();
            $cxcl->idContact = $contact->idContact;
            $cxcl->idContactlist = $idContactlist;
            $cxcl->unsubscribed = 0;
            $cxcl->$status = time();
            $cxcl->status = $status;
            $cxcl->singlePhone = $singlePhone;
    
            if (!$cxcl->save()) {
              $contact->delete();
              foreach ($cxcl->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
              }
            }
            $this->addBlockedContactExist($contact->idContact);         
            $validateEmail = $this->validateEmail($contact->email);
            if (!$validateEmail) {
    
              if (isset($contact->email)) {
                $email = \Email::findFirst([["email" => $contact->email, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
    
                if (!$email) {
                  $email = new \Email();
                }
                $email->email = $contact->email;
                $domainEmail = $this->extractDomainEmail($contact->email);
                $domain = \Domain::findFirst([["domain" => $domainEmail]]);
                if (!$domain) {
                  $domain = $this->createDomain($domainEmail);
                }
                $email->idDomain = $domain->idDomain;
                $email->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
                $email->deleted = 0;
                if (!$email->save()) {
                  foreach ($email->getMessages() as $message) {
    
                  }
                  $this->trace("fail", "No se logro crear una cuenta");
                }
              }
            } else {
              $email = \Email::findFirst([["email" => $contact->email, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
              $email->deleted = 0;
              if (!$email->save()) {
                foreach ($email->getMessages() as $message) {
    
                }
                $this->trace("fail", "No se logro crear una cuenta");
              }
            }
            $validatePhone = $this->validatePhone($contact->phone);
            if (!$validatePhone) {
              if (isset($contact->phone)) {
                $phone = \Phone::findFirst([["phone" => $contact->email, "indicative" => $contact->indicative, "idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
                if (!$phone) {
                  $phone = new \Phone();
                }
                $phone->indicative = $contact->indicative;
                $phone->phone = $contact->phone;
                $phone->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
                $phone->deleted = 0;
                if (!$phone->save()) {
                  foreach ($phone->getMessages() as $message) {
      //                throw new \InvalidArgumentException($message);
                  }
                  $this->trace("fail", "No se logro crear una cuenta");
                }
              }
            }
          
            $this->setAccountants($idContactlist);
            $sql = "CALL updateCountersAccount({$contactlist->Subaccount->idAccount})";
            $this->db->fetchAll($sql);
            $msg['menssage'] = "Se ha creado el contacto";
            $msg['idContact'] = $idContactReturn;
      } else {
        throw new \Exception("Ha ocurrio un error al guardar el contacto");
      }
    }
    return $msg;
  }
  
  public function getidContact(){
  return $this->idContact;  
  }
  
}