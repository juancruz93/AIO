<?php

namespace Sigmamovil\Wrapper;

use Sigmamovil\General\Misc\AssetsManager;

/**
 * Description of LandingpagetemplateWrapper
 *
 * @author juan.pinzon
 */
class LandingpagetemplateWrapper extends \BaseWrapper {

  private $dirPublic;
  private $space;
  private $idAccount;

  public function __construct() {
    $this->dirPublic = getcwd();
    parent::__construct();
  }

  function getSpace() {
    return $this->space;
  }

  function setSpace($space) {
    $this->space = $space;
  }

  function getIdAccount() {
    return $this->idAccount;
  }

  function setIdAccount($idAccount) {
    $this->idAccount = $idAccount;
  }

  public function getAllLandingPageTemplate($page, $data) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitizer = new \Phalcon\Filter;

    $conditions = array(
        "conditions" => "deleted = ?0 ",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "idLandingPageTemplate DESC",
    );

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if (isset($idAccount)) {
      $conditions["conditions"] .= "AND idAccount = :idAccount: ";
      $conditions["bind"]["idAccount"] = $idAccount;
    }

    if (isset($idAllied)) {
      $conditions["conditions"] .= "AND idAllied = :idAllied: ";
      $conditions["bind"]["idAllied"] = $sanitizer->sanitize($idAllied, 'int');
    }

    if (isset($data->name)) {
      $conditions["conditions"] .= "AND name LIKE :name: ";
      $conditions["bind"]["name"] = "%{$sanitizer->sanitize($data->name, 'string')}%";
    }

    if (isset($data->category) && count($data->category) > 0) {
      $conditions["conditions"] .= "AND idLandingPageTemplateCategory IN(:ids:) ";
      $conditions["bind"]["ids"] = implode(",", $data->category);
    }

    if (isset($data->dateStart) && isset($data->dateEnd)) {
      $conditions["conditions"] .= "AND created BETWEEN :start: AND :end: ";
      $conditions["bind"]["start"] = strtotime($sanitizer->sanitize($data->dateStart, 'string'));
      $conditions["bind"]["end"] = strtotime($sanitizer->sanitize($data->dateEnd, 'string'));
    }

    $lptemplate = \LandingPageTemplate::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $conditions["columns"] = "idLandinPageTemplate";
    $total = \LandingPageTemplate::count($conditions);
    
    $rows = [];
    if ($lptemplate->count() > 0) {
      foreach ($lptemplate as $key => $value) {
        $dirThumbnail = "";
        if ($value->idAllied !== NULL) {
          $dir = getcwd() . "/allied-assets/{$value->idAllied}/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirThumbnail = "allied-assets/{$value->idAllied}/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          }
        } elseif ($value->idAccount !== NULL) {
          $dir = getcwd() . "/assets/{$value->idAccount}/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirThumbnail = "assets/{$value->idAccount}/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          }
        } else {
          $dir = getcwd() . "/root-assets/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          if (file_exists($dir)) {
            $dirThumbnail = "root-assets/landing-pages-templates/{$value->idLandingPageTemplate}/{$value->idLandingPageTemplate}_thumbnail.png";
          }
        }

        $rows[$key] = array(
            "idLandingPageTemplate" => $value->idLandingPageTemplate,
            "idLandingPageTemplateCategory" => $value->idLandingPageTemplateCategory,
            "namCategory" => $value->LandingPageTemplateCategory->name,
            "name" => $value->name,
            "created" => date("Y-m-d H:i:s", $value->created),
            "updated" => date("Y-m-d H:i:s", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBY,
            "dirThumbnail" => $dirThumbnail
        );
      }
    }

    $response = array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $rows
    );

    return $response;
  }

  public function createBasicInfoLPT($idlpt, $data) {//Creación información básica de landinpage template
    //LandingPageTemplate as lpt
    if ($idlpt != 0) {
      return $this->editBasicInfoLPT($idlpt, $data);
    }

    $lpt = new \LandingPageTemplate();
    $form = new \LandingpagetemplateForm();
    $form->bind($data, $lpt);

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if (isset($idAccount)) {
      $lpt->setIdAccount($idAccount);
    } elseif (isset($idAllied)) {
      $lpt->setIdAllied($idAllied);
    }

    if (!$form->isValid() || !$lpt->save()) {
      foreach ($form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($lpt->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $filePath = "{$this->dirPublic}/assets/{$idAccount}/landing-pages-templates/{$lpt->idLandingPageTemplate}/";

    if (!mkdir($filePath, 0777, TRUE)) {
      throw new \InvalidArgumentException("No se ha encontrado el archivo base de la plantilla de Landing Page");
    }

    if (file_put_contents("{$filePath}site{$lpt->idLandingPageTemplate}.json", "{}") === FALSE) {
      throw new \InvalidArgumentException("No se pudo escribir el contenido de la plantilla de landing page en el archivo base");
    }

    return ["message" => "La información báscia se ha guardado exitosamente", "idLandingPageTemplate" => $lpt->getIdLandingPageTemplate()];
  }

  private function editBasicInfoLPT($idlpt, $data) {

    $lpt = \LandingPageTemplate::findFirst(array(
                "conditions" => "idLandingPageTemplate = ?0",
                "bind" => array($idlpt)
    ));

    if (!$lpt) {
      throw new \InvalidArgumentException("La plantilla de landingpage que intenta editar no existe");
    }

    $form = new \LandingpagetemplateForm();
    $form->bind($data, $lpt);
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if (isset($idAccount)) {
      $lpt->setIdAccount($idAccount);
    } elseif (isset($idAllied)) {
      $lpt->setIdAllied($idAllied);
    }

    if (!$form->isValid() || !$lpt->save()) {
      foreach ($form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($lpt->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La información báscia se ha editado exitosamente", "idLandingPageTemplate" => $lpt->getIdLandingPageTemplate()];
  }

  public function getLandinPageTemplate($idlpt) {
    if (!isset($idlpt)) {
      throw new \InvalidArgumentException("No se ha podido obtener el argumento de la plantilla de Landing Page");
    }

    $lpt = \LandingPageTemplate::findFirst(array(
                "conditions" => "idLandingPageTemplate = ?0",
                "bind" => array($idlpt)
    ));

    return $lpt->toArray();
  }

  public function getContentLandingPageTemplate($idlpt) {
    if (!isset($idlpt)) {
      throw new \InvalidArgumentException("No se ha podido obtener el argumento de la plantilla de Landing Page");
    }

    if ($idlpt == 0) {
      return [];
    }

    $lpt = \LandingPageTemplate::findFirst(array(
                "columns" => "idLandingPageTemplate",
                "conditions" => "idLandingPageTemplate = ?0",
                "bind" => array($idlpt)
    ));

    if (!$lpt) {
      throw new \InvalidArgumentException("La Landing Page a la que intenta agregar o editar contenido, no existe");
    }

    $objSubaccount = $this->user->Usertype->Subaccount;

    $filePath = "{$this->dirPublic}/assets/{$objSubaccount->Account->idAccount}/landing-pages-templates/{$lpt->idLandingPageTemplate}/site{$lpt->idLandingPageTemplate}.json";
    if (!file_exists($filePath)) {
      throw new \InvalidArgumentException("No se ha encontrado el archivo base de la plantilla de Landing Page");
    }

    $content = file_get_contents($filePath);

    if ($content == FALSE) {
      throw new \InvalidArgumentException("No se pudo leer correctamente el archivo base de la LandingPage");
    }

    $site = json_decode($content);

    return (array) $site;
  }

  public function saveContentLandingPageTemplate($idlpt, $data) {
    if (!isset($idlpt)) {
      throw new \InvalidArgumentException("No se ha podido obtener el argumento de Landing Page");
    }

    $lptemplate = \LandingPageTemplate::findFirst(array(
                "columns" => "idLandingPageTemplate",
                "conditions" => "idLandingPageTemplate = ?0",
                "bind" => array($idlpt)
    ));

    if (!$lptemplate) {
      throw new \InvalidArgumentException("La plantilla de landing page a la que intenta guardar contenido, no existe");
    }

    $objSubaccount = $this->user->Usertype->Subaccount;

    $json = json_encode($data['data']);

    $filePath = "{$this->dirPublic}/assets/{$objSubaccount->Account->idAccount}/landing-pages-templates/{$lptemplate->idLandingPageTemplate}/site{$lptemplate->idLandingPageTemplate}.json";

    if (!file_exists($filePath)) {
      throw new \InvalidArgumentException("El archivo base de la plantilla de landing page no se pudo crear correctamente");
    }

    if (file_put_contents($filePath, $json) === FALSE) {
      throw new \InvalidArgumentException("No se pudo escribir el contenido de la plantilla de landing page en el archivo base");
    }
    
    $this->createThumbnailLandingPageTemplate($lptemplate->idLandingPageTemplate);

    return ["responseCode" => 1, "urlRedirect" => "landingpagetemplate#/", "message" => "El contenido se ha guardado correctamente"];
  }

  public function uploadFileLandingPage($file) {
    $this->validateSpaceInAccount();
    $this->validateFile($file);
    $assetsManager = new AssetsManager();
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0",
                "bind" => array($this->getIdAccount())
    ));
    $assetsManager->setAccount($account);
    $assetsManager->setFile($file);
    $dirImage = $assetsManager->uploadImage();

    return ["message" => "La imagen se cargó correctamenta", "dirImage" => "{$this->asset->assets}{$this->getIdAccount()}/images/{$dirImage}", "code" => 1];
  }
  
  private function validateFile($file) {
    if (empty($file['name'])) {
      throw new InvalidArgumentException("No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información");
    }

    if ($file['error'] == 1) {
      throw new InvalidArgumentException("Ha ocurrido un error mientras se cargaba el archivo, por favor valide la información");
    }
  }
  
  private function validateSpaceInAccount() {
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0",
                "bind" => array($this->getIdAccount())
    ));

    if ($this->getSpace() >= $account->AccountConfig->diskSpace) {
      throw new InvalidArgumentException("Ha sobrepasaso el limite de espacio en disco. para liberar espacio en disco elimine imágenes o archivos que considere innecesarios");
    }
  }
  
  public function selectLandingPageTemplate($idlp, $idlpt){
    $landingpage = \LandingPage::findFirst(array(
        "conditions" => "idLandingPage = ?0",
        "bind" => array($idlp)
    ));
    
    if (!$landingpage) {
      throw new \InvalidArgumentException("La LandingPage a la que trata aplicarle una plantilla, no existe");
    }
    
    $landingpagetemplate = \LandingPageTemplate::findFirst(array(
        "conditions" => "idLandingPageTemplate = ?0",
        "bind" => array($idlpt)
    ));
    
    if (!$landingpagetemplate) {echo "Hola perras";
      throw new \InvalidArgumentException("La plantilla de LandingPage que intenta aplicar, no existe");
    }
    
    $filelp = "{$this->dirPublic}/{$this->asset->assets}/{$landingpage->Subaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/site{$landingpage->idLandingPage}.json";
    
    if (!file_exists($filelp)) {
      throw new \InvalidArgumentException("El archivo base de la LandingPage no existe, por favor comuniquese con soporte");
    }
    
    $filelpt = "{$this->dirPublic}/{$this->asset->assets}/{$landingpagetemplate->Account->idAccount}/landing-pages-templates/{$landingpagetemplate->idLandingPageTemplate}/site{$landingpagetemplate->idLandingPageTemplate}.json";
    
    if (!file_exists($filelpt)) {
      throw new Exception("El archivo base de la plantilla de LandingPage no existe, por favor comuníquese con soporte");
    }
    
    $jsonlp = file_get_contents($filelp);
    $jsonlpt = file_get_contents($filelpt);
    
    $contentlp = json_decode($jsonlp);
    $contentlpt = json_decode($jsonlpt);
    
    unset($jsonlp, $jsonlpt);
    
    array_push($contentlpt->pages->index->blocks, $contentlp->pages->index->blocks[0]);
    
    $newcontentlp = json_encode($contentlpt);
    
    unset($contentlpt, $contentlpt);
    
    if (!file_put_contents($filelp, $newcontentlp)) {
      throw new \InvalidArgumentException("No se pudo aplicar correctamente la plantilla");
    }
    
    return true;
  }
  
  private function createThumbnailLandingPageTemplate($idLandingPageTemplate) {
    $idAccount = ((isset($this->user->Usertype->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));

    $dirAcc = "{$this->dirPublic}{$this->asset->assets}{$idAccount}/landing-pages-templates/";

    if (isset($idAccount)) {
      $dir = "{$dirAcc}{$idLandingPageTemplate}/";
    }

    if (!file_exists($dir)) {
      mkdir($dirAcc, 0777, true);
    }

    $dirthumb = "{$dir}{$idLandingPageTemplate}_thumbnail.png";
    $domain = $this->urlManager->get_base_uri(true);

    exec("wkhtmltoimage --quality 25 --zoom 0.2 --width 180 --height 180 {$domain}landingpagetemplate/preview/{$idLandingPageTemplate} $dirthumb");
  }

}
