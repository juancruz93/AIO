<?php

namespace Sigmamovil\Wrapper;

use MongoDB\Driver\Query;
use Psr\Log\InvalidArgumentException;

class ScheduledWrapper extends \BaseWrapper {

  private $sendings;

  public function findSendings($initialSMS, $initialMail, $data) {

    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;



//PARA MAIL
    (($initialMail > 0) ? $initialMail = ($initialMail * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $whereNameMail = "";
    if (isset($data) && isset($data->name) && $data->name != 1) {
      $whereNameMail = " AND Mail.name LIKE '%" . $data->name . "%'";
    }

    $this->datamails = $this->modelsManager->createBuilder()
            ->from('Mail')
            ->where("(Mail.status='scheduled' OR Mail.status='paused' OR Mail.status='sending') AND Mail.deleted = 0 AND Mail.idSubaccount  = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount . $whereNameMail)
            ->orderBy("Mail.created DESC")
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, $initialMail)
            ->getQuery()
            ->execute();

//    exit;
    $this->totalsmails = $this->modelsManager->createBuilder()
            ->from('Mail')
            ->where("(Mail.status = 'scheduled' OR Mail.status = 'paused' OR Mail.status = 'sending') AND Mail.deleted = 0 AND Mail.idSubaccount = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount . $whereNameMail)
            ->orderBy("Mail.created DESC")
            ->getQuery()
            ->execute();
    $this->modelData();
    //FIN MAIL
    //PARA SMS
    (($initialSMS > 0) ? $initialSMS = ($initialSMS * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $whereNameSms = "";
    if ($data->name != 1) {
      $whereNameSms = " AND Sms.name LIKE '%" . $data->name . "%'";
    }
    $this->datasms = $this->modelsManager->createBuilder()
            ->from('Sms')
            ->where("(Sms.status = 'scheduled' OR Sms.status = 'paused' OR Sms.status = 'sending') AND Sms.logicodeleted = 0 AND Sms.idSubaccount = " . $this->user->Usertype->idSubaccount . $whereNameSms)
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, $initialSMS)
            ->orderBy("Sms.created DESC")
            ->getQuery()
            ->execute();
    $this->totalssms = $this->modelsManager->createBuilder()
            ->from('Sms')
            ->where("(Sms.status = 'scheduled' OR Sms.status = 'paused' OR Sms.status = 'sending') AND Sms.logicodeleted = 0 AND Sms.idSubaccount = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount . $whereNameSms)
            ->orderBy("Sms.created DESC")
            ->getQuery()
            ->execute();
    $this->modelData();
    //FIN SMS
  }

  public function modelData() {
    $this->sendings['mail'] = array("total" => count($this->totalsmails), "total_pages" => ceil(count($this->totalsmails) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $this->sendings['sms'] = array("total" => count($this->totalssms), "total_pages" => ceil(count($this->totalssms) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->datamails as $data) {
      $mail = new \stdClass();
      $mail->idMail = $data->idMail;
      $mail->name = $data->name;
      $mail->created = $data->created;
      $mail->updated = $data->updated;
      $mail->createdBy = $data->createdBy;
      $mail->updatedBy = $data->updatedBy;
      $mail->status = $data->status;
      if ($data->status == "scheduled") {
        $mail->statusEsp = "Programado";
      } else if ($data->status == "paused") {
        $mail->statusEsp = "Pausado";
      } else if ($data->status == "sending") {
        $mail->statusEsp = "En proceso de envío";
      }if ($data->status == "draft") {
        $mail->statusEsp = "Borrador";
      }

      $mail->deleted = $data->deleted;
      $mail->scheduleDate = $data->scheduleDate;
      $mail->quantitytarget = $data->quantitytarget;

      $arrmail[] = $mail;
    }
    $this->sendings['mail']['items'] = $arrmail;

    foreach ($this->datasms as $data) {
      $sms = new \stdClass();
      $sms->idSms = $data->idSms;
      $sms->name = $data->name;
      $sms->created = $data->created;
      $sms->updated = $data->updated;
      $sms->createdBy = $data->createdBy;
      $sms->updatedBy = $data->updatedBy;
      $sms->status = $data->status;
      if ($data->status == "scheduled") {
        $sms->statusEsp = "Programado";
      } else if ($data->status == "paused") {
        $sms->statusEsp = "Pausado";
      } else if ($data->status == "sending") {
        $sms->statusEsp = "En proceso de envío";
      }if ($data->status == "draft") {
        $sms->statusEsp = "Borrador";
      }

//      $sms->deleted = $data->deleted;
      $sms->startdate = $data->startdate;
      $sms->sent = $data->sent;

      $arrsms[] = $sms;
    }
    $this->sendings['sms']['items'] = $arrsms;
  }

  function getSendings() {
    return $this->sendings;
  }

}
