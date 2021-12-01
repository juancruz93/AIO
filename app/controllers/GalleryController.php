<?php

class GalleryController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Galería");
    parent::initialize();
  }

  public function indexAction() {
    $id = "";
    $currentPage = $this->request->getQuery('page', null, 1);
    if ($this->user->UserType->idAccount) {
      $id = $this->user->UserType->idAccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $id = $this->user->UserType->idSubaccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }

    $builder = $this->modelsManager->createBuilder()
            ->from('Asset')
            ->where("idAccount = {$account->idAccount}")
            ->orderBy('Asset.created');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();
    $this->view->setVar("account", $account->AccountConfig);
    $this->view->setVar("space", round($this->getSpaceUsedInAccount(), 2));
    $this->view->setVar("page", $page);
  }

  public function uploadAction() {
    
  }

  public function uploadimageAction() {
    try {
      $this->validateSpaceInAccount();
      $file = $_FILES['file'];
      $this->validateFile($file);
      $assetsManager = $this->getAssetsManager($file);
      $assetsManager->uploadImage();

      return $this->set_json_response(array('Se han cargado los archivos exitosamente', 200, 'success'));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 401, 'error');
    } catch (Exception $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 500, 'error');
    }
  }

  public function uploadfileAction() {
    try {
      $this->validateSpaceInAccount();
      $file = $_FILES['file'];
      $this->validateFile($file);
      $assetsManager = $this->getAssetsManager($file);
      $assetsManager->uploadFile();
//      var_dump($assetsManager);
//      exit;
      return $this->set_json_response(array('Se han cargado los archivos exitosamente', 200, 'success'));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 401, 'error');
    } catch (Exception $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 500, 'error');
    }
  }

  public function deleteAction($idAsset, $page) {
    if ($this->user->UserType->idAccount) {
      $account = $this->user->UserType->Account;
    }
    if ($this->user->UserType->idSubaccount) {
      $account = $this->user->UserType->Subaccount->Account;
    }

    $asset = Asset::findFirst(array(
                "conditions" => "idAsset = ?1 AND idAccount = ?2",
                "bind" => array(1 => $idAsset,
                    2 => $account->idAccount)
    ));

    if (!$asset) {
      $this->notification->error("El archivo que intenta borrar no se encuentra, por favor valide la información");
      return $this->response->redirect("gallery/index?page={$page}");
    }

    try {
      $assetsManager = new \Sigmamovil\General\Misc\AssetsManager();
      $assetsManager->setAsset($asset);
      $assetsManager->setAccount($account);
      $assetsManager->delete();

      $this->notification->warning("Se ha eliminado el archivo exitosamente");
      return $this->response->redirect("gallery/index?page={$page}");
    } catch (Exception $ex) {
      $this->logger->log($ex);
      $this->notification->error("Ha ocurrido un error, por favor contacte al administrador");
      return $this->response->redirect("gallery/index?page={$page}");
    }
  }

  public function showAction($idAsset) {
    $id = "";
    if ($this->user->UserType->idAccount) {
      $id = $this->user->UserType->idAccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $id = $this->user->UserType->idSubaccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }

    $asset = Asset::findFirst(array(
                "conditions" => "idAccount = ?1 AND idAsset = ?2",
                "bind" => array(1 => $account->idAccount,
                    2 => $idAsset)
    ));

    if (!$asset) {
      return $this->set_json_response(array('Imagen o archivo no encontradp, por favor valide la información'), 404, 'No se encontro la imágen!!');
    }

    $img = "{$this->path->path}{$this->assets->folder}{$account->idAccount}/images/{$asset->idAsset}.{$asset->extension}";
    $this->response->setHeader("Content-Type", $asset->type);

    $this->view->disable();
    return $this->response->setContent(file_get_contents($img));
  }

  public function thumbnailAction($idAsset) {
    $id = "";
    if ($this->user->UserType->idAccount) {
      $id = $this->user->UserType->idAccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $id = $this->user->UserType->idSubaccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }

    $asset = Asset::findFirst(array(
                "conditions" => "idAccount = ?1 AND idAsset = ?2",
                "bind" => array(1 => $account->idAccount,
                    2 => $idAsset)
    ));

    if (!$asset) {
      return $this->set_json_response(array('Imagen o archivo no encontradp, por favor valide la información'), 404, 'No se encontro la imágen!!');
    }

    $img = "{$this->path->path}{$this->assets->folder}{$account->idAccount}/images/thumbnail_{$asset->idAsset}.png";
    //$this->logger->log($img);
    $this->response->setHeader("Content-Type", "image/png");

    $this->view->disable();

    return $this->response->setContent(file_get_contents($img));
  }

  private function validateFile($file) {
    if (empty($file['name'])) {
      throw new InvalidArgumentException("No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información");
    }

    if ($file['error'] == 1) {
      throw new InvalidArgumentException("Ha ocurrido un error mientras se cargaba el archivo, por favor valide la información");
    }
  }

  private function getAssetsManager($file) {
    $am = new \Sigmamovil\General\Misc\AssetsManager();
    $id = "";

    if ($this->user->UserType->idAccount) {
      $id = $this->user->UserType->idAccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $id = $this->user->UserType->idSubaccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }
    $am->setAccount($account);
    $am->setFile($file);
    return $am;
  }

  private function validateSpaceInAccount() {
    $space = $this->getSpaceUsedInAccount();
    if ($this->user->UserType->idAccount) {
      $id = $this->user->UserType->idAccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $id = $this->user->UserType->idSubaccount;
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }

    if ($space >= $account->AccountConfig->diskSpace) {
      throw new InvalidArgumentException("Ha sobrepasaso el limite de espacio en disco. Para liberar espacio en disco elimine imágenes o archivos que considere innecesarios");
    }
  }

  /*
   * FUNCION QUE GUARDA LOS ARCHIVOS Y CREA REGISTRO EN LA TABLA MAIL ATACHMENT USADA EN FORMULARIOS DE MAIL
  */
  public function uploadfileadjuntAction($idMail) {
    try {
      $this->validateSpaceInAccount();
      $file = $_FILES['file'];
      $this->validateFile($file);
      $assetsManager = $this->getAssetsManager($file);
      $assetsManager->uploadAttachment();
      $mail = Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idMail]]);
      $mail->attachment = 1;
      $sql = "select * from asset order by idAsset DESC limit 1";
      $asset = $this->db->fetchAll($sql);

      //$this->uploadAttachment($file, $asset[0]);
      
      $attachment = new Mailattachment();
      $attachment->idAsset = $asset[0]['idAsset'];
      $attachment->idMail = $idMail;
      $attachment->createdon = time();
      if (!$attachment->save()) {
        foreach ($attachment->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      return $this->set_json_response(array($assetsManager, 200, 'success'));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 401, 'error');
    } catch (Exception $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 500, 'error');
    }
  }

  /*
   * FUNCION QUE GUARDA LOS ARCHIVOS USADA EN CA
  */
  public function uploadfileadjuntcaAction() {
    try {
      $this->validateSpaceInAccount();
      $file = $_FILES['file'];
      $this->validateFile($file);
      $assetsManager = $this->getAssetsManager($file);

      return $this->set_json_response(array($assetsManager->uploadAttachmentAC(), 200, 'success'));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 401, 'error');
    } catch (Exception $ex) {
      $this->logger->log("Exception: {$ex->getMessage()}");
      return $this->set_json_response(array($ex->getMessage()), 500, 'error');
    }
  }

  public function uploadAttachment($file, $asset) {
    $dir = "{$this->path->path}{$this->assets->folder}{$this->user->Usertype->Subaccount->account->idAccount}/attachments/";
    $dirAsset = "{$this->path->path}{$this->assets->folder}{$this->user->Usertype->Subaccount->account->idAccount}/files/";
    $nameAsset = $asset["idAsset"] . "." . $asset["extension"];
    if (!\file_exists($dir)) {
      \mkdir($dir, 0777, true);
    }
    if (!\copy($dirAsset . $nameAsset, $dir . $nameAsset)) {
      
    }
  }

}
