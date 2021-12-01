<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$updateblocked = new UpdateBlocked();
$updateblocked->index($id);

class UpdateBlocked {

  public function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
  }
  
  public function index($id){
    $contactData = $this->modelsManager->createBuilder()
      ->columns("Cxcl.idContact")
      ->distinct(true)
      ->from("Cxcl")
      ->join("Contactlist", "Contactlist.idContactlist = Cxcl.idContactlist")
      ->join("Subaccount", "Subaccount.idSubaccount = Contactlist.idSubaccount")
      ->where("Subaccount.idAccount = {$id} AND Cxcl.blocked > 0 AND Cxcl.deleted = 0")
      ->getQuery()
      ->execute();
    foreach($contactData as $contact){
      $contacts = \Contact::findFirst([[
        "idAccount" => (string) $id, 
        "deleted" => 0, 
        "idContact" => (int)$contact->idContact
      ]]);
      if(isset($contacts->idContact)){
        if(isset($contacts->blocked)){
          $blockedEmail = \Blocked::findFirst([["email" => $contacts->email, "idAccount" => (int) $id]]);
          $blockedPhone = \Blocked::findFirst([["phone" => $contacts->phone, "idAccount" => (int) $id]]);
          if($blockedEmail == null || $blockedPhone == null){
            $this->saveBlocked($contacts,$id);
          } else {
            continue;
          }
        } else {
          $this->saveBlocked($contacts,$id);
          $this->updateContact($contacts->idContact, $id);
        }
      } else {
        continue;
      }
    }
    exit;
  }
  
  public function saveBlocked($contact,$idAccount){
    $account = \Account::findFirst(["idAccount" => $idAccount, "deleted" => 0]);
    //
    $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
    
    if($contact->email != null && $contact->phone == "" && $contact->indicative == ""){
      $this->setBlockedContact($contact, "email");
      //INICIO Para indicar el email bloqueado en colecci�n Blocked (exista o no)
      $blockedEmail = \Blocked::findFirst([["email" => $contact->email, "idAccount" => $idAccount]]);
      if ($blockedEmail == false) {
        $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
        $blockedEmail = new \Blocked();
        $blockedEmail->idBlocked = $nextIdBlocked;
        $blockedEmail->field = $contact->email;
        $blockedEmail->idAccount = $idAccount;
        $blockedEmail->blocked = time();
        $blockedEmail->motive = "Bloqueado por importación de archivo csv";
        if (!$blockedEmail->save()) {
          throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
        }
        $this->logger->log("Blocked: {$blockedEmail->idBlocked}");
      } else {
        $blockedEmail->blocked = time();
        $blockedEmail->motive = "Bloqueado por importación de archivo csv";
        if (!$blockedEmail->save()) {
          throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
        }
        $this->logger->log("Blocked: {$blockedEmail->idBlocked}");
      }
      //FIN
      //Se llama el idBlocked del registro anterior para actualizar el createdBy y updatedBy
      $updateblockedEmail = \Blocked::findFirst([["idBlocked" => $blockedEmail->idBlocked]]);
      $updateblockedEmail->createdBy = $account->email;
      $updateblockedEmail->updatedBy = $account->email;
      if (!$updateblockedEmail->save()) {
        foreach ($updateblockedEmail->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
    
    if($contact->phone != null){
      $this->setBlockedContact($contact, "phone");
      
    }
  }
  
  public function updateContact($idContact,$idAccount){
    $contact = \Contact::findFirst([[
      "idContact" => (int) $idContact,
      "idAccount" => (string) $idAccount
    ]]);
    $contact->blocked = time();
    if (!$contact->save()) {
      throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
    }
  }
  
  public function setBlockedContact($blockedContact, $email, $phone, $indicative) {
    if ($blockedContact) {
      $blockedContact->blocked = time();

      if ($email != "") {
        $blockedContact->blockedEmail = time();
      }
      if ($phone != "") {
        $blockedContact->blockedPhone = time();
      }
      $blockedContact->save();
      $cxcls = \Cxcl::find(array("conditions" => "idContact = ?0 and deleted = 0", "bind" => array(0 => $blockedContact->idContact)));

      if ($cxcls) {
        foreach ($cxcls as $cxcl) {
          $cxcl->status = 'blocked';
          $cxcl->blocked = time();
          $cxcl->save();
          $this->setAccountants($cxcl->idContactlist);
        }
      }
      $idSubaccount = (int) \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount;
      $idAccount = (int) \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount;
      $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
      //INICIO Para indicar el bloqueado en coleccion Blocked (exista o no)
      $blocked = \Blocked::findFirst([["email" => $email, "phone" => $phone, "indicative" => $indicative, "idAccount" => (int) $idAccount, "deleted" => 0]]);
      if ($blocked == false) {
        $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
        $blocked = new \Blocked();
        $blocked->idBlocked = $nextIdBlocked;
        $blocked->email = $email != "" ? $email : "";
        $blocked->phone = $phone != "" ? $phone : "";
        $blocked->indicative = $indicative != "" ? $indicative : "";
        $blocked->idAccount = $idAccount;
        $blocked->blocked = time();
        $blocked->idContacts[] = (int) $blockedContact->idContact;
        unset($blocked->field);
        if (!$blocked->save()) {
          throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
        }
        $msg = "Se ha Registrado un Contacto bloqueado";
      } else {
        if(!in_array((int) $blockedContact->idContact, $blocked->idContacts)){
          $blocked->idContacts[] = (int) $blockedContact->idContact;
        }
        $blocked->blocked = time();
        unset($blocked->field);
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
      $customLogger->email = $email;
      $customLogger->phone = $phone;
      $customLogger->indicative = $indicative;
      $customLogger->typeName = "blockedMethod";
      $customLogger->detailedLogDescription = $msg;
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
      unset($customLogger);
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
  
}
