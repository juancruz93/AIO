<?php

namespace Sigmamovil\General\Misc;

class AutoresponderSmsManager{

  private $targetSms,
          $autoresponder;

  /**
   * AutoresponderSmsManager constructor.
   * @param $autoresponder
   */
  public function __construct()
  {
    //$this->autoresponder = $autoresponder;
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }
  
  
//  public function getNumberOfContactLists($dataCtls){
//    $var1 = json_decode($dataCtls);
//    return count($var1->contactlists);  
//  }

  public function getCountContacts($data){
    $count = 0;
    $arrIdContact = array();
    
    switch ($data->type) {
      case "contactlist":
        $where = " ";
        
        for ($i = 0; $i < count($data->contactlists); $i++) {
          $where .= $data->contactlists[$i]->idContactlist;
          if ($i != (count($data->contactlists) - 1)) {
            $where .= " , ";
          }
        }
        $sql = "select DISTINCT idContact from cxcl "
                . "where idContactlist in ({$where}) "
                . " AND unsubscribed = 0 "
                . " AND deleted = 0 ";
        $count = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
        foreach ($count as $key) {
          array_push($arrIdContact, (int) $key['idContact']);
        }
        return $this->getCountContactsValidate($arrIdContact); //
        break;
      case "segment":
        $where = "";
        $count = 0;
        foreach ($data->segment as $key) {
          $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);

          if ($sxcs) {
            foreach ($sxcs as $sxc) {
              $count++;
              array_push($arrIdContact, (int) $sxc->idContact);

              $sql = "SELECT DISTINCT idContactlist from cxcl "
                      . "where idContact = " . $sxc->idContact;
              $contactlist = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
              foreach ($contactlists as $cl) {
                $where .= $cl['idContactlist'] . ", ";
              }
            }
          }
        }
        foreach ($data->segment as $key) {
          $countSegments = \Sxc::count([["idSegment" => $key->idSegment]]);
          if ($countSegments > 0) {
            for ($i = 0; $i < count($data->contactlists); $i++) {
              $where .= $data->contactlists[$i]->idContactlist;
              $countContacts = \Cxcl::count(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->contactlists[$i]->idContactlist]]);
              if ($countContacts > 0) {
                $datas['contact'] = $this->getFisrtContact($data->contactlists[$i]->idContactlist, $data->type);
                break;
              }
            }
            break;
          }
        }

        return $count;  //devuelvo count por que solamente necesito el numero de contactos

        break;
      default:
        break;
    }
  }
  
  public function getCountContactsValidate($arrIdContact) {
    $where = array("idContact" => ['$in' => $arrIdContact], "phone" => ['$nin' => ["", null, "null"]], "indicative" => ['$nin' => ["", null, "null"]], "blockedPhone" => ['$in' => ["", null, "null"]]);
    $contact = \Contact::count(array($where));
    return $contact;
  }
  
  
  
  
  /**
   * @description se utiliza para la insercion de sms desde la autorespuesta con class sms
   * @return boolean
   * @throws \InvalidArgumentException
   */

  public function insertSmsFromAutoresponder($autoresponder) { 
   
    $sms = new \Sms();
    $sms->idSubaccount = $autoresponder->idSubaccount;
      
    $sms->idAutoresponder = $autoresponder->idAutoresponder;
    $sms->idSmsCategory = $autoresponder->idSmsCategory;
    $sms->name = $autoresponder->name;
    $sms->startdate = date('Y-m-d H:i:s');
    $sms->message = $autoresponder->autoresponderContent->content;
    
    //tratando los datos  del target...
    $targetConverted = json_decode($autoresponder->target); //convierto a array el json
    $countTarget = $this->getCountContacts($targetConverted); //paso
    
    $sms->confirm = 1;  //confirmar envio...
    $sms->target = $countTarget;
    $sms->type = 'contact';
    $sms->created = time();
    $sms->updated = time();
    $sms->createdBy = $autoresponder->createdBy;
    $sms->updatedBy = $autoresponder->updatedBy;
    $sms->status = 'scheduled';
    $sms->receiver = $autoresponder->target;
    $sms->gmt = "-0500";
    $sms->originalDate = date('Y-m-d H:i:s');
    $sms->morecaracter = $autoresponder->morecaracter;
    
    if (!$sms->save()) {
      foreach ($sms->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    
//    // el contenido de la autorespuesta se le pasa al contenido del mail pero en sms no necesito esto... 
//    $contentSms = new \MailContent();
//    $contentSms->idMail = $mail->idMail;
//    $contentSms->typecontent = $this->autoresponder->AutoresponderContent->type;
//    $contentSms->content = $this->autoresponder->AutoresponderContent->content;
//    $contentSms->plaintext = $plainText->getPlainText($html);
//    $contentSms->createdBy = $this->autoresponder->AutoresponderContent->createdBy;
//    $contentSms->updatedBy = $this->autoresponder->AutoresponderContent->updatedBy;
//    if (!$contentMail->save()) {
//      foreach ($contentMail->getMessages() as $msg) {
//        $this->logger->log("Message: {$msg}");
//        throw new \InvalidArgumentException($msg);
//      }
//    }
//    return true;
    
     
    return true;
  }

}
