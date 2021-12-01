<?php

ini_set('memory_limit', '512M');
require_once(__DIR__ . "\\..\\..\\bootstrap\\index.php");

if (isset($argv[1])) {
  $id = $argv[1];
}

$import = new SegmentManager();
$import->addContactByImport($id);

class SegmentManager
{

  public function typeConditionsAll($contact, $field, $value, $key, $flag) {
    if ($key == "Es igual a") {
      if ($contact[$field] != $value) {
        $flag = false;
      }
    } else if ($key == "Contiene") {
      if (strpos($contact[$field], $value) == false) {
        $flag = false;
      }
    } else if ($key == "No contiene") {
      if (strpos($contact[$field], $value) >= 0) {
        $flag = false;
      }
    } else if ($key == "Empieza con") {
      if (preg_match("#^" . $value . ".*#s", trim($contact[$field])) == 0) {
        $flag = false;
      }
    } else if ($key == "Termina en") {
      if (preg_match("#.*" . $value . "$#s", trim($contact[$field])) == 0) {
        $flag = false;
      }
    } else if ($key == "Mayor a") {
      if ($contact->$field < $value && is_numeric($contact[$field])) {
        $flag = false;
      }
    } else if ($key == "Menor a") {
      if ($contact->$field > $value && is_numeric($contact[$field])) {
        $flag = false;
      }
    }
    return $flag;
  }

  public function typeConditionsSome($contact, $field, $value, $key, $flag) {

    if ($key == "Es igual a") {
      if ($contact[$field] == $value) {
        $flag = true;
      }
    } else if ($key == "Contiene") {
      $p = strpos($contact[$field], $value);
      if (is_numeric($p)) {
        $flag = true;
      }
    } else if ($key == "No contiene") {
      if (strpos($contact[$field], $value) == false) {
        $flag = true;
      }
    } else if ($key == "Empieza con") {
      if (preg_match("#^" . $value . ".*#s", trim($contact[$field])) == 1) {
        $flag = true;
      }
    } else if ($key == "Termina en") {
      if (preg_match("#.*" . $value . "$#s", trim($contact[$field])) >= 1) {
        $flag = true;
      }
    } else if ($key == "Mayor a") {
      if ($contact->$field > $value && is_numeric($contact[$field])) {
        $flag = true;
      }
    } else if ($key == "Menor a") {
      if ($contact->$field < $value && is_numeric($contact[$field])) {
        $flag = true;
      }
    }
    return $flag;
  }

  public function addContactByImport($idImport) {
    $bulkCont = new MongoDB\Driver\BulkWrite;
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $importcontactfile = \Importcontactfile::findFirst(array(
          "conditions" => "idImportcontactfile = ?0",
          "bind" => array(0 => $idImport)
    ));
    $options = array(
      'projection' => array('_id' => 0, 'idContact' => 1),
    );
    $query = new \MongoDB\Driver\Query(["contactlist.idContactlist" => "" . $importcontactfile->Importfile->idContactlist . ""]);
    $segment = $manager->executeQuery("aio.segment", $query)->toArray();
    unset($query);
    unset($importcontactfile);
    $queryRepeat = new \MongoDB\Driver\Query([], $options);
//    $queryRepeat = new \MongoDB\Driver\Query(["idContact" => ['$in' => [1200480, 1200481]]], $options);
    $tmp = $manager->executeQuery("aio.tmpid{$idImport}", $queryRepeat)->toArray();
//    var_dump($tmp.length);
//    var_dump(count($tmp));
//    var_dump($tmp[0]);
//    var_dump($tmp[ (count($tmp) - 1) ]);
//    exit();

    $queryContact = new MongoDB\Driver\Query(["idContact" => ['$in' => [$tmp[0]->idContact, $tmp[ (count($tmp) - 1)]->idContact ]]], $options);
    $array = $manager->executeQuery("aio.contact", $queryContact)->toArray();
    $newArray = array_map("current", $array);
    unset($array);
    $queryRepeatContact = new \MongoDB\Driver\Query([]);

    $c = $manager->executeQuery("aio.contact", $queryRepeatContact)->toArray();
    exit();
    $p = json_encode($c);
    unset($c);
    $contact = json_decode($p, true);
    unset($p);
    try {
      foreach ($tmp as $keytmp) {
        $index = array_search($keytmp->idContact, $newArray);
        foreach ($segment as $keysegment) {

          if ($keysegment->conditions == "Todas las condiciones") {
            $flag = true;
            foreach ($keysegment->filters as $key) {
              $field = $key->idCustomfield;
              $value = $key->value;
              $flag = $this->typeConditionsAll($contact[$index], $field, $value, $key->conditions, $flag);
            }
            if ($flag == true) {
              $contact[$index]["idSegment"] = $keysegment->idSegment;
              unset($contact[$index]["_id"]);
              $bulkCont->insert($contact[$index]);
            }
          } else {
            $flag = false;
            foreach ($keysegment->filters as $key) {
              $field = $key->idCustomfield;
              $value = $key->value;
              $flag = $this->typeConditionsSome($contact[$index], $field, $value, $key->conditions, $flag);
            }
            if ($flag == true) {
              $contact[$index]["idSegment"] = $keysegment->idSegment;
              unset($contact[$index]["_id"]);
              $bulkCont->insert($contact[$index]);
            }
          }
        }
      }
//      $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
//      $manager->executeCommand('aio', $dropCollectionTmp);
      $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      $manager->executeBulkWrite('aio.sxc', $bulkCont, $writeConcern);
    } catch (InvalidArgumentException $ex) {
//      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log("Error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("error: " . $e->getWriteResult());
    }
  }

}
