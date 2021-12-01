<?php

class Survey extends Modelbase {

  public
          $idSurvey,
          $idSurveyCategory,
          $idSubaccount,
          $deleted,
          $totalCount,
          $status,
          $name,
          $description,
          $messageFinal,
          $url,
          $type,
          $startDate,
          $endDate;
       
  public function initialize() {
    $this->hasOne("idSurvey", "SurveyContent", "idSurvey");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->belongsTo("idSurveyCategory", "SurveyCategory", "idSurveyCategory");
    $this->hasMany("idMail", "Mail", "idMail");
    $this->hasMany("idSurvey", "Survey", "idSurvey");
    $this->belongsTo("idSurvey", "AutomaticCampaignStep", "idSurvey");
  }

}
