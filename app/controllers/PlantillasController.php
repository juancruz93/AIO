<?php

class PlantillasController extends ControllerBase
{

  public function indexAction() {
    
  }

  public function defaultAction() {
    $variable="hola mundo";
    $this->view->setVar('variable',$variable);    
  }

}
