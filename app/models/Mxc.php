<?php

/**
 * Description of Mxc
 *
 * @author desarrollo3
 */
class Mxc extends Modelbasemongo {

  public $open,
          $clicks,
          $bounced,
          $spam,
          $unsubscribed,
          $share_fb,
          $share_tw,
          $share_gp,
          $share_li,
          $open_fb,
          $open_tw,
          $open_gp,
          $open_li,
          $email;

//  public function writeAttribute($attribute, $value) {
//    return $this->{$attribute} = $value;
//  }

  public function getSource() {
    return "mxc";
  }

  public function beforeValidationOnCreate() {
    $this->bounced = 0;
    $this->clicks = 0;
    $this->open_fb = 0;
    $this->open_gp = 0;
    $this->open_li = 0;
    $this->open_tw = 0;
    $this->open = 0;
    $this->share_fb = 0;
    $this->share_gp = 0;
    $this->share_li = 0;
    $this->share_tw = 0;
    $this->spam = 0;
    $this->unsubscribed = 0;
  }

}
