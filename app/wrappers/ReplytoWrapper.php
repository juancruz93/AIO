<?php

namespace Sigmamovil\Wrapper;

class ReplytoWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listReplyto($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $email = (isset($filter->email) ? "AND email like '%{$sanitize->sanitize($filter->email, "string")}%'" : '');


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
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$email} {$where}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $replyto = \ReplyTos::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\ReplyTos::find($conditions));

    $data = array();
    if (count($replyto) > 0) {
      foreach ($replyto as $key => $value) {
        $data[$key] = array(
            "idReplyTo" => $value->idReplyTo,
            "idAccount" => $value->idAccount,
            "email" => $value->email,
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
   * @descripcion guarda un correo de respuesta
   * @param Array $data
   * @return Array idReplyTo   
   */
  public function saveReplyto($data) {
    $replyto = new \ReplyTos();
    $replyto->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null));
    $replyto->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : Null);

    $replyto->email = ucwords($data->email);

    if (!empty($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido");
    }

    if (isset($data->status)) {
      $replyto->status = $data->status;
    } else {
      $replyto->status = 0;
    }

    $replyto->deleted = 0;
    $replyto->createdBy = $this->user->email;
    $replyto->updatedBy = $this->user->email;

    if (!$replyto->save()) {
      foreach ($replyto->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $replyto->idReplyTo;
  }

  /**
   * @descripcion borra un correo de respuesta
   * @param Array $data
   * @return bolean   
   */
  public function deletereplyto($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato de correo de respuesta inválido");
    }

    $replyto = \ReplyTos::findFirst(array(
                "conditions" => "idReplyTo = ?0",
                "bind" => array($data->id)
    ));

    if (!$replyto) {
      throw new \InvalidArgumentException("El correo de respuesta que intenta editar no existe");
    }

    $replyto->deleted = 1;

    if (!$replyto->update()) {
      foreach ($replyto->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @descripcion busca un correo de respuesta para editarlo
   * @param integer $id
   * @return Array $data   
   */
  public function getreplyto($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    $replyto = \ReplyTos::findFirst(array(
                "conditions" => "idReplyTo = ?0",
                "bind" => array($id)
    ));

    if (!$replyto) {
      throw new \InvalidArgumentException("El correo de respuesta que intenta editar no existe");
    }

    $data = array(
        "idReplyTo" => $replyto->idReplyTo,
        "email" => $replyto->email,
        "status" => $replyto->status
    );

    return $data;
  }

  /**
   * @descripcion guarda la edicion de un correo de respuesta
   * @param array $data
   * @return bolean   
   */
  public function editreplyto($data) {

    if (!isset($data["idReplyTo"])) {
      throw new \InvalidArgumentException("Dato de correo de respuesta inválido");
    }

    if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido");
    }

    $replyto = \ReplyTos::findFirst(array(
                "conditions" => "idReplyTo = ?0",
                "bind" => array($data["idReplyTo"])
    ));

    if (!$replyto) {
      throw new \InvalidArgumentException("El correo de respuesta que intenta editar no existe");
    }

    $replyto->email = ucwords($data['email']);

    if (isset($data['status'])) {
      $replyto->status = $data['status'];
    } else {
      $replyto->status = 0;
    }

    if (!$replyto->update()) {
      foreach ($replyto->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
