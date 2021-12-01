<?php


class Masteraccountheme extends Modelbase
{

  public $idMasteraccount,
      $idMasterAccountTheme,
      $created,
      $updated,
      $title,
      $mainTittle,
      $mainColor,
      $linkColor,
      $linkHoverColor,
      $headerTextColor,
      $headerColor,
      $footerColor,
      $footerIconColor,
      $createdBy,
      $updatedBy,
      $userBoxColor,
      $userBoxHoverColor,
      $deleted;
  
   public function getSource() {
    return "master_account_theme";
  }
 }