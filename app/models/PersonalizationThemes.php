<?php


class PersonalizationThemes extends Modelbase {

  public $idPersonalizationThemes;
  public $idAllied;
  public $name;
  public $description;
  public $title;
  public $headerColor;
  public $mainColor;
  public $linkColor;
  public $linkHoverColor;
  public $footerColor;
  public $headerTextColor;
  public $mainTitle;
  public $footerIconColor;
  public $userBoxColor;
  public $userBoxHoverColor;
  public $updated;
  public $created;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->hasOne("idAllied", "Allied", "idAllied");
  }



}
