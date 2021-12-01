<?php

/**
 * Description of AutomaticCampaignStep
 *
 * @author desarrollo3
 */
class AutomaticCampaignStep extends Modelbase {

  public $idAutomaticCampaignStep,
          $idMailTemplate,
          $idSmsTemplate,
          $idContact,
          $idAutomaticCampaign,
          $scheduleDate,
          $idNode,
          $negation,
          $open,
          $totalOpening,
          $click,
          $bounced,
          $spam,
          $totalClicks,
          $uniqueClicks,
          $bouncedCode,
          $updated,
          $updatedBy,
          $created,
          $createdBy,
          $status,
          $statusSms,
          $beforeStep,
          $unsubscribed;

  public function getSource() {
    return "automatic_campaign_step";
  }

  public function initialize() {
    $this->belongsTo("idAutomaticCampaign", "AutomaticCampaign", "idAutomaticCampaign");
    $this->belongsTo("idForm", "Form", "idForm");
    $this->belongsTo("idLandingPage", "LandingPage", "idLandingPage");
    $this->belongsTo("idMail", "Mail", "idMail"); 
    $this->belongsTo("idSms", "Sms", "idSms");
    $this->belongsTo("idSmsTwoway", "Smstwoway", "idSmsTwoway");
    $this->belongsTo("idSurvey", "Survey", "idSurvey");
  }

}
