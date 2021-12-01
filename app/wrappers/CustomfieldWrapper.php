<?php

namespace Sigmamovil\Wrapper;

class CustomfieldWrapper extends \BaseWrapper {

  private $customfield;
  private $customfields = array();
  private $totals;

  public function setCustomfield(\Customfield $customfield) {
    $this->customfield = $customfield;
  }

  public function modelCustomfield() {
    $data = $this->customfield;
    $this->customfield = array();
    $this->customfield['idCustomfield'] = $data->idCustomfield;
    $this->customfield['idContactlist'] = $data->idContactlist;
    $this->customfield['name'] = $data->name;
    $this->customfield['alternativename'] = $data->alternativename;
    $this->customfield['defaultvalue'] = $data->defaultvalue;
    $this->customfield['type'] = $data->type;
    $this->customfield['value'] = $data->value;
    $this->customfield['created'] = $data->created;
    $this->customfield['updated'] = $data->updated;
  }

  public function getCustomfield() {
    return $this->customfield;
  }

  public function getCustomfields() {
    return $this->customfields;
  }

  public function editCustomfield() {
    $string = new \Sigmamovil\General\Misc\CleanString();
    $this->customfield->idCustomfield = $this->data->idCustomfield;
    
    if (empty($this->data->name)) {
      throw new \InvalidArgumentException("El campo nombre es obligatorio");
    }
 
    if (empty($this->data->type)) {
      throw new \InvalidArgumentException("El campo tipo de formato es obligatorio");
    }

    if ($this->data->type == 'Select' || $this->data->type == 'Multiselect') {
      if (empty($this->data->value)) {
        throw new \InvalidArgumentException("El campo valor es obligatorio si es por formato de selección");
      }
    }

    $this->customfield->name = $this->data->name;
    $this->customfield->defaultvalue = $this->data->defaultvalue;
    $this->customfield->type = $this->data->type;
//    $this->customfield->value = $this->data->value;
    $this->customfield->value = implode(",", $this->data->value);
    $this->customfield->alternativename = $string->clear($this->data->name);

    if (!$this->customfield->save()) {
      foreach ($this->customfield->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $bulk = new \MongoDB\Driver\BulkWrite;

    $cxcl = \Cxcl::find(["conditions" => "idContactlist = ?0", "bind" => [0 => $this->customfield->idContactlist]]);
    if (count($cxcl) >= 1) {
      $in = array();
      for ($i = 0; $i < count($cxcl); $i++) {
        $in[$i] = (int) $cxcl[$i]->idContact;
      };
      $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      $bulk->update(['idContact' => ['$in' => $in]], ['$set' => ['idContactlist.' . $this->customfield->idContactlist .
              "." . $this->customfield->idCustomfield . ".name" => $this->data->name, 'idContactlist.' . $this->customfield->idContactlist .
              "." . $this->customfield->idCustomfield . ".type" => $this->data->type]
              ], ['multi' => true]);
      $manager->executeBulkWrite('aio.cxc', $bulk, $writeConcern);
    }
  }

  public function deleteCustomfield() {
    $this->customfield->deleted = time();
    if (!$this->customfield->update()) {
      foreach ($this->customfield->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo eliminar el campo personalizado, es posible que tenga una relacion activa, contacta al administrador para solicitar más información");
      }
    }
  }

  public function findCustomfield($idContactslist, $page) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1 ) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }
//    $this->data = \Customfield::find(array(
//          "conditions" => "idContactlist = ?0",
//          "bind" => array(0 => $idContactslist)
//    ));
//    var_dump($this->data );
//    exit();
    $this->data = $this->modelsManager->createBuilder()
            ->from('Customfield')
            ->where("Customfield.deleted = 0 AND Customfield.idContactlist  = {$idContactslist}" .
                    " LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET {$page}")
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Customfield')
            ->where("Customfield.deleted = 0 AND Customfield.idContactlist  = {$idContactslist}")
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function modelData() {
    $this->customfields = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $customfield = new \stdClass();
      $customfield->idCustomfield = $data->idCustomfield;
      $customfield->idContactlist = $data->idContactlist;
      $customfield->name = $data->name;
      $customfield->alternativename = $data->alternativename;
      if ($data->defaultvalue == null or $data->defaultvalue == "") {
        $customfield->defaultvalue = 'Sin valor por defecto';
      } else {
        $customfield->defaultvalue = 'Valor por defecto: ' . $data->defaultvalue;
      }

      $customfield->type = $data->type;
      $customfield->value = $data->value;
      $customfield->created = date("d/m/Y H:i:sa", $data->created);
      $customfield->updated = date("d/m/Y H:i:sa", $data->updated);

      array_push($arr, $customfield);
    }
    $contaslit = \Contactlist::findFirst([
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $customfield->idContactlist)]);
    array_push($this->customfields, array("nameContactlist" => $contaslit->name));
    array_push($this->customfields, array("items" => $arr));
  }

}
