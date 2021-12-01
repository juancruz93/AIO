<?php

class Systemmail extends Modelbase {

  public $idSystemmail,
          $idAllied,
          $idMasteraccount,
          $name,
          $category,
          $description,
          $subject,
          $fromEmail,
          $fromName,
          $content,
          $plainText,
          $previewData,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $deleted;

  public function initialize() {
    $this->hasOne("idAllied", "Allied", "idAllied");
  }

}
