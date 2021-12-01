<?php

namespace Sigmamovil\General;

class AppObject {

    protected $config;

    /**
     *
     * @var \Phalcon\DI
     */
    protected $di;
    protected $status;
    protected $urlManager;
    protected $allowedIps;
    protected $ip;

    /**
     * Archivo de configuración del sistema necesario para iniciar la plataforma 
     * @param String ruta del archivo de configuración
     */
    public function set_config_file($config_path) {
        $this->config = new \Phalcon\Config\Adapter\Ini($config_path);
        $this->status = $this->config->system->status;
        $this->allowedIps = array();
        foreach ($this->config->system->override_ip as $ip) {
            $this->allowedIps[] = $ip;
        }

        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function configure() {
        $this->create_di();
        $this->set_chat_config();
        $this->set_theme_data();
        if (!$this->config->system->status && !\in_array($this->ip, $this->allowedIps)) {
            $this->setUrlManagerObject();
            $this->set_app_path();
            $this->set_full_path();
            $this->set_url_manager_object();
            $this->set_uri();
            $this->set_dispatcher();

            $this->set_logger();

            $this->set_session_manager();

            $this->set_flash_session_messages();
            $this->set_nofication_manager();

            $this->set_db();
            $this->set_models_manager();

            $this->set_smart_menu();
            $this->set_smart_submenu();
            $this->set_view();
            $this->set_volt_compiler();
            $this->set_roles();
            $this->set_name_roles();
            $this->file_exists();
            $this->set_services();
            $this->set_string_status_sms();
            //$this->set_key_jwt();
            $this->hour_sms();
            $this->setinstanceIDprefix();
            $this->set_suggestions_datas();
            $this->setIdAlliedSigma();
            $this->setConfigFb();
            $this->set_mail_tester();
            $this->setUrlDefaultSurvey();
            $this->setDefaultgoogleAnalyticsEmail();
            $this->setDataValidation();
            $this->setApiSmsTwoWay();
            $this->setAdapters();
            $this->setCookie();
            $this->setHabeasData();
            $this->setIpServer();
            $this->setGeneral();
            $this->setMtaDefault();
            $this->setInfobitAnswersCharged();
           // $this->setApiWhatsapp();
        } else {
            $this->hour_sms();
            $this->set_key_jwt();
            $this->setUrlManagerObject();
            $this->setPrivateAssetsFolder();
            $this->set_mongo_manager_db();
            $this->set_mongo_db();
            $this->set_collection_manager();
            $this->set_string_status_sms();
            $this->set_status_sms();
            $this->set_config_public_domain();
            $this->set_type_sms();
            $this->set_type_survey();
            $this->set_component_question();
            $this->set_app_path();
            $this->set_full_path();
            $this->set_url_manager_object();
            $this->set_uri();
            $this->set_router();

            $this->set_acl();
            $this->set_memcache();
            $this->set_dispatcher();
            $this->set_hash_validator();
            $this->set_session_manager();

            $this->set_flash_session_messages();
            $this->set_nofication_manager();
            $this->set_administrative_messages();

//                $this->set_models_metadata();
            $this->set_mta_config();
            $this->set_assets_config();
            $this->setUploadConfig();

            $this->set_db();
            $this->set_models_manager();

//                $this->set_pdf_templates_folder();
            $this->set_private_assets_folder();
            $this->set_public_assets_folder();
            $this->set_tmp_folder();
//                $this->set_public_footers_folder();
//                $this->set_facebook_app_config();
//                $this->set_twitter_app_config();
//                $this->set_sockets_config();
//                $this->set_google_analitycs_config();
//                $this->set_mail_reports_folder();

            $this->set_logger();
//                $this->set_delete_logger();
//                $this->set_profiler();

            $this->set_smart_menu();
            $this->set_smart_submenu();
            $this->set_view();
            $this->set_volt_compiler();
            $this->set_roles();
            $this->set_name_roles();
            $this->file_exists();
            $this->set_services();
            $this->set_filters_segment();
            $this->setinstanceIDprefix();
            $this->set_global_counters_manager();
            $this->set_personalized_css();
            $this->set_suggestions_datas();
            $this->setIdAlliedSigma();
            $this->setConfigFb();
            $this->set_mail_tester();
            $this->setUrlDefaultSurvey(); 
            $this->setDefaultgoogleAnalyticsEmail();
            $this->setDataValidation();
            $this->setApiSmsTwoWay();
            $this->setAdapters();
            $this->setCookie();
            $this->setHabeasData();
            $this->setIpServer();
            $this->setGeneral();
            $this->setMtaDefault();
            $this->setInfobitAnswersCharged();
            //$this->setApiWhatsapp();
        }
    }

    /**
     * Creación del inyector de dependencias
     */
    private function create_di() {
        $this->di = new \Phalcon\DI\FactoryDefault();
    }

    /**
     * El objeto encargado de armar las url puntuales basandose en el archivo de configuración
     */
    private function setUrlManagerObject() {
        $this->urlManager = new Misc\UrlManagerObject($this->config);
        $this->di->set('urlManager', $this->urlManager);
    }

    /**
     * Inicializar el chat de Olark si está disponible
     */
    protected function set_chat_config() {
        $chat = new \stdClass();
        if (isset($this->config->olark) && isset($this->config->olark->enabled)) {
            $chat->enabled = $this->config->olark->enabled;
        } else {
            $chat->enabled = false;
        }

        $this->di->set('chat', $chat);
    }

    /**
     * iniciarlizar el tema, si no está definido se deja por defecto el 
     */
    public function set_theme_data() {
        $config = $this->config;

        $this->di->set('theme', function() use ($config) {
            $theme = new \stdClass;

            if (isset($config->theme->name)) {
                $theme->name = $config->theme->name;
                $theme->logo = $config->theme->logo;
                $theme->title = $config->theme->title;
                $theme->subtitle = $config->theme->subtitle;
                $theme->footer = 'Sigma Engine - (c) ' . date("Y") . ' Sigma Movil S.A.S';
            } else {
                $theme->name = 'default';
                $theme->logo = '';
                $theme->title = 'Sigma Engine';
                $theme->subtitle = 'Comunicación Digital';
                $theme->footer = 'Sigma Engine - (c) ' . date("Y") . ' Sigma Movil S.A.S';
            }
            return $theme;
        });
    }

    /**
     * Ruta principal de la aplicacion
     * @return DI object
     */
    private function set_app_path() {
        // Ruta de APP
        $apppath = realpath('../');
        $this->di->set('apppath', function () use ($apppath) {
            $obj = new \stdClass;
            $obj->path = $apppath;

            return $obj;
        });
    }

    private function set_full_path() {
        $path = new \stdClass();
        $path->path = $this->config->path->path;
        $this->di->set('path', $path);
    }

    /**
     * El objeto encargado de armar las url puntuales basandose en el archivo de configuración
     */
    private function set_url_manager_object() {
        $this->urlManager = new \Sigmamovil\General\Misc\UrlManagerObject($this->config);
        $this->di->set('urlManager', $this->urlManager);
    }

    /**
     * Configuración de la base URI, para generar automaticacmente todas las direcciones posibles dentro de la carpeta 
     * principal de la aplicación
     * @return DI object
     */
    private function set_uri() {
        $urlManagerObj = $this->urlManager;
        $this->di->set('url', function() use ($urlManagerObj) {
            $url = new \Phalcon\Mvc\Url();
            $uri = $urlManagerObj->get_base_uri();

            // Adicionar / al inicio y al final
            if (substr($uri, 0, 1) != '/') {
                $uri = '/' . $uri;
            }
            if (substr($uri, -1) != '/') {
                $uri .= '/';
            }

            $url->setBaseUri($uri);
            return $url;
        });
    }

    /**
     * Encargado de escuchar cada peticion(controlador/acción) que hace el usuario a la plataforma
     * @return DI object
     */
    private function set_dispatcher() {
        $di = $this->di;
        $status = $this->status;
        $allowedIps = $this->allowedIps;
        $ip = $this->ip;

        $di->set('dispatcher', function() use ($di, $status, $allowedIps, $ip) {
            $eventsManager = $di->getShared('eventsManager');

            $security = new \Security($di, $status, $allowedIps, $ip);
//                /**
//                 * We listen for events in the dispatcher using the Security plugin
//                 */
            $eventsManager->attach('dispatch', $security);

            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });
    }

    /**
     * Log Object, utilizado para logging en general a archivo
     * @return DI object
     */
    private function set_logger() {
        $this->di->set('logger', function () {
            // Archivo de log
            return new \Phalcon\Logger\Adapter\File("../app/logs/debug.log");
        });
    }

    /**
     * Log Delete Contact Object, utilizado para logging de eliminación de contactos a archivo
     * @return DI object
     */
    private function set_delete_logger() {
        $this->di->set('deletelogger', function () {
            // Archivo de log
            return new \Phalcon\Logger\Adapter\File("../app/logs/deletecontacts.log");
        });
    }

    /**
     * Encargado de configurar volt para la plataforma no esta disponible
     * @return DI object
     */
    private function set_view_system_not_available() {
        $this->di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir('../app/views/');
            $view->registerEngines(array(
                ".volt" => 'Phalcon\Mvc\View\Engine\Volt'
            ));
            return $view;
        });

        $di = $this->di;
        $di->setShared('volt', function($view, $di) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                "compileAlways" => true,
                "compiledPath" => "../app/compiled-templates/",
                'stat' => true
            ));

            $compiler = $volt->getCompiler();
            $Compiler->addFilter('number_format', function() {
                return "hola";
            });
            $compiler->addFilter("split", function ($resolvedArgs) {
                return "explode(',' , " . $resolvedArgs . ")";
            });

            return $volt;
        });
    }

    /**
     * Encargado de enrutar las peticiones de acuerdo a su url
     * @return DI object
     */
    public function set_router() {
        $this->di->set('router', function () {
            $router = new \Phalcon\Mvc\Router\Annotations();
            $router->addResource('Apisecurity', '/api/security');
            $router->addResource('Apicontactlist', '/api/contactlist');
            $router->addResource('Apicontact', '/api/contact');
            $router->addResource('Apisegment', '/api/segment');
            $router->addResource('Apisxc', '/api/sxc');
            $router->addResource('Apiblockade', '/api/blockade');
            $router->addResource('Apisendmail', '/api/sendmail');
            $router->addResource('Apimailcategory', '/api/mailcategory');
            $router->addResource('Apifooter', '/api/footer');
            $router->addResource('Apimailstructure', '/api/mailstructure');
            $router->addResource('Apigallery', '/api/gallery');
            $router->addResource('Apimailtemplatecategory', '/api/mailcategorytemplatecategory');
            $router->addResource('Apimailtemplate', '/api/mailtemplate');
            $router->addResource('Apistatics', '/api/statics');
            $router->addResource('Apismstemplatecategory', '/api/smstemplatecategory');
            $router->addResource('Apismstemplate', '/api/smstemplate');
            $router->addResource('Apiunsubscribe', '/api/unsubscribe');
            $router->addResource('Apiautomaticcampaign', '/api/automacamp');
            $router->addResource('Apiautomaticcampaigncategory', '/api/automacampcateg');
            $router->addResource('Apiapikey', '/api/apikey');
            $router->addResource('Apiversionone', '/api/v1');
            $router->addResource('Apimail', '/api/mail');
            $router->addResource('Apischeduled', '/api/scheduled');
            $router->addResource('Apilanguage', '/api/language');
            $router->addResource('Apicurrency', '/api/currency');
            $router->addResource('Apitax', '/api/tax');
            $router->addResource('Apipricelist', '/api/pricelist');
            $router->addResource('Apipaymentplan', '/api/paymentplan');
            $router->addResource('Apismscategory', '/api/smscategory');
            $router->addResource('Apisaxs', '/api/saxs');
            $router->addResource('Apiactivitylog', '/api/activitylog');
            $router->addResource('Apisupportcontact', '/api/supportcontact');
            $router->addResource('Apireport', '/api/report');
            $router->addResource('Apisession', '/api/session');
            $router->addResource('Apiaccountcategory', '/api/accountcategory');
            $router->addResource('Apisms', '/api/sms');
            $router->addResource('Apismssendingrule', '/api/smssendingrule');
            $router->addResource('Apiautoresponder', '/api/autoresponder');
            $router->addResource('Apicustomizing', '/api/customizing');
            $router->addResource('Apiregister', '/api/register');
            $router->addResource('Apiforms', '/api/forms');
            $router->addResource('Apisurvey', '/api/survey');
            $router->addResource('Apisurveycategory', '/api/surveycategory');
            $router->addResource('Apidashboardconfig', '/api/dashboardconfig');
            $router->addResource('Apipost', '/api/post');
            $router->addResource('Apihistory', '/api/history');
            $router->addResource('Apiknowledgebase', '/api/knowledgebase');
            $router->addResource('Apicountry', '/api/country');
            $router->addResource('Apiaccounting', '/api/accounting');
            $router->addResource('Apistatisallied', '/api/statisallied');
            $router->addResource('Apisubaccount', '/api/subaccount');
            $router->addResource('Apismstwoway', '/api/smstwoway');
            $router->addResource('Apilandingpage', '/api/landingpage');
            $router->addResource('Apilandingpagetemplate', '/api/lptemplate');
            $router->addResource('Apilandingpagetemplatecategory', '/api/lptemplatecategory');
            $router->addResource('Publiclandingpage', '/lp');
            $router->addResource('Apirate', '/api/rate');
            $router->addResource('Apilandingpagecategory', '/api/landingpagecategory');
            $router->addResource('Apinamesender', '/api/namesender');
            $router->addResource('Apireplyto', '/api/replyto');
            $router->addResource('Apiemailsender', '/api/emailsender');
            $router->addResource('Apiip', '/api/ip');
            $router->addResource('Apimtaxip', '/api/mtaxip');
            $router->addResource('Apismsxemail', '/api/smsxemail');
            $router->addResource('Apismstwowaypostnotify', '/api/smstwowaypostnotify');
            $router->addResource('Apivoicemessages', '/api/voicemessages');
            $router->addResource('Apiwhatsapp', '/api/whatsapp');
            $router->addResource('Apiwppcategory', '/api/wppcategory');
            $router->addResource('Apiwpptemplate', '/api/wpptemplate');
            return $router;
        });
    }

    /**
     * Comunicación con Memcache
     * @return DI object
     */
    private function set_memcache() {
        $conf = $this->config;
        $this->di->set('cache', function () use ($conf) {
            $frontCache = new \Phalcon\Cache\Frontend\Data(array(
                "lifetime" => 172800
            ));
            $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
                "cacheDir" => $conf->cache->acldir
            ));
//      if (class_exists('Memcache')) {
//        $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
//          "host" => "localhost",
//          "port" => "11211"
//        ));
//      } else {
//        $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
//          "cacheDir" => $conf->cache->acldir
//        ));
//      }
            return $cache;
        });
    }

    /**
     * Se encargar de injectar la clase que administra el menu principal de la aplicación
     * @return DI object
     */
    private function set_smart_menu() {
        $this->di->set('smartMenu', function() {
            return new \Sigmamovil\General\View\SmartMenu();
        });
    }

    /**
     * 
     */
    private function set_global_counters_manager() {
        $this->di->set('globalCountersManager', function() {
            return new \Sigmamovil\General\View\GlobalCountersManager();
        });
    }

    /**
     * 
     */
    private function set_personalized_css() {
        $this->di->set('personalizedCss', function() {
            return new \Sigmamovil\General\View\PersonalizedCss();
        });
    }

    /**
     * Se encarga de inyectar la clase que administra los submenus del menu principal según el rol que este en sesión
     * @return object Clase menú
     */
    private function set_smart_submenu() {
        $this->di->set("submenu", function () {
            return new \Sigmamovil\General\View\SmartSubMenu();
        });
    }

    /**
     * Lista de control de usuario para permisos sobre recursos
     * @return DI object
     */
    private function set_acl() {
        $this->di->set('acl', function() {
            $acl = new \Phalcon\Acl\Adapter\Memory();
            $acl->setDefaultAction(\Phalcon\Acl::DENY);

            return $acl;
        });
    }

    /**
     * Hash para validacion y creacion de contraseñas de los usuarios
     * @return DI object
     */
    private function set_hash_validator() {
        $this->di->set('hash', function() {
            $hash = new \Phalcon\Security();

            //Set the password hashing factor to 12 rounds
            $hash->setWorkFactor(12);

            return $hash;
        }, true);
    }

    /**
     * Models metadata crea metadatos en cache de los modelos en la aplicación
     * para evitar estar consultandolos
     * @return DI object
     */
    private function set_models_metadata() {
        $this->di->set('modelsMetadata', function() {
            $metaData = new \Phalcon\Mvc\Model\MetaData\Files(array(
//                  "lifetime" => 86400,
                "metaDataDir" => "../app/cache/metadata/"
            ));
            return $metaData;
        });
    }

    /**
     * Database Object, conexion primaria a la base de datos
     * @return DI object
     */
    private function set_db() {
        $config = $this->config;
        $di = $this->di;
        $di->setShared('db', function() use ($di, $config) {
            // Events Manager para la base de datos
            $eventsManager = new \Phalcon\Events\Manager();

            if ($config->general->profiledb) {
                // Profiler
                $profiler = $di->get('profiler');
                $timer = $di->get('timerObject');

                $eventsManager->attach('db', function ($event, $connection) use ($profiler, $timer) {
                    if ($event->getType() == 'beforeQuery') {
                        $profiler->startProfile($connection->getSQLStatement());
                        $timer->startTimer('SQL', 'Query Execution');
                    } else if ($event->getType() == 'afterQuery') {
                        $profiler->stopProfile();
                        $timer->endTimer('SQL');
                    }
                });
            }

            $connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());

            $connection->setEventsManager($eventsManager);

            return $connection;
    });
  }

  /**
   * Para creación de consultas PHQL
   * @return DI object
   */
  private function set_models_manager() {
    $this->di->set('modelsManager', function() {
      return new \Phalcon\Mvc\Model\Manager();
    });
  }

  /**
   * Directorio de assets privados
   */
  private function set_pdf_templates_folder() {
    $pt = new \stdClass;
    $pt->templates = $this->config->pdf->templates;
    $pt->relativetemplatesfolder = $this->config->pdf->templatesrelative;
    $pt->explodedbatch = $this->config->pdf->explodedbatch;
    $pt->encryptedbatch = $this->config->pdf->encryptedbatch;
    $pt->sourcebatch = $this->config->pdf->sourcebatch;
    $pt->csvbatch = $this->config->pdf->csvbatch;
    $pt->relativecsvbatch = $this->config->pdf->csvbatchrelative;
    $pt->fop = $this->config->pdf->fop;
    $pt->foplog = $this->config->pdf->foplogs;
    $pt->config = $this->config->pdf->config;
    $this->di->set('pdf', $pt);
  }

  /**
   * Directorio de assets privados
   */
  private function set_private_assets_folder() {
    $asset = new \stdClass;
    $asset->dir = $this->config->general->assetsfolder;
    $asset->dirmailstructure = $this->config->general->foldermailstructure;
    $asset->dirAllied = $this->config->general->assetsfolderallied;
    $asset->dirRoot = $this->config->general->assetsfolderroot;
    $asset->assetsbaseuri = $this->config->general->assetsbaseuri;
    $asset->assets = $this->config->general->assets;
    $asset->url = '/' . $this->urlManager->get_prefix_url_asset() . '/';
    $this->di->set('asset', $asset);
  }

  /**
   * Directorio de assets publicos
   */
  private function set_public_assets_folder() {
    $templatesfolder = new \stdClass();
    $templatesfolder->dir = $this->config->general->templatesfolder;
    $this->di->set('templatesFolder', $templatesfolder);
  }

  /**
   * Directorio de para archivos temporales
   */
  private function set_tmp_folder() {
    $tmpdir = new \stdClass;
    $tmpdir->dir = $this->config->general->tmpdir;
    $tmpdir->exportdir = $this->config->general->tmpexportdir;
    $this->di->set('tmpPath', $tmpdir);
  }

  /**
   * Directorio de footers publicos
   */
  private function set_public_footers_folder() {
    $footersfolder = new \stdClass();
    $footersfolder->dir = $this->config->general->footersfolder;
    $this->di->set('footersFolder', $footersfolder);
  }

  /**
   * Configuración MTA
   */
  private function set_mta_config() {
    $mtaConfig = new \stdClass();
    $mtaConfig->address = $this->config->mta->address;
    $mtaConfig->port = $this->config->mta->port;
    $mtaConfig->mailClass = $this->config->mta->mailclass;
    $this->di->set('mta', $mtaConfig);
  }

  /*
   * 
   */

  private function setUploadConfig() {
    $uploadConfig = new \stdClass();
    $uploadConfig->imgAssetSize = $this->config->upload->asset_img_size;
    $uploadConfig->attachmentSize = $this->config->upload->attachment_size;
    $uploadConfig->imgAssetMin = $this->config->upload->asset_img_min;
    $this->di->set('uploadConfig', $uploadConfig);
  }

  private function set_assets_config() {
    $assets = new \stdClass();
    $assets->imageSize = $this->config->assets->image_size;
    $assets->fileSize = $this->config->assets->file_size;
    $assets->folder = $this->config->assets->assets_folder;
    $assets->url = '/' . $this->urlManager->get_url_asset() . '/';
    $this->di->set('assets', $assets);
  }

  /*
   * Configuración Facebook App 
   */

  private function set_facebook_app_config() {
    $fbapp = new \stdClass();
    $fbapp->iduser = $this->config->fbapp->id;
    $fbapp->token = $this->config->fbapp->token;
    $this->di->set('fbConfig', $fbapp);
  }

  /*
   * Configuración Twitter App 
   */

  private function set_twitter_app_config() {
    $twapp = new \stdClass();
    $twapp->iduser = $this->config->twapp->id;
    $twapp->token = $this->config->twapp->token;
    $this->di->set('twConfig', $twapp);
  }

  /**
   * Configuración Sockets
   */
  private function set_sockets_config() {
    $sockets = new \stdClass();
    $sockets->importrequest = $this->config->sockets->importrequest;
    $sockets->importtochildren = $this->config->sockets->importtochildren;
    $sockets->importfromchild = $this->config->sockets->importfromchild;
    $sockets->exportrequest = $this->config->sockets->exportrequest;
    $sockets->exporttochildren = $this->config->sockets->exporttochildren;
    $sockets->exportfromchild = $this->config->sockets->exportfromchild;
    $sockets->mailrequest = $this->config->sockets->mailrequest;
    $sockets->mailtochildren = $this->config->sockets->mailtochildren;
    $sockets->mailfromchild = $this->config->sockets->mailfromchild;
    $sockets->pdfcreatorrequest = $this->config->sockets->pdfcreatorrequest;
    $sockets->pdfcreatortochildren = $this->config->sockets->pdfcreatortochildren;
    $sockets->pdfcreatorfromchild = $this->config->sockets->pdfcreatorfromchild;
    $this->di->set('sockets', $sockets);
  }

  /**
   * Configuración Google Analytics 
   */
  private function set_google_analitycs_config() {
    $googleAnalytics = new \stdClass();
    $googleAnalytics->utm_source = $this->config->googleanalytics->utm_source;
    $googleAnalytics->utm_medium = $this->config->googleanalytics->utm_medium;
    $this->di->set('googleAnalytics', $googleAnalytics);
  }

  /**
   * Directorio de reportes de correo
   */
  private function set_mail_reports_folder() {
    $mailReportsDir = new \stdClass();
    $mailReportsDir->reports = $this->config->mailreports->tmpdirmailreports;
    $this->di->set('mailReportsDir', $mailReportsDir);
  }

  /**
   * Gestor de sesiones
   * @return DI object
   */
  private function set_session_manager() {
    $this->di->setShared('session', function() {

      // Set the max lifetime of a session with 'ini_set()' to one hour
      /* ini_set('session.gc_maxlifetime', 4600);
        session_set_cookie_params(3600); */

      $session = new \Phalcon\Session\Adapter\Files(
              array(
          'uniqueId' => 'sigmamovil.aio'
              )
      );
      $session->start();
      return $session;
    });
  }

  /**
   * Flash Object, para mantener mensajes flash entre una página y otra
   * @return DI object
   */
  private function set_flash_session_messages() {
    $this->di->set('flashSession', function() {
      $flash = new \Phalcon\Flash\Session(array(
          'error' => 'alert alert-danger',
          'success' => 'alert alert--success',
          'notice' => 'alert alert--info',
          'warning' => 'alert alert--warning'
      ));
      return $flash;
    });
  }

  /**
   * Administrador de notificaciones, se encarga de crear y mostrar notificaciones hacia el cliente entre una página y otra
   */
  private function set_nofication_manager() {
    $this->di->set('notification', function() {
      $notification = new \Sigmamovil\General\View\NotificationManager();
      return $notification;
    });
  }

  /**
   * FlashMessage Object, para mostrar mensajes informativos y administrativos a los usuarios
   * @return DI object
   */
  public function set_administrative_messages() {
    $this->di->set('flashMessage', function() {
      $flashMessage = new \Sigmamovil\General\View\FlashMessages();
      return $flashMessage;
    });
  }

  /**
   * Profiler Object. Lo utilizamos en modo de depuracion/desarrollo para
   * determinar los tiempos de ejecucion de SQL
   * @return DI object
   */
  private function set_profiler() {
    $this->di->set('profiler', function() {
      return new \Phalcon\Db\Profiler();
    }, true);
  }

  /**
   * Compilador de archivos volt
   * @param type $di
   * @return DI object
   */
  private function set_volt_compiler() {
    $di = $this->di;
    $di->setShared('volt', function($view, $di) {
      $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

      $volt->setOptions(array(
          "compileAlways" => true,
          "compiledPath" => "../app/compiled-templates/",
          'stat' => true
      ));

      $compiler = $volt->getCompiler();
//      $compiler->addFilter('number_format', function($number) {
//        return $number + 1;
////        return number_format((float) $resolvedArgs, 0, ",", ".");
//      });
            $compiler->addFilter("split", function ($resolvedArgs) {
                return "explode(',' , " . $resolvedArgs . ")";
            });
            $compiler->addFunction("in_array", "in_array");

//                $compiler->addFilter('int', function($resolvedArgs, $exprArgs) {
//                    return 'intval(' . $resolvedArgs . ')';
//                });
//
            $compiler->addFilter('numberf', function ($resolvedArgs, $exprArgs) {
                return 'number_format(' . $resolvedArgs . ', 0, \',\', \'.\')';
            });
//
//                $compiler->addFilter('change_spaces_in_between', function ($resolvedArgs, $exprArgs){
//                    return 'str_replace(" ", "_", ' . $resolvedArgs . ')';
//                });
//                $compiler->addFunction('value_in_array', function ($resolvedArgs, $exprArgs) use ($compiler){
//                    return 'in_array(' . $resolvedArgs . ')';
//                });
//
//                $compiler->addFunction('ember_customfield', function ($resolvedArgs, $exprArgs) {
//                    return 'CreateViewEmber::createField(' . $resolvedArgs . ')';
//                });
//
//                $compiler->addFunction('ember_customfield_xeditable', function ($resolvedArgs, $exprArgs) {
//                    return 'CreateViewEmber::createCustomFieldXeditable(' . $resolvedArgs . ')';
//                });
//
//                $compiler->addFunction('ember_customfield_options', function ($resolvedArgs, $exprArgs) {
//                    return 'CreateViewEmber::createOptions(' . $resolvedArgs . ')';
//                });
//
//                $compiler->addFunction('ember_customfield_options_xeditable', function ($resolvedArgs, $exprArgs) {
//                    return 'CreateViewEmber::createOptionsForXeditable(' . $resolvedArgs . ')';
//                });
//
//                $compiler->addFunction('ember_textfield', function ($resolvedArgs, $exprArgs) {
//                    return 'CreateViewEmber::createEmberTextField(' . $resolvedArgs . ')';
//                });
//                
//                $compiler->addFunction('get_inactive', function ($resolvedArgs, $exprArgs) {
//                    return 'ContactCounter::getInactive(' . $resolvedArgs . ')';
//                });
//                
//                $compiler->addFunction('acl_Ember', function ($resolvedArgs, $exprArgs){
//                    return 'CreateAclEmber::getAclToEmber(' . $resolvedArgs . ')';
//                });
//                
//                $compiler->addFunction('mail_options', function ($resolvedArgs, $exprArgs){
//                    return 'OptionsMail::getOptions(' . $resolvedArgs . ')';
//                });
//                
//                $compiler->addFunction('programming_options', function ($resolvedArgs, $exprArgs){
//                    return 'ProgrammingOptions::getOptions(' . $resolvedArgs . ')';
//                });
//            
//                $compiler->addFunction('smart_wizard', function ($resolvedArgs, $exprArgs){
//                    return 'SmartWizard::getWizard(' . $resolvedArgs . ')';
//                });
//                
//                $compiler->addFunction('select_target', function ($resolvedArgs, $exprArgs){
//                    return 'SmartSelect::getSelectTarget(' . $resolvedArgs . ')';
//                });

            return $volt;
        });
    }

    /**
     * Encargado de configurar volt 
     * @return DI object
     */
    private function set_view() {
        $this->di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir('../app/views/');
            $view->registerEngines(array(
                ".volt" => 'volt'
            ));
            return $view;
        });
    }

    public function get_di() {
        return $this->di;
    }

    public function get_config() {
        return $this->config;
    }

    /**
     * Directorio de assets privados
     */
    private function set_roles() {
        $this->di->set('roles', $this->config->roles);
    }

    private function set_services() {
        $this->di->set('services', $this->config->services);
    }

    private function set_type_sms() {
        $this->di->set('typeSms', $this->config->type_sms);
    }

    private function set_type_survey() {
        $this->di->set('typeSurvey', $this->config->type_survey);
    }

    private function set_component_question() {
        $this->di->set('componentQuestion', $this->config->component_question);
    }

    private function set_status_sms() {
        $this->di->set('statusSms', $this->config->status_sms);
    }

    private function set_config_public_domain() {
        $this->di->set('publicDomain', $this->config->public_domain->invalid_domain);
    }

    private function file_exists() {
        $this->di->set('file_exists', function () {
            return new Misc\Uploader();
        });
    }

    public function set_string_status_sms() {
        $this->di->set('StringStatus', function() {
            $StringStatus = new View\StringStatus();
            return $StringStatus;
        });
    }

    public function set_mongo_db() {
        $this->di->set('mongo', function () {
            $mongo = new \MongoClient($this->config->host_mongo->localhost);
            //$mongo->DEFAULT_HOST  = "192.168.18.140";
            return $mongo->selectDB("aio");
        }, true);
    }

    public function set_mongo_manager_db() {
        $this->di->set('mongomanager', function () {
            $mongo = new \MongoDB\Driver\Manager($this->config->host_mongo->localhost);
            return $mongo;
        }, true);
    }

    public function set_collection_manager() {
        $this->di->set(
                'collectionManager', function () {
            $eventsManager = new \Phalcon\Events\Manager();
            $modelsManager = new \Phalcon\Mvc\Collection\Manager();
            $modelsManager->setEventsManager($eventsManager);
            return $modelsManager;
        }, true
        );
    }

    private function set_filters_segment() {
        $this->di->set('filtersSegment', $this->config->filters_segment);
    }

    /**
     * Directorio de assets privados
     */
    private function setPrivateAssetsFolder() {
        $asset = new \stdClass;
        $asset->dir = $this->config->general->assetsfolder;
        $asset->url = '/' . $this->urlManager->getAppUrlAsset() . '/';
        $this->di->set('asset', $asset);
    }

    private function set_key_jwt() {
        $jwt = new \stdClass;
        $jwt->key = $this->config->general->keyjwt;
        $this->di->set('keyjwt', $jwt);
    }

    private function hour_sms() {
        $hour = new \stdClass;
        $hour->startHour = $this->config->hour_sms->start_hour;
        $hour->endHour = $this->config->hour_sms->end_hour;
        $this->di->set('hoursms', $hour);
    }

    private function setinstanceIDprefix() {
        $obj = new \stdClass();
        $obj->prefix = isset($this->config->system->instance_id) ? $this->config->system->instance_id : '';
        $this->di->set('instanceIDprefix', $obj);
    }

    public function set_name_roles() {
        $roles = (object) $this->config->nameRoles->toArray();
        $this->di->set('nameRoles', $roles);
    }

    public function set_suggestions_datas() {
        $suggestionsDatas = (object) $this->config->suggestionsDatas->toArray();
        $this->di->set('suggestionsDatas', $suggestionsDatas);
    }

    public function setIdAlliedSigma() {
        $obj = new \stdClass;
        $obj->idAlliedSigma = (int) $this->config->general->idAlliedSigma;
        $this->di->set('idAllied', $obj);
    }

    public function setConfigFb() {
        $obj = new \stdClass;
        $obj->idApp = $this->config->app_facebook->idApp;
        $obj->secretApp = $this->config->app_facebook->secretApp;
        $this->di->set('configFb', $obj);
    }

    public function set_app_facebook() {
        $credentials = (object) $this->config->app_facebook->toArray();
        $this->di->set("app_facebook", $credentials);
    }

    public function set_mail_tester() {
        $credentials = (object) $this->config->mail_tester->toArray();
        $this->di->set("mail_tester", $credentials);
    }

    public function setUrlDefaultSurvey() {
        $obj = new \stdClass;
        $obj->urlSurvey = $this->config->general->urlDefaultSurvey;
        $this->di->set('urlSurvey', $obj);
    }

    public function setDefaultgoogleAnalyticsEmail() {
        $googleAnalyticsEmail = (object) $this->config->googleAnalyticsEmail->toArray();
        $this->di->set("googleAnalyticsEmail", $googleAnalyticsEmail);
    }

    public function setDataValidation() {
        $dataValidation = (object) $this->config->dataValidation->toArray();
        $this->di->set("dataValidation", $dataValidation);
    }

    public function setApiSmsTwoWay() {
        $dataApiSmsTwoWay = (object) $this->config->apismstwoway->toArray();
        $this->di->set("apiSmsTwoWay", $dataApiSmsTwoWay);
    }

    /*public function setApiWhatsapp() {
        $dataApiWhatsapp = (object) $this->config->apiwhatsapp->toArray();
        $this->di->set("apiWhatsapp", $dataApiWhatsapp);
    }*/

    public function setAdapters() {
        $dataAdapters = (object) $this->config->adapters->toArray();
        $this->di->set("adapters", $dataAdapters);
    }
    
    public function setCookie() {
      $this->di->set(
            'cookies', function () {
              $cookies = new \Phalcon\Http\Response\Cookies();

              $cookies->useEncryption(true);

              return $cookies;
            }
      );
      $this->di->set('crypt', function() {
      $crypt = new \Phalcon\Crypt();
      $crypt->setKey('#1dj8$=dp?.ak//j'); //Use your own key!
      return $crypt;
    });
  }
  
  public function setHabeasData(){
    $obj = new \stdClass();
    $obj->habeasData = $this->config->general->habeas_data;
    $this->di->set("habeasData", $obj);
  }
  
  public function setIpServer(){
    $obj = new \stdClass();
    $obj->ip = $this->config->general->ip;
    $this->di->set("ipServer", $obj);
  }
  
  public function setGeneral() {
    $general = (object) $this->config->general->toArray();
    $this->di->set("general", $general);
  }
  
  public function setMtaDefault(){
    $obj = new \stdClass();
    $obj->idMtaDefault = $this->config->general->idMtaDefault;
    $this->di->set("mtaDefault", $obj);
  }
  
  public function setInfobitAnswersCharged(){
    $obj = new \stdClass();
    //$dataAnswersNegativesInfobit = (object) $this->config->infobitAnswersNegatives->toArray();
    $obj->infobitAnswersCharged = $this->config->infobitAnswersCharged;
    $this->di->set("infobitAnswersCharged", $obj);
  }

}
