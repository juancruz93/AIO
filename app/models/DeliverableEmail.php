<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DeliverableEmail
 *
 * @author juan.pinzon
 */
class DeliverableEmail extends Modelbasemongo {

  public $idDeliverableEmail;
  public $email;
  public $idAccount;
  public $dateTime;
  public $score;
  public $source;
  public $created;
  public $updated;
  public $createdBy;
  public $updatedBy;
  public $idMail;
  public $name;
  public $idSubaccount;

//  public function writeAttribute($attribute, $value) {
//    return $this->{$attribute} = $value;
//  }

//  public function getSource() {
//    return "deliverable_email";
//  }

}
