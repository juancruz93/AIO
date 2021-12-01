<?php

namespace Sigmamovil\Wrapper;

ini_set('memory_limit', '768M');

class TrackWrapper extends \BaseWrapper {

  public function openContact($idMail, $idContact) {
    $where = array("idContact" => (Int) $idContact, "idMail" => $idMail);
    $mxc = \Mxc::findFirst([$where]);

    if (!$mxc) {
      throw new \Exception("Ocurrio un problema consultado el mail {$idMail} con el contacto {$idContact}");
    }
    if (isset($mxc->totalOpening)) {
      $mxc->totalOpening += 1;
    } else {
      $mxc->totalOpening = (int) 1;
    }
    if ($mxc->open == 0) {
      $mxc->open = time();
      if (!$mxc->save()) {
        foreach ($mxc->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      return true;
    } else {
      if (!$mxc->save()) {
        foreach ($mxc->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      return false;
    }
  }

  public function openMail($idMail, $new = true) {
    $mail = \Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));

    if (!$mail) {
      throw new \Exception("Ocurrio un problema consultado el mail {$idMail}");
    }
    if ($new) {
      $open = \Mxc::count([["open" => ['$gte' => (int) 1], "idMail" => $idMail]]);
      $mail->uniqueOpening = $open;
      $mail->totalOpening += 1;
    } else {
      $mail->totalOpening += 1;
    }

    if (!$mail->save()) {
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function getMail($idMail) {
    return \Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
  }

  public function getAutomaticConfiguration($idAutomaticCampaign) {
    $configuration = \AutomaticCampaignConfiguration::findFirst(array("conditions" => "idAutomaticCampaign = ?0", "bind" => array($idAutomaticCampaign)));

    if (!$configuration) {
      throw new \Exception("Ocurrio un problema consultado la configuracion de la campaña {$idAutomaticCampaign}");
    }

    return $configuration;
  }

  public function canTrackBounce($objConsultMXC, $bounce, $email = 0, $objConsultMail = null) {
    switch ($bounce) {
      case 0:
        if ($objConsultMXC->bounced == 0 && $objConsultMXC->spam == 0 && $objConsultMXC->open == 0) {
          return true;
        }
        break;
      case 1:
        //&& $objConsultMail->status == 'sent'
        //if ($objConsultMXC->open == 0 && $objConsultMXC->bounced == 0 && $objConsultMXC->spam == 0 && (!isset($email->bounce) || $email->bounce == 0) && (!isset($email->spam) || $email->spam == 0)) {
    if($objConsultMXC->idMail == "4282"){
      $this->logger->log(print_r($objConsultMXC,true));
      }
    if ($objConsultMXC->open == 0 && $objConsultMXC->bounced == 0 && $objConsultMXC->spam == 0) {
      if($objConsultMXC->idMail == "4282"){
      $this->logger->log("yes verywey mondongo");
        }
          return true;
        }
        break;
      case 2:
        //if ($objConsultMXC->open == 0 && $objConsultMXC->bounced == 0 && $objConsultMXC->spam == 0 && (!isset($email->bounce) || $email->bounce == 0) && (!isset($email->spam) || $email->spam == 0)) {
      if ($objConsultMXC->open == 0 && $objConsultMXC->bounced == 0 && $objConsultMXC->spam == 0) {
          return true;
        }
        break;
    }
    return false;
  }

  public function canInsBounceMail($email) {
    $where = array("email" => $email);
    $bounceMail = \Bouncedmail::findFirst([$where]);
    return $bounceMail;
  }

  public function insMxc($mxc, $date, $content, $spam = false) {
    if (!$spam) {
      $mxc->bounced = (string) $date;
      $mxc->bounced_type = "greenArrow";
    } else {
      $mxc->spam = (string) $date;
    }

    $mxc->bouncedCode = $content['bounce_code'];
    if (!$mxc->save()) {
      foreach ($mxc->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return true;
  }

  public function insMail($mail, $date, $spam = false) {
    if (!$spam) {
      $mail->bounced = $mail->bounced + 1;
    } else {
      $mail->spam = $mail->spam + 1;
    }
    if (!$mail->save()) {
      foreach ($mail->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return true;
  }

  public function insEmail($email, $date, $spam = false) {
    if (!$spam) {
      $email->bounced = $date;
    } else {
      $email->spam = $date;
    }
    if (!$email->save()) {
      foreach ($email->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return true;
  }

  public function setBouncedMxc($mxc) {
    $mxc->bounced = 0;
    $mxc->spam = 0;
    $mxc->bouncedCode = 0;
    if (!$mxc->save()) {
      foreach ($mxc->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return true;
  }

  public function setEmail($email) {
    $email->bounced = 0;
    $email->spam = 0;
    if (!$email->save()) {
      foreach ($email->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return true;
  }

  public function instBouncedMail($content) {

    if (!$this->canInsBounceMail($content['email'])) {
      $bounce = new \Bouncedmail();
      $date = new \DateTime();

      $bounce->email = $content['email'];
      $bounce->datetime = $date->format('Y-m-d H:i:s');
      $bounce->description = $content['bounce_text'];
      $bounce->type = $content['event_type'];
      $bounce->code = $content['bounce_code'];

      if (!$bounce->save()) {
        foreach ($bounce->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
    return true;
  }

  public function trackSoftBounceEvent($idMail, $idContact, $content, $date = null) {
    $customLogger = new \LogsxMtaxEvent();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $idMail;
    $customLogger->idContact = $idContact;
    $customLogger->id = $content['id'];
    $customLogger->sendid = $content['sendid'];
    $customLogger->email = $content['email'];
    $customLogger->bounce_code = $content['bounce_code'];
    $customLogger->bounce_type = $content['bounce_type'];
    $customLogger->type = $content['event_type'];
    $customLogger->click_tracking_id = $content['click_tracking_id'];
    $customLogger->typeName = "RegisterBounceSoftMethod";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    
    if ($content['bounce_code'] == 10 || $content['bounce_code'] == 90 || $content['bounce_code'] == 200) {
      $this->setStatusCxcl($idMail, $content['email'], 'bounced');
      //$this->bouncedCxcl($idMail,$content['bounce_code'], $content['email']);
    }
    $where = array("idMail" => $idMail, "idContact" => (Int) $idContact);
    $mxc = \Mxc::findFirst([$where]);
    unset($where);

    if ($date == null) {
      $date = time();
    }

    if (!$mxc) {
      throw new \Exception("Ocurrio un problema consultado en la tabla mxmc con el mail {$idMail} y el contacto {$idContact}");
    }

    if ($this->canTrackBounce($mxc, 0)) {
      $this->insMxc($mxc, $date, $content);
    }

    $this->saveBounceEvent($idMail, $content);

    unset($mxc);
    unset($date);
  }

  public function trackHardBounceEvent($idMail, $idContact, $content, $date = null) {
    $customLogger = new \LogsxMtaxEvent();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $idMail;
    $customLogger->idContact = $idContact;
    $customLogger->id =  $content['id'];
    $customLogger->sendid = $content['sendid'];
    $customLogger->email = $content['email'];
    $customLogger->bounce_code = $content['bounce_code'];
    $customLogger->bounce_type = $content['bounce_type'];
    $customLogger->type = $content['event_type'];
    $customLogger->click_tracking_id = $content['click_tracking_id'];
    $customLogger->typeName = "RegisterBounceHardMethod";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();

    if ($content['bounce_code'] == 10 || $content['bounce_code'] == 90 || $content['bounce_code'] == 200) {
      $this->setStatusCxcl($idMail, $content['email'], 'bounced');
      //$this->bouncedCxcl($idMail,$content['bounce_code'], $content['email']);
    }

    //$this->bouncedCxcl($idMail,$content['bounce_code'], $content['email']);
    
    //
    $this->logger->log(print_r($content, true));
    $where = array("idMail" => $idMail, "idContact" => (Int) $idContact);
    $mxc = \Mxc::findFirst([$where]);
    $mail = \Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
    $email = $content["email"];
    $whereEmail = array("email" => array('$regex' => ".*$email.*"));
    $email = \Email::findFirst([$whereEmail]);
    $this->logger->log(print_r($email->email, true));
    unset($whereEmail);

    if (!$mxc) {
      throw new \Exception("Ocurrio un problema consultado en la tabla mxc con el mail {$idMail} y el contacto {$idContact}");
    }

    if (!$mail) {
      throw new \Exception("Ocurrio un problema consultado en la tabla mail con el mail {$idMail}");
    }

    if (!$email) {
      throw new \Exception("Ocurrio un problema consultado en la tabla email con el mail {$content["email"]}");
    }

    if ($date == null) {
      $date = time();
    }

    try {
      if ($this->canTrackBounce($mxc, 1, $email, $mail)) {
//        $cxcl = \Cxcl::find(["conditions" => "idContact = ?0", "bind" => [0 => $idContact]]);
//        foreach ($cxcl as $contact) {
//          if (!($contact->status == 'spam' or $contact->status == 'blocked')) {
//            $contact->status = 'bounced';
//          }
//          $contact->bounced = (string) time();
//          $contact->save();
//        }
//        $cxcl = \Cxcl::find(["conditions" => "idContact = ?0", "bind" => [0 => $idContact]]);
//        foreach ($cxcl as $key) {
//          $contactlist = \Contactlist::findFirst(["conditions" => "idContactlist = ?0", "bind" => [0 => $key->idContactlist]]);
//          if ($contactlist) {
//            $contactlist->cbounced +=1;
//            $contactlist->cactive -=1;
//            $contactlist->save();
//          }
//        }

        $this->db->begin();
        if (!$this->insMxc($mxc, $date, $content)) {
          $this->db->rollback();
        }
        if (!$this->insMail($mail, $date)) {
          $this->setBouncedMxc($mxc);
          $this->db->rollback();
        }
        if (!$this->insEmail($email, $date)) {
          $this->setBouncedMxc($mxc);
          $this->db->rollback();
        }
        if (!$this->instBouncedMail($content)) {
          $this->setBouncedMxc($mxc);
          $this->setEmail($email);
          $this->db->rollback();
        }
        $this->db->commit();
        $this->saveBounceEvent($idMail, $content);
        return true;
      }
    } catch (\InvalidArgumentException $ex) {
      $this->setBouncedMxc($mxc);
      $this->logger->log("Error  InvalidArgumentException trackHardBounceEvent ... {$ex}");
    } catch (Exception $ex) {
      $this->setBouncedMxc($mxc);
      $this->logger->log("Error  Exception trackHardBounceEvent ... {$ex}");
    }
  }

  public function setStatusCxcl($idMail, $email, $type) {
    
    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);
    $target = json_decode($mail->target);
    $idAccount = $mail->Subaccount->Account->idAccount;
    unset($mail);
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          //foreach ($target->contactlists as $key) {
          $contact = \Contact::findFirst([["email" => (string) $email, "idAccount" => $idAccount, "deleted" => 0]]);
          unset($idAccount);

          unset($email);
          //foreach ($contact as $value){
            $cxcl = \Cxcl::find(["conditions" => "idContact = ?0 AND deleted = 0", "bind" => [0 => (int) $contact->idContact]]);
            unset($contact);
            foreach ($cxcl as $key => $content) {
              if ($type == "bounced") {
                if($content->blocked == 0){
                  $content->status = 'bounced';
                  $content->bounced = (string) time();
                }
              } elseif ($type == 'spam') {
                if($content->blocked == 0){
                  $content->status = 'spam';
                  $content->spam = (string) time();
                }
              }
              $content->save();
              unset($content);
            }
            unset($cxcl);
          //}
          //unset($contact);
        }
        break;
      case "segment":
        $this->logger->log("Segment Bounced 1 ... {$email}");
        if (isset($target->segment)) {
          $this->logger->log("Segment Bounced 1 ... {$idAccount}");
          $this->setStatusCxclBySegments($target->segment, $email, $idAccount, $type);
        }
        break;
      default:
        throw new \Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
    }
    unset($target);
  }

  public function setStatusCxclBySegments($listSegment, $email, $idAccount, $type) {
    foreach ($listSegment as $key) {
      $idSegment = (string) $key->idSegment;
      $segment = \Segment::findFirst([["idSegment" => $idSegment]]);
      foreach ($segment->contactlist as $k) {
        $contacts = \Contact::find([["email" => (string) $email, "idAccount" => $idAccount, "deleted" => 0]]);
        foreach ($contacts as $contact) {
          $cxcl = \Cxcl::findFirst(["conditions" => "idContact = ?0 AND idContactlist = ?1", "bind" => [0 => $contact->idContact, 1 => $k["idContactlist"]]]);
          if($cxcl != false){
            //exit;
            //foreach ($cxcl as $contact) {
            if ($type == "bounced") {
              $cxcl->status = 'bounced';
              $cxcl->bounced = (string) time();
            } elseif ($type == 'spam') {
              $cxcl->status = 'spam';
              $cxcl->spam = (string) time();
            }

            $cxcl->save();
          }
        }
        unset($contacts);
      }
      unset($segment);
    }
    unset($listSegment);
  }

  public function trackScompBounceEvent($idMail, $idContact, $content, $date = null) {
    $customLogger = new \LogsxMtaxEvent();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idMail = $idMail;
    $customLogger->idContact = $idContact;
    $customLogger->id =  $content['id'];
    $customLogger->sendid = $content['sendid'];
    $customLogger->email = $content['email'];
    $customLogger->type = $content['event_type'];
    $customLogger->click_tracking_id = $content['click_tracking_id'];
    $customLogger->typeName = "RegisterScompMethod";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    
    $this->setStatusCxcl($idMail, $content['email'], 'spam');

    $where = array("idMail" => $idMail, "idContact" => (Int) $idContact);
    $mxc = \Mxc::findFirst([$where]);
    $mail = \Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idMail)));
    $whereEmail = array("email" => $content["email"]);
    $email = \Email::findFirst([$whereEmail]);

    if (!$mxc) {
      throw new \Exception("Ocurrio un problema consultado en la tabla mxc con el mail {$idMail} y el contacto {$idContact}");
    }

    if (!$mail) {
      throw new \Exception("Ocurrio un problema consultado en la tabla mail con el mail {$idMail}");
    }

    if (!$email) {
      throw new \Exception("Ocurrio un problema consultado en la tabla email con el mail {$content["email"]}");
    }

    if ($date == null) {
      $date = time();
    }

    try {
      if ($this->canTrackBounce($mxc, 2, $email, $mail)) {

        $this->db->begin();

        if (!$this->insMxc($mxc, $date, $content, true)) {
          $this->db->rollback();
        }
        if (!$this->insMail($mail, $date, true)) {
          $this->setBouncedMxc($mxc);
          $this->db->rollback();
        }
        if (!$this->insEmail($email, $date, true)) {
          $this->setBouncedMxc($mxc);
          $this->db->rollback();
        }
        if (!$this->instBouncedMail($content)) {
          $this->setBouncedMxc($mxc);
          $this->setEmail($email);
          $this->db->rollback();
        }

        $this->db->commit();

        $this->saveBounceEvent($idMail, $content);

        return true;
      }
    } catch (Exception $ex) {
      $this->setBouncedMxc($mxc);
      $this->logger->log("Error  Exception trackHardBounceEvent ... {$ex}");
    } catch (\InvalidArgumentException $ex) {
      $this->setBouncedMxc($mxc);
      $this->logger->log("Error  InvalidArgumentException trackHardBounceEvent ... {$ex}");
    }
  }

  public function saveBounceEvent($idMail, $content) {
    try {
      $mail = \Mail::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($idMail)
      ));

      if (!$mail) {
        throw new \InvalidArgumentException("El envío no existe realizado no existe en la base de datos");
      }

      $bouncedemail = \Bouncedmail::findFirst([array(
              "email" => $content["email"]
      )]);
    
      $idAccount = $mail->Subaccount->Account->idAccount;
      $flag = true;
      if ($bouncedemail != false) {
    if(!isset($bouncedemail->idAccount) || $bouncedemail->idAccount == NULL){
        $bouncedemail->idAccount = array();
      }
    if(!isset($bouncedemail->idMail) || $bouncedemail->idMail == NULL){
      $bouncedemail->idMail = array();  
    }
        foreach ($bouncedemail->idAccount as $idAcc) {
          if ($idAcc == $idAccount) {
            $flag = false;
            break;
          }
        }

        if ($flag) {
          array_push($bouncedemail->idAccount, (int) $mail->Subaccount->Account->idAccount);
          array_push($bouncedemail->idMail, (int) $mail->idMail);
      $bouncedemail->save();
        }
        return true;
      }

      $bouncedmail = new \Bouncedmail();
      $contactManager = new \Sigmamovil\General\Misc\ContactManager();
      $nextIdAnswer = $contactManager->autoIncrementCollection("id_bouncedmail");

      $bouncedmail->idBouncedMail = $nextIdAnswer;
      $bouncedmail->idAccount = [(int) $mail->Subaccount->Account->idAccount];
      $bouncedmail->datetime = date("Y-m-d H:i:s", time());
      $bouncedmail->idMail = [(int) $mail->idMail];
      $bouncedmail->email = $content["email"];
      $bouncedmail->source = "GreenArrow";
      $bouncedmail->status = "blocked";
      $bouncedmail->type = $content['event_type'];
      $bouncedmail->code = $content['bounce_code'];
      $bouncedmail->description = $content['bounce_text'];
      $bouncedmail->created = time();
      $bouncedmail->updated = time();
    

    if (!$bouncedmail->save()) {
          foreach ($bouncedmail->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      
      
      $this->validateInDeliverableEmail($bouncedmail->email, $mail->Subaccount->Account->idAccount);
    } catch (\InvalidArgumentException $ex) {
      $this->logger->log($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->log("Ha ocurrido un error {$ex->getMessage()} \n {$ex->getTraceAsString()}");
    }
  }

  private function validateInDeliverableEmail($email, $idAccount) {
    $deliverableEmail = \DeliverableEmail::findFirst(array(
                "conditions" => array(
                    "email" => (string) $email,
                    "idAccount" => (int) $idAccount
                )
    ));

    if ($deliverableEmail) {
      if (!$deliverableEmail->delete()) {
        $messages = $deliverableEmail->getMessages;
        foreach ($messages as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }
}