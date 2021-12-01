<?php

class ReplyTos extends Modelbase {

  public $idReplyTo,
          $idAccount,
          $email,
          $status,
          $created,
          $updated,
          $deleted,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idReplyTo", "Mail", "idReplyTo");
  }

  public function getSource() {
    return "reply_tos";
  }

}
