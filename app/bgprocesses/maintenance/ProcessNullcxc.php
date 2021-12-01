<?php

//ini_set("auto_detect_line_endings", true);
//ini_set('memory_limit', '900M');
ini_set('max_execution_time', 0);
require_once(__DIR__ . "/../bootstrap/index.php");
$ProcessNullcxc = new ProcessNullcxc();
$ProcessNullcxc->proccessNull();

class ProcessNullcxc {

  public function __construct() {

  }

  public
          $skip,
          $limit = 100,
          $count = 0;

  public function proccessNull() {
//    \Phalcon\DI::getDefault()->get('logger')->log("ProcessNullcxc : Stado de recorrido de los registros de cxc, stado del skip :" . $this->skip);

    try {
      $manager = \Phalcon\DI::getDefault()->get('mongomanager');
      $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      while (true) {
        \Phalcon\DI::getDefault()->get('logger')->log("ProcessNullcxc : Stado de recorrido de los registros de cxc, stado del skip :" . $this->skip);
        $contion = array(''
            . 'conditions' => array(
            ),
            'fields' => array(
            ),
            'skip' => $this->skip,
            'limit' => $this->limit);
        $this->skip = $this->skip + $this->limit;
        $queryEmailContact = \Cxc::find($contion);
        if (count($queryEmailContact) == 0) {
          break;
        }

        foreach ($queryEmailContact as $k => $va) {
          if (!isset($va->idContactlist)) {
            continue;
          }

          foreach ($va->idContactlist as $ke => $val) {
            $bulkDeleted = new \MongoDB\Driver\BulkWrite;

            $bulkDeleted->update(['idContact' => (int) $va->idContact], ['$unset' => ['idContactlist.' . $ke => null]]);
            $manager->executeBulkWrite('aio.cxc', $bulkDeleted, $writeConcern);
            unset($bulkDeleted);

            $bulk = new \MongoDB\Driver\BulkWrite;
            foreach (array_filter($val) as $key => $value) {

              $bulk->update(['idContact' => (int) $va->idContact], ['$set' => ['idContactlist.' . $ke . "." . $key => $value]], ['multi' => true]);
            }
            if (count($val) > 0) {
              $manager->executeBulkWrite('aio.cxc', $bulk, $writeConcern);
              unset($bulk);
            }
            if (count($val) == 0) {
              $bulkEmpty = new \MongoDB\Driver\BulkWrite;
              $bulkEmpty->update(['idContact' => (int) $va->idContact], ['$set' => ['idContactlist.' . $ke => (Object) $val]], ['multi' => true]);
              $manager->executeBulkWrite('aio.cxc', $bulkEmpty, $writeConcern);
              unset($bulk);
            }
          }
          $this->count++;
        }
      }
      \Phalcon\DI::getDefault()->get('logger')->log("ProcessNullcxc : fin de los procesos, stado del skip :" . $this->skip);
    } catch (Exception $exc) {
      var_dump($exc);
      \Phalcon\DI::getDefault()->get('logger')->log("Error ProcessNullcxc : contador de registros == " . $this->count . "Tipo de error :" . $exc);
      \Phalcon\DI::getDefault()->get('logger')->log("Error ProcessNullcxc : ultimo Skip procesado == " . $this->skip);
    }
  }

}
