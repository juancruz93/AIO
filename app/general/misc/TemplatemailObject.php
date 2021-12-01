<?php

namespace Sigmamovil\General\Misc;

class TemplatemailObject {

  protected $template;

  function __construct($template) {
    $this->template = $template;
  }

  function getLinksTemplate($html) {
    if (trim($html) === '') {
      throw new \InvalidArgumentException("Error mail's content is empty");
    }

    $htmlObj = new \DOMDocument();
    @$htmlObj->loadHTML($html);

    $links = $htmlObj->getElementsByTagName('a');

    $marks = [];

    if ($links->length !== 0) {
      foreach ($links as $link) {
        $url = trim($link->getAttribute('href'));
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
          $marks[] = $url;
        }
      }
    }



    return $marks;
  }

}
