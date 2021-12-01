<?php

class ErrorController extends ControllerBase {

  public function indexAction() {
    
  }

  public function linkAction() {
    
  }

  public function notavailableAction() {
    $this->tag->setTitle("No disponible");
  }

  public function unauthorizedAction() {
    $this->tag->setTitle("Inautorizado");
  }

  public function forbiddenAction() {
    $this->tag->setTitle("Acceso Denegado");
  }
  
  public function maintenanceAction(){
    $this->tag->setTitle("Mantenimiento");
  }

}
