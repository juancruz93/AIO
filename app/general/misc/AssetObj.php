<?php

namespace Sigmamovil\General\Misc;

class AssetObj {

  protected $logger;
  protected $account;
  protected $allied;
  protected $assetsrv;
  protected $url;
  protected $uploadConfig;
  protected $user;

  function __construct(\Account $account = null, \Allied $allied = null) {
    $this->account = $account;
    $this->allied = $allied;
    $this->assetsrv = \Phalcon\DI::getDefault()->get('asset');
    $this->url = \Phalcon\DI::getDefault()->get('url');
    $this->uploadConfig = \Phalcon\DI::getDefault()->get('uploadConfig');
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

  /**
   * Función que se encarga de gestionar la creación de una imagen y thumbnail en el servidor y un registro en la base de datos
   * @param string $name
   * @param int $size
   * @param string $type
   * @param string $tmp_dir
   */
  public function createImage($name, $type, $tmp_dir, $size = null) {
    try {
      $this->validateFile($name, $size);
      $this->saveAssetInDb($name, $size, $type, $tmp_dir);

      $account = null;
      $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : $this->user->Usertype->Subaccount->Account );
      if (isset($account)) {
        $dir = $this->assetsrv->dir . $this->account->idAccount . '/images/';
      } else {
        $dir = $this->assetsrv->dirAllied . $this->allied->idAllied . '/images/';
      }

      if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
      }

      $ext = pathinfo($name, PATHINFO_EXTENSION);

      $dir1 = $dir . $this->asset->idAsset . '.' . strtolower($ext);
      $dir2 = $dir . $this->asset->idAsset . '_thumb.png';
      $dir3 = "{$dir}thumbnail_{$this->asset->idAsset}.png";

      $imageObj = new ImageObject();
      $imageObj->moveImageFromSiteToAnother($tmp_dir, $dir1);
      $imageObj->createImageFromFile($dir1, $name);
      
      if (isset($account)) {
        $imageObj->resizeImage(238, 260, "#ffffff");
        $imageObj->saveImage("png", $dir3);
      }

      $imageObj->resizeImage(100, 74);
      $imageObj->saveImage('png', $dir2);

    } catch (\InvalidArgumentException $e) {
      throw new \InvalidArgumentException('Error: ' . $e->getMessage());
    } catch (DivisionByZeroError $e){
      throw new \InvalidArgumentException("Esta es una división por cero {$e->getMessage()}");
    }
  }

  public function createGlobalImage($name, $type, $tmp_dir, $size = null) {
    try {
      $this->validateFile($name, $size);
      $this->saveGloblaImageInDb($name, $size, $type, $tmp_dir);

      $image = new Image($this->account);
      $dirImage = $image->saveImage($this->asset, $name, $tmp_dir);

      $dir = $this->assetsrv->dir . $this->account->idAccount . '/images/';

      if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
      }

      $dir .= $this->asset->idAsset . '_thumb.png';

      $imageObj = new ImageObject();
      $imageObj->createImageFromFile($dirImage, $name);
      $imageObj->resizeImage(100, 74);
      $imageObj->saveImage('png', $dir);
    } catch (\InvalidArgumentException $e) {
      throw new \InvalidArgumentException("we have a error... {$e}");
    }
  }

  /**
   * Funcion que valida que el archivo este correcto
   * @param string $name
   * @param string $size
   * @throws \InvalidArgumentException
   */
  protected function validateFile($name, $size) {
    $ext = '%\.(gif|jpe?g|png)$%i';

    $isValid = preg_match($ext, $name);

    if ($size > $this->uploadConfig->imgAssetSize) {
      throw new \InvalidArgumentException('File size exceeds maximum: ' . $this->uploadConfig->imgAssetSize . ' bytes');
    } else if (!$isValid) {
      throw new \InvalidArgumentException('Invalid extension for file...');
    }
  }

  /**
   * Funcion que guarda información de un asset en la base de datos
   * @param string $name nombre con extensión del archivo
   * @param int $size peso del archivo
   * @param string $type tipo mime del archivo
   * @param string $tmp_dir ubicación de archivo
   * @throws \InvalidArgumentException
   */
  protected function saveAssetInDb($name, $size, $type, $tmp_dir) {
    $info = getimagesize($tmp_dir);
    $dimensions = $info[0] . ' x ' . $info[1];

    $asset = new \Asset();

    $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null));
    if (isset($account)) {
      $asset->idAccount = $this->account->idAccount;
    } else {
      $asset->idAllied = $this->allied->idAllied;
    }
    $asset->name = $name;
    $asset->size = $size;
    $asset->dimensions = $dimensions;
    $asset->type = "Image";
    $asset->contentType = $type;
    $asset->extension = \pathinfo($asset->name, PATHINFO_EXTENSION);
    $asset->created = time();
    $this->logger->log($asset->idAccount);
    if (!$asset->save()) {
      throw new \InvalidArgumentException('could not be saved on the database');
    }
    $this->asset = $asset;
  }

  /**
   * funcion que retorna un objeto assetObj
   * @param Account $account
   * @return \AssetObj
   */
  public static function findAllAssetsInAccount(\Account $account) {
    $assets = \Asset::findAllAssetsInAccount($account);
    $aobjs = array();
    foreach ($assets as $a) {
      $obj = new AssetObj($account);
      $obj->setAsset($a);
      $aobjs[] = $obj;
    }

    return $aobjs;
  }

  /**
   * funcion que retorna un objeto assetObj pero paginado
   * @param Account $account
   * @return \AssetObj
   */
  public static function findAllAssetsInAccountPagination(\Account $account, $page = 0) {
    $assets = \Asset::findAllAssetsInAccountPagination($account, $page);

    $aobjs = array();
    $aobjs['total'] = $assets['total'];
    $aobjs['total_pages'] = $assets['total_pages'];
    foreach ($assets['items'] as $a) {
      $obj = new AssetObj($account);
      $obj->setAsset($a);
      $aobjs['items'][] = $obj;
    }

    return $aobjs;
  }

  /**
   * funcion que retorna un objeto assetObj
   * @param Allied $allied
   * @return \AssetObj
   */
  public static function findAllAssetsInAllied(\Allied $allied) {
    $assets = \Asset::findAllAssetsInAllied($allied);
    $aobjs = array();
    foreach ($assets as $a) {
      $obj = new AssetObj(null, $allied);
      $obj->setAsset($a);
      $aobjs[] = $obj;
    }

    return $aobjs;
  }

  /**
   * funcion que retorna un objeto assetObj paginado
   * @param Allied $allied
   * @return \AssetObj
   */
  public static function findAllAssetsInAlliedPagination(\Allied $allied, $page = 0) {
    $assets = \Asset::findAllAssetsInAlliedPagination($allied, $page);
    $aobjs = array();
    $aobjs['total'] = $assets['total'];
    $aobjs['total_pages'] = $assets['total_pages'];
    foreach ($assets['items'] as $a) {
      $obj = new AssetObj(null, $allied);
      $obj->setAsset($a);
      $aobjs['items'][] = $obj;
    }

    return $aobjs;
  }

  protected function setAsset(\Asset $a) {
    $this->asset = $a;
  }

  public function getImagePrivateUrl() {
    $urlImage = $this->url->get('asset/show') . '/' . $this->asset->idAsset;
    return $urlImage;
  }

  public function getThumbnailUrl() {
    $dir = $this->assetsrv->dir . $this->account->idAccount . '/images/';
    $thumb = $dir . $this->asset->idAsset . '_thumb.png';
    if (!file_exists($thumb)) {
      $ext = pathinfo($this->asset->name, PATHINFO_EXTENSION);
      $image = $dir . $this->asset->idAsset . '.' . $ext;
      $thumbnail = new Thumbnail($this->account);
      $thumbnail->createThumbnail($this->asset, $image, $this->asset->name);
    }

    $urlThumbnail = $this->url->get('asset/thumbnail') . '/' . $this->asset->idAsset;
    return $urlThumbnail;
  }

  public function getThumbnailUrlAllied() {
    $dir = $this->assetsrv->dirAllied . $this->allied->idAllied . '/images/';
    $thumb = $dir . $this->asset->idAsset . '_thumb.png';
    if (!file_exists($thumb)) {
      $ext = pathinfo($this->asset->name, PATHINFO_EXTENSION);
      $image = $dir . $this->asset->idAsset . '.' . $ext;
      $thumbnail = new Thumbnail();
      $thumbnail->createThumbnailByUrl($this->asset, $image, $this->asset->name, $thumb);
    }

    $urlThumbnail = $this->url->get('asset/thumbnail') . '/' . $this->asset->idAsset;
    return $urlThumbnail;
  }

  public function getFileName() {
    return $this->asset->name;
  }

  public function getIdAsset() {
    return $this->asset->idAsset;
  }

}
