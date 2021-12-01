<?php

namespace Sigmamovil\Wrapper;

class SegmentWrapper extends \BaseWrapper {

  private $segments = array();
  private $segment;
  private $sxc = array();
  private $totals;
  private $contactlist;

  public function findAllSegment($page, $stringSearch) {
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $where = ["idSubaccount" => \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount, "deleted" => 0];
    if ($stringSearch != -1) {
      $where['$or'] = [["name" => ['$regex' => ".*$stringSearch.*"]]];
    }
    $this->data = \Segment::find([$where, "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, "skip" => $page, "sort"=> array("_id"=>-1)]);
    $this->totals = \Segment::count([ $where]);
    $this->modelData();
  }

  public function setSegment($data) {
    $this->segment = $data;
  }

  public function modelData() {
    $this->segments = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $value) {
      $totalsxc = \Sxc::count(array(
                  "conditions" => array(
                      "idSegment" => (int) $value->idSegment,
                      "deleted" => (int) 0,
                      "unsubscribed" => (int) 0, 
                      "blocked" => (int) 0 
                  )
      ));
      $value->totalSxc = $totalsxc;
      array_push($arr, $value);
    }
    array_push($this->segments, ["items" => $arr]);
  }

  public function getSegments() {
    return $this->segments;
  }

  public function getSxc() {
    return $this->sxc;
  }

  public function findAllSxc($idSegment, $page, $stringSearch) {
    //$where = ["idSegment" => (int) $idSegment];
    $where = ["idSegment" => (int) $idSegment, "deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0 ];
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0", "bind" => [0 => $idContactlist]]);
    $fields = array("name", "email", "phone");
    foreach ($customfield as $key) {
      array_push($fields, $key->idCustomfield);
    }
    if ($stringSearch != -1) {
      $stringSearch = explode(",", $stringSearch);
      foreach ($fields as $value) {
        foreach ($stringSearch as $key) {
          if ($key or $key != "" or ! empty($key)) {
            $key = trim($key);
            $arr[] = [ $value => ['$regex' => ".*$key.*"]];
            $where['$or'] = $arr;
          }
        }
      }
    }
    $this->data = \Sxc::find([$where,
       "skip" =>  \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT * $page,
       "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT
    ]);
    $this->totals = \Sxc::count([ $where]);
    $this->contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $idContactlist)));
    $this->modelDataSxc();
  }

  public function modelDataSxc() {
    $this->sxc = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    $customManager = new \Sigmamovil\General\Misc\ContactManager();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $cxcl = \Cxcl::find(array("conditions" => "idContact = ?0", "bind" => array(0 => $value->idContact)));
      $cxc = \Cxc::findFirst([["idContact" => $value->idContact]]);
      unset($value->_id);
      unset($value->idSegment);
      $value = json_encode($value);
      $value = json_decode($value, true);
      foreach ($cxcl as $keycxcl) {
        foreach ($cxc->idContactlist[$keycxcl->idContactlist] as $p => $v) {
          $customfield = ["value" => $v["value"], "type" => $v["type"], "idCustomfield" => $p];
          $value[$v["name"]] = $customfield;
        }
      }

      $getcontactlist = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
              ->from('Cxcl')
              ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
              ->where(" Cxcl.deleted = 0 AND Cxcl.idContact  = {$value["idContact"]} ")
              ->getQuery()
              ->execute();

      $nameContactlist = array();
      foreach ($getcontactlist as $keycontactlist) {
        $nameContactlist[] = array("name" => $keycontactlist->Contactlist->name,
            "idContactlist" => $keycontactlist->Contactlist->idContactlist,
            "unsubscribed" => $keycontactlist->unsubscribed);
      }
      $value["contactlist"] = $nameContactlist;
      array_push($arr, $value);
    }
    array_push($this->sxc, ["items" => $arr]);
  }

  public function findsegment($idSegment) {
    $filter = ["idSegment" => (int) $idSegment];
    $options = [];
    $query = new \MongoDB\Driver\Query($filter, $options);
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $this->segment = $manager->executeQuery("aio.segment", $query)->toArray();
    $this->modelSegment();
  }

  public function modelSegment() {
    $arr = array();
    foreach ($this->segment as $key => $value) {
      $arr[$key] = $value;
    }
    $this->segments = $arr;
  }

  public function editSegment() {
    if (!isset($this->segment->idSegment)) {
      throw new \InvalidArgumentException("El argumento del segmento que intenta modificar, no existe");
    }

    $segment = \Segment::findFirst(array(
                "conditions" => array(
                    "idSegment" => (int) $this->segment->idSegment
                )
    ));

    if (!$segment) {
      throw new \InvalidArgumentException("El segmento que intenta editar no existe");
    }

    $segment->idSegment = $this->segment->idSegment;
    $segment->idSubaccount = $this->segment->idSubaccount;
    $segment->name = $this->segment->name;
    $segment->description = $this->segment->description;
    $segment->conditions = $this->segment->conditions;
    $segment->updated = time();
    $segment->status = "processing";

    $filters = array();
    foreach ($this->segment->filters as $key) {
      $arr = new \stdClass();
      $arr->idCustomfield = $key->idCustomfield;
      $arr->type = $key->type;
      $arr->conditions = $key->conditions;
      $arr->value = ((isset($key->value) ? $key->value : $key->value2));
      array_push($filters, $arr);
    }
    $segment->filters = $filters;

    $contactlist = array();
    foreach ($this->segment->contactlist as $key) {
      $arr = new \stdClass();
      $arr->idContactlist = $key->idContactlist;
      $arr->name = $key->name;
      array_push($contactlist, $arr);
    }
    $segment->contactlist = $contactlist;

    if (!$segment->save()) {
      foreach ($segment->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $mongoclient = new \MongoClient();
    $collection = $mongoclient->aio->sxc;
    $res = $collection->remove(array(
        "idSegment" => $segment->idSegment
    ));

    if (!($res["ok"] == (float) 1)) {
      throw new \InvalidArgumentException("No se puedieron eliminar los contactos correspondientes al segmento, por favor contacte con soporte");
    }

    return $segment->idSegment;
  }

  public function deleteSegment($idSegment) {
    $segment = \Segment::findFirst(array(
                "conditions" => array(
                    "idSegment" => (int) $idSegment
                )
    ));

    if (!$segment) {
      throw new \InvalidArgumentException("El segmento que intenta eliminar, no existe");
    }

    $segment->deleted = time();
    if (!$segment->save()) {
      foreach ($segment->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $mongoclient = new \MongoClient();
    $collection = $mongoclient->aio->sxc;
    $res = $collection->remove(array(
        "idSegment" => $segment->idSegment
    ));

    if (!($res["ok"] == (float) 1)) {
      throw new \InvalidArgumentException("No se puedieron eliminar los contactos correspondientes al segmento, por favor contacte con soporte");
    }

    return ["message" => "El segmento ha sido eliminado exitosamente"];
  }

  private function wc() {
    return new \MongoDB\Driver\WriteConcern(
            \MongoDB\Driver\WriteConcern::MAJORITY, 1000
    );
  }

  public function addSegment($datas) {
    $segment = new \Segment();
    $arr = array();
    $arrContactlist = array();
    $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
    $nextIdSegment = $contactmanager->autoIncrementCollection("id_segment");
    $segment->idSegment = $nextIdSegment;
    $segment->idSubaccount = $this->user->Usertype->idSubaccount;
    $segment->name = $datas->information->name;
    $segment->description = ((isset($datas->information->description)) ? $datas->information->description : "");
    $segment->conditions = $datas->information->conditions;
    $segment->deleted = (int) 0;
    foreach ($datas->filters as $key) {
      if (isset($key->customfield->idCustomfield) && isset($key->conditions) && isset($key->value)) {
        $obj = new \stdClass();

        $obj->idCustomfield = $key->customfield->idCustomfield;
        $obj->type = $key->customfield->type;
        $obj->conditions = $key->conditions;
        $value = $key->value;
        if (substr($value, 10, -1) == "T05:00:00.000") {
          $value = substr($value, 0, -14);
        }
        $obj->value = $value;
        array_push($arr, $obj);
      }
    }
    foreach ($datas->information->contactlist as $key) {
      $obj = new \stdClass();
      $obj->idContactlist = $key->idContactlist;
      $obj->name = $key->name;

      array_push($arrContactlist, $obj);
    }

    if (count($arr) == 0) {
      throw new InvalidArgumentException("Debes agregar al menos una condición valida");
    }

    $segment->filters = $arr;
    $segment->contactlist = $arrContactlist;
    $segment->status = "processing";
    $segment->deleted = (int) 0;
    if (!$segment->save()) {
      foreach ($segment->getMessages() as $message) {
        $this->trace("fail", "No se logro crear el smslote {$message}");
        throw new \InvalidArgumentException("No se logró crear el smslote {$message}");
      }
    }

    return $segment->idSegment;
  }

}
