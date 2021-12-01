<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;
ini_set('memory_limit', '1024M');

use Sigmamovil\General\Links\ParametersEncoder;

/**
 * Description of WhatsappWrapper
 *
 * @author santiago.cardona
 */
class WhatsappWrapper extends \BaseWrapper {

    private $nameRoles;
    private $roles;
    private $security;
    private $configFb;
    private $session;
    private $contactlists;
    private $contact = array();
    public $where;

    public function __construct() {
      $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
      $this->roles = \Phalcon\DI::getDefault()->get('roles');
      $this->security = \Phalcon\DI::getDefault()->get('security');
      $this->configFb = \Phalcon\DI::getDefault()->get('configFb');
      $this->session = \Phalcon\DI::getDefault()->get('session');
      parent::__construct();
    }

    public function receiverWhatsapp($data){
      if(isset($data["bulkId"])){
          \Phalcon\DI::getDefault()->get('logger')->log("SI bulkId ".json_encode($data["bulkId"]));
      }

      $wppResponse = new \WppResponse();
      $wppResponse->response = json_encode($data);
      $wppResponse->created = time();
      $wppResponse->updated = time();

      if (!$wppResponse->save()) {
          foreach ($wppResponse->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
          }
      }  
    }

    public function getallwhatsapp($page, $data) {
      $where = " ";
  
      if (isset($data['name']) && $data['name'] != "") {
        $where .= " AND whatsapp.name LIKE '%{$data['name']}%' ";
      }
  
      if (isset($data['category']) && count($data['category']) >= 1) {
        $arr = implode(",", $data['category']);
        $where .= "  AND whatsapp.idWppCategory IN ({$arr})";
      }

      if (isset($data['wppStatus']) && $data['wppStatus'] != "" && $data['wppStatus'] != "allStatuses") {
          $where .= " AND whatsapp.status = '{$data['wppStatus']}' ";
      }
  
      if (isset($data['dateinitial']) && isset($data['dateend'])) {
        if ($data['dateinitial'] != "" && $data['dateend'] != "") {
          if (strtotime($data['dateinitial']) > strtotime($data['dateend'])) {
            throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final. ');
          }
          $where .= " AND startdate BETWEEN '{$data['dateinitial']}' AND '{$data['dateend']}'";
        }
      }
  
      if ((isset($data['dateinitial']) && $data['dateinitial'] != "") && (!isset($data['dateend']) && $data['dateend'] == "")) {
        if ($data['dateinitial'] > date('Y-m-d')) {
          throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
        }
        $where .= " AND startdate BETWEEN '{$data['dateinitial']}' AND Date_format(now(),'%Y/%m/%d')";
      }
  
  
  
      if ((isset($data['dateinitial']) && !empty($data['dateinitial'])) && (isset($data['dateend']) && !empty($data['dateend']))) {
        if ($data['dateinitial'] > $data['dateend']) {
          return \InvalidArgumentException('La fecha inicial es mayor por favor cambie el rango.');
        }
        $startDate = strtotime($data['dateinitial']);
        $finalDate = strtotime($data['dateend']);
        $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
      }
  
  
      (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
      $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
      $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
      $sql = "SELECT * from whatsapp "
              . " WHERE whatsapp.idSubaccount = {$idSubaccount} AND whatsapp.logicodeleted = 0 {$where}"
              . " ORDER BY idWhatsapp DESC "
              . "  LIMIT {$limit} "
              . " OFFSET {$page}";
      $sql2 = "SELECT whatsapp.idWhatsapp FROM whatsapp "
              . " WHERE whatsapp.idSubaccount = {$idSubaccount} AND whatsapp.logicodeleted = 0 {$where}";
      //. " GROUP BY whatsapp.idWhatsapp ";
  
      $data = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
      $totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql2);
  
      return $this->modelData($data, $totals);
      
    }


    public function modelData($data, $totals) {
      $arrReturn = array("total" => count($totals), "total_pages" => ceil(count($totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
      $arr = array();
      foreach ($data as $key => $value) {
        $arr[$key] = array("idWhatsapp" => $value['idWhatsapp'],
            "idWppCategory" => $value['idWppCategory'],
            "idSubaccount" => $value['idSubaccount'],
            "logicodeleted" => $value['logicodeleted'],
            "notification" => $value['notification'],
            "status" => $value['status'],
            "type" => $value['type'],
            "created" => date('d/m/Y g:i a', $value['created']),
            "updated" => date('d/m/Y g:i a', $value['updated']),
            "createdBy" => $value['createdBy'],
            "updatedBy" => $value['updatedBy'],
            "sent" => $value['sent'],
            "total" => $value['total'],
            "target" => $value['target'],
            "startdate" => $value['startdate'],
            "email" => (empty($value['email'])) ? false : $value['email'],
            "name" => $value['name']);
      }
      $arrReturn["items"] = $arr;
      return $arrReturn;
    }

    public function findallcategory() {
      $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
  
      $conditions = array(
          "conditions" => "deleted = ?0 AND idAccount = ?1 AND status=1",
          "bind" => array(0, $idAccount)
      );
  
      $smscategory = \WppCategory::find($conditions);
      $data = array();
      if (count($smscategory) > 0) {
        foreach ($smscategory as $key => $value) {
          $data[$key] = array(
              "idWppCategory" => $value->idWppCategory,
              "idAccount" => $value->idAccount,
              "name" => $value->name,
              "description" => $value->description,
              "createdBy" => $value->createdBy,
              "updatedBy" => $value->updatedBy
          );
        }
      }
      return $data;
    }

    public function getAllContanctList() {
      $subAccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
      $this->contactlists = \Contactlist::find(array(
        "conditions" => "idSubaccount = ?0 and listWhatsApp=1 and deleted = 0", 
        "bind" => array(0 => $subAccount)
      ));
      $this->modelDataContactlist();
    }

    public function modelDataContactlist() {
      foreach ($this->contactlists as $data) {
        $contactlist = new \stdClass();
        $contactlist->idContactlist = $data->idContactlist;
        $contactlist->name = $data->name;
        $contactlist->idContactlistCategory = $data->idContactlistCategory;
        $contactlist->cactive = $data->cactive;
        array_push($this->contact, $contactlist);
      }
    }

    public function getContact() {
      return $this->contact;
    }

    public function listWppTemplate() {

      $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));

      $wpptemplate = \WppTemplate::find(array(
        "conditions" => "idAccount = ?0 and approved=1 and deleted = 0", 
        "bind" => array(0 => $idAccount)
      ));
        
      $data = array();
      if (count($wpptemplate) > 0) {
        foreach ($wpptemplate as $key => $value) {
          $data[$key] = array(
              "idWppTemplate" => $value->idWppTemplate,
              "name" => $value->name,
              "content" => $value->content,
          );
        }
      }

      return $data;

    }

    public function getCountContacts($idContactlist) {

      $count = 0;
      $arrIdContact = array();

      /*$where = " ";
      for ($i = 0; $i < count($data->contactlist); $i++) {
        $where .= $data->contactlist[$i]->idContactlist;
        if ($i != (count($data->contactlist) - 1)) {
          $where .= " , ";
        }
      }        
      $sql = "select DISTINCT idContact from cxcl "
              . "where idContactlist in ({$where}) "
              . " AND unsubscribed = 0 "
              . " AND deleted = 0 ";  */

      $sql = "select DISTINCT idContact from cxcl where idContactlist in ({$idContactlist}) AND unsubscribed = 0  AND deleted = 0 "; 
      \Phalcon\DI::getDefault()->get('logger')->log("sql ".json_encode($sql));
      $count = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
      foreach ($count as $key) {
        $arrIdContact[] = (int) $key['idContact'];
      }
      \Phalcon\DI::getDefault()->get('logger')->log("arrIdContact ".json_encode($arrIdContact));
      $this->where = $where;
      $datas['counts'] = $this->getCountContactsValidate($arrIdContact);
      $datas['tags'] = $this->getAllTags($where);

      $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $idContactlist]]);
      if ($countContacts > 0) {
        $datas['contact'] = $this->getFisrtContact($idContactlist);
        break;
      }
      
      \Phalcon\DI::getDefault()->get('logger')->log("datas ".json_encode($datas));

      return $datas;

    }

    public function getCountContactsValidate($arrIdContact) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $idAccount = $this->user->Usertype->Subaccount->idAccount;
    if($idAccount == 49 || $idAccount == '49' || $idAccount == 101 || $idAccount == '101'){
        $contact = 0;
        if(!empty($this->where)){            
         $ids = implode(',',$arrIdContact);          
         //$contact = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist IN ({$this->where}) AND Cxcl.idContact IN($idss)"]);
         $contact = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist IN ({$this->where}) "]); 
                   
        }                
        
    }else{
         
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
    return $contact;
  }

  public function getFisrtContact($id) {
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

}