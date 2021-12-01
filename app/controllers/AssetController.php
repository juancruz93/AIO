<?php

use Sigmamovil\General\Misc\SanitizeString;

class AssetController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Assets");
    parent::initialize();
  }

  public function uploadAction() {
    $space = $this->getSpaceInAccount();

    if (!$space) {
      return $this->set_json_response(
                      array(
                  'error' => 'Ha sobrepasaso el limite de espacio en disco. para liberar espacio en disco elimine imágenes o archivos que considere innecesarios'
                      )
                      , 401, 'Ha sobrepasado el limite de espacio en disco!');
    } else if (empty($_FILES['file']['name'])) {
      return $this->set_json_response(
                      array(
                  'error' => 'No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información'
                      )
                      , 400, 'Archivo vacio o incorrecto');
    } else {
      $sanitizeString = new SanitizeString($_FILES['file']['name']);
      $sanitizeString->strTrim();
      $sanitizeString->sanitizeBlanks();
      $sanitizeString->sanitizeAccents();
      $sanitizeString->sanitizeSpecials();
      $sanitizeString->toLowerCase();
      $name = $sanitizeString->getString();
      $size = $_FILES['file']['size'];
      $type = $_FILES['file']['type'];
      $tmp_dir = $_FILES['file']['tmp_name'];
      $info = \getimagesize($tmp_dir);
      $message = "Imagen cargada con exito";
      if ($size > $this->uploadConfig->imgAssetMin) {
        $message = "La imagen cargada puede ocasionar un tiempo de carga mayor a la hora de abrir el Correo electrónico";
      }
      if ($info[0] > 600) {
        return $this->set_json_response(
                      array(
                  'error' => 'Ancho de la imagen sobrepasa el maximo permitido (600px)'
                      )
                      , 400, 'Archivo vacio o incorrecto');
      }
      try {
        $account = null;
        $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : $this->user->Usertype->Subaccount->Account );
        if (isset($account)) {
          $assetObj = new \Sigmamovil\General\Misc\AssetObj($account);

          $assetObj->createImage($name, $type, $tmp_dir, $size);
          $idAsset = $assetObj->getIdAsset();

          $array = array(
              'filelink' => $assetObj->getImagePrivateUrl(),
              'thumb' => $assetObj->getThumbnailUrl(),
              'title' => $assetObj->getFileName(),
              'id' => $idAsset,
              'message' => $message
          );
        } else {
          $assetObj = new \Sigmamovil\General\Misc\AssetObj(null, $this->user->Usertype->Allied);

          $assetObj->createImage($name, $type, $tmp_dir, $size);
          $idAsset = $assetObj->getIdAsset();

          $array = array(
              'filelink' => $assetObj->getImagePrivateUrl(),
              'thumb' => $assetObj->getThumbnailUrl(),
              'title' => $assetObj->getFileName(),
              'id' => $idAsset,
              'message' => "Cargado correctamente"
          );
        }

        $this->trace(1,"Upploading asset, idAsset: {$idAsset}");
      } catch (InvalidArgumentException $e){
        $this->logger->log("Esto es una invalid desde AssetController => {$e->getMessage()}");
      } catch (Exception $e) {
        $kb = $this->uploadConfig->imgAssetSize / 1024;
        $mb = $kb / 1024;
        $mb = explode('.', $mb);

        $this->logger->log("Exception: Error while uplodaing asset, {$e} size {$mb[0]} MB");
        $this->trace(0,"Upploading asset:");
        return $this->set_json_response(
                        array(
                    'error' => "Error, el archivo debe ser una imagen (jpeg, jpg, gif, png) con 2MB de peso como máximo."
                        )
                        , 400, 'Error en archivo!');
      }
      return $this->set_json_response($array);
    }
  }

  public function getSpaceInAccount() {

    $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null));
    if (isset($account)) {
      $phql = "SELECT SUM(Asset.size) cnt FROM Asset WHERE Asset.idAccount = :idAccount:";
      $result = $this->modelsManager->executeQuery($phql, array('idAccount' => $account->idAccount));

      $space = ($result->getFirst()->cnt / 1048576);

      if ($space >= $account->AccountConfig->diskSpace) {
        return false;
      }
      return true;
    } else {
      $allied = $this->user->Usertype->Allied;

      $phql = "SELECT SUM(Asset.size) cnt FROM Asset WHERE Asset.idAllied = :idAllied:";
      $result = $this->modelsManager->executeQuery($phql, array('idAllied' => $allied->idAllied));

      $space = ($result->getFirst()->cnt / 1048576);

      if ($space >= $allied->Alliedconfig->diskSpace) {
        return false;
      }
      return true;
    }
  }

  public function listAction() {
    $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null));

    $assets = \Sigmamovil\General\Misc\AssetObj::findAllAssetsInAccount($account);

    if (count($assets) < 1) {
      return $this->set_json_response(array('status' => 'failed'), 404, 'No se encontraron la imágenes!!');
    }

    $jsonImage = array();
    foreach ($assets as $a) {
      $jsonImage[] = array('thumb' => $a->getThumbnailUrl(),
          'image' => $a->getImagePrivateUrl(),
          'title' => $a->getFileName());
    }
    return $this->set_json_response($jsonImage);
  }

  public function showAction($idAsset) {

    $asset = Asset::findFirst(array(
                "conditions" => "idAsset = ?1",
                "bind" => array(1 => $idAsset)
    ));

    if (isset($asset->idAccount)) {
      $img = $this->asset->dir . $asset->idAccount . "/images/" . $asset->idAsset;
    } elseif (isset($asset->idAllied)) {
      $img = $this->asset->dirAllied . $asset->idAllied . "/images/" . $asset->idAsset;
    } else {
      $img = $this->asset->dirRoot . "/images/" . $asset->idAsset;
    }

    /* if (isset($this->user->Usertype->Subaccount->Account->idAccount)) {
      $idAccount = $this->user->Usertype->Subaccount->Account->idAccount;

      $asset = Asset::findFirst(array(
      "conditions" => "idAccount = ?1 AND idAsset = ?2",
      "bind" => array(1 => $idAccount,
      2 => $idAsset)
      ));

      $img = $this->asset->dir . $idAccount . "/images/" . $asset->idAsset;
      } else {
      $idAllied = $this->user->Usertype->idAllied;

      $asset = Asset::findFirst(array(
      "conditions" => "idAllied = ?1 AND idAsset = ?2",
      "bind" => array(1 => $idAllied,
      2 => $idAsset)
      ));

      $img = $this->asset->dirAllied . $idAllied . "/images/" . $asset->idAsset;
      } */

    if (!$asset) {
      return $this->set_json_response(array('Error' => 'not found'), 404, 'No se encontro la imágen!!');
    }

    $ext = pathinfo($asset->name, PATHINFO_EXTENSION);

    $img .= "." . $ext;

    $this->response->setHeader("Content-Type", $asset->type);
//		$this->response->setHeader("Content-Length", $asset->size);

    $this->view->disable();
    return $this->response->setContent(file_get_contents($img));
  }

  public function thumbnailAction($idAsset) {
    $account = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null));
    if (isset($account)) {
      $idAccount = $account->idAccount;

      $asset = Asset::findFirst(array(
                  "conditions" => "idAccount = ?1 AND idAsset = ?2",
                  "bind" => array(1 => $idAccount,
                      2 => $idAsset)
      ));

      $img = $this->asset->dir . $idAccount . "/images/" . $asset->idAsset . "_thumb.png";
    } else {
      $idAllied = $this->user->Usertype->idAllied;

      $asset = Asset::findFirst(array(
                  "conditions" => "idAllied = ?1 AND idAsset = ?2",
                  "bind" => array(1 => $idAllied,
                      2 => $idAsset)
      ));

      $img = $this->asset->dirAllied . $idAllied . "/images/" . $asset->idAsset . "_thumb.png";
    }

    if (!$asset) {
      return $this->set_json_response(array('Error' => 'not found'), 404, 'No se encontró la imágen!!');
    }

    $this->response->setHeader("Content-Type", "image/png");
//		$this->response->setHeader("Content-Length", $asset->size);

    $this->view->disable();
    return $this->response->setContent(file_get_contents($img));
  }

  public function thumbnailmailAction($idMail) {

    $mail = \Mail::findFirst(array("conditions" => " idMail = {$idMail}"));
    $dirImage = "images/circle/opened-email-envelope.png";
    if ($mail) {
      $dir = getcwd() . "/assets/{$mail->Subaccount->idAccount}/images/mails/{$idMail}_thumbnail.png";
      if (file_exists($dir)) {
        $dirImage = "assets/{$mail->Subaccount->idAccount}/images/mails/{$idMail}_thumbnail.png";
        $length = filesize($dir);
      }
    }
//    $this->response->setStatusCode(304, "Not Modified");
    $date = new DateTime('UTC');
    $this->response->setHeader("Content-Type", "image/png");
//    $this->response->setRawHeader("HTTP/1.1 ");
//    $this->response->setHeader("Accept-Ranges", "bytes");
//    $this->response->setHeader("Access-Control-Allow-Origin", "*");
//    $this->response->setHeader("Content-Length", $length);
//    $this->response->setHeader("Date", $date->format('D, d M Y H:i:s \G\M\T'));
//    $this->response->setHeader("Expires ", "Thu, 17 May 2018 15:41:33 GMT");
//    $this->response->setHeader("Cache-Control", "public, max-age=31536000");
//    $this->response->setHeader("X-Content-Type-Options", "nosniff");
//    $this->response->setHeader("Server ", "sffe");
    $this->view->disable();
    return $this->response->setContent(file_get_contents($dirImage));
  }

  public function showalliedassetsAction($idAsset) {
    $asset = Asset::findFirst(array(
                "conditions" => "idAsset = ?1",
                "bind" => array(1 => $idAsset)
    ));
    if (isset($asset->idAllied)) {
      $img = $this->asset->dirAllied . $asset->idAllied . "/images/" . $asset->idAsset;
    } else {
      $img = $this->asset->dirRoot . "/images/" . $asset->idAsset;
    }

    if (!$asset) {
      return $this->set_json_response(array('Error' => 'not found'), 404, 'No se encontro la imágen!!');
    }
    $ext = pathinfo($asset->name, PATHINFO_EXTENSION);
    $img .= "." . $ext;
    $this->response->setHeader("Content-Type", $asset->type);
    $this->view->disable();
    return $this->response->setContent(file_get_contents($img));
  }

}
