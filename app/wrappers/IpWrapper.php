<?php

namespace Sigmamovil\Wrapper;

class IpWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listIp($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $ip = (isset($filter->ip) ? "AND ip like '%{$sanitize->sanitize($filter->ip, "string")}%'" : '');


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
        "conditions" => "deleted = ?0 {$ip} {$where}",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $ip = \Ip::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\Ip::find($conditions));

    $data = array();
    if (count($ip) > 0) {
      foreach ($ip as $key => $value) {
        $data[$key] = array(
            "idIp" => $value->idIp,
            "ip" => $value->ip,
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
   * @descripcion guarda una direccion IP
   * @param Array $data
   * @return Array idIp   
   */
  public function saveIp($data) {
    $ip = new \Ip();

    if (!empty($data->ip) && !filter_var($data->ip, FILTER_VALIDATE_IP)) {
      throw new \InvalidArgumentException("Esta dirección IP " . $data->ip . " no es válida.");
    }


    $ip->ip = ucwords($data->ip);

    if (isset($data->status)) {
      $ip->status = $data->status;
    } else {
      $ip->status = 0;
    }

    $ip->deleted = 0;
    $ip->createdBy = $this->user->email;
    $ip->updatedBy = $this->user->email;

    if (!$ip->save()) {
      foreach ($ip->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $ip->idIp;
  }

  /**
   * @descripcion borra una direccion ip
   * @param Array $data
   * @return bolean   
   */
  public function deleteIp($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    $ip = \Ip::findFirst(array(
                "conditions" => "idIp = ?0",
                "bind" => array($data->id)
    ));

    if (!$ip) {
      throw new \InvalidArgumentException("La dirección IP que intenta editar no existe");
    }

    $ip->deleted = 1;

    if (!$ip->update()) {
      foreach ($ip->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @descripcion busca una direccion IP para editar
   * @param integer $id
   * @return Array $data   
   */
  public function getIp($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    $ip = \Ip::findFirst(array(
                "conditions" => "idIp = ?0",
                "bind" => array($id)
    ));

    if (!$ip) {
      throw new \InvalidArgumentException("La direccción IP que intenta editar no existe");
    }

    $data = array(
        "idIp" => $ip->idIp,
        "ip" => $ip->ip,
        "status" => $ip->status
    );

    return $data;
  }

  /**
   * @descripcion guarda la edicion de una direccion IP
   * @param array $data
   * @return bolean   
   */
  public function editIp($data) {

    if (!isset($data["idIp"])) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    if (!empty($data["ip"]) && !filter_var($data["ip"], FILTER_VALIDATE_IP)) {
      throw new \InvalidArgumentException("Esta dirección IP " . $data["ip"] . " no es válida.");
    }

    $ip = \Ip::findFirst(array(
                "conditions" => "idIp = ?0",
                "bind" => array($data["idIp"])
    ));

    if (!$ip) {
      throw new \InvalidArgumentException("La dirección IP que intenta editar no existe");
    }

    $ip->ip = ucwords($data['ip']);

    if (isset($data['status'])) {
      $ip->status = $data['status'];
    } else {
      $ip->status = 0;
    }

    if (!$ip->update()) {
      foreach ($ip->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
