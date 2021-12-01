<?php

//Loading Phalcon
$loader = new \Phalcon\Loader();

//Register directories
$loader->registerDirs(array(
    '../app/controllers/',
    '../app/plugins/',
    '../app/models/',
    '../app/forms/',
    '../app/validators/',
    '../app/library/',
    '../app/wrappers/',
    '../app/logic/',
//        '../app/library/facebook/',
//        '../app/library/twitter/',
    '../app/editorlogic/',
//        '../app/bgprocesses/sender/',
));

//Register namespaces
$loader->registerNamespaces(
        array(
//            'EmailMarketing\\SocialTracking' => '../app/SocialTracking/',
    'Sigmamovil\\General' => '../app/general/',
    'Sigmamovil\\General\\Misc' => '../app/general/misc',
    'Sigmamovil\\General\\FormElements' => '../app/general/form-elements',
    'Sigmamovil\\General\\Automatic' => '../app/general/automatic',
    'Sigmamovil\\General\\View' => '../app/general/view',
    'Sigmamovil\\General\\Exceptions' => '../app/exceptions',
    'ElephantIO' => '../app/general/misc/elephantio/wisembly/elephant.io/src/',
    'Psr\\Log' => '../app/general/misc/elephantio/psr/log/Psr/Log/',
    'Sigmamovil\\Logic\\Editor' => '../app/logic/editor/',
    'Sigmamovil\\Wrapper' => '../app/wrappers/',
    'Sigmamovil\\Bgprocesses' => '../app/bgprocesses/'
        ), true
);

$loader->registerClasses(array(
    "simple_html_dom" => "../app/library/simple_html_dom.php",
));

// register autoloader
$loader->register();


