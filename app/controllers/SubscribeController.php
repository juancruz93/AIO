<?php

/**
 * Description of SubscribeController
 *
 * @author jose.quinones
 */
class SubscribeController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Suscripción");
    parent::initialize();
  }

  public function formAction($idAccount){
    $account = \Account::findFirst(array(
      "conditions" => "idAccount = {$idAccount} "
    ));
    $name = 'SIGMA MÓVIL S.A.S';
    if($account != false){
      $name = $account->name;
    }
    //
    $this->view->setVar('name', $name);
  }
  
}
