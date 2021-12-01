<?php

namespace Sigmamovil\General\Misc;

use Sigmamovil\General\Misc\SanitizeString;

class AssetsManager {

  protected $FILE_EXT_NOT_ALLOWED = array('ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'exe', 'hta', 'ins', 'isp', 'jse', 'lib', 'lnk', 'mde', 'msc', 'msp',
      'ksh', 'msh', 'reg', 'mst', 'pif', 'scr', 'sct', 'shb', 'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh', 'apk', 'app',
      'csh', 'gadget', 'js', 'zip', 'tar', 'tgz', 'taz', 'z', 'gz', 'rar');
  protected $IMG_EXT_ALLOWED = array('gif', 'jpe?g', 'png');
  protected $logger;
  protected $account;
  protected $assets;
  protected $path;
  protected $file;
  protected $asset;
  protected $dir;
  protected $fileDir;
  protected $thumbDir;

  function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->assets = \Phalcon\DI::getDefault()->get('assets');
    $this->path = \Phalcon\DI::getDefault()->get('path');
  }

  public function setAccount(\Account $account) {
    $this->account = $account;
  }

  public function setAsset(\Asset $asset) {
    $this->asset = $asset;
  }

  public function setFile($file) {
    $this->file = $file;
  }

  public function uploadImage() {
    $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/images/";

    \Phalcon\DI::getDefault()->get('db')->begin();
    $uploader = new Uploader();
    $image = new ImageObject();
    try {
      $sanitizeString = new SanitizeString($this->file['name']);
      $sanitizeString->strTrim();
      $sanitizeString->sanitizeBlanks();
      $sanitizeString->sanitizeAccents();
      $sanitizeString->sanitizeSpecials();
      $sanitizeString->toLowerCase();
      $this->file['name'] = $sanitizeString->getString();
      $uploader->setExtensionsAllowed($this->IMG_EXT_ALLOWED);
      $uploader->setMaxSizeSupported($this->assets->imageSize);
      $uploader->setDir($this->dir);
      $uploader->setFile($this->file);
      $uploader->validate();
      $this->saveAsset("Image");
      $uploader->setFile($this->file);
      $uploader->upload();
      $this->fileDir = $uploader->getFileDirectory();
      $image->createImageFromFile($this->fileDir, $this->file['newName']);
      $image->resizeImage(238, 260, "#ffffff");
      $this->thumbDir = "{$this->dir}thumbnail_{$this->asset->idAsset}.png";
      $image->saveImage("png", $this->thumbDir);

      \Phalcon\DI::getDefault()->get('db')->commit();
      return $this->file['newName'];
    } catch (\InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \InvalidArgumentException($ex->getMessage());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \Exception($ex->getMessage());
    }
  }

  public function uploadFile() {
    $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/files/";
    \Phalcon\DI::getDefault()->get('db')->begin();
    $uploader = new Uploader();
    try {
      $uploader->setExtensionsNotAllowed($this->FILE_EXT_NOT_ALLOWED);
      $uploader->setMaxSizeSupported($this->assets->fileSize);
      $uploader->setDir($this->dir);
      $uploader->setFile($this->file);
      $uploader->validate();
      $this->saveAsset("File");
      $uploader->setFile($this->file);
      $uploader->upload();
      $this->fileDir = $uploader->getFileDirectory();

      \Phalcon\DI::getDefault()->get('db')->commit();
    } catch (\InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \InvalidArgumentException($ex->getMessage());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \Exception($ex->getMessage());
    }
  }

  public function uploadAttachment() {
    $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/attachments/";

    \Phalcon\DI::getDefault()->get('db')->begin();
    $uploader = new Uploader();
    try {
      $uploader->setExtensionsNotAllowed($this->FILE_EXT_NOT_ALLOWED);
      $uploader->setMaxSizeSupported($this->assets->fileSize);
      $uploader->setDir($this->dir);
      $uploader->setFile($this->file);
      $uploader->validate();
      $this->saveAsset("File");

      $uploader->setFile($this->file);
      $uploader->upload();
//                       var_dump($this->dir);
//      echo 123;
//      exit;
      $this->fileDir = $uploader->getFileDirectory();

      \Phalcon\DI::getDefault()->get('db')->commit();
    } catch (\InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \InvalidArgumentException($ex->getMessage());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \Exception($ex->getMessage());
    }
  }

  //GUARDA LOS DOCUMENTOS ENLAZADOS EN LAS CAMPAÑAS AUTOMATICAS
  public function uploadAttachmentAC() {
    $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/attachments/";

    \Phalcon\DI::getDefault()->get('db')->begin();
    $uploader = new Uploader();
    try {
      $uploader->setExtensionsNotAllowed($this->FILE_EXT_NOT_ALLOWED);
      $uploader->setMaxSizeSupported($this->assets->fileSize);
      $uploader->setDir($this->dir);
      $uploader->setFile($this->file);
      $uploader->validate();
      $this->saveAsset("File");
      $uploader->setFile($this->file);
      $uploader->upload();

      $this->fileDir = $uploader->getFileDirectory();

      \Phalcon\DI::getDefault()->get('db')->commit();

      return $this->file;

    } catch (\InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \InvalidArgumentException($ex->getMessage());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $uploader->deleteFileFromServer($this->fileDir);
      $uploader->deleteFileFromServer($this->thumbDir);
      $this->logger->log($ex);
      throw new \Exception($ex->getMessage());
    }
  }

  public function delete() {
    $uploader = new Uploader();
    try {
      \Phalcon\DI::getDefault()->get('db')->begin();
      $this->deleteAsset();

      if ($this->asset->type == 'Image') {
        $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/images/{$this->asset->idAsset}.{$this->asset->extension}";
        $uploader->deleteFileFromServer("{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/images/thumbnail_{$this->asset->idAsset}.png");
        $uploader->deleteFileFromServer("{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/images/{$this->asset->idAsset}_thumb.png");
      } else if ($this->asset->type == 'File') {
        $this->dir = "{$this->path->path}{$this->assets->folder}{$this->account->idAccount}/files/{$this->asset->idAsset}.{$this->asset->extension}";
      }

      $uploader->deleteFileFromServer($this->dir);

      \Phalcon\DI::getDefault()->get('db')->commit();
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      throw new \Exception($ex->getMessage());
    }
  }

  private function deleteAsset() {
    if (!$this->asset->delete()) {
      foreach ($this->asset->getMessages() as $msg) {
        $this->logger->log("Error while deleting asset: {$msg}");
      }
      throw new \Exception("$msg");
    }
  }

  protected function saveAsset($type) {
    $this->asset = new \Asset();
    $this->asset->idAccount = $this->account->idAccount;
    $this->asset->created = \time();
    $this->asset->name = $this->file['name'];
    $this->asset->size = $this->file['size'];
    $this->asset->type = $type;
    $this->asset->contentType = $this->file['type'];
    $dimensions = "not available";
    if ($type == "Image") {
      $info = \getimagesize($this->file['tmp_name']);
      //$info[0] hace referencia al valor del ancho (width) de la imagen
      //$info[1]  hace referencia al valor del alto (height) de la imagen
      if($info[0] > 600){
        throw new \InvalidArgumentException('La imagen no puede tener un ancho mayor a 600px');
      }
      $dimensions = $info[0] . ' x ' . $info[1];
    }
    $this->asset->dimensions = $dimensions;
    $this->asset->extension = \pathinfo($this->asset->name, PATHINFO_EXTENSION);
    $this->asset->preview = null;

    if (!$this->asset->save()) {
      throw new \InvalidArgumentException('could not be saved on the database');
    }

    $this->file['newName'] = "{$this->asset->idAsset}.{$this->asset->extension}";
  }

  public function savePreview(\Asset $asset, $preview) {
    $asset->preview = $preview;

    if (!$this->asset->save()) {
      throw new \InvalidArgumentException('could not be saved on the database');
    }
  }

  public function getAsset() {
    return $this->asset;
  }

}
