<?php
/**
 * Description of Cxclxun
 *
 * @author jose.quinones
 */
class Cxclxun extends Modelbase
{
  public $idCxclxun,
      $idCxcl,
      $idUnsubscribed;   
  
  public function initialize() {
    $this->belongsTo("idCxcl", "Cxcl", "idCxcl");
    $this->belongsTo("idUnsubscribed", "Unsubscribed", "idUnsubscribed");
  }
}
