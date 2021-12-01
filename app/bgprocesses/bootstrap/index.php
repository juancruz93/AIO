<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url as UrlProvider;

try {
    /* Carga el archivo de configuracion */
    $config = new ConfigIni(__DIR__ . '/../../config/configuration.ini');

    /* Carga el autoloader */
//Loading Phalcon
    $loader = new \Phalcon\Loader();


//Register directories
    $loader->registerDirs(array(
        __DIR__ . '/../../models/',
        __DIR__ . '/../../logic/',
        __DIR__ . '/../../validators/',
        __DIR__ . '/../../wrappers/'
    ));
//Register namespaces
    $loader->registerNamespaces(
            array(
//            'EmailMarketing\\SocialTracking' => '../app/SocialTracking/',
        'Sigmamovil\\General' => __DIR__ . '/../../general/',
        'Sigmamovil\\General\\Misc' => __DIR__ . '/../../general/misc',
        'Sigmamovil\\General\\Exceptions' => __DIR__ . '/../../exceptions',
        'Sigmamovil\\General\\Automatic' => __DIR__ . '/../../general/automatic',
        'Sigmamovil\\General\\View' => __DIR__ . '/../../general/view',
        'Sigmamovil\\General\\Links' => __DIR__ . '/../../general/links',
        'ElephantIO' => __DIR__ . '/../../general/misc/elephantio/wisembly/elephant.io/src/',
        'Psr\\Log' => __DIR__ . '/../../general/misc/elephantio/psr/log/Psr/Log/',
        'Sigmamovil\\Logic\\Editor' => __DIR__ . '/../../logic/editor/',
        'Sigmamovil\\Logic' => __DIR__ . '/../../logic/',
        'Sigmamovil\\Wrapper' => __DIR__ . '/../../wrappers/',
        'Sigmamovil\\Bgprocesses' => __DIR__ . '/../../bgprocesses/'
            ), true
    );

    $loader->registerClasses(array(
        "simple_html_dom" => __DIR__ . "/../../library/simple_html_dom.php",
    ));

// register autoloader
    $loader->register();

    /* Carga los servicios */
    $di = new FactoryDefault();

    $di->set('urlManager', function () use ($config) {

        $url = new \Sigmamovil\General\Misc\UrlManagerObject($config);

//    $url = new UrlProvider();
//    $url->setBaseUri($config->general->baseuri);
        return $url;
    });

    $di->set('db', function () use ($config) {
        $config = $config->get('database')->toArray();
        $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
        unset($config['adapter']);
        return new $dbClass($config);
    });

    $di->set('statusSms', $config->status_sms);
    $di->set('services', $config->services);
    $di->set('mta', $config->mta);

    $di->set('user', function () {
        return null;
    });

    $obj = new \stdClass();
    $obj->prefix = isset($config->system->instance_id) ? $config->system->instance_id : '';
    $di->set('instanceIDprefix', $obj);

    $di->set('logger', function () {
        // Archivo de log
        return new \Phalcon\Logger\Adapter\File(__DIR__ . "/../../logs/scripts.log");
    });

    $di->set('mongo', function () use ($config) {
        $mongo = new \MongoClient($config->host_mongo->localhost);
//      $mongo->DEFAULT_HOST  = "192.168.18.140";
        return $mongo->selectDB("aio");
    }, true);

    $di->set('mongomanager', function () use ($config) {
        $mongo = new \MongoDB\Driver\Manager($config->host_mongo->localhost);
        return $mongo;
    }, true);

    $di->set(
            'collectionManager', function () {
        $eventsManager = new \Phalcon\Events\Manager();
        $modelsManager = new \Phalcon\Mvc\Collection\Manager();
        $modelsManager->setEventsManager($eventsManager);
        return $modelsManager;
    }, true
    );

    $di->set(
            'asset', function () use ($config) {
        $asset = new \stdClass;
        $asset->dir = $config->general->assetsfolder;
        $url = new \Sigmamovil\General\Misc\UrlManagerObject($config);
        $asset->url = '/' . $url->getAppUrlAsset() . '/';
        return $asset;
    }, true
    );

    /**
     * Configuración MTA
     */
    $mtaConfig = new \stdClass();
    $mtaConfig->address = $config->mta->address;
    $mtaConfig->port = $config->mta->port;
    $mtaConfig->mailClass = $config->mta->mailclass;
    $di->set('mtadata', $mtaConfig);

    /**
     * Directorio de para archivos temporales
     */
    $tmpdir = new \stdClass;
    $tmpdir->dir = $config->general->tmpdir;
    $tmpdir->exportdir = $config->general->tmpexportdir;
    $di->set('tmpPath', $tmpdir);

    $asset = new \stdClass;
    $asset->dir = $config->general->assetsfolder;
    $asset->dirAllied = $config->general->assetsfolderallied;
    $asset->dirRoot = $config->general->assetsfolderroot;
    $url = new \Sigmamovil\General\Misc\UrlManagerObject($config);
    $asset->url = '/' . $url->get_prefix_url_asset() . '/';
    $di->set('asset', $asset);

    $path = new \stdClass();
    $path->path = $config->path->path;
    $di->set('path', $path);
    $di->set('mail_return_path', "correoreturnpath@correo.com");

    $kannelProperties = new \stdClass();
    $kannelProperties->baseUrl = $config->kannel_properties->baseUrl;
    $kannelProperties->dlrURL = $config->kannel_properties->dlrURL;
    $kannelProperties->keyjwt = $config->general->keyjwt;
    $di->set('kannelProperties', $kannelProperties);

    $di->set('services', $config->services);

    $googleAnalyticsEmail = (object) $config->googleAnalyticsEmail->toArray();
    $di->set("googleAnalyticsEmail", $googleAnalyticsEmail);

    $dataValidation = (object) $config->dataValidation->toArray();
    $di->set("dataValidation", $dataValidation);


    $dataApiSmsTwoWay = (object) $config->apismstwoway->toArray();
    $di->set("apiSmsTwoWay", $dataApiSmsTwoWay);

    $dataAdapters = (object) $config->adapters->toArray();
    $di->set("adapters", $dataAdapters);
    
    $di->set('filtersSegment', $config->filters_segment);
    
    $di->set('infobitAnswersCharged', $config->infobitAnswersCharged);

    $app = new Application($di);
//   echo $app->handle()->getContent();
} catch (\Phalcon\Exception $e) {
    echo "Exception: ", $e->getMessage();
    echo "Exception: ", $e->getTraceAsString();
}
