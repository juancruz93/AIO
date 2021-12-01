<?php

namespace Sigmamovil\Wrapper;

require_once '../app/library/dompdf/autoload.inc.php';

use MongoDB\Driver\Query;
use Psr\Log\InvalidArgumentException;

class CustomizingWrapper extends \BaseWrapper {

  private $customizing,
          $themes = [],
          $socials = [],
          $infosordered;

  function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }

  public function findCustomizing() {

    $idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied;

    $this->data = \PersonalizationThemes::find(["conditions" => "(idAllied = ?0 OR idAllied is NULL) AND deleted = 0 ", "bind" => [0 => $idAllied]]);
    $this->modelData();
  }

  public function validateSelected($themes) {

    $rs = false;
    foreach ($themes as $theme) {
      if ($theme->status == 'selected') {
        $rs = true;
      }
    }
    return $rs;
  }

  public function setDataArray($data) {
    $this->data = (object) $data;
  }

  public function modelDataSocialNetwork() {
    $arr = array();
    foreach ($this->data as $data) {
      $socials = new \stdClass();
      $socials->idSocialNetwork = $data->idSocialNetwork;
      $socials->name = $data->name;
      $socials->title = $data->title;
      $socials->img = $data->img;
      $socials->updated = $data->updated;
      $socials->created = $data->created;
      $socials->updatedBy = $data->updatedBy;
      $socials->createdBy = $data->createdBy;
      $socials->deleted = $data->deleted;
      $socials->createdDate = date("d/m/Y", $data->created);
      $socials->updatedDate = date("d/m/Y", $data->updated);
      $socials->createdHour = date("H:i a", $data->created);
      $socials->updatedHour = date("H:i a", $data->updated);

      array_push($arr, $socials);
    }
    array_push($this->socials, ["items" => $arr]);
    array_push($this->socials, ["url" => \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true)]);
  }

  public function modelData() {
    $flag = false;
    if (!$this->validateSelected($this->data)) {
      $flag = true;
    }
    $arr = array();
    foreach ($this->data as $data) {
      $themes = new \stdClass();
      $themes->idPersonalizationThemes = $data->idPersonalizationThemes;
      $themes->idAllied = $data->idAllied;
      $themes->name = $data->name;
      $themes->description = $data->description;
      $themes->title = $data->title;
      $themes->headerColor = $data->headerColor;
      $themes->mainColor = $data->mainColor;
      $themes->linkColor = $data->linkColor;
      $themes->linkHoverColor = $data->linkHoverColor;
      $themes->footerColor = $data->footerColor;
      $themes->headerTextColor = $data->headerTextColor;
      $themes->mainTitle = $data->mainTitle;
      $themes->footerIconColor = $data->footerIconColor;
      $themes->userBoxColor = $data->userBoxColor;
      $themes->userBoxHoverColor = $data->userBoxHoverColor;
      $themes->updated = $data->updated;
      $themes->created = $data->created;
      $themes->updatedBy = $data->updatedBy;
      $themes->createdBy = $data->createdBy;
      $themes->deleted = $data->deleted;
      if($data->logoRoute){
      $themes->logoRoute = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . $data->logoRoute;
      }
//      echo "Estado:";
//      var_dump($data->status==null);
//      exit;
      $themes->status = $data->status;
      if ($flag) {
        if ($data->idAllied == null) {
          $themes->status = 'selected';
        }
      }
      $themes->createdDate = date("d/m/Y", $data->created);
      $themes->updatedDate = date("d/m/Y", $data->updated);
      $themes->createdHour = date("H:i a", $data->created);
      $themes->updatedHour = date("H:i a", $data->updated);

      array_push($arr, $themes);
    }
//    var_dump($this->themes);
//    echo "<br>";
//    var_dump($arr);
//    exit;
    array_push($this->themes, ["items" => $arr]);
    array_push($this->themes, ["url" => \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true)]);
  }

  public function setLogo($logo) {
    $this->logo = (object) $logo;
  }

  public function saveCustomizing() {

    $theme = new \PersonalizationThemes();
    $theme->idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied;
    $theme->name = $this->data->name;
    $validateName = \PersonalizationThemes::findFirst(["conditions" => "name = ?0 AND idAllied = ?1 AND deleted = 0", "bind" => [0 => $this->data->name, 1 => \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied]]);
    if ($validateName != false) {
      throw new \InvalidArgumentException("El nombre '{$this->data->name}' ya se encuentra registrado, por favor seleccione otro nombre");
    }

    if (strlen($this->data->name) > 35) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 35 caracteres");
    }
    if (!$theme->name) {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
    if (isset($this->data->description)) {
      $theme->description = $this->data->description;

      if (strlen($this->data->description) > 200) {
        throw new \InvalidArgumentException("El campo descripción no puede tener mas de 200 caracteres");
      }
    }

    if ($this->data->title) {
      $theme->title = $this->data->title;
      if (strlen($this->data->title) > 80) {
        throw new \InvalidArgumentException("El campo 'Título de la pestaña (PRINCIPAL)' no puede tener mas de 80 caracteres");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Título de la pestaña (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->mainColor) {
      $theme->mainColor = $this->data->mainColor;
      if (!$this->validateColor($this->data->mainColor)) {
        throw new \InvalidArgumentException("El campo 'Color principal (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color principal (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->linkColor) {
      $theme->linkColor = $this->data->linkColor;
      if (!$this->validateColor($this->data->linkColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->linkHoverColor) {
      $theme->linkHoverColor = $this->data->linkHoverColor;
      if (!$this->validateColor($this->data->linkHoverColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los enlaces cuando pasa por encima (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->mainTitle) {
      $theme->mainTitle = $this->data->mainTitle;
      if (strlen($this->data->mainTitle) > 80) {
        throw new \InvalidArgumentException("El campo 'Título del logo (CABECERA)' no puede tener mas de 80 caracteres");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Título del logo (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->headerColor) {
      $theme->headerColor = $this->data->headerColor;
      if (!$this->validateColor($this->data->headerColor)) {
        throw new \InvalidArgumentException("El campo 'Color de la cabecera (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de la cabecera (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->headerTextColor) {
      $theme->headerTextColor = $this->data->headerTextColor;
      if (!$this->validateColor($this->data->headerTextColor)) {
        throw new \InvalidArgumentException("El campo 'Color de la letra (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de la letra (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->userBoxColor) {
      $theme->userBoxColor = $this->data->userBoxColor;
      if (!$this->validateColor($this->data->userBoxColor)) {
        throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->userBoxHoverColor) {
      $theme->userBoxHoverColor = $this->data->userBoxHoverColor;
      if (!$this->validateColor($this->data->userBoxHoverColor)) {
        throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario cuando pasa por encima (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->footerColor) {
      $theme->footerColor = $this->data->footerColor;
      if (!$this->validateColor($this->data->footerColor)) {
        throw new \InvalidArgumentException("El campo 'Color de fondo (PIE DE PÁGINA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de fondo (PIE DE PÁGINA)' es de caracter obligatorio");
    }

    if ($this->data->footerIconColor) {
      $theme->footerIconColor = $this->data->footerIconColor;
      if (!$this->validateColor($this->data->footerIconColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los iconos (PIE DE PÁGINA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los iconos (PIE DE PÁGINA)' es de caracter obligatorio");
    }

//    var_dump($this->data->socialsordered) ;
//    var_dump(isset($this->data->socialsordered[0]->idSocial)) ;
//    exit;
    $this->db->begin();
    $theme->status = "unselected";

    $this->validateSocialNetworks();
//    $this->validateInfoBlock();

    if (!$theme->save()) {
      foreach ($theme->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        $this->db->rollback();
        throw new \InvalidArgumentException($msg);
      }
    }

    if (isset($this->logo->tmp_name)) {
      $this->setLogoRoute($theme->idPersonalizationThemes);
      $theme->logoRoute = $this->logoRoute;
      $theme->deleted = 0;
      if (!$theme->save()) {
        foreach ($theme->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          $this->db->rollback();
          throw new \InvalidArgumentException($msg);
        }
      }
    }
    if (isset($this->socialsordered[0]->idSocial)) {
      $this->saveSocialNetworks($theme->idPersonalizationThemes);
    }
    if (isset($this->infosordered[0]->textInfo)) {
      $this->saveInfoBlock($theme->idPersonalizationThemes);
    }
    $this->db->commit();
    return $theme;
  }

  public function setLogoRoute($idPersonalizationThemes) {
//    $imobj = new \Sigmamovil\General\Misc\ImageObject();
//    
//    $imobj->resizeImage($w, $h);
//    if (is_uploaded_file($this->logo->tmp_name)) {
    $location = \Phalcon\DI::getDefault()->get('path')->path . '/public/images/logos';
    if (!file_exists($location)) {
      mkdir($location, 0777, true);
    }
    $filename = $idPersonalizationThemes . ".png";
    if (move_uploaded_file($this->logo->tmp_name, $location . '/' . $filename)) {
      $this->logoRoute = 'images/logos/' . $filename;
    } else {
      throw new \InvalidArgumentException("No se ha podido subir el archivo");
    }
  }

  function saveSocialNetworks($idPersonalizationThemes) {

    $block = new \FooterBlock();
    $block->idPersonalizationThemes = $idPersonalizationThemes;
    $block->position = $this->socialBlockPosition;
    if (!$block->save()) {
      foreach ($block->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        $this->db->rollback();
        throw new \InvalidArgumentException($msg);
      }
    }

    foreach ($this->socialsordered as $social) {
      if (isset($social->idSocial) && !isset($social->positionSocial)) {
        throw new \InvalidArgumentException("Hay alguna posición que no está definida o es incorrecta");
      }
      if ((isset($social->urlSocial) or isset($social->titleSocial) or isset($social->positionSocial)) && !isset($social->idSocial)) {
        throw new \InvalidArgumentException("No se puede dejar el nombre vacío en una red social ");
      }

      $perSocial = new \PersonalizationSocialNetwork;
      $perSocial->idFooterBlock = $block->idFooterBlock;
      $perSocial->idSocialNetwork = $social->idSocial;
      if(preg_match( '/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$data["url"])){
         $perSocial->url = $social->urlSocial;
      }else{
        //Concateno SCHEME con los datos de la URL
        $parse = 'http://' . $social->urlSocial;
        //Realiza una comparación con la variable $parse
        if(preg_match( '/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$parse)){
            //Enviamos los datos de la URL concatenada SCHEME
            $perSocial->url = 'http://' . $social->urlSocial;
        }else {
            throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");     
        }
      }
      $perSocial->title = $social->titleSocial;
      $perSocial->position = $social->positionSocial;
      if (!$perSocial->save()) {
        foreach ($perSocial->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          $this->db->rollback();
          throw new \InvalidArgumentException($msg);
        }
      }
    }
  }

  function editSocialNetworks($idPersonalizationThemes) {

    $block = new \FooterBlock();
    $block->idPersonalizationThemes = $idPersonalizationThemes;
    $block->position = $this->socialBlockPosition;
    if (!$block->save()) {
      foreach ($block->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    foreach ($this->socialsordered as $social) {
      if (isset($social->idSocial) && !isset($social->positionSocial)) {
        throw new \InvalidArgumentException("Hay alguna posición que no está definida o es incorrecta");
      }
      if ((isset($social->urlSocial) or isset($social->titleSocial) or isset($social->positionSocial)) && !isset($social->idSocial)) {
        throw new \InvalidArgumentException("No se puede dejar el nombre vacío en una red social ");
      }

      $perSocial = new \PersonalizationSocialNetwork;
      $perSocial->idFooterBlock = $block->idFooterBlock;
      $perSocial->idSocialNetwork = $social->idSocial;
      if(preg_match( '/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$data["url"])){
         $perSocial->url = $social->urlSocial;
      }else{
        //Concateno SCHEME con los datos de la URL
        $parse = 'http://' . $social->urlSocial;
        //Realiza una comparación con la variable $parse
        if(preg_match( '/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$parse)){
            //Enviamos los datos de la URL concatenada SCHEME
            $perSocial->url = 'http://' . $social->urlSocial;
        }else {
            throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");     
        }
      }
      $perSocial->title = $social->titleSocial;
      $perSocial->position = $social->positionSocial;
      if (!$perSocial->save()) {
        foreach ($perSocial->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
    }
  }

  function validateSocialNetworks() {

    foreach ($this->socialsordered as $social) {


      if ((isset($social->idSocial) or $social->idSocial != "") && (!isset($social->positionSocial) or $social->positionSocial == "")) {
        throw new \InvalidArgumentException("Hay alguna posición que no está definida o es incorrecta en REDES SOCIALES");
      }
      if ((isset($social->urlSocial) or isset($social->titleSocial) or isset($social->positionSocial)) && (!isset($social->idSocial) or $social->idSocial == "")) {
//        if($social->urlSocial!="" or $social->titleSocial!="" or $social->positionSocial!=""){
        throw new \InvalidArgumentException("No se puede dejar el nombre vacío en una red social ");
//        }
      }

      $cont = 0;
      foreach ($this->socialsordered as $soc) {
        if ($social->positionSocial == $soc->positionSocial) {
          $cont++;
        }
      }

      if ($cont > 1) {
        throw new \InvalidArgumentException("Hay alguna posición repetida en REDES SOCIALES");
      }
      if ((isset($social->idSocial) or $social->idSocial != "")) {
        $cont = 0;
        for ($x = 1; $x <= count($this->socialsordered); $x++) {
          foreach ($this->socialsordered as $social) {
            if ($x == $social->positionSocial) {
              $cont++;
            }
          }
        }

        if ($cont < count($this->socialsordered)) {
          throw new \InvalidArgumentException("Debe posicionar correctamente las REDES SOCIALES");
        }
      }
    }
  }

  function validateInfoBlock() {
//    var_dump($this->data->infosordered);
//    exit;
//    if(count($this->infosordered[0]['textInfo'])>50){
//          throw new \InvalidArgumentException("El texto del bloque de información no puede superar los 50 carcateres");
//    }
//    foreach ($this->infosordered as $info) {
//      if ((isset($info->textInfo) or $info->textInfo != "") && (!isset($info->positionInfo) or $info->positionInfo == "")) {
//        throw new \InvalidArgumentException("Hay alguna posición que no está definida o es incorrecta en INFORMACIÓN ADICIONAL");
//      }
//      if ((isset($info->positionInfo) or $info->positionInfo != "") && (!isset($info->textInfo) or $info->textInfo == "")) {
//        throw new \InvalidArgumentException("No se puede dejar el texto vacío en información adicional");
//      }
//    }
  }

  function saveInfoBlock($idPersonalizationThemes) {
    $block = new \FooterBlock();
    $block->idPersonalizationThemes = $idPersonalizationThemes;
    $block->position = $this->infoBlockPosition;
    if (!$block->save()) {
      foreach ($block->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        $this->db->rollback();
        throw new \InvalidArgumentException($msg);
      }
    }

    foreach ($this->infosordered as $info) {
      $addInfo = new \AdditionalInfo();
      $addInfo->idFooterBlock = $block->idFooterBlock;
      $addInfo->text = $info->textInfo;
      $addInfo->position = $info->positionInfo;
//      var_dump($addInfo);
      if (!$addInfo->save()) {
        foreach ($addInfo->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          $this->db->rollback();
          throw new \InvalidArgumentException($msg);
        }
      }
//      var_dump($addInfo);
//    exit;
    }
  }

  function validateColor($colorCode) {
    if (!preg_match('/^#[0-9A-Fa-f]{3,6}$/', $colorCode)) {
      return false;
    }
    return true;
  }

  function getThemes() {
    return $this->themes;
  }

  function getSocialNetworks() {
    return $this->socials;
  }

  function getTheme() {
    return $this->theme;
  }

  /**
   * @param \PersonalizationThemes $theme
   */
  public function setTheme(\PersonalizationThemes $theme) {
    $this->theme = $theme;
  }

  public function setSocialNet(\PersonalizationSocialNetwork $socialNet) {
    $this->socialNet = $socialNet;
  }

  public function setAdditionalInfo(\AdditionalInfo $additionalInfo) {
    $this->additionalInfo = $additionalInfo;
  }

  public function setInfoBlock($blocks, $socialNet, $additionalInfo) {

    foreach ($blocks as $block) {

      if (count($socialNet) > 0) {
        if ($block->idFooterBlock == $socialNet[0]->idFooterBlock) {
          $this->socialBlockPosition = $block->position;
          $this->socialBlockId = $block->idFooterBlock;
        }
      }
      if (count($additionalInfo) > 0) {

        if ($block->idFooterBlock == $additionalInfo[0]->idFooterBlock) {
          $this->infoBlockPosition = $block->position;
          $this->infoBlockId = $block->idFooterBlock;
        }
      }
    }
  }

  public function deleteTheme() {
    $this->theme->deleted = time();
    if (!$this->theme->update()) {
      foreach ($this->theme->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo eliminar el tema personalizado, contacta al administrador para solicitar más información");
      }
    }
  }

  public function selectTheme() {
    $actualTheme = \PersonalizationThemes::findFirst(array('conditions' => "idAllied = ?0 AND status='selected' AND deleted=0", 'bind' => array($this->theme->idAllied)));
    if ($actualTheme) {
      $actualTheme->status = 'unselected';

      if (!$actualTheme->update()) {
        foreach ($actualTheme->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException("No se pudo des-seleccionar el tema personalizado actual, contacta al administrador para solicitar más información");
        }
      }
    }
    $this->theme->status = 'selected';
    if (!$this->theme->update()) {
      foreach ($this->theme->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo seleccionar el tema personalizado, contacta al administrador para solicitar más información");
      }
    }
  }

  public function selectDefaultTheme() {
    $idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied;
    $theme = \PersonalizationThemes::findFirst(array('conditions' => "idAllied = ?0 AND status='selected' AND deleted=0", 'bind' => array($idAllied)));
    if ($theme) {
      $theme->status = 'unselected';

      if (!$theme->update()) {
        foreach ($theme->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException("No se pudo des-seleccionar el tema personalizado actual, contacta al administrador para solicitar más información");
        }
      }
    }
  }

  public function modelTheme() {
    $data = $this->theme;
    $this->theme = array();
    $this->theme['idPersonalizationThemes'] = $data->idPersonalizationThemes;
    $this->theme['idAllied'] = $data->idAllied;
    $this->theme['name'] = $data->name;
    $this->theme['description'] = $data->description;
    $this->theme['title'] = $data->title;
    $this->theme['headerColor'] = $data->headerColor;
    $this->theme['mainColor'] = $data->mainColor;
    $this->theme['linkColor'] = $data->linkColor;
    $this->theme['linkHoverColor'] = $data->linkHoverColor;
    $this->theme['footerColor'] = $data->footerColor;
    $this->theme['headerTextColor'] = $data->headerTextColor;
    $this->theme['mainTitle'] = $data->mainTitle;
    $this->theme['footerIconColor'] = $data->footerIconColor;
    $this->theme['userBoxColor'] = $data->userBoxColor;
    $this->theme['userBoxHoverColor'] = $data->userBoxHoverColor;
    $this->theme['socials'] = $this->socials;
    $this->theme['infos'] = $this->infos;
    $this->theme['socialBlockPosition'] = $this->socialBlockPosition;
    $this->theme['socialBlockId'] = $this->socialBlockId;
    $this->theme['infoBlockPosition'] = $this->infoBlockPosition;
    $this->theme['infoBlockId'] = $this->infoBlockId;
    $this->theme['socialsordered'] = $this->socialsordered;
    $this->theme['infosordered'] = $this->infosordered;

    if ($data->logoRoute) {
      $this->theme['logoRoute'] = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . $data->logoRoute;
    } else {
      $this->theme['logoRoute'] = null;
    }
  }

  public function modelSocialNet() {
    $data = $this->socialNet;
    $this->socialNet = array();
    $this->socialNet['idPersonalizationSocialNetwork'] = $data->idPersonalizationSocialNetwork;
    $this->socialNet['idFooterBlock'] = $data->idFooterBlock;
    $this->socialNet['idSocial'] = $data->idSocialNetwork;
    $this->socialNet['urlSocial'] = $data->url;
    $this->socialNet['titleSocial'] = $data->title;
    $this->socialNet['positionSocial'] = (int) $data->position;
    $this->socialNet['created'] = $data->created;
    $this->socialNet['updated'] = $data->updated;
    $this->socialNet['deleted'] = $data->deleted;
    $this->socialNet['createdBy'] = $data->createdBy;
    $this->socialNet['updatedBy'] = $data->updatedBy;

    array_push($this->socials, $this->socialNet);

    $this->orderSocials();
  }

  public function modelAdditionalInfo() {
    $data = $this->additionalInfo;
    $this->additionalInfo = array();
    $this->additionalInfo['idAdditionalInfo'] = $data->idAdditionalInfo;
    $this->additionalInfo['idFooterBlock'] = $data->idFooterBlock;
    $this->additionalInfo['textInfo'] = $data->text;
    $this->additionalInfo['positionInfo'] = (int) $data->position;
    $this->additionalInfo['created'] = $data->created;
    $this->additionalInfo['updated'] = $data->updated;
    $this->additionalInfo['deleted'] = $data->deleted;
    $this->additionalInfo['createdBy'] = $data->createdBy;
    $this->additionalInfo['updatedBy'] = $data->updatedBy;

    array_push($this->infos, $this->additionalInfo);
    $this->orderInfos();
  }

  public function orderSocials() {
    $this->socialsordered = array();
    $cont = count($this->socials);
    for ($x = 1; $x <= $cont; $x++) {
      foreach ($this->socials as $social) {
        if ($social['positionSocial'] == $x) {

          array_push($this->socialsordered, $social);
//     
        }
      }
    }
  }

  public function orderInfos() {

    $this->infosordered = array();
    $cont = count($this->infos);
    for ($x = 1; $x <= $cont; $x++) {
      foreach ($this->infos as $info) {
        if ($info['positionInfo'] == $x) {

          array_push($this->infosordered, $info);
        }
      }
    }
  }

  public function editTheme() {

    //1. Validamos que no haya otro tema en la base de datos con el mismo nombre
//    $this->validateThemeName();
    //2. Asignamos los datos
    $this->theme->name = $this->data->name;
    $this->theme->description = $this->data->description;
    $this->theme->title = $this->data->title;
    $this->theme->headerColor = $this->data->headerColor;
    $this->theme->mainColor = $this->data->mainColor;
    $this->theme->linkColor = $this->data->linkColor;
    $this->theme->linkHoverColor = $this->data->linkHoverColor;
    $this->theme->footerColor = $this->data->footerColor;
    $this->theme->headerTextColor = $this->data->headerTextColor;
    $this->theme->mainTitle = $this->data->mainTitle;
    $this->theme->footerIconColor = $this->data->footerIconColor;
    $this->theme->userBoxColor = $this->data->userBoxColor;
    $this->theme->userBoxHoverColor = $this->data->userBoxHoverColor;
//Validamos

    if (strlen($this->data->name) > 35) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 35 caracteres");
    }
    if (!$this->data->name) {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
//   
    if ($this->data->description) {
      if (strlen($this->data->description) > 200) {
        throw new \InvalidArgumentException("El campo descripción no puede tener mas de 200 caracteres");
      }
    }

    if ($this->data->title) {
      if (strlen($this->data->title) > 80) {
        throw new \InvalidArgumentException("El campo 'Título de la pestaña (PRINCIPAL)' no puede tener mas de 80 caracteres");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Título de la pestaña (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->mainColor) {
      if (!$this->validateColor($this->data->mainColor)) {
        throw new \InvalidArgumentException("El campo 'Color principal (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color principal (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->linkColor) {
      if (!$this->validateColor($this->data->linkColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->linkHoverColor) {
      if (!$this->validateColor($this->data->linkHoverColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los enlaces cuando pasa por encima (PRINCIPAL)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los enlaces (PRINCIPAL)' es de caracter obligatorio");
    }

    if ($this->data->mainTitle) {
      if (strlen($this->data->mainTitle) > 80) {
        throw new \InvalidArgumentException("El campo 'Título del logo (CABECERA)' no puede tener mas de 80 caracteres");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Título del logo (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->headerColor) {
      if (!$this->validateColor($this->data->headerColor)) {
        throw new \InvalidArgumentException("El campo 'Color de la cabecera (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de la cabecera (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->headerTextColor) {
      if (!$this->validateColor($this->data->headerTextColor)) {
        throw new \InvalidArgumentException("El campo 'Color de la letra (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de la letra (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->userBoxColor) {
      if (!$this->validateColor($this->data->userBoxColor)) {
        throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->userBoxHoverColor) {
      if (!$this->validateColor($this->data->userBoxHoverColor)) {
        throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario cuando pasa por encima (CABECERA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color del cuadro de usuario (CABECERA)' es de caracter obligatorio");
    }

    if ($this->data->footerColor) {
      if (!$this->validateColor($this->data->footerColor)) {
        throw new \InvalidArgumentException("El campo 'Color de fondo (PIE DE PÁGINA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de fondo (PIE DE PÁGINA)' es de caracter obligatorio");
    }

    if ($this->data->footerIconColor) {
      if (!$this->validateColor($this->data->footerIconColor)) {
        throw new \InvalidArgumentException("El campo 'Color de los iconos (PIE DE PÁGINA)' no tiene el formato correcto");
      }
    } else {
      throw new \InvalidArgumentException("El campo 'Color de los iconos (PIE DE PÁGINA)' es de caracter obligatorio");
    }

    //Se validan los datos del footer
    $this->validateSocialNetworks();
//    $this->validateInfoBlock();
    if (isset($this->logo->tmp_name)) {
      $this->setLogoRoute($this->theme->idPersonalizationThemes);
      $this->theme->logoRoute = $this->logoRoute;
    }
//3. Guardamos el tema
    if (!$this->theme->save()) {

      //4. En caso de que haya ocurrido un error, obtenemos los mensajes de error del modelo y generamos una InvalidArgumentException
      foreach ($this->theme->getMessages() as $msg) {
        $this->logger->log("Message saving theme: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

//    //4. Guardamos los datos del footer
//
//    if (isset($this->socialsordered[0]->idSocial)) {
//      $this->saveSocialNetworks($this->theme->idPersonalizationThemes);
//    }
//
//    if (isset($this->infosordered[0]->textInfo)) {
//      $this->saveInfoBlock($this->theme->idPersonalizationThemes);
//    }
  }

  public function validateThemeName() {
    //1. Buscamos un tema en la base de datos con el nombre ingresado
    $th = \PersonalizationThemes::findFirst(array("conditions" => "idAllied = ?0 AND name = ?1", "bind" => array(\Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied, $this->data->name)));
    //2. Si existe el tema, y no es el mismo que se está editando, quiere decir que hay otro tema con el mismo nombre en la base de datos, entonces
    //generamos la una InvalidArgumentException
    if ($th && $th->idPersonalizationThemes != $this->theme->idPersonalizationThemes) {
      throw new \InvalidArgumentException("Ya existe un tema personalizado guardado con el nombre '" . $this->data->name . "', por favor valida la información");
    }
  }

  public function findSocialNetworks() {

    $this->data = \SocialNetwork::find(array('conditions' => "deleted = 0", 'bind' => array()));
    if (!$this->data) {
      throw new InvalidArgumentException("No se han encontrado redes sociales, por favor intenta de nuevo");
    }

    $this->modelDataSocialNetwork();
  }

  public function setAllSocialNets($socialNets) {
    $this->socials = array();
    foreach ($socialNets as $socialNet) {
      $this->setSocialNet($socialNet);
      $this->modelSocialNet();
    }
  }

  public function setAllAdditionalInfos($additionalInfos) {
    $this->infos = array();
    foreach ($additionalInfos as $additionalInfo) {
      $this->setAdditionalInfo($additionalInfo);
      $this->modelAdditionalInfo();
    }
  }

  public function editFooter() {
//Se validan los datos del footer
    $this->validateSocialNetworks();
    $this->validateInfoBlock();
    if ($this->socialBlockId) {
      $block = \FooterBlock::findFirst(["conditions" => "idFooterBlock = ?0 AND deleted = 0 ", "bind" => [0 => $this->socialBlockId]]);
      $block->position = $this->socialBlockPosition;
      if (!$block->save()) {
        foreach ($block->getMessages() as $msg) {
          $this->logger->log("Message saving block: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      $otherblock = \FooterBlock::findFirst(["conditions" => "idFooterBlock != ?0 AND idPersonalizationThemes = ?1 AND deleted = 0 ", "bind" => [0 => $this->socialBlockId, 1 => $this->idPersonalizationThemes]]);
      if ($otherblock) {
        if ($this->socialBlockPosition == "right") {
          $otherPosition = "left";
        } else if ($this->socialBlockPosition == "left") {
          $otherPosition = "right";
        }
        $otherblock->position = $otherPosition;
        if (!$otherblock->save()) {
          foreach ($otherblock->getMessages() as $msg) {
            $this->logger->log("Message saving block: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }

      foreach ($this->socialsordered as $social) {

        if ($social->idPersonalizationSocialNetwork) {

          $perSocial = \PersonalizationSocialNetwork::findFirst(["conditions" => "idPersonalizationSocialNetwork= ?0 AND deleted = 0 ", "bind" => [0 => $social->idPersonalizationSocialNetwork]]);

          if (count($this->socialsDeleted) > 0 and in_array($perSocial->idPersonalizationSocialNetwork, $this->socialsDeleted)) {

            $perSocial->deleted = time();
          } else {
            $perSocial->url = $social->urlSocial;
            $perSocial->title = $social->titleSocial;
            $perSocial->position = $social->positionSocial;
            $perSocial->idSocialNetwork = $social->idSocial;
          }
        } else {

          $perSocial = new \PersonalizationSocialNetwork;
          $perSocial->idFooterBlock = $block->idFooterBlock;
          $perSocial->idSocialNetwork = $social->idSocial;
          $perSocial->url = $social->urlSocial;
          $perSocial->title = $social->titleSocial;
          $perSocial->position = $social->positionSocial;
        }
        if (!$perSocial->save()) {
          foreach ($perSocial->getMessages() as $msg) {
            $this->logger->log("Message saving perSocial: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    } else if (count($this->socialsordered) > 0) {

      $block = new \FooterBlock();

      $block->idPersonalizationThemes = $this->idPersonalizationThemes;
      $block->position = $this->socialBlockPosition;
      if (!$block->save()) {
        foreach ($block->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }

      foreach ($this->socialsordered as $social) {
        if (isset($social->idSocial) && !isset($social->positionSocial)) {
          throw new \InvalidArgumentException("Hay alguna posición que no está definida o es incorrecta");
        }
        if ((isset($social->urlSocial) or isset($social->titleSocial) or isset($social->positionSocial)) && !isset($social->idSocial)) {
          throw new \InvalidArgumentException("No se puede dejar el nombre vacío en una red social ");
        }
        $perSocial = new \PersonalizationSocialNetwork;
        $perSocial->idFooterBlock = $block->idFooterBlock;
        $perSocial->idSocialNetwork = $social->idSocial;
        if (isset($social->urlSocial)) {
          $perSocial->url = $social->urlSocial;
        }
        if (isset($social->titleSocial)) {
          $perSocial->title = $social->titleSocial;
        }
        $perSocial->position = $social->positionSocial;
        if (!$perSocial->save()) {
          foreach ($perSocial->getMessages() as $msg) {
            $this->logger->log("Message: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    }

    if ($this->infoBlockId) {
      $block = \FooterBlock::findFirst(["conditions" => "idFooterBlock = ?0 AND deleted = 0 ", "bind" => [0 => $this->infoBlockId]]);
      $block->position = $this->infoBlockPosition;

      if (!$block->save()) {
        foreach ($block->getMessages() as $msg) {
          $this->logger->log("Message saving block: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }

      $otherblock = \FooterBlock::findFirst(["conditions" => "idFooterBlock != ?0 AND idPersonalizationThemes = ?1 AND deleted = 0 ", "bind" => [0 => $this->infoBlockId, 1 => $this->idPersonalizationThemes]]);
      if ($otherblock) {
        if ($this->infoBlockPosition == "right") {
          $otherPosition = "left";
        } else if ($this->infoBlockPosition == "left") {
          $otherPosition = "right";
        }
        $otherblock->position = $otherPosition;
        if (!$otherblock->save()) {
          foreach ($otherblock->getMessages() as $msg) {
            $this->logger->log("Message saving block: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }
      foreach ($this->infosordered as $info) {
        if (isset($info->idAdditionalInfo)) {
          $perInfo = \AdditionalInfo::findFirst(["conditions" => "idAdditionalInfo= ?0 AND deleted = 0 ", "bind" => [0 => $info->idAdditionalInfo]]);

          if (count($this->infosDeleted) > 0 and in_array($perInfo->idAdditionalInfo, $this->infosDeleted)) {
            $perInfo->deleted = time();
          } else {
            $perInfo->text = $info->textInfo;
            $perInfo->position = $info->positionInfo;
          }
        } else {

          $perInfo = new \AdditionalInfo();
          $perInfo->idFooterBlock = $block->idFooterBlock;
          $perInfo->text = $info->textInfo;
          $perInfo->position = $info->positionInfo;
        }

        if (!$perInfo->save()) {
          foreach ($perInfo->getMessages() as $msg) {
            $this->logger->log("Message saving perInfo: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    } else if (count($this->infosordered) > 0) {
      $block = new \FooterBlock();

      $block->idPersonalizationThemes = $this->idPersonalizationThemes;
      $block->position = $this->infoBlockPosition;
      if (!$block->save()) {
        foreach ($block->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }

      if ($this->infosordered) {
        $info = $this->infosordered[0];
        $addInfo = new \AdditionalInfo();
        $addInfo->idFooterBlock = $block->idFooterBlock;
        $addInfo->text = $info->textInfo;
        $addInfo->position = $info->positionInfo;
        if (!$addInfo->save()) {
          foreach ($addInfo->getMessages() as $msg) {
            $this->logger->log("Message: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    }

  }

  public function setFooter($data) {

    $this->socialsordered = $this->data->socialsordered;
    $this->infosordered = $this->data->infos;
    $this->infoBlockId = $this->data->infoBlockId;
    $this->socialBlockId = $this->data->socialBlockId;
    $this->socialBlockPosition = $this->data->socialBlockPosition;
    $this->infoBlockPosition = $this->data->infoBlockPosition;
    $this->socialsDeleted = $this->data->socialsDeleted;
    $this->infosDeleted = $this->data->infosDeleted;
    $this->idPersonalizationThemes = $this->data->id;
  }

  public function setSocialsordered() {
    $this->socialsordered = array();
    if ($this->data->socialsordered) {
      foreach ($this->data->socialsordered as $ds) {
        array_push($this->socialsordered, (object) $ds);
      }
    }
  }

  public function setInfosordered($data) {
    $this->infosordered = array();
    if ($this->data->infosordered) {
      foreach ($this->data->infosordered as $ds) {
        array_push($this->infosordered, (object) $ds);
      }
    }
  }

  public function setSocialsdeleted($data) {
    $this->socialsDeleted = array();
    foreach ($data->socialsDeleted as $ds) {
      array_push($this->socialsDeleted, $ds);
    }
  }

  public function setInfosdeleted($data) {
    $this->infosDeleted = array();
    foreach ($data->infosDeleted as $ds) {
      array_push($this->infosDeleted, (object) $ds);
    }
  }

  function getInfosordered() {
    return $this->infosordered;
  }

}
