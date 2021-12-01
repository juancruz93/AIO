<?php

namespace Sigmamovil\General\View;

class PersonalizedCss {

  private $user;
  private $logger;

  public function __construct() {
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

  public function getPersonalizedCss() {

    //Por defecto se establece el tema personalizado como el Default de AIO

    $theme = \PersonalizationThemes::findFirst(array('conditions' => "idAllied is null AND deleted = 0", 'bind' => array()));
    // Si el usuario loggeado es diferente a el usuario root  
   
    if ($this->user->idRole != \Phalcon\DI::getDefault()->get('roles')->root and $this->user->idRole != \Phalcon\DI::getDefault()->get('roles')->master) {

      //Buscamos el tema que está seleccionado para el masteraccount del usuario que está loggeado
      $this->getIdAllied();
      $pertheme = \PersonalizationThemes::findFirst(array('conditions' => "idAllied = ?0 AND deleted = 0 AND status = 'selected'", 'bind' => array($this->idAllied)));

      //Si encontramos un tema seleccionado se establece, sino se deja Default

      if ($pertheme) {
        $theme = $pertheme;
      }
    }
    //Se setean los valores del tema
    if ($theme) {
      $this->setValues($theme);
    }

    $css = ' <style>
                    .per-link-cutomized{
                      color: ' . $this->linkColor . ';
                    }
                    .per-link-cutomized:hover{
                      text-decoration: none;
                      color: ' . $this->linkHoverColor . ' !important;
                    }
                    /*Color de las líneas con el color principal*/
                    .per-topLine{
                      border-bottom-color: ' . $this->mainColor . ';
                    }
                    .per-bottomLine{
                      border-top-color: ' . $this->mainColor . ';
                    }

                    /*Color de la caja del usuario*/
                    .per-userBoxColor{
                      background: ' . $this->userBoxColor . ';
                    }
                    /*Color de la caja del usuario en hover*/
                    .per-userBoxColor:hover{
                      background: ' . $this->userBoxHoverColor . ' !important;
                    }
                    /*Color de los iconos del footer en hover*/
                    .per-dashed-effect-customized .per-hi-icon-customized:hover{
                      color: ' . $this->mainColor . ' !important;
                    }
                    /*Color de los iconos del footer*/
                    .per-dashed-effect-customized .per-hi-icon-customized{
                      color: ' . $this->footerIconColor . ';
                      box-shadow: 0 0 0 4px ' . $this->mainColor . ';
                    }
                    /*Color de los iconos del footer*/
                    .per-icon-footer-color > li > a:after{
                      border: 2px dashed ' . $this->mainColor . ' !important;
                    }
                    /*Color de la letra del header*/
                    .per-icon-footer-color .navbar-nav > li > a, .per-headerTextColor{
                      color: ' . $this->headerTextColor . ';
                    }
                    /*Hover del texto del header*/
                    .per-headerTextColor:hover{
                      text-decoration: none;
                      color: ' . $this->mainColor . ' !important;
                    }
                    /*Texto del header*/
                    .per-headerTextColor{
                      color: ' . $this->headerTextColor . ' !important;
                    }

                    /*Color del header*/
                    .per-container-fluid-customized{
                      background-color:' . $this->headerColor . ';
                    }

                    /*Color del footer*/
                    .per-footerColor{
                      background: ' . $this->footerColor . ';
                    }
                    
                    .item-menu-container>.shining>.active{
                        color: ' . $this->mainColor . ';
                        box-shadow: 0 0 0 0 ' . $this->mainColor . ';
                    }
                    
                    .ch-item{
                      border: 1px solid ' . $this->mainColor . ';
                        box-shadow: inset 0 0 0 0 ' . $this->mainColor . ', 
                        inset 0 0 0 16px rgba(245,245,245,.4), 0 1px 2px rgba(0,0,0,.1);
                    }
                    
                    .ch-info p a::hover {
                      color: ' . $this->mainColor . ' !important;
                    }

                    .ch-item:hover {
                      box-shadow: 
                        inset 0 0 0 110px ' . $this->mainColor . ',
                        inset 0 0 0 16px ' . $this->mainColor . ',
                        0 1px 2px rgba(0,0,0,0.1);
                    }
                    
                    .title{
                      color: ' . $this->mainColor . ';
                    }'
            . '</style>';

    //Se encuentran los datos del footer

    $blocks = \FooterBlock::find(["conditions" => "idPersonalizationThemes = ?0 AND deleted = 0 ", "bind" => [0 => $theme->idPersonalizationThemes]]);
    if (count($blocks) > 0) {
      $socialNets = \PersonalizationSocialNetwork::find(["conditions" => "(idFooterBlock = ?0 OR idFooterBlock = ?1) AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock, 1 => $blocks[1]->idFooterBlock]]);
      $additionalInfos = \AdditionalInfo::find(["conditions" => "(idFooterBlock = ?0 OR idFooterBlock = ?1) AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock, 1 => $blocks[1]->idFooterBlock]]);
      $this->setInfoBlock($blocks, $socialNets, $additionalInfos);

      if ($socialNets) {
        $this->setAllSocialNets($socialNets);
      }
      if ($additionalInfos) {
        $this->setAllAdditionalInfos($additionalInfos);
      }
    }
    $this->findSocialNetworks();

//    var_dump());
////    foreach ($this->socialNets as $val){
////      var_dump($val);
////    }
//    
//    exit;

    return $css;
  }

  public function setValues($theme) {
    $this->name = $theme->name;
    $this->description = $theme->description;
    $this->description = $theme->description;
    $this->title = $theme->title;
    $this->headerColor = $theme->headerColor;
    $this->mainColor = $theme->mainColor;
    $this->linkColor = $theme->linkColor;
    $this->linkHoverColor = $theme->linkHoverColor;
    $this->footerColor = $theme->footerColor;
    $this->headerTextColor = $theme->headerTextColor;
    $this->mainTitle = $theme->mainTitle;
    $this->footerIconColor = $theme->footerIconColor;
    $this->userBoxColor = $theme->userBoxColor;
    $this->userBoxHoverColor = $theme->userBoxHoverColor;
    $this->logoRoute = $theme->logoRoute;
  }

//  public function getLogoRoute() {
//    return \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . $this->logoRoute;
//  }
  
  public function getLogo() {

    $logo = '<div style="height: 44px;padding: 13px;">Logo</div>';
    if ($this->mainTitle) {
      $logo = '<div style="height: 44px;padding: 13px;">' . $this->mainTitle . '</div>';
    }
    if ($this->logoRoute) {
      $logo = '<img class="logo" src="' . \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . $this->logoRoute . '" style="width:45px;height:44px;padding-left: 5px;display:inline;" alt="Sigma Móvil"/>';
    }
    return $logo;
  }

  public function getIdAllied() {
    if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->allied) {
      $this->idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idAllied;
    } else if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->account) {
      $this->idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Account->Allied->idAllied;
    } else if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->subaccount) {
      $this->idAllied = \Phalcon\DI::getDefault()->get("user")->Usertype->Subaccount->Account->Allied->idAllied;
    }
  }
//  public function getIdMasterAccount() {
//    if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->master) {
//      $this->idMasteraccount = $idMasteraccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idMasteraccount;
//    } else if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->allied) {
//      $this->idMasteraccount = $idMasteraccount = \Phalcon\DI::getDefault()->get("user")->Usertype->Allied->idMasteraccount;
//    } else if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->account) {
//      $this->idMasteraccount = $idMasteraccount = \Phalcon\DI::getDefault()->get("user")->Usertype->Account->Allied->idMasteraccount;
//    } else if ($this->user->idRole == \Phalcon\DI::getDefault()->get('roles')->subaccount) {
//      $this->idMasteraccount = $idMasteraccount = \Phalcon\DI::getDefault()->get("user")->Usertype->Subaccount->Account->Allied->idMasteraccount;
//    }
//  }

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

  public function setSocialNet(\PersonalizationSocialNetwork $socialNet) {
    $this->socialNet = $socialNet;
  }

  public function setAdditionalInfo(\AdditionalInfo $additionalInfo) {
    $this->additionalInfo = $additionalInfo;
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
    for ($x = 0; $x <= $cont; $x++) {
      foreach ($this->infos as $info) {
        if ($info['positionInfo'] == $x) {

          array_push($this->infosordered, $info);
        }
      }
    }
  }

  public function getRightBlock() {
    $rigthBlock = '';
    if (isset($this->socialBlockPosition)) {
      if ($this->socialBlockPosition == 'right') {
        if (isset($this->socialsordered)) {
          $rigthBlock = '<div class="social-network" style="padding: 10px;">';
          foreach ($this->socialsordered as $social) {
            foreach ($this->socialNets as $sn) {

              if ($social['idSocial'] == $sn->idSocialNetwork) {
                $rigthBlock .= '
          <a href="' . $social['urlSocial'] . '" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $social['titleSocial'] . '">
            <img style="width:25px;" src="' . \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'themes/default/images/social-networks/' . $sn->img . '" />
          </a>';
              }
            }
          }
          $rigthBlock .= '</div>';
        }
      } else {
        if (isset($this->infosordered)) {
          $rigthBlock = ''
                  . '<div class="copy" style="padding: 10px;font-size: 15px;">' . $this->infosordered[0]['textInfo'] . '</div>';
        }
      }
    }


    return $rigthBlock;
  }

  public function getLeftBlock() {
    $leftBlock = '';
    if (isset($this->socialBlockPosition)) {
      if ($this->socialBlockPosition == 'left') {
        if (isset($this->socialsordered)) {
          $leftBlock = '<div class="social-network" style="padding: 10px;">';
          foreach ($this->socialsordered as $social) {
            foreach ($this->socialNets as $sn) {

              if ($social['idSocial'] == $sn->idSocialNetwork) {
                $leftBlock .= '
          <a href="' . $social['urlSocial'] . '" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $social['titleSocial'] . '">
            <img style="width:25px;" src="' . \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'themes/default/images/social-networks/' . $sn->img . '" />
          </a>';
              }
            }
          }
          $leftBlock .= '</div>';
        }
      } else {
        if (isset($this->infosordered)) {

          $leftBlock = '<div class="copy" style="padding: 10px;font-size: 15px;">' . $this->infosordered[0]['textInfo'] . '</div>';
        }
      }
    }
    return $leftBlock;
  }

  public function findSocialNetworks() {

    $this->socialNets = \SocialNetwork::find(array('conditions' => "deleted = 0", 'bind' => array()));
  }

}
