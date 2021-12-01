<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

use Sigmamovil\General\Misc\Accounting;

/**
 * Description of AccountingWrapper
 *
 * @author juan.pinzon
 */
class AccountingWrapper extends \BaseWrapper {

  public function listAccounts($page) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $accounting = new Accounting();
    $idAllied = $this->user->Usertype->Allied->idAllied;
    
    $total = \Account::count(array(
                "columns" => "idAccount",
                "conditions" => "idAllied = ?0 AND status = ?1 AND deleted = ?2",
                "bind" => array($idAllied, 1, 0)
    ));
    $this->logger->log($accounting->accountingAccounts($idAllied, $accounting->lastMonth(time()), time(), $page));
    $resultAccounting = $this->db->fetchAll($accounting->accountingAccounts($idAllied, $accounting->lastMonth(time()), time(), $page));

    $data = [];
    if (count($resultAccounting) > 0) {
      foreach ($resultAccounting as $key => $value) {
        $data[$key] = array(
            "idAccount" => $value["idAccount"],
            "name" => $value["name"],
            "lastTotalSmsSent" => $value["lastTotalSmsSent"],
            "currentTotalSmsSent" => $value["currentTotalSmsSent"],
            "lastTotalMailSent" => $value["lastTotalMailSent"],
            "currentTotalMailSent" => $value["currentTotalMailSent"],
            "lastTotalContacts" => $value["lastTotalContacts"],
            "currentTotalContacts" => $value["currentTotalContacts"]
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
