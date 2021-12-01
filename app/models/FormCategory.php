<?php

/**
 * Description of FormCategory
 *
 * @author desarrollo3
 */
class FormCategory extends \Modelbase {

  public $idFormCategory,
          $idAccount,
          $name,
          $deleted,
          $description;

  public function getSource() {
    return "form_category";
  }

  public function initialize() {
    $this->hasMany("idFormCategory", "Form", "idFormCategory");
    $this->belongsTo("idAccount", "Account", "idAccount");
  }

}
