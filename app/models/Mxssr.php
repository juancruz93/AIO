<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mxssr
 *
 * @author juan.pinzon
 */
class Mxssr extends Modelbase {
  
  public $idMxssr;
  public $idMasteraccount;
  public $idSmsSendingRule;
  public $deleted;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idSmsSendingRule", "SmsSendingRule", "idSmsSendingRule");
  }

}
