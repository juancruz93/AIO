<?php

/**
 * Description of MinusContacts
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$minusContacts = new MinusContacts();
$minusContacts->index($id);

class MinusContacts {
  
  public function index($idAccount){
    $subAccounts = \Subaccount::find(["conditions" => "idAccount = ?0 AND deleted = 0","bind" => [0 => $idAccount]]);
    $ids = "";
    foreach ($subAccounts as $subAccount){
      $ids .= $subAccount->idSubaccount . ",";
    }
    unset($subAccounts);
    $ids = trim($ids, ',');

    $contactlists = Contactlist::find(["conditions" => "idSubaccount IN ({$ids}) AND deleted = 0"]);
    unset($ids);
    $ids = 0;
    foreach ($contactlists as $contactlist){
      $sql = "SELECT DISTINCT idContact FROM cxcl"
        . " WHERE idContactlist = {$contactlist->idContactlist}"
        . " AND deleted = 0 ";
      $cxcls = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
      //$cxcls = \Cxcl::find(["conditions" => "idContactlist = {$contactlist->idContactlist} AND deleted = 0", "distinct" => "idContact"]);
      foreach ($cxcls as $cxcl){
        $where = array("idContact" => (int) $cxcl['idContact'], "deleted" => 0);
        $contact = \Contact::findFirst([$where]);
        if(isset($contact->idContact) && isset($contact->idAccount) && $contact->email != ""){
          //
          //var_export($contact->email);
          $this->isEmail($contact->email, $idAccount);
          $this->isEmailBlocked($contact->email, $idAccount);
          $this->isEmailBuonced($contact->email);
          //
          if(strtolower($contact->email) != $contact->email){
            var_dump("Correo Editado: ".$contact->email);
            $contact->email = strtolower($contact->email);
            $contact->save();
            //
            $ids++;
          }       
        }
      }
    }
    unset($contactlists);
    unset($cxcls);
    unset($contact);
    var_dump("Fueron {$ids} Contactos a los cuales se modifico el Email a Minuscula"); 
  }
  
  public function isEmail($email, $idAccount) {
    $flag = false;
    $result = \Email::findFirst([["email" => $email, "idAccount" => $idAccount]]);
    unset($idAccount);
    if($result != false){
      if(strtolower($email) != $email){
        $result->field = strtolower($email);
        $result->save();
        $flag = true;
      }
    }
    unset($result);
    return $flag;
  }
    
  public function isEmailBlocked($email, $idAccount) {
    $flag = false;
    $result = \Blocked::findFirst([["email" => $email, "deleted" => 0, "idAccount" => (int) $idAccount]]);
    unset($idAccount);
    if($result != false){
      if(strtolower($email) != $email){
        $result->field = strtolower($email);
        $result->save();
        $flag = true;
      }
    }
    unset($result);
    return $flag;
  }

  public function isEmailBuonced($email) {
    $flag = false;
    $result = \Bouncedmail::findFirst([["email" => $email]]);
    if($result != false){
      if(strtolower($email) != $email){
        $result->field = strtolower($email);
        $result->save();
        $flag = true;
      }
    }
    unset($result);
    return $flag;
  }

}
