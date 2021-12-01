<?php

/**
 * Description of FormStatistic
 *
 * @author desarrollo3
 */
class FormStatistic extends \Modelbase {

  public $idFormOptin,
          $idForm,
          $idContact,
          $created,
          $updated,
          $createdBy,
          $updatedBy;

  public function getSource() {
    return "form_statistic";
  }

  public function initialize() {
    $this->belongsTo("idForm", "Form", "idForm");
  }

}
