<?php

namespace Sigmamovil\General\Misc;

class ContactManager
{

  public function autoIncrementCollection($field) {
    $autoincrementcollection = \Autoincrementcollection::findFirst(array(
        "conditions" => array(
            "field_id" => (string) $field
        )
    ));
    $autoincrementcollection->nextId +=1;
    $autoincrementcollection->save();
    return $autoincrementcollection->nextId - 1;
  }

  public function getIdcustomField($alternativename) {
    $customfield = \Customfield::findFirst(["conditions" => "alternativename = ?0", "bind" => [0 => $alternativename]]);
    if ($customfield) {
      return $customfield->idCustomfield;
    } else {
      return $alternativename;
    }
  }

  public function getNameCustomField($idCustomfield) {
    $customfield = \Customfield::findFirst(["conditions" => "idCustomfield = ?0", "bind" => [0 => $idCustomfield]]);
    if ($customfield) {
      return $customfield->name;
    } else {
      return $idCustomfield;
    }
  }

  public function getTypeCustomField($idCustomfield) {
    $customfield = \Customfield::findFirst(["conditions" => "idCustomfield = ?0", "bind" => [0 => $idCustomfield]]);
    if ($customfield) {
//      $arr = ['name' => $customfield->name, 'type' => $customfield->type];
      return $customfield->type;
    } else {
      return $idCustomfield;
    }
  }

  public function getIdCustomFieldByIdCustomfield($idCustomfield) {
    $customfield = \Customfield::findFirst(["conditions" => "idCustomfield = ?0", "bind" => [0 => $idCustomfield]]);
    if ($customfield) {
      return $customfield->idCustomfield;
    } else {
      return $idCustomfield;
    }
  }

}
