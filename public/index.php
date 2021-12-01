<?php
require_once '../app/autoload.php';

try {
    $app = new \Sigmamovil\General\AppObject();
    $app->set_config_file("../app/config/configuration.ini");

    // Create timer object
//    $timer = new \Sigmamovil\General\Misc\TimerObject();
    // Start counting
//    $timer->startTimer('app', 'The whole app');

    $app->configure();

    $di = $app->get_di();

   // $di->set('timerObject', $timer);
    //Handle the request
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

    // Finalizar timer
//    $timer->endTimer('app');

    // Grabar en el log
//    $di->get('logger')->log($timer);

    // Grabar en LOG toda la ejecucion de SQL del profiler
    // Solamente si esta configurado asi
    if ($app->get_config()->general->profiledb) {
        $dblogger = new \Phalcon\Logger\Adapter\File("../app/logs/dbdebug.log");;
        $profiles = $di->get('profiler')->getProfiles();
        if (count($profiles) > 0) {
            $dblogger->log("==================== Application Profiling Information ========================", \Phalcon\Logger::INFO);
            foreach ($profiles as $profile) {
                $str = '******************************************************' . PHP_EOL .
                    \sprintf('SQL Statement: [%s]', $profile->getSQLStatement()) . PHP_EOL .
                    \sprintf('Start time: [%d]', $profile->getInitialTime()) . PHP_EOL .
                    \sprintf('End time: [%d]', $profile->getFinalTime()) . PHP_EOL .
                    \sprintf('Total elapsed time: [%f]', $profile->getTotalElapsedSeconds()) . PHP_EOL .
                    '******************************************************';
                $dblogger->log($str, \Phalcon\Logger::INFO);
            }
            $dblogger->log("==================== Application Profiling Information End ====================", \Phalcon\Logger::INFO);
        }
    }
    
    $di->set('security', function(){

        $security = new \Phalcon\Security();

        //Set the password hashing factor to 12 rounds
        $security->setWorkFactor(12);

        return $security;
        }, true);
} 
catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}
