<?php

namespace Sigmamovil\Wrapper;

require_once __DIR__ . "/../general/misc/forceutf8/src/ForceUTF8/Encoding.php";

use Sigmamovil\General\Misc\MailTemplateObj;

class MailtemplateWrapper extends \BaseWrapper {

  public $search = null,
          $mailtemplates,
          $user;

  function __construct() {
    parent::__construct();
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->ipServer = \Phalcon\DI::getDefault()->get('ipServer');
  }

  public function listMailTemplate($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filtro = new \Phalcon\Filter;

    $mailtempcat = (isset($filter->mailtempcat) ? ($filtro->sanitize($filter->mailtempcat, "int") == 0 ? '' : "AND idMailTemplateCategory = {$filtro->sanitize($filter->mailtempcat, "int")}") : '');
    $namemailtemp = (isset($filter->namemailtemp) ? "AND name like '%{$filtro->sanitize($filter->namemailtemp, "string")}%'" : '');

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));

    $conditionacco = (($idAccount != NULL) ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "AND idAccount IS NULL");
    //Se solicitó eliminar y no visualizar todas las plantillas globales 11-03-2020 por comite de lideres
    /*$conditionallied = (($idAllied != NULL) ? "idAllied = {$filtro->sanitize($idAllied, 'int')}" : "idAllied IS NULL");
    $condition = $idAccount != NULL ? 'OR' : 'AND'; */

    $conditions = array(
        "conditions" => "deleted = ?0 {$conditionacco} {$mailtempcat} {$namemailtemp} {$condition} {$conditionallied} {$mailtempcat} {$namemailtemp} {$mailtempcat} {$namemailtemp} OR global=1",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order by" => "created DESC"
    );

    $mailtemplate = \MailTemplate::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = \MailTemplate::count($conditions);

    $data = array();
    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $mailtemplatecontent = \MailTemplateContent::findFirst(array(
                    "conditions" => "idMailTemplate = ?0",
                    "bind" => array($value->idMailTemplate)
        ));

        $dirImage = "images/circle/plantillas.png";

        if (isset($value->idAllied)) {
          $dir = getcwd() . "/allied-assets/{$value->idAllied}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirImage = "allied-assets/{$value->idAllied}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          }
        } elseif (isset($value->idAccount)) {
          $dir = getcwd() . "/assets/{$value->idAccount}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirImage = "assets/{$value->idAccount}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          }
        } else {
          $dir = getcwd() . "/root-assets/images/templates/{$value->idMailTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirImage = "root-assets/images/templates/{$value->idMailTemplate}_thumbnail.png";
          }
        }

        $data[$key] = array(
            "idMailTemplate" => $value->idMailTemplate,
            "idMailTemplateContent" => $mailtemplatecontent->idMailTemplateContent,
            "nameMailTemplateContent" => $mailtemplatecontent->MailTemplate->MailTemplateCategory->name,
            "idAccount" => $value->idAccount,
            "idAllied" => $value->idAllied,
            "name" => ((strlen($value->name) < 40) ? $value->name : substr($value->name, 0, 40) . "..." ),
            //"name" => $value->name,
            "content" => $mailtemplatecontent->content,
            "global" => $value->global,
            "dirImage" => $dirImage
        );
      }
    }



    $array = array(
        "total" => $total,
        "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT),
        "items" => $data
    );

    return $array;
  }

  public function saveMailTemplate($data) {
    $mailtemplate = new \MailTemplate();
    $name = strtolower($this->user->Usertype->name);
    if ($name === "allied") {
      $mailtemplate->idAllied = $this->user->Usertype->idAllied;
      $mailtemplate->idAccount = null;
      $mailtemplate->global = 1;
    } elseif ($name === "account" || $name === "subaccount") {
      $mailtemplate->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : $this->user->Usertype->Subaccount->idAccount);
      $mailtemplate->idAllied = null;
      $mailtemplate->global = 0;
    }
    $mailtemplate->idMailTemplateCategory = $data->mailTemplateCateg;
    $mailtemplate->deleted = 0;
    $mailtemplate->name = $data->nameMailTemplate;
    $mailtemplate->createdBy = $this->user->email;
    $mailtemplate->updatedBy = $this->user->email;
    $mailtemplate->status = 1;

    if (!$mailtemplate->save()) {
      foreach ($mailtemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    $mailtemplateobj = new MailTemplateObj($mailtemplate);
    $mailtemplateobj->saveMailTemplateImage($data->editor);

    $mailtempcontent = new \MailTemplateContent();
    $mailtempcontent->idMailTemplate = $mailtemplate->idMailTemplate;
    $cont = str_replace("\xE2\x80\x8B", "", $data->editor);
    $conte = str_replace("\xCC\x81a-de", "", $cont);
    $mailtempcontent->content = $conte;
    $mailtempcontent->content = $cont;
    $mailtempcontent->createdBy = $this->user->email;
    $mailtempcontent->updatedBy = $this->user->email;

    if (!$mailtempcontent->save()) {
      foreach ($mailtempcontent->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    //exec("wkhtmltoimage --quality 25 --zoom 0.2 --width 180 --height 180 https://aio.sigmamovil.com/thumbnail/mailtemplateshow/56 /websites/aio/public/assets/20/images/templates/56_thumbnail.png");


    //$this->createThumbnailTemplate($mailtemplate->idMailTemplate);
    return $mailtemplate->idMailTemplate;
  }

  public function findMailTemplateCategories() {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
    $this->dataMailTemplateCategories = \MailTemplateCategory::find([
                "conditions" => "idAccount = ?0 AND deleted = 0",
                "bind" => [0 => $idAccount]
    ]);
    $this->modelMailTemplateCategories();
  }

  public function modelMailTemplateCategories() {
    $this->mailTemplateCategories = array();
    foreach ($this->dataMailTemplateCategories as $key) {
      $obj = new \stdClass();
      $obj->idMailTemplateCategory = $key->idMailTemplateCategory;
      $obj->idAllied = $key->idAllied;
      $obj->idAccount = $key->idAccount;
      $obj->name = $key->name;
      $obj->created = $key->created;
      $obj->updated = $key->updated;
      $obj->createdBy = $key->createdBy;
      $obj->updatedBy = $key->updatedBy;
      array_push($this->mailTemplateCategories, $obj);
    }
  }

  public function getMailTemplateCategories() {
    return $this->mailTemplateCategories;
  }

  public function getMailtemplate($idMailTemplate) {
    if (!isset($idMailTemplate)) {
      throw new \InvalidArgumentException("Dato de plantilla inválido");
    }

    $mailtemplate = \MailTemplate::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($idMailTemplate)
    ));

    if (!$mailtemplate) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no existe");
    }

    $mailtemplatecontent = \MailTemplateContent::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($mailtemplate->idMailTemplate)
    ));

    if (!$mailtemplatecontent) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no tiene contenido");
    }

    $data = array(
        "idMailTemplate" => $mailtemplate->idMailTemplate,
        "idMailTemplateCategory" => $mailtemplate->idMailTemplateCategory,
        "name" => $mailtemplate->name,
        "idMailTemplateContent" => $mailtemplatecontent->idMailTemplate,
        "idAccount" => ((isset($mailtemplate->idAccount)) ? $mailtemplate->idAccount : 0),
        "content" => $mailtemplatecontent->content,
    );

    return $data;
  }

  public function editMailTemplate($id, $data) {
    $mailtemplate = \MailTemplate::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($id)
    ));
       
    if (!$mailtemplate) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no existe");
    }

    if (!isset($mailtemplate->idAllied) && !isset($mailtemplate->idAccount)) {
      throw new \InvalidArgumentException("Esta es una plantilla del sistema no se puede modificar ni alterar");
    }

    //if (isset($this->user->Usertype->Allied->idAllied)) {
    if (isset($mailtemplate->idAllied)) {
      $oldOwner = 0;
    } elseif (isset($mailtemplate->idAccount)) {
      $oldOwner = $mailtemplate->idAccount;
    }
    //}

    //Dato anterior $data->idMailTemplateCategory;
    $mailtemplate->idMailTemplateCategory = $data->idMailTemplateCategory;
    //Dato anterior $data->name
    $mailtemplate->name = $data->name;
 
    if (!$mailtemplate->update()) {
      foreach ($mailtemplate->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $mailtemplateobj = new MailTemplateObj($mailtemplate);
    //Dato anterior $data->content
    $mailtemplateobj->updateMailTemplateImage($data->content);

    $mailtemplatecontent = \MailTemplateContent::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($mailtemplate->idMailTemplate)
    ));

    if (!$mailtemplatecontent) {
      throw new \InvalidArgumentException("La plantila que intenta editar no tiene un contenido, por favor verífique los datos");
    }

    $mailtemplatecontent->idMailTemplate = $mailtemplate->idMailTemplate;
    $forceUtf8 = new \ForceUTF8\Encoding();
    //Dato anterior $data->content
    $mailtemplatecontent->content = $forceUtf8->fixUTF8($data->content);

    if (!$mailtemplatecontent->save()) {
      foreach ($mailtemplatecontent->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    if ($oldOwner != $data->owner) {
      if ((int) $data->owner === 0) {
        $this->moveAsset($mailtemplate, $data->owner, true);
      } else {
        $this->moveAsset($mailtemplate, $data->owner);
      }
    }

    $this->db->commit();
    \Phalcon\DI::getDefault()->get('notification')->info("Se ha guardado exitosamente la plantilla {$mailtemplate->name}");
    //$this->createThumbnailTemplate($mailtemplate->idMailTemplate);
    return $mailtemplate->idMailTemplate;
  }

  /**
   * Metodo para mover las imagenes de privadas a globales y viceversa
   * 
   * @param Integer $idAsset
   * @param Bool $global
   */
  private function moveAsset(\MailTemplate $mailtemplate, $owner, $global = false) {
    $mailtemplateimage = \MailTemplateImage::find(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($mailtemplate->idMailTemplate)
    ));

    if (count($mailtemplateimage) > 0) {
      if ($global && $owner == 0) {
        $basedirAcc = $this->asset->dir . $mailtemplate->idAccount . "/images/";
        $basedirAll = $this->asset->dirAllied . $this->user->Usertype->Allied->idAllied . "/images/";
        if (!\file_exists($basedirAll)) {
          if (!mkdir($basedirAll, 0777, true)) {
            throw new \InvalidArgumentException("Ocurrió un problema creando el directorio de destino");
          }
        }

        foreach ($mailtemplateimage as $value) {
          $asset = $this->saveAssets($value, true);

          $imagecopied = $basedirAll . $asset->idAsset . "." . $asset->extension;
          $imageorigin = $basedirAcc . $value->idAsset . "." . $value->Asset->extension;

          if (!\file_exists($imageorigin)) {
            throw new \InvalidArgumentException("No se ha encontrado una de las imágenes para copiar de la plantilla");
          }

          $this->copiedFiles($imageorigin, $imagecopied);

          $imageCopiedThumb = $basedirAll . $asset->idAsset . "_thumb.png";
          $imageOriginThumb = $basedirAcc . $value->idAsset . "_thumb.png";

          $this->copiedFiles($imageOriginThumb, $imageCopiedThumb);

          $imageCopiedThumbnail = $basedirAll . "thumbnail_" . $asset->idAsset . ".png";
          $imageOriginThumbnail = $basedirAcc . "thumbnail_" . $value->idAsset . ".png";

          $this->copiedFiles($imageOriginThumbnail, $imageCopiedThumbnail);
        }

        $mailtemplate->idAccount = null;
        $mailtemplate->idAllied = $this->user->Usertype->Allied->idAllied;
        $mailtemplate->global = 1;

        if (!$mailtemplate->update()) {
          foreach ($mailtemplate->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      } else {
        if (isset($mailtemplate->idAllied)) {
          $basedirOrigin = $this->asset->dirAllied . $this->user->Usertype->Allied->idAllied . "/images/";
        } elseif (isset($mailtemplate->idAccount)) {
          $basedirOrigin = $this->asset->dir . $mailtemplate->idAccount . "/images/";
        }

        $basedirDestiny = $this->asset->dir . $owner . "/images/";

        if (!\file_exists($basedirDestiny)) {
          if (!mkdir($basedirDestiny, 0777, true)) {
            throw new \InvalidArgumentException("Ocurrió un problema creando el directorio de destino");
          }
        }

        foreach ($mailtemplateimage as $value) {
          $asset = $this->saveAssets($value, false, $owner);

          $imageorigin = $basedirOrigin . $value->idAsset . "." . $value->Asset->extension;
          $imagedestiny = $basedirDestiny . $asset->idAsset . "." . $asset->extension;

          if (!\file_exists($imageorigin)) {
            throw new \InvalidArgumentException("No se ha encontrado una de las imágenes para copiar de la plantilla");
          }

          $this->copiedFiles($imageorigin, $imagedestiny);

          $imageOriginThumb = $basedirOrigin . $value->idAsset . "_thumb.png";
          $imageDestinyThumb = $basedirDestiny . $asset->idAsset . "_thumb.png";

          $this->copiedFiles($imageOriginThumb, $imageDestinyThumb);

          $imageOriginThumbnail = $basedirOrigin . "thumbnail_" . $value->idAsset . ".png";
          $imageDestinyThumbnail = $basedirDestiny . "thumbnail_" . $asset->idAsset . ".png";

          if (\file_exists($imageOriginThumbnail)) {
            $this->copiedFiles($imageOriginThumbnail, $imageDestinyThumbnail);
          }
        }

        $mailtemplate->idAccount = $owner;
        $mailtemplate->idAllied = null;
        $mailtemplate->global = 0;

        if (!$mailtemplate->update()) {
          foreach ($mailtemplate->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    }
  }

  public function copiedFiles($origin, $copy) {
    if (!\copy($origin, $copy)) {
      throw new \InvalidArgumentException("Ocurrió un error copiando una de las imágenes de la plantilla");
    }
  }

  public function saveAssets($refAsset, $idAllied = false, $idAccount = null) {//$refAsset es el asset de referencia;
    $asset = new \Asset();
    if (!$idAllied) {
      $asset->idAllied = $this->user->Usertype->Allied->idAllied;
    } else {
      $asset->idAccount = $idAccount;
    }

    $asset->name = $refAsset->Asset->name;
    $asset->size = $refAsset->Asset->size;
    $asset->type = $refAsset->Asset->type;
    $asset->contentType = $refAsset->Asset->contentType;
    $asset->dimensions = $refAsset->Asset->dimensions;
    $asset->extension = $refAsset->Asset->extension;

    if (!$asset->save()) {
      foreach ($asset->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
    }

    return $asset;
  }

  public function deleteMailTemplate($data) {
    if (!isset($data->idMailTemplate)) {
      throw new \InvalidArgumentException("Dato de plantilla inválido");
    }

    $mailtemplate = \MailTemplate::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($data->idMailTemplate)
    ));

    if (!$mailtemplate) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no existe");
    }

    $mailtemplate->deleted = time();

    if (!$mailtemplate->update()) {
      foreach ($mailtemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    return true;
  }

  public function listMailTemplateByaccount($id, $page) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $mailtemplate = \MailTemplate::find(array(
                "conditions" => "deleted = ?0 AND idAccount = ?1 AND global = ?2",
                "bind" => array(0, $id, 0),
                "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
                "offset" => $page
    ));

    $total = \MailTemplate::count(array(
                "conditions" => "deleted = ?0 AND idAccount = ?1 AND global = ?2",
                "bind" => array(0, $id, 0)
    ));

    $data = array();
    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $mailtemplatecontent = \MailTemplateContent::findFirst(array(
                    "conditions" => "idMailTemplate = ?0",
                    "bind" => array($value->idMailTemplate)
        ));

        $dirImage = "images/circle/plantillas.png";
        if (!empty($value->idAccount)) {
          $dir = getcwd() . "/assets/{$value->idAccount}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirImage = "assets/{$value->idAccount}/images/templates/{$value->idMailTemplate}_thumbnail.png";
          }
        }

        $data[$key] = array(
            "idMailTemplate" => $value->idMailTemplate,
            "idMailTemplateContent" => $mailtemplatecontent->idMailTemplateContent,
            "idAccount" => $value->idAccount,
            "idAllied" => $value->idAllied,
            "name" => $value->name,
            //"content" => $mailtemplatecontent->content,
            "global" => $value->global,
            "urlThumbnail" => $dirImage
        );
      }
    }
    $array = array(
        "total" => $total,
        "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT),
        "items" => $data
    );

    return $array;
  }

  public function getmailtemplateautocomplete($filter) {

    $sanitize = new \Phalcon\Filter;
    $mailtemplate = \MailTemplate::find(array(
                "conditions" => "name like '%{$sanitize->sanitize($filter, "string")}%'"
    ));
    $data = array();
    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $data["items"][$key] = array(
            "id" => $value->idMailTemplate,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }

  public function getcontenttemplate($idTemplate) {
    $mailtemplatecontent = \MailTemplateContent::findFirst(array(
                "conditions" => "idMailTemplate = ?0",
                "bind" => array($idTemplate)
    ));

    return $mailtemplatecontent;
  }

  public function getAllMailTemplateByFilter() {
    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");
//    $condition = $idAccount != '' ? 'OR' : 'AND';
    $condition = " OR (idAccount is null AND global = 1 ) ";
    $condition2 = " OR (idAccount is null AND global = 1 AND name LIKE ?1) ";

    if ($this->getSearch() != null) {
      $mailtemplate = \MailTemplate::find(array(
                  "conditions" => "deleted = ?0 AND name LIKE ?1 {$conditionacco} {$conditionallied} {$condition2}",
                  "bind" => [0 => 0, 1 => "%" . $this->getSearch() . "%"]
      ));
    } else {
      $mailtemplate = \MailTemplate::find(array(
                  "conditions" => "deleted = ?0  {$conditionacco} {$conditionallied} {$condition}",
                  "bind" => [0],
                  "limit" => 30,
      ));
    }

    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $this->mailtemplates[$key] = array(
            "idMailTemplate" => $value->idMailTemplate,
            "name" => ((strlen($value->name) < 17) ? $value->name : substr($value->name, 0, 15) . "..." )
        );
      }
    }
  }

  public function getallmailtemplate() {

    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");
    //    $condition = $idAccount != '' ? 'OR' : 'AND';
    $condition = " OR (idAccount is null AND global = 1 ) ";

    $mailtemplate = \MailTemplate::find(array(
                "conditions" => "deleted = ?0 {$conditionacco} {$conditionallied} {$condition}",
                "bind" => array(0),
                "limit" => $this->limit,      
    ));

    $data = array();
    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $data[$key] = array(
            "idMailTemplate" => $value->idMailTemplate,
            "name" => ((strlen($value->name) < 17) ? $value->name : substr($value->name, 0, 15) . "..." )
        );
      }
    }
    return $data;
  }

  public function createThumbnailTemplate($idMailTemplate) {
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));
    $dirAcc = getcwd() . "/assets/";
    $dirAlli = getcwd() . "/allied-assets/";
    $dir = "";

    if (isset($idAllied)) {
      $dirAlli .= "{$idAllied}/images/templates/";
      $dir = $dirAlli;
    } elseif ($idAccount) {
      $dirAcc .= "{$idAccount}/images/templates/";
      $dir = $dirAcc;
    }

    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
    $dir .= "{$idMailTemplate}_thumbnail.png";
//    $domain = $this->urlManager->get_base_uri(true);
    $domain = $this->ipServer->ip."/";
    $exec = "wkhtmltoimage --quality 25 --zoom 0.2 --width 180 --height 180 {$domain}thumbnail/mailtemplateshow/{$idMailTemplate} {$dir}";
    $this->logger->log("Exec: {$exec}");
    exec($exec);
  }

  function getMailtemplates() {
    return $this->mailtemplates;
  }

  function setSearch($search) {
    $this->search = $search;
  }

  function getSearch() {
    return $this->search;
  }

  function getAccountsForAllied() {
    if (!isset($this->user->Usertype->Allied->idAllied)) {
      return FALSE;
    }

    $idAllied = $this->user->Usertype->Allied->idAllied;

    $accounts = \Account::find(array(
                "conditions" => "idAllied = ?0",
                "bind" => array($idAllied)
    ));

    $data = [];
    if (count($accounts) > 0) {
      foreach ($accounts as $key => $value) {
        $data[$key] = array(
            "idAccount" => $value->idAccount,
            "name" => $value->name
        );
      }
      $data[] = array("idAccount" => 0, "name" => "Global");
    }

    return $data;
  }

  public function getallmailtemplatesurvey() {
    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND mail_template.idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND mail_template.idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");


    $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
        mail_template.deleted =0 {$conditionacco} {$conditionallied} ";
    $datas = $this->db->fetchAll($sql);



//    $mailtemplate = \MailTemplate::find(array(
//                "conditions" => "deleted = ?0 {$conditionacco} {$conditionallied} {$condition}",
//                "bind" => array(0),
//                "limit" => $this->limit,
//    ));


    $data = array();
    if (count($datas)) {
      foreach ($datas as $key => $value) {
        $data[$key] = array(
            "idMailTemplate" => $value['idMailTemplate'],
            "name" => ((strlen($value['name']) < 17) ? $value['name'] : substr($value['name'], 0, 15) . "..." )
        );
      }
    }

    return $data;
  }

  public function getAllMailTemplateByFilterSurvey() {
    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");
//    $condition = $idAccount != '' ? 'OR' : 'AND';
    $condition = " OR (idAccount is null AND global = 1 ) ";
    $condition2 = " OR (idAccount is null AND global = 1 AND name LIKE ?1) ";
    $sql = '';
    if ($this->getSearch() != null) {

      $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
        (mail_template.deleted =0 AND A.name = LIKE %{$this->getSearch()}% {$conditionacco} {$conditionallied} {$condition2} 
        )  order by idMailTemplate DESC";
    } else {
      $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
       ( mail_template.deleted =0 {$conditionacco} {$conditionallied} {$condition} 
        )AND mail_template_content.content LIKE '%%SURVEY%%' order by idMailTemplate DESC";
    }
    $mailtemplate = $this->db->fetchAll($sql);

    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $this->mailtemplates[$key] = array(
            "idMailTemplate" => $value['idMailTemplate'],
            "name" => ((strlen($value['name']) < 17) ? $value['name'] : substr($value['name'], 0, 15) . "..." )
        );
      }
    }
  }

  /**
   * 
   * @@autho Jordan Zapata Mora
   * @param type $data, recibe como parametro el id de una plantilla de datos existente
   * @return type, el idMailTemplate en el cual se realizo el update
   */
  public function saveAsMailtemplateNew($data) {
    $mailtemplate = new \MailTemplate();
    $name = strtolower($this->user->Usertype->name);
    
    if (((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '')) {
      $mailtemplate->idAllied = $this->user->Usertype->idAllied;
      $mailtemplate->idAccount = null;
      $mailtemplate->global = 1;
    } elseif (((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : '')) || ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->idSubaccount : '')) {
      $mailtemplate->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : $this->user->Usertype->Subaccount->idAccount);
      $mailtemplate->idAllied = null;
      $mailtemplate->global = 0;
    }
    $mailtemplate->idMailTemplateCategory = $data->idMailTemplateCategory;
    $mailtemplate->deleted = 0;
    $mailtemplate->name = $data->name;
    $mailtemplate->createdBy = $this->user->email;
    $mailtemplate->updatedBy = $this->user->email;
    $mailtemplate->status = 1;
    if (!$mailtemplate->save()) {
      foreach ($mailtemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    $mailtemplateobj = new MailTemplateObj($mailtemplate);
    $mailtemplateobj->saveMailTemplateImage($data->content);
    $mailtempcontent = new \MailTemplateContent();
    $mailtempcontent->idMailTemplate = $mailtemplate->idMailTemplate;
    $cont = str_replace("\xE2\x80\x8B", "", $data->content);
    $conte = str_replace("\xCC\x81a-de", "", $cont);
    $mailtempcontent->content = $conte;
    $mailtempcontent->content = $cont;
    $mailtempcontent->createdBy = $this->user->email;
    $mailtempcontent->updatedBy = $this->user->email;

    if (!$mailtempcontent->save()) {
      foreach ($mailtempcontent->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }


    //$this->createThumbnailTemplate($mailtemplate->idMailTemplate);
    return $mailtemplate->idMailTemplate;
  }
/**
 * 
 * @autho Jordan Zapata Mora
 * @param int $loadlimit, recibe como parametro de la vista el total de caracteres que se desea filtar por nombre de plantilla prediseñana de Mail
 * @return type, todas las plantillas prediseñadas de mail, por rol de cuenta y aliado
 */
 
  public function getFortotalAllmailTemplate($loadlimit) {

    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");
   
    $mailtemplate = \MailTemplate::find(array(
                "conditions" => "deleted = ?0 {$conditionacco} {$conditionallied}",
                "bind" => array(0),
//                "limit" => $this->limit,
    ));
    
    if (!isset($loadlimit) && $loadlimit == "" ) {
      $loadlimit = 15;
    }            

    $data = array();
    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $data[$key] = array(
            "idMailTemplate" => $value->idMailTemplate,
            "name" => ((strlen($value->name) < 17) ? $value->name : substr($value->name, 0, $loadlimit) . "..." )
        );
      }
    }
    return $data;
  }
  
  /**
 * 
 * @autho Juan Cruz
 * @param sin parametros
 * @return array, con la informacion de la template por account
 */
  
  public function getallmailtemplatelandingpage() {
    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND mail_template.idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND mail_template.idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");


    $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
        mail_template.deleted =0 {$conditionacco} {$conditionallied} ";
    $datas = $this->db->fetchAll($sql);

    $data = array();
    if (count($datas)) {
      foreach ($datas as $key => $value) {
        $data[$key] = array(
            "idMailTemplate" => $value['idMailTemplate'],
            "name" => ((strlen($value['name']) < 17) ? $value['name'] : substr($value['name'], 0, 15) . "..." )
        );
      }
    }

    return $data;
  }
  
  public function getAllMailTemplateByFilterLandingpage() {
    $filtro = new \Phalcon\Filter;
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Account->Allied)) ? $this->user->Usertype->Allied->idAllied : '');


    $conditionacco = (($idAccount != '') ? "AND idAccount = {$filtro->sanitize($idAccount, 'int')}" : "");
    $conditionallied = (($idAllied != '') ? " AND idAllied = {$filtro->sanitize($idAllied, 'int')}" : "");
//    $condition = $idAccount != '' ? 'OR' : 'AND';
    $condition = " OR (idAccount is null AND global = 1 ) ";
    $condition2 = " OR (idAccount is null AND global = 1 AND name LIKE ?1) ";
    $sql = '';
    if ($this->getSearch() != null) {

      $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
        (mail_template.deleted =0 AND A.name = LIKE %{$this->getSearch()}% {$conditionacco} {$conditionallied} {$condition2} 
        )  order by idMailTemplate DESC";
    } else {
      $sql = "SELECT
	mail_template.idMailTemplate,
	mail_template.`name`
    FROM
        mail_template
    INNER JOIN mail_template_content ON mail_template.idMailTemplate = mail_template_content.idMailTemplate
    WHERE
       ( mail_template.deleted =0 {$conditionacco} {$conditionallied} {$condition} 
        )AND mail_template_content.content LIKE '%%LANDINGPAGE%%' order by idMailTemplate DESC";
    }
    $mailtemplate = $this->db->fetchAll($sql);

    if (count($mailtemplate)) {
      foreach ($mailtemplate as $key => $value) {
        $this->mailtemplates[$key] = array(
            "idMailTemplate" => $value['idMailTemplate'],
            "name" => ((strlen($value['name']) < 17) ? $value['name'] : substr($value['name'], 0, 15) . "..." )
        );
      }
    }
  }

}
