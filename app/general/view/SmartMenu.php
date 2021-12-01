<?php

namespace Sigmamovil\General\View;

class SmartMenu extends \Phalcon\Mvc\User\Component implements \Iterator {

  protected $controller;
  private $_menu = array(
      "Dashboard" => array(
          "controller" => array("index"),
          "class" => "",
          "url" => "",
          "title" => "Página de inicio",
          "icon" => "hi-icon-home",
          "target" => ""
      ),
      "Contactos" => array(
          "controller" => array("contactlist", "dbase", "contact", "segment","blockade"),
          "class" => "",
          "url" => "contactlist/show#/",
          "title" => "Lista de contactos",
          "icon" => "hi-icon-contacts",
          "target" => ""
      ),
      "Marketing" => array(
          "controller" => array("marketing", "sms", "smscategory", "smstemplate", "mail", "mailcategory", "mailtemplate",
              "automaticcampaign", "statistic","automaticcampaigncategory", "autoresponder", "forms", "survey", "landingpage"),
          "class" => "",
          "url" => "marketing",
          "title" => "Marketing digital",
          "icon" => "hi-icon-marketing",
          "target" => ""
      ),
      "Reportes" => array(
          "controller" => array("report", "reports","accounting"),
          "class" => "",
          "url" => "reports/index",
          "title" => "Reportes y estadísticas",
          "icon" => "hi-icon-statistics",
          "target" => ""
      ),
      "Herramientas" => array(
          "controller" => array("mailtemplate",'tools', "flashmessage", "socialmedia", 'footer', 'apikey', 'systemmail', 'gallery', 'permissionsystem', "mail_structure", "allied", "currency", "pricelist","tax", "paymentplan"),
          "class" => "",
          "url" => "tools",
          "title" => "Herramientas",
          "icon" => "hi-icon-tools",
          "target" => ""
      ),
      "Cuentas" => array(
          "controller" => array("accounts", "account", "subaccount", "masteraccount", "user"),
          "class" => "",
          "url" => "accounts",
          "title" => "Cuentas",
          "icon" => "hi-icon-accounts",
          "target" => ""
      ),
      "Sistema" => array(
          "controller" => array('system', 'mta', "adapter", 'urldomain', "services", 'mailclass', 'process', 'activitylog', 'smssendingrule', "language"),
          "class" => "",
          "url" => "system",
          "title" => "Sistema",
          "icon" => "hi-icon-system",
          "target" => ""
      ),
      "Ayuda" => array(
          "controller" => array(''),
          "class" => "",
          "url" => "https://wksigmamovil.atlassian.net/wiki/pages/viewpage.action?pageId=39190538",
          "title" => "Ayuda",
          "icon" => "hi-icon-help",
          "target" => "_blank"
      ),
//      "Chat" => array(
//          "controller" => array(),
//          "class" => "",
//          "url" => "",
//          "title" => "Chat",
//          "icon" => "hi-icon-chat",
//          "target" => ""
//      ),
  );

  public function __construct() {
    $this->controller = $this->view->getControllerName();
  }

  public function get() {
    if ($this->user->UserType->idSubaccount == null) {
      unset($this->_menu["Contactos"]);
      unset($this->_menu["Marketing"]);
    }else{
      unset($this->_menu["Sistema"]);
      unset($this->_menu["Herramientas"]["controller"][0]);
    }
    
    if ($this->user->UserType->idAllied != null || $this->user->Usertype->idAccount || $this->user->Usertype->idSubaccount) {
//      unset($this->_menu["Sistema"]);
    }
    
    if ($this->user->UserType->idMasteraccount == null 
            && $this->user->UserType->idAllied == null 
            && $this->user->UserType->idAccount == null
            && $this->user->UserType->idSubaccount == null) {
      unset($this->_menu["Contactos"]);
      unset($this->_menu["Marketing"]);
      unset($this->_menu["Reportes"]);
      unset($this->_menu["Chat"]);
    }
    
    if ($this->user->UserType->idMasteraccount != null || $this->user->Usertype->idMasteraccount) {
      unset($this->_menu["Contactos"]);
      unset($this->_menu["Marketing"]);
      unset($this->_menu["Reportes"]);
    }
    
    return $this;
  }

  public function rewind() {
    \reset($this->_menu);
  }

  public function current() {
    $obj = new \stdClass();

    $curr = \current($this->_menu);

    $obj->title = $curr['title'];
    $obj->icon = $curr['icon'];
    $obj->url = $curr['url'];
    $obj->class = '';
    $obj->target = $curr['target'];

    if (\in_array($this->controller, $curr['controller'])) {
      $obj->class = 'active';
    }

    return $obj;
  }

  public function key() {
    return \key($this->_menu);
  }

  public function next() {
    $var = \next($this->_menu);
  }

  public function valid() {
    $key = \key($this->_menu);
    $var = ($key !== NULL && $key !== FALSE);
    return $var;
  }

}
