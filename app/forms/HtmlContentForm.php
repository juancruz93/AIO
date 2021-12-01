<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Textarea;
class HtmlContentForm extends Form {
  public function initialize(){
    $content = new Textarea("content", array(
            "class" => "form-control",
            "minlength" => 20,
            "rows" => 4,
            "required" => "true"
        ));
    $this->add($content);
  }
}

