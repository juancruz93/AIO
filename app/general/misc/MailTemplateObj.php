<?php

namespace Sigmamovil\General\Misc;

class MailTemplateObj {

  private $logger;
  private $db;
  private $mailTemplate;
  private $modelsManager;
  private $user;

  public function __construct(\MailTemplate $mailTemplate) {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->mailTemplate = $mailTemplate;
  }

  /**
   * Método que extrae todos los links de las imagenes del contenido html
   * 
   * @param String $editorContent
   * @return array
   */
  public function extractImgSrc($editorContent) {
    $arr = array();
    $content = $this->convertToHtml($editorContent);
    $html = new \DOMDocument();
    @$html->loadHTML($content);
    $images = $html->getElementsByTagName("img");
    if ($images->length > 0) {
      foreach ($images as $image) {
        $src = $image->getAttribute("src");
        array_push($arr, $src);
      }
    }
    return $arr;
  }

  
  /**
   * _Función para guardar en la base de datos las imágenes asociadas a una plantilla
   * @param type $editorContent
   * @throws \InvalidArgumentException
   */
  public function saveMailTemplateImage($editorContent) {
    $content = $this->convertToHtml($editorContent);

    $html = new \DOMDocument();
    @$html->loadHTML($content);
    $images = $html->getElementsByTagName("img");

    if ($images->length > 0) {

      foreach ($images as $image) {
        $src = $image->getAttribute("src");

        if ($this->validateImageSrc($src)) {
          $url = explode('/', $src);
          $key = (count($url) - 1);

          $idAsset = $url[$key];

          $asset = \Asset::findFirst(array(
                      "conditions" => "idAsset = ?0",
                      "bind" => array($idAsset)
          ));

          if (!$asset) {
            throw new \InvalidArgumentException("No se encontró el recurso");
          }

          $this->saveMailTemplateImageInDb($asset);
        }
      }
    }
  }
  
  /**
   * _Función para la actualización del mailtemplateimage en la base de datos, cuando haya nuevos elementos o
   * cuando se remuevan elementos de la plantillas
   * @param type $editorContent
   * @throws \InvalidArgumentException
   */
  public function updateMailTemplateImage($editorContent) {
    $content = $this->convertToHtml($editorContent);

    $html = new \DOMDocument();
    @$html->loadHTML($content);
    $images = $html->getElementsByTagName("img");

    $sql = "SELECT * FROM mail_template_image WHERE idMailTemplate = {$this->mailTemplate->idMailTemplate}";
    $mailtempimage = $this->db->fetchAll($sql);
    if ($images->length > 0) {
      foreach ($images as $image) {
        $src = $image->getAttribute("src");
        if ($this->validateImageSrc($src)) {
          $url = explode("/", $src);
          $key = (count($url) - 1);

          $idAsset = $url[$key];

          $asset = \Asset::findFirst(array(
                      "conditions" => "idAsset = ?0",
                      "bind" => array($idAsset)
          ));
          if (!$asset) {
            throw new \InvalidArgumentException("No se encontró el recurso");
          }
          if ($asset) {
            if (count($mailtempimage) > 0) {
              $bool = true;
              foreach ($mailtempimage as $key => $value) {
                if ($asset->idAsset === $value['idAsset']) {
                  $bool = false;//Esta bandera indica que el asset que se validó no es nuevo, entonces no se volverá a guardar
                  unset($mailtempimage[$key]);
                  break;
                }
              }
              if ($bool) {//Se cumple cuando el asset es nuevo en la plantilla, entonces se guardará
                $this->saveMailTemplateImageInDb($asset);
              }
            } else {
              $this->saveMailTemplateImageInDb($asset);
            }
          }
        }
      }
    }
    /**
     * Ciclo que elimina de la base de datos, las imagenes que ya no esten en el editor avanzado
     */
    foreach ($mailtempimage as $key => $value) {
      $mailtemplateimage = new \MailTemplateImage();
      $mailtemplateimage->idMailTemplateImage = $mailtempimage[$key]['idMailTemplateImage'];
      $mailtemplateimage->idMailTemplate = $mailtempimage[$key]['idMailTemplate'];
      $mailtemplateimage->name = $mailtempimage[$key]['name'];
      if ($mailtemplateimage->delete()) {
        foreach ($mailtemplateimage->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }

    $a = $html->getElementsByTagName("a");

    if ($a->length > 0) {
      foreach ($a as $value) {
        $href = $value->getAttribute("href");
        if($this->validateAHref($href)) {
          $maillink = \Maillink::findFirst(array(
            'colums' => 'idMail_link',
            'conditions' => 'link = ?0 AND idAccount = ?1',
            'bind' => array($href, $this->user->Usertype->Subaccount->idAccount)
          ));
          if (!$maillink) {
            $maillink = new \Maillink();
            $maillink->idAccount = $this->user->Usertype->Subaccount->idAccount;
            $maillink->link = $href;
            $maillink->created = time();

            if (!$maillink->save()) {
              foreach ($maillink->getMessages() as $msg) {
                Phalcon\DI::getDefault()->get('logger')->log('Error saving link: ' . $msg);
              }
              throw new InvalidArgumentException('Error while saving Maillink');
            }
          }
        }
      }
    }
  }
  
  /**
   * _Function for save MailTemplateImage in the database
   * @param \Asset $asset
   * @return \MailTemplateImage
   * @throws \InvalidArgumentException
   */
  public function saveMailTemplateImageInDb(\Asset $asset) {
    $mailtemplateimage = new \MailTemplateImage();
    $mailtemplateimage->idMailTemplate = $this->mailTemplate->idMailTemplate;
    $mailtemplateimage->idAsset = $asset->idAsset;
    $mailtemplateimage->name = $asset->name;

    if (!$mailtemplateimage->save()) {
      $this->db->rollback();
      throw new \InvalidArgumentException("Error guardando una imagen de la plantilla");
    }

    return $mailtemplateimage;
  }
  
  /**
   * _Function for convert JSON to HTML
   * @param type $editorContent
   * @return type EditorContent in HTML
   */
  private function convertToHtml($editorContent) {
    $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount(null);
    $editorObj->assignContent(json_decode($editorContent));
    $content = $editorObj->render();

    return $content;
  }

  private function validateImageSrc($src) {
    if (!preg_match('/asset\/show/', $src)) {
      return false;
    }
    return true;
  }

  private function validateAHref($href) {
    if (!preg_match('%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i', $href)) {
      return false;
    }
    return true;
  }

}
