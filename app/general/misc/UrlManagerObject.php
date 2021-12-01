<?php

namespace Sigmamovil\General\Misc;

class UrlManagerObject {

  protected $url_manager;
  protected $protocol;
  protected $host;
  protected $port;
  protected $app_base;
  protected $api_v1;
  protected $api_v1_statistics;
  protected $api_vi_dbase;
  protected $assets;
  protected $assets_allied;
  protected $templates;
  protected $footers;
  protected $protocol_mail;
  protected $host_mail;
  protected $host_assets;
  protected $app_base_trailing;
  protected $assets_root;

  /**
   * Cargamos los datos del archivo de configuración si existe
   * @param type $config
   */
  public function __construct($config) {

    if (isset($config->urlmanager)) {
      $this->protocol = $config->urlmanager->protocol;
      $this->host = $config->urlmanager->host;
      $this->port = $config->urlmanager->port;
      if ($config->urlmanager->appbase != '') {
        $this->app_base = $config->urlmanager->appbase . '/';
      } else {
        $this->appbase = $config->urlmanager->appbase;
      }
      $this->api_v1 = $config->urlmanager->api_v1;
      $this->api_v1_statistics = $config->urlmanager->api_v1_statistics;
      $this->api_vi_dbase = $config->urlmanager->api_vi_dbase;
      $this->assets = $config->urlmanager->assets;
      $this->assets_allied = $config->urlmanager->assets_allied;
      $this->assets_root = "root-assets";
      $this->templates = $config->urlmanager->templates;
      $this->footers = $config->urlmanager->footers;
      $this->protocol_mail = $config->urlmanager->protocol_mail;
      $this->host_mail = $config->urlmanager->host_mail;
      $this->host_assets = $config->urlmanager->host_assets;
    } else {
      $this->protocol = "https";
      $this->host = "https://aio.sigmamovil.com";
      $this->port = 80;
      $this->appbase = "aio";
      $this->api_v1 = "api";
      $this->api_v1_statistics = "apistatistics";
      $this->api_vi_dbase = "dbaseapi";
      $this->assets = "asset";
      $this->assets_allied = "allied-assets";
      $this->assets_root = "root-assets";
      $this->templates = "template";
      $this->footers = "footer";
      $this->protocol_mail = "https";
      $this->host_mail = "nmailer.sigmamovil.com";
      $this->host_assets = "files.sigmamovil.com";
    }

    $this->app_base_trailing = $this->app_base . ($this->app_base == '' ? '/' : '');
  }

  /**
   * Crea el prefijo de la URL, si se le pasa true, retorna el prefijo con protocolo ejemplo: http://localhost/ ,
   * si se le pasa false retorna vacio
   * @param boolean $full
   * @return string
   */
  protected function get_prefix($full = false) {
    if ($full) {
      $prefix = $this->protocol . '://' . $this->host . '/';
    } else {
      $prefix = '';
    }

    return $prefix;
  }

  /**
   * Returns the url base ex: "emarketing" and url full ex: "http://localhost/aio"
   * @return type
   */
  public function get_base_uri($full = false) {
    return $this->get_prefix($full) . $this->app_base;
  }

  /**
   * Return full or relative assets url ex: "http://localhost/aio/assets", "aio/assets"
   * @param boolean $full
   * @return URL string
   */
  public function get_prefix_url_asset($full = false) {
    return $this->get_prefix($full) . $this->app_base_trailing . $this->assets;
  }

  /**
   * Return assets url ex: "assets"
   * @return URL string
   */
  public function get_url_asset() {
    return $this->assets;
  }

  /**
   * Return assets-allied url ex: "assets"
   * @return URL string
   */
  public function get_url_asset_allied() {
    return $this->assets_allied;
  }

  /**
   * Return assets-allied url ex: "assets"
   * @return URL string
   */
  public function get_url_asset_root() {
    return $this->assets_root;
  }

  /**
   * Return full or relative templates url ex: "http://localhost/aio/templates", "aio/templates"
   * @param boolean $full
   * @return URL string
   */
  public function get_prefix_url_template($full = false) {
    return $this->get_prefix($full) . $this->app_base_trailing . $this->templates;
  }

  /**
   * Return templates url ex: "templates"
   * @return URL string
   */
  public function get_url_template() {
    return $this->templates;
  }

  /**
   * Return full or relative footers url ex: "http://localhost/aio/footers", "aio/footers"
   * @param boolean $full
   * @return URL string
   */
  public function get_prefix_url_footer($full = false) {
    return $this->get_prefix($full) . $this->app_base_trailing . $this->footers;
  }

  /**
   * Return footers url ex: "footers"
   * @return URL string
   */
  public function get_url_footer() {
    return $this->footers;
  }

  /**
   * Return URL for ember comunication (API_v1) ex: "emarketing/api"
   * @return URL string
   */
  public function get_api_v1_url($full = false) {
    return $this->get_prefix($full) . "/" . $this->host . $this->app_base_trailing . $this->api_v1;
  }

  /**
   * Return URL for ember comunication (API_v1_statistics) ex: "emarketing/apistatistics"
   * @return URL string
   */
  public function get_api_v1_statistics_url($full = false) {
    return $this->get_prefix($full) . $this->app_base_trailing . $this->api_v1_statistics;
  }

  /**
   * Return URL for dbaseapi ex: "emarketing/dbaseapi"
   * @return URL string
   */
  public function get_api_v1_dbaseapi_url($full = false) {
    return $this->get_prefix($full) . $this->app_base_trailing . $this->api_vi_dbase;
  }

  /**
   * Return full or relative assets url ex: "http://localhost/aio/assets", "aio/assets"
   * @param boolean $full
   * @return URL string
   */
  public function getAppUrlAsset($full = false) {
    return $this->getPrefix($full) . $this->app_base_trailing . $this->assets;
  }

  protected function getPrefix($full) {
    if ($full) {
      $prefix = $this->protocol . '://' . $this->host . '/';
    } else {
      $prefix = '';
    }
    return $prefix;
  }

}
