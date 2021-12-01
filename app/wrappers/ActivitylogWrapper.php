<?php

namespace Sigmamovil\Wrapper;

class ActivitylogWrapper extends \BaseWrapper {

  public function listActivityLog($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filtro = new \Phalcon\Filter;

    $email = (isset($filter->email) ? "AND us.email LIKE '%{$filtro->sanitize($filter->email, "string")}%'" : '');
    $services = (isset($filter->idServices) ? ($filtro->sanitize($filter->idServices, "int") == 0 ? '' : "AND ac.idservices = {$filtro->sanitize($filter->idServices, "int")}") : '');
    $startDate = (isset($filter->startDate) || $filter->startDate != "" ? $filtro->sanitize($filter->startDate, "string") : NULL);
    $endDate = (isset($filter->endDate) || $filter->endDate != "" ? $filtro->sanitize($filter->endDate, "string") : NULL);
    if (isset($startDate) && $filter->startDate != "" || isset($endDate) && $filter->endDate != "") {
      if ($startDate > $endDate) {
        throw new \InvalidArgumentException("La fecha fin no puede ser anterior a la fecha inicial del filtro por fecha");
      }
      $condDate = "AND ac.created BETWEEN " . strtotime($startDate) . " AND " . strtotime($endDate);
    } else {
      $condDate = "";
    }


    $phql = "SELECT ac.idActivityLog, us.email usemail, se.name AS sename, ac.amount, ac.dateTime, ac.description "
            . "FROM ActivityLog AS ac "
            . "INNER JOIN Services se "
            . "ON ac.idServices = se.idServices "
            . "INNER JOIN User AS us "
            . "ON ac.idUser = us.idUser "
            . "INNER JOIN Usertype AS ust "
            . "ON us.idUsertype = ust.idUsertype "
            . "WHERE 1 = 1 ";

    $roleName = strtolower($this->user->Usertype->name);
    if ($roleName == "master") {
      $idMasteraccount = $this->user->Usertype->Masteraccount->idMasteraccount;
      $phql .= "AND ust.idMasteraccount = {$filtro->sanitize($idMasteraccount, "int")} ";
    } elseif ($roleName == "allied") {
      $idAllied = $this->user->Usertype->Allied->idAllied;
      $phql .= "AND ust.idAllied = {$filtro->sanitize($idAllied, "int")} ";
    } elseif ($roleName == "account") {
      $idAccount = $this->user->Usertype->Account->idAccount;
      $phql .= "AND ust.idAccount = {$filtro->sanitize($idAccount, "int")} ";
    } else {
      $phql .= "AND ust.name = '{$filtro->sanitize("root", "string")}'";
    }

    $phql .= "{$services} {$condDate} {$email} ";
    $total = count($this->modelsManager->executeQuery($phql));

    $phql .= "LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " "
            . "OFFSET {$filtro->sanitize($page, "int")}";

    $activitylog = $this->modelsManager->executeQuery($phql);

    $data = array();
    if (count($activitylog) > 0) {
      foreach ($activitylog as $key => $value) {
        $data[$key] = array(
            "idActivityLog" => $value->idActivityLog,
            "user" => $value->usemail,
            "service" => $value->sename,
            "amount" => $value->amount,
            "dateTime" => $value->dateTime,
            "description" => $value->description
        );
      }
    }

    $array = array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );

    return $array;
  }

}
