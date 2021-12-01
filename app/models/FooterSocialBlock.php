<?php


class FooterSocialBlock extends Modelbase
{

  public $idFooterSocialBlock,
      $idMasterAccountTheme,
      $created,
      $updated,
      $align,
      $createdBy,
      $updatedBy,
      $deleted;
  
   public function getSource() {
    return "footer_social_block";
  }
 }