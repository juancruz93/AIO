<?php

/**
 * Description of TrackingUrlObjectAutomatization
 *
 * @author desarrollo3
 */
class TrackingUrlObjectAutomatization {

  protected $idAutomaticCampaignStep;
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

  public function getTrackingUrl($html, $idAutomaticCampaignStep, $idContact, $urls) {
    $this->links = array();
    $this->idAutomaticCampaignStep = $idAutomaticCampaignStep;
    $this->idContact = $idContact;
    $this->html = $html;
    $this->urls = $urls;

    $this->getOpenTrackingUrl();
    $this->getClicksTrackingUrl();
//    $this->getWebVersionTrack();
//    $this->getSocialMediaShare();
//    $this->getUnsubscribeTracking();

    $htmlWithTracking = str_replace($this->links['search'], $this->links['replace'], $html);

    return $htmlWithTracking;
  }

  public function getOpenTrackingUrl($social = false) {
    $linkdecoder = $this->encoder;

    if ($social !== false) {
      $action = 'track/opensocial';
      $parameters = array(1, $this->idAutomaticCampaignStep, $this->idContact, $social);
    } else {
      $action = 'track/open';
      $parameters = array(1, $this->idAutomaticCampaignStep, $this->idContact);
    }

    $url = $linkdecoder->encodeLink($action, $parameters);
    $img = '<img src="' . $url . '" />';

    $this->links['search'][] = '$$$_open_track_$$$';
    $this->links['replace'][] = $img;
  }

  public function getClicksTrackingUrl($social = false) {
    $linkdecoder = $this->encoder;

    if (count($this->urls) !== 0) {
      while ($true = current($this->urls)) {
        $this->links['search'][] = key($this->urls);
        $idMailLink = current($this->urls);

        if ($social !== false) {
          $action = 'track/clicksocial';
          $parameters = array(1, $idMailLink, $this->idAutomaticCampaignStep, $this->idContact, $social);
        } else {
          $action = 'track/click';
          $parameters = array(1, $idMailLink, $this->idAutomaticCampaignStep, $this->idContact);
        }

        $url = $linkdecoder->encodeLink($action, $parameters);

        $this->links['replace'][] = $url;
        next($this->urls);
      }
    }
  }

  public function getWebVersionTrack() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idAutomaticCampaignStep, $this->idContact);
    $url = $linkdecoder->encodeLink('webversion/show', $parameters);

    $this->links['search'][] = '$$$_webversion_track_$$$';
    $this->links['replace'][] = $url;
  }

  public function getSocialMediaShare() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idAutomaticCampaignStep, $this->idContact);
    $url = $linkdecoder->encodeLink('socialmedia/share', $parameters);

    $this->links['search'][] = '$$$_social_media_share_$$$';
    $this->links['replace'][] = $url . '-';
  }

  public function getUnsubscribeTracking() {
    $linkdecoder = $this->encoder;

    $parameters = array(1, $this->idAutomaticCampaignStep, $this->idContact);
    $url = $linkdecoder->encodeLink('unsubscribe/contact', $parameters);

    $this->links['search'][] = '$$$_unsubscribe_track_$$$';
    $this->links['replace'][] = $url;

    $this->unsubscribe_link = $url;
  }

}
