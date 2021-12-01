<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\General\Misc;

/**
 * Description of Accounting
 *
 * @author juan.pinzon
 */
class Accounting {

  private $logger;
  private $db;
  private $modelsManager;

  public function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
  }

  public function accountingAccounts($idAllied, $lastPeriod, $currentPeriod, $page) {
    $sql = "SELECT "
            . "acco.idAccount, acco.name, "
            . "{$this->getSqlTotalSmsSent($this->firstDayMonth($lastPeriod), $this->lastDayMonth($lastPeriod))} AS lastTotalSmsSent, "
            . "{$this->getSqlTotalSmsSent($this->firstDayMonth($currentPeriod), $this->lastDayMonth($currentPeriod))} AS currentTotalSmsSent, "
            . "{$this->getSqlTotalMailsSent($this->firstDayMonth($lastPeriod), $this->lastDayMonth($lastPeriod))} AS lastTotalMailSent, "
            . "{$this->getSqlTotalMailsSent($this->firstDayMonth($currentPeriod), $this->lastDayMonth($currentPeriod))} AS currentTotalMailSent, "
            . "{$this->getSqlTotalContactsHistory($this->lastDayMonthTimestamp($lastPeriod))} AS lastTotalContacts, "
            . "{$this->getSqlTotalContacts($this->firstDayMonthTimestamp($currentPeriod), $this->lastDayMonthTimestamp($currentPeriod))} AS currentTotalContacts "
            . "FROM account AS acco "
            . "WHERE acco.idAllied = {$idAllied} "
            . "AND acco.status = 1 "
            . "AND acco.deleted = 0 "
            . "LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " "
            . "OFFSET {$page}";

    return $sql;
  }

  public function getSqlTotalContacts($date1, $date2) {
    $sql = "(SELECT "
            . "COUNT(DISTINCT cxcl.idContact) AS totalContacts "
            . "FROM cxcl AS cxcl "
            . "INNER JOIN contactlist AS cl "
            . "ON cxcl.idContactlist = cl.idContactlist "
            . "INNER JOIN subaccount AS sub "
            . "ON cl.idSubaccount = sub.idSubaccount "
            . "INNER JOIN account acc "
            . "ON sub.idAccount = acc.idAccount "
            . "WHERE cxcl.deleted = 0 "
            . "AND (cxcl.active > 0 OR cxcl.unsubscribed > 0) "
            . "AND acc.idAccount = acco.idAccount "
            . "AND cxcl.created BETWEEN '{$date1}' "
            . "AND '{$date2}')";

    return $sql;
  }

  public function getSqlTotalContactsHistory($date2) {
    $sql = "(SELECT "
            . "COUNT(DISTINCT cxcl.idContact) AS totalContacts "
            . "FROM cxcl AS cxcl "
            . "INNER JOIN contactlist AS cl "
            . "ON cxcl.idContactlist = cl.idContactlist "
            . "INNER JOIN subaccount AS sub "
            . "ON cl.idSubaccount = sub.idSubaccount "
            . "INNER JOIN account acc "
            . "ON sub.idAccount = acc.idAccount "
            . "WHERE cxcl.deleted = 0 "
            . "AND (cxcl.active > 0 OR cxcl.unsubscribed > 0) "
            . "AND acc.idAccount = acco.idAccount "
            . "AND (cxcl.created <= '{$date2}' OR cxcl.created IS NULL)) ";

    return $sql;
  }

  public function getSqlTotalMailsSent($date1, $date2) {
    $sql = "(SELECT "
            . "IFNULL(SUM(mail.messagesSent), 0) AS totalMailsSent "
            . "FROM mail AS mail "
            . "INNER JOIN subaccount AS sub "
            . "ON mail.idSubaccount = sub.idSubaccount "
            . "INNER JOIN account AS acc "
            . "ON sub.idAccount = acc.idAccount "
            . "WHERE acc. idAccount = acco.idAccount "
            . "AND mail.status = 'sent' "
            . "AND DATE_FORMAT(mail.scheduleDate, '%Y-%m-%d') BETWEEN '{$date1}' "
            . "AND '{$date2}')";

    return $sql;
  }

  public function getSqlTotalSmsSent($date1, $date2) {
    $sql = "(SELECT "
            . "IFNULL(SUM(sent), 0) AS totalSmsSent "
            . "FROM sms AS sms "
            . "INNER JOIN subaccount AS sub "
            . "ON sms.idSubaccount = sub.idSubaccount "
            . "INNER JOIN account AS acc "
            . "ON sub.idAccount = acc.idAccount "
            . "WHERE acc.idAccount = acco.idAccount "
            . "AND sms.status IN('sent', 'paused', 'canceled') "
            . "AND DATE_FORMAT(sms.startdate, '%Y-%m-%d') BETWEEN '{$date1}' "
            . "AND '{$date2}')";

    return $sql;
  }

  public function firstDayMonth($date) {
    $month = date('m', $date);
    $year = date('Y', $date);

    return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
  }

  public function firstDayMonthTimestamp($date) {
    $month = date('m', $date);
    $year = date('Y', $date);

    return mktime(0, 0, 0, $month, 1, $year);
  }

  public function lastDayMonth($date) {
    $month = date('m', $date);
    $year = date('Y', $date);
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
  }

  public function lastDayMonthTimestamp($date) {
    $month = date('m', $date);
    $year = date('Y', $date);
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return mktime(23, 59, 59, $month, $day, $year);
  }

  public function lastMonth($date) {
    return mktime(0, 0, 0, date("m", $date) - 1, 1, date("Y", $date));
  }

}
