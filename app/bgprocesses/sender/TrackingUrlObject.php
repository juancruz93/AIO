<?php

class TrackingUrlObject {

  protected $idMail;
  protected $idContact;
  protected $links;
  protected $html;
  protected $urls;
  protected $urlManager;
  protected $unsubscribe_link;
  protected $encoder;
  protected $mxc;

  public function __construct() {
    $this->urlManager = Phalcon\DI::getDefault()->get('urlManager');

    $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
    $this->encoder->setBaseUri(Phalcon\DI\FactoryDefault::getDefault()->get("urlManager")->get_base_uri(true));
  }

  public function getTrackingUrl($html, $idMail, $idContact, $urls,$survey = false, $idMailTracking = false, $landingpage = false) {
    $this->links = array();
    $this->idMail = $idMail;
    $this->idContact = $idContact;
    $this->html = $html;
    $this->urls = $urls;

    $this->getOpenTrackingUrl();
    $this->getClicksTrackingUrl();
    $this->getWebVersionTrack();
    $this->getSocialMediaShare();
    $this->getUnsubscribeTracking();    
   
    if($survey){
      $this->getSurveyTracking($survey);
    }
    
    if($idMailTracking){
      $this->getIdMailTracking($idMailTracking);
    }
    
    if($landingpage){
      $this->getLandingPageTracking($landingpage);
    }
    

    $htmlWithTracking = str_replace($this->links['search'], $this->links['replace'], $html);

    return $htmlWithTracking;
  }
  

  public function getTrackingUrlAutomatization($html, $idMail, $idContact, $urls) {
    $this->links = array();
    $this->idMail = $idMail;
    $this->idContact = $idContact;
    $this->html = $html;
    $this->urls = $urls;

    $this->getOpenTrackingUrlAutomatization();
    $this->getClicksTrackingUrlAutomatization();
    $this->getWebVersionTrack();
    $this->getSocialMediaShare();
    $this->getUnsubscribeTrackingAutomatizacion();

    $htmlWithTracking = str_replace($this->links['search'], $this->links['replace'], $html);

    return $htmlWithTracking;
  }

  public function getSocialTrackingUrl($html, $idMail, $idContact, $urls, $social) {
    $this->links = array();
    $this->idMail = $idMail;
    $this->idContact = $idContact;
    $this->html = $html;
    $this->urls = $urls;

    $this->getOpenTrackingUrl($social);
    $this->getClicksTrackingUrl($social);
    $this->getSocialMediaShare();

    $htmlWithTracking = str_replace($this->links['search'], $this->links['replace'], $html);

    return $htmlWithTracking;
  }

  public function getOpenTrackingUrl($social = false) {
    $linkdecoder = $this->encoder;

    if ($social !== false) {
      $action = 'track/opensocial';
      $parameters = array(1, $this->idMail, $this->idContact, $social);
    } else {
      $action = 'track/open';
      $parameters = array(1, $this->idMail, $this->idContact);
    }

    $url = $linkdecoder->encodeLink($action, $parameters);
    $img = '<img src="' . $url . '" />';
    
    $img .= "<!--
Start of DoubleClick Floodlight Tag: Please do not remove
Activity name of this tag: Sigmamovil RT
URL of the webpage where the tag is expected to be placed: http://www.sigmamovil.com
This tag must be placed between the <body> and </body> tags, as close as possible to the opening tag.
Creation Date: 10/06/2017
-->
<script type=\"text/javascript\">
var axel = Math.random() + \"\";
var a = axel * 10000000000000;
document.write('<img src=\"https://ad.doubleclick.net/ddm/activity/src=8212484;type=invmedia;cat=jdhbji85;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;ord=' + a + '?\" width=\"1\" height=\"1\" alt=\"\"/>');
</script>
<noscript>
<img src=\"https://ad.doubleclick.net/ddm/activity/src=8212484;type=invmedia;cat=jdhbji85;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;ord=1?\" width=\"1\" height=\"1\" alt=\"\"/>
</noscript>
<!-- End of DoubleClick Floodlight Tag: Please do not remove -->";     

    $this->links['search'][] = '$$$_open_track_$$$';
    $this->links['replace'][] = $img;
  }

  public function getOpenTrackingUrlAutomatization($social = false) {
    $linkdecoder = $this->encoder;

    if ($social !== false) {
      $action = 'track/opensocial';
      $parameters = array(1, $this->idMail, $this->idContact, $social);
    } else {
      $action = 'track/openautomatization';
      $parameters = array(1, $this->idMail, $this->idContact);
    }

    $url = $linkdecoder->encodeLink($action, $parameters);
    $img = '<img src="' . $url . '" />';

    $this->links['search'][] = '$$$_open_track_$$$';
    $this->links['replace'][] = $img;
  }

//  public function getTrackingUrlAutomatization($social = false) {
//    $linkdecoder = $this->encoder;
//
//    if ($social !== false) {
//      $action = 'track/opensocial';
//      $parameters = array(1, $this->idMail, $this->idContact, $social);
//    } else {
//      $action = 'track/openautomatization';
//      $parameters = array(1, $this->idMail, $this->idContact);
//    }
//
//    $url = $linkdecoder->encodeLink($action, $parameters);
//    $img = '<img src="' . $url . '" />';
//
//    $this->links['search'][] = '$$$_open_track_$$$';
//    $this->links['replace'][] = $img;
//  }

  public function getWebVersionTrack() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idMail, $this->idContact);
    $url = $linkdecoder->encodeLink('webversion/show', $parameters);

    $this->links['search'][] = '$$$_webversion_track_$$$';
    $this->links['replace'][] = $url;
  }

  public function getSocialMediaShare() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idMail, $this->idContact);
    $url = $linkdecoder->encodeLink('socialmedia/share', $parameters);

    $this->links['search'][] = '$$$_social_media_share_$$$';
    $this->links['replace'][] = $url . '-';
  }

  public function getUnsubscribeTracking() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idMail, $this->idContact);
    $url = $linkdecoder->encodeLink('unsubscribe/contact', $parameters);

    $this->links['search'][] = '$$$_unsubscribe_track_$$$';
    $this->links['replace'][] = $url;

    $this->unsubscribe_link = $url;
  }
  
  public function getUnsubscribeTrackingAutomatizacion() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idMail, $this->idContact);
    $url = $linkdecoder->encodeLink('unsubscribe/contactautomatic', $parameters);

    $this->links['search'][] = '$$$_unsubscribe_track_$$$';
    $this->links['replace'][] = $url;

    $this->unsubscribe_link = $url;
  }
  
  public function getSurveyTracking($survey){
    $url = "{$this->urlManager->get_base_uri(true)}survey/showsurvey/{$survey->idSurvey}/{$this->idContact}";
    
    $this->links['search'][] = '$$$_survey_track_$$$';
    $this->links['replace'][] = $url;
  }
  
  public function getIdMailTracking($idMailTracking){
    $this->links['search'][] = '$$$_id_mail_$$$';
    $this->links['replace'][] = $this->idMail;
  }

  public function getClicksTrackingUrl($social = false) {
    $linkdecoder = $this->encoder;

    if (count($this->urls) !== 0) {
      while ($true = current($this->urls)) {
        $this->links['search'][] = key($this->urls);
        $idMailLink = current($this->urls);

        if ($social !== false) {
          $action = 'track/clicksocial';
          $parameters = array(1, $idMailLink, $this->idMail, $this->idContact, $social);
        } else {
          $action = 'track/click';
          $parameters = array(1, $idMailLink, $this->idMail, $this->idContact);
        }

        $url = $linkdecoder->encodeLink($action, $parameters);

        $this->links['replace'][] = $url;
        next($this->urls);
      }
    }
  }
  public function getClicksTrackingUrlAutomatization($social = false) {
    $linkdecoder = $this->encoder;

    if (count($this->urls) !== 0) {
      while ($true = current($this->urls)) {
        $this->links['search'][] = key($this->urls);
        $idMailLink = current($this->urls);

        if ($social !== false) {
          $action = 'track/clicksocial';
          $parameters = array(1, $idMailLink, $this->idMail, $this->idContact, $social);
        } else {
          $action = 'track/clickautomatization';
          $parameters = array(1, $idMailLink, $this->idMail, $this->idContact);
        }

        $url = $linkdecoder->encodeLink($action, $parameters);

        $this->links['replace'][] = $url;
        next($this->urls);
      }
    }
  }

  public function searchDomainsAndProtocols($html, $text) {
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    $imgTag = new DOMDocument();
    @$imgTag->loadHTML($html);

    $hrefs = $imgTag->getElementsByTagName('a');

    $urls = array();
    if ($hrefs->length !== 0) {
      foreach ($hrefs as $href) {
        $link = $href->getAttribute('href');
        $domain = $this->validateDomain($link);
        if ($domain !== false && !in_array($domain, $urls)) {
          $urls[] = $domain;
        }
      }
    }

    if (preg_match_all($reg_exUrl, $text, $u)) {
      $links = $u[0];
      foreach ($links as $link) {
        $domain = $this->validateDomain($link);
        if ($domain !== false && !in_array($domain, $urls)) {
          $urls[] = $domain;
        }
      }
    }

    return $urls;
  }

  private function validateDomain($link) {
    $invalidDomains = array(
        'facebook' => '/[^\/]*\.*facebook.com.*$/',
        'twiter' => '/[^\/]*\.*twitter.com.*$/',
        'linkedin' => '/[^\/]*\.*linkedin.com.*$/',
        'google-plus' => '/[^\/]*\.*plus.google.com.*$/'
    );

    $parts = parse_url($link);
    foreach ($invalidDomains as $domain) {
      if (!isset($parts['host']) || empty($parts['host']) || preg_match($domain, $link)) {
        return false;
      }
    }

    return $parts['scheme'] . '://' . $parts['host'];
  }

  public function getUnsubscribeLink() {
    return $this->unsubscribe_link;
  }
  
  public function getLandingPageTracking($landingpage){
    
    $footerInfo = json_decode($landingpage->footerInfo);
    $titleLP = strtolower(str_replace(" ", "", $footerInfo->website));
    
    $url = "{$this->urlManager->get_base_uri(true)}lp/{$titleLP}/{$landingpage->idLandingPage}";    
    
    $this->links['search'][] = '$$$_landingpage_track_$$$';
    $this->links['replace'][] = $url;
    
  }

}
