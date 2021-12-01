<?php

/**
 * Description of UpdateBlockedInIdContact
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$updateBlockedInIdContact = new UpdateBlockedInIdContact();
$updateBlockedInIdContact->index($id);

class UpdateBlockedInIdContact {
  
  public function index($idAccount){
    $findBlockeds = Blocked::find([[
      "idAccount" => (string) $idAccount,
    ]]);
    
    foreach ($findBlockeds as $blocked){
      $findFirst = Blocked::findFirst([["idBlocked" => $blocked->idBlocked]]);
      //Sacar el correo Primero
      if(filter_var($findFirst->field, FILTER_VALIDATE_EMAIL) || is_numeric($findFirst->field)){
        $email = "";
        $phone = "";
        $indicative = "";
        //
        if(filter_var($findFirst->field, FILTER_VALIDATE_EMAIL)){
          $email = $findFirst->field;
        }
        if(is_numeric($findFirst->field)){
          $phone = $findFirst->field;
          $indicative = $findFirst->indicative;
        }
        //
        $findContacts = Contact::find([[
          "idAccount" => (string) $idAccount,
          "email" => $email != "" ? $email : "",
          "phone" => $phone != "" ? $phone : "",
          "indicative" => $indicative != "" ? $indicative : "",
          "deleted" => 0
        ]]);       
        if($findContacts != false){
          foreach ($findContacts as $contact){
            if(!in_array((int) $contact->idContact, $findFirst->idContacts)){
              $findFirst->idContacts[] = (int) $contact->idContact;
            }
          }
          unset($findContacts);
        } else {
          $findFirst->idContacts = [];
        }
        $findFirst->email = (string) $email;
        $findFirst->phone = (string) $phone;
        $findFirst->indicative = (int) $indicative;
        $findFirst->idAccount = (int) $idAccount;
        $findFirst->updated = time();
        unset($findFirst->field);
        $findFirst->save();
      } else {
        var_dump($findFirst->idBlocked);
        var_dump($findFirst->field);
      }
    }
  }
}
