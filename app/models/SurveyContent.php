<?php

class SurveyContent extends Modelbase
{
  public $idSurveyContent;
  public $idSurvey;
  public $content;

  public function initialize() {
    $this->belongsTo("idSurvey", "Survey", "idSurvey");
  }

}