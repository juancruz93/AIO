<?php

/**
 * Description of FormNotificationMail
 *
 * @author desarrollo3
 */
class FormNotificationMail extends \Modelbase {

  public $idFormNotificationMail,
          $idForm,
          $idMailTemplate,
          $created,
          $updated,
          $emails,
          $createdBy,
          $updatedBy,
          $nameSender,
          $emailSender,
          $replyTo,
          $subject;

  public function getSource() {
    return "form_notification_mail";
  }

  public function initialize() {
    $this->hasOne("idForm", "Form", "idForm");
    $this->belongsTo("idMailTemplate", "MailTemplate", "idMailTemplate");
  }

}
