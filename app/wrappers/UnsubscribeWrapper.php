<?php

namespace Sigmamovil\Wrapper;

class UnsubscribeWrapper extends \BaseWrapper {

  private $idContactlist;
  public $data;
  public $totals;
  
  public $contact;
  public $modelsManager;
  public $idCategoriesUnsub = array();
  public $idCategoriesSub = array();
  
  public function getContact($idContact, $idMail) {
    $returnArray = array();
    $where = array("idContact" => (int) $idContact);
    $validateView = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);    
    $contact = \Contact::findFirst([$where]);
    if (!$contact) {
      throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    
    $contactList = \Cxcl::find(array("conditions" => "idContact = ?0 AND deleted = 0", "bind" => array($idContact)));
    
    if (!$contactList) {
      throw new \InvalidArgumentException("El usuario no se encuentra registrado en ninguna lista de contactos");
    }
    $target = $validateView->target;
    $p = json_decode($target);
    $idcl = array();
    $idsg = array();
    if (isset($p->contactlists)) {
      for ($index = 0; $index < count($p->contactlists); $index++) {
        $idcl[]= $p->contactlists[$index]->idContactlist;
      }
    } else if (isset($p->segment)) {
      for ($index = 0; $index < count($p->segment); $index++) {
        $idsg[]= $p->segment[$index]->idSegment;
      }
    } 

    $auxArrCategory = array();
    $subscribedCategories1 = array();
    $unsubscribedCategories1 = array();

    $sql = "SELECT cxcl.idContactlist,"
		." cc.idContactlistCategory, cc.name,"
		." cc.description,	cxcl.active,"
		." cxcl.status, cxcl.deleted"
		." FROM cxcl"
	." LEFT JOIN contactlist AS cl"
		." ON cl.idcontactlist = cxcl.idContactlist"
	." LEFT JOIN contactlist_category AS cc"
		." ON cc.idContactlistCategory = cl.idContactlistCategory"
	." WHERE cxcl.idContact = ".$idContact
		." AND cxcl.deleted = 0"
		." AND cl.idSubaccount = ".(int)$validateView->idSubaccount;
    $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);

    if(!empty($c) || count($c)>0){
        foreach ($c as $value) {
          if (!array_search($value['idContactlistCategory'], $auxArrCategory)) {
            if ($value['active'] > 0 && $value['status'] == 'active') {
              array_push($subscribedCategories1, array("idContactlistCategory" => $value['idContactlistCategory'], "name" => $value['name'], "description" =>$value['description']));
            } else {
              array_push($unsubscribedCategories1, array("idContactlistCategory" => $value['idContactlistCategory'], "name" => $value['name'], "description" =>$value['description']));
            }
          }
        }   
    } 

    $returnArray["arrSubscribedCategories"] = array();
    $returnArray["arrUnsubscribedCategories"] = array();
    $returnArray["validateView"] = $validateView->typeUnsuscribed;   
    $returnArray["arrSubscribedCategories"] = array_values(array_unique($subscribedCategories1, SORT_REGULAR));
    $returnArray["arrUnsubscribedCategories"] = array_values(array_unique($unsubscribedCategories1, SORT_REGULAR));      
    $returnArray["contact"] = $contact;

    $unsubscribe = \Unsubscribed::findFirst([
      "conditions" => "idContact = ?0 AND idMail = ?1", 
      "bind" => array($idContact, $idMail)
    ]);
    $returnArray["message"] = "";
    if($unsubscribe){
      $returnArray["message"] = "Su desuscripción ya se encuentra registrada";
    }    
    unset($c);
    unset($sql);
    unset($validateView);
    unset($unsubscribe);
    unset($unsubscribe);
    unset($contact);
    unset($contactList);
    return $returnArray;
  }

  public function unsubcribeContact($infoUnsubscribed, $idContact) {
    $idMail = $infoUnsubscribed->idMail;
    //Logs de insunsubscribeAction
    //$logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $idMail;
    $customLogger->idContact = $idContact;
    $customLogger->typeName = "insunsubscribeMethod";
    //$customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
    //$customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    //
    $arrSubs = $infoUnsubscribed->arrSubs;
    $arrUnsubs = $infoUnsubscribed->arrUnsubs;
    
    $where = array("idContact" => (int) $idContact);

    $contact = \Contact::findFirst([$where]);
    unset($where);
    if (!$contact) {
      throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    unset($contact);
    $mxc = \Mxc::findFirst([["idContact" => (int) $idContact, "idMail" => (string) $idMail]]);
    if (!$mxc) {
      throw new \InvalidArgumentException("No hay correo para este contacto");
    }
    if ($mxc->unsubscribed == 0) {
      $mxc->unsubscribed = time();
      $mxc->save();
    }
    unset($mxc);
    //Para desuscribir

    if($arrUnsubs[0]->idContactlistCategory != NULL){
    	$contactlistUnsubs = $this->findContactlists($arrUnsubs);
	    $this->findIdcontactlistUnsubscribed($contactlistUnsubs,$idContact);
	    unset($contactlistUnsubs);
    }
    //Fin de desuscribir    
    //Para inscribir
    if($arrSubs[0]->idContactlistCategory != NULL){
	    $contactlistSubs = $this->findContactlists($arrSubs);
	    $this->findIdcontactlistSubscribed($contactlistSubs,$idContact);
	    unset($contactlistSubs);
	}
    //Fin de inscribir
    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);

    if (isset($mail->idAutomaticCampaign)) {
    	var_export("Pasa Por Aqui");
      $automaticcampsteps = \AutomaticCampaignStep::find(array(//Resto de pasos que se van a cancelar para este contacto
                  "conditions" => "idAutomaticCampaign = ?0 AND idContact = ?1 AND status = ?2",
                  "bind" => array($mail->idAutomaticCampaign, $idContact, "scheduled")
      ));
      unset($idContact);
      if (count($automaticcampsteps) > 0) {
        foreach ($automaticcampsteps as $value) {
          $automaticcampstepx = new \AutomaticCampaignStep();
          $automaticcampstepx = $value;
          $automaticcampstepx->status = "canceled";
          $automaticcampstepx->statusSms = "canceled";
          $automaticcampstepx->save();
        }
        unset($automaticcampsteps);
      }
    }
    unset($mail);
    //$sql = "CALL updateCountersGlobal()";
    //$this->db->execute($sql);
    /* $sql = "CALL updateCountersAccount({$idAccount})";
      $this->db->fetchAll($sql); */
  }

  public function insUnsubscribe($data, $idContact) {

    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
    $this->getIdContaclist($mail);
//    $mxc->
    $returnArray = array();
    $where = array("idContact" => $idContact);
    $contact = \Contact::findFirst($where);
    if (!$contact) {
      throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    $contactListUpdate = $data->unsubscribe;

//      var_dump($key);
//    }
//    exit;
    for ($i = 0; $i < count($contactListUpdate); $i++) {
      foreach ($this->idContactlist as $key) {
        if ($contactListUpdate[$i]->idContactlist == $key) {
          $mxc = \Mxc::findFirst([["idContact" => (int) $idContact, "idMail" => (string) $data->idMail]]);
          if ($mxc->unsubscribed == 0) {
            $mxc->unsubscribed = time();
          } else {
            $mxc->unsubscribed = "0";
          }
          $mxc->save();
        }
      }

      $contactList = \Cxcl::findFirst(array("conditions" => "idContact = ?0 and idContactlist = ?1", "bind" => array($idContact, $contactListUpdate[$i]->idContactlist)));
      $contactlistDes = \Contactlist::findFirst(["conditions" => "idContactlist = ?0", "bind" => [0 => $contactListUpdate[$i]->idContactlist]]);
      if (!$contactList) {
        throw new \InvalidArgumentException("Ocurrio un problema buscando el contacto en la lista de contacto.");
      }
      if ($contactListUpdate[$i]->unsubscribe) {
//        $contactList->unsubscribed = 0;
//        $contactlistDes->cactive +=1;
//        $contactlistDes->cunsubscribed -=1;
        $cxcl->active = time();
        $cxcl->unsubscribed = 0;
        if (!($cxcl->status == 'blocked' or $cxcl->status == 'spam' or $cxcl->status == 'bounced')) {
          $cxcl->status = 'active';
        }
        $this->setAccountants($cxcl->idContactlist);
      } else {
//        $contactlistDes->cactive -=1;
//        $contactlistDes->cunsubscribed +=1;
//        $contactList->unsubscribed = time();
        $cxcl->unsubscribed = time();
        $cxcl->active = 0;
        if (!($cxcl->status == 'blocked' or $cxcl->status == 'spam' or $cxcl->status == 'bounced')) {
          $cxcl->status = 'unsubscribed';
        }
        $this->setAccountants($cxcl->idContactlist);
      }
      if (!$contactList->save()) {
        foreach ($contactList->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      if (!$contactlistDes->save()) {
        foreach ($contactlist->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
//    }
    $returnArray = array("message" => "Se ha desuscrito de las listas seleccionadas exitosamente");
    return $returnArray;
  }

  public function getIdContaclist($mail) {
    $target = json_decode($mail->target);
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
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
      $segment = \Segment::findFirst([["idSegment" => $key->idSegment]]);
      foreach ($segment->contactlist as $k) {
        $this->idContactlist[] = $k["idContactlist"];
      }
      unset($segment);
    }
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

  public function getContactlistForSegment($arrIds) {

    $arrid = explode(",", $arrIds);
    $whereids = array();
    foreach ($arrid as $key) {
      $whereids[] = $key;
    }
    $where = array("idSegment" => array('$in' => $whereids));
    $segment = \Segment::find($where);
    $arrIdContactList = array();

    foreach ($segment as $key => $val) {
      foreach ($val->contactlist as $key) {
        $arrIdContactList[] = (Int) $key['idContactlist'];
      }
    }

    $arrIdContactList = array_unique($arrIdContactList);


    return $arrIdContactList;
  }

  public function unsubcribeContactAutomaticCampaign($infoUnsubscribed, $idContact) {
    $idStep = $infoUnsubscribed->idMail;

    $arrSubs = $infoUnsubscribed->arrSubs;
    $arrUnsubs = $infoUnsubscribed->arrUnsubs;
    $where = array("idContact" => (int) $idContact);

    $contact = \Contact::findFirst([$where]);
    unset($where);
    if (!$contact) {
      throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    unset($contact);
    //Para desuscribir
    $contactlistUnsubs = $this->findContactlists($arrUnsubs);
    $this->findIdcontactlistUnsubscribed($contactlistUnsubs,$idContact);
    unset($contactlistUnsubs);
    //Fin de desuscribir
    //Para inscribir
    $contactlistSubs = $this->findContactlists($arrSubs);
    $this->findIdcontactlistSubscribed($contactlistSubs,$idContact);
    unset($contactlistSubs);
    //Fin de inscribir
    
    //Inicio cancelación de camapaña automática
    $automaticcampstep = \AutomaticCampaignStep::findFirst(array(//automatic campaig step
                "conditions" => "idAutomaticCampaignStep = ?0",
                "bind" => array($idStep)
    ));

    $automaticcampstep->status = "unsubscribed";
    $automaticcampstep->statusSms = "canceled";
    $automaticcampstep->unsubscribed = time();
    $automaticcampstep->save();

    $automaticcampsteps = \AutomaticCampaignStep::find(array(//Resto de pasos que se van a cancelar para este contacto
                "conditions" => "idAutomaticCampaign = ?0 AND idContact = ?1 AND status = ?2",
                "bind" => array($automaticcampstep->idAutomaticCampaign, $idContact, "scheduled")
    ));
    unset($idContact);
    if (count($automaticcampsteps) > 0) {
      foreach ($automaticcampsteps as $value) {
        $automaticcampstepx = new \AutomaticCampaignStep();
        $automaticcampstepx = $value;
        $automaticcampstepx->status = "canceled";
        $automaticcampstepx->statusSms = "canceled";
        $automaticcampstepx->save();
      }
      unset($automaticcampstep);
    }

    //Fin de cancelación de los pasos de campaña automatica

    $idAccount = $automaticcampstep->AutomaticCampaign->Subaccount->Account->idAccount;

    $sql = "CALL updateCountersGlobal()";
    $this->db->execute($sql);
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);
    unset($idAccount);
    return array("message" => "La información de suscripción de categorías fue actualizada exitosamente.");
  }

  public function unsubcribeContactSimple($idMail, $idContact) {
    //Logs de insunsubscribeAction
    //$logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $idMail;
    $customLogger->idContact = $idContact;
    $customLogger->typeName = "insunsubscribesimpleMethod";
    //$customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
    //$customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    //
    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);
    $this->getIdContaclist($mail);
    $returnArray = array();
    $where = array("idContact" => $idContact);
    $contact = \Contact::findFirst($where);
    if (!$contact) {
      throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    
    $mxc = \Mxc::findFirst([["idContact" => (int) $idContact, "idMail" => (string) $idMail]]);
    $mxc->unsubscribed = time();
    $mxc->save();
    
    foreach ($this->idContactlist as $key) {
      $cxcl = \Cxcl::findFirst(array("conditions" => "idContact = ?0 and idContactlist = ?1", "bind" => array($idContact, $key)));
      $cxcl->unsubscribed = time();
      $cxcl->active = 0;
      if (!($cxcl->status == 'blocked' or $cxcl->status == 'spam' or $cxcl->status == 'bounced')) {
        $cxcl->status = 'unsubscribed';
      }
      $cxcl->save();
    }
    
    $this->setAccountants($cxcl->idContactlist);

    $returnArray = array("message" => "Se ha desuscrito de las listas seleccionadas exitosamente");
    return $returnArray;
  }
  
  public function findContactlists($arr){
    $contIdContacList = 0;
    $strArrIdContacList = array(); 
    foreach ($arr as $cat) {
      if (isset($cat->idContactlistCategory)) {
        $contactlists = \Contactlist::find(array("conditions" => "idContactlistCategory = {$cat->idContactlistCategory}"));
            foreach ($contactlists as $contactlist) {
                $strArrIdContacList[] = $contactlist->idContactlist;
                $contIdContacList++;
            }    
        }    
      }

    $dataIdContacList = implode(",", $strArrIdContacList);
    
    unset($strArrIdContacList);
    if ($contIdContacList == 0) {
      $dataIdContacList = "NULL";
    }
    unset($contIdContacList);
    return $dataIdContacList;
  }
  
  public function findIdcontactlistUnsubscribed($strArrIdContacList,$idsContact,$data, $idSubaccount){

    $arrayIdC = implode(",", $idsContact);
    foreach($idsContact as $id){        
        $query = "SELECT idCategories, idUnsubscribed FROM unsubscribed WHERE idContact = {$id}"
                ." AND deleted = 0 AND unsubscribed.option = 'categorie' AND idSubaccount = ".(int)$idSubaccount;
        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);                      
        if(count($c)>0){                    
            $idUnsubscribed = array();  
            $idCat = array();         
            foreach ($c as $value) {
                $idUnsubscribed[] = (int) $value['idUnsubscribed'];
                $idCat =  explode(',',$value['idCategories']);
            }           
            foreach ($idCat as $key => $value) {                
                foreach ($this->idCategoriesUnsub as $key1 => $value1) {                    
                    if((int)$value1!== (int)$value){
                      $idCat[] = $value1;
                      break;
                    }                    
                }            
            }
            $query = "UPDATE unsubscribed SET updated = ".time()
                    .", idCategories = '". implode(',', array_values(array_unique($idCat, SORT_REGULAR)))
                    ."', created = ".time()
                    ." WHERE idUnsubscribed IN (". implode(",", $idUnsubscribed).")";  
            $this->db->execute($query); 
        }else{
            $contact = \Contact::findFirst([["idContact" => (int) $id,"deleted" => 0]]);
            $unsubscribed = new \Unsubscribed();
            $unsubscribed->idMail = $data->idMail;
            $unsubscribed->idContact = $id;
            $unsubscribed->motive = $data->option;
            $unsubscribed->option = "categorie";
            $unsubscribed->other = $data->other;
            $unsubscribed->idSubaccount = $idSubaccount;
            $unsubscribed->email = $contact->email;
            $unsubscribed->idCategories = implode(",",$this->idCategoriesUnsub);
            $unsubscribed->createdBy = $contact->email;
            $unsubscribed->updatedBy = $contact->email;
            if (!$unsubscribed->save()) {
             foreach ($unsubscribed->getMessages() as $msg) {
                 throw new \InvalidArgumentException($msg);
             }
            }    
            unset($contact);
        }               
    }
    $Cxcl = \Cxcl::find(["conditions" => "idContact IN ({$arrayIdC}) AND idContactlist IN ({$strArrIdContacList}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"]);

    if($Cxcl){
        foreach ($Cxcl as $value){            
            $value->unsubscribed = (int) time();
            $value->status = 'unsubscribed';
            $value->updated = (int) time();
            if (!$value->update()) {
                foreach ($value->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }            
        }
    } 
  }
  
  public function findIdcontactlistSubscribed($strArrIdContacList,$idsContact,$idSubaccount){    
    $arrayIdC = implode(",", $idsContact);
    foreach($idsContact as $id){        
        $query = "SELECT idCategories, idUnsubscribed FROM unsubscribed WHERE idContact = {$id}"
                ." AND deleted = 0 AND unsubscribed.option = 'categorie' AND idSubaccount = ".(int)$idSubaccount;
        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);                      
        if(count($c)>0){                    
            $idUnsubscribed = array();  
            $idCat = array();         
            foreach ($c as $value) {
                $idUnsubscribed[] = (int) $value['idUnsubscribed'];
                $idCat =  explode(',',$value['idCategories']);
            } 
            foreach ($idCat as $key => $value) {                
                foreach ($this->idCategoriesSub as $key1 => $value1) {                    
                    if((int)$value1== (int)$value){
                      unset($idCat[$key]);
                      break;
                    }                    
                }            
            }
            if(count($idCat)>0){
                $query = "UPDATE unsubscribed SET updated = ".time()
                    .", idCategories = '". implode(',', array_values(array_unique($idCat, SORT_REGULAR)))
                    ."', created = ".time()
                    ." WHERE idUnsubscribed IN (". implode(",", $idUnsubscribed).")";  
                $this->db->execute($query);   
            }else{
                $query = "UPDATE unsubscribed SET updated = ".time()
                    .", idCategories = NULL, deleted = ".time()
                    .", created = ".time()
                    ." WHERE idUnsubscribed IN (". implode(",", $idUnsubscribed).")";  
                $this->db->execute($query);       
            } 
        }             
    }
    $Cxcl = \Cxcl::find(["conditions" => "idContact IN ({$arrayIdC}) AND idContactlist IN ({$strArrIdContacList}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"]);

    unset($strArrIdContacList);
    unset($idsContact);
    if ($Cxcl) {
      foreach ($Cxcl as $key) {
        $key->unsubscribed = 0;
        $key->active = time();
        $key->status = "active";
        $key->save(); 
      }
      unset($Cxcl);
    }
  }
  
  public function unsubcribeAllContact($data, $idContact){
    if ($data->option == "Otro" && $data->other == "") {
      throw new \InvalidArgumentException("Debe de llenar la información faltante.");
    }
    if ($data->click == "" && $data->typeView == 0) {
      throw new \InvalidArgumentException("Debe de seleccionar una de las 2 opciones.");
    }
    //
    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $data->idMail;
    $customLogger->idContact = $idContact;
    $customLogger->typeName = "unsubscribeMethod";
    $customLogger->created = time();
    $customLogger->updated = time();
    if (!$customLogger->save()) {
      foreach ($customLogger->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    if ($data->typeView == 0) {
        $mxc = \Mxc::findFirst([["idContact" => (int) $idContact, "idMail" => (string) $data->idMail]]);
        $mail = \Mail::findFirst([
            "conditions" => "idMail = ?0 ",
            "columns" => "idSubaccount",
            "bind" => array(0 => $data->idMail)
        ]);
        $contact2 = \Contact::findFirst([["idContact" => (int) $idContact,"deleted" => 0]]);
        $unsubscribed = new \Unsubscribed();
        $unsubscribed->idMail = $data->idMail;
        $unsubscribed->idContact = $idContact;
        $unsubscribed->motive = $data->option;
        $unsubscribed->option = $data->click;
        $unsubscribed->other = $data->other;
        $unsubscribed->idSubaccount = $mail->idSubaccount;
        $unsubscribed->email = $contact2->email;
        $unsubscribed->createdBy = $contact2->email;
        $unsubscribed->updatedBy = $contact2->email;
        if (!$unsubscribed->save()) {
            foreach ($unsubscribed->getMessages() as $msg) {
                throw new \InvalidArgumentException($msg);
            }
        }
        //
        unset($mail);
        if (!$mxc) {
            throw new \InvalidArgumentException("No hay correo para este contacto");
        }
        if ($mxc->unsubscribed == 0) {
            $mxc->unsubscribed = time();
            $mxc->save();
        }
        unset($mxc);
        // 
    if($unsubscribed->option == "one"){
            if (!filter_var($data->contact->email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
            }
            $mail = \Mail::findFirst([
                        "conditions" => "idMail = ?0 ",
                        "columns" => "target",
                        "bind" => array(0 => $data->idMail)
            ]);
            // Consulta los idContactlist de la campaña de Mail
            $this->getIdContaclist($mail);
            //BUSCAMOS EN SXC CON EL ID DEL SEGMENTO---------------------------------------
            $idSegment=0;
            $target = json_decode($mail->target);
            $listSegment = $target->segment;
            foreach ($listSegment as $key) {
              $segment = \Segment::findFirst([["idSegment" => $key->idSegment]]);
              $idSegment = $segment->idSegment;
              unset($segment);
            }
            
            $contactsegment = \Sxc::findFirst([["idSegment" => $idSegment]]);
            $contactsegment->unsubscribed = time();
            if (!$contactsegment->save()) {
                throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
            }
            unset($mail);
            // Consulta lista de contactos en la cual se hizo el envio
            $where = ['email' => (string) $data->contact->email, 'idAccount' => (string) $data->contact->idAccount, 'deleted' => 0];
            unset($data);
            $contact = \Contact::find([$where]);
            $arrayIdCxcl = array();
            foreach ($contact as $value) {
                $arrayIdCxcl[] = $value->idContact;
            }
            unset($contact);
            //Separo por coma los idContact
            $arrayIdCxcl = implode(",", $arrayIdCxcl);
            //Separo por coma los idContactlist
            $idContactlist = implode(",", $this->idContactlist);
            //
            $cxcl = \Cxcl::find([
                        "conditions" => "idContact IN ({$arrayIdCxcl}) AND idContactlist IN ({$idContactlist}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"
            ]);
            if($cxcl != false){
                foreach ($cxcl as $value){
                    //
                    $value->unsubscribed = (int) time();
                    $value->status = 'unsubscribed';
                    if (!$value->update()) {
                        foreach ($value->getMessages() as $msg) {
                            throw new \InvalidArgumentException($msg);
                        }
                    }
                    //
                    $cxclxun = new \Cxclxun();
                    $cxclxun->idCxcl = $value->idCxcl;
                    $cxclxun->idUnsubscribed = $unsubscribed->idUnsubscribed;
                    if (!$cxclxun->save()) {
                        foreach ($cxclxun->getMessages() as $msg) {
                            throw new \InvalidArgumentException($msg);
                        }
                    }
                }
                unset($cxcl);
            }
        }
        // 
    if($unsubscribed->option == "all"){
            if (!filter_var($data->contact->email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
            }
            $mail = \Mail::findFirst([
              "conditions" => "idMail = ?0 ",
              "columns" => "target",
              "bind" => array(0 => $data->idMail)
            ]);
            //BUSCAMOS EN SXC CON EL ID DEL SEGMENTO---------------------------------------
            $idSegment=0;
            $target = json_decode($mail->target);
            $listSegment = $target->segment;
            foreach ($listSegment as $key) {
              $segment = \Segment::find([["idSegment" => $key->idSegment]]);
              foreach ($segment as $value) {
                $contactsegment = \Sxc::findFirst([["idSegment" => $value->idSegment]]);
                $contactsegment->unsubscribed = time();
                if (!$contactsegment->save()) {
                  foreach ($cxclxun->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                  }
                }
                unset($contactsegment);
              }
              unset($segment);
            }

            unset($mail);
            $where = ['email' => (string) $data->contact->email, 'idAccount' => (string) $data->contact->idAccount, 'deleted' => 0];
            $contact = \Contact::find([$where]);
            $arrayIdCxcl = array();
            foreach ($contact as $value) {
                $arrayIdCxcl[] = (int) $value->idContact;
            }
            //Separo por coma los idContactlist
            $arrayIdCxcl = implode(",", $arrayIdCxcl);
            $cxcl = \Cxcl::find([
                        "conditions" => "idContact IN ({$arrayIdCxcl}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"
            ]);
      foreach ($cxcl as $value){
                //
                $value->unsubscribed = (int) time();
                $value->status = 'unsubscribed';
                if (!$value->update()) {
                    foreach ($value->getMessages() as $msg) {
                        throw new \InvalidArgumentException($msg);
                    }
                }
                //
                $cxclxun = new \Cxclxun();
                $cxclxun->idCxcl = $value->idCxcl;
                $cxclxun->idUnsubscribed = $unsubscribed->idUnsubscribed;
                if (!$cxclxun->save()) {
                    foreach ($cxclxun->getMessages() as $msg) {
                        throw new \InvalidArgumentException($msg);
                    }
                }
            }
        }
    }else if($data->typeView == 1 ){
        $mail = \Mail::findFirst([
            "conditions" => "idMail = ?0 ",
            "columns" => "idSubaccount",
            "bind" => array(0 => $data->idMail)
        ]);
        $this->unsuscribedCategories($data,$idContact,$mail->idSubaccount);      
        unset($mail);  
    }
  }
  
  public function unsuscribedCategories($data,$idContact,$idsubaccount){
   
    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $data->idMail;
    $customLogger->idContact = $idContact;
    $customLogger->typeName = "unsubscribeMethod";
    $customLogger->created = time();
    $customLogger->updated = time();
    if (!$customLogger->save()) {
      foreach ($customLogger->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    
    $arrSubs = $data->subscribe;
    $arrUnsubs = $data->unsubscribe;
	$mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => (string)$data->idMail]]);
    $contact = \Contact::find([['email' => (string) $data->contact->email, 'idAccount' => (string) $mail->Subaccount->idAccount, 'deleted' => 0]]);
    $idsContact = array();
    foreach ($contact as $value) {
        $idsContact[] = $value->idContact;
    }
    unset($contact);
    
    $mxc = \Mxc::find([["idContact" => ['$in' => $idsContact], "idMail" => (string) $data->idMail]]);
    
    if (!$mxc) {
      throw new \InvalidArgumentException("No hay campañas enviadas para este contacto");
    }
    
    foreach ($mxc as $value){
        if ($value->unsubscribed == 0) {
          $value->unsubscribed = time();
          $value->updated = time();
          if (!$value->save()) {
            foreach ($value->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
        }   
    }
    unset($mxc);
    

    //Para desuscribir
    if(!empty($arrUnsubs) || count($arrUnsubs) > 0){
         $contactlistUnsubs = $this->findContactlistCategorie($arrUnsubs,1,$mail->idSubaccount);
         $this->findIdcontactlistUnsubscribed($contactlistUnsubs,$idsContact,$data,$mail->idSubaccount);
         unset($contactlistUnsubs);
    }
    //Fin de desuscribir    

    //Para suscribir
    if(!empty($arrSubs) || count($arrSubs) > 0){
         $contactlistSubs = $this->findContactlistCategorie($arrSubs,2,$mail->idSubaccount);
         $this->findIdcontactlistSubscribed($contactlistSubs,$idsContact,$mail->idSubaccount);
         unset($contactlistSubs);
     }
    //Fin de suscribir   
    unset($mail);
   
  }
  
    public function findContactlistCategorie($arr,$type,$idSubaaccount){
    $contIdContacList = 0;
    $strArrIdContacList = array(); 
    $dataIdContacList = "";
    
    foreach ($arr as $cat) {            
      if (isset($cat->idContactlistCategory)) {
        if($type == 1){
            $this->idCategoriesUnsub[] = $cat->idContactlistCategory;      
        }else{
            $this->idCategoriesSub[] = $cat->idContactlistCategory;      
        }
        
        $sql = "SELECT DISTINCT idContactlist  FROM contactlist "
             . " WHERE idSubaccount = ".(int) $idSubaaccount
             . " AND idContactlistCategory = ".$cat->idContactlistCategory
             . " AND deleted = 0";
        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);

        if(!empty($c) || count($c)>0){
            foreach ($c as $value) {
              $strArrIdContacList[] = (int) $value['idContactlist'];
              $contIdContacList++;
            }   
        } 
        unset($c);
      }
    }

    if($contIdContacList == 0){
        $dataIdContacList = "NULL";
    }else{
        $dataIdContacList = implode(",", $strArrIdContacList); 
    }
    unset($contIdContacList);

    return $dataIdContacList;
  }
  

  public function findContactsUnsuscribe($page, $data){
       
      (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
      $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
      $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
      if ($data->stringsearch != -1) {
          
        $string = trim($data->stringsearch);
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
          if($key->idSubaccount == (string)$idSubaccount && $key->deleted == 0){
            $ids .= $key->idContact . ",";
          }          
        }
        $ids = trim($ids, ',');
        $this->totals = 0;
        
        if (!empty($ids)) {
            $query = "SELECT DISTINCT cxcl.idContact"
              ." FROM cxcl"                
              ." INNER JOIN unsubscribed AS ub ON ub.idContact = cxcl.idContact"
              ." LEFT JOIN contactlist AS cl ON cl.idContactlist = cxcl.idContactlist"
              ." WHERE ub.option = 'categorie' AND ub.deleted = 0 AND cl.deleted = 0 AND ub.idCategories is not NULL"
              ." AND cxcl.idContact IN ({$ids})"
              ." AND cl.idSubaccount = ".$idSubaccount
              ." AND ub.idSubaccount = ".$idSubaccount; 
              
            $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);      
            $in = array();
            if(count($c)>0){
              foreach ($c as $value) {
                $in[] = (int) $value['idContact'];
              }    
            }
            
            $where = array("idContact" => ['$in' => $in], "deleted" => 0, 'idSubaccount' => (string) $idSubaccount, 'idAccount' => (string) $idAccount);
            $this->totals = \Contact::count([$where]);
            //$this->totals = count($this->totals);
            $this->data = \Contact::find(array($where, 'limit' => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, 'skip' => $page));
        }
      }else{
        $query = "SELECT DISTINCT cxcl.idContact"
              ." FROM cxcl"                
              ." INNER JOIN unsubscribed AS ub ON ub.idContact = cxcl.idContact"
              ." LEFT JOIN contactlist AS cl ON cl.idContactlist = cxcl.idContactlist"
              ." WHERE ub.option = 'categorie' AND ub.deleted = 0 AND cl.deleted = 0 AND ub.idCategories is not NULL"
              ." AND cl.idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
              ." AND ub.idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
 
        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);      
        $in = array();
        if(count($c)>0){
          foreach ($c as $value) {
            $in[] = (int) $value['idContact'];
          }    
        }

        $where = array("idContact" => ['$in' => $in], "deleted" => 0);
        $this->data = \Contact::find([$where,"limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, "skip" => $page, "sort" => ["idContact" => -1]]);

        $query2 = "SELECT count(DISTINCT cxcl.idContact) as total"
              ." FROM cxcl"                
              ." INNER JOIN unsubscribed AS ub ON ub.idContact = cxcl.idContact"
              ." LEFT JOIN contactlist AS cl ON cl.idContactlist = cxcl.idContactlist"
              ." WHERE ub.option = 'categorie' AND ub.deleted = 0 AND cl.deleted = 0 AND ub.idCategories is not NULL"
              ." AND cl.idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
              ." AND ub.idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
        $c1= \Phalcon\DI::getDefault()->get("db")->fetchAll($query2);
        $this->totals = $c1[0]["total"];
      }

      $this->modelData();
  }
  
  public function modelData(){
    $this->contact = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $key => $val) {
      $idContact = $val->idContact;
      unset($val->_id);
      unset($val->idSubaccount);
      unset($val->idAccount);
      unset($val->created);
      unset($val->createdBy);
      $val = json_encode($val);
      $val = json_decode($val, true);        
      
      $query1 = "SELECT idCategories from unsubscribed as ub"
             ." WHERE ub.OPTION = 'categorie'"
             ." AND idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
             ." AND deleted = 0"
             ." AND idContact = ".(int) $idContact;
      $result = $this->db->fetchAll($query1);
      $idsct = array();
      foreach($result as $key => $value){
        $idsct[] = $value['idCategories'];  
      }
      $idsct = array_values(array_unique($idsct, SORT_REGULAR));      
      $query = "SELECT DISTINCT name"
              ." FROM contactlist_category"
              ." WHERE idContactlistCategory IN (".implode(',',$idsct).")"
              ." AND deleted = 0";
         
      $val["contactlistcategories"]= (object) $this->db->fetchAll($query);      

      $sql = "SELECT REPLACE(GROUP_CONCAT(DISTINCT CASE ub.motive WHEN 'Otro' THEN ub.other ELSE ub.motive END ),',',', ') AS motive from unsubscribed as ub"
              ." WHERE ub.OPTION = 'categorie'"
              ." AND idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
              ." AND deleted = 0"
              ." AND idContact = ".(int) $idContact;
      $mt = $this->db->fetchAll($sql);
      $val["motive"] = $mt[0]['motive'];
      $sql1 = "SELECT created,createdBy from unsubscribed as ub"
              ." WHERE ub.OPTION = 'categorie'"
              ." AND idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
              ." AND deleted = 0"
              ." AND idContact = ".(int) $idContact;
      $mt1 = $this->db->fetchAll($sql1);
      $val["created"] = $mt1[0]['created'];
      $val["createdBy"] = $mt1[0]['createdBy'];
      array_push($arr, $val);      
    }
    array_push($this->contact, array("items" => $arr));
    unset($arr);   
  }
  
  public function getContactsUnsuscribe() {
    return $this->contact;
  }
  
  public function deleteUnsub($idContact){
    $contact = \Contact::findFirst([['idContact' => (int) $idContact,"idAccount" => (string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount, 'deleted' => 0]]);    
    if(!$contact){
        throw new \InvalidArgumentException("El contacto no se encuentra registrado");
    }
    $email = $contact->email;
    unset($contact);
    $contacts = \Contact::find([['email' => $email,"idAccount" => (string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount, 'deleted' => 0]]);

    $ids = array();
    foreach($contacts as $value){
       $ids[] = $value->idContact;
    }
    $idscontact = implode(',',$ids);
    $unsuscribe = \Unsubscribed::find(["conditions" => "idContact IN($idscontact) AND option = 'categorie' AND idSubaccount = ".(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount." AND deleted = 0"]);
    
    if($unsuscribe){        
        foreach ($unsuscribe as $value){
            $value->deleted = time();
            if (!$value->save()) {
              throw new \InvalidArgumentException("No se pudo desuscribir el contacto");
            }        
        }
    }
    $builder = $this->modelsManager->createBuilder()
      ->columns('Cxcl.idCxcl')
      ->from('Subaccount')
      ->join('Contactlist', 'Contactlist.idSubaccount = Subaccount.idSubaccount')
      ->join('Cxcl', 'Cxcl.idContactlist = Contactlist.idContactlist')
      ->where("Contactlist.deleted = 0 AND Subaccount.idAccount = ".\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount. " AND Subaccount.idSubaccount = ".(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount. " AND Cxcl.idContact IN (".$idscontact.")")      
      ->getQuery()
      ->execute();
    if(!empty($builder) || count($builder)>0){
        $in = array();
        for ($i = 0; $i < count($builder); $i++) {
          $in[$i] = (int) $builder[$i]->idCxcl;
        }

        $query = "UPDATE cxcl SET active = ".time()
                .", unsubscribed = 0, status = 'active'"
                ." WHERE idCxcl IN (". implode(",", $in).")"; 
        $this->db->query($query);   
    }    

    
  }
  
  public function getCategories(){
      $query = "SELECT DISTINCT	ct.name,ct.idContactlistCategory, "
              ." GROUP_CONCAT(cl.idContactlist) as contactlist"
              ." FROM contactlist as cl left join contactlist_category as ct"
              ." ON cl.idContactlistCategory = ct.idContactlistCategory"
              ." WHERE cl.idSubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
              ." AND cl.deleted = 0 GROUP BY ct.name";
     
      return $this->db->fetchAll($query);
  }
  
  public function createUnsub($data){
       //Validacion data
    if (!isset($data->email) || empty($data->email) ) {
      throw new \InvalidArgumentException("Debe ingresar el correo electrónico");
    }
    /*if (isset($data->phone) and !isset($data->phoneCode)) {
      throw new \InvalidArgumentException("Debe seleccionar el indicativo");
    } */
    if (!isset($data->motive)) {
      throw new \InvalidArgumentException("El motivo es un campo obligatorio");
    }
    if ((isset($data->motive) && strlen($data->motive) < 2) or ( isset($data->motive) && strlen($data->motive) > 100)) {
      throw new \InvalidArgumentException("El motivo debe contener entre 2 y 100 caracteres");
    }
       
       
       //Validacion para email lleno
      if(!empty($data->email)){
        $data->email = strtolower($data->email);
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
        }
        $email = (string) trim($data->email);
        $where = array("email" => $data->email, "deleted" => 0, "idSubaccount" => ['$in' => [(string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idSubaccount,(int)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idSubaccount]] ,
                     "idAccount" => (string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount);
        $contact = \Contact::find([$where]);        
        $ids = array();
        if($contact){
            $idscat = array();
            $idscl = array();

            foreach ($data->services as $value) {
                list($tmpidCt, $tmpidCl) = explode("-", $value);
                $idscat[] = $tmpidCt;
                $idscl[] = $tmpidCl;
                $tmpidCl = "";
                $tmpidCt = "";
            }
            
            foreach($contact as $value){
                $ids[] = $value->idContact;    
            }
            
            $sql = "SELECT distinct cl.idContactlistCategory"
                 ." FROM cxcl LEFT JOIN contactlist as cl"
                 ." ON cl.idContactlist = cxcl.idContactlist"
                 ." LEFT JOIN contactlist_category as ct"
                 ." ON ct.idContactlistCategory = cl.idContactlistCategory"
                 ." WHERE cl.deleted = 0 "
                 ." AND cxcl.idContact IN(".implode(',',$ids).")";
            $result = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);

            if(count($result)>0){
                $nofindCategories = array();
                $idsctsql = array();
                foreach ($result as $key1 => $value1) {
                    $idsctsql[]= $value1['idContactlistCategory'];                
                }
                unset($result);
                foreach ($idscat as $key => $value2) {
                    if(!in_array($value2, $idsctsql)){
                        $nofindCategories[] = $value2;
                        unset($idscat[$key]);
                    }   
                }
                unset($idsctsql);
                $idscat = array_values(array_unique($idscat, SORT_REGULAR));
                if(count($nofindCategories)>0){
                    $categories = \ContactlistCategory::find(array("conditions" => "idContactlistCategory IN(". implode(',', $nofindCategories).") AND deleted = 0"));
                    $name = array();
                    foreach ($categories as $vl) {
                        $name[] = $vl->name;
                    }
                    throw new \InvalidArgumentException("El correo ingresado no está asociado a las categorías: ".implode(', ', $name));    
                }                               
            }

            foreach($ids as $key => $val){                
               $query = "SELECT idCategories, idUnsubscribed FROM unsubscribed WHERE idContact = {$val}"
                        ." AND deleted = 0 AND unsubscribed.option = 'categorie' AND idSubaccount = ".(int)\Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
               $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);

               if(count($c)>0){                    
                    $idUnsubscribed = array();  
                    $idCat = array();         
                    foreach ($c as $value) {
                        $idUnsubscribed[] = (int) $value['idUnsubscribed'];
                        $idCat =  explode(',',$value['idCategories']);
                    }
                    unset($c);          
                    foreach ($idscat as $key => $val) {
                        if(!in_array($val, $idCat)){
                            $idCat[] = $val;
                        }   
                    }
                    unset($idscat);
                    $query = "UPDATE unsubscribed SET updated = ".time().", idCategories = '". implode(',', array_values(array_unique($idCat, SORT_REGULAR)))."',"
                            ." created = ".time().", other = '".$data->motive."' WHERE idUnsubscribed IN (". implode(",", $idUnsubscribed).")";  
                    $this->db->execute($query); 
                }else{
                    $contact2 = \Contact::findFirst([["idContact" => (int) $val,"deleted" => 0]]);
                    $unsubscribed = new \Unsubscribed();                    
                    $unsubscribed->idContact = $val;
                    $unsubscribed->motive = "Otro";
                    $unsubscribed->option = "categorie";
                    $unsubscribed->other = $data->motive;
                    $unsubscribed->idCategories = implode(",",$idscat);
                    $unsubscribed->idSubaccount = (int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
                    $unsubscribed->email = $contact2->email;
                    $unsubscribed->createdBy = \Phalcon\DI::getDefault()->get("user")->email;
                    $unsubscribed->updatedBy = \Phalcon\DI::getDefault()->get("user")->email;
                    if (!$unsubscribed->save()) {
                        foreach ($unsubscribed->getMessages() as $msg) {
                             throw new \InvalidArgumentException($msg);
                        }
                    }
                }                    
            }                          
            unset($contact);
            $idcontact = implode(',', $ids);
            unset($ids);
            $idcontactlist = implode(',', $idscl);
            unset($idscl);
    
            $Cxcl = \Cxcl::find(["conditions" => "idContact IN ({$idcontact}) AND idContactlist IN ({$idcontactlist}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"]);
            if($Cxcl){
                foreach ($Cxcl as $value){
                    $value->unsubscribed = (int) time();
                    $value->status = 'unsubscribed';
                    $value->updated = (int) time();
                    if (!$value->update()) {
                        foreach ($value->getMessages() as $msg) {
                            throw new \InvalidArgumentException($msg);
                        }
                    }            
                }
            }         
            // borre el insert a cxclxun
        }else{
          
            throw new \InvalidArgumentException("No existe un contacto asociado según el correo ingresado"); 
            
        }
        //Validacion para email lleno y phone lleno
      }else if(!empty($data->email) && !empty($data->phone) && !empty($data->phoneCode)){
        $data->email = strtolower($data->email);
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("El formato de correo electrónico no es correcto");
        }
        $email = (string) trim($data->email);
        if (!is_numeric($data->phone)) {
            throw new InvalidArgumentException("Sólo se permite números en el campo móvil");
        }
        $phone = (string) trim($data->phone);       
        $indicative = (string) trim($data->phoneCode);
        $where = array("email" => $email,"phone" => $phone, 'indicative' => $indicative,
                     "deleted" => 0,
                     "idAccount" => (string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount);
        $contact = \Contact::find([$where]);
        
        $ids = array();
        
        if($contact){
            foreach ($contact as $value) {
                $idContact = $value->idContact;
                //$unsuscribe = \Unsubscribed::find(["conditions" => "idContact = {$value->idContact} AND option = 'categorie' AND idSubaccount = ".(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount." AND deleted = 0"]);
                $query = "SELECT idUnsubscribed FROM unsubscribed "
                        ." WHERE idContact = ".$value->idContact
                        ." AND deleted = 0 AND unsubscribed.option = 'categorie'"
                        ." AND idSubaccount = ".(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
                $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);      
                
                if(count($c)>0){
                  $in = array();  
                  foreach ($c as $value) {
                    $in[] = (int) $value['idUnsubscribed'];
                  }
                  $query = "UPDATE unsubscribed SET updated = ".time()
                            ." WHERE idUnsubscribed IN (". implode(",", $in).")";    
                  $this->db->query($query);                  
                }else{
                    $unsubscribed = new \Unsubscribed();                    
                    $unsubscribed->idContact = $value->idContact;
                    $unsubscribed->motive = "Otro";
                    $unsubscribed->option = "categorie";
                    $unsubscribed->other = $data->motive;
                    $unsubscribed->idSubaccount = (int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
                    $unsubscribed->createdBy = \Phalcon\DI::getDefault()->get("user")->email;
                    $unsubscribed->updatedBy = \Phalcon\DI::getDefault()->get("user")->email;
                    if (!$unsubscribed->save()) {
                     foreach ($unsubscribed->getMessages() as $msg) {
                         throw new \InvalidArgumentException($msg);
                     }
                    }
                }
                $ids[] = $idContact;            
            }
            unset($contact);
            $idcontact = implode(',', $ids);
            unset($ids);
            $idscat = array();
            $idscl = array();
            foreach ($data->services as $value) {
                list($tmpidCt, $tmpidCl) = explode("-", $value);
                $idscat[] = $tmpidCt;
                $idscl[] = $tmpidCl;
                $tmpidCl = "";
                $tmpidCt = "";
            }

            //\Phalcon\DI::getDefault()->get('logger')->log(implode(',', $idscl));
            $idcontactlist = implode(',', $idscl);
            unset($idscl);

            $Cxcl = \Cxcl::find(["conditions" => "idContact IN ({$idcontact}) AND idContactlist IN ({$idcontactlist}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"]);
            if($Cxcl){
                foreach ($Cxcl as $value){
                    $value->unsubscribed = (int) time();
                    $value->status = 'unsubscribed';
                    $value->updated = (int) time();
                    if (!$value->update()) {
                        foreach ($value->getMessages() as $msg) {
                            throw new \InvalidArgumentException($msg);
                        }
                    }            
                }
            }
        }else{
          throw new \InvalidArgumentException("No existe un contacto asociado al correo y número de móvil ingresado"); 
        }
     //Validacion para phone lleno
      }else if(!empty($data->phoneCode) && !empty($data->phone)){
        if (!is_numeric($data->phone)) {
            throw new InvalidArgumentException("Solo se permite números en el campo móvil");
        }
        $phone = (string) trim($data->phone);
        $indicative = (string) trim($data->phoneCode);
        $where = array("phone" =>$phone, 'indicative' => $indicative,
                     "deleted" => 0, 
                     "idAccount" => (string)\Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount);
        $contact = \Contact::find([$where]);
        
        $ids = array();
        
        if($contact){
            foreach ($contact as $value) {
                $idContact = $value->idContact;
                $query = "SELECT idUnsubscribed FROM unsubscribed "
                        ." WHERE idContact = ".$value->idContact
                        ." AND deleted = 0 AND unsubscribed.option = 'categorie'"
                        ." AND idSubaccount = ".(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
                $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);      
                
                if(count($c)>0){                    
                  $in = array();  
                  foreach ($c as $value) {
                    $in[] = (int) $value['idUnsubscribed'];
                  }
                  $query = "UPDATE unsubscribed SET updated = ".time()
                            ." WHERE idUnsubscribed IN (". implode(",", $in).")";    
                  $this->db->query($query);                  
                }else{
                    $unsubscribed = new \Unsubscribed();
                    $unsubscribed->idContact = $value->idContact;
                    $unsubscribed->motive = "Otro";
                    $unsubscribed->option = "categorie";
                    $unsubscribed->other = $data->motive;
                    $unsubscribed->idSubaccount = (int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
                    $unsubscribed->createdBy = \Phalcon\DI::getDefault()->get("user")->email;
                    $unsubscribed->updatedBy = \Phalcon\DI::getDefault()->get("user")->email;
                    if (!$unsubscribed->save()) {
                     foreach ($unsubscribed->getMessages() as $msg) {
                         throw new \InvalidArgumentException($msg);
                     }
                    }
                }
                $ids[] = $idContact;            
            }
            unset($contact);
            $idcontact = implode(',', $ids);
            unset($ids);
            $idscat = array();
            $idscl = array();
            foreach ($data->services as $value) {
                list($tmpidCt, $tmpidCl) = explode("-", $value);
                $idscat[] = $tmpidCt;
                $idscl[] = $tmpidCl;
                $tmpidCl = "";
                $tmpidCt = "";
            }
            //\Phalcon\DI::getDefault()->get('logger')->log(implode(',', $idscl));
            $idcontactlist = implode(',', $idscl);
            unset($idscl);

            $Cxcl = \Cxcl::find(["conditions" => "idContact IN ({$idcontact}) AND idContactlist IN ({$idcontactlist}) AND spam = 0 AND bounced = 0 AND blocked = 0 AND deleted = 0"]);
            if($Cxcl){
                foreach ($Cxcl as $value){
                    $value->unsubscribed = (int) time();
                    $value->status = 'unsubscribed';
                    $value->updated = (int) time();
                    if (!$value->update()) {
                        foreach ($value->getMessages() as $msg) {
                            throw new \InvalidArgumentException($msg);
                        }
                    }            
                }
            }
        }else{
          throw new \InvalidArgumentException("No existe un contacto asociado al número de móvil ingresado"); 
        }          
      }      
  }
}