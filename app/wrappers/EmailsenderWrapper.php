<?php

namespace Sigmamovil\Wrapper;

class EmailsenderWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listEmailsender($page, $filter) {
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

    $emailsender = \Emailsender::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\Emailsender::find($conditions));

    $data = array();
    if (count($emailsender) > 0) {
      foreach ($emailsender as $key => $value) {
        $data[$key] = array(
            "idEmailsender" => $value->idEmailsender,
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
   * @descripcion guarda un correo de remitente
   * @param Array $data
   * @return Array idemailsender  
   */
  public function saveEmailsender($data) {
    $emailsender = new \Emailsender();
    $emailsender->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null));
    $emailsender->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : Null);

    $emailsender->email = ucwords($data->email);

    if (!empty($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido");
    }

    if (isset($data->status)) {
      $emailsender->status = $data->status;
    } else {
      $emailsender->status = 0;
    }

    $emailsender->deleted = 0;
    $emailsender->createdBy = $this->user->email;
    $emailsender->updatedBy = $this->user->email;

    if (!$emailsender->save()) {
      foreach ($emailsender->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $emailsender->idEmailsender;
  }

  /**
   * @descripcion borra un correo de remitente
   * @param Array $data
   * @return bolean   
   */
  public function deleteEmailsender($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato de correo de remitente inválido");
    }

    $emailsender = \Emailsender::findFirst(array(
                "conditions" => "idEmailsender = ?0",
                "bind" => array($data->id)
    ));

    if (!$emailsender) {
      throw new \InvalidArgumentException("El correo de remitente que intenta editar no existe");
    }

    $emailsender->deleted = 1;

    if (!$emailsender->update()) {
      foreach ($emailsender->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @descripcion busca un correo de remitente para editarlo
   * @param integer $id
   * @return Array $data   
   */
  public function getEmailsender($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato inválido");
    }

    $emailsender = \Emailsender::findFirst(array(
                "conditions" => "idEmailsender = ?0",
                "bind" => array($id)
    ));

    if (!$emailsender) {
      throw new \InvalidArgumentException("El correo de remitente que intenta editar no existe");
    }

    $data = array(
        "idEmailsender" => $emailsender->idEmailsender,
        "email" => $emailsender->email,
        "status" => $emailsender->status
    );

    return $data;
  }

  /**
   * @descripcion guarda la edicion de un correo de remitente
   * @param array $data
   * @return bolean   
   */
  public function editEmailsender($data) {

    if (!isset($data["idEmailsender"])) {
      throw new \InvalidArgumentException("Dato de correo de remitente inválido");
    }

    if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("Formato de correo invalido");
    }

    $emailsender = \Emailsender::findFirst(array(
                "conditions" => "idEmailsender = ?0",
                "bind" => array($data["idEmailsender"])
    ));

    if (!$emailsender) {
      throw new \InvalidArgumentException("El correo de remitente que intenta editar no existe");
    }

    $emailsender->email = ucwords($data['email']);

    if (isset($data['status'])) {
      $emailsender->status = $data['status'];
    } else {
      $emailsender->status = 0;
    }

    if (!$emailsender->update()) {
      foreach ($emailsender->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
