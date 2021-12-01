<?php

/**
 * Description of SupportContact
 *
 * @author desarrollo3
 */
class SupportContact extends Modelbase {

  public $idSupportContact,
          $idAllied,
          $created,
          $updated,
          $name,
          $lastname,
          $email,
          $phone,
          $type,
          $createdBy,
          $updatedBy;

  public function getSource() {
    return "support_contact";
  }

  public function initialize() {
    $this->belongsTo("idAllied", "Allied", "idAllied");
  }

}
