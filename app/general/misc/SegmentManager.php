<?php

namespace Sigmamovil\General\Misc;

ini_set('memory_limit', '1024M');

class SegmentManager {

  public $cxcl = array();
  public $in = array();
  public $array = array();

  public function typeConditionsAll($contact, $field, $value, $key, $idContactlist, $flag) {

/*    \Phalcon\DI::getDefault()->get('logger')->log("---PASA POR typeConditionsAll CUSTOMFIELD---");
    \Phalcon\DI::getDefault()->get('logger')->log("FIELD ({$field}) - value ({$value})");
    \Phalcon\DI::getDefault()->get('logger')->log("FIELD ({$field}) - value ({$value})");*/ 
         
    //\Phalcon\DI::getDefault()->get('logger')->log(print_r($customfield,true));
    
    if($field == "name" || $field == "lastname" || $field == "email" || $field == "birthdate" || $field == "phone" ){
     
         if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to) {
          if (isset($contact->$field) && $contact->$field != $value) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains) {
          if (isset($contact->$field) && strpos($contact->$field, $value) == false) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have) {
          if (isset($contact->$field) && strpos($contact->$field, $value) >= 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with) {
          if (isset($contact->$field) && preg_match("#^" . $value . ".*#s", trim($contact->$field)) == 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in) {
          if (isset($contact->$field) && preg_match("#.*" . $value . "$#s", trim($contact->$field)) == 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than) {
          if (isset($contact->$field) && $contact->$field < $value && is_numeric($contact->$field)) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->low_to) {
          if (isset($contact->$field) && $contact->$field > $value && is_numeric($contact->$field)) {
            $flag = false;
          }
        }
        
    }else{  
            
        $customfield = \Cxc::findFirst([["idContact" => $contact->idContact]]);
        $customfield = $customfield->idContactlist[$idContactlist];
        
        //\Phalcon\DI::getDefault()->get('logger')->log(print_R($customfield[$field],true));
                    
        if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to) {
          if ($customfield[$field]["value"] != $value && isset($customfield[$field]["value"])) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains) {
          if (isset($customfield[$field]["value"]) && strpos($customfield[$field]["value"], $value) == false) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have) {
          if (isset($customfield[$field]["value"]) && strpos($customfield[$field]["value"], $value) >= 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with) {
          if (isset($customfield[$field]["value"]) && preg_match("#^" . $value . ".*#s", trim($customfield[$field]["value"])) == 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in) {
          if (isset($customfield[$field]["value"]) && preg_match("#.*" . $value . "$#s", trim($customfield[$field]["value"])) == 0) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than) {
          if (isset($customfield[$field]["value"]) && $customfield[$field]["value"] < $value && is_numeric($customfield[$field]["value"])) {
            $flag = false;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->low_to) {
          if (isset($customfield[$field]["value"]) && $customfield[$field]["value"] > $value && is_numeric($customfield[$field]["value"])) {
            $flag = false;
          }
        }
        unset($customfield);
    }
    
    return $flag;
  }

  public function typeConditionsSome($contact, $field, $value, $key, $idContactlist, $flag) {
    
    if($field == "name" || $field == "lastname" || $field == "email" || $field == "birthdate" || $field == "phone" ){
        
        if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to) {          
          if (isset($contact->$field) && $contact->$field == $value) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains) {
          $p = strpos($contact->$field, $value);
          if (is_numeric($p) && isset($p)) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have) {
          if (strpos($contact->$field, $value) == false) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with) {
          if (preg_match("#^" . $value . ".*#s", trim($contact->$field)) == 1) {
            $flag = true;
          }

        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in) {
          if (preg_match("#.*" . $value . "$#s", trim($contact->$field)) >= 1) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than) {
          if ($contact->$field > $value && is_numeric($contact->$field)) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->low_to) {
          if ($contact->$field < $value && is_numeric($contact->$field)) {
            $flag = true;
          }
        }
        
    }else{
        $customfield = \Cxc::findFirst([["idContact" => $contact->idContact]]);
        $customfield = $customfield->idContactlist[$idContactlist];
        
        if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to) {
          if ($customfield[$field]["value"] == $value) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains) {          
          if (isset($customfield[$field])) {
            $p2 = strpos($customfield[$field]["value"], $value);
          }
          if (isset($p2) && is_numeric($p2)) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have) {
          if (strpos($customfield[$field]["value"], $value) == false) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with) {
          if (preg_match("#^" . $value . ".*#s", trim($customfield[$field]["value"])) == 1) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in) {
          if (preg_match("#.*" . $value . "$#s", trim($customfield[$field]["value"])) >= 1) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than) {
          if ($customfield[$field]["value"] > $value && is_numeric($customfield[$field]["value"])) {
            $flag = true;
          }
        } else if ($key == \Phalcon\DI::getDefault()->get('filtersSegment')->low_to) {
          if ($customfield[$field]["value"] < $value && is_numeric($customfield[$field]["value"])) {
            $flag = true;
          }
        }
        unset($customfield);
    }
        
    return $flag;
  }

  public function addOneContact($idContact, $idContactlist) {

    $contact = \Contact::findFirst([["idContact" => $idContact]]);
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $queryRepeat = new \MongoDB\Driver\Query(["contactlist.idContactlist" => "" . $idContactlist . "","deleted" => 0]);
    $segment = $manager->executeQuery("aio.segment", $queryRepeat)->toArray();
    
    if ($segment) {
      foreach ($segment as $keysegment) {
        
        if ($keysegment->conditions == "Todas las condiciones") {

          $flag = true;
          foreach ($keysegment->filters as $key) {
            $field = $key->idCustomfield;
            $value = $key->value;
            $flag = $this->typeConditionsAll($contact, $field, $value, $key->conditions, $idContactlist, $flag);
          }
          
          if ($flag == true) {
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por true de flag");
            $contactsegment = \Sxc::find([["idContact" => $contact->idContact, "deleted" => 0, "idSegment" => $keysegment->idSegment]]);

            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por contactsegment IDSEGMENTO  es {$keysegment->idSegment} : "); 

            if($contactsegment){
            
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por true de flag-pasa por contactsegment TRUE  es {$keysegment->idSegment} : ");
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por true de flag-pasa por contactsegment TRUE  idcontact es {$contact->idContact} : ");                               
            $this->editOneContact($contact->idContact, $idContactlist);
            
            }else{
            
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por contactsegment FALSE es {$keysegment->idSegment}");
            
                $sxc = new \Sxc();
                $sxc->idSegment = $keysegment->idSegment;
                
                $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
                $unsubscribed = (int) $thiscontact->unsubscribed;            
                $blocked = (int) $thiscontact->blocked;
                foreach ($contact as $key => $value) {
                  if ($key != "_id") {
                    $sxc->$key = $value;
                  }
                  $sxc->unsubscribed = (int) $unsubscribed;
                  $sxc->blocked = (int) $blocked;
                }
                $sxc->save();            
                unset($thiscontact);
                unset($unsubscribed);
            }
          }else{
            
            /*\Phalcon\DI::getDefault()->get('logger')->log("pasa por false de flag");
            \Phalcon\DI::getDefault()->get('logger')->log("pasa por false de flag idcontact es {$contact->idContact}");  */         
            $contactsegments = \Sxc::find([["idContact" => $contact->idContact, "idSegment" => $keysegment->idSegment]]);
            
            if($contactsegments){
               /* \Phalcon\DI::getDefault()->get('logger')->log("pasa por el true de flag flase");
                \Phalcon\DI::getDefault()->get('logger')->log("pasa por el true de flag flase idcontact es {$contact->idContact}");*/
                //\Phalcon\DI::getDefault()->get('logger')->log(print_r($contactsegments,true)); 
                
                $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
                $unsubscribed = (int) $thiscontact->unsubscribed;
                $blocked = (int) $thiscontact->blocked;
                $blockedEmail = $contact->blockedEmail;
                $blockedPhone = $contact->blockedPhone;
                
                //\Phalcon\DI::getDefault()->get('logger')->log("pasa por el foreach de flag flase");
                
                foreach($contactsegments as $cs ){                    
                    $cs->unsubscribed = (int) $unsubscribed;
                    $cs->blocked = (int) $blocked;
                    $cs->blockedEmail = (int) $blockedEmail;
                    $cs->blockedPhone = (int) $blockedPhone; 
                    $cs->deleted = time();               
                    $cs->save();   
                }   
            }
            
            
          }
        } else {
            
          $flag = false;
          foreach ($keysegment->filters as $key) {
            $field = $key->idCustomfield;
            $value = $key->value;
            $flag = $this->typeConditionsSome($contact, $field, $value, $key->conditions, $idContactlist, $flag);
          }
          
          if ($flag == true) {
            
            $contactsegment = \Sxc::find([["idContact" => $contact->idContact, "deleted" => 0, "idSegment" => $keysegment->idSegment]]);

            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por contactsegment typeConditionsSome IDSEGMENTO  es {$keysegment->idSegment} : "); 
            
            if($contactsegment){
            
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por contactsegment de typeConditionsSome TRUE  es {$keysegment->idSegment} : ");                               
            $this->editOneContact($contact->idContact, $idContactlist);
            
            }else{
            
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por contactsegment de typeConditionsSome FALSE es {$keysegment->idSegment}");
            
                $sxc = new \Sxc();
                $sxc->idSegment = $keysegment->idSegment;
                
                $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
                $unsubscribed = (int) $thiscontact->unsubscribed;            
                $blocked = (int) $thiscontact->blocked;
                foreach ($contact as $key => $value) {
                  if ($key != "_id") {
                    $sxc->$key = $value;
                  }
                  $sxc->unsubscribed = (int) $unsubscribed;
                  $sxc->blocked = (int) $blocked;
                }
                $sxc->save();            
                unset($thiscontact);
                unset($unsubscribed);
            }
          }else{
            
            //\Phalcon\DI::getDefault()->get('logger')->log("pasa por false de flag");           
            $contactsegments = \Sxc::find([["idContact" => $contact->idContact, "idSegment" => $keysegment->idSegment]]);
            
            if($contactsegments){
                //\Phalcon\DI::getDefault()->get('logger')->log("pasa por el true de flag flase");
                //\Phalcon\DI::getDefault()->get('logger')->log(print_r($contactsegments,true)); 
                
                $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
                $unsubscribed = (int) $thiscontact->unsubscribed;
                $blocked = (int) $thiscontact->blocked;
                $blockedEmail = $contact->blockedEmail;
                $blockedPhone = $contact->blockedPhone;
                
                //\Phalcon\DI::getDefault()->get('logger')->log("pasa por el foreach de flag flase");
                
                foreach($contactsegments as $cs ){                    
                    $cs->unsubscribed = (int) $unsubscribed;
                    $cs->blocked = (int) $blocked;
                    $cs->blockedEmail = (int) $blockedEmail;
                    $cs->blockedPhone = (int) $blockedPhone;    
                    $cs->deleted = time();            
                    $cs->save();   
                }   
            }                       
          }      
        }
      }
    }
  }

  public function editOneContact($idContact, $idContactlist) {
    $contact = \Contact::findFirst([["idContact" => $idContact]]);
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $queryRepeat = new \MongoDB\Driver\Query(["contactlist.idContactlist" => "" . $idContactlist . "","deleted" => 0]);
    $segment = $manager->executeQuery("aio.segment", $queryRepeat)->toArray();
    foreach ($segment as $keysegment) {
      
      $contactSegments = \Sxc::find([["idContact" => $idContact, "idSegment" => $keysegment->idSegment]]);
      
      if ($keysegment->conditions == "Todas las condiciones") {

        $flag = true;          
        foreach ($keysegment->filters as $key) {
          $field = $key->idCustomfield;
          $value = $key->value;
          $flag = $this->typeConditionsAll($contact, $field, $value, $key->conditions, $idContactlist, $flag);
        }
        
        if ($flag == true) {

          if($contactSegments){
          
              unset($contact->_id);
              unset($contact->updatedBy);
              unset($contact->createdBy);
              unset($contact->updated);
              unset($contact->created);
              $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                    "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
              $unsubscribed = (int) $thiscontact->unsubscribed;
              $blocked = (int) $thiscontact->blocked;
              foreach($contactSegments as $contactSegment ){
                    
                foreach ($contact as $k => $v) {
                    $contactSegment->$k = $v;
                }
                $contactSegment->unsubscribed = (int) $unsubscribed;
                $contactSegment->blocked = (int) $blocked;
                $contactSegment->save();  
             }
          
          }
          
        }
        
        
      } else {
        
        $flag = false;        
        foreach ($keysegment->filters as $key) {
            $field = $key->idCustomfield;
            $value = $key->value;
            $flag = $this->typeConditionsSome($contact, $field, $value, $key->conditions, $idContactlist, $flag);
        }

        if ($flag == true) {
            
          if($contactSegments){  
            
              unset($contact->_id);
              unset($contact->updatedBy);
              unset($contact->createdBy);
              unset($contact->updated);
              unset($contact->created);
              $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                                                    "bind" => array(0 => $contact->idContact, 1 => $idContactlist)));
              $unsubscribed = (int) $thiscontact->unsubscribed;                  
              $blocked = (int) $thiscontact->blocked;
              foreach($contactSegments as $contactSegment ){
                        
                foreach ($contact as $k => $v) {
                    $contactSegment->$k = $v;
                }
                $contactSegment->unsubscribed = (int) $unsubscribed;
                $contactSegment->blocked = (int) $blocked;
                $contactSegment->save();  
              }
          }
        }
      }
    }
  }

  public function addContactByImport($nameCollection, $segment) {

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $queryRepeat = new \MongoDB\Driver\Query([]);
    $tmp = $manager->executeQuery("aio.{$nameCollection}", $queryRepeat)->toArray();

    foreach ($tmp as $keytmp) {
      $contact = \Contact::findFirst([["idContact" => $keytmp->idContact]]);

      foreach ($segment as $keysegment) {
        if ($keysegment->conditions == "Todas las condiciones") {
          $flag = true;
          foreach ($keysegment->filters as $key) {
            $field = $key->idCustomfield;
            $value = $key->value;
            $flag = $this->typeConditionsAll($contact, $field, $value, $key->conditions, $flag);
          }
          if ($flag == true) {
            $sxc = new \Sxc();
            $sxc->idSegment = $keysegment->idSegment;
            foreach ($contact as $key => $value) {
              if ($key != "_id") {
                $sxc->$key = $value;
              }
            }
            $sxc->save();
            return true;
          }
          return false;
        } else {
          $flag = false;
          foreach ($keysegment->filters as $key) {
            $field = $key->idCustomfield;
            $value = $key->value;
            $flag = $this->typeConditionsSome($contact, $field, $value, $key->conditions, $flag);
          }

          if ($flag == true) {
            $sxc = new \Sxc();
            $sxc->idSegment = $keysegment->idSegment;
            foreach ($contact as $key => $value) {
              if ($key != "_id") {
                $sxc->$key = $value;
              }
            }
            $sxc->save();
            return true;
          }
        }
      }
    }
  }

  public function editOneSegment($segment) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $in = $this->inIdContactlist($segment);
    try {
      $dir = \Phalcon\DI::getDefault()->get('path')->path . "tmp";
      if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
      }
      $report = "SELECT   CONCAT('{','idContact:' ,idContact,'}')   FROM cxcl WHERE idContactlist IN ({$in}) AND deleted = 0 "
              . "INTO OUTFILE  '" . $dir . "/tmpidcontact.json'";
      $db = \Phalcon\DI::getDefault()->get('db');
      $db->execute($report);

      
      $collectiontmp = new \MongoDB\Driver\Command(['eval' => "createcollection('tmpcontact')", 'nolock' => true]);
      $manager->executeCommand('aio', $collectiontmp);
      $collectiontmp = new \MongoDB\Driver\Command(['eval' => "createcollection('tmpidcontact')", 'nolock' => true]);
      $manager->executeCommand('aio', $collectiontmp);
      $collectiontmp = new \MongoDB\Driver\Command(['eval' => "createcollection('tmpsxc')", 'nolock' => true]);
      $manager->executeCommand('aio', $collectiontmp);

      shell_exec("mongoimport --db aio --collection tmpidcontact --file " . \Phalcon\DI::getDefault()->get('path')->path . "tmp/tmpidcontact.json ");
      $query = '{idContact:{$in: db.tmpidcontact.find({},{_id:0}).toArray().map(function(u){return u.idContact})}}';
      $createTmpContact = new \MongoDB\Driver\Command(['eval' => "createTmpContact({$query})", 'nolock' => true]);
//      $count = $manager->executeCommand('aio', $createTmpContact);
//      $segment->totalcontacts = $count->toArray()[0]->retval;
      $manager->executeCommand('aio', $createTmpContact);
//      $segment->totalcontacts = $count->toArray()[0]->retval;
//      $segment->save();
      $where = [];
      foreach ($segment->filters as $key) {
        if (is_numeric($key->idCustomfield)) {
          foreach ($segment->contactlist as $k) {
            if (isset($k)) {
              switch ($key->conditions) {
                case \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => $key->value];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$regex' => ".*$key->value.*"]];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$ne' => "$key->value"]];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$regex' => "^$key->value.*"]];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$regex' => ".*$key->value$"]];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$gt' => $key->value]];
                  break;
                case \Phalcon\DI::getDefault()->get('filtersSegment')->low_to:
                  $arr[] = ["cxc.idContactlist." . $k->idContactlist . "." . $key->idCustomfield . ".value" => ['$lt' => $key->value]];
                  break;
              }
            }
          }
        } else {
          switch ($key->conditions) {
            case \Phalcon\DI::getDefault()->get('filtersSegment')->equal_to:
              $arr[] = [ $key->idCustomfield => $key->value];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->in_contains:
              $arr[] = [ $key->idCustomfield => ['$regex' => ".*$key->value.*"]];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->it_does_not_have:
              $arr[] = [ $key->idCustomfield => ['$ne' => "$key->value"]];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->starts_with:
              $arr[] = [ $key->idCustomfield => ['$regex' => "^$key->value"]];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->ends_in:
              $arr[] = [ $key->idCustomfield => ['$regex' => ".*$key->value$"]];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->grater_than:
              $arr[] = [ $key->idCustomfield => ['$gt' => $key->value]];
              break;
            case \Phalcon\DI::getDefault()->get('filtersSegment')->low_to:
              $arr[] = [ $key->idCustomfield => ['$lt' => $key->value]];
              break;
          }
        }
      }
      if ($segment->conditions == "Todas las condiciones") {
        $where['$and'] = $arr;
      } else {
        $where['$or'] = $arr;
      }

      $p = json_encode($where);
      $p = str_replace('"', "'", $p);
      $createTmpSxc = new \MongoDB\Driver\Command(['eval' => "createTmpSxc({$p})", 'nolock' => true]);
      $manager->executeCommand('aio', $createTmpSxc);
      $copycollection = new \MongoDB\Driver\Command(['eval' => "copycollection('sxc',{$segment->idSegment})", 'nolock' => true]);
      $count = $manager->executeCommand('aio', $copycollection);
      $segment->totalcontacts = $count->toArray()[0]->retval;
      $segment->save();
      unlink(\Phalcon\DI::getDefault()->get('path')->path . "tmp/tmpidcontact.json");
    } catch (Exception $exc) {
      $this->logger->log("{$exc}");
      return $this->set_json_response(array('message' => $exc->getMessage()), 400);
    }
  }

  private function findCxcl($idContactlist) {
    $arr = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
            ->from('Cxcl')
            ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
            ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ")
            ->getQuery()
            ->execute();
    array_push($this->cxcl, $arr);
  }

  public function inIdContactlist($target) {
    $idContactlist = "";
    for ($index = 0; $index < count($target->contactlist); $index++) {
      $idContactlist .= $target->contactlist[$index]->idContactlist;
      if (count($target->contactlist) != ($index + 1)) {
        $idContactlist .=",";
      }
    }
    return $idContactlist;
  }

}
