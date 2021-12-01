<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Post extends Modelbase {
  
  public $idPost,
         $idMail,
         $idSms,
         $idSmsTwoway,
         $idSurvey, 
         $type,
         $idPage,
         $scheduleddate,
         $link,
         $description;
  
  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail");
    $this->belongsTo("idSms", "Sms", "idSms");
    $this->belongsTo("idSurvey", "Survey", "idSurvey");
    $this->belongsTo("idSmsTwoway", "Smstwoway", "idSmsTwoway");
  }
         
  
}
