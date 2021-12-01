<?php


class FooterTextBlock extends Modelbase
{

  public $idFooterTextBlock,
      $idMasterAccountTheme,
      $created,
      $updated,
      $align,
      $createdBy,
      $updatedBy,
      $deleted;
  
   public function getSource() {
    return "footer_text_block";
  }
 }