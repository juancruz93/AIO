<?php

namespace Sigmamovil\Wrapper;

class BlockadeWrapper extends \BaseWrapper {

  private $blocked = array();
  private $totals;
  public $data = array();

  public function findBlocked($page, $stringSearch) {
    $where = array();
    $where["idAccount"] = (int)$this->user->Usertype->Subaccount->idAccount;
    $where["deleted"] = 0;
    if ($stringSearch != -1) {
      $stringSearch = explode(",", $stringSearch);
      foreach ($stringSearch as $key) {
        $key = trim($key);
        $arr[] = ["email" => ['$regex' => ".*$key.*", '$options' => "i"]];
        $arr[] = ["phone" => ['$regex' => ".*$key.*"]];
        $where['$or'] = $arr;
      }
    }
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $this->data = \Blocked::find([$where,"limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
                "skip" => $page, "sort" => ["idBlocked" => -1]]);
    $this->totals = \Blocked::count([$where]);
    //$this->blocked = array(array("items" => $this->data),"total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $this->modelData();
  }

  public function modeldata() {
    $arr = array();
    foreach ($this->data as $value) {
      $obj = array();
      foreach ($value->idContacts as $key) {
        
        if(empty($key)){
            
         $obj = new \stdClass();
         $obj->idContactlist = 0;
         $obj->name = "";  
         
        }else{
            $sql = "SELECT contactlist.idContactlist, contactlist.name FROM cxcl JOIN  contactlist ON cxcl.idContactlist = contactlist.idContactlist"
                . " WHERE cxcl.deleted = 0 AND cxcl.idContact  = {$key} "
                . " AND contactlist.idSubaccount = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
            if($this->db->fetchAll($sql)){
                $obj = (object) $this->db->fetchAll($sql);
            }   
        }

      }
      
      $value->contactlist = $obj;
      $arr[] = $value;
    }
    $this->blocked = array(array("items" => $arr),"total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
  }

  public function getBlocked() {
    return $this->blocked;
  }

  public function saveBlocked($data) {
    

    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
    $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
    if (!isset($data->email) && !(isset($data->phone) and isset($data->idCountry))) {
      throw new \InvalidArgumentException("Debes ingresar el correo electronico o el numero del movil");
    }
    if (isset($data->phone) and ! isset($data->idCountry)) {
      throw new \InvalidArgumentException("Debe seleccionar el indicativo");
    }
    if (!isset($data->motive)) {
      throw new \InvalidArgumentException("El motivo es un campo obligatorio");
    }
    if ((isset($data->motive) && strlen($data->motive) < 2) or ( isset($data->motive) && strlen($data->motive) > 160)) {
      throw new \InvalidArgumentException("El motivo debe contener entre 2 y 160 caracteres");
    }
    $email = "";
    $phone = "";
    $indicative = "";
    //
    //
    if (isset($data->email) && isset($data->phone) && isset($data->idCountry)){
      $data->email = strtolower($data->email);
      if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
      }
      $email = (string) $data->email;
      if (!is_numeric($data->phone)) {
        throw new InvalidArgumentException("Solo se permite números en el campo móvil");
      }
      $phone = (string) $data->phone;

      $indicativeCountry = \Country::findFirst(array(
        "columns" => "phoneCode",
        "conditions" => "idCountry = ?0",
        "bind" => array($data->idCountry)
      ));
      if($indicativeCountry != false){
        $indicative = (string) $indicativeCountry->phoneCode;
      }
      $where = ['email' => $email, 'phone' => $phone, 'indicative' => $indicative, 'idAccount' => (string) $idAccount, 'deleted' => 0];
      $blockedContact = \Contact::find([$where]);
      if ($blockedContact) {
        $this->setBlockedContact($data, $blockedContact, $idAccount, "email", "phone");
      }else{
        $validateblocked = \Blocked::findFirst([["email" => $email, "phone" => $phone, "indicative" => $indicative, "idAccount" => (int) $idAccount, "deleted" => 0]]);
        
        if($validateblocked){
            $validateblocked->blocked = time();  
            $validateblocked->updated = time();
            $validateblocked->motive = $data->motive;
            $validateblocked->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
            if (!$validateblocked->save()) {
                throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
            }  
        }else{
            $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
            $blocked = new \Blocked();
            $blocked->idBlocked = $nextIdBlocked;
            $blocked->email = $email;
            $blocked->phone = $phone;
            $blocked->indicative = $indicative;
            $blocked->idAccount = (int) $idAccount;
            $blocked->motive = $data->motive;
            $blocked->blocked = time();
            $blocked->created = time();
            $blocked->updated = time();
            $blocked->deleted = 0;
            $blocked->idContacts = [];
            $blocked->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
            $blocked->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
            if (!$blocked->save()) {
                throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
            } 
        }        
      }
              

      unset($blockedContact);
    } else if (isset($data->email)) {
      $data->email = strtolower($data->email);
      if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
      }
      $email = (string) $data->email;
      $where = ['email' => $email, 'idAccount' => $idAccount, 'deleted' => 0];
      $blockedContact = \Contact::find([$where]);
      if ($blockedContact) {
        $this->setBlockedContact($data, $blockedContact, $idAccount, "email", "");
      }else{
        
        $validateblocked = \Blocked::findFirst([["email" => $email, "phone" => "", "indicative" => ['$in' => ["", null, "null", 0, "0"]], "idAccount" => (int) $idAccount, "deleted" => 0]]);
        
        if($validateblocked){
            $validateblocked->blocked = time();  
            $validateblocked->updated = time();
            $validateblocked->motive = $data->motive;
            $validateblocked->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
            if (!$validateblocked->save()) {
                throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
            }   
        }else{
            $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
            $blocked = new \Blocked();
            $blocked->idBlocked = $nextIdBlocked;
            $blocked->email = $email;
            $blocked->phone = "";
            $blocked->indicative = "";
            $blocked->idAccount = (int) $idAccount;
            $blocked->motive = $data->motive;
            $blocked->blocked = time();
            $blocked->created = time();
            $blocked->updated = time();
            $blocked->deleted = 0;
            $blocked->idContacts = [];            
            $blocked->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
            $blocked->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
            if (!$blocked->save()) {
                throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
            } 
        }
      }
      unset($blockedContact);
    } else if (isset($data->phone) && isset($data->idCountry)) {
        
      if (!is_numeric($data->phone)) {
        throw new InvalidArgumentException("Solo se permite números en el campo móvil");
      }
      $phone = (string) $data->phone;

      $indicativeCountry = \Country::findFirst(array(
        "columns" => "phoneCode",
        "conditions" => "idCountry = ?0",
        "bind" => array((int)$data->idCountry)
      ));
      if($indicativeCountry != false){
        $indicative = (string) $indicativeCountry->phoneCode;
      }
      $where = ['phone' => $phone, 'indicative' => $indicative, 'idAccount' => (string) $idAccount, 'deleted' => 0];
      $blockedContact = \Contact::find([$where]);
      if ($blockedContact) {
        $this->setBlockedContact($data, $blockedContact, $idAccount, "", "phone");
      }else{
            $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
            $blocked = new \Blocked();
            $blocked->idBlocked = $nextIdBlocked;
            $blocked->email = "";
            $blocked->phone = (string)$data->phone;
            $blocked->indicative = (string)$indicative;
            $blocked->idAccount = (int) $idAccount;
            $blocked->motive = $data->motive;
            $blocked->blocked = time();
            $blocked->created = time();
            $blocked->updated = time();
            $blocked->deleted = 0;
            $blocked->idContacts = [];            
            $blocked->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
            $blocked->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
            if (!$blocked->save()) {
                throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
            } 
      }
      unset($blockedContact);
    }
  }

  public function setBlockedContact($data, $blockedContact, $idAccount, $email, $phone) {
    $arrayIdCxcl = array();
    $contact = array();
    foreach ($blockedContact as $bqc) {
      $contact = (object) ["idContact" => $bqc->idContact, "email" => $bqc->email, "phone" => $bqc->phone, "indicative" => $bqc->indicative];
      if($bqc->idAccount == $idAccount && $bqc->deleted == 0){
        //$contactsegment = \Sxc::findFirst([["idContact" => $bqc->idContact]]);
        $contactsegment = \Sxc::find([["idContact" => $bqc->idContact]]);
         
        $bqc->blocked = time();
        $blockedEmail = "";
        $blockedPhone = "";
        //  
        if ($email == "email" && $phone == "phone") {
          $bqc->blockedEmail = time();
          $bqc->blockedPhone = time();
          $blockedEmail = time();
          $blockedPhone = time();
        } else if ($email == "email" && $phone != "phone") {
          $bqc->blockedEmail = time();
          $blockedEmail = time();
          $contact->phone = "";
          $contact->indicative = "";
        } else if ($email != "email" && $phone == "phone") {
          $bqc->blockedPhone = time();
          $blockedPhone = time();
          $contact->email = "";
        }
        
        if($contactsegment != false){
            foreach( $contactsegment as $value){
                $value->blocked = time();
                $value->save();
            }   
        }
        //var_dump($bqc); 
        $this->findBlockedContact($data, $contact, $idAccount);
        $bqc->save();
        
        $arrayIdCxcl[] = $bqc->idContact;
      }
      $bqc->save();
      $arrayIdCxcl[] = $bqc->idContact;
    } 
    $arrayData = implode(",", $arrayIdCxcl);
    $builder = $this->modelsManager->createBuilder()
      ->columns('Cxcl.idCxcl')
      ->from('Cxcl')
      ->join('Contactlist', 'Contactlist.idContactlist = Cxcl.idContactlist')
      ->join('Subaccount', 'Subaccount.idSubaccount = Contactlist.idSubaccount')
      ->where("Subaccount.idAccount = {$idAccount} AND Cxcl.idContact IN ({$arrayData}) AND Cxcl.deleted = 0" )
      ->getQuery()
      ->execute();
    unset($idAccount);
    unset($bqc);
    foreach ($builder as $cxcl){   
      $cxcls = \Cxcl::findFirst(array("conditions" => "idCxcl = {$cxcl->idCxcl} AND deleted = 0"));
      $cxcls->status = 'blocked';
      $cxcls->blocked = time();
      $cxcls->save();
      $this->setAccountants($cxcls->idContactlist);
    }
    unset($builder);
  }

  public function setAccountants($idContactlist) {
    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0 and deleted = 0", "bind" => array(0 => $idContactlist)));
    if ($contactlist) {
      $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?1 AND deleted = 0", "bind" => array(1 => $contactlist->idContactlist)));
      $contactlist->cactive = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "active", 1 => $contactlist->idContactlist)));
      $contactlist->cunsubscribed = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "unsubscribed", 1 => $contactlist->idContactlist)));
      $contactlist->cbounced = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "bounced", 1 => $contactlist->idContactlist)));
      $contactlist->cblocked = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "blocked", 1 => $contactlist->idContactlist)));
      $contactlist->cspam = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "spam", 1 => $contactlist->idContactlist)));
      $contactlist->save();
    }
  }
  public function setAllAccountants() {
    $contactlists = \Contactlist::findFirst(array("conditions" => "deleted = ?0", "bind" => array(0 => 0)));
    if ($contactlists) {
      foreach ($contactlists as $contactlist){
      $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?1 AND deleted = 0", "bind" => array(1 => $contactlist->idContactlist)));
      $contactlist->cactive = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "active", 1 => $contactlist->idContactlist)));
      $contactlist->cunsubscribed = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "unsubscribed", 1 => $contactlist->idContactlist)));
      $contactlist->cbounced = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "bounced", 1 => $contactlist->idContactlist)));
      $contactlist->cblocked = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "blocked", 1 => $contactlist->idContactlist)));
      $contactlist->cspam = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "spam", 1 => $contactlist->idContactlist)));
      $contactlist->save();
      }
    }
  }

  public function setUnblockedContact($idContact) {

    $cxcls = \Cxcl::find(array("conditions" => "idContact = ?0 and deleted = 0", "bind" => array(0 => $idContact)));
    if ($cxcls) {
      foreach ($cxcls as $cxcl) {
        $cxcl->blocked = 0;
        if($cxcl->bounced > 0){
          $cxcl->status = "bounced";
        } else if($cxcl->spam > 0){
          $cxcl->status = "spam";
        } else if($cxcl->unsubscribed > 0){
          $cxcl->status = "unsubscribed";
        } else {
          $cxcl->status = "active";
        }
        if (!$cxcl->save()) {
          \Phalcon\DI::getDefault()->get('db')->rollback();
          foreach ($cxcl->getMessages() as $message) {
            $this->trace("fail", "No se pudo bloquear el contacto, contacte con el administrador");
          }
        }
        $this->setAccountants($cxcl->idContactlist);
      }
    }
  }

  public function deleteBlocked($idBlocked) {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
    $blocked = \Blocked::findFirst([["idBlocked" => (int) $idBlocked, "idAccount" => (int) $idAccount, "deleted" => 0]]);
  
    if (count($blocked->idContacts) > 0) {
      foreach ($blocked->idContacts as $idContact) {
        $contact = \Contact::findFirst([["idContact" => $idContact, "idAccount" => (string) $idAccount, "deleted" => 0]]);
        $contactsegment = \Sxc::find([["idContact" => $idContact]]);
        
        if ($blocked->email != "" && $blocked->phone != "" && $blocked->indicative != ""){
          $this->setUnblockedContact($contact->idContact);
          $contact->blocked = 0;
          $contact->blockedEmail = "";
          $contact->blockedPhone = "";
          $contact->save();
          unset($contact);
        } else if ($blocked->email != ""){
          if($contact->blockedPhone == "" ||  $contact->blockedPhone == 0){
            $this->setUnblockedContact($contact->idContact);
            $contact->blocked = 0;
          } 
          $contact->blockedEmail = "";
          $contact->save();
          unset($contact);
        } else if ($blocked->phone != "" && $blocked->indicative != ""){
          if($contact->blockedEmail == "" ||  $contact->blockedEmail == 0){
            $this->setUnblockedContact($contact->idContact);
            $contact->blocked = 0;
          } 
          $contact->blockedPhone = "";
          $contact->save();
          unset($contact);
        }
        
        if($contactsegment != false){
            foreach( $contactsegment as $value){
                $value->blocked = 0;
                $value->save();
            }   
        }
      }
    }

    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
    $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
    $customLogger->idBlocked = $blocked->idBlocked;
    $customLogger->email = $blocked->email != "" ? $blocked->email : "";
    $customLogger->phone = $blocked->phone != "" ? $blocked->phone : "";
    $customLogger->indicative = $blocked->indicative != "" ? $blocked->indicative : "";
    $customLogger->typeName = "des-blockedMethod";
    $customLogger->detailedLogDescription = "Se ha desbloqueado el contacto.";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    unset($customLogger);


    $blocked->deleted = time();
    if (!$blocked->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($blocked->getMessages() as $message) {
        $this->trace("fail", "No se pudo Desbloquear el contacto, contacte con el administrador");
      }
    }
  }
  
  public function findBlockedContact($data, $bqc, $idAccount){
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount;
    $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
    //INICIO Para indicar el bloqueado en coleccion Blocked (exista o no)
    $blocked = \Blocked::findFirst([["email" => $bqc->email, "phone" => $bqc->phone, "indicative" => $bqc->indicative, "idAccount" => (int) $idAccount, "deleted" => 0]]);
    if ($blocked == false) {
      $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
      $blocked = new \Blocked();
      $blocked->idBlocked = $nextIdBlocked;
      $blocked->email = $bqc->email;
      $blocked->phone = $bqc->phone;
      $blocked->indicative = $bqc->indicative;
      $blocked->idAccount = (int)$idAccount;
      $blocked->blocked = time();
      $blocked->idContacts[] = (int) $bqc->idContact;
      unset($blocked->field);
      ((isset($data->motive)) ? $blocked->motive = $data->motive : "");
      if (!$blocked->save()) {
        throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
      }
      $msg = "Se ha Registrado un Contacto bloqueado";
    } else {
      if(!in_array((int) $bqc->idContact, $blocked->idContacts)){
        $blocked->idContacts[] = (int) $bqc->idContact;
      }
      $blocked->blocked = time();
      unset($blocked->field);
      ((isset($data->motive)) ? $blocked->motive = $data->motive : "");
      if (!$blocked->save()) {
        throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
      }
      $msg = "Se ha Actualizado el Contacto bloqueado";
    }
    //FIN
    $customLogger = new \Logs();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idAccount = $idAccount;
    $customLogger->idSubaccount = $idSubaccount;
    $customLogger->idBlocked = $blocked->idBlocked;
    $customLogger->email = $bqc->email;
    $customLogger->phone = $bqc->phone;
    $customLogger->indicative = $bqc->indicative;
    $customLogger->typeName = "blockedMethod";
    $customLogger->detailedLogDescription = $msg;
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    unset($customLogger);
  }

}
