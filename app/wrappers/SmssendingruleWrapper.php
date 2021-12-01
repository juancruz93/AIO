<?php

namespace Sigmamovil\Wrapper;

class SmssendingruleWrapper extends \BaseWrapper {

  private $form;

  public function listSmsSendingRule($page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filter = new \Phalcon\Filter;

    $filName = ((isset($name)) ? "AND name LIKE '%{$filter->sanitize($name, "string")}%'" : ''); //Filtro por nombre

    $conditions = array(
        "conditions" => "deleted = ?0 {$filName}",
        "bind" => array(0),
        "order" => "idSmsSendingRule DESC",
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page
    );

    $smssendingrule = \SmsSendingRule::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \SmsSendingRule::count($conditions);

    $data = array();
    if (count($smssendingrule) > 0) {
      foreach ($smssendingrule as $key => $value) {
        $data[$key] = array(
            "idSmsSendingRule" => $value->idSmsSendingRule,
            "name" => $value->name,
            "description" => $value->description,
            "country" => $value->Country->name,
            "indicative" => $value->Country->phoneCode,
            "status" => $value->status,
            "created" => date("Y-m-d H:i:s", $value->created),
            "updated" => date("Y-m-d H:i:s", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $data
    );
  }

//  public function listfullindicative() {
//    $indicative = \Indicative::find();
//
//    $data = [];
//    if (count($indicative) > 0) {
//      foreach ($indicative as $key => $value) {
//        $data[$key] = array(
//            "idIndicative" => $value->idIndicative,
//            "name" => "(+{$value->phonecode}) {$value->name}"
//        );
//      }
//    }
//
//    return $data;
//  }

  public function createSmsSendingRule($data) {
    $smssendingrule = new \SmsSendingRule();
    $this->form = new \SmssendingruleForm();
    $this->form->bind($data, $smssendingrule);

    if (!isset($data["idCountry"])) {
      throw new \InvalidArgumentException("Debe seleccionar un país para la regla de envío de SMS");
    }

    $country = \Country::findFirst(array(
                "columns" => "idCountry, phoneCode",
                "conditions" => "idCountry = ?0",
                "bind" => array($smssendingrule->idCountry)
    ));
    
    $smssendingrule->idCountry = $country->idCountry;
    
    $ssr = \SmsSendingRule::findFirst(array(//ssr abreviado para SmsSendingRule
                "conditions" => "deleted = ?0 AND status = ?1 AND name = ?2",
                "bind" => array(0, 1, $smssendingrule->name)
    ));

    if ($ssr) {
      throw new \InvalidArgumentException("La regla que intenta guardar ya existe");
    }

    if (!$this->form->isValid() || !$smssendingrule->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($smssendingrule->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    if (empty($data["forms"]) || !isset($data["forms"])) {
      throw new \InvalidArgumentException("No ha enviado datos de la configuración de la regla de envío de SMS <b>{$smssendingrule->name}</b>, por favor complete los datos");
    } elseif (is_array($data["forms"])) {
      $this->saveSsrxadapter($data["forms"], $smssendingrule);
      $this->db->commit();
    }
    return ["message" => "La regla <b>{$smssendingrule->name}</b> ha sido guardada exitosamente"];
  }

  public function showsmssendingrule($id) {
    $smssendingrule = \SmsSendingRule::findFirst(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($id)
    ));

    if (!$smssendingrule) {
      throw new \InvalidArgumentException("La regla de envío de SMS a la que intenta ver su detalle no existe");
    }

    $ssrxadapter = \Ssrxadapter::find(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($smssendingrule->idSmsSendingRule)
    ));

    $ssrxa = [];
    if (count($ssrxadapter) > 0) {
      foreach ($ssrxadapter as $key => $value) {
        $ssrxa[$key] = array(
            "idAdapter" => $value->idAdapter,
            "adapter" => $value->Adapter->fname,
            "byDefault" => ((int) $value->byDefault === 1 ? true : false),
            "prefix" => (empty($value->prefix) ? '' : explode(",", $value->prefix)),
            "disabled" => (!isset($value->idIndicative) ? true : false),
            "prefixDisabled" => ((int) $value->byDefault === 1 ? true : false)
        );
      }
    }

    return array(
        "idSmsSendingRule" => $smssendingrule->idSmsSendingRule,
        "idCountry" => $smssendingrule->idCountry,
        "indicative" => $smssendingrule->Country->phoneCode,
        "country" => $smssendingrule->Country->name,
        "name" => $smssendingrule->name,
        "description" => $smssendingrule->description,
        "status" => $smssendingrule->status,
        "config" => $ssrxa
    );
  }

  public function editsmssendingrule($data) {
    $smssendingrule = \SmsSendingRule::findFirst(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($data["idSmsSendingRule"])
    ));

    if (!$smssendingrule) {
      throw new \InvalidArgumentException("La regla de envío de SMS que intenta editar, no existe");
    }

    $this->form = new \SmssendingruleForm();
    $this->form->bind($data, $smssendingrule);

    if (!isset($data["idCountry"])) {
      throw new \InvalidArgumentException("Debe seleccionar un país para la regla de envío de SMS");
    }

    $smssendingrule->idCountry = $data["idCountry"];

    if (!$this->form->isValid() || !$smssendingrule->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($smssendingrule->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $ssrxadapter = \Ssrxadapter::find(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($smssendingrule->idSmsSendingRule)
    ));

    if (count($ssrxadapter) > 0) {
      foreach ($ssrxadapter as $value) {
        if (!$value->delete()) {
          foreach ($ssrxadapter->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    if (!is_array($data["forms"])) {
      throw new \InvalidArgumentException("No ha enviado datos de la configuración de la regla de envío de SMS <b>{$smssendingrule->name}</b>, por favor complete los datos");
    }

    $this->saveSsrxadapter($data["forms"], $smssendingrule);
    $this->db->commit();

    return ["message" => "La regla <b>{$smssendingrule->name}</b> ha sido actualizada exitosamente"];
  }

  public function deletesmssendingrule($id) {
    $smssendingrule = \SmsSendingRule::findFirst(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($id)
    ));

    if (!$smssendingrule) {
      throw new \InvalidArgumentException("La regla de envío de SMS que intenta eliminar, no existe");
    }

    $smssendingrule->deleted = time();
    $name = $smssendingrule->name;

    if (!$smssendingrule->update()) {
      foreach ($smssendingrule->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La regla <b>{$name}</b> ha sido eliminada exitosamente"];
  }

  public function listAllSmsSendingRule() {
    $conditions = array(
        "conditions" => "deleted = 0 AND status = 1",
    );

    $smssendingrule = \SmsSendingRule::find($conditions);

    $data = array();
    if (count($smssendingrule) > 0) {
      foreach ($smssendingrule as $key => $value) {
        $data[$key] = array(
            "idSmsSendingRule" => $value->idSmsSendingRule,
            "name" => $value->name,
        );
      }
    }

    return array("smsSendingRule" => $data);
  }

  public function saveSsrxadapter($configs, $smssendingrule) {
    foreach ($configs as $key => $value) {
      $value = (object) $value;
      if (empty($value->idAdapter)) {
        throw new \InvalidArgumentException("Debe seleccionar el adaptador en la configuración <b><i>" . ($key + 1) . "</i></b>");
      }
      if (!$value->byDefault) {
        if (empty($value->prefix) || !is_array($value->prefix)) {
          throw new \InvalidArgumentException("Debe escribir los prefijos de la configuración <b><i>" . ($key + 1) . "</i></b>");
        }
      }

      $ssrxadapter = new \Ssrxadapter();
      $ssrxadapter->idSmsSendingRule = $smssendingrule->idSmsSendingRule;
      $ssrxadapter->idAdapter = $value->idAdapter;
      $ssrxadapter->byDefault = $value->byDefault;
      $prefixes = [];
      foreach ($value->prefix as $val) {
        $val = (int) $val;
        if (is_int($val) && (strlen($val) > 0) && $val > 0) {
          array_push($prefixes, $val);
        } else {
          throw new \InvalidArgumentException("Los prefijos deben ser de tipo númerico entero, debe tener al menos un digito y ser mayor a cero");
        }
      }
      $ssrxadapter->prefix = implode(",", $prefixes);
      if (!$ssrxadapter->save()) {
        foreach ($ssrxadapter->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

}
