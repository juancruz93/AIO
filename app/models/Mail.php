<?php

class Mail extends Modelbase {

  public $idMail,
          $idSubaccount,
          $idEmailsender,
          $idNameSender,
          $idReplyTo,
          $idAutoresponder,
          $idSurvey,
          $idLandingPage,          
          $categorycampaign,
          $name,
          $sender,
          $replyto,
          $subject,
          $target,
          $content,
          $shippingdate,
          $status,
          $test,
          $type,
          $quantitytarget,
          $deleted,
          $plaintext,
          $attachment,
          $scheduleDate,
          $confirmationDate,
          $gmt,
          $sentprocessstatus,
          $notificationEmails,
          $idAutomaticCampaign,
          $postFacebook,
          $googleAnalytics,
          $singleMail,
          $messagesSent,
          $pdf,
          $canceleduser,
          $alldb,
          $typeUnsuscribed;

  public function initialize() {
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idEmailsender", "Emailsender", "idEmailsender");
    $this->belongsTo("idEmailname", "Emailname", "idEmailname");
    $this->belongsTo("idReplyTo", "ReplyTos", "idReplyTo");
    $this->belongsTo("idAutoresponder", "Autoresponder", "idAutoresponder");
    $this->belongsTo("idNameSender", "NameSender", "idNameSender");
    $this->belongsTo("idAutomaticCampaign", "AutomaticCampaign", "idAutomaticCampaign");
    $this->belongsTo("idSurvey", "Survey", "idSurvey");
    $this->belongsTo("idLandingPage", "LandingPage", "idLandingPage");
    $this->hasMany("idMail", "mxmc", "idMail");
    $this->hasMany("idMail", "Mailattachment", "idMail");
    $this->hasOne("idMail", "MailContent", "idMail");
    $this->hasOne("idMail", "Post", "idMail");
    $this->hasOne("idMail", "MailStatisticNotification", "idMail");
    $this->belongsTo("idMail", "AutomaticCampaignStep", "idMail");
  }

  public function getCleanMail() {
    $obj = new stdClass();
    $obj->idMail = $this->idMail;
    $obj->idSubaccount = $this->idSubaccount;
    $obj->idEmailsender = $this->idEmailsender;
    $obj->idNameSender = $this->idNameSender;
    $obj->idReplyTo = $this->idReplyTo;
    $obj->categorycampaign = $this->categorycampaign;
    $obj->name = $this->name;
    $obj->sender = $this->sender;
    $obj->subject = $this->subject;
    $obj->scheduleDate = $this->scheduleDate;
    $obj->confirmationDate = $this->confirmationDate;
    $obj->gmt = $this->gmt;
    $obj->target = $this->target;
    $obj->created = $this->created;
    $obj->updated = $this->updated;
    $obj->attachment = $this->attachment;
    $obj->status = $this->status;
    $obj->quantitytarget = $this->quantitytarget;
    $obj->test = $this->test;
    $obj->uniqueOpening = $this->uniqueOpening;
    $obj->deleted = $this->deleted;
    $obj->totalOpening = $this->totalOpening;
    $obj->uniqueClicks = $this->uniqueClicks;
    $obj->previewData = $this->previewData;
    $obj->bounced = $this->bounced;
    $obj->totalClicks = $this->totalClicks;
    $obj->spam = $this->spam;
    $obj->messagesSent = $this->messagesSent;
    $obj->sentprocessstatus = $this->sentprocessstatus;
    $obj->idNameSender = $this->idNameSender;
    $obj->singleMail = $this->singleMail;
    $obj->alldb = $this->alldb;
    $obj->idAccount = $this->Subaccount->idAccount;
    $obj->typeAccount = $this->Subaccount->Account->registerType;
    $obj->typeUnsuscribed = $this->typeUnsuscribed;
    return (array) $obj;
  }

}
