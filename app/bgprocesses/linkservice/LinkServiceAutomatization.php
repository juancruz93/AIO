<?php

/**
 * Description of LinkServiceAutomatization
 *
 * @author desarrollo3
 */
class LinkServiceAutomatization {

  protected $account;
  protected $automaticCampaignStep;
  protected $mappings;

  public function __construct(Account $account, $automaticStep) {

    $this->account = $account;
    $this->automaticCampaignStep = $automaticStep;

    $this->mappings = array();
  }

  /**
   * 
   * @return array
   */
  public function getUrlMappings() {
    /*
     * $this->mappings =
     * 		[
     * 			'URL'  =>  [ '$$$x_sigma$$$', 'http://URLBASE/track/click/etc...' ]
     * 		]
     */
    $map = array();
    foreach ($this->mappings as $maps) {
      // $maps[0] = '$$$1_sigma$$$'
      // $maps[1] = 'idMailLink'
      $map[$maps[0]] = $maps[1];
    }
    /*
     * El resultado debe ser:
     * [   '$$$1_sigma$$$' => 'idLink',
     * 		...
     * ]
     */
    return $map;
  }

  /**
   * Retorna la marca
   * @param string $url
   * return string
   */
  public function getPlatformUrl($url) {
//		echo 'Me llegó esto: ' . $url . PHP_EOL;
    /*
     * $this->mappings =
     * 		[
     * 			'URL'  =>  [ '$$$x_sigma$$$', 'http://URLBASE/track/click/etc...' ]
     * 		]
     */
    /* 1) Validar LINK (ej: no nulo, no en lista de hosts a no hacer seguimiento .. etc */
    $valid = $this->validateHost($url);

      if (!$valid) {
      return false;
    }
    /* 2) Verificar si esta en la lista local (mappings) si es asi, retornar el key */
    if (isset($this->mappings[$url])) {
      $mark = $this->mappings[$url][0];
    } else {
      /* 3) Buscar en BD, si esta crear en lista local (mappings) y retornar el key */
      /* 4) No esta en BD, crear en BD y etc */
      $idMailLink = $this->saveLink($url);
      $mark = $this->createMark($idMailLink);
      $this->mappings[$url] = array($mark, $idMailLink);
    }
    return $mark;
  }

  private function saveLink($url) {

    $maillink = Maillink::findFirst(array(
                'conditions' => 'idAccount = ?1 AND link = ?2',
                'bind' => array(1 => $this->account->idAccount,
                    2 => $url)
    ));

    if (!$maillink) {
      unset($maillink);
      $maillink = new Maillink();
      $maillink->idAccount = $this->account->idAccount;
      $maillink->link = $url;
      $maillink->createdon = time();

      if (!$maillink->save()) {
        foreach ($maillink->getMessages() as $msg) {
          Phalcon\DI::getDefault()->get('logger')->log('Error saving link: ' . $msg);
        }
        throw new InvalidArgumentException('Error while saving Maillink');
      }
    }

    $acxl = Acxl::findFirst([
                "conditions" => "idMail_link = ?0 AND idAutomaticCampaignStep = ?1",
                "bind" => [
                    0 => $maillink->idMail_link,
                    1 => $this->automaticCampaignStep->idAutomaticCampaignStep
                ]
    ]);

    if (!$acxl) {
      $acxl = new Acxl();
      $acxl->idAutomaticCampaignStep = $this->automaticCampaignStep->idAutomaticCampaignStep;
      $acxl->idMail_link = $maillink->idMail_link;
      $acxl->createdBy = $this->automaticCampaignStep->createdBy;

      if (!$acxl->save()) {
        foreach ($acxl->getMessages() as $msg) {
          var_dump($msg);
          exit();
          Phalcon\DI::getDefault()->get('logger')->log('Error saving acxl: ' . $msg);
        }
        throw new InvalidArgumentException('Error while saving acxl');
      }
    }
    return $acxl->idMail_link;
  }

  private function createMark($id) {
    return $newUrl = '$$$' . $id . '_sigma_url_$$$';
  }

//	private function createUrlBase($id)
//	{
//		return $this->urlManager->getBaseUri(true) . 'track/click/1-' . $id;
//	}

  private function validateHost($url) {
    $invalidDomains = array(
        'facebook' => '/[^\/]*\.*facebook.com.*$/',
        'twiter' => '/[^\/]*\.*twitter.com.*$/',
        'linkedin' => '/[^\/]*\.*linkedin.com.*$/',
        'google-plus' => '/[^\/]*\.*plus.google.com.*$/'
    );

    $parts = parse_url($url);
    foreach ($invalidDomains as $domain) {
      if (!isset($parts['host']) || empty($parts['host']) || preg_match($domain, $url)) {
        return false;
      }
    }
    return true;
  }

}
