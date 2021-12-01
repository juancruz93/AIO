<?php

/**
 * Description of FormOptin
 *
 * @author desarrollo3
 */
class FormOptin extends \Modelbase {

  public $idFormOptin,
          $idForm,
          $idMailTemplate,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $nameSender,
          $emailSender,
          $replyTo,
          $urlSuccess,
          $subject;

  public function getSource() {
    return "form_optin";
  }

  public function initialize() {
    $this->hasOne("idForm", "Form", "idForm");
    $this->belongsTo("idMailTemplate", "MailTemplate", "idMailTemplate");
  }

}
