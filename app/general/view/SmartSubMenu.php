<?php

namespace Sigmamovil\General\View;

class SmartSubMenu extends \Phalcon\Mvc\User\Component implements \Iterator {

  private $controller;
  private $submenu;
  private $idMasteraccount;

  public function __construct() {
    $this->controller = $this->view->getControllerName();
    $this->logger->log($this->user->Usertype->idMasteraccount);
    $this->idMasteraccount = ((isset($this->user->Usertype->idMasteraccount)) ? $this->user->Usertype->idMasteraccount : ((isset($this->user->Usertype->Allied->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Allied->Masteraccount->idMasteraccount : ((isset($this->user->Usertype->Account->Allied->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Account->Allied->Masteraccount->idMasteraccount : ((isset($this->user->Usertype->Subaccount->Account->Allied->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Subaccount->Account->Allied->Masteraccount->idMasteraccount : ""))));
    $this->submenu = array(
        "marketing" => array(
            "sendSms" => array(
                "controller" => "sms",
                "class" => "sms-marck ",
                "url" => "sms",
                "title" => "Envío de SMS",
                "roles" => array("subaccount")
            ),
            "SmsTwoWay" => array(
                "controller" => "smstwoway",
                "class" => "smstwoway",
                "url" => "smstwoway",
                "title" => "Envío de Sms Doble Via",
                "roles" => array("subaccount")
            ),
            "SendMail" => array(
                "controller" => "mail",
                "class" => "mail-marck",
                "url" => "mail",
                "title" => "Envíos de correo",
                "roles" => array("subaccount")
            ),
            "AutomaticCampaign" => array(
                "controller" => "automaticcampaign",
                "class" => "campaign-marck",
                "url" => "automaticcampaign",
                "title" => "Campañas automáticas",
                "roles" => array("subaccount")
            ),
            "AutoResponder" => array(
                "controller" => "autoresponder",
                "class" => "autoresponder",
                "url" => "autoresponder",
                "title" => "Auto respuestas",
                "roles" => array("subaccount")
            ),
            "Forms" => array(
                "controller" => "forms",
                "class" => "forms",
                "url" => "forms#/",
                "title" => "Formularios",
                "roles" => array("subaccount")
            ),
            "Survey" => array(
                "controller" => "survey",
                "class" => "survey",
                "url" => "survey#/",
                "title" => "Encuestas",
                "roles" => array("subaccount")
            ),
            "Landing" => array(
                "controller" => "landingpage",
                "class" => "landingpage",
                "url" => "landingpage",
                "title" => "Landing",
                "roles" => array("subaccount")
            ),
        ),
        "reports" => array(
            "ReportMail" => array(
                "controller" => "report",
                "class" => "report-mail",
                "url" => "report/index#/mail",
                "title" => "Reporte de envíos de correos por cuenta",
                "roles" => array("allied")
            ),
            "ReportSms" => array(
                "controller" => "report",
                "class" => "report-sms",
                "url" => "report/index#/sms",
                "title" => "Reporte de envíos de sms por cuenta",
                "roles" => array("allied")
            ),
            "Recharge" => array(
                "controller" => "report",
                "class" => "report-recharge",
                "url" => "report/index#/recharge",
                "title" => "Reporte Historial de recargas por Cuenta",
                "roles" => array("allied")
            ),
            "Changeplan" => array(
                "controller" => "report",
                "class" => "report-change",
                "url" => "report/index#/changeplan",
                "title" => "Reporte de Cambios de planes",
                "roles" => array("allied")
            ),
            /*Se comenta debido a que es una opción habilitada para el aliado ya que con cuenta se revienta*/
//            "Excelsms" => array(
//                "controller" => "report",
//                "class" => "graph",
//                "url" => "report/index#/changeplan",
//                "title" => "Tendencia de envios",
//                "roles" => array("account")
//            ),
            "Excelsmsday" => array(
                "controller" => "report",
                "class" => "sms-info-day",
                "url" => "report/index#/excelsmsday",
                "title" => "Reporte de sms por dia",
                "roles" => array("account")
            ),
            "Infosms" => array(
                "controller" => "report",
                "class" => "sms-info-detail-day",
                "url" => "report/index#/infosms",
                "title" => "Detalle general de SMS",
                "roles" => array("account", "subaccount")
            ),
            "infosmsbydestinataries" => array(
                "controller" => "report",
                "class" => "sms-info-detail-by-destinataries",
                "url" => "report/index#/infosmsbydestinataries",
                "title" => "Detalle de SMS por Celular",
                "roles" => array("subaccount")
            ),
            "Infomail" => array(
                "controller" => "report",
                "class" => "mail-info-detail-day",
                "url" => "report/index#/infomail",
                "title" => "Detalle de envíos de Email",
                "roles" => array("account", "subaccount")
            ),
            "Accounting" => array(
                "controller" => "accounting",
                "class" => "i-accounting",
                "url" => "accounting#/",
                "title" => "Contabilidad de cuentas por mes",
                "roles" => array("allied")
            ),
//            "GeneralReports" => array(
//                "controller" => "report",
//                "class" => "report-general-chart",
//                "url" => "report/index#/stadisticsgeneral",
//                "title" => "Reportes generales",
//                "roles" => array("subaccount")
//            )
            "ReportValidation" => array(
                "controller" => "report",
                "class" => "mail_validation",
                "url" => "report/index#/reportvalidation",
                "title" => "Detalle de correos validados por aliado",
                "roles" => array("allied")
            ),
            "ListSmsChannel" => array(
                "controller" => "listsmschannel",
                "class" => "send-message",
                "url" => "report/index#/listsmschannel",
                "title" => "Informe de envios sms por canal",
                "roles" => array("allied")
            ),
            "reportsmsxemail" => array(
                "controller" => "reportsmsxemail",
                "class" => "reportsmsxemail",
                "url" => "report/index#/smsxemail",
                "title" => "Detalle de SMS por Email",
                "roles" => array("subaccount")
            ),
        ),
        "tools" => array(
            "Customizing" => array(
                "controller" => "customizing",
                "class" => "customizing",
                "url" => "customizing",
                "title" => "Personalización",
                "roles" => array("allied")
            ),
            "flashMessage" => array(
                "controller" => "flashmessage",
                "class" => "flash-message",
                "url" => "flashmessage",
                "title" => "Mensajes flash",
                "roles" => array("-1", "master", "allied")
            ),            
            "footers" => array(
                "controller" => "footer",
                "class" => "footers",
                "url" => "footer",
                "title" => "Footers",
                "roles" => array("allied")
            ),
            /* "intelligentManagement" => array(
              "controller" => "process",
              "class" => "smart",
              "url" => "process",
              "title" => "Gestión inteligente",
              "roles" => array("allied")
              ), */
            "systemMail" => array(
                "controller" => "systemmail",
                "class" => "system-mail",
                "url" => "systemmail",
                "title" => "Correos del sistema",
                "roles" => array("allied")
            ),
            "mailStructure" => array(
                "controller" => "mailstructure",
                "class" => "mail-structure",
                "url" => "mail_structure/index",
                "title" => "Estructuras predefinidas",
                "roles" => array("allied")
            ),
            "mailTemplate" => array(
                "controller" => "mailtemplate",
                "class" => "mail-template",
                "url" => "mailtemplate#/",
                "title" => "Plantillas de correo",
                "roles" => array("allied", "account")
            ),
            "permissions" => array(
                "controller" => "permissionsystem",
                "class" => "security",
                "url" => "permissionsystem#/roles",
                "title" => "Permisos de usuario",
                "roles" => array("-1")
            ),
            "currrency" => array(
                "controller" => "currency",
                "class" => "i-currency",
                "url" => "currency#/",
                "title" => "Divisas",
                "roles" => array("-1")
            ),
            "globalProgramming" => array(
                "controller" => "process",
                "class" => "scheduled",
                "url" => "scheduled",
                "title" => "Programación global",
                "roles" => array("subaccount")
            ),
            "socialNetwork" => array(
                "controller" => "process",
                "class" => "social-networks",
                "url" => "",
                "title" => "Redes sociales",
                "roles" => array("account")
            ),
            "apiKeys" => array(
                "controller" => "process",
                "class" => "api-keys",
                "url" => "apikey",
                "title" => "API Keys",
                "roles" => array("account")
            ),
            "gallery" => array(
                "controller" => "gallery",
                "class" => "gallery",
                "url" => "gallery",
                "title" => "Galería de archivos",
                "roles" => array("account", "subaccount"),
            ),
            "habeasdata" => array(
                "controller" => "habeasdata",
                "class" => "habeasdata",
                "url" => "habeasdata",
                "title" => "Habeas Data",
                "roles" => array("account"),
            ),
            "namesender" => array(
                "controller" => "namesender",
                "class" => "namesender",
                "url" => "namesender#/",
                "title" => "Nombre de remitentes",
                "roles" => array("account"),
            ),
            "replyto" => array(
                "controller" => "replyto",
                "class" => "replyto",
                "url" => "replyto#/",
                "title" => "Correos de respuesta",
                "roles" => array("account"),
            ),
            "emailsender" => array(
                "controller" => "emailsender",
                "class" => "emailsender",
                "url" => "emailsender#/",
                "title" => "Correos de remitente",
                "roles" => array("account"),
            ),
            "pricelist" => array(
                "controller" => "pricelist",
                "class" => "i-pricelist",
                "url" => "pricelist#/",
                "title" => "Lista de precios",
                "roles" => array("-1", "master", "allied")
            ),
            "tax" => array(
                "controller" => "tax",
                "class" => "i-tax",
                "url" => "tax#/",
                "title" => "Impuestos",
                "roles" => array("-1", "master", "allied")
            ),
            "paymentPlan" => array(
                "controller" => "paymentplan",
                "class" => "i-paymentplan",
                "url" => "paymentplan#/",
                "title" => "Planes de pago",
                "roles" => array("-1", "master", "allied")
            ),
            "accountcategory" => array(
                "controller" => "accountcategory",
                "class" => "i-accountcategory",
                "url" => "accountcategory#/",
                "title" => "Categoria de cuentas",
                "roles" => array("-1", "master", "allied")
            ),
            "knowledgebase" => array(
                "controller" => "knowledgebase",
                "class" => "i-knowledgebase",
                "url" => "knowledgebase#/",
                "title" => "Base del conocimiento",
                "roles" => array("allied")
            ),
            "ip" => array(
                "controller" => "ip",
                "class" => "flash-message",
                "url" => "ip#/",
                "title" => "Lista de IP",
                "roles" => array("master")
            ),
            "mtaxip" => array(
                "controller" => "mtaxip",
                "class" => "flash-message",
                "url" => "mtaxip#/",
                "title" => "Lista de MTA",
                "roles" => array("master")
            ),
            "smsxemail" => array(
                "controller" => "smsxemail",
                "class" => "smsxemail",
                "url" => "smsxemail#/",
                "title" => "SMS por Email",
                "roles" => array("subaccount")
            ),
            "smstwowaypostnotify" => array(
                "controller" => "smstwowaypostnotify",
                "class" => "sms-post-notify",
                "url" => "smstwowaypostnotify#/",
                "title" => "Notificaciones POST SMS",
                "roles" => array("subaccount")
            ),            
            "unsubavance" => array(
                "controller" => "unsubscribe",
                "class" => "unsubavance",
                "url" => "unsubscribe/list",
                "title" => "Desuscripción avanzada",
                "roles" => array("subaccount"),
            )
     
        ),
        "accounts" => array(
            "masteraccount" => array(
                "controller" => "masteraccount",
                "class" => "account",
                "url" => "masteraccount",
                "title" => "Cuentas maestras",
                "roles" => array("-1")
            ),
            "allieds" => array(
                "controller" => "masteraccount",
                "class" => "allied",
                "url" => "masteraccount/aliaslist/{$this->user->Usertype->idMasteraccount}",
                "title" => "Aliados",
                "roles" => array("master")
            ),
            "accounts" => array(
                "controller" => "account",
                "class" => "account",
                "url" => "account",
                "title" => "Cuentas de usuario",
                "roles" => array("allied")
            ),
            "subaccounts" => array(
                "controller" => "subaccount",
                "class" => "allied",
                "url" => "subaccount/index/{$this->user->Usertype->idAccount}",
                "title" => "Subcuentas",
                "roles" => array("account")
            ),
            "users" => array(
                "controller" => "user",
                "class" => "user",
                "url" => "user",
                "title" => "Usuarios",
                "roles" => array("master", "allied", "account")
            ),
            "masterConfig" => array(
                "controller" => "masteraccount",
                "class" => "config",
                "url" => "masteraccount/show/{$this->idMasteraccount}",
                "title" => "Mi configuración",
                "roles" => array("master")
            ),
            "alliedConfig" => array(
                "controller" => "allied",
                "class" => "config",
                "url" => "allied/show/{$this->user->Usertype->idAllied}",
                "title" => "Mi configuración",
                "roles" => array("allied")
            ),
            "accountConfig" => array(
                "controller" => "account",
                "class" => "config",
                "url" => "account/show/{$this->user->Usertype->idAccount}",
                "title" => "Mi configuración",
                "roles" => array("account")
            ),
            "subaccountConfig" => array(
                "controller" => "subaccount",
                "class" => "config",
                "url" => "subaccount/showconfig/{$this->user->Usertype->idSubaccount}",
                "title" => "Mi configuración",
                "roles" => array("subaccount")
            )
        ),
        "system" => array(
            "Languages" => array(
                "controller" => "language",
                "class" => "language",
                "url" => "language",
                "title" => "Idiomas",
                "roles" => array("-1")
            ),
            "mtas" => array(
                "controller" => "mta",
                "class" => "mta",
                "url" => "mta",
                "title" => "MTA'S",
                "roles" => array("-1")
            ),
            "adapters" => array(
                "controller" => "adapter",
                "class" => "adapter",
                "url" => "adapter",
                "title" => "Adaptadores",
                "roles" => array("-1")
            ),
            "urls" => array(
                "controller" => "urldomain",
                "class" => "url-domain",
                "url" => "urldomain",
                "title" => "URL'S",
                "roles" => array("-1")
            ),
            "services" => array(
                "controller" => "services",
                "class" => "platform",
                "url" => "services",
                "title" => "Servicios",
                "roles" => array("-1")
            ),
            "mailClases" => array(
                "controller" => "mailclass",
                "class" => "mail-class",
                "url" => "mailclass",
                "title" => "Mail Clases",
                "roles" => array("-1")
            ),
            "process" => array(
                "controller" => "process",
                "class" => "process",
                "url" => "process",
                "title" => "Procesos en background",
                "roles" => array("-1")
            ),
            "activitylog" => array(
                "controller" => "activitylog",
                "class" => "i-activitylog",
                "url" => "activitylog#/",
                "title" => "Log de actividad",
                "roles" => array("-1", "master", "aliado", "account")
            ),
            "smssendingrule" => array(
                "controller" => "smssendingrule",
                "class" => "i-rules",
                "url" => "smssendingrule#/",
                "title" => "Reglas de envío de SMS",
                "roles" => array("-1")
            ),
            "history" => array(
                "controller" => "history",
                "class" => "i-history",
                "url" => "history#/",
                "title" => "Historial de actividades",
                "roles" => array("-1", "master", "allied")
            ),
            "country" => array(
                "controller" => "country",
                "class" => "i-country",
                "url" => "country#/",
                "title" => "Paises",
                "roles" => array("-1")
            )
        )
    );
  }

  public function get() {


    return $this;
  }

  public function rewind() {
    \reset($this->submenu);
  }

  public function current() {
    //$obj = new \stdClass();

    $curri = \current($this->submenu);

    $curr = array();
    $jeje = new \stdClass();
    foreach ($curri as $key => $value) {
      $curr[$key] = array(
          "controller" => $value["controller"],
          "class" => $value["class"],
          "url" => $value["url"],
          "title" => $value["title"],
          "roles" => $value["roles"],
          "controllerCurrent" => $this->controller
      );
    }
    //$curr2 = (object) $curr;
    //$curr["controllerCurrent"] = array("controllerCur" => $this->controller);
//    var_dump($curr);
//    exit;
//    $obj->controller = $curr['controller'];
//    $obj->class = $curr['class'];
//    $obj->url = $curr["url"];
//    $obj->title = $curr["title"];
//    $obj->roles = $curr["roles"];
//    $obj->controllerCurrent = $this->controller;

    return $curr;
  }

  public function key() {
    return \key($this->submenu);
  }

  public function next() {
    $var = \next($this->submenu);
  }

  public function valid() {
    $key = \key($this->submenu);
    $var = ($key !== NULL && $key !== FALSE);
    return $var;
  }

}
