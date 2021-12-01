<?php

namespace Sigmamovil\Wrapper;

class MtaxipWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listMtaxip($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
   
        
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
        "conditions" => "deleted = ?0 {$where}",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "idMta DESC"
    );

    $mtaxip = \Mtaxip::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\Mtaxip::find($conditions));

    $data = array();
    if (count($mtaxip) > 0) {
      foreach ($mtaxip as $key => $value) {
        $data[$key] = array(
            "idMtaxip" => $value->idMtaxip,
            "nameMta" => $value->mta->name,
            "idMta" => $value->mta->idMta,
            "nameIp" => $value->ip->ip,
            "created" => date("Y-m-d H:m", $value->created),
            "updated" => date("Y-m-d H:m", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }

    $datareal = array();
    $ipdata = array();


    foreach ($data as $key => $value) {
      if ($idMtaConta != $value["idMta"]) {
        $datareal[$key] = array(
            "idMtaxip" => $value["idMtaxip"],
            "nameMta" => $value["nameMta"],
            "idMta" => $value["idMta"],
            "nameIp" => $ipdata,
            "created" => $value['created'],
            "updated" => $value['updated'],
            "createdBy" => $value['createdBy'],
            "updatedBy" => $value['updatedBy']
        );
      }
      $idMtaConta = $value["idMta"];
    }

    $baseEnd = array();
    foreach ($datareal as $key => $value) {


      foreach ($data as $val) {

        if ($val['idMta'] == $value["idMta"]) {
          array_push($value['nameIp'], $val['nameIp']);
        }
      }

      $baseEnd[$key] = $value;
    }

    $total = count($baseEnd);

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $baseEnd
    );
  }

  /**
   * @descripcion guarda el mta por ip
   * @param Array $data
   * @return Array idIp   
   */
  public function saveMtaxip($data) {
    $mta = new \Mta();

    if (!$data->ipdta) {
      throw new \InvalidArgumentException("Debe seleccionar una dirección IP");
    }
    if (!$data->name) {
      throw new \InvalidArgumentException("Por favor ingrese un nombre MTA");
    }
    if (!$data->description) {
      $mta->description = 'Sin descripción';
    } else {
      $mta->description = $data->description;
    }
    if (!$data->observation) {
      $mta->observation = 'Sin observación';
    } else {
      $mta->observation = $data->observation;
    }

    $mta->name = ucwords($data->name);
    $mta->deleted = 0;
    $mta->createdBy = $this->user->email;
    $mta->updatedBy = $this->user->email;
    $mta->status = 1;

    if (!$mta->save()) {
      foreach ($mta->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    foreach ($data->ipdta as $val) {
      $mtaxip = new \Mtaxip();
      $mtaxip->idIp = (int) $val;
      $mtaxip->idMta = (int) $mta->idMta;
      $mtaxip->deleted = 0;

      if (!$mtaxip->save()) {
        foreach ($mtaxip->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
    }

    return $ip->idMtaxip;
  }

  /**
   * @descripcion borra una mta por ips
   * @param Array $data
   * @return bolean   
   */
  public function deleteMtaxip($data) {
    $mtaxip = \Mtaxip::find(array(
                "conditions" => "idMta = ?0",
                "bind" => array($data->id)
    ));

    if (count($mtaxip) > 0) {
      foreach ($mtaxip as $key => $value) {

        $value->deleted = 1;

        if (!$value->update()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    return true;
  }

  /**
   * @descripcion busca MTA por ip para editar
   * @param integer $id
   * @return Array $data   
   */
  public function getMtaxip($id) {
    $mtaxip = \Mtaxip::find(array(
                "conditions" => "idMta = ?0 and deleted = 0",
                "bind" => array($id)
    ));
    $datareal = array();
    $ipdata = array();

    foreach ($mtaxip as $key => $value) {

      if ($idMtaConta != $value->mta->idMta) {
        $datareal[$key] = array(
            "idMtaxip" => $value->idMtaxip,
            "name" => $value->mta->name,
            "description" => $value->mta->description,
            "observation" => $value->mta->observation,
            "idMta" => $value->mta->idMta,
            "ipdta" => $ipdata,
        );
      }
      $idMtaConta = $value->mta->idMta;
    }
    $baseEnd = array();
    foreach ($datareal as $key => $value) {
      foreach ($mtaxip as $val) {
        if ($val->mta->idMta == $value['idMta']) {
          array_push($value['ipdta'], $val->idIp);
        }
      }
      $baseEnd = $value;
    }

    return $baseEnd;
  }

  /**
   * @descripcion guarda la edicion de mta por ips
   * @param array $data
   * @return bolean   
   */
  public function editMtaxip($data) {

    if (!$data['ipdta']) {
      throw new \InvalidArgumentException("Debe seleccionar una dirección IP");
    }
    if (!$data['name']) {
      throw new \InvalidArgumentException("Por favor ingrese un nombre MTA");
    }


    $mta = \Mta::findFirst(array(
                "conditions" => "idMta = ?0",
                "bind" => array($data['idMta'])
    ));


    if (!$data['description']) {
      $mta->description = 'Sin descripción';
    } else {
      $mta->description = $data['description'];
    }
    if (!$data['observation']) {
      $mta->observation = 'Sin observación';
    } else {
      $mta->observation = $data['observation'];
    }

    $mta->name = ucwords($data['name']);

    if (!$mta->update()) {
      foreach ($mta->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $mtaxip = \Mtaxip::find(array(
                "conditions" => "idMta = ?0",
                "bind" => array($data['idMta'])
    ));

    if (count($mtaxip) > 0) {
      foreach ($mtaxip as $key => $value) {

        $value->deleted = 1;

        if (!$value->update()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    foreach ($data['ipdta'] as $val) {
      $mtaxip2 = new \Mtaxip();
      $mtaxip2->idIp = (int) $val;
      $mtaxip2->idMta = (int) $data['idMta'];
      $mtaxip2->deleted = 0;

      if (!$mtaxip2->save()) {
        foreach ($mtaxip2->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
    }

    return true;
  }

  /**
   * @descripcion trae todas las ip
   * @param integer $id
   * @return Array $data   
   */
  public function getIpmta() {

    $conditions = array(
        "conditions" => "deleted = ?0 AND status = 1",
        "bind" => array(0)
    );

    $ip = \Ip::find($conditions);
    $data = array();
    if (count($ip) > 0) {
      foreach ($ip as $key => $value) {
        $data[$key] = array(
            "idIp" => $value->idIp,
            "name" => $value->ip
        );
      }
    }
    return $data;
  }

}
