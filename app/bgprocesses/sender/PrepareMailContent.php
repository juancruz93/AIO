<?php

class PrepareMailContent {

  protected $linkService;
  protected $imageService;
  private $googleAnalyticsEmail;

  public function __construct($linkService, $imageService, $mark = TRUE, $form = false) {
    $this->linkService = $linkService;
    $this->imageService = $imageService;
    $this->mark = $mark;
    $this->form = $form;
    $this->googleAnalyticsEmail = [];
  }

  public function processContent($html, $link_service = true, $mail) {
    if (trim($html) === '') {
      throw new \InvalidArgumentException("Error mail's content is empty");
    }

    $htmlObj = new DOMDocument();
    @$htmlObj->loadHTML($html);

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
    $arrLinkChangeDobleOptin = array();
    $arrLinkChangeSurvey = array();
    $arrLinkChangeLandingpage = array();
    $urlDobleOptin = \Phalcon\DI::getDefault()->get('path')->path ."/subscribe/survey/".$mail->Subaccount->idAccount;

    if ($mail->googleAnalytics == 1) {
      $this->googleAnalyticsEmail = Mailgoogleanalytics::findFirst(array(
                  "columns" => "idMailGoogleAnalytics, name, links",
                  "conditions" => "idMail = ?0",
                  "bind" => array($mail->idMail)
      ));
    }

    if ($links->length !== 0) {
      foreach ($links as $link) {
        //$linkHref = trim($link->getAttribute('href'));
        $linkHref = trim($this->addGoogleAnalyticsToContentMail($link));
        if ($linkHref != "%%WEBVERSION%%" && $linkHref != "%%UNSUBSCRIBE%%") {
          if (strpos($linkHref, '%%WEBVERSION%%')) {
            array_push($arrLinkChangeWebVersion, $linkHref);
            continue;
          } else if (strpos($linkHref, '%%UNSUBSCRIBE%%')) {
            array_push($arrLinkChangeUnsubscribe, $linkHref);
            continue;
          }
        }
        if ($linkHref != "%%SURVEY%%") {
          if (strpos($linkHref, '%%SURVEY%%')) {
            array_push($arrLinkChangeSurvey, $linkHref);
            continue;
          }
        }
        if ($linkHref != "%%LANDINGPAGE%%") {
          if (strpos($linkHref, '%%LANDINGPAGE%%')) {
            array_push($arrLinkChangeLandingpage, $linkHref);
            continue;
          }
        }
        if ($this->form) {
          if ($linkHref != "%%DOBLEOPTIN%%") {
            array_push($arrLinkChangeDobleOptin, $linkHref);
            continue;
          }
        }
        if ($this->linkService) {
          if(strpos($linkHref, "%%IDMAIL%%") !== false){
            $linkHref = str_replace ("%%IDMAIL%%",$mail->idMail,$linkHref);
            $str = $linkHref;
          }
          $mark = $this->linkService->getPlatformUrl($linkHref);
          if ($mark) {
            $link->setAttribute('href', $mark);
          }
        }
      }
      if ($this->linkService) {
        $marks = $this->linkService->getUrlMappings();
      }
    }



    $html = $htmlObj->saveHTML();

    if (count($arrLinkChangeWebVersion)) {
      $html = str_replace($arrLinkChangeWebVersion, '%%WEBVERSION%%', $html);
    }
    if (count($arrLinkChangeUnsubscribe)) {
      $html = str_replace($arrLinkChangeUnsubscribe, '%%UNSUBSCRIBE%%', $html);
    }
    if (count($arrLinkChangeDobleOptin)) {
      $html = str_replace($urlDobleOptin, '%%DOBLEOPTIN%%', $html);
    }
    if (count($arrLinkChangeSurvey)) {
      $html = str_replace($arrLinkChangeSurvey, '%%SURVEY%%', $html);
    }
    /*if (count($arrLinkChangeIdMail)) {
      $html = str_replace($arrLinkChangeIdMail, '%%IDMAIL%%', $html);
    }*/
    if (count($arrLinkChangeLandingpage)) {
      $html = str_replace($arrLinkChangeLandingpage, '%%LANDINGPAGE%%', $html);
    }

    $html1 = str_replace('%24%24%24', '$$$', $html);
    if ($this->form) {
      $html1 = str_replace('%%DOBLEOPTIN%%', '$$$_doble_optin_$$$', $html1);
    } else {
      $html1 = str_replace('%%DOBLEOPTIN%%', '', $html1);
    }


    $search = array('</body>', '%%WEBVERSION%%', '%%UNSUBSCRIBE%%', '%%SURVEY%%', '%%IDMAIL%%', '%%LANDINGPAGE%%');

    $open_track = ($this->mark) ? '$$$_open_track_$$$</body>' : '</body>';

    if ($this->mark) {
      $replace = array($open_track, '$$$_webversion_track_$$$', '$$$_unsubscribe_track_$$$', '$$$_survey_track_$$$', '$$$_id_mail_$$$', '$$$_landingpage_track_$$$');
    } else {
      $replace = array($open_track, '', '', '', '');
    }

    $html2 = str_ireplace($search, $replace, $html1);

    // Arreglo con [ HTML, MARCAS ]
    return array($html2, $marks);
  }

  private function addGoogleAnalyticsToContentMail($link) {
    if ($this->googleAnalyticsEmail) {
      $linksgae = json_decode($this->googleAnalyticsEmail->links);
      foreach ($linksgae as $linkgae) {
        if ($link->getAttribute('href') == $linkgae) {
          $linkfinal = "{$linkgae}/?utm_source=" . \Phalcon\DI\FactoryDefault::getDefault()->get("googleAnalyticsEmail")->utm_source . "&"
                  . "utm_medium=" . \Phalcon\DI\FactoryDefault::getDefault()->get("googleAnalyticsEmail")->utm_medium . "&"
                  . "utm_campaign=" . str_replace(" ", "%20", $this->googleAnalyticsEmail->name);
          return $linkfinal;
        }
      }
    }
    return $link->getAttribute('href');
  }

}
