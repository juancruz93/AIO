<?php

/**
 * Description of FormWelcomeMail
 *
 * @author desarrollo3
 */
class FormWelcomeMail extends \Modelbase {

  public $idFormWelcomeMail,
          $idForm,
          $idMailTemplate,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $nameSender,
          $emailSender,
          $replyTo,
          $subject;

  public function getSource() {
    return "form_welcome_mail";
  }

  public function initialize() {
    $this->hasOne("idForm", "Form", "idForm");
    $this->belongsTo("idMailTemplate", "MailTemplate", "idMailTemplate");
  }

}
