<?php

namespace Sigmamovil\General\Misc;

class PrepareMailContent {

  protected $linkService;
  protected $imageService;

  public function __construct($linkService, $imageService, $mark = TRUE) {
    $this->linkService = $linkService;
    $this->imageService = $imageService;
    $this->mark = $mark;
  }

  public function processContent($html, $link_service = true) {
    if (trim($html) === '') {
      throw new \InvalidArgumentException("Error mail's content is empty");
    }

    $htmlObj = new \DOMDocument();
    //@$htmlObj->loadHTML($html);
    @$htmlObj->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $images = $htmlObj->getElementsByTagName('img');
    if ($link_service) {
      $links = $htmlObj->getElementsByTagName('a');
    } else {
      $links = new stdClass();
      $links->length = 0;
    }

    if ($images->length !== 0) {
      foreach ($images as $image) {
        $imageSrc = $image->getAttribute('src');
        $newSrc = $this->imageService->transformImageUrl($imageSrc);
        //var_dump("newSrc",$newSrc);
        if ($newSrc) {
          $image->setAttribute('src', $newSrc);
        }
      }
    }

    $marks = null;
    $arrLinkChangeWebVersion = array();
    $arrLinkChangeUnsubscribe = array();
    if ($this->linkService) {
      if ($links->length !== 0) {
        foreach ($links as $link) {
          $linkHref = trim($link->getAttribute('href'));
          if ($linkHref != "%%WEBVERSION%%" && $linkHref != "%%UNSUBSCRIBE%%") {
            if(strpos($linkHref, '%%WEBVERSION%%')){
              array_push($arrLinkChangeWebVersion, $linkHref);
              continue;
            }else if(strpos($linkHref, '%%UNSUBSCRIBE%%')){
              array_push($arrLinkChangeUnsubscribe, $linkHref);
              continue;
            }else {
              continue;
            }
          }
          $mark = $this->linkService->getPlatformUrl($linkHref);
          if ($mark) {
            $link->setAttribute('href', $mark);
          }
        }
        $marks = $this->linkService->getUrlMappings();
      }
    }


    $html = $htmlObj->saveHTML();
    
    if(count($arrLinkChangeWebVersion)){
      $html = str_replace($arrLinkChangeWebVersion, '%%WEBVERSION%%', $html);
    }
    if(count($arrLinkChangeUnsubscribe)){
      $html = str_replace($arrLinkChangeUnsubscribe, '%%UNSUBSCRIBE%%', $html);
    }
    $html1 = str_replace('%24%24%24', '$$$', $html);
    $search = array('</body>', '%%WEBVERSION%%', '%%UNSUBSCRIBE%%');

    $open_track = ($this->mark) ? '$$$_open_track_$$$</body>' : '</body>';

    if ($this->mark) {
      $replace = array($open_track, '$$$_webversion_track_$$$', '$$$_unsubscribe_track_$$$');
    } else {
      $replace = array($open_track, '', '');
    }

    $html2 = str_ireplace($search, $replace, $html1);

    // Arreglo con [ HTML, MARCAS ]
    return array($html2, $marks);
  }

}
