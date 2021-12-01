<?php
/**
 * Description of Unsubscribed
 *
 * @author jose.quinones
 */
class Unsubscribed extends Modelbase
{
  public $idUnsubscribed,
      $idMail,
      $idContact,    
      $motive,
      $option,
      $other,
      $deleted,
      $updated,
      $idCategories,
      $email,
      $idSubaccount;

  public function initialize() {
    $this->belongsTo("idMail", "Mail", "idMail");
  }
}
