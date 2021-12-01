<?php

/**
 * Description of DuplicateContactsController
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$duplicateContacts = new DuplicateContacts();
$duplicateContacts->index($id);

class DuplicateContacts {
  
  public function index($idAccount){
    //$idAccount = '';
    $subAccounts = Subaccount::find([["idAccount" => $idAccount, "deleted" => 0]]);
    $ids = "";
    foreach ($subAccounts as $subAccount){
      $ids .= $subAccount->idSubaccount . ",";
    }
    unset($subAccounts);
    $ids = trim($ids, ',');
    $contactlists = Contactlist::find(["idSubaccount IN ({$ids}) AND deleted = 0"]);
    unset($ids);
    $ids = "";
    foreach ($contactlists as $contactlist){
      $cxcls = \Cxcl::find(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$contactlist->idContactlist}"]);
      foreach ($cxcls as $cxcl){
        $where = array("idContact" => (int) $cxcl->idContact, "deleted" => 0);
        $contact = \Contact::findFirst([$where]);
        if(isset($contact->idContact) && isset($contact->idAccount) && $contact->idAccount != $idAccount){
          $idnNewContact = $this->createContact($contact);
          $this->editCxcl($contact->idContact, $cxcl->idContactlist, $idnNewContact);
          //
          $ids .= $idnNewContact . ",";
        }
      }
    }
    var_dump($ids);    
  }
  
  public function createContact($data){
    $idAccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $idSubaccount = \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount : $this->idSubaccount;

    $contact = new \Contact();
    $contactManger = new \Sigmamovil\General\Misc\ContactManager();
    $nextIdContact = $contactManger->autoIncrementCollection("id_contact");
    $contact->idContact = $nextIdContact;
    $contact->blockedPhone = $data->blockedPhone != "" ? $data->blockedPhone : "";
    $contact->blockedEmail = $data->blockedEmail != "" ? $data->blockedEmail : "";
    $contact->phone = $data->phone;
    $contact->indicative = $data->indicative;
    $contact->email = $data->email;
    $contact->name = $data->name;
    $contact->lastname = $data->lastname;
    $contact->birthdate = $data->birthdate;
    $contact->created = $data->created;
    $contact->updated = $data->updated;

    $contact->idSubaccount = $idSubaccount;
    $contact->idAccount = $idAccount;
    $contact->deleted = 0;
    
    if ($contact->email != "" and $this->isEmailBlocked($contact->email) ){
      $contact->blockedEmail = time();
    }
    if ($contact->phone != "" and $this->isPhoneBlocked($contact->phone, $contact->indicative)) {
      $contact->blockedPhone = time();
    }
    if (!$contact->save()) {
      foreach ($contact->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $idNewContact = $contact->idContact;
    unset($contact);
    return $idNewContact;
  }
  
  public function isEmailBlocked($email) {
    $flag = false;
    $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $result = \Blocked::find([["email" => $email, "deleted" => 0, "idAccount" => $idAccount]]);
    unset($idAccount);
    if ($result) {
      $flag = true;
    }
    unset($result);
    return $flag;
  }

  public function isPhoneBlocked($phone, $indicative) {
    $flag = false;
    $idAccount = (int) \Phalcon\DI::getDefault()->has('user') ? \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount : $this->idAccount;
    $result = \Blocked::find([["phone" => $phone, "indicative" => $indicative, "deleted" => 0, "idAccount" => $idAccount]]);
    unset($idAccount);
    if ($result) {
      $flag = true;
    }
    unset($result);
    return $flag;
  }
  
  public function editCxcl($idContact, $idContactlist, $idnNewContact){
    $findCxcl = \Cxcl::findFirst(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} AND Cxcl.idContact = {$idContact}"]);
    $findCxcl->idContact = (int) $idnNewContact;
    $findCxcl->updated = time();
    $findCxcl->save();
    unset($findCxcl);
    $findCxc = \Cxc::findFirst([["idContact" => $idContact, "deleted" => 0]]);
    $findCxc->idContact = (int) $idnNewContact;
    $findCxc->updated = time();
    $findCxc->save();
    unset($findCxc);
  }

}
