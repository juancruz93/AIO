<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Hidden;

class PaymentForm extends Form {

  public function initialize() {
    $merchantId = new Hidden("merchantId", array(
        "value" => 508029
    ));
    $this->add($merchantId);

    $accountId = new Hidden("accountId", array(
        "value" => 512321
    ));
    $this->add($accountId);

    $description = new Hidden("description", array(
        "value" => "Test PAYU"
    ));
    $this->add($description);

    $referenceCode = new Hidden("referenceCode", array(
        "value" => "TestPayU"
    ));
    $this->add($referenceCode);

    $amount = new Hidden("amount", array(
        "value" => 3
    ));
    $this->add($amount);

    $tax = new Hidden("tax", array(
        "value" => 0
    ));
    $this->add($tax);

    $taxReturnBase = new Hidden("taxReturnBase", array(
        "value" => 0
    ));
    $this->add($taxReturnBase);

    $currency = new Hidden("currency", array(
        "value" => "COP"
    ));
    $this->add($currency);

    $signature = new Hidden("signature", array(
        "value" => "ba9ffa71559580175585e45ce70b6c37"
    ));
    $this->add($signature);

    $test = new Hidden("test", array(
        "value" => 1
    ));
    $this->add($test);

    $buyerEmail = new Hidden("buyerEmail", array(
        "value" => "test@test.com"
    ));
    $this->add($buyerEmail);

    $responseUrl = new Hidden("responseUrl", array(
        "value" => "http://www.test.com/response"
    ));
    $this->add($responseUrl);

    $confirmationUrl = new Hidden("confirmationUrl", array(
        "value" => "http://www.test.com/confirmation"
    ));
    $this->add($confirmationUrl);
  }

}
