<?php

/**
 * Description of Form
 *
 * @author desarrollo3
 */
class Form extends \Modelbase {

  public $idForm,
          $idFormCategory,
          $idSubaccount,
          $idContactlist,
          $created,
          $updated,
          $deleted,
          $status,
          $optin,
          $welcomeMail,
          $notificationMail,
          $type,
          $name,
          $description,
          $successUrl,
          $errorUrl,
          $welcomeUrl,
          $successMessage,
          $errorMessage,
          $welcomeMessage,
          $createdBy,
          $updatedBy,
          $habeasData;

  public function getSource() {
    return "form";
  }

  public function initialize() {
    $this->belongsTo("idFormCategory", "FormCategory", "idFormCategory");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasMany("idContactlist", "Contactlist", "idContactlist");
    $this->hasOne("idForm", "FormOptin", "idForm");
    $this->hasOne("idForm", "FormNotificationMail", "idForm");
    $this->hasOne("idForm", "FormWelcomeMail", "idForm");
    $this->hasMany("idForm", "FormStatistic", "idForm");
    $this->belongsTo("idForm", "AutomaticCampaignStep", "idForm");
  }

}
