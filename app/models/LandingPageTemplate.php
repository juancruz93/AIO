<?php

/**
 * Description of LandingPageTemplate
 *
 * @author juan.pinzon
 */
class LandingPageTemplate extends Modelbase {

  private $idLandingPageTemplate;
  private $idLandingPageTemplateCategory;
  private $idAllied;
  private $idAccount;
  private $created;
  private $updated;
  private $deleted;
  private $global;
  private $name;
  private $description;
  private $createdBy;
  private $updatedBy;

  public function initialize() {
    $this->belongsTo("idLandingPageTemplateCategory", "LandingPageTemplateCategory", "idLandingPageTemplateCategory");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->belongsTo("idAccount", "Account", "idAccount");
  }
  
  function getIdLandingPageTemplate() {
    return $this->idLandingPageTemplate;
  }

  function getIdLandingPageTemplateCategory() {
    return $this->idLandingPageTemplateCategory;
  }

  function getIdAllied() {
    return $this->idAllied;
  }

  function getIdAccount() {
    return $this->idAccount;
  }

  function getCreated() {
    return $this->created;
  }

  function getUpdated() {
    return $this->updated;
  }

  function getDeleted() {
    return $this->deleted;
  }

  function getGlobal() {
    return $this->global;
  }

  function getName() {
    return $this->name;
  }

  function getDescription() {
    return $this->description;
  }

  function getCreatedBy() {
    return $this->createdBy;
  }

  function getUpdatedBy() {
    return $this->updatedBy;
  }

  function setIdLandingPageTemplate($idLandingPageTemplate) {
    $this->idLandingPageTemplate = $idLandingPageTemplate;
  }

  function setIdLandingPageTemplateCategory($idLandingPageTemplateCategory) {
    $this->idLandingPageTemplateCategory = $idLandingPageTemplateCategory;
  }

  function setIdAllied($idAllied) {
    $this->idAllied = $idAllied;
  }

  function setIdAccount($idAccount) {
    $this->idAccount = $idAccount;
  }

  function setCreated($created) {
    $this->created = $created;
  }

  function setUpdated($updated) {
    $this->updated = $updated;
  }

  function setDeleted($deleted) {
    $this->deleted = $deleted;
  }

  function setGlobal($global) {
    $this->global = $global;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setCreatedBy($createdBy) {
    $this->createdBy = $createdBy;
  }

  function setUpdatedBy($updatedBy) {
    $this->updatedBy = $updatedBy;
  }

}
