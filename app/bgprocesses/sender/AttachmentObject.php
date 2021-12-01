<?php

class AttachmentObject {

  private $arratch = array();

  function __construct(Mail $mail, Account $account, $assetsrv) {
    $this->mail = $mail;
//    $this->dir = $assetsrv->path . "public/assets/" . $account->idAccount . '/attachments/';
    $this->dir = $assetsrv->path . "public/assets/" . $account->idAccount;
    $this->dirImage = $this->dir . '/images/';
    $this->dirAttachments = $this->dir . '/attachments/';
    $this->dirPdf = $this->dir. '/pdf/';
//    $this->dir = $assetsrv->path . "public/assets/" . $account->idAccount . '/attachments/' . $mail->idMail . '/';
  }

  function addAttachment() {
    $attachments = Mailattachment::find(array(
                'conditions' => 'idMail = ?1',
                'bind' => array(1 => $this->mail->idMail)
    )); 
    if (count($attachments) > 0) {
      foreach ($attachments as $att) {
        $asset = Asset::findFirst(["conditions" => "idAsset = ?0", "bind" => [0 => $att->idAsset]]);
//        $attPath = $this->dir . $asset->idAsset . "." . $asset->extension;
        $obj = new stdClass();
        $obj->name = $att->Asset->name;
        if ($att->Asset->type == "File") {
          $obj->path = $this->dirAttachments . $att->Asset->idAsset . '.' . $att->Asset->extension;
          \Phalcon\DI\FactoryDefault::getDefault()->get("logger")->log($obj->path);
        } else {
          $obj->path = $this->dirImage . $att->Asset->idAsset . '.' . $att->Asset->extension;
          \Phalcon\DI\FactoryDefault::getDefault()->get("logger")->log($obj->path);
        }
        if (is_readable($obj->path)) {
            $this->arratch[] = $obj;
        }
//        $obj->path = $attPath;
      }
    }
  }

  function getArratch() {
    return $this->arratch;
  }
  
  function addAttachmentPdf() {
    $sql = "SELECT DISTINCT idContact, name, type FROM pdfmail WHERE"
      . " idMail = {$this->mail->idMail} "
      . " AND status = 1 ";
    $pdfMail = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    if (count($pdfMail) > 0) {
      foreach ($pdfMail as $pdf) {
        $obj = new stdClass();
        $obj->name = $pdf['name'];
        if ($pdf['type'] == "pdf") {
          $obj->idContact = $pdf['idContact'];
          $obj->path = $this->dirPdf . $this->mail->idMail . '/' . $pdf['name'];
          \Phalcon\DI::getDefault()->get('logger')->log($obj->path);
        } 
        if (is_readable($obj->path)) {
          $this->arratch[] = $obj;
        }
//        $obj->path = $attPath; 
      }
    }
  }

}
