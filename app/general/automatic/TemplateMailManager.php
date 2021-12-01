<?php

namespace Sigmamovil\General\Automatic;
require_once(__DIR__ . "/../../bgprocesses/sender/CustomfieldManagerSms.php");

class TemplateMailManager {

  protected $mailTemplate;
  protected $smsTemplate;
  protected $campaign;
  protected $subAccount;
  protected $targetMail;
  protected $targetSms;
  protected $contact;
  protected $dataCampaign;
  protected $db;
  protected $mailPlainText;
  protected $idContactlist;
  protected $inIdcontact;
  protected $countTarget;

  public function __construct(\AutomaticCampaign $campaign) {
    $this->campaign = $campaign;
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }

  public function setSubAccount(\Subaccount $subAccount) {
    $this->subAccount = $subAccount;
  }

  /*public function setTargetSms($sendData) {
    
   $objReturn = new \stdClass();
   if ($sendData->list->id == 1) {  
       
       $objReturn->type = "contactlist";
       $data = json_decode(json_encode($sendData), true);
      
       for($i = 0; $i < count($data['selecteds']); $i ++){
        $objReturn->contactlists[$i]->idContactlist =  $data['selecteds'][$i]['idContactlist'];
        $objReturn->contactlists[$i]->name = $data['selecteds'][$i]['name'];
        $objReturn->contactlists[$i]->idContactlistCategory =  $data['selecteds'][$i]['idContactlistCategory'];
       }      
   } else {
      
       $objReturn->type = "segment";
       $data = json_decode($sendData, true);
       for($i = 0; $i < count($data['selecteds']); $i ++){
        $objReturn->segment[$i]->idSegment = $data['selecteds'][$i]['idSegment'];
        $objReturn->segment[$i]->name = $data['selecteds'][$i]['name'];           
       }
   }
   
   $this->targetSms = json_encode($objReturn);    
  }*/

  public function setDataMail($sendDataCampaign) {
    $this->dataCampaign = $sendDataCampaign;
  }

  public function setTarget($targetMail) {
    $this->targetMail = $targetMail;
  }
  
  public function setTargetSms($targetSms) {
    $this->targetSms = $targetSms;
  }

  public function setContentTemplate() {
    $idMailTemplate = $this->dataCampaign->mailtemplate->idMailTemplate;
    $idAccount = $this->subAccount->Account->idAccount;
//    $MailContent = \MailTemplate::findFirst(array("conditions" => "idMailTemplate = ?0 and idAccount = ?1 and deleted =?2 ", "bind" => array($idMailTemplate,$idAccount,0)));
    $MailContent = \MailTemplateContent::findFirst(array("conditions" => "idMailTemplate = ?0  ", "bind" => array($idMailTemplate)));
    if (!$MailContent) {
      throw new \InvalidArgumentException("La plantilla de correo '{$this->dataCampaign->mailtemplate->name}' ha sido eliminado por favor verifique la información.");
    }
    $html = $this->getContentHtml($MailContent->content);
    $plainText = new \PlainText();
    $this->mailPlainText = $plainText->getPlainText($html);
    $this->mailTemplate = $MailContent;
  }

  public function setContentTemplateSms() {
    $idMailTemplateSms = $this->dataCampaign->smstemplate->idSmsTemplate;
    $this->smsTemplate = \SmsTemplate::findFirst([
                "conditions" => "idSmsTemplate = ?0",
                "bind" => [0 => $idMailTemplateSms]]);
  }

  public function cloneSms() {
    $this->getIdContaclist();
    $this->countTargetSms();
    $this->getAllCxcl();
    
     try {

        $amount = 0;
        foreach ($this->subAccount->saxs as $key) {
            if ($key->idServices == 1) {
                $amount = $key->amount;
                $totalAmount = $key->totalAmount;
                $subaccountName = $this->subAccount->name;
                $accountName = $this->subAccount->Account->name;
                $arraySaxs = array(
                    "amount" => $amount,
                    "totalAmount" => $totalAmount,
                    "subaccountName" => $subaccountName,
                    "accountName" => $accountName,
                );
            }
        }

        if ($amount <= 0) {
          $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
          $sendMailNot->sendSmsNotification($arraySaxs);
          throw new \InvalidArgumentException("No tienes saldo disponible para realizar envíos de SMS de campaña automatica");
        }                      
          
        if ($this->countTarget > $amount) {
          $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
          $sendMailNot->sendSmsNotification($arraySaxs);
          throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de SMS de campaña automatica.");
        } 
            
        $this->db->begin();
        $sms = new \Sms();
        $sms->target = $this->countTarget; 
        $sms->name = $this->dataCampaign->smstemplate->name;
        $sms->idSubaccount =  $this->subAccount->idSubaccount;
        $sms->idSmsCategory = $this->dataCampaign->smscategory->idSmsCategory;
        $sms->status = 'scheduled';
        $sms->confirm = 1;
        $sms->logicodeleted = 0;
        $sms->type = "automatic"; 
        $sms->startdate = $this->campaign->startDate;
        $sms->receiver = $this->targetSms;
        $sms->message = $this->dataCampaign->smstemplate->content;
        $sms->sent = 0;
        $sms->notification = 0;
        $sms->email = null;
        $sms->divide = 0;
        $sms->sendingTime = null;
        $sms->quantity = null;
        $sms->timeFormat = null;
        $sms->dateNow = 1;
        $sms->gmt = "-0500";
        $sms->idAutomaticCampaign = $this->campaign->idAutomaticCampaign;
        $sms->createdBy = $this->campaign->createdBy;
        $sms->updatedBy = $this->campaign->updatedBy;

        if (!$sms->save()) {
          $this->db->rollback();
          foreach ($sms->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        
        /*$where = array("idContact" => ['$in' => $this->inIdcontact]);
        $this->contact = \Contact::find(array($where));
        unset($this->inIdcontact);
        
        if($this->contact){ 
          foreach($this->contact as $c){
            $this->validatePhoneSmsFailed($sms, $c->idContact, $c->phone, $c->indicative);
          }
        }
        unset($this->contact);            
        $this->db->commit();

        // Cuenta los erroneos en sms_failed
        $SmsFailed = \SmsFailed::count(["conditions" => "idSms = ?0", 
                                       "bind" => [0 => (int) $sms->idSms]]);
        //Resta los registros erroneos del target de envío
        $sms->target = $sms->target - $SmsFailed;

        if (!$sms->save()) {
          foreach ($sms->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
          $this->trace("fail", "No se cambio el target error desde campaña automatica");
        }*/
        return $sms;
      } catch (\InvalidArgumentException $msg) {
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error  creando nodo sms de campaña automatica: " . $msg->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($msg->getTrace());        
        
      } catch (\Exception $e) {
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error creando nodo sms de campaña automatica:: " . $e->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($e->getTrace());  
      } 
    
    
    
  }
  
    public function validatePhoneSmsFailed($sms, $idcontact, $phone, $country) {
    $phoneactual = $phone;
    $savesmsfail = 0;
    $messagefail = "";
    $phone = str_replace(' ', '', $phone);

    // valida si el indicativo que ingresa es correcto con el pais
    $Country = \Country::findFirst(["conditions" => "phoneCode = ?0", "bind" => [0 => (int) $country]]);
    if (!$Country) {
      $messagefail = "No se encuentra el indicativo del pais.";
      $savesmsfail = 1;
    }
    // valida si los primeros 3 numeros del numero es correcto de acuerdo con el indicativo del pais
    $phoneindi = substr($phone, 0, 3);
    $PhonePrefix = \PhonePrefix::findFirst(["conditions" => "idCountry = ?0 and phonePrefix = ?1", "bind" => [0 => (int) $Country->idCountry, 1 => (string) $phoneindi]]);
    if (!$PhonePrefix) {
      $messagefail = $messagefail . "Verifique que el número sea valido, de acuerdo al indicativo del país.";
      $savesmsfail = 1;
    }

    if ($savesmsfail == 1) {

      $smsfailed = new \SmsFailed();
      $smsfailed->idSms = $sms->idSms;
      $smsfailed->idContact = (int) $idcontact;
      $smsfailed->indicative = $country;
      $smsfailed->message = $sms->message;
      $smsfailed->phone = $phoneactual;
      $smsfailed->count = 1;
      $smsfailed->detail = $messagefail;
      $smsfailed->type = "contact";


      if (!$smsfailed->save()) {
        foreach ($smsfailed->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
        $this->trace("fail", "error al guardar en smsfailed");
      }
    }
    unset($PhonePrefix);
    unset($Country);
  }

  public function cloneMail() {
    $this->validate();
    $amount = 0;
    $sending = false;
    foreach ($this->subAccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == "sending") {
        $sending = true;
        $amount = $key->amount;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->subAccount->name;
        $accountName = $this->subAccount->Account->name;
        $arraySaxs = array(
          "amount" => $amount,
          "totalAmount" => $totalAmount,
          "subaccountName" => $subaccountName,
          "accountName" => $accountName,
        );
      }
    }

    if ($sending && $amount <= 0) {
      $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      $sendMailNot->sendMailNotification($arraySaxs);
      throw new \InvalidArgumentException("No tienes saldo disponible para realizar envíos de Mail de campaña automatica");
    }                      

    if ($sending && $this->countTarget > count($this->inIdcontact)) {
      $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
      $sendMailNot->sendMailNotification($arraySaxs);
      throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de Mail de campaña automatica.");
    } 
    $this->db->begin();
    $mail = new \Mail();
    $mail->idSubaccount = $this->subAccount->idSubaccount;
    $mail->idEmailsender = $this->dataCampaign->senderEmail->idEmailsender;
    $mail->idNameSender = $this->dataCampaign->senderName->idNameSender;
    $mail->idAutomaticCampaign = $this->campaign->idAutomaticCampaign;
    $mail->name = $this->campaign->name;
    $mail->replyto = ($this->dataCampaign->replyto == '') ? null : $this->dataCampaign->replyto;
    $mail->subject = $this->dataCampaign->subject;
    $mail->scheduleDate = $this->campaign->startDate;
    $mail->confirmationDate = $this->campaign->startDate;
    $mail->gmt = $this->campaign->gmt;
    $mail->target = $this->targetMail;
    $mail->type = 'automatic';
    $mail->test = 0;
    $mail->status = 'scheduled';
    $mail->quantitytarget = count($this->inIdcontact);
    if(isset($this->dataCampaign->idAssets)){
      $mail->attachment = 1;
    }
    if (!$mail->save()) {
      $this->db->rollback();
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    if($mail->attachment == 1){
      $assets = $this->dataCampaign->idAssets;
      foreach($assets as $asset){
          $attachment = new \Mailattachment();
          $attachment->idAsset = $asset->id;
          $attachment->idMail = $mail->idMail;
          $attachment->createdon = time();
          if (!$attachment->save()) {
              foreach ($attachment->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
              }
          }
      }
    }
    $contentMail = new \MailContent();
    $contentMail->idMail = $mail->idMail;
    $contentMail->typecontent = 'Editor';
    $contentMail->content = $this->mailTemplate->content;
    $contentMail->plaintext = $this->mailPlainText;
    $contentMail->createdBy = $this->campaign->createdBy;
    $contentMail->updatedBy = $this->campaign->updatedBy;
    if (!$contentMail->save()) {
      $this->db->rollback();
      foreach ($contentMail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    $this->db->commit();
    return $mail;
  }

  public function getContactSms() {
    
  }
  
  public function countTargetSms(){
    $idContactlist = implode(",", $this->idContactlist);
    $sql = "SELECT idContact FROM cxcl where idContactlist IN ({$idContactlist}) and deleted = 0 and unsubscribed = 0 and blocked = 0";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    $contact = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $contact[] = (int) $cxcl[$i]['idContact'];
    }
    $where = array("idContact" => ['$in' => $contact], "phone" => ['$nin' => ["", null, "null"]], "indicative" => ['$nin' => ["", null, "null"]], "blockedPhone" => ['$in' => ["", null, "null"]]);
    $this->countTarget = \Contact::count([$where]);
    
  }
  
  public function getIdContaclist() {
    $target = json_decode($this->targetSms);
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

  public function getAllCxcl() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT idContact FROM cxcl where idContactlist IN ({$idContactlist})";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    };
//    if (!isset($this->inIdcontact) || count($this->inIdcontact) < $this->limit) {
//      $this->flag = false;
//    }
    unset($sql);
    unset($cxcl);
  }

  protected function validate() {
    if (!isset($this->campaign)) {
      throw new \InvalidArgumentException("La campaña no se encuentra registrada, por favor verifique la información.");
    }
    if (!isset($this->subAccount)) {
      throw new \InvalidArgumentException("El subAccount no se encuentra registrado, por favor verifique la información.");
    }
    if (!isset($this->dataCampaign)) {
      throw new \InvalidArgumentException("La informacion del correo no se encuentra registrado, por favor verifique la información.");
    }
    if (!isset($this->targetMail)) {
      throw new \InvalidArgumentException("La target del correo no se encuentra registrada,por favor verifique la información.");
    }
    if (!isset($this->mailTemplate)) {
      throw new \InvalidArgumentException("La plantilla de correo no se encuentra registrada,por favor verifique la información.");
    }
  }

  public function getContentHtml($content) {
    $editor = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editor->setAccount($this->subAccount->Account);
    $editor->assignContent(json_decode($content));
    $html = $editor->render();
    return $html;
  }
  
  public function setInidContact($inIdContact) {
    $this->inIdcontact = $inIdContact;
  }

}
