<?php

namespace Sigmamovil\Wrapper;

class NamesenderWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listNamesender($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $name = (isset($filter->name) ? "AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');


    if ((isset($filter->dateinitial) && !empty($filter->dateinitial)) && (isset($filter->dateend) && !empty($filter->dateend))) {

      $startDate = strtotime($filter->dateinitial);

      $finalDate = strtotime($filter->dateend);

      if ($startDate > $finalDate) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a final');
      }
      if ($filter->dateinitial > date('Y-m-d')) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
      }

      $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
    }

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$name} {$where}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $namesender = \NameSender::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\NameSender::find($conditions));

    $data = array();
    if (count($namesender) > 0) {
      foreach ($namesender as $key => $value) {
        $data[$key] = array(
            "idNameSender" => $value->idNameSender,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "created" => date("Y-m-d H:m", $value->created),
            "updated" => date("Y-m-d H:m", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "status" => $value->status
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  /**
   * @descripcion guarda un nombre de remitente
   * @param Array $data
   * @return Array id namesender   
   */
  public function saveNamesender($data) {
    $namesender = new \NameSender();
    $namesender->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null));
    $namesender->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : Null);

    $namesender->name = ucwords($data->name);

    if (isset($data->status)) {
      $namesender->status = $data->status;
    } else {
      $namesender->status = 0;
    }

    $namesender->deleted = 0;
    $namesender->createdBy = $this->user->email;
    $namesender->updatedBy = $this->user->email;

    if (!$namesender->save()) {
      foreach ($namesender->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $namesender->idNameSender;
  }

  /**
   * @descripcion borra un nombre de remitente
   * @param Array $data
   * @return bolean   
   */
  public function deletenamesender($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato de nombre de remitente inválido");
    }

    $namesender = \NameSender::findFirst(array(
                "conditions" => "idNameSender = ?0",
                "bind" => array($data->id)
    ));

    if (!$namesender) {
      throw new \InvalidArgumentException("El nombre de remitente que intenta editar no existe");
    }

    $namesender->deleted = 1;

    if (!$namesender->update()) {
      foreach ($namesender->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @descripcion busca un nombre remitente para editarlo
   * @param integer $id
   * @return Array $data   
   */
  public function getnamesender($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    $namesender = \NameSender::findFirst(array(
                "conditions" => "idNameSender = ?0",
                "bind" => array($id)
    ));

    if (!$namesender) {
      throw new \InvalidArgumentException("El nombre que intenta editar no existe");
    }

    $data = array(
        "idNameSender" => $namesender->idNameSender,
        "name" => $namesender->name,
        "status" => $namesender->status
    );

    return $data;
  }

  /**
   * @descripcion guarda la edicion de un nombre de remitente
   * @param array $data
   * @return bolean   
   */
  public function editnamesender($data) {

    if (!isset($data["idNameSender"])) {
      throw new \InvalidArgumentException("Dato de nombre de remitente inválido");
    }

    $namesender = \NameSender::findFirst(array(
                "conditions" => "idNameSender = ?0",
                "bind" => array($data["idNameSender"])
    ));

    if (!$namesender) {
      throw new \InvalidArgumentException("El nombre de remitente que intenta editar no existe");
    }

    $namesender->name = ucwords($data['name']);

    if (isset($data['status'])) {
      $namesender->status = $data['status'];
    } else {
      $namesender->status = 0;
    }

    if (!$namesender->update()) {
      foreach ($namesender->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
