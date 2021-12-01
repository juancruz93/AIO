<?php

ini_set("auto_detect_line_endings", true);
ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");

$import = new Script();
$import->start();

class Script
{

  public function start()
  {
    var_dump(time());
    exit;
    try {
      $route = __DIR__ . "/mxc.csv";
      $handle = fopen($route, "r");

      if (!$handle) {
        \Phalcon\DI::getDefault()->get('logger')->log("Error al abrir el archivo original");
        throw new \InvalidArgumentException('Error al procesar el archivo. Contacte a su administrador!');
      }
      $ids = "";
      while ((($record = fgetcsv($handle, 0, ";")) !== false)) {
        var_dump($record[0]);
        $ids .= $record[0] . ",";
      }
      fclose($handle);

      $ids = substr($ids, 0, -1);

      echo $ids;
      exit;

      $sql = "UPDATE cxcl SET deleted = " . time() . ", active=0 WHERE idContact IN ({$ids})";
      $this->db->execute($sql);
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

}