<?php 
ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");

//Se instancia la clase y se ejecuta el metodo principal
$systemProfile = new SystemProfile();
$systemProfile->start();

class SystemProfile {

  protected $logger;
  protected $db;

  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->logger = $di->get("logger");
    $this->db = $di->get('db');
  }

  public function start() {

    //Conexion a bd aio en mongo
    $dbname = 'aio';
    $mongo = (new MongoClient());
    $db = $mongo->$dbname;

    //Array donde quedaran almacenados la data de la actividad de los usuarios
    $arrMongoUserLogs = array();

    //Se selecciona (para luego hacer uso de ella) la coleccion pruebaAioUsersLogs donde quedaran almacenados los logs solo de los usuarios de BD de AIO
    $col = $db->selectCollection('aio_users_logs');

    //Se consulta la coleccion system.profile
    $response = $db->system->profile->find();
    // Se recorre la coleccion system.profile
      foreach ($response as $key => $value) {
        //Si los usuarios coinciden con los asignados al qeuipo TI entonces Que guarde en el array la data de dicha actividad
        if($value['user'] == 'yipia@aio' || 
           $value['user'] == 'lcollazos@aio' || 
           $value['user'] == 'jquinones@aio' || 
           $value['user'] == 'fgarcia@aio' ){
          
          $arrMongoUserLogs = array(
            'user' => $value['user'],
            'collection' => $value['ns'],
            'operation' => $value['op'],
            'query' => $value['query'],
            'ts' => date("Y-m-d h:i:sa",$value['ts']->sec)
          );
          //Se hace insercion de dichas actividades en la coleccion pruebaAioUsersLogs
          $col->insert($arrMongoUserLogs);
      }
    }

    //Se desactiva profiling en mongo
    $db->setProfilingLevel(0);
    //Se borran los elementos (documentos) de la coleccion system.profile
    $db->system->profile->drop();

    //Se reactiva profiling
    $db->setProfilingLevel(2);
    $db->setProfilingLevel(2,50);

    //Se verifica que el nivel de profiling siempre sea 2
    var_dump($db->getProfilingLevel());
  }

}
