<?php

class PaymentplanController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Planes de pago");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "paymentplan");
  }

  public function listAction() {
    
  }

  public function createAction() {
    $this->view->setVar("form", new PaymentplanForm());
  }

  public function editAction() {
    $this->view->setVar("form", new PaymentplanForm());
  }

  public function showAction() {
    
  }

  public function apiAction(){
    $ranges_prices = \RangesPrices::find();
    return $this->set_json_response($ranges_prices->toArray());
  }

  public function pricesAction($idRangesPrices){
    $ranges_prices = \RangesPrices::findFirst(array(
      "conditions" => "idRangesPrices  = ?0",
      "bind" => array($idRangesPrices)
    ));
    return $this->set_json_response($ranges_prices->toArray());
  }

}
