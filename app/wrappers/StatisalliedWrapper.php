<?php

namespace Sigmamovil\Wrapper;

class StatisalliedWrapper extends \BaseWrapper {

  public function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }

  /** Ultimo dia de este mes * */
  function _data_last_month_day() {
    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
  }

  /** Primer dia de este mes * */
  function _data_first_month_day() {
    $month = date('m');
    $year = date('Y');
    return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
  }

  /** Primer dia de este mes anterior* */
  function _data_first_month_previous_day() {
    $month = date('m', strtotime('now - 1 month'));
    $year = date('Y');
    return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
  }

  /** Ultimo dia de este mes anterior* */
  function _data_last_month_previous_day() {
    $month = date('m', strtotime('now - 1 month'));
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
  }

  public function findStatisallied($page) {
    // paginador
    $datedaystart = $this->_data_first_month_day();
    $datedayend = $this->_data_last_month_day();

    $datedaystartpreviousa = $this->_data_first_month_previous_day();
    $datedaystartpreviousb = $this->_data_last_month_previous_day();

    $datepres = date('F', strtotime($datedaystart));
    $dateprev = date('F', strtotime($datedaystartpreviousa));

    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;


    if ($this->flag == 0) {
      $comple = " LIMIT {$limit} OFFSET {$page}";
    } else {
      $comple = "";      
    }

    $sql = "SELECT "
            . "ac.idAccount, ac.name, m.enviosmail, s.enviossms "
            . "FROM ( "
            . "SELECT "
            . "a.idAccount, a.name "
            . "FROM "
            . "subaccount AS s "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "group by a.name "
            . ") as ac "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(m.messagesSent) AS enviosmail "
            . "FROM "
            . "mail AS m "
            . "JOIN subaccount AS s ON s.idSubaccount = m.idSubaccount "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "m.scheduleDate >= '{$datedaystartpreviousa}' "
            . "AND m.scheduleDate < '{$datedaystartpreviousb}' AND m.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS m "
            . "ON ac.idAccount = m.idAccount "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(sm.sent) AS enviossms "
            . "FROM "
            . "sms AS sm "
            . "LEFT JOIN subaccount AS s ON s.idSubaccount = sm.idSubaccount "
            . "LEFT JOIN account AS a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "sm.startdate >= '{$datedaystartpreviousa}' "
            . "AND sm.startdate < '{$datedaystartpreviousb}' AND sm.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS s "
            . "ON ac.idAccount = s.idAccount "
            . "GROUP BY ac.name " . $comple;

    $sql2 = "SELECT "
            . "ac.idAccount, ac.name, m.enviosmail, s.enviossms "
            . "FROM ( "
            . "SELECT "
            . "a.idAccount, a.name "
            . "FROM "
            . "subaccount AS s "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "group by a.name "
            . ") as ac "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(m.messagesSent) AS enviosmail "
            . "FROM "
            . "mail AS m "
            . "JOIN subaccount AS s ON s.idSubaccount = m.idSubaccount "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "m.scheduleDate >= '{$datedaystart}' "
            . "AND m.scheduleDate < '{$datedayend}' AND m.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS m "
            . "ON ac.idAccount = m.idAccount "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(sm.sent) AS enviossms "
            . "FROM "
            . "sms AS sm "
            . "LEFT JOIN subaccount AS s ON s.idSubaccount = sm.idSubaccount "
            . "LEFT JOIN account AS a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "sm.startdate >= '{$datedaystart}' "
            . "AND sm.startdate < '{$datedayend}' AND sm.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS s "
            . "ON ac.idAccount = s.idAccount "
            . "GROUP BY ac.name ";

    $sql3 = "SELECT "
            . "ac.idAccount, ac.name, m.enviosmail, s.enviossms "
            . "FROM ( "
            . "SELECT "
            . "a.idAccount, a.name "
            . "FROM "
            . "subaccount AS s "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "group by a.name "
            . ") as ac "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(m.messagesSent) AS enviosmail "
            . "FROM "
            . "mail AS m "
            . "JOIN subaccount AS s ON s.idSubaccount = m.idSubaccount "
            . "JOIN account as a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "m.scheduleDate >= '{$datedaystartpreviousa}' "
            . "AND m.scheduleDate < '{$datedaystartpreviousb}' AND m.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS m "
            . "ON ac.idAccount = m.idAccount "
            . "LEFT JOIN "
            . "( "
            . "SELECT "
            . "a.idAccount, "
            . "a.name AS Cuenta, "
            . "sum(sm.sent) AS enviossms "
            . "FROM "
            . "sms AS sm "
            . "LEFT JOIN subaccount AS s ON s.idSubaccount = sm.idSubaccount "
            . "LEFT JOIN account AS a ON a.idAccount = s.idAccount "
            . "WHERE "
            . "sm.startdate >= '{$datedaystartpreviousa}' "
            . "AND sm.startdate < '{$datedaystartpreviousb}' AND sm.`status` = 'sent' "
            . "GROUP BY 1 "
            . ") AS s "
            . "ON ac.idAccount = s.idAccount "
            . "GROUP BY ac.name ";

    $this->data = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    $this->data2 = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql2);
    $this->totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql3);



//    echo "<pre>";
//    print_r($sql);
//    print_r($sql2);
//    echo "</pre>";
//    exit;      

    $arr = array();
    foreach ($this->data as $data) {

      $dataarray = new \stdClass();

      $dataarray->idAccount = $data['idAccount'];

      $where = array("idAccount" => $dataarray->idAccount);

      //unset($dataarray->idAccount);
//      var_dump($where);
//      exit;

      $this->contact = \Contact::count(array($where));

      if ($this->contact == '') {
        $dataarray->contacts = 0;
      } else {
        $dataarray->contacts = $this->contact;
      }

      $dataarray->name = $data['name'];
      if ($data['enviosmail'] == '') {
        $dataarray->enviosmail = 0;
      } else {
        $dataarray->enviosmail = $data['enviosmail'];
      }

      if ($data['enviossms'] == '') {
        $dataarray->enviossms = 0;
      } else {
        $dataarray->enviossms = $data['enviossms'];
      }

      foreach ($this->data2 as $data2) {
        if ($data['idAccount'] == $data2['idAccount']) {

          if ($data2['enviosmail'] == '') {
            $dataarray->enviosmaila = 0;
          } else {
            $dataarray->enviosmaila = $data2['enviosmail'];
          }

          if ($data2['enviossms'] == '') {
            $dataarray->enviossmsb = 0;
          } else {
            $dataarray->enviossmsb = $data2['enviossms'];
          }
        }
      }

      array_push($arr, $dataarray);
    }

    $this->datastatis = array("datepres" => $datepres, "dateprev" => $dateprev, "items" => $arr, "total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    //array_push($this->datastatis, array($arr)); 

    $this->getDatastatisalliedReport = $arr;

    return $this->datastatis;
  }

  public function getDataStatisallied($data) {
    $this->getDatastatisalliedReport = $data;
  }

  public function generateReportExcel() {
    $this->flag = 1;
    $this->findStatisallied(0);

    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStatisalliedReport();
    $excel->setDatastatic($this->getDatastatisalliedReport);
    $excel->generatedReportStatisallied();
    $excel->download();
  }

  function getAccount() {
    return $this->account;
  }

}
