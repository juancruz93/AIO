<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SurveyCategory
 *
 * @author juan.pinzon
 */
class SurveyCategory extends Modelbase {

  public $idSurveyCategory;
  public $idAccount;
  public $created;
  public $updated;
  public $deleted;
  public $status;
  public $name;
  public $description;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasMany("idSurveyCategory", "Survey", "idSurveyCategory");
    $this->belongsTo("idAccount", "Account", "idAccount");
  }

}
