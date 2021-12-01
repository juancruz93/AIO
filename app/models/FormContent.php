<?php

/**
 * Description of FormContent
 *
 * @author desarrollo3
 */
class FormContent extends \Modelbase {

  public $idFormContent,
          $idForm,
          $created,
          $updated,
          $content,
          $createdBy,
          $updatedBy;

  public function getSource() {
    return "form_content";
  }

  public function initialize() {
    $this->hasOne("idForm", "FormContent", "idForm");
  }

}
