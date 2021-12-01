<?php

/**
 * Description of ValidateAutomaticCampaingStep
 *
 * @author desarrollo3
 */
class ValidateAutomaticCampaingStep {

  public $mail,
          $interval;

  public function __construct(Mail $mail, $interval) {
    $this->mail = $mail;
    $this->interval = $interval;
  }

  public function insertNextStepNoOpen() {
//    $mxc
  }

}
