<?php

class Account extends Modelbase {

  public $idAccountclassification,
          $idAccount,
          $idPaymentPlan,
          $idAccountCategory,
          $idAllied,
          $created,
          $updated,
          $publicDomain,
          $status,
          $accountingMode,
          $subscriptionEmailMode,
          $subscriptionSmsMode,
          $name,
          $phone,
          $idCity,
          $address,
          $tolerancePeriod,
          $email,
          $nit,
          $attachments,
          $url,
          $registerType,
          $hourInit,
          $hourEnd,
          $ip,
          $termsconditions;

  public function initialize() {
    $this->belongsTo("idAccountclassification", "Accountclassification", "idAccountclassification");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->belongsTo("idPaymentPlan", "PaymentPlan", "idPaymentPlan");
    $this->hasMany("idAccount", "User", "idAccount");
    $this->hasMany("idAccount", "Subaccount", "idAccount");
    $this->hasMany("idAccount", "Axc", "idAccount");
    $this->hasMany("idAccount", "Sendingcategory", "idAccount");
    $this->hasOne("idAccount", "AccountConfig", "idAccount");
    $this->hasOne("idAccount", "Usertype", "idAccount");
    $this->hasOne("idCity", "City", "idCity");
    $this->hasMany("idAccount", "Mail_category", "idAccount");
    $this->hasMany("idAccount", "FormCategory", "idAccount");
    $this->hasMany("idAccount", "MailTemplate", "idAccount");
    $this->hasMany("idAccount", "MailTemplateCategory", "idAccount");
    $this->hasMany("idAccount", "SmsTemplateCategory", "idAccount");
    $this->hasMany("idAccount", "SmsTemplate", "idAccount");
    $this->hasmany("idAccount", "AutomaticCampaignCategory", "idAccount");
    $this->hasOne("idAccountCategory", "AccountCategory", "idAccountCategory");
    $this->hasMany("idAccount", "SurveyCategory", "idAccount");
    $this->hasMany("idAccount", "DashboardImage", "idAccount");
    $this->hasMany("idAccount", "Dashboard", "idAccount");
    $this->hasMany("idAccount", "RatexRange", "idAccount");
    $this->hasMany("idAccount", "LandingPageTemplate", "idAccount");
    $this->hasMany("idAccount", "Mail_landing_page", "idAccount");
  }

  public function beforeValidationOnCreate() {
    parent::beforeValidationOnCreate();
    if (!isset($this->hourInit) || empty($this->hourInit)) {
      $this->hourInit = \Phalcon\DI::getDefault()->get('hoursms')->startHour;
    }
    if (!isset($this->hourEnd) || empty($this->hourEnd)) {
      $this->hourEnd = \Phalcon\DI::getDefault()->get('hoursms')->endHour;
    }
  }

}
