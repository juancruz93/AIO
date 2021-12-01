<?php

namespace Sigmamovil\Wrapper;

include_once( "../app/library/phpexcel/Classes/PHPExcel.php");
include_once( "../app/library/phpexcel/Classes/PHPExcel/Writer/Excel2007.php");

/**
 * Description of ApiWrapper
 *
 * @author desarrollo3
 */
class ReportWrapper extends \BaseWrapper {

  public $page,
          $report,
          $user,
          $limit,
          $totals,
          $account = array(),
          $subaccount = array(),
          $users = array(),
          $search,
          $infoDetail = array(),
          $graph = array(),
          $allMail = array();

  function __construct() {
    parent::__construct();
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $this->limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
  }

  public function getAllSubaccount() {
    $subaccount = \Subaccount::find(["conditions" => "idAccount = ?0", "bind" => [$this->user->Usertype->idAccount]]);
    foreach ($subaccount as $value) {
      $obj = new \stdClass();
      $obj->idSubaccount = $value->idSubaccount;
      $obj->name = $value->name;
      array_push($this->subaccount, $obj);
    }
  }

  public function getEmailUsers() {
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
    if ($idSubaccount) {
      $where = " WHERE subaccount.idSubaccount = {$idSubaccount} ";
    } else {
      $idSubaccount = $this->getIdAccount();
      $where = " WHERE subaccount.idSubaccount IN ({$idSubaccount})";
    }
    $sql = "SELECT user.name, user.idUser, user.email, subaccount.idSubaccount "
            . " FROM user "
            . " LEFT JOIN usertype ON usertype.idUsertype = user.idUsertype "
            . " LEFT JOIN subaccount ON subaccount.idSubaccount = usertype.idSubaccount "
            . " {$where}";
    $data = $this->db->fetchAll($sql);
    //    $subaccount = \Subaccount::find(["conditions" => "idAccount = ?0", "bind" => [$this->user->Usertype->idAccount]]);
    foreach ($data as $value) {
      $obj = new \stdClass();
      $obj->idUser = $value['idUser'];
      $obj->email = $value['email'];
      $obj->name = $value['name'];
      array_push($this->users, $obj);
    }
  }

  public function getAllReportMail() {
    $wherein = "1 = 1";
    $wheredate = "1 = 1";
    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      for ($i = 0; $i < count($this->search->account); $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = "Account.idAccount IN ({$in}) ";
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "Mail.scheduleDate BETWEEN '{$this->search->dateInitial} 00:01 01' AND '{$this->search->dateFinal} 23:59 59' ";
    }
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $this->data = $this->modelsManager->createBuilder()
            ->columns(["Mail.scheduleDate AS scheduleDate", "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount", "Mail.name AS name", "Mail.quantitytarget AS ctotal",
                "Mail.quantitytarget AS ctotal", "Mail.uniqueOpening AS copen", "Mail.bounced AS cbounced", "Mail.spam AS cspam"])
            ->from('Mail')
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Mail.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied . " AND Mail.status = 'sent'")
            ->andWhere($wherein)
            ->andWhere($wheredate)
            ->limit($this->limit, $this->page)
            ->getQuery()
            ->execute();

    $this->totals = $this->modelsManager->createBuilder()
            ->columns(["Mail.idMail AS count"])
            ->from('Mail')
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Mail.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied . " AND Mail.status = 'sent'")
            ->andWhere($wherein)
            ->andWhere($wheredate)
            ->getQuery()
            ->execute();
    $this->modelDataMail();
  }

  public function getAllReportSms() {
    $wherein = "1 = 1";
    $wheredate = "1 = 1";
    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      for ($i = 0; $i < count($this->search->account); $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = "Account.idAccount IN ({$in}) ";
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "Sms.startdate BETWEEN '{$this->search->dateInitial} 00:01' AND '{$this->search->dateFinal} 23:59' ";
    }

    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $this->data = $this->modelsManager->createBuilder()
            ->columns(["Sms.name AS nameSms", "Sms.startdate",
                "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount",
                "COUNT(Smslote.idSmslote) AS target"])
            ->from('Sms')
            ->leftJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied .
                    " AND Sms.status = 'sent' AND Smslote.status = 'sent'")
            ->andWhere($wherein)
            ->andWhere($wheredate)
            ->limit($this->limit, $this->page)
            ->groupBy(["1", "2", "3", "4", "5"])
            ->getQuery()
            ->execute();

    $this->totals = $this->modelsManager->createBuilder()
            ->columns(["Sms.idSms AS count"])
            ->from('Sms')
            ->leftJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied .
                    " AND Sms.status = 'sent' AND Smslote.status = 'sent'")
            ->andWhere($wherein)
            ->andWhere($wheredate)
            ->groupBy("1")
            ->getQuery()
            ->execute();
    $this->modelDataMail();
  }

  public function modelDataMail() {
    $this->report = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / $this->limit));
    $arr = array();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      array_push($arr, $value);
    }
    array_push($this->report, ["items" => $arr]);
  }
  public function modelDataMail2() {
    $this->report = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / $this->limit));
    $arr = array();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      
      var_dump($value["idAccountConf"]);
      var_dump($value["idService"]);
      var_dump($value["idRecharge"]);
      $history = \RechargeHistory::find(["conditions" => "idAccountConfig = ?0 AND idServices=?1 AND idRechargeHistory!=?2", 
                                  "bind" => [0 =>$value["idAccountConf"],1 => $value["idService"],2 =>$value["idRecharge"]],
                                  //"columns" => ["idAccount","name"],
                                  //"group" => ["idAccountConfig","idServices"],
                                  "order" => "created DESC"])->toArray();
      $value["history"] = $history;
      array_push($arr, $value);
    }
    array_push($this->report, ["items" => $arr]);
    
  }

  public function getAllAccountByAllied() {
    $this->data = \Account::find(["conditions" => "idAllied = ?0 AND status=?1", 
                                  "bind" => [0 => $this->user->Usertype->idAllied,1 => (int) 1],
                                  "columns" => ["idAccount","name"],
                                  "order" => "name ASC"]);
    
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      $this->account[] = $value;
      //array_push($this->account, $value);
    }
  }

  public function generateReportExcel() {
    $dateEnd = time();
    $dateInitial = strtotime("-30 day", $dateEnd);
    $dateInitial = date("Y-m-d h:i s", $dateInitial);
    $dateEnd = date("Y-m-d h:i s", $dateEnd);

    $wheredate = "Mail.scheduleDate BETWEEN '{$dateInitial}' AND '{$dateEnd}'";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "Mail.scheduleDate BETWEEN '{$this->search->dateInitial} 00:01 01' AND '{$this->search->dateFinal} 23:59 59' ";
    }
    $data = $this->modelsManager->createBuilder()
            ->columns(["Mail.scheduleDate AS scheduleDate", "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount", "Mail.name AS name", "Mail.quantitytarget AS ctotal",
                "Mail.quantitytarget AS ctotal", "Mail.uniqueOpening AS copen", "Mail.bounced AS cbounced", "Mail.spam AS cspam"])
            ->from('Mail')
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Mail.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied . " AND Mail.status = 'sent' ")
            ->andWhere($wheredate)
            ->getQuery()
            ->execute();
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsReportMail();
    $excel->setData($data);
    $excel->setTableInfoReportMail();
    $excel->generatedReportMail();
    $excel->download();
  }

  public function generateReportExcelRecharge($title) {   
    $wherein = "1 = 1";
    $wheredate = "1 = 1";
//    $dateEnd = time();
//    $dateInitial = strtotime("-30 day", $dateEnd);

    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      $accNumber = count($this->search->account);
      for ($i = 0; $i < $accNumber; $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = " account.idAccount IN ({$in}) ";
    }
//    $wheredate = "RechargeHistory.created BETWEEN '{$dateInitial}' AND '{$dateEnd}' ";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if (strtotime($this->search->dateFinal) < strtotime($this->search->dateInitial)) {
        throw new \InvalidArgumentException("La fecha final no puede ser inferior a la inicial");
      }else{
        $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitial . ' 00:00:00'));
        $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . ' 23:59:59'));
        $dateInitial = strtotime($fechainiConseg);
        $dateFinal = strtotime($fechaFinConseg);
        $wheredate = " recharge_history.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
      }
    }
//    $data = $this->modelsManager->createBuilder()
//            ->columns(["RechargeHistory.idRechargeHistory AS idRecharge",
//                "RechargeHistory.idMasterConfig AS idMaster",
//                "RechargeHistory.idAccountConfig AS idAccountConf",
//                "RechargeHistory.idAlliedconfig AS idAllieds",
//                "RechargeHistory.idServices AS idService",
//                "RechargeHistory.rechargeAmount AS recharge",
//                "RechargeHistory.initialTotal AS initialTotals",
//                "FROM_UNIXTIME(RechargeHistory.created) AS createds",
//                "FROM_UNIXTIME(RechargeHistory.updated) AS updateds",
//                "RechargeHistory.createdBy AS createdBy",
//                "RechargeHistory.updatedBy AS updatedBy",
//                "Allied.name AS nameallied",
//                "Account.name AS nameaccount"
//            ])
//            ->from('RechargeHistory')
//            ->innerjoin('AccountConfig', 'AccountConfig.idAccountConfig = RechargeHistory.idAccountConfig')
//            ->innerjoin('Account', 'Account.idAccount = AccountConfig.idAccount')
//            ->innerjoin('Allied', 'Allied.idAllied = Account.idAllied')
//            ->where("Account.idAllied = " . $this->user->Usertype->idAllied)
//            ->andWhere($wherein)
//            ->andWhere($wheredate)
//            ->getQuery()
//            ->execute();
    
    $sql = "SELECT
              account. NAME as nameaccount,
              maxidRh.idAccountConfig as idAccountConf,
              maxidRh.idServices as idService,
              idRechargeHistory as idRecharge,
              rechargeAmount,
              (
              initialAmount + rechargeAmount
              ) DisponibleAfter,
              (
              initialTotal + rechargeAmount
              ) TotalAfter,
              FROM_UNIXTIME(recharge_history.created) as createds,
              recharge_history.createdBy
            FROM
              recharge_history
            INNER JOIN account_config ON recharge_history.idAccountConfig = account_config.idAccountConfig
            INNER JOIN account ON account.idAccount = account_config.idAccount
            INNER JOIN (
                SELECT
                    max(idRechargeHistory) idRh,
                    idAccountConfig,
                    idServices
                FROM
                    recharge_history
                GROUP BY
                    idAccountConfig,
                    idServices
            ) AS maxidRh ON maxidRh.idAccountConfig = recharge_history.idAccountConfig
            AND maxidRh.idRh = recharge_history.idRechargeHistory
            WHERE 
              account.idAllied = {$this->user->Usertype->idAllied}
            AND {$wherein}
            AND {$wheredate}  
            ORDER BY 8 DESC";
    $data = $this->db->fetchAll($sql);
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsReportRecharge();
    $excel->setData($data);
    $excel->setTableInfoReportMail();
    $excel->generatedReportGeneralRecharge();
    return $excel->downloadExcel($title);
  }

  public function generateReportExcelChangePlan($title) {
    $wherein = " 1 = 1 AND ";
    $dateEnd = time();
    $dateInitial = strtotime("-30 day", $dateEnd);

    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      for ($i = 0; $i < count($this->search->account); $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = "C.idAccount IN ({$in}) AND ";
    }
    $wheredate = "history_payment_plan.created BETWEEN '{$dateInitial}' AND '{$dateEnd}' ";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {

      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      //$wheredate = "HistoryPaymentPlan.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
      $wheredate = "history_payment_plan.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }

    $sql = "SELECT
                    history_payment_plan.idHistoryPaymentPlan AS idHistoryPaymentPlan,
                    account_config.idAccountConfig AS idAccounConf,
                    allied.name AS nameAllied,
                    C.name AS nameAccount,
                    payment_plan.idPaymentPlan AS idPayment,
                    payment_plan.name AS namePreviPlan,
                    payment_plan.createdBy AS createdBys,
                    from_unixtime(history_payment_plan.created) AS dateChange,
                    (select 
                            payment_plan.name
                     from 
                        payment_plan
                                inner join
                            account AS ac on ac.idPaymentPlan = payment_plan.idPaymentPlan
                         where
                            ac.idAllied = {$this->user->Usertype->idAllied} AND ac.idAccount = C.idAccount
                        )
                    AS nameCurrentPlan
                FROM
                    history_payment_plan
                        INNER JOIN
                    account_config ON account_config.idAccountConfig = history_payment_plan.idAccountConfig
                        INNER JOIN
                    payment_plan ON payment_plan.idPaymentPlan = history_payment_plan.idPaymentPlan
                        INNER JOIN
                    account AS C ON C.idAccount =  account_config.idAccount
                        INNER JOIN
                    allied ON allied.idAllied = C.idAllied
                WHERE
                    allied.idAllied = {$this->user->Usertype->idAllied} AND
                    {$wherein} 
                    {$wheredate}";

    $data = $this->db->fetchAll($sql);

    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsReportChangePlan();
    $excel->setData($data);
    $excel->setTableInfoReportMail();
    $excel->generatedReportChangePlan();
    $excel->downloadExcel($title);
  }

  public function generateReportExcelSms() {
    $dateEnd = time();
    $dateInitial = strtotime("-30 day", $dateEnd);
    $dateInitial = date("Y-m-d h:i", $dateInitial);
    $dateEnd = date("Y-m-d h:i", $dateEnd);

    $wheredate = "Sms.startdate BETWEEN '{$dateInitial}' AND '{$dateEnd}'";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "Sms.startdate BETWEEN '{$this->search->dateInitial} 00:01' AND '{$this->search->dateFinal} 23:59' ";
    }

    $data = $this->modelsManager->createBuilder()
            ->columns(["Sms.name AS nameSms", "Sms.startdate",
                "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount",
                "COUNT(Smslote.idSmslote) AS target"])
            ->from('Sms')
            ->leftJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $this->user->Usertype->idAllied .
                    " AND Sms.status = 'sent' AND Smslote.status = 'sent'")
            ->andWhere($wheredate)
            ->groupBy(["1", "2", "3", "4", "5"])
            ->getQuery()
            ->execute();
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsReportSms();
    $excel->setData($data);
    $excel->setTableInfoReportMail();
    $excel->generatedReportGeneralSms();
    $excel->download();
  }

  public function getIdAccount() {
    $idSubaccount = "";
    if ($this->user->Usertype->idAllied) {
      $account = \Account::find(["conditions" => "idAllied = ?0", "bind" => [$this->user->Usertype->idAllied]]);
      for ($index = 0; $index < count($account); $index++) {
        for ($index2 = 0; $index2 < count($account[$index]->Subaccount); $index2++) {
          $idSubaccount .= $account[$index]->Subaccount[$index2]->idSubaccount;
          if (($index + 1) != count($account)) {
            $idSubaccount .= ", ";
          }
        }
      }
    } else if ($this->user->Usertype->idAccount) {
      $subAccount = \Subaccount::find(["conditions" => "idAccount = ?0", "bind" => [$this->user->Usertype->idAccount]]);
      for ($index = 0; $index < count($subAccount); $index++) {
        $idSubaccount .= $subAccount[$index]->idSubaccount;
        if (($index + 1) != count($subAccount)) {
          $idSubaccount .= ", ";
        }
      }
    }
    return $idSubaccount;
  }

  public function reportGraphMail() {
    $dateEnd = time("Y-m");
    $dateInitial = strtotime("-12 month", $dateEnd);
    $dateInitial = date("Y-m-d " . "01:00", $dateInitial);
    $dateEnd = date("Y-m-d " . "23:59", $dateEnd);
    $idSubaccount = $this->getIdAccount();
    $wheredate = "mail.scheduleDate BETWEEN '{$dateInitial}' AND '{$dateEnd}'";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "mail.scheduleDate BETWEEN '{$this->search->dateInitial}-01 00:01' AND '{$this->search->dateFinal}-31 23:59' ";
    }

    $sql = "SELECT extract(YEAR FROM mail.scheduleDate) year, "
            . " extract(MONTH FROM mail.scheduleDate) month, "
            . "count(idMail) AS y FROM mail WHERE mail.scheduleDate != '' "
            . " AND mail.status =  'sent' "
            . " AND {$wheredate} AND mail.idSubaccount IN ({$idSubaccount}) GROUP BY 1, 2";
    $data = $this->db->fetchAll($sql);
    foreach ($data as $key) {
      $obj = new \stdClass();
      $obj->name = $this->stringMonth($key['month']) . "-" . $key['year'];
      $obj->y = (int) $key['y'];
      array_push($this->graph, $obj);
    }
  }

  public function reportGraphSms() {
    $dateEnd = time("Y-m");
    $dateInitial = strtotime("-12 month", $dateEnd);
    $dateInitial = date("Y-m-d " . "01:00", $dateInitial);
    $dateEnd = date("Y-m-d " . "23:59", $dateEnd);
    $idSubaccount = $this->getIdAccount();
    $wheredate = "sms.startdate BETWEEN '{$dateInitial}' AND '{$dateEnd}'";
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $wheredate = "sms.startdate BETWEEN '{$this->search->dateInitial}-01 00:01' AND '{$this->search->dateFinal}-31 23:59' ";
    }

    $sql = "SELECT extract(YEAR FROM sms.startdate) year, "
            . " extract(MONTH FROM sms.startdate) month, "
            . " count(idSms) AS y "
            . " FROM sms"
            . " WHERE sms.startdate != ''"
            . " AND sms. STATUS = 'sent' "
            . " AND {$wheredate} "
            . " AND sms.idSubaccount IN ({$idSubaccount}) "
            . " GROUP BY 1, 2";
    $data = $this->db->fetchAll($sql);
    foreach ($data as $key) {
      $obj = new \stdClass();
      $obj->name = $this->stringMonth($key['month']) . "-" . $key['year'];
      $obj->y = (int) $key['y'];
      array_push($this->graph, $obj);
    }
  }

  public function getInfoExcelSms() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $string = "";
    $intervalMonth = 0;
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es inferior a la fecha inicial");
      }
      $this->search->dateFinal .= "-31";
      $this->search->dateInitial .= "-01";
      $datetime1 = new \DateTime($this->search->dateInitial);
      $datetime2 = new \DateTime($this->search->dateFinal);
      $interval = $datetime1->diff($datetime2);
      $interval1 = $interval->format("%m");
      $interval2 = $interval->format("%y") * 12;
      $intervalMonth = ($interval1 + $interval2);
      $dateEnd = $this->search->dateFinal . " 23:59";
    } else {
      $intervalMonth = 11;
      $fecha = new \DateTime();
      $dateEnd = $fecha->modify('last day of this month');
      $dateEnd = $fecha->format('Y-m-d' . " 23:59");
    }

    for ($index = 0; $index <= $intervalMonth; $index++) {
      if ($index == 0) {
        $dateInitial = strtotime("first day of", strtotime($dateEnd));
        $dateInitial = date("Y-m-d" . " 01:00", $dateInitial);
        $dateInitial2 = $dateEnd;
      } else {
        $dateInitial = strtotime("-1 month first day of", strtotime($dateInitial2));
        $dateInitial2 = strtotime("last day of", $dateInitial);
        $dateInitial = date("Y-m-d" . " 01:00", $dateInitial);
        $dateInitial2 = date("Y-m-d" . " 23:59", $dateInitial2);
      }
      $string .= "COUNT(CASE WHEN s.startdate BETWEEN '{$dateInitial}' AND '{$dateInitial2}' "
              . "THEN smslote.idSmslote  END) " .
              "'" . $this->stringMonth(date("m", strtotime($dateInitial))) . " " . date("Y", strtotime($dateInitial)) . "'";
      if ($index < $intervalMonth) {
        $string .= ", ";
      }
    }
    $idSubaccount = $this->getIdAccount();
    $where = " WHERE s.idSubaccount IN ({$idSubaccount}) ";
    $this->queryPivot($string, $where);
  }

  public function queryPivot($string, $where = "") {
    $arr = explode(",", $string);
    $arr = array_reverse($arr);
    $string = implode(",", $arr);
    $sql = "SELECT  account.idAccount AS id, account.name AS 'Cuenta', {$string} FROM sms s "
            . " LEFT JOIN smslote on s.idSms = smslote.idSms "
            . " LEFT JOIN subaccount ON s.idSubaccount = subaccount.idSubaccount "
            . " LEFT JOIN account ON subaccount.idAccount = account.idAccount "
            . " {$where} "
            . "  GROUP BY 1, 2 "
            . " LIMIT {$this->limit} OFFSET {$this->page} ";

    $sql2 = "SELECT  account.idAccount AS id, account.name AS 'Cuenta', {$string} FROM sms s "
            . " LEFT JOIN smslote on s.idSms = smslote.idSms "
            . " LEFT JOIN subaccount ON s.idSubaccount = subaccount.idSubaccount "
            . " LEFT JOIN account ON subaccount.idAccount = account.idAccount "
            . " {$where} "
            . "  GROUP BY 1, 2 ";

    $data = $this->db->fetchAll($sql);
    $total = $this->db->fetchAll($sql2);
    $arr = $this->modelDataInfo($data);
    $this->infoDetail = array("total" => count($total),
        "total_pages" => ceil(count($total) / $this->limit),
        "items" => $arr);
  }

  public function getInfoExcelSmsByDay() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $string = "";
    $intervalMonth = 0;
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es infeior a la fecha inicial");
      }
      $dias = (strtotime($this->search->dateInitial) - strtotime($this->search->dateFinal)) / 86400;
      $dias = abs($dias);
      $intervalMonth = floor($dias);
      $dateEnd = $this->search->dateFinal . " 23:59";
    } else {
      $intervalMonth = 29;
      $dateEnd = date("Y-m-d" . " 23:59");
    }

    for ($index = 0; $index <= $intervalMonth; $index++) {
      if ($index == 0) {
        $dateInitial = date("Y-m-d" . " 00:01", strtotime($dateEnd));
        $dateInitial2 = $dateEnd;
      } else {
        $dateInitial = strtotime("-1 day", strtotime($dateInitial2));
        $dateInitial = date("Y-m-d" . " 00:01", $dateInitial);
        $dateInitial2 = date("Y-m-d" . " 23:59", strtotime($dateInitial));
      }

      $string .= "COUNT(CASE WHEN s.startdate BETWEEN '{$dateInitial}' AND '{$dateInitial2}' "
              . "THEN smslote.idSmslote  END) " .
              "'" . date("d/m/Y", strtotime($dateInitial)) . "'";
      if ($index < $intervalMonth) {
        $string .= ", ";
      }
    }
    $idSubaccount = $this->getIdAccount();
    if ($idSubaccount != "" && $idSubaccount != null) {
      $where = " WHERE s.idSubaccount IN ({$idSubaccount}) ";
      $this->queryPivot($string, $where);
    } else {
      $this->infoDetail = array("total" => count($total),
          "total_pages" => ceil(0 / $this->limit),
          "items" => array());
    }
  }

  public function modelDataInfo($data) {
    $arr = [];
    $arr2 = [];
    $arr3 = [];
    $count = 1;
    foreach ($data as $value) {
      $arr2 = [];
      foreach ($value as $key => $value2) {
        if ($count == 1) {
          $arr3[] = $key;
        }
        $arr2[] = $value2;
      }
      if ($count == 1) {
        array_push($arr, $arr3);
      }
      array_push($arr, $arr2);
      $count++;
    }
    return $arr;
  }

  public function downloadInfoSms() {
    $string = "";
    $intervalMonth = 0;
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es infeior a la fecha inicial");
      }
      $this->search->dateFinal .= "-31";
      $this->search->dateInitial .= "-01";
      $datetime1 = new \DateTime($this->search->dateInitial);
      $datetime2 = new \DateTime($this->search->dateFinal);
      $interval = $datetime1->diff($datetime2);
      $interval1 = $interval->format("%m");
      $interval2 = $interval->format("%y") * 12;
      $intervalMonth = ($interval1 + $interval2);
      $dateEnd = $this->search->dateFinal . " 23:59";
    } else {
      $intervalMonth = 11;
      $fecha = new \DateTime();
      $dateEnd = $fecha->modify('last day of this month');
      $dateEnd = $fecha->format('Y-m-d' . " 23:59");
    }
    for ($index = 0; $index <= $intervalMonth; $index++) {
      if ($index == 0) {
        $dateInitial = strtotime("first day of", strtotime($dateEnd));
        $dateInitial = date("Y-m-d" . " 01:00", $dateInitial);
        $dateInitial2 = $dateEnd;
      } else {
        $dateInitial = strtotime("-1 month first day of", strtotime($dateInitial2));
        $dateInitial2 = strtotime("last day of", $dateInitial);
        $dateInitial = date("Y-m-d" . " 01:00", $dateInitial);
        $dateInitial2 = date("Y-m-d" . " 23:59", $dateInitial2);
      }
      $string .= "COUNT(CASE WHEN s.startdate BETWEEN '{$dateInitial}' AND '{$dateInitial2}' "
              . "THEN smslote.idSmslote  END) " .
              "'" . $this->stringMonth(date("m", strtotime($dateInitial))) . " " . date("Y", strtotime($dateInitial)) . "'";
      if ($index < $intervalMonth) {
        $string .= ", ";
      }
    }
    $idSubaccount = $this->getIdAccount();
    $where = " WHERE s.idSubaccount IN ({$idSubaccount}) ";
    $sql = "SELECT  CONCAT(account.idAccount, '-', account.name)  AS 'Cuenta', {$string} FROM sms s "
            . " LEFT JOIN smslote on s.idSms = smslote.idSms "
            . " LEFT JOIN subaccount ON s.idSubaccount = subaccount.idSubaccount "
            . " LEFT JOIN account ON subaccount.idAccount = account.idAccount "
            . " {$where} "
            . " GROUP BY 1, account.idAccount ";
    $data = $this->db->fetchAll($sql);
    $arr = $this->modelDataInfo($data);
    $this->modelDataSmsDownload($arr);
  }

  public function downloadInfoSmsByDay($title) {
    $string = "";
    $intervalMonth = 0;
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es inferior a la fecha inicial");
      }
      $dias = (strtotime($this->search->dateInitial) - strtotime($this->search->dateFinal)) / 86400;
      $dias = abs($dias);
      $intervalMonth = floor($dias);
      $dateEnd = $this->search->dateFinal . " 23:59";
    } else {
      $intervalMonth = 29;
      $dateEnd = date("Y-m-d" . " 23:59");
    }

    for ($index = 0; $index <= $intervalMonth; $index++) {
      if ($index == 0) {
        $dateInitial = date("Y-m-d" . " 00:01", strtotime($dateEnd));
        $dateInitial2 = $dateEnd;
      } else {
        $dateInitial = strtotime("-1 day", strtotime($dateInitial2));
        $dateInitial = date("Y-m-d" . " 00:01", $dateInitial);
        $dateInitial2 = date("Y-m-d" . " 23:59", strtotime($dateInitial));
      }
      $string .= "COUNT(CASE WHEN s.startdate BETWEEN '{$dateInitial}' AND '{$dateInitial2}' "
              . "THEN smslote.idSmslote  END) " .
              "'" . date("d/m/Y", strtotime($dateInitial)) . "'";
      if ($index < $intervalMonth) {
        $string .= ", ";
      }
    }
    $idSubaccount = $this->getIdAccount();
    $where = " WHERE s.idSubaccount IN ({$idSubaccount}) ";
    $sql = "SELECT  CONCAT(account.idAccount, '-', account.name)  AS 'Cuenta', {$string} FROM sms s "
            . " LEFT JOIN smslote on s.idSms = smslote.idSms "
            . " LEFT JOIN subaccount ON s.idSubaccount = subaccount.idSubaccount "
            . " LEFT JOIN account ON subaccount.idAccount = account.idAccount "
            . " {$where} "
            . " GROUP BY 1, account.idAccount ";
    $data = $this->db->fetchAll($sql);
    $arr = $this->modelDataInfo($data);
    $this->modelDataSmsDownload($arr, $title);
  }

  public function infoDetailMail() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $where = " WHERE mail.status = 'sent' ";
    if (isset($this->search->subaccount)) {
      $where .= " AND mail.idSubaccount = " . $this->search->subaccount;
    }
    if (isset($this->search->emailUser)) {
      $where .= " AND mail.createdBy = '" . $this->search->emailUser . "'";
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $where .= " AND mail.scheduleDate BETWEEN '" . $this->search->dateInitial . "' AND '" . $this->search->dateFinal . "'";
    }
    $idSubaccount = $this->getIdAccount();
    if ($idSubaccount != "" && !isset($this->search->subaccount)) {
      $where .= " AND mail.idSubaccount IN ({$idSubaccount})";
    }
    if ($this->user->userType->idSubaccount) {
      $where .= " AND mail.idSubaccount = " . $this->user->userType->idSubaccount;
    } elseif ($this->user->userType->idAccount && $idSubaccount == "") {
      $where .= " AND mail.idSubaccount IN (-1)";
    }
    $sql = "SELECT"
            . " mail.idMail,"
            . " mail.name AS nameMail,"
            . " mail.scheduleDate, "
            . " mail.createdBy, "
            . " mail.uniqueOpening,"
            . " mail.bounced, "
            . " mail.spam, "
            . " mail.messagesSent,"
            //. " mail.uniqueClicks,"
            . " subaccount.name AS nameSubaccount FROM "
            . " mail INNER JOIN subaccount ON subaccount.idSubaccount = mail.idSubaccount {$where} ORDER BY mail.idMail DESC"
            . " LIMIT {$this->limit} OFFSET {$this->page}";
    $sql2 = "SELECT count(mail.idMail) FROM mail {$where}";
    $this->data = $this->db->fetchAll($sql);
    $this->totals = $this->db->fetchAll($sql2);
    $data = $this->modelDataInfoDetailMail();
    $this->infoDetail = array(
        "total" => (int) $this->totals[0]["count(mail.idMail)"],
        "total_pages" => ceil((int) $this->totals[0]["count(mail.idMail)"] / $this->limit),
        "items" => $data);
  }

  public function modelDataInfoDetailMail() {
    $arr = [];
    foreach ($this->data as $value) {
      $obj = new \stdClass();
      $obj->idMail = $value['idMail'];
      $obj->nameMail = $value['nameMail'];
      $obj->scheduleDate = $value['scheduleDate'];
      $obj->createdBy = $value['createdBy'];
      //      $obj->uniqueOpening = $value['uniqueOpening'];
      $obj->uniqueOpening = $this->getUniqueOpenings($value['idMail']);
//      $obj->bounced = $value['bounced'];
      $obj->bounced = $this->getTotalBounced($value['idMail']);
//      $obj->spam = $value['spam'];
      $obj->spam = $this->getTotalSpam($value['idMail']);
//      $obj->uniqueClicks = $value['uniqueClicks'];
      $obj->uniqueClicks = $this->getTotalUniqueClicks($value["idMail"]);
      $obj->messagesSent = $value['messagesSent'];
      $obj->nameSubaccount = $value['nameSubaccount'];
      $obj->unsuscribed = $this->getUnsuscribed($value['idMail']);
      array_push($arr, $obj);
    }
    return $arr;
  }
    
   public function getUniqueOpenings($idMail) {
    $open = \Mxc::count([["open" => ['$gte' => 1], "idMail" => (string) $idMail]]);
    return $open;
  }
  
  public function getTotalUniqueClicks($idMail) {
    $totalUniqueClicks = \Mxc::count([["uniqueClicks" => ['$gte' => 1], "idMail" => (string) $idMail]]);
    return $totalUniqueClicks;
  }
  
   public function getTotalBounced($idMail) {
    $bounced = \Mxc::count([["bounced" => ['$gte' => "1"], "idMail" => (string) $idMail]]);
    return $bounced;
  }  
  
  public function getUnsuscribed($idMail) {
    $open = \Mxc::count([["unsubscribed" => ['$gte' => 1], "idMail" => (string) $idMail]]);
    return $open;
  }
  
  public function getTotalSpam($idMail) {
    $spam = \Mxc::count([["spam" => ['$gte' => "1"], "idMail" => (string) $idMail]]);
    return $spam;
  }
  public function infoDetailSms() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $where = " WHERE 1 = 1 AND sms.status = 'sent' ";
    if (isset($this->search->emailUser)) {
      $where .= " AND sms.createdBy = '" . $this->search->emailUser . "'";
    }
    if (isset($this->search->subaccount)) {
      $where .= " AND sms.idSubaccount = " . $this->search->subaccount;
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es infeior a la fecha inicial");
      }
      $dateInitial = $this->search->dateInitial;
      $dateEnd = $this->search->dateFinal;
    } else {
      $dateEnd = date("Y-m-d" . " 23:59");
      $dateInitial = strtotime("-30 day", strtotime($dateEnd));
      $dateInitial = date("Y-m-d" . " 00:01", $dateInitial);
    }
    $where .= " AND sms.startdate BETWEEN '" . $dateInitial . "' AND '" . $dateEnd . "'";

    $idSubaccount = $this->getIdAccount();
    if ($idSubaccount != "" && !isset($this->search->subaccount)) {
      $where .= " AND sms.idSubaccount IN ({$idSubaccount})";
    }
    if ($this->user->userType->idSubaccount) {
      $where .= " AND sms.idSubaccount = " . $this->user->userType->idSubaccount;
    } elseif ($this->user->userType->idAccount && $idSubaccount == "") {
      $where .= " AND sms.idSubaccount IN (-1)";
    }
    $sql = "SELECT sms.createdBy, sms.idSms, sms.startdate, subaccount.name AS namesubaccount, "
            . " sms.name AS namesms,"
            . " sms.total  AS total, "
            . " sms.sent  AS sent,  "
            . " sms.type, "
            . " sms.total - sms.sent  AS undelivered, "
            . " SUM(smslote.messageCount) AS messageCount"
            . " FROM sms "
            . " LEFT JOIN smslote ON smslote.idSms = sms.idSms "
            . " LEFT JOIN subaccount ON sms.idSubaccount = subaccount.idSubaccount  "
            . " {$where} "
            . " GROUP BY 1,2,3,4,5,6,7,8"
            . " LIMIT {$this->limit} OFFSET {$this->page} ";
    $sql2 = "SELECT sms.idSms, sms.startdate, subaccount.name AS namesubaccount, "
            . " sms.name AS namesms, count(smslote.idSmslote) AS total, "
            . " count(CASE WHEN smslote.`status` = 'sent' THEN smslote.idSmslote END) AS sent,  "
            . " count(CASE WHEN smslote.`status` = 'undelivered' THEN smslote.idSmslote END) AS undelivered "
            . " FROM sms "
            . " LEFT JOIN smslote ON smslote.idSms = sms.idSms "
            . " LEFT JOIN subaccount ON sms.idSubaccount = subaccount.idSubaccount "
            . " {$where} "
            . " GROUP BY 1,2,3,4";
    $this->data = $this->db->fetchAll($sql);
    $this->totals = $this->db->fetchAll($sql2);
    $this->modelDataInfoDetail();
  }

  public function modelDataInfoDetail() {
    $arr = [];
    foreach ($this->data as $value) {
      $messageCount = $this->findMessageCount($value['idSms'], $value['type']);
      $arr[] = [
        "idSms"           => $value['idSms'],
        "namesms"         => $value['namesms'],
        "namesubaccount"  => $value['namesubaccount'],
        "sent"            => $value['sent'],
        "startdate"       => $value['startdate'],
        "total"           => $value['total'],
        "undelivered"     => $value['undelivered'],
        "messageCount"    => $messageCount,
        "createdBy"       => $value['createdBy']
      ];
      //array_push($arr, $value);
    }
    $this->infoDetail = array("total" => count($this->totals),
        "total_pages" => ceil(count($this->totals) / $this->limit),
        "items" => $arr);
  }

  public function dowloadReportInfoDetailMail($title) {
    $where = " WHERE mail.status = 'sent' ";
    if (isset($this->search->subaccount)) {
      $where .= " AND mail.idSubaccount = " . $this->search->subaccount;
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $where .= " AND mail.scheduleDate BETWEEN '" . $this->search->dateInitial . "' AND '" . $this->search->dateFinal . "'";
    }
    if (isset($this->search->emailUser)) {
      $where .= " AND mail.createdBy = '" . $this->search->emailUser . "'";
    }
    $idSubaccount = $this->getIdAccount();
    if ($idSubaccount != "" && !isset($this->search->subaccount)) {
      $where .= " AND mail.idSubaccount IN ({$idSubaccount})";
    }
    if ($this->user->userType->idSubaccount) {
      $where .= " AND mail.idSubaccount = " . $this->user->userType->idSubaccount;
    } elseif ($this->user->userType->idAccount && $idSubaccount == "") {
      $where .= " AND mail.idSubaccount IN (-1)";
    }
    $sql = "SELECT "
            . " mail.idMail, "
            . " mail.name AS nameMail, "
            . " mail.scheduleDate,  "
            . " mail.createdBy, "
            . " mail.uniqueOpening,"
            . " mail.bounced,"
            . " mail.spam, "
            . " mail.messagesSent, "
           // . " mail.uniqueClicks, "
            . " subaccount.name AS nameSubaccount FROM "
            . " mail  INNER JOIN subaccount ON subaccount.idSubaccount = mail.idSubaccount {$where} order by mail.idMail Desc";
    $this->data = $this->db->fetchAll($sql);
    $this->data = $this->modelDataInfoDetailMail();
    $this->modelDataInfoDetailMailDownload($title);
  }

  public function dowloadReportInfoDetailSms($title) {
    $where = " WHERE 1 = 1 AND sms.status = 'sent' ";
    $intervalMonth = 0;
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if ($this->search->dateFinal < $this->search->dateInitial) {
        throw new \InvalidArgumentException("La fecha final es infeior a la fecha inicial");
      }
      $dateInitial = $this->search->dateInitial;
      $dateEnd = $this->search->dateFinal;
    } else {
      $dateEnd = date("Y-m-d" . " 23:59");
      $dateInitial = strtotime("-30 day", strtotime($dateEnd));
      $dateInitial = date("Y-m-d" . " 00:01", $dateInitial);
    }
    $where .= " AND sms.startdate BETWEEN '" . $dateInitial . "' AND '" . $dateEnd . "'";
    if (isset($this->search->emailUser)) {
      $where .= " AND sms.createdBy = '" . $this->search->emailUser . "'";
    }
    if (isset($this->search->subaccount)) {
      $where .= " AND sms.idSubaccount = " . $this->search->subaccount;
    }

    $idSubaccount = $this->getIdAccount();
    if ($idSubaccount != "" && !isset($this->search->subaccount)) {
      $where .= " AND sms.idSubaccount IN ({$idSubaccount})";
    }
    if ($this->user->userType->idSubaccount) {
      $where .= " AND sms.idSubaccount = " . $this->user->userType->idSubaccount;
    } elseif ($this->user->userType->idAccount && $idSubaccount == "") {
      $where .= " AND sms.idSubaccount IN (-1)";
    }
    $sql = "SELECT sms.createdBy, sms.idSms, sms.startdate, sms.type, subaccount.name AS namesubaccount, "
            . " sms.name AS namesms,"
            . " sms.target  AS total, "
            . " sms.sent  AS sent,  "
            . " sms.target - sms.sent  AS undelivered, "
            . " SUM(smslote.messageCount) AS messageCount "
            . " FROM sms "
            . " LEFT JOIN smslote ON smslote.idSms = sms.idSms "
            . " LEFT JOIN subaccount ON sms.idSubaccount = subaccount.idSubaccount  "
            . " {$where} "
            . " GROUP BY 1,2,3,4,5,6,7,8";
    \Phalcon\DI::getDefault()->get('logger')->log("*******SQL EXCEL SMS ".json_encode($sql));        
    $this->data = $this->db->fetchAll($sql);
    $this->modelDataInfoDetailSmsDownload($title);
  }

  public function modelDataInfoDetailMailDownload($title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->basicPropertiesInfoDetailMail();
    $excel->setData($this->data);
    $excel->setInfoDetailMail();
    $excel->generatedReportInfoDetailMail();
    $excel->downloadExcel($title);
  }

  public function modelDataInfoDetailSmsDownload($title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->basicPropertiesInfoDetailSms();
    $excel->setData($this->data);
    $excel->setInfoDetailSms();
    $excel->generatedReportInfoDetailSms();
    $excel->downloadExcel($title);
  }

  public function modelDataSmsDownload($data, $title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->basicPropertiesInfoSms();
    $excel->setData($data);
    $excel->setInfoSms();
    $excel->generatedReportInfoSms();
    $excel->downloadExcel($title);
  }

  public function modelDataInfoDetailEmailValidation($data) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    //$excel->basicPropertiesSms();
    $excel->setData($data);
    //$excel->setInfoDetailSms();
    $excel->generatedInfoDetailEmailValidation();
    $nameExcel = "reporte_de_correos_validados";
    return $excel->downloadExcel($nameExcel);
  }

  public function getAllReportRecharge() {
    $wherein = "1 = 1";
    $wheredate = "1 = 1";
    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      $accNumber = count($this->search->account);
      for ($i = 0; $i < $accNumber; $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = " account.idAccount IN ({$in}) ";
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      if (strtotime($this->search->dateFinal) < strtotime($this->search->dateInitial)) {
        throw new \InvalidArgumentException("La fecha final no puede ser inferior a la inicial");
      }else{
        $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitial . ' 00:00:00'));
        $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . ' 23:59:59'));
        $dateInitial = strtotime($fechainiConseg);
        $dateFinal = strtotime($fechaFinConseg);
        $wheredate = " recharge_history.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
      }
      
    }
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : 1);
//    $this->data = $this->modelsManager->createBuilder()
//            ->columns(["RechargeHistory.idRechargeHistory AS idRecharge",
//                "RechargeHistory.idMasterConfig AS idMaster",
//                "RechargeHistory.idAccountConfig AS idAccountConf",
//                "RechargeHistory.idAlliedconfig AS idAllieds",
//                "RechargeHistory.idServices AS idService",
//                "RechargeHistory.rechargeAmount AS recharge",
//                //"RechargeHistory.initialAmount AS initialAmount",
//                //"(RechargeHistory.initialTotal - RechargeHistory.initialAmount) AS initialConsumed",
//                //"RechargeHistory.initialTotal AS initialTotals",
//                
//                "DetailConfig.amount as finalAmount",
//                "DetailConfig.totalAmount as finalTotalAmount",
//                "(DetailConfig.totalAmount - DetailConfig.amount ) as finalConsumed",
//                
//                "FROM_UNIXTIME(RechargeHistory.created) AS createds",
//                "FROM_UNIXTIME(RechargeHistory.updated) AS updateds",
//                "RechargeHistory.createdBy AS createdBy",
//                "RechargeHistory.updatedBy AS updatedBy",
//                "Allied.name AS nameallied",
//                "Account.name AS nameaccount"
//            ])
//            ->from('RechargeHistory')
//            ->innerjoin('AccountConfig', 'AccountConfig.idAccountConfig = RechargeHistory.idAccountConfig')
//            ->innerjoin('DetailConfig', 'AccountConfig.idAccountConfig = DetailConfig.idAccountConfig')
//            ->innerjoin('Account', 'Account.idAccount = AccountConfig.idAccount')
//            ->innerjoin('Allied', 'Allied.idAllied = Account.idAllied')
//            ->where("Account.idAllied = " . $this->user->Usertype->idAllied)
//            ->andWhere($wherein)
//            ->andWhere($wheredate)
//            ->groupBy("Account.idAccount")
//            ->orderBy("RechargeHistory.created desc")
//            ->limit($this->limit, $this->page)
//            ->getQuery()
//            ->execute();
//
//    $this->totals = $this->modelsManager->createBuilder()
//            ->columns(["RechargeHistory.idRechargeHistory AS count"])
//            ->from('RechargeHistory')
//            ->innerjoin('AccountConfig', 'AccountConfig.idAccountConfig = RechargeHistory.idAccountConfig')
//            ->innerjoin('DetailConfig', 'AccountConfig.idAccountConfig = DetailConfig.idAccountConfig')
//            ->innerjoin('Account', 'Account.idAccount = AccountConfig.idAccount')
//            ->innerjoin('Allied', 'Allied.idAllied = Account.idAllied')
//            ->where("Account.idAllied = " . $this->user->Usertype->idAllied)
//            ->andWhere($wherein)
//            ->andWhere($wheredate)
//            ->groupBy("Account.idAccount")
//            ->getQuery()
//            ->execute();
    $sql = "SELECT
              account. NAME as nameaccount,
              maxidRh.idAccountConfig as idAccountConf,
              maxidRh.idServices as idService,
              idRechargeHistory as idRecharge,
              rechargeAmount,
              (
              initialAmount + rechargeAmount
              ) DisponibleAfter,
              (
              initialTotal + rechargeAmount
              ) TotalAfter,
              FROM_UNIXTIME(recharge_history.created) as createds,
              recharge_history.createdBy
            FROM
              recharge_history
            INNER JOIN account_config ON recharge_history.idAccountConfig = account_config.idAccountConfig
            INNER JOIN account ON account.idAccount = account_config.idAccount
            INNER JOIN (
                SELECT
                    max(idRechargeHistory) idRh,
                    idAccountConfig,
                    idServices
                FROM
                    recharge_history
                GROUP BY
                    idAccountConfig,
                    idServices
            ) AS maxidRh ON maxidRh.idAccountConfig = recharge_history.idAccountConfig
            AND maxidRh.idRh = recharge_history.idRechargeHistory
            WHERE 
              account.idAllied = {$this->user->Usertype->idAllied}
            AND {$wherein}
            AND {$wheredate}  
            ORDER BY 8 DESC limit {$this->limit} offset {$this->page}";
    $this->data = $this->db->fetchAll($sql);
    
    $sqlcount = "SELECT
                recharge_history.idRechargeHistory as count
            FROM
              recharge_history
            INNER JOIN account_config ON recharge_history.idAccountConfig = account_config.idAccountConfig
            INNER JOIN account ON account.idAccount = account_config.idAccount
            INNER JOIN (
                SELECT
                    max(idRechargeHistory) idRh,
                    idAccountConfig,
                    idServices
                FROM
                    recharge_history
                GROUP BY
                    idAccountConfig,
                    idServices
            ) AS maxidRh ON maxidRh.idAccountConfig = recharge_history.idAccountConfig
            AND maxidRh.idRh = recharge_history.idRechargeHistory
            WHERE 
              account.idAllied = {$this->user->Usertype->idAllied}
            AND {$wherein}
            AND {$wheredate}";
    $this->totals = $this->db->fetchAll($sqlcount);       
    $this->modelDataMail2();
  }

  public function getAllReportPlan() {
    $wherein = " 1 = 1 AND ";
    $wheredate = " 1 = 1 ";
    if (isset($this->search->account) && count($this->search->account) >= 1) {
      $in = "";
      for ($i = 0; $i < count($this->search->account); $i++) {
        $in .= $this->search->account[$i] . (( (count($this->search->account) - 1 ) > $i) ? "," : "");
      }
      $wherein = "C.idAccount IN ({$in}) AND ";
    }
    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      //$wheredate = "HistoryPaymentPlan.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
      $wheredate = "history_payment_plan.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $sql = "SELECT
                history_payment_plan.idHistoryPaymentPlan AS idHistoryPaymentPlan,
                account_config.idAccountConfig AS idAccounConf,
                allied.name AS nameAllied,
                C.name AS nameAccount,
                payment_plan.idPaymentPlan AS idPayment,
                payment_plan.name AS namePreviPlan,
                payment_plan.createdBy AS createdBys,
                from_unixtime(history_payment_plan.created) AS dateChange,
                (select 
                        payment_plan.name
                 from 
                    payment_plan
                                inner join
                        account AS ac on ac.idPaymentPlan = payment_plan.idPaymentPlan
                     where
                        ac.idAllied = {$this->user->Usertype->idAllied} AND ac.idAccount = C.idAccount
                    )
                AS nameCurrentPlan
            FROM
                history_payment_plan
                    INNER JOIN
                account_config ON account_config.idAccountConfig = history_payment_plan.idAccountConfig
                    INNER JOIN
                payment_plan ON payment_plan.idPaymentPlan = history_payment_plan.idPaymentPlan
                    INNER JOIN
                account AS C ON C.idAccount =  account_config.idAccount
                    INNER JOIN
                allied ON allied.idAllied = C.idAllied
            WHERE
                allied.idAllied = {$this->user->Usertype->idAllied} AND
                {$wherein} 
                {$wheredate}
            LIMIT {$this->limit}
            OFFSET {$this->page}";

    $this->data = $this->db->fetchAll($sql);

//    $totalRow = count($this->data);
//    $this->report = array("total" => $totalRow, "total_pages" => ceil($totalRow / $this->limit));
//    $arr = array();
//    foreach ($this->data as $key => $value) {
//      $obj = new \stdClass();
//      $obj->$key = $value;
//      array_push($arr, $value);
//    }
//    array_push($this->report, ["items" => $arr]);

    $sqlCount = "SELECT
                    history_payment_plan.idHistoryPaymentPlan as count
                FROM
                    history_payment_plan
                        INNER JOIN
                    account_config ON account_config.idAccountConfig = history_payment_plan.idAccountConfig
                        INNER JOIN
                    payment_plan ON payment_plan.idPaymentPlan = history_payment_plan.idPaymentPlan
                        INNER JOIN
                    account AS C ON C.idAccount =  account_config.idAccount
                        INNER JOIN
                    allied ON allied.idAllied = C.idAllied
                WHERE
                    allied.idAllied = {$this->user->Usertype->idAllied} AND 
                   {$wherein} 
                   {$wheredate}";
    $this->totals = $this->db->fetchAll($sqlCount);

    $this->modelDataMail();
  }

  public function stringMonth($month) {
    $string = "";
    switch ($month) {
      case 1:
        $string = "Enero";
        break;
      case 2:
        $string = "Febrero";
        break;
      case 3:
        $string = "Marzo";
        break;
      case 4:
        $string = "Abril";
        break;
      case 5:
        $string = "Mayo";
        break;
      case 6:
        $string = "Junio";
        break;
      case 7:
        $string = "Julio";
        break;
      case 8:
        $string = "Agosto";
        break;
      case 9:
        $string = "Septiembre";
        break;
      case 10:
        $string = "Octubre";
        break;
      case 11:
        $string = "Noviembre";
        break;
      case 12:
        $string = "Diciembre";
        break;
    }
    return $string;
  }

  function setPage($page) {
    $this->page = $page;
  }

  function getReport() {
    return $this->report;
  }

  function getAccount() {
    return $this->account;
  }

  function setSearch($search) {
    $this->search = $search;
  }

  function getGraph() {
    return $this->graph;
  }

  function getInfoDetail() {
    return $this->infoDetail;
  }

  function getSubaccount() {
    return $this->subaccount;
  }

  function getUsers() {
    return $this->users;
  }

  public function getAllMailValidation($page, $export) {

    if ($this->page != 0) {
      $this->page = $this->page + 1;
    }
    if ($this->page > 1) {
      $this->page = ($this->page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    //EXPORT EXCEL
    $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    if ($export) {
      $limit = 0;
    }

    $modelSubAccount = new \Subaccount();
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));

    if (isset($this->search->dateFinal) && isset($this->search->dateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
    }
    //Data Account
    $conditionSql = array(
        "conditions" => "idAllied = ?0",
        "columns" => array("idAccount", "name"),
        "bind" => array($idAllied)
    );
    $resultAccount = \Account::find($conditionSql);

    $arrayAccount = array();
    foreach ($resultAccount as $key => $value) {
      $arrayAccount[] = $value;
    }

    $whereIdAccount = "1 = 1";
    if ($this->search->account) {
      $whereIdAccount = "Account.idAccount IN(" . implode(',', $this->search->account) . ")";
    }

    //Data SubAccount
    $dataSubAccount = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "Account.idAccount AS idAccounts",
                        "Subaccount.idSubaccount AS idSubaccount",
                        "Subaccount.name AS name"
            ])
            ->from('Account')
            ->innerjoin("Subaccount", "Subaccount.idAccount = Account.idAccount")
            ->where("Account.idAllied = " . $idAllied)
            ->andWhere($whereIdAccount)
            ->getQuery()
            ->execute();

    $arrayDataSubAccount = [];
    foreach ($dataSubAccount as $key => $value) {
      $arrayDataSubAccount[] = $value;
    }

    //Mix data account with subaccount
    foreach ($arrayAccount as $key => $value) {
      foreach ($arrayDataSubAccount as $key2 => $value2) {
        if ($value['idAccount'] == $value2->idAccounts) {
          $arrayAccount[$key]->nameSubAccount = $value2->name;
        }
      }
    }

    //Data Accounts in Allied
    $data = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "Account.idAccount AS idAccounts"
            ])
            ->from('Account')
            ->innerjoin("Allied", "Allied.idAllied = Account.idAllied")
            ->where("Allied.idAllied = " . $idAllied)
            ->andWhere($whereIdAccount)
            ->getQuery()
            ->execute();

    $arrayIdsAccounts = [];
    foreach ($data as $key => $value) {
      $arrayIdsAccounts[] = (int) $value->idAccounts;
    }

    $conditions = array(
        "conditions" => array(
            'idAccount' => array(
                '$in' => $arrayIdsAccounts
            )
        ),
        "skip" => $this->page,
        "limit" => $limit
    );

    $conditionCount = array(
        "conditions" => array(
            'idAccount' => array(
                '$in' => $arrayIdsAccounts
            )
        )
    );

    $tamanioCategorie = count($this->search->categorie);
    if ($tamanioCategorie > 0) {
      $arrayCategories = array('$in' => $this->search->categorie);
      $conditions["conditions"]["score"] = $arrayCategories;
      $conditionCount["conditions"]["score"] = $arrayCategories;
    }
    //Data DataValidation in mongo
    if ($dateInitial != "" && $dateFinal != "") {
      $arrayCreated = array('$gte' => $dateInitial, '$lt' => $dateFinal);
      $conditions["conditions"]["created"] = $arrayCreated;
      $conditionCount["conditions"]["created"] = $arrayCreated;
    }
    if ($this->search->email != "") {
      $conditions["conditions"]["email"] = $this->search->email;
      $conditionCount["conditions"]["email"] = $this->search->email;
    }

    $respuestMongo = \DeliverableEmail::find($conditions);
    $respuestCountMongo = \DeliverableEmail::Count($conditionCount);
    $arrayData = array();
    foreach ($respuestMongo as $key => $value) {
      $arrayData[] = $value;
    }

    //Organize Data datavalidation
    foreach ($arrayData as $key2 => $value2) {
      $arrayInfo = $value2->idAccount;
      foreach ($arrayAccount as $key => $value) {
        $valueFind = $value['idAccount'];
        if (in_array($valueFind, $arrayInfo)) {
          $value2->account = $value['name'];
          $value2->subaccount = $value['nameSubAccount'];
        }
      }
      if ($value2->score == 'A') {
        $value2->evaluation = 'Muy bueno';
      } else if ($value2->score == 'B') {
        $value2->evaluation = 'Bueno';
      } else if ($value2->score == 'D') {
        $value2->evaluation = 'No hay información';
      } else if ($value2->score == 'F') {
        $value2->evaluation = 'Correo malo';
      } else {
        $value2->evaluation = 'Indefinido';
      }
    }



    $totalValidations = count($arrayData);
    $total_pages = ceil(($respuestCountMongo) / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    return array('data' => $arrayData,
        'totalValidations' => $respuestCountMongo,
        'total_pages' => $total_pages,
        'total_rows' => $totalValidations);
  }

  /**
   * This method download excel report
   *
   * @param[]
   *
   * @return
   */
  public function downloadMailValidation() {
    $export = true;
    $page = 0;
    $dataExport = $this->getAllMailValidation($page, $export);
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));
    $conditionSql = array(
        "conditions" => "idAllied = ?0",
        "columns" => array("idAllied", "name"),
        "bind" => array($idAllied)
    );
    $resultAccount = \Allied::findFirst($conditionSql);

    $dataExport['nameAllied'] = $resultAccount['name'];
    $dataExcel = $dataExport;
    return $this->modelDataInfoDetailEmailValidation($dataExcel);
  }

  /**
   * 
   * @param int $idAllied
   * @return obj
   */
  public function mailExport($idAllied, $title) {
    try {
      $arrayMail = $this->dataMail($idAllied);
      $objPHPExcel = $this->phpExcelMail();
      $this->phpExcelWorksheet($objPHPExcel);
      $this->findMail($objPHPExcel, $arrayMail);
      return $this->downloadExcel($objPHPExcel, $title);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @param int $idAllied
   * @return obj
   */
  public function smsExport($idAllied, $title) {
    try {
      $arraySms = $this->dataSms($idAllied);
      $objPHPExcel = $this->phpExcelSms();
      $this->phpExcelWorksheet($objPHPExcel);
      $this->findSms($objPHPExcel, $arraySms);
      return $this->downloadExcel($objPHPExcel, $title);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @param int $idAllied
   * @return array
   */
  public function dataMail($idAllied) {
    $arrayMail = $this->modelsManager->createBuilder()
            ->columns(["Mail.scheduleDate AS scheduleDate", "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount", "Mail.name AS name", "Mail.quantitytarget AS ctotal",
                "Mail.quantitytarget AS ctotal", "Mail.uniqueOpening AS copen", "Mail.bounced AS cbounced", "Mail.spam AS cspam"])
            ->from('Mail')
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Mail.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $idAllied . " AND Mail.status = 'sent' ")
            ->getQuery()
            ->execute();
    //Retornamos la consulta de la base de datos
    return $arrayMail;
  }

  /**
   * 
   * @param int $idAllied
   * @return array
   */
  public function dataSms($idAllied) {
    $arraySms = $this->modelsManager->createBuilder()
            ->columns(["Sms.name AS nameSms", "Sms.startdate",
                "Account.idAccount", "Account.name AS nameAccount",
                "Subaccount.name AS nameSubaccount",
                "COUNT(Smslote.idSmslote) AS target"])
            ->from('Sms')
            ->leftJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
            ->where("Account.idAllied = " . $idAllied .
                    " AND Sms.status = 'sent' AND Smslote.status = 'sent'")
            ->groupBy(["1", "2", "3", "4", "5"])
            ->getQuery()
            ->execute();
    return $arraySms;
  }

  /**
   * 
   * @return \PHPExcel
   */
  public function phpExcelMail() {
    //Instanciar la clase de PhpExcel
    $objPHPExcel = new \PHPExcel();
    //Creamos este for para que recorra la cantidad de celdas de acuerdo a los campos de los titulos
    for ($i = 65; $i <= 75; $i++) {
      //Deacuerdo a la cantidad de campos de los titulos calcula el ancho de la columna 
      $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle("Reporte de Mail");
    $objPHPExcel->getActiveSheet()->getStyle('A8:J8')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A8', "Fecha:");
    $objPHPExcel->getActiveSheet()->setCellValue('B8', "Cuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('C8', "Nombre de la cuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('D8', "Subcuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('E8', "Nombre del envio:");
    $objPHPExcel->getActiveSheet()->setCellValue('F8', "Enviados:");
    $objPHPExcel->getActiveSheet()->setCellValue('G8', "Aperturas:");
    $objPHPExcel->getActiveSheet()->setCellValue('H8', "Desuscritos:");
    $objPHPExcel->getActiveSheet()->setCellValue('I8', "Rebotes:");
    $objPHPExcel->getActiveSheet()->setCellValue('J8', "Spam:");
    //Retornamos el objecto de la clase PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @return \PHPExcel
   */
  public function phpExcelSms() {
    //Instanciar la clase de PhpExcel
    $objPHPExcel = new \PHPExcel();
    //Creamos este for para que recorra la cantidad de celdas de acuerdo a los campos de los titulos
    for ($i = 65; $i <= 70; $i++) {
      //Deacuerdo a la cantidad de campos de los titulos calcula el ancho de la columna 
      $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle("Reporte de Mail");
    $objPHPExcel->getActiveSheet()->getStyle('A8:J8')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A8', "Fecha:");
    $objPHPExcel->getActiveSheet()->setCellValue('B8', "Cuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('C8', "Nombre de la cuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('D8', "Subcuenta:");
    $objPHPExcel->getActiveSheet()->setCellValue('E8', "Nombre del envio:");
    $objPHPExcel->getActiveSheet()->setCellValue('F8', "Enviados:");
    //Retornamos el objecto de la clase PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @param type $objPHPExcel
   * @return \PHPExcel_Worksheet_Drawing
   */
  public function phpExcelWorksheet($objPHPExcel) {
    //Instaciamos la Clase PHPExcel_Worksheet_Drawing
    $objDrawing = new \PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('aio');
    $objDrawing->setDescription('aio');
    //Colocamos la imagen institucional
    $objDrawing->setPath('./images/sigma-logo.png');
    //Esta es la celda donde la imagen va aparecer
    $objDrawing->setCoordinates('A1');
    $objDrawing->getShadow()->setVisible(true);
    $objDrawing->getShadow()->setDirection(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    //Retornamos el objecto de la clase PHPExcel_Worksheet_Drawing
    return $objDrawing;
  }

  /**
   * 
   * @param PHPExcel $objPHPExcel
   * @param array $arrayMail
   * @return PHPExcel
   */
  public function findMail($objPHPExcel, $arrayMail) {
    //Creamos una variable y le asignamos la cantidad de filas donde van a aparecer la informacion de Mail
    $rowMail = 9;
    //Recorremos el array del Mail
    foreach ($arrayMail as $data) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowMail, $data['scheduleDate']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowMail, $data['idAccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowMail, $data['nameAccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowMail, $data['nameSubaccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowMail, $data['name']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowMail, $data['ctotal']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowMail, $data['copen']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowMail, 1);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowMail, $data['cbounced']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowMail, $data['cspam']);
      $rowMail++;
    }
    //Retornamos el objecto de clase PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @param PHPExcel $objPHPExcel
   * @param array $arraySms
   * @return PHPExcel
   */
  public function findSms($objPHPExcel, $arraySms) {
    //Creamos una variable y le asignamos la cantidad de filas donde van a aparecer la informacion de Mail
    $rowSms = 9;
    //Recorremos el array del Mail
    foreach ($arraySms as $data) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowSms, $data['scheduleDate']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowSms, $data['idAccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowSms, $data['nameAccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowSms, $data['nameSubaccount']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowSms, $data['name']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowSms, $data['ctotal']);
      $rowSms++;
    }
    //Retornamos el objecto de clase PhpExcel
    return $objPHPExcel;
  }

  /**
   * 
   * @param PHPExcel $objPHPExcel
   * @return \Phalcon\Http\Response
   */
  public function download($objPHPExcel) {
    $name = "reporte.xlsx";
    //Instaciamos la Clase PHPExcel_Writer_Excel2007
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $this->logger->log("Entra 6");
    $objWriter->save($name);
    //Instaciamos la Clase Response
    $response = new \Phalcon\Http\Response();
    $this->logger->log("Entra 7");
    $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->setHeader('Content-Disposition', 'attachment;filename="' . $name . '"');
    $response->setHeader('Cache-Control', 'max-age=0');
    $response->setHeader('Cache-Control', 'max-age=1');
    $response->setContent(file_get_contents($name));
    unlink($name);
    //Retornamos el objecto de clase Response
    return $response;
  }

  /**
   * 
   * @param type $page
   * @param type $export
   * @return type
   */
  public function getAllMailBounced($page, $export) {

    if ($this->page != 0) {
      $this->page = $this->page + 1;
    }
    if ($this->page > 1) {
      $this->page = ($this->page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    //EXPORT EXCEL
    $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    if ($export) {
      $limit = 0;
    }

    $modelSubAccount = new \Subaccount();
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));

    if (isset($this->search->dateFinalTwo) && isset($this->search->dateInitialTwo)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateInitialTwo . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinalTwo . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
    }
    //Data Account
    $conditionSql = array(
        "conditions" => "idAllied = ?0",
        "columns" => array("idAccount", "name"),
        "bind" => array($idAllied)
    );
    $resultAccount = \Account::find($conditionSql);

    $arrayAccount = array();
    foreach ($resultAccount as $key => $value) {
      $arrayAccount[] = $value;
    }

    $whereIdAccount = "1 = 1";
    if ($this->search->account) {
      $whereIdAccount = "Account.idAccount IN(" . implode(',', $this->search->account) . ")";
    }

    //Data SubAccount
    $dataSubAccount = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "Account.idAccount AS idAccounts",
                        "Subaccount.idSubaccount AS idSubaccount",
                        "Subaccount.name AS name"
            ])
            ->from('Account')
            ->innerjoin("Subaccount", "Subaccount.idAccount = Account.idAccount")
            ->where("Account.idAllied = " . $idAllied)
            ->andWhere($whereIdAccount)
            ->getQuery()
            ->execute();

    $arrayDataSubAccount = [];
    foreach ($dataSubAccount as $key => $value) {
      $arrayDataSubAccount[] = $value;
    }

    //Mix data account with subaccount
    foreach ($arrayAccount as $key => $value) {
      foreach ($arrayDataSubAccount as $key2 => $value2) {
        if ($value['idAccount'] == $value2->idAccounts) {
          $arrayAccount[$key]->nameSubAccount = $value2->name;
        }
      }
    }

    //Data Accounts in Allied
    $data = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "Account.idAccount AS idAccounts"
            ])
            ->from('Account')
            ->innerjoin("Allied", "Allied.idAllied = Account.idAllied")
            ->where("Allied.idAllied = " . $idAllied)
            ->andWhere($whereIdAccount)
            ->getQuery()
            ->execute();

    $arrayIdsAccounts = [];
    foreach ($data as $key => $value) {
      $arrayIdsAccounts[] = (int) $value->idAccounts;
    }

    $letterFil = "F";
    $conditions = array(
        "conditions" => array(
            'idAccount' => array(
                '$in' => $arrayIdsAccounts
            ),
            'code' => $letterFil
        ),
        "skip" => $this->page,
        "limit" => $limit
    );

    $conditionCount = array(
        "conditions" => array(
            'idAccount' => array(
                '$in' => $arrayIdsAccounts
            ),
            'code' => $letterFil
        )
    );
    /* Se comenta debido a que todos los correos de data validation son categoria F */
    /* $tamanioCategorie = count($this->search->categorie);
      if ($tamanioCategorie > 0) {
      $arrayCategories = array('$in' => $this->search->categorie);
      $conditions["conditions"]["code"] = $arrayCategories;
      $conditionCount["conditions"]["code"] = $arrayCategories;
      } */
    //Data DataValidation in mongo
    if ($dateInitial != "" && $dateFinal != "") {
      $arrayCreated = array('$gte' => $dateInitial, '$lt' => $dateFinal);
      $conditions["conditions"]["created"] = $arrayCreated;
      $conditionCount["conditions"]["created"] = $arrayCreated;
    }
    if ($this->search->email != "") {
      $conditions["conditions"]["email"] = $this->search->email;
      $conditionCount["conditions"]["email"] = $this->search->email;
    }

    $respuestBouncedMail = \Bouncedmail::find($conditions);
    $respuestCountMongo = \Bouncedmail::Count($conditionCount);
    $arrayData = array();
    foreach ($respuestBouncedMail as $key => $value) {
      $arrayData[] = $value;
    }

    //Organize Data datavalidation
    foreach ($arrayData as $key2 => $value2) {
      $arrayInfo = $value2->idAccount;
      foreach ($arrayAccount as $key => $value) {
        $valueFind = $value['idAccount'];
        if (in_array($valueFind, $arrayInfo)) {
          $value2->account = $value['name'];
          $value2->subaccount = $value['nameSubAccount'];
        }
      }
      if ($value2->code == 'F') {
        $value2->evaluation = 'Correo no valido';
      } else {
        $value2->evaluation = 'Indefinido';
      }
    }

    $totalValidations = count($arrayData);
    $total_pages = ceil(($respuestCountMongo) / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    return array('data' => $arrayData,
        'totalValidations' => $respuestCountMongo,
        'total_pages' => $total_pages,
        'total_rows' => $totalValidations);
  }

  /**
   * This method download excel report
   *
   * @param[]
   *
   * @return
   */
  public function downloadGetAllMailBounced() {
    $export = true;
    $page = 0;
    $dataExport = $this->getAllMailBounced($page, $export);
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));
    $conditionSql = array(
        "conditions" => "idAllied = ?0",
        "columns" => array("idAllied", "name"),
        "bind" => array($idAllied)
    );
    $resultAccount = \Allied::findFirst($conditionSql);

    $dataExport['nameAllied'] = $resultAccount['name'];
    $dataExcel = $dataExport;
    return $this->modelDataInfoDetailEmailBounced($dataExcel);
  }

  public function modelDataInfoDetailEmailBounced($data) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    //$excel->basicPropertiesSms();
    $excel->setData($data);
    //$excel->setInfoDetailSms();
    $excel->generatedInfoDetailEmailBounced();
    $nameExcel = "reporte_de_correos_no_validados";
    return $excel->downloadExcel($nameExcel);
  }

  /**
   * 
   * @param PHPExcel $objPHPExcel
   * @return \Phalcon\Http\Response
   */
  public function downloadExcel($objPHPExcel, $title) {

    $nameFull = str_replace(" ", "_", $title) . "_" . date('Y-m-d') . ".xlsx";
    $name = $nameFull;
    //Instaciamos la Clase PHPExcel_Writer_Excel2007
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($name);
    //Instaciamos la Clase Response
    $response = new \Phalcon\Http\Response();
    $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->setHeader('Content-Disposition', 'attachment;filename="' . $name . '"');
    $response->setHeader('Cache-Control', 'max-age=0');
    $response->setHeader('Cache-Control', 'max-age=1');
    $response->setContent(file_get_contents($name));
    unlink($name);
    //Retornamos el objecto de clase Response
    return $response;
  }

  public function getAllReportSmsxEmail($page, $filterAll) {
    (($filterAll['initial'] > 0) ? $page = ($filterAll['initial'] * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : $filterAll['initial'] );
    ($filterAll['filter'] > 0 ? $filterData = json_decode(json_encode($filterAll['filter'])) : $filterData = new \stdClass());
    $filDate = "";
    if (isset($filterData->valuedateInitial) && isset($filterData->valuedateFinal)) {
      if ($filterData->valuedateInitial != "" && $filterData->valuedateFinal != "") {
        $initial = strtotime($filterData->valuedateInitial);
        $end = strtotime($filterData->valuedateFinal);
        if ($initial > $end) {
          throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final. ');
        }
        $filDate .= " AND created BETWEEN '{$initial}' AND '{$end}'";
      }
    }

    $conditions = array(
        "conditions" => "idSubaccount = ?0 {$filDate}",
        "bind" => array($this->user->Usertype->Subaccount->idSubaccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "idSms DESC"
    );
    $sms = \Sms::find($conditions);

    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $smsxemail = \Smsxemail::findFirst(array("conditions" => "idSubaccount = ?0", "bind" => array($this->user->Usertype->Subaccount->idSubaccount)));
    $data = array();
    $i = 0;
    foreach ($sms as $value) {
      $report = \ReportSmsxemail::count(array("conditions" => "idSms = ?0", "bind" => array($value->idSms)));
      if ($report > 0) {
        $smslote = \Smslote::findFirst(array("conditions" => "idSms = ?0", "bind" => array($value->idSms)));
        $data[$i] = array(
            "idSms" => $value->idSms,
            "senderEmail" => $smsxemail->senderEmail,
            "startdate" => $value->startdate,
            "typeShipping" => (($value->target == 1) ? "Uno a uno" : "Uno a muchos"),
            "quantitySms" => $value->target,
            "name" => $value->name,
            "idSmsCategory" => $value->idSmsCategory,
            "notificationEmail" => $value->email,
            "indicative" => $smslote->indicative,
            "phone" => $smslote->phone,
            "message" => $smslote->message,
        );
        $i++;
      }
    }
    $total = count($data);
    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $data
    );
  }

  /**
   * 
   * @param array $sms
   * @return obj
   */
  public function downloadsmxemail($sms) {
    try {
      $objPHPExcel = $this->phpExcelSmsxEmail();
      $this->phpExcelWorksheet($objPHPExcel);
      $this->findData($objPHPExcel, $sms);
      $this->findSmsxEmail($objPHPExcel, $sms);
      return $this->downloadSmsxemail($sms, $objPHPExcel);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while export contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function phpExcelSmsxEmail() {
    $objPHPExcel = new \PHPExcel();
    //Colocar en negrilla los titulos
    $objPHPExcel->getActiveSheet()->getStyle("B18:E18")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
    //Colocamos el titulo de Reporte SMS
    $objPHPExcel->getActiveSheet()->setCellValue("C3", "REPORTE SMS");
    $objPHPExcel->getActiveSheet()->setCellValue("B18", "Codigo del país");
    $objPHPExcel->getActiveSheet()->setCellValue("C18", "Móvil");
    $objPHPExcel->getActiveSheet()->setCellValue("D18", "Mensaje");
    $objPHPExcel->getActiveSheet()->setCellValue("E18", "Estado");
    $objPHPExcel->getActiveSheet()->setCellValue("A7", "Nombre del envió:");
    $objPHPExcel->getActiveSheet()->setCellValue("A8", "Fecha del envió:");
    $objPHPExcel->getActiveSheet()->setCellValue("A9", "Tipo de envió:");
    $objPHPExcel->getActiveSheet()->setCellValue("A10", "Correo del remitente");
    $objPHPExcel->getActiveSheet()->setCellValue("A11", "Cantidad de registros:");
    $objPHPExcel->getActiveSheet()->setCellValue("A12", "Registros repetidos:");
    $objPHPExcel->getActiveSheet()->setCellValue("A13", "Destinatario:");
    $objPHPExcel->getActiveSheet()->setCellValue("A14", "Enviados:");
    $objPHPExcel->getActiveSheet()->setCellValue("A15", "No enviados:");
    $objPHPExcel->getActiveSheet()->getStyle("A17")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue("A17", "Envíos realizados");
    //Retornamos el objecto PhpExcel
    return $objPHPExcel;
  }

  public function findData($objPHPExcel, $sms) {
    $smsSender = \Sms::findFirst(array("conditions" => "idSms = ?0", "bind" => array($sms->idSms)));
    $Smslote = \Smslote::count(array("conditions" => "idSms = ?0 ", "bind" => array($sms->idSms)));
    $sql = "SELECT phone, count(*) as count FROM smslote WHERE idSms = {$sms->idSms} GROUP BY phone HAVING count(*) > 1";
    $sendTotal = $this->db->fetchAll($sql);
    if ($smsSender->type == 'contact') {
      $smsxc = Smsxc::find([["idSms" => $smsSender->idSms]]);
      var_dump($smsxc);
    }
    $objPHPExcel->getActiveSheet()->setCellValue("B7", $smsSender->name);
    $objPHPExcel->getActiveSheet()->setCellValue("B8", $smsSender->startdate);
    $objPHPExcel->getActiveSheet()->setCellValue("B9", (($smsSender->target == 1) ? "Uno a uno" : "Uno a muchos"));
    $objPHPExcel->getActiveSheet()->setCellValue("B10", $smsSender->email);
    $objPHPExcel->getActiveSheet()->setCellValue("B11", $smsSender->target);
    if (!$smsxc) {
      $objPHPExcel->getActiveSheet()->setCellValue("B12", ($sendTotal[0]['count'] ? $sendTotal[0]['count'] : 0));
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (count($sendTotal) ? count($sendTotal) : 1));
      $objPHPExcel->getActiveSheet()->setCellValue("B14", $Smslote);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", ((int) $smsSender->target - $Smslote));
    } else {
      $objPHPExcel->getActiveSheet()->setCellValue("B12", ($sendTotal[0]['count'] ? $sendTotal[0]['count'] : 0));
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (count($smsxc) ? count($smsxc) : 1));
      $objPHPExcel->getActiveSheet()->setCellValue("B14", count($smsxc));
      $objPHPExcel->getActiveSheet()->setCellValue("B15", ((int) $smsSender->target - count($smsxc)));
    }
    return $objPHPExcel;
  }

  public function findSmsxEmail($objPHPExcel, $Sms) {
    for ($i = 65; $i < 71; $i++) {
      $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
    }
    $smslote = \Smslote::find(array("conditions" => "idSms = ?0 ", "bind" => array($Sms->idSms)));
    $rowContacs = 19;
    $rowSmsxEmail = 0;
    if (count($smslote) > 0) {
      foreach ($smslote as $reciver) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowContacs, $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowContacs, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowContacs, $reciver->message);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowContacs, $this->traslateStatusSms($reciver->status));
        $rowContacs++;
        $rowSmsxEmail = $rowContacs;
        unset($smslote);
      }
    } else {
      $rowSmsxEmail = $rowContacs;
    }
    $report = \ReportSmsxemail::findFirst(array("conditions" => "idSms = ?0", "bind" => array($Sms->idSms)));
    $encode = json_decode($report->smsFailed);
    if ($encode) {
      $objPHPExcel->getActiveSheet()->getStyle("F18")->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->setCellValue("F18", "Validación de error");
      foreach ($encode as $reciver) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowSmsxEmail, $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowSmsxEmail, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowSmsxEmail, $reciver->message);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowSmsxEmail, $this->traslateStatusSms($reciver->status));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowSmsxEmail, $reciver->smsFailed);
        $rowSmsxEmail++;
        unset($encode);
      }
    }
    $sms = \Sms::findFirst(array("conditions" => "idSms = ?0 ", "bind" => array($Sms->idSms)));
    if ($sms->type == 'contact') {
      $smsxc = Smsxc::find([["idSms" => $sms->idSms]]);
      foreach ($smsxc as $reciver) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowSmsxEmail, $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowSmsxEmail, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowSmsxEmail, $reciver->message);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowSmsxEmail, $this->traslateStatusSms($reciver->status));
        $rowSmsxEmail++;
        unset($smsxc);
      }
    }
    //Retornamos el objecto PhpExcel
    return $objPHPExcel;
  }

  public function downloadSmsxemail($sms, $objPHPExcel) {
    $modelSms = \Sms::findFirst(array("conditions" => "idSms = ?0", "bind" => array($sms->idSms)));
    //Asignamos el nombre del Sms, la fecha e hora y el de documento.
    $name = $modelSms->name . " " . date('Y-m-d') . ".xlsx";
    $temp_file = "reporte_de_envios_realizados";
    //Instanciamos la clase PHPExcel_Writer_Excel2007
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($temp_file);
    //Instanciamos la clase Response
    $response = new \Phalcon\Http\Response();
    $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->setHeader('Content-Disposition', 'attachment;filename="' . $name . '"');
    $response->setHeader('Cache-Control', 'max-age=0');
    $response->setHeader('Cache-Control', 'max-age=1');
    $response->setContent(file_get_contents($temp_file));
    unlink($temp_file);
    //
    unset($sms);
    //Retornamos el objecto de clase Response
    return $response;
  }

  /**
   * 
   * @param type $page
   * @param type $export
   * @return \Phalcon\Http\Response
   */
  public function getDataSmschannel($page, $export) {

    $fechainiConseg = "";
    $fechaFinConseg = "";
    $dateData = "";
    if (isset($this->search->dateFinal)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . '-' . '01' . ' 00:01:01'));
      $monthF = date('m', strtotime($fechainiConseg));
      $yearF = date('Y', strtotime($fechainiConseg));
      $dayF = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->dateFinal . '-' . $dayF . ' 23:59:59'));
      $dateData = $yearF . '-' . $monthF;
    } else if (!isset($this->search->dateFinal)) {
      $yearActually = date('Y');
      $monthActually = date('m');
      $dayActually = date('d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($yearActually . '-' . $monthActually . '-' . '01' . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($yearActually . '-' . $monthActually . '-' . $dayActually . ' 23:59:59'));
      $dateData = $yearActually . '-' . $monthActually;
    }

    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));

    $sqlSmsChannel = $this->modelsManager->createBuilder()
            ->columns(["COUNT(Adapter.idAdapter) AS countIdAdapter",
                "Adapter.fname AS fname",
                "Adapter.idAdapter"])
            ->from("Allied")
            ->innerJoin("Account", "Account.idAllied = Allied.idAllied")
            ->innerJoin("Subaccount", "Subaccount.idAccount = Account.idAccount")
            ->innerJoin("Sms", "Sms.idSubaccount = Subaccount.idSubaccount")
            ->innerJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->innerJoin("Adapter", "Adapter.idAdapter = Smslote.idAdapter")
            ->where("Allied.idAllied = {$idAllied}")
            ->andWhere("Sms.status = 'sent' ")
            ->andWhere("Sms.startdate BETWEEN '{$fechainiConseg}' AND '{$fechaFinConseg}' ")
            ->groupBy(["3"])
            ->getQuery()
            ->execute();

    $arrayDataAdapter = array();
    foreach ($sqlSmsChannel as $key => $value) {
      $arrayDataAdapter[] = array(
          "idAdapter" => $value['idAdapter'],
          "countIdAdapter" => (int) $value['countIdAdapter'],
          "fname" => $value["fname"]
      );
    }

    $sqlSubCuentaXAllied = $this->modelsManager->createBuilder()
            ->columns(["Allied.idAllied AS idAllieds",
                "Subaccount.idSubaccount AS idSubaccount"])
            ->from("Allied")
            ->innerJoin("Account", "Account.idAllied = Allied.idAllied")
            ->innerJoin("Subaccount", "Subaccount.idAccount = Account.idAccount")
            ->where("Allied.idAllied = {$idAllied}")
            ->orderBy("2")
            ->getQuery()
            ->execute();

    $arrayIdSubAccounts = array();
    foreach ($sqlSubCuentaXAllied as $key => $value) {
      $arrayIdSubAccounts[] = $value['idSubaccount'];
    }

    $collectionSmsXc = array(
        array(
            '$match' => array(
                'idSubaccount' => array(
                    '$in' => $arrayIdSubAccounts
                ),
                'status' => 'sent'
            ),
        ),
        array(
            '$group' => array(
                '_id' => '$idAdapter',
                'total' => array('$sum' => 1)
            )
        ),
        array(
            '$project' => array(
                'countIdAdapter' => '$total',
                'idAdapter' => '$_id'
            )
        )
    );

    if (isset($this->search->dateFinal)) {
      $arrayCreated = array('$gte' => $fechainiConseg, '$lt' => $fechaFinConseg);
      $collectionSmsXc[0]['$match']['scheduleDate'] = $arrayCreated;
    }

    $respuestSmsXc = \Smsxc::aggregate($collectionSmsXc);
    $arrayRespuestMongo = array();

    foreach ($respuestSmsXc['result'] as $key => $value) {
      $arrayRespuestMongo[] = $value;
    }

    $totalSmsChannel = 0;
    $arrayColumnsChannel = array('FECHA');
    $arrayRespuestFinal = array();

    $sizeMysqlAdapter = count($arrayDataAdapter);
    $sizeMongoAdapter = count($arrayRespuestMongo);
    if ($sizeMysqlAdapter > 0 && $sizeMongoAdapter > 0) {
      foreach ($arrayDataAdapter as $key => $value) {
        foreach ($arrayRespuestMongo as $key2 => $value2) {
          if ($value['idAdapter'] == $value2['idAdapter']) {
            $value["countIdAdapter"] += $value2["countIdAdapter"];
          }
        }
        $arrayRespuestFinal[] = $value;
        $arrayColumnsChannel[] = $value['fname'];
        $totalSmsChannel = $value['countIdAdapter'] + $totalSmsChannel;
      }
      $arrayRespuestFinal[] = array(
          "idAdapter" => (string) 0,
          "countIdAdapter" => (int) $totalSmsChannel,
          "fname" => (string) "TOTAL DE SMS"
      );
      $arrayColumnsChannel[] = "TOTAL DE SMS";
    } else if ($sizeMysqlAdapter <= 0 && $sizeMongoAdapter > 0) {
      $arrIdAdapter = array();
      foreach ($arrayRespuestMongo as $key => $value) {
        $arrIdAdapter[] = $value['idAdapter'];
      }
      //$sqlColumnsAdapters = \Smslote::find(["conditions"=>"Adapter In (?0)", "bind"=>array(0=>$arrIdAdapter)]);
      $whereArrayIdAdapter = "Adapter.idAdapter IN(" . implode(',', $arrIdAdapter) . ")";
      $sqlSubCuentaXAllied = $this->modelsManager->createBuilder()
              ->columns(["Adapter.idAdapter AS idAdapter",
                  "Adapter.fname AS fname"])
              ->from("Adapter")
              ->where($whereArrayIdAdapter)
              ->getQuery()
              ->execute();

      $arrayNamesAdapters = array();
      foreach ($sqlSubCuentaXAllied as $key => $value) {
        $arrayNamesAdapters[] = $value;
      }

      foreach ($arrayNamesAdapters as $key => $value) {
        $arrayColumnsChannel[] = $value['fname'];
      }

      foreach ($arrayRespuestMongo as $key2 => $value2) {
        $arrayRespuestFinal[] = $value2;
        $intAux = $value2['countIdAdapter'];
        $totalSmsChannel += $intAux;
      }

      $arrayRespuestFinal[] = array(
          "idAdapter" => (string) 0,
          "countIdAdapter" => (int) $totalSmsChannel,
          "fname" => (string) "TOTAL DE SMS"
      );
      $arrayColumnsChannel[] = "TOTAL DE SMS";
    } else if ($sizeMysqlAdapter > 0 && $sizeMongoAdapter <= 0) {
      foreach ($arrayDataAdapter as $key => $value) {
        $arrayRespuestFinal[] = $value;
        $arrayColumnsChannel[] = $value['fname'];
        $totalSmsChannel = $value['countIdAdapter'] + $totalSmsChannel;
      }
      $arrayRespuestFinal[] = array(
          "idAdapter" => (string) 0,
          "countIdAdapter" => (int) $totalSmsChannel,
          "fname" => (string) "TOTAL DE SMS"
      );
      $arrayColumnsChannel[] = "TOTAL DE SMS";
    }

    $arrayColumns = array_unique($arrayColumnsChannel);

    $arrayRespuest = ['data' => $arrayRespuestFinal,
        'columnsChannel' => $arrayColumns,
        'dataTotalChannel' => $arrayRespuestFinal,
        'totalValidations' => $totalSmsChannel,
        'dateInfo' => $dateData];
    return $arrayRespuest;
  }

  public function traslateStatusSms($status) {
    $statusSpanish = "";
    switch ($status) {
      case "sent":
        $statusSpanish = "Enviado";
        break;
      case "canceled":
        $statusSpanish = "Cancelado";
        break;
      case "undelivered":
        $statusSpanish = "No enviado";
        break;
      case "scheduled":
        $statusSpanish = "Programado";
        break;
    }
    return $statusSpanish;
  }

  public function getSmsByDestinataries($name, $phone, $dateInitial, $dateEnd, $type, $paramForNotLimit) {
//    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $idSubaccount = $this->user->usertype->subaccount->idSubaccount;

    if ($type == "loteCsv") {


      (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
      $where = "1 = 1 AND Smslote.status in ('sent','undelivered') ";
      if (isset($name) && $name != "") {
        $where .= "AND Sms.name LIKE '%" . $name . "%' ";
      }
      if (isset($phone) && $phone != "") {
        $where .= "AND Smslote.phone LIKE '%" . $phone . "%' ";
      }
      if (isset($dateInitial) && isset($dateEnd)) {
        if (strtotime($dateEnd) < strtotime($dateInitial)) {
          throw new \InvalidArgumentException("La fecha final no puede ser inferior a la inicial");
        } else {
          $where .= " AND Sms.startdate BETWEEN '{$dateInitial} 00:00:00' AND '{$dateEnd} 23:59:59'";
        }
      }


      $modelManager = \Phalcon\DI::getDefault()->get('modelsManager');
      $dataSmsByDestinataries = $modelManager->createBuilder()
              ->columns(["Smslote.message AS Mensaje", "Smslote.phone AS Celular ", "Sms.startdate AS Fecha", "Sms.name AS Nombre", "Smslote.status AS Estado", "Smslote.response AS Respuesta", "Smslote.messageCount AS MessageCount" /* "count(Smslote.idSmslote) as Cantidad" */])
              ->from('Smslote')
              ->innerJoin("Sms", "Sms.idSms = Smslote.idSms")
//            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
//            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
//            ->where("Sms.startdate BETWEEN '2017-11-07 12:00' AND '2017-11-07 18:00'")
              ->where("Sms.idSubaccount = {$idSubaccount}")
//            ->andWhere("Smslote.status = 'sent' OR Smslote.status = 'undelivered'")
              ->andWhere($where)
//            ->groupBy([ "1","2","3"])
              ->orderBy("Fecha DESC")
//              ->groupBy(["Fecha", "Celular"])
              ->limit($this->limit)
              ->offset($this->page)
              ->getQuery()
              ->execute();
      $totalDataSmsByDestinataries = $modelManager->createBuilder()
              ->columns(array('count' => 'COUNT(Smslote.idSmslote)'))
              ->from('Smslote')
              ->innerJoin("Sms", "Sms.idSms = Smslote.idSms")
//            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
//            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
//            ->where("Sms.startdate BETWEEN '2017-11-07 12:00' AND '2017-11-07 18:00'")
              ->where("Sms.idSubaccount = {$idSubaccount}")
//            ->andWhere("Smslote.status = 'sent' OR Smslote.status = 'undelivered'")
              ->andWhere($where)
//            ->groupBy([ "1","2","3"])
//            ->groupBy([ "Fecha", "Celular"])     
              ->getQuery()
              ->execute();

      if ($paramForNotLimit == 1) {
        $modelManager = \Phalcon\DI::getDefault()->get('modelsManager');
        $dataSmsByDestinataries = $modelManager->createBuilder()
                ->columns(["Smslote.message AS Mensaje", "Smslote.phone AS Celular ", "Sms.startdate AS Fecha", "Sms.name AS Nombre", "Smslote.status AS Estado", "Smslote.response AS Respuesta", "Smslote.messageCount AS MessageCount"/* "count(Smslote.idSmslote) as Cantidad" */])
                ->from('Smslote')
                ->innerJoin("Sms", "Sms.idSms = Smslote.idSms")
//            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
//            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
//            ->where("Sms.startdate BETWEEN '2017-11-07 12:00' AND '2017-11-07 18:00'")
                ->where("Sms.idSubaccount = {$idSubaccount}")
//            ->andWhere("Smslote.status = 'sent' OR Smslote.status = 'undelivered'")
                ->andWhere($where)
//            ->groupBy([ "1","2","3"])
                ->orderBy("Fecha DESC")
//              ->groupBy(["Fecha", "Celular"])
//              ->offset($this->page)
                ->getQuery()
                ->execute();
        $totalDataSmsByDestinataries = $modelManager->createBuilder()
                ->columns(array('count' => 'COUNT(Smslote.idSmslote)'))
                ->from('Smslote')
                ->innerJoin("Sms", "Sms.idSms = Smslote.idSms")
//            ->leftJoin("Subaccount", "Subaccount.idSubaccount = Sms.idSubaccount")
//            ->leftJoin("Account", "Account.idAccount = Subaccount.idAccount")
//            ->where("Sms.startdate BETWEEN '2017-11-07 12:00' AND '2017-11-07 18:00'")
                ->where("Sms.idSubaccount = {$idSubaccount}")
//            ->andWhere("Smslote.status = 'sent' OR Smslote.status = 'undelivered'")
                ->andWhere($where)
//            ->groupBy([ "1","2","3"])
//            ->groupBy([ "Fecha", "Celular"])     
                ->getQuery()
                ->execute();
      }

      foreach ($dataSmsByDestinataries as $key => $value) {
        $arrayDataSmsByDestinataries[] = array(
            "messageCount" => $value['MessageCount'],
            "message" => $value['Mensaje'],
            "phone" => $value['Celular'],
            "date" => $value["Fecha"],
//          "quantity" => $value["Cantidad"],
//          "status" => $value["Estado"],
            "status" => (($value["Respuesta"] == "0: Accepted for delivery" || $value["Respuesta"] == "PENDING_ENROUTE") ? "sent" : "undelivered"),
            "name" => $value["Nombre"]
        );
      }
      return ["data" => $arrayDataSmsByDestinataries,
          "totals" => (int) $totalDataSmsByDestinataries[0]['count'],
          "page" => ceil((int) $totalDataSmsByDestinataries[0]['count'] / $this->limit),
          "type" => $type
      ];
    } else if ($type == "contact") {
      (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
      $stringRegexName = new \MongoRegex("/^{$name}/i");
      $stringRegexPhone = new \MongoRegex("/^{$phone}/i");

      $arrayDataSmsByDestinatariesMongo = array(
          "conditions" => array(
              "idSubaccount" => (string) $idSubaccount
          ),
          "fields" => array(
              "scheduleDate" => true,
              "phone" => true,
              "smsName" => true,
              "message" => true,
              "status" => true,
              "response" => true
          ),
          "skip" => $this->page,
          "limit" => $this->limit,
          "sort" => array(
              "scheduleDate" => -1
          )
      );

      $arrayDataSmsByDestinatariesMongo['conditions']['status'] = array(
          '$in' => array(
              "sent",
              "undelivered"
          )
      );

      $arrayTotalMongo = array(
          "conditions" => array(
              "idSubaccount" => (string) $idSubaccount
          ),
          "fields" => array(
              "scheduleDate" => true,
              "phone" => true,
              "smsName" => true,
              "message" => true,
              "status" => true,
              "response" => true
          )
      );

      $arrayTotalMongo['conditions']['status'] = array(
          '$in' => array(
              "sent",
              "undelivered"
          )
      );

      if ($paramForNotLimit == 1) {
        $arrayDataSmsByDestinatariesMongo = array(
            "conditions" => array(
                "idSubaccount" => (string) $idSubaccount
            ),
            "fields" => array(
                "scheduleDate" => true,
                "phone" => true,
                "smsName" => true,
                "message" => true,
                "status" => true,
                "response" => true
            ),
            "sort" => array(
                "scheduleDate" => -1
            )
        );

        $arrayDataSmsByDestinatariesMongo['conditions']['status'] = array(
            '$in' => array(
                "sent",
                "undelivered"
            )
        );
      }
      if (isset($name) && $name != "") {
        $arrayDataSmsByDestinatariesMongo['conditions']['smsName'] = $stringRegexName;
        $arrayTotalMongo['conditions']['smsName'] = $stringRegexName;
      }
      if (isset($phone) && $phone != "") {
        $arrayDataSmsByDestinatariesMongo['conditions']['phone'] = $stringRegexPhone;
        $arrayTotalMongo['conditions']['phone'] = $stringRegexPhone;
      }
      if (isset($dateInitial) && isset($dateEnd)) {
        if (strtotime($dateEnd) < strtotime($dateInitial)) {
          throw new \InvalidArgumentException("La fecha final no puede ser inferior a la inicial");
        } else {
          $arrayDataSmsByDestinatariesMongo['conditions']['scheduleDate'] = array(
              '$gte' => "{$dateInitial} 00:00:00",
              '$lt' => "{$dateEnd} 23:59:59"
          );
          $arrayTotalMongo['conditions']['scheduleDate'] = array(
              '$gte' => "{$dateInitial} 00:00:00",
              '$lt' => "{$dateEnd} 23:59:59"
          );
        }
      }
      $dataSmsByDestinatariesMongo = \Smsxc::find($arrayDataSmsByDestinatariesMongo);
      $totalDataMongo = \Smsxc::count($arrayTotalMongo);
      $arrayDataSmsByDestinatariesMongo1 = array();
      foreach ($dataSmsByDestinatariesMongo as $key => $value) {
        $messageCount = strlen(trim($value->message)) > 160 ? 2 : 1;
        $arrayDataSmsByDestinatariesMongo1[] = array(
            "messageCount" => $messageCount,
            "message" => $value->message,
            "phone" => $value->phone,
            "date" => $value->scheduleDate,
//            "quantity" => $value["Cantidad"],
//            "status" => $value->status,
            "status" => (($value->response == "0: Accepted for delivery" || $value->response == "PENDING_ENROUTE") ? "sent" : "undelivered"),
            "name" => $value->smsName,
        );
      }
      return ["data" => $arrayDataSmsByDestinatariesMongo1,
          "totals" => $totalDataMongo,
          "page" => ceil($totalDataMongo / $this->limit),
          "type" => $type
      ];
    }
  }

  public function downloadRepSmsByDestinataries($data) {
    $export = true;
    $page = 0;
    $paramForNotLimit = 1;
    $dataExport = $this->getSmsByDestinataries($data->filterNameCampaign, $data->filterPhoneNumber, $data->dateInitial, $data->dateEnd, $data->dataTab, $paramForNotLimit);
    $dataExcel = $dataExport["data"];
    return $this->modelDataSmsByDestinataries($dataExcel);
  }

  public function modelDataSmsByDestinataries($data) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
//    $excel->basicPropertiesSms();
    $excel->setData($data);
    $excel->generatedInfoSmsByDestinataries();
    return $excel->downloadExcel("Detalle de envíos de SMS por Celular");
  }

  public function findReportmail($data) {
    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data['idMail']]]);
    $array = [
        'name' => $mail->name,
        'confirmationDate' => $mail->confirmationDate,
        'subject' => $mail->subject,
        'sender' => $mail->NameSender->name . ' <' . $mail->Emailsender->email . '>',
        'recipients' => $this->target($mail->target),
        'replyto' => $mail->ReplyTos->email ? $mail->ReplyTos->email : 'No asignado',
        'messageSent' => $this->findMessge($mail->idMail),
        'Statitics' => $this->findStatitics($mail->idMail),
        'Opening' => $this->findOpen($mail->idMail, $data['page']),
        'Click' => $this->findClick($mail->idMail, $data['page']),
        'Unsubscribed' => $this->findUnsubscribed($mail->idMail, $data['page']),
        'Bounced' => $this->findBounced($mail->idMail, $data['page']),
        'Spam' => $this->findSpam($mail->idMail, $data['page']),
        'Buzon' => $this->findBuzon($mail->idMail, $data['page']),
    ];
    unset($mail);
    return $array;
  }

  public function findMessge($idMail) {
    $this->totals = \Mxc::count([["status" => 'sent', "idMail" => $idMail]]);
    return $this->totals;
  }

  public function findStatitics($idMail) {
    $this->openTotal($idMail);
    $this->clickTotal($idMail);
    $this->unsubscribedTotal($idMail);
    $this->bouncedTotal($idMail);
    $this->spamTotal($idMail);
    $this->buzonTotal($idMail);
    return $this->allMail;
  }

  public function openTotal($idMail) {
    $open = \Mxc::count([["open" => ['$gte' => 1], "idMail" => $idMail]]);
    $this->allMail['open'] = ["Amount" => $open, "Porcentage" => $this->calculatePercentage($this->totals, $open)];
    return $this->allMail;
  }

  public function clickTotal($idMail) {
    $uniqueClicks = \Mxc::count([["uniqueClicks" => ['$gte' => 1], "idMail" => $idMail]]);
    $this->allMail['uniqueClicks'] = ["Amount" => $uniqueClicks, "Porcentage" => $this->calculatePercentage($this->totals, $uniqueClicks)];
    return $this->allMail;
  }

  public function unsubscribedTotal($idMail) {
    $unsubscribed = \Mxc::count([["unsubscribed" => ['$gte' => 1], "idMail" => $idMail]]);
    $this->allMail['unsubscribed'] = ["Amount" => $unsubscribed, "Porcentage" => $this->calculatePercentage($this->totals, $unsubscribed)];
    return $this->allMail;
  }

  public function bouncedTotal($idMail) {
    $bounced = \Mxc::count([["bounced" => ['$gte' => "1"], "idMail" => $idMail]]);
    $this->allMail['bounced'] = ["Amount" => $bounced, "Porcentage" => $this->calculatePercentage($this->totals, $bounced)];
    return $this->allMail;
  }

  public function spamTotal($idMail) {
    $spam = \Mxc::count([["spam" => ['$gte' => "1"], "idMail" => $idMail]]);
    $this->allMail['spam'] = ["Amount" => $spam, "Porcentage" => $this->calculatePercentage($this->totals, $spam)];
    return $this->allMail;
  }

  public function buzonTotal($idMail) {
    $buzon = \Mxc::count([["status" => 'sent', "bounced" => 0, "spam" => 0, "idMail" => $idMail]]);
    $this->allMail['buzon'] = ["Amount" => $buzon, "Porcentage" => $this->calculatePercentage($this->totals, $buzon)];
    return $this->allMail;
  }

  public function findOpen($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $where = array(
        "open" => ['$gte' => (int) 1], "idMail" => $idMail
    );
    $open = \Mxc::find([$where, "limit" => $this->limit, "skip" => $offset]);
    //
    $total = \Mxc::count([$where]);
    $data = array();
    foreach ($open as $value) {
      $obj = new \stdClass();
      $obj->scheduleDate = $value->scheduleDate;
      $obj->email = $value->email;
      $obj->totalOpening = 1;
      $obj->name = $value->name;
      $obj->lastname = $value->lastname;
      $obj->indicative = $value->indicative;
      $obj->phone = $value->phone;
      array_push($data, $obj);
    }
    $arr[] = $data;
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function findClick($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $sql = "SELECT link, totalClicks FROM mxl LEFT JOIN mail_link ON mxl.idMail_link = mail_link.idMail_link WHERE idMail = {$idMail}";
    $uniqueClicks = $this->db->fetchAll($sql);
    $click = array();
    foreach ($uniqueClicks as $value) {
      $click[] = [
          "link" => $value['link'],
          "totalClicks" => $value['totalClicks'],
      ];
    }
    $data = array();
    $where = array();
    $sql = "SELECT * FROM mail_link LEFT JOIN  mxl ON mail_link.idMail_link = mxl.idMail_link WHERE "
            . " idMail = {$idMail}";
    $link = $this->db->fetchAll($sql);
    $where['idMailLink'] = $link[0]['idMail_link'];
    $mxcxl = \Mxcxl::find([$where, "limit" => $this->limit, "skip" => $offset]);
    $total = \Mxcxl::count([$where]);
    foreach ($mxcxl as $key) {
      $obj = new \stdClass();
      $mailLink = \Maillink::findFirst(["conditions" => "idMail_link = ?0", "bind" => [0 => $key->idMailLink]]);
      $contact = \Contact::findFirst([["idContact" => (int) $key->idContact]]);
      $obj->email = $contact->email;
      $obj->name = $contact->name;
      $obj->lastname = $contact->lastname;
      $obj->indicative = $contact->indicative;
      $obj->phone = $contact->phone;
      $obj->link = $mailLink->link;
      $obj->date = $key->uniqueClicks;
      array_push($data, $obj);
    }
    $arr[] = $click;
    $arr[] = $data;
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function findUnsubscribed($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $where = array(
        "unsubscribed" => ['$gte' => (int) 1], "idMail" => $idMail
    );
    $unsubscribed = \Mxc::find([$where, "limit" => $this->limit, "skip" => $offset]);
    //
    $total = \Mxc::count([$where]);
    $data = array();
    foreach ($unsubscribed as $value) {
      $data[] = [
          "scheduleDate" => $value->scheduleDate,
          "email" => $value->email,
          "name" => $value->name,
          "lastname" => $value->lastname,
          "indicative" => $value->indicative,
          "phone" => $value->phone,
      ];
    }
    $arr[] = $data;
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function findBounced($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $where = array(
        "bounced" => ['$gte' => "1"], "idMail" => $idMail,
    );
    $bounced = \Mxc::find([$where, "limit" => $this->limit, "skip" => $offset]);
    //
    $total = \Mxc::count([$where]);
    $data = array();
    $soft = 0;
    $hard = 0;
    foreach ($bounced as $value) {
      $c = new \stdClass();
      $c->date = date('Y-m-d h:i:s', $value->bounced);
      $c->email = $value->email;
      $sql = "SELECT * FROM bounced_code WHERE idBounced_code = {$value->bouncedCode}";
      $bounced_code = $this->db->fetchAll($sql);
      if ($value->bouncedCode == 10 || $value->bouncedCode == 90 || $value->bouncedCode == 200) {
        $c->type = "hard";
        $hard++;
      } else {
        $c->type = "soft";
        $soft++;
      }
      $c->description = $bounced_code[0]['description'];
      array_push($data, $c);
    }
    $arr[] = $data;
    if ($total != 0) {
      $arr[] = ["soft" => $soft, "hard" => $hard];
    }
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function findSpam($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $where = array(
        "spam" => ['$gte' => "1"], "idMail" => $idMail
    );
    $spam = \Mxc::find([$where, "limit" => $this->limit, "skip" => $offset]);
    //
    $total = \Mxc::count([$where]);
    $data = array();
    foreach ($spam as $value) {
      $data[] = [
          "scheduleDate" => $value->scheduleDate,
          "email" => $value->email,
          "name" => $value->name,
          "lastname" => $value->lastname,
          "indicative" => $value->indicative,
          "phone" => $value->phone
      ];
    }
    $arr[] = $spam;
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function findBuzon($idMail, $page) {
    $arr = array();
    $offset = ($page - 1) * $this->limit;
    $where = array(
        "status" => 'sent', "bounced" => 0, "spam" => 0, "idMail" => $idMail
    );
    $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $offset]);
    //
    $total = \Mxc::count([$where]);
    $data = array();
    foreach ($mxc as $value) {
      if ($value->bounced == 0) {
        $obj = new \stdClass();
        $obj->dateOpen = $value->scheduleDate;
        $obj->email = $value->email;
        $obj->name = $value->name;
        $obj->lastname = $value->lastname;
        $obj->buzon = 1;
        array_push($data, $obj);
      }
    }
    $arr[] = $data;
    $arr[] = array("total_pages" => ceil($total / $this->limit));
    return $arr;
  }

  public function calculatePercentage($total, $value) {
    $porcentaje = ($value / $total) * 100;
    if ($porcentaje % 1 == 0) {
      return round($porcentaje, 2);
    } else {
      return $porcentaje;
    }
  }

  public function target($value) {
    $v = '';
    $p = json_decode($value);
    if (isset($p->contactlists)) {
      $v = "Lista de contactos: ";
      for ($index = 0; $index < count($p->contactlists); $index++) {
        $v .= $p->contactlists[$index]->name . ", ";
      }
    } else if (isset($p->segment)) {
      $v = "Segmentos: ";
      for ($index = 0; $index < count($p->segment); $index++) {
        
      }
    }
    $v = substr($v, 0, -2);
    return $v;
  }

  public function downloadSmsFailedReport($data, $idSms) {
    //consulto la data de sms failed con idSms guardado mientras se carga csv
    $modelManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $dataSmsFailed = $modelManager->createBuilder()
            ->columns(["Smsfailed.indicative AS Indicativo", "Smsfailed.phone AS Celular", "Smsfailed.message AS Mensaje", "Smsfailed.detail AS Detalle"])
            ->from('Smsfailed')
            ->where("Smsfailed.idSms = {$idSms}")
            ->orderBy("Smsfailed.idSms DESC")
            ->getQuery()
            ->execute();
    foreach ($dataSmsFailed as $value) {
      $arrayDataSmsFailed[] = array(
          "indicativo" => $value['Indicativo'],
          "celular" => $value['Celular'],
          "mensaje" => $value["Mensaje"],
          "detalle" => $value["Detalle"]
      );
    }
    $dataExcel = $arrayDataSmsFailed;
    $data->excel = $dataExcel;
    return $this->modelDataSmsFailed($data);
  }

  public function modelDataSmsFailed($data) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->setData($data);
    $excel->generatedReportSmsFailed();
    return $excel->downloadExcel("Detalle números Invalidos");
  }

  public function downloadSmsFailedReportContact($data, $idSms) {
    //consulto la data de sms failed con idSms guardado mientras se carga csv
    $arrayDataSmsFailed =[];
    $modelManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $dataSmsFailed = $modelManager->createBuilder()
            ->columns(["Smsfailed.indicative AS Indicativo", "Smsfailed.phone AS Celular", "Smsfailed.message AS Mensaje", "Smsfailed.detail AS Detalle"])
            ->from('Smsfailed')
            ->where("Smsfailed.idSms = {$idSms}")
            ->orderBy("Smsfailed.idSms DESC")
            ->getQuery()
            ->execute();
    foreach ($dataSmsFailed as $value) {
      $arrayDataSmsFailed[] = array(
          "indicativo" => $value['Indicativo'],
          "celular" => $value['Celular'],
          "mensaje" => $value["Mensaje"],
          "detalle" => $value["Detalle"]
      );
    }
    $dataExcel = $arrayDataSmsFailed;
    $data->excel = $dataExcel;
    return $this->modelDataSmsFailedContact($data,$idSms);
  }

  public function modelDataSmsFailedContact($data,$idSms) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $excel = new \Sigmamovil\General\Misc\reportExcel();

    $Sms = \Sms::findFirst(array("conditions" => "idSms = ?0", "bind" => array($idSms)));
    $SmsFailed = \SmsFailed::count(array("conditions" => "idSms = ?0", "bind" => array($idSms)));

    $smsdecode = json_decode($Sms->receiver);

    $arrContactsPhones = array();
    
    $arrIdContactlist = array();
    $arrIdContact = array();
    
    if ($Sms->singleSendContact == 1) {
      if (strpos($Sms->receiver, 'contactlists') !== false) {
        foreach ($smsdecode->contactlists as $key) {
          $arrIdContactlist[] = (int) $key->idContactlist;
        }
        $commaSeparatedIdContact = implode(",", $arrIdContactlist);
        $cxcl = \Cxcl::find(array(
                        "conditions" => "idContactlist IN ($commaSeparatedIdContact) "
                        . "and deleted = 0 "
                        . "AND unsubscribed = 0 "
                        . "AND status='active'",
                        //"group" => "idContact"
                    ))->toArray();
        foreach ($cxcl as $val) {
            $arrIdContact[] = (int) $val["idContact"];
        }
        for($i=0;$i<count($arrIdContact);$i++){
          $conditions = array(
                'idContact' =>$arrIdContact[$i],
                'phone' => array('$ne' => ""),
                'blockedPhone' => array('$in' => array("", null, "null"))
            );
          $contact = \Contact::find(array($conditions));
          if ($contact) {
            //$contact = $contact[0]->result;
              foreach ($contact as $c){
               //$c = $c->data;
               $arrContactsPhones[] = $c->phone;
            }
          }
        }
//        $command = new \MongoDB\Driver\Command([
//              'aggregate' => 'contact',
//              'pipeline' => [
//                  ['$match' => $conditions],
//                  ['$group' => [/*'_id' => '$idContact',*/ 'data' => ['$first' => '$$ROOT']]],
//                ]
//              ]);
//        $contact = $manager->executeCommand('aio', $command)->toArray();
        $numRepeatedPhones = array();
        $repeatedPhonesReport = array();
        $numRepeatedPhones = array_count_values($arrContactsPhones);
        
        //var_dump(print_r($numRepeatedPhones,true));exit;
        
        $totalRepeated= 0;
        foreach ($numRepeatedPhones as $k=>$v) {
          if ($v > 1){
            
            $repeatedPhonesReport[] = $k;
          }
          while($v>1){
            $totalRepeated++;
            $v--;
          }      
        }
      }else{
        $arrIdContactFromSegment = array();
        foreach ($smsdecode->segment as $key) {
          $sxcs = \Sxc::find([["idSegment" => $key->idSegment]]);
        }
        if ($sxcs) {
          foreach ($sxcs as $sxc) {
            $arrIdContactFromSegment[] = (int) $sxc->idContact;
            }
          }
        for($i=0;$i<count($arrIdContactFromSegment);$i++){  
          $contactConditions = array(
              'idContact' =>$arrIdContactFromSegment[$i],
              'phone' => array('$ne' => ""),
              'blockedPhone' => array('$in' => array("", null, "null"))
            );
          $contactsFromSegment = \Contact::find(array($contactConditions));
          if ($contactsFromSegment) {
            //$contactsFromSegment = $contactsFromSegment[0]->result;
            foreach ($contactsFromSegment as $c){
              //$c = $c->data;
              $arrContactsPhones[] = $c->phone;
            }
          }
        }
//        $command = new \MongoDB\Driver\Command([
//              'aggregate' => 'contact',
//              'pipeline' => [
//                ['$match' => $contactConditions],
//                ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]],
//              ]
//            ]);
//            
//      $contactsFromSegment = $manager->executeCommand('aio', $command)->toArray();
        
        
        $numRepeatedPhones = array();
        $repeatedPhonesReport = array();
        $numRepeatedPhones = array_count_values($arrContactsPhones);
        
        $totalRepeated= 0;
        foreach ($numRepeatedPhones as $k=>$v) {
          if ($v > 1){
            $repeatedPhonesReport[] = $k;
          }
          while($v>1){
            $totalRepeated++;
            $v--;
          }          
        }
      }
      $Smsdetail = array();
      array_push($Smsdetail, ["TotalInicial" => $Sms->target + $SmsFailed + $totalRepeated]);
      array_push($Smsdetail, ["TotalUnicos" => $Sms->target + $SmsFailed ]);
      array_push($Smsdetail, ["Total" => $Sms->target]);
      array_push($Smsdetail, ["Invalidos" => $SmsFailed]);
      array_push($Smsdetail, ["Repetidos" => count($repeatedPhonesReport)]);
      array_push($Smsdetail, ["mensaje" => $Sms->message]);
      array_push($Smsdetail, ["numerosRepetidos" => $repeatedPhonesReport]);
    }else{
      $Smsdetail = array();
      array_push($Smsdetail, ["TotalInicial" => $Sms->target + $SmsFailed]);
      array_push($Smsdetail, ["TotalUnicos" => "No aplica" ]);
      array_push($Smsdetail, ["Total" => $Sms->target]);
      array_push($Smsdetail, ["Invalidos" => $SmsFailed]);
      array_push($Smsdetail, ["Repetidos" => "No Aplica"]);
      array_push($Smsdetail, ["mensaje" => $Sms->message]);
    }

    $excel->setData($data);
    $excel->generatedReportSmsFailedContact($Smsdetail);
    return $excel->downloadExcel("Detalle números Invalidos");
  }
  
  public function findMessageCount($idSms, $type){
    if($type == "lote" || $type == "csv" || $type == "single" || $type == "encrypted"){
      $sql = "SELECT SUM(messageCount) AS totalMessage FROM smslote WHERE idSms = ".$idSms;
      return $this->db->fetchAll($sql)[0]["totalMessage"];
    }
    if($type == "contact" || $type == "automatic"){
      $collectionSmsXc = [[ '$match' => ['idSms' => (string) $idSms] ],[ '$group' => ['_id' => '$idSms', 'messageCount' => ['$sum' => '$messageCount']]]];
      $count1 = \Smsxc::aggregate($collectionSmsXc);
      return $count1['result'][0]['messageCount'];
    }
  }

}
