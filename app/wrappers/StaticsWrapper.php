<?php

namespace Sigmamovil\Wrapper;

ini_set('memory_limit', '1536M');
date_default_timezone_set('America/Bogota');

/**
 * Description of StaticsWrapper
 *
 * @author desarrollo3
 */
class StaticsWrapper extends \BaseWrapper {
    
  public $searchPhone = 0;  
  public function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->statusSms = \Phalcon\DI::getDefault()->get('statusSms');
    $this->limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
  }
  
  public function setSearchPhone($phone){
    $this->searchPhone = (string) $phone;              
  }
  
  public $statics = array(),
          $idMail,
          $idSurvey,
          $openWeek,
          $openDay,
          $openHour,
          $page,
          $type,
          $stringSearch,
          $typeFilter,
          $idSms,
          $statusSms,
          $configuration,
          $idContactlist,
          $idSmsTwoway,
          $objSmsTwoway,
          $surveyInfo;

  public function getAllInfoMail($type) {

    $sql = "SELECT mail.idMail, mail.idSubaccount, mail.idEmailsender,  mail.categorycampaign,"
            . " mail.name, mail.sender, IF(mail.idReplyTo is null,mail.replyto,reply_tos.email) as replyto, mail.subject, mail.target, mail.created, "
            . " mail.updated, mail.status, mail.quantitytarget, mail.test, mail.deleted, emailsender.email AS emailsender, "
            . " name_sender.name as namesender, mail.scheduleDate AS confirmationDate FROM mail  "
            . " LEFT JOIN mxmc ON mxmc.idMail = mail.idMail "
            . " LEFT JOIN emailsender ON mail.idEmailsender = emailsender.idEmailsender "
            . " LEFT JOIN name_sender ON mail.idNameSender = name_sender.idNameSender "
            . " LEFT JOIN reply_tos ON mail.idReplyTo = reply_tos.idReplyTo "
            . " WHERE mail.idMail = {$this->idMail} "
            . " GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18";
    $mail = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);

    $this->statics["mail"] = $mail[0];
    $this->statics["mail"]['target'] = $this->target($this->statics["mail"]['target']);
    if ($type == 0) {
      $this->statics["shortStaticsUrl"] = $this->encodeLink($this->statics["mail"]["idMail"], $this->statics["mail"]["idSubaccount"], "summary");
      $this->statics["fullStaticsUrl"] = $this->encodeLink($this->statics["mail"]["idMail"], $this->statics["mail"]["idSubaccount"], "complete");
    }
    $subaccount = \Subaccount::findFirst(["conditions" => "idSubaccount = ?0",
                "bind" => [$this->statics["mail"]["idSubaccount"]]]);

    $this->statics["urlImg"] = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . "assets/" . $subaccount->idAccount . "/images/mails/" . $this->statics["mail"]["idMail"] . "_thumbnail.png";
    $this->statics["urlImgDefault"] = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . "images/general/no-preview.png";
    $this->getAllMessageSent();
    $this->getAllOpen();
    $this->clicsTotalUniques();
    $this->bouncedTotal();
    $this->unsubscribedTotal();
    $this->spamTotal();
    $this->buzonTotal();

    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $this->idMail]]);
    $mail->uniqueOpening = $this->statics["open"];
    $mail->bounced = $this->statics["bounced"];
    $mail->spam = $this->statics["spam"];

    if (!$mail->save()) {
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function encodeLink($idMail, $idSubaccount, $type) {
    $src = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'statistic/share/1-' . $idMail . "-" . $idSubaccount . "-" . $type;
    return $src . '-' . md5($src . '-Sigmamovil_Rules');
  }

  public function target($value) {

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

  public function staticsClic() {
    $this->cleanTmpTable();
  
    $open = \Mxc::find(array(
      "conditions" => array(
        "idMail" => $this->idMail,
        "uniqueClicks" => array(
          '$gte' => (int) 1
        )
      ),
      "fields" => array(
        "uniqueClicks" => true,
        "idContact" =>true,
        "email" => true,
        "name" => true,
        "lastname" => true,
        "indicative" => true,
        "phone" => true,
        "totalOpening" => true
      )
    ));
  
    // $open = \Mxc::find([["uniqueClicks" => ['$gte' => 1], "idMail" => $this->idMail]]);
    $this->createTmpTable("uniqueClicks", $open);
    unset($open);
  }

  public function staticsUnsuscribed() {
    $this->cleanTmpTable();
    $open = \Mxc::find(array(
      "conditions" => array(
        "idMail" => $this->idMail,
        "unsubscribed" => array(
          '$gt' => (int) 0
        )
      ),
      "fields" => array(
        "_id" => false,
        "unsubscribed" => true,
        "idContact" =>true,
        "email" => true,
        "name" => true,
        "lastname" => true,
        "indicative" => true,
        "phone" => true,
        "totalOpening" => true
      )
    ));
    $data = [];
    foreach ($open as $value) {
      $unsuscribe = \Unsubscribed::findFirst([
        "conditions" => "idMail = ?0 AND idContact = ?1", 
        "bind" => array(0 => $this->idMail, 1 => $value->idContact)
      ]);
      $movite = "Ninguno";
      if($unsuscribe != false){
        $movite = $unsuscribe->motive != "Otro" ? $unsuscribe->motive : $unsuscribe->other;
      }
      $value->motive = $movite;
      $data[] = $value;
    }
    //$open = \Mxc::find([["unsubscribed" => ['$gte' => 1], "idMail" => $this->idMail]]);
    $this->createTmpTable("unsubscribed", $data);
    unset($open);
  }

  public function staticsSpam() {
    $this->cleanTmpTable();
      $open = \Mxc::find(array(
        "conditions" => array(
          "idMail" => $this->idMail,
          "spam" => array(
            '$gte' => (string) '1'
          )
        ),
        "fields" => array(
          "spam" => true,
          "idContact" =>true,
          "email" => true,
          "name" => true,
          "lastname" => true,
          "indicative" => true,
          "phone" => true,
          "totalOpening" => true
        )
      ));
      
    //$open = \Mxc::find([["spam" => ['$gte' => "1"], "idMail" => $this->idMail]]);
    $this->createTmpTable("spam", $open);
    unset($open);
  }

  public function staticsOpen() {
    
  $this->cleanTmpTable();
    
  if (isset($this->statics["open"])) {

      $this->createTmpTable("open", $this->statics["open"]);
    } else {

      $open = \Mxc::find(array(
        "conditions" => array(
          "idMail" => $this->idMail,
          "open" => array(
            '$type' => (int) 18
          )
        ),
        "fields" => array(
          "open" => true,
          "idContact" =>true,
          "email" => true,
          "name" => true,
          "lastname" => true,
          "indicative" => true,
          "phone" => true,
          "totalOpening" => true
        )
      ));
      //ASI ESTABA ANTES:
      //$open = \Mxc::find([["open" => ['$type' => (int) 18], "idMail" => (string) $this->idMail]]);

      $this->createTmpTable("open", $open);
      unset($open);
    }
  }

  public function staticsBounced() {
    $bounced = \Mxc::find([["bounced" => ['$gte' => "1"], "idMail" => $this->idMail]]);
    $arr = array();
    $soft = 0;
    $hard = 0;
    foreach ($bounced as $key) {
      if ($key->bouncedCode == 10 || $key->bouncedCode == 90 || $key->bouncedCode == 200) {
        $hard++;
      } else {
        $soft++;
      }
    }
    $obj = new \stdClass();
    $obj->total = count($bounced);
    $obj->soft = $soft;
    $obj->hard = $hard;
    array_push($arr, $obj);
    unset($obj);
    unset($bounced);
    return $arr;
  }

  public function createTmpTable($type, $info) {
    $route = \Phalcon\DI::getDefault()->get('path')->path . "tmpopen.csv";
    $file = fopen($route, "w");
    $i = 1;
    foreach ($info as $value) {
      fwrite($file, $value->idContact . " ,");
      fwrite($file, $value->$type . " ,");
      fwrite($file, $value->email . " ,");
      fwrite($file, $value->name . " ,");
      fwrite($file, $value->lastname . " ,");
      fwrite($file, $value->indicative . " ,");
      fwrite($file, $value->phone . " ,");
      if($type == "unsubscribed"){
        fwrite($file, utf8_encode($value->motive) . " ,");
      }
      fwrite($file, $value->totalOpening);
//      fwrite($file, $value->email);
      fwrite($file, "\r\n");
      $i++;
    }
    $tmpopen = fclose($file);
    if (!$tmpopen) {
      throw new Exception("No se ha generado el archivo temporal con las fechas");
    }
    $this->db->query("LOAD DATA INFILE '{$route}' IGNORE INTO TABLE tmp_table "
            . "FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' ");

    //Se realizo el cambio dado que se tenia problemas con las estadisticas de la campa??a 13247.
    //$this->db->query("LOAD DATA INFILE '{$route}' IGNORE INTO TABLE tmp_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");
//    $this->db->query("LOAD DATA INFILE 'C:/Users/juan.pinzon/Documents/NetBeansProjects/aio/tmpopen.csv' IGNORE INTO TABLE tmp_table "
//            . "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");
    $this->graphWeek();
//    $j = 0;
//    $k = 0;
//    $startArr = [];
    $count = 0;
    $p = [];
    foreach ($this->openWeek as $key) {
      $end = strtotime("next sunday", strtotime($key["week"]));
      $start = strtotime("-1 week +1 day", $end);

      if (isset($p[$start])) {
        $p[$start] += $key["count"];
      } else {
        $p[$start] = $key["count"];
      }

      if (isset($this->statics["statics"][$start])) {

        //OPCIONAL SE TIENE Q PROBAR CON MUCHOS REGISTROS
        $this->statics["statics"][$start]["week"] = ["week" => strtotime($key["week"]), "count" => $p[$start],
            "interval" => "Entre " . date('d/M/Y', $start) . " y " . date('d/M/Y', $end)];
      } else {

        $this->statics["statics"][$start]["week"] = ["week" => strtotime($key["week"]), "count" => $p[$start],
            "interval" => "Entre " . date('d/M/Y', $start) . " y " . date('d/M/Y', $end)];
      }
      $this->graphDay($key, $start);
    }

    //unlink($route);
  }

  public function graphWeek() {
    $sql = "SELECT count(idTmpTable) AS count, FROM_UNIXTIME(dateOpen, '%Y-%m-%d') as week  "
            . " FROM tmp_table GROUP BY  WEEK (FROM_UNIXTIME(dateOpen)), 2 ORDER BY week ASC";
//    $sql = "SELECT count(dateOpen) as count, dateOpen as week FROM tmp_table GROUP BY  week(from_unixtime(2)), dateOpen";
    $this->openWeek = $this->db->fetchAll($sql);
    unset($sql);
  }

  public function graphDay($time, $indexWeek) {
    $end = strtotime("next sunday", strtotime($time["week"]));
    $start = strtotime("-1 week +1 day", $end);

    $sql = "SELECT count(dateOpen) AS count, FROM_UNIXTIME(dateOpen, '%d-%m-%Y') AS day FROM tmp_table WHERE "
            . " dateOpen BETWEEN {$start} AND {$end} "
            . " GROUP BY day(FROM_UNIXTIME(dateOpen)), 2";
    $p = $this->db->fetchAll($sql);
    $indexDay = 0;
    foreach ($p as $value) {
      $hour = str_replace("-", "/", $value["day"]);
      $this->statics["statics"][$indexWeek]["week"]["day"][$indexDay] = ["interval" => $hour, "day" => strtotime("next sunday", strtotime($value["day"])), "count" => $value["count"], "week" => $start];
      $this->graphHour($value, $indexDay, $indexWeek, $start);
      $indexDay++;
    }
    unset($sql);
    unset($p);
  }

  public function graphHour($time, $indexDay, $indexWeek, $startWeek) {
    $hour = $time["day"];
    $start = strtotime($hour . " 00:00:00");
    $end = strtotime($hour . " 23:59:00");
    $sql = "SELECT count(dateOpen) AS count, FROM_UNIXTIME(dateOpen, '%H')  AS hour"
            . "  FROM tmp_table WHERE "
            . " dateOpen BETWEEN {$start} AND {$end} "
            . "GROUP BY hour(from_unixtime(dateOpen)), 2";

    $p = $this->db->fetchAll($sql);
    foreach ($p as $value) {
      $this->statics["statics"][$indexWeek]["week"]["day"][$indexDay]["hour"][] = ["interval" => $value["hour"] . "-00", "hour" => $value["hour"], "count" => $value["count"], "week" => $startWeek];
    }
    unset($sql);
    unset($p);
  }

  public function dataInfo() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");

    switch ($this->type) {
      case "open":
        $sql = "SELECT * FROM tmp_table LIMIT {$this->limit} OFFSET {$this->page}";
        $data = $this->db->fetchAll($sql);
        $sql2 = "SELECT * FROM tmp_table ";
        $open = $this->db->fetchAll($sql2);
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
        unset($sql);
        unset($data);
        unset($sql2);
        unset($open);
        
        break;
      case "clic":
        $sql = "SELECT link, totalClicks FROM mxl LEFT JOIN mail_link ON"
                . " mxl.idMail_link = mail_link.idMail_link WHERE idMail = {$this->idMail} LIMIT {$this->limit} OFFSET {$this->page}";
        $data = $this->db->fetchAll($sql);
        $sql2 = "SELECT link, totalClicks FROM mxl LEFT JOIN mail_link ON"
                . " mxl.idMail_link = mail_link.idMail_link WHERE idMail = {$this->idMail}";
        $data2 = $this->db->fetchAll($sql2);
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => count($data2), "total_pages" => ceil(count($data2) / $this->limit));
        $this->statics["info"][] = $data2;
        unset($sql);
        unset($data);
        unset($sql2);
        unset($data2);
        
        break;
      case "unsuscribe":
        $sql = "SELECT * FROM tmp_table LIMIT {$this->limit} OFFSET {$this->page}";
        $data = $this->db->fetchAll($sql);
        $sql2 = "SELECT * FROM tmp_table ";
        $open = $this->db->fetchAll($sql2);
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
        unset($sql);
        unset($data);
        unset($sql2);
        unset($open);        
        break;
      case "bounced":
        $idSub = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount; 
        $where = array();
        $where = ["bounced" => ['$gte' => "1"], "idMail" => $this->idMail];
        if (isset($this->stringSearch->name)) {
          if ($this->stringSearch->id != -1) {
            switch ($this->typeFilter) {
              case "type":
                $in = array();
                if ($this->stringSearch->name == "soft") {
                  $in = [20, 21, 22, 23, 29, 30, 40, 50, 51, 52, 53, 54, 59, 60, 70, 100, 110, 120, 121];
                  $in = array_map('strval', $in);
                } else if ($this->stringSearch->name == "hard") {
                  $in = [10, 90, 200];
                  $in = array_map('strval', $in);
                }
                $where["bouncedCode"] = ['$in' => $in];
                 unset($in);
                break;
              case "category":
                $where["bouncedCode"] = (string) $this->stringSearch->id;
                break;
              case "domain":
                $name = $this->stringSearch->name;
                $where["email"] = ['$regex' => ".*$name"];
                unset($name);
                break;
              default :
                throw new Exception("Se ha producido un error al enviar el tipo de filtros");
                break;
            }
          }
        }
        $bounced = \Mxc::find(["limit" => $this->limit, "skip" => $this->page, $where]);
        $open = \Mxc::find([$where]);
        unset($where);
        $array = array();
        foreach ($bounced as $key) {
          $c = new \stdClass();
          $c->date = $key->bounced;
          $c->email = $key->email;
          $sql = "SELECT * FROM bounced_code WHERE idBounced_code = {$key->bouncedCode}";
          $data = $this->db->fetchAll($sql);
          if ($key->bouncedCode == 10 || $key->bouncedCode == 90 || $key->bouncedCode == 200) {
            $c->type = "hard";
          } else {
            $c->type = "soft";
          }
          $c->description = $data[0]['description'];
          array_push($array, $c);
          unset($c);
          unset($data);
          unset($sql);
        }
        unset($bounced);
        $this->statics["info"][] = $array;
        $this->statics["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
        unset($array);
        unset($open);
        break;
      case "spam":
        $sql = "SELECT * FROM tmp_table LIMIT {$this->limit} OFFSET {$this->page}";
        $data = $this->db->fetchAll($sql);
        $sql2 = "SELECT * FROM tmp_table ";
        $open = $this->db->fetchAll($sql2);
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
        unset($sql);
        unset($sql2);
        unset($data);
        unset($open);
        break;
      case "buzon":
        $where = [
            "idMail" => $this->idMail,
            "status" => 'sent',
            "bounced" => 0,
            "spam" => 0
        ];
        $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $this->page]);
        //
        $total = \Mxc::count([$where]);
        unset($where);
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
            unset($obj);
          }
        }
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
        unset($total);
        unset($data);
        break;
    }
  }

  public function dataInfoClic() {

    $arr = array();
    $array = array();
    $where = array();
    $where['idMail'] = $this->idMail;

    if (isset($this->stringSearch)) {
      $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
      $sql = "SELECT * FROM mail_link LEFT JOIN  mxl ON mail_link.idMail_link = mxl.idMail_link WHERE "
              . " idMail = {$this->idMail} AND link = '{$this->stringSearch}' AND idAccount = {$idAccount}";
      $link = $this->db->fetchAll($sql);
      $where['idMailLink'] = $link[0]['idMail_link'];
      unset($link);
      unset($sql);
    }

    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
    $mxcxl = \Mxcxl::find([$where, "limit" => $this->limit, "skip" => $this->page]);
    

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
      array_push($array, $obj);
      unset($obj);
    }
    $open = \Mxcxl::find([$where]);
    $arr["info"][] = $array;
    $arr["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
    unset($mxcxl);
    return $arr;
  }

  public function getAllDomain() {
    $arr = array();
    $domain = \Domain::find([["idAccount" => \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount]]);
    foreach ($domain as $key) {
      $obj = new \stdClass();
      $obj->id = $key->idDomain;
      $obj->name = $key->domain;
      array_push($arr, $obj);
    }
    return $arr;
  }

  public function getAllCategoryBounced() {
    $arr = array();
    $bounced = \BouncedCode::find();
    foreach ($bounced as $key) {
      $obj = new \stdClass();
      $obj->id = $key->idBounced_code;
      $obj->name = $key->description;
      array_push($arr, $obj);
    }
    return $arr;
  }

  public function getAllMessageSent() {
    $open = \Mxc::count([["idMail" => $this->idMail, "status" => "sent"]]);
    if($this->idMail == 15961){
      $this->statics["messageSent"] = 13185;
    } else {
      $this->statics["messageSent"] = $open;
    }
  }

  public function getAllOpen() {
    $open = \Mxc::count([["open" => ['$type' => (int) 18], "idMail" => $this->idMail]]);
//    $open = \Mxc::count([["open" != null]]);
    $this->statics["open"] = $open;
  }

  public function clicsTotalUniques() {
    $uniqueClicks = \Mxc::count([["uniqueClicks" => ['$gte' => 1], "idMail" => $this->idMail]]);
    $this->statics["uniqueClicks"] = $uniqueClicks;
  }

  public function bouncedTotal() {
    $uniqueClicks = \Mxc::count([["bounced" => ['$gte' => "1"], "idMail" => $this->idMail]]);
    $this->statics["bounced"] = $uniqueClicks;
  }

  public function unsubscribedTotal() {
    $uniqueClicks = \Mxc::count([["unsubscribed" => ['$gte' => 1], "idMail" => $this->idMail]]);
    $this->statics["unsubscribed"] = $uniqueClicks;
  }

  public function spamTotal() {
    $uniqueClicks = \Mxc::count([["spam" => ['$gte' => "1"], "idMail" => $this->idMail]]);
    $this->statics["spam"] = $uniqueClicks;
  }

  public function cleanTmpTable() {
    $sql = "DELETE FROM tmp_table WHERE 1= 1";
    $this->db->execute($sql);
  }

  public function getInfoSms() {
    /*$sql = "SELECT sms.name AS name, sms.startdate AS startdate, sms_category.name AS namecategory, "
            . " sms.target AS target "
            . " FROM sms  JOIN sms_category ON "
            . "sms_category.idSmsCategory = sms.idSmsCategory WHERE sms.idSms = {$this->idSms}";*/
    $sql = "SELECT 
                sms.idSms AS idSms,
                sms.name AS name,
                sms.startdate AS startdate,
                case sms.type when 'automatic' then 'contact' else sms.type end AS type,
                sms_category.name AS namecategory
            FROM
                sms
                    JOIN
                sms_category ON sms_category.idSmsCategory = sms.idSmsCategory
            WHERE
                sms.idSms = {$this->idSms};";
    $sms = $this->db->fetchAll($sql);
    $targetSmsxc = 0;
    if($sms[0]['type']=='contact' || $sms[0]['type'] == 'automatic'){
      $findMongoContact = array(
          'conditions'=>array(
              'idSms'=>$sms[0]['idSms']
          )
      );
      $respuestMongoContact = \Smsxc::find($findMongoContact);
      $targetSmsxc = count($respuestMongoContact);
    }
//    else if($sms[0]['type']=='lote'||$sms[0]['type']=="csv"){Se cambia debido a que si es por api es por lote
    else{
      $sqlFindLote = "SELECT 
                          COUNT(smslote.idSmslote) as target
                      FROM
                          smslote
                      WHERE
                          smslote.idSms = {$this->idSms};";
      $smsxc = $this->db->fetchAll($sqlFindLote);
      $targetSmsxc = $smsxc[0]['target'];
    }
    
    $this->statics["sms"] = $sms[0];
    $this->statics["sms"]['target'] = $targetSmsxc;
    $this->statics["sms"]['birthdatetype'] = $this->isBirthdaySms($this->idSms);
    $this->statics["sms"]['shortStaticsUrl'] = $this->encodeLinkSms($this->idSms, 'summary');
//    $this->statics["sms"]['fullStaticsUrl'] = $this->encodeLinkSms($this->idSms, 'complete');

    $sql2 = "SELECT * FROM smslote WHERE idSms = {$this->idSms} ";
    $total = $this->db->fetchAll($sql2);
    $this->statics["detail"][] = ['idSms' => $this->idSms];
    $this->statics["detail"][] = array("total" => count($total), "total_pages" => ceil(count($total) / $this->limit));
  }

  public function getInfoSmsTwoway() {
    $smsTwoWay = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0", "bind" => array($this->idSmsTwoway)));
    $this->statics["sms"] = $smsTwoWay;
    $this->objSmsTwoway = $smsTwoWay;
//    $smsLote = \Smslotetwoway::find(array("conditions" => "idSmsTwoway = ?0 ", "bind" => array($this->idSmsTwoway)));
//    $this->statics["detail"][] = $smsLote;
//    $this->statics["detail"][] = array("total" => count($smsLote), "total_pages" => ceil(count($smsLote) / $this->limit));
  }

  public function encodeLinkSms($idSms, $type) {
    $src = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'statistic/smsshare/1-' . $idSms . "-" . $type;
    return $src . '-' . md5($src . '-Sigmamovil_Rules');
  }

  public function graphSms() {
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $this->idSms]]);
    if ($sms->type == "contact" || $sms->type == 'automatic') {
      //$statusSuccess = ["0: Accepted for delivery", "PENDING_ENROUTE"];
      $statusSuccess = "sent";
      /*$sent = \Smsxc::count(array(
                  "conditions" => array(
                      "idSms" => (string) $this->idSms,
                      "response" => array(
                          '$in' => $statusSuccess
                      )
                  )
      ));
      $undelivered = \Smsxc::count(array(
                  "conditions" => array(
                      "idSms" => (string) $this->idSms,
                      "response" => array(
                          '$nin' => $statusSuccess
                      )
                  )
      ));*/
      $sent = \Smsxc::count(array(
                  "conditions" => array(
                      "idSms" => (string) $this->idSms,
                      "status" => $statusSuccess
                  )
      ));
      $undelivered = \Smsxc::count(array(
                  "conditions" => array(
                      "idSms" => (string) $this->idSms,
                      "status" => array(
                          '$nin' => array($statusSuccess)
                      )
                  )
      ));
    } else {
      //$sent = \Smsxc::count([["idSms" => (string) $this->idSms, "response" => "0: Accepted for delivery"]]);
      //$undelivered = \Smsxc::count([["idSms" => (string) $this->idSms, "response" => ['$ne' => "0: Accepted for delivery"]]]);

      $sent = \Smslote::count(["conditions" => "status = ?0 AND idSms = ?1", "bind" => [0 => $this->statusSms->sent, 1 => $this->idSms]]);
      $report = \ReportSmsxemail::findFirst(array("conditions" => "idSms = ?0", "bind" => array($this->idSms)));
      if ($report) {
        $count = \Smslote::count(["conditions" => "status = ?0 AND idSms = ?1", "bind" => [0 => "undelivered", 1 => $this->idSms]]);
        $encode = json_decode($report->smsFailed);
        $undelivered = count(get_object_vars($encode)) + $count;
      } else {
//        $undelivered = \Smslote::count(["conditions" => "status = ?0 AND idSms = ?1", "bind" => [0 => "undelivered", 1 => $this->idSms]]);
        $undelivered = \Smslote::count(["conditions" => "status <> ?0 AND idSms = ?1", "bind" => [0 => "sent", 1 => $this->idSms]]);
      }
    }
    $this->statics["sent"] = (int) $sent;
    $this->statics["undelivered"] = (int) $undelivered;
  }

  public function graphSmsTwoway() {
//    $sms = \Smstwoway::findFirst(["conditions" => "idSmsTwoway = ?0", "bind" => [0 => $this->idSmsTwoway]]);
//    $sent = \Smslotetwoway::count(["conditions" => "status = ?0 AND idSmsTwoway = ?1", "bind" => [0 => $this->statusSms->sent, 1 => $this->idSmsTwoway]]);
//    $undelivered = \Smslotetwoway::count(["conditions" => "status = ?0 AND idSmsTwoway = ?1", "bind" => [0 => "undelivered", 1 => $this->idSmsTwoway]]);
    $this->statics["sent"] = (int) $this->objSmsTwoway->sent;
    $this->statics["undelivered"] = (int) $this->objSmsTwoway->total - $this->objSmsTwoway->sent;
  }

  public function getDetailSms() {
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $this->idSms]]);
    if ($sms->type == "contact" || $sms->type == 'automatic') {
      if(!empty($this->searchPhone)){
        
        $detail = \Smsxc::find([["idSms" => (string) $this->idSms, "phone" => ['$regex' => ".*$this->searchPhone.*"]], "limit" => $this->limit, "skip" => $this->page]);
        $total = \Smsxc::count([["idSms" => (string) $this->idSms]]);  
      }else{
        $detail = \Smsxc::find([["idSms" => (string) $this->idSms], "limit" => $this->limit, "skip" => $this->page]);
        $total = \Smsxc::count([["idSms" => (string) $this->idSms]]);
      }       
      
      
      $data = $this->modelDataSmsContactlist($detail);
    } else {
      (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
      $report = \ReportSmsxemail::findFirst(array("conditions" => "idSms = {$this->idSms}"));
      $reporSms = json_decode($report->smsFailed);
      $count = 0;
      if ($report) {
        $sql = "SELECT * FROM smslote WHERE idSms = {$this->idSms} LIMIT {$this->limit} OFFSET {$this->page}";
        $smslote = $this->db->fetchAll($sql);
        $arrayData = array();
        $key = count($smslote);
        foreach ($reporSms as $value) {
          $arrayData[$key] = $value;
          $key++;
          $count++;
        }
        $data = array_merge($smslote, $arrayData);
        
      } else {
        
        if(!empty($this->searchPhone)){
            $sql = "SELECT * FROM smslote WHERE idSms = {$this->idSms} AND phone LIKE '%".(int) $this->searchPhone."%' LIMIT {$this->limit} OFFSET {$this->page}";
        }else{
            $sql = "SELECT * FROM smslote WHERE idSms = {$this->idSms} LIMIT {$this->limit} OFFSET {$this->page}";    
        }
        
        $data = $this->db->fetchAll($sql);
        foreach ($data as $key => $value) {
          $arrayData[] = array(
              "idSmslote" => $value["idSmslote"],
              "idSms" => $value["idSms"],
              "idAdapter" => $value["idAdapter"],
              "indicative" => $value["indicative"],
              "phone" => $value["phone"],
              "message" => $value["message"],
              //"status" => (($value["response"] == "0: Accepted for delivery" || $value["response"] == "PENDING_ENROUTE") ? "sent" : "undelivered"),
              "status" => ($value["status"]=='sent')? $value["status"]: 'undelivered',//Enviado de Aio no el de Infobit
              "messageCount"  => $value["messageCount"],
              "created" => $value["created"],
              "updated" => $value["updated"],
              "response" => $value["response"],
              "createdBy" => $value["createdBy"],
              "updatedBy" => $value["updatedBy"],
          );
        }
        $data = $arrayData;
      }
      $sql2 = "SELECT * FROM smslote WHERE idSms = {$this->idSms} ";
      if ($report) {
        $consulta = $this->db->fetchAll($sql2);
        $total = count($consulta) + $count;
      } else {
        $total = $this->db->fetchAll($sql2);
        $total = count($total);
      }
    }
    $this->statics["detail"][] = $data;
   /* \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++ESTE ES EL DATA+++++++++++++");   
    \Phalcon\DI::getDefault()->get('logger')->log(print_r($data,true));
    \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++SALGO DEL DATA+++++++++++++");   */
    $this->statics["detail"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
  }

  public function modelDataSmsContactlist($detail) {
    $arr = [];
    foreach ($detail as $value) {
      $obj = new \stdClass();
      $obj->indicative = $value->indicative;
      $obj->phone = $value->phone;
      $obj->message = $value->message;
//      $obj->status = (($value->response == "0: Accepted for delivery" || $value->response == "PENDING_ENROUTE") ? "sent" : "undelivered");
      $obj->status = (($value->status == "sent") ? "sent" : "undelivered");
      $obj->messageCount = $value->messageCount;
      array_push($arr, $obj);
    }
    return $arr;
  }

  public function modelDataSms() {
    $arr = array();
    foreach ($this->data as $key) {
      $obj = new \stdClass();
      $obj->indicative = $key->indicative;
      $obj->phone = $key->phone;
      $obj->message = $key->message;
      array_push($arr, $obj);
    }
    $this->statics["detail"] = $arr;
  }

  public function reportStatics($title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->setIdMail($this->idMail);
    $excel->createStatics();
    $this->getAllMessageSent();
    $this->getAllOpen();
    $this->clicsTotalUniques();
    $this->bouncedTotal();
    $this->unsubscribedTotal();
    $this->spamTotal();
    $this->buzonTotal();

    $excel->setInfo($this->statics);
    $excel->setType($this->type);
    switch ($this->type) {
      case "clic":
        $excel->SetTittle("REPORTE DE CLICS SOBRE ENLACE");
        break;
      case "open":
        $excel->SetTittle("REPORTE DE APERTURAS DE CORREO");
        break;
      case "unsuscribe":
        $excel->SetTittle("REPORTE CORREOS DESUSCRITOS");
        break;
      case "bounced":
        $excel->SetTittle("REPORTE CORREOS REBOTADOS");
        break;
      case "spam":
        $excel->SetTittle("REPORTE CORREOS QUE HAN MARCADO SPAM");
        break;
      case "buzon":
        $excel->SetTittle("REPORTE DE CORREOS EN BUZ??N");
        break;
    }
    $excel->setData($this->dataDetailReport());
    $excel->setContentMail();
    $excel->downloadExcel($title);
  }

  public function dataDetailReport() {
    $array = array();
    switch ($this->type) {
      case "clic":
        $where = array();
        $where['idMail'] = (string) $this->idMail;
        if (isset($this->stringSearch)) {
          $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
          $sql = "SELECT * FROM mail_link LEFT JOIN  mxl ON mail_link.idMail_link = mxl.idMail_link WHERE "
                  . " idMail = {$this->idMail} AND link = '{$this->stringSearch}' AND idAccount = {$idAccount}";
          $link = $this->db->fetchAll($sql);
          $where['idMailLink'] = (string) $link[0]['idMail_link'];
        }
        (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");
        $mxcxl = \Mxcxl::find([$where]);
//        var_dump($where);
//        var_dump(count($mxcxl));
//        
//        exit;
        foreach ($mxcxl as $key) {
          $obj = new \stdClass();
          $mailLink = \Maillink::findFirst(["conditions" => "idMail_link = ?0", "bind" => [0 => $key->idMailLink]]);
          $contact = \Contact::findFirst([["idContact" => (int) $key->idContact]]);
          $obj->idTmpTable = $contact->idContact;
          $obj->email = $contact->email;
          $obj->name = $contact->name;
          $obj->lastname = $contact->lastname;
          $obj->indicative = $contact->indicative;
          $obj->phone = $contact->phone;
          $obj->link = $mailLink->link;
          $obj->date = $key->uniqueClicks;
          array_push($array, $obj);
        }
        break;
      case "unsuscribe":
        $open = \Mxc::find(array(
          "conditions" => array(
            "idMail" => $this->idMail,
            "unsubscribed" => array(
              '$gt' => (int) 0
            )
          ),
          "fields" => array(
            "_id" => false,
            "unsubscribed" => true,
            "idContact" =>true,
            "email" => true,
            "name" => true,
            "lastname" => true,
            "indicative" => true,
            "phone" => true,
            "totalOpening" => true
          )
        ));
        foreach ($open as $value) {
          $unsuscribe = \Unsubscribed::findFirst([
            "conditions" => "idMail = ?0 AND idContact = ?1", 
            "bind" => array(0 => $this->idMail, 1 => $value->idContact)
          ]);
          $movite = "Ninguno";
          if($unsuscribe != false){
            $movite = $unsuscribe->motive != "Otro" ? $unsuscribe->motive : $unsuscribe->other;
          }
          $obj = new \stdClass();
          $obj->dateOpen = $value->scheduleDate;
          $obj->email = $value->email;
          $obj->name = $value->name;
          $obj->lastname = $value->lastname;
          $obj->indicative = $value->indicative;
          $obj->phone = $value->phone;
          $obj->motive = $movite;
          $value->motive = $movite;
          $array[] = $obj;
        }
        break;
      case "open":
      case "spam":
        $sql = "SELECT * FROM tmp_table";
        $array = $this->db->fetchAll($sql);
        break;
      case "bounced":
        $bounced = \Mxc::find(array(
          "conditions" => array(
            "idMail" => $this->idMail,
            "bounced" => ['$gte' => "1"]
          ),
          "fields" => array(
            "bounced" => true,
            "email" =>true,
            "name" => true,
            "lastname" => true,
            "indicative" => true,
            "phone" => true,
            "bouncedCode" => true
          )
        ));
//        var_dump(count($bounced));
//        var_dump($where);
//        exit;
        $array = array();
        foreach ($bounced as $key) {
          $c = new \stdClass();
          $c->name = $key->name;
          $c->lastname = $key->lastname;
          $c->indicative = $key->indicative;
          $c->phone = $key->phone;
          $c->date = $key->bounced;
          $c->email = $key->email;
          $sql = "SELECT * FROM bounced_code WHERE idBounced_code = {$key->bouncedCode}";
          $data = $this->db->fetchAll($sql);
          if ($key->bouncedCode == 10 || $key->bouncedCode == 90 || $key->bouncedCode == 200) {
            $c->type = "hard";
          } else {
            $c->type = "soft";
          }
          $c->description = $data[0]['description'];
          array_push($array, $c);
        }
        break;
      case "buzon":
        $data = \Mxc::find(array(
          "conditions" => array(
            "idMail" => $this->idMail,
            "status" => 'sent',
            "bounced" => (int) 0,
            "spam"=> (int) 0
          ),
          "fields" => array(
            "scheduleDate" => true,
            "email" =>true,
            "name" => true,
            "lastname" => true,
            "indicative" => true,
            "phone" => true
          )
        ));
        $array = array();
        foreach ($data as $value) {
          $obj = new \stdClass();
          $obj->dateOpen = $value->scheduleDate;
          $obj->email = $value->email;
          $obj->name = $value->name;
          $obj->lastname = $value->lastname;
          $obj->indicative = $value->indicative;
          $obj->phone = $value->phone;
          $obj->buzon = 1;
          array_push($array, $obj);
        }
        break;
    }
    return $array;
  }

  public function reportStaticsSms($title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsSms();
//    echo "<pre>";
//    var_dump($this->statics);
//    echo "</pre>";
//    exit();
    $excel->setData($this->statics);
    $excel->setTableInfoSms();
    $excel->generatedReportSms($this->idSms);
    $excel->downloadExcel($title);
  }

  public function reportStaticsSmsTwoway($title) {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsSms();
    $excel->setData($this->statics);
    $excel->setTableInfoSmsTwoWay();
    $excel->generatedReportSmsTwoWay($this->idSmsTwoway);
    $excel->downloadExcel($title);
  }

  public function getDetailSmsReport() {
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $this->idSms]]);
    if ($sms->type == "contact") {
      $data = \Smsxc::find([["idSms" => (string) $this->idSms]]);
    } else {
      $sql = "SELECT * FROM smslote WHERE idSms = {$this->idSms}";
      $data = $this->db->fetchAll($sql);
    }
    $sqlcountfailed = "SELECT count(*) as total FROM sms_failed WHERE idSms = {$this->idSms}";
    $countfailed = $this->db->fetchAll($sqlcountfailed);
    $failed = \SmsFailed::find(array("conditions" => "idSms = ?0", "bind" => array($this->idSms)));

    $this->statics["countfailed"] = $countfailed;
    $this->statics["failed"] = $failed;
    $this->statics["detail"] = $data;
  }

  public function getDetailSmsTwoWayReport() {
    $data = \Smslotetwoway::find(array("conditions" => "idSmsTwoway = ?0 ", "bind" => array($this->idSmsTwoway)));

    $this->statics["detail"] = $data;
  }

  public function getAllInfoSurvey() {
    $survey = \Survey::findFirst(array(
                'conditions' => 'idSurvey = ?0',
                'bind' => [$this->idSurvey]
    ));
    if (!$survey) {
      throw new \InvalidArgumentException('No se encontr?? la encuesta solicitada, por favor valide la informaci??n');
    }
    $survey->SurveyContent;
    $survey = json_decode(json_encode($survey), true);

    $array[] = "encabezado";
    $array[] = "button";

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    $optionsQuestion = array(
        'projection' => array('_id' => 0, 'idSurvey' => 1, 'idQuestion' => 1, 'component' => 1, 'question' => 1, 'count' => 1),
    );

    $query = [
        "idSurvey" => $this->idSurvey,
        "component" => ['$nin' => $array],
        "deleted" => 0
    ];

    $queryQuestion = new \MongoDB\Driver\Query($query, $optionsQuestion);
    $question = $manager->executeQuery("aio.question", $queryQuestion)->toArray();

    foreach ($question as $value) {
      $optionsAnswer = array(
        //'projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),
        'group' => array('_id' => '$answer', 'count' => ['$sum' => 1]),
      );

      $query = [
          "idQuestion" => $value->idQuestion,
          "deleted" => 0
      ];

      $queryAnswer = new \MongoDB\Driver\Query($query, $optionsAnswer);
      $answer = $manager->executeQuery("aio.answer", $queryAnswer)->toArray();
      $value->answer = $answer;
      $std1 = new \stdClass();
      $std2 = [];
      $acuTotal = 0;
      foreach ($answer as $item) {
        $std3 = new \stdClass();
        $std3->name = $item->answer;
        $std3->y = $item->count;
        $acuTotal += $item->count;
        $std2[] = $std3;
      }
      $std1->name = $value->question;
      $std1->data = $std2;
      $value->totalAnswer = $acuTotal;
      $value->chart = $std1;
    }

    $data = array();
    $data['survey'] = $survey;
    $data['questions'] = json_decode(json_encode($question), true);

    return $data;
  }

  public function getAllInfoSurveyReport() {
    $survey = \Survey::findFirst(array(
                'conditions' => 'idSurvey = ?0',
                'bind' => [$this->idSurvey]
    ));

    if (!$survey) {
      throw new \InvalidArgumentException('No se encontr?? la encuesta solicitada, por favor valide la informaci??n');
    }
    $survey->SurveyContent;
    $survey = json_decode(json_encode($survey), true);


    $array[] = "encabezado";
    $array[] = "button";

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    $optionsQuestion = array(
        'projection' => array('_id' => 0, 'idSurvey' => 1, 'idQuestion' => 1, 'component' => 1, 'question' => 1, 'count' => 1),
    );

    $query = [
        "idSurvey" => $this->idSurvey,
        "component" => ['$nin' => $array],
        "deleted" => 0
    ];

    $queryQuestion = new \MongoDB\Driver\Query($query, $optionsQuestion);
    $question = $manager->executeQuery("aio.question", $queryQuestion)->toArray();
    $one = 0;

    $aswerAr = array();

    $i = 0;
    if ($one == 0) {
      foreach ($question as $value) {
        $optionsAnswer = array('projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),);
        $query = ["idQuestion" => $value->idQuestion, "deleted" => 0];
        $queryAnswer = new \MongoDB\Driver\Query($query, $optionsAnswer);
        $answer = $manager->executeQuery("aio.answer", $queryAnswer)->toArray();

        foreach ($answer as $contacts) {

          foreach ($contacts->contacts as $keyuser =>$contactdate) {
            $con = $keyuser;
            $conta = \Contact::findFirst([["idContact" => (int) $con]]);

            $i++;
            $contactarray = new \stdClass();
            if ($conta == false) {
              $contactarray->idContact = $con;
              $contactarray->name = $con;
              $contactarray->lastname = 'sin datos';
              $contactarray->email = 'sin datos';
              $contactarray->dateandhour = $contactdate;
            } else {
              $contactarray->idContact = $con;
              $contactarray->name = $conta->name;
              $contactarray->lastname = $conta->lastname;
              $contactarray->email = $conta->email;
              $contactarray->dateandhour = $contactdate;
            }
            $contactarray->questions = array();
            foreach ($question as $value) {
              $idQuestion = $value->idQuestion;
              $obj = new \stdClass();
              $obj->question = $value->question;
              array_push($contactarray->questions, $obj);
              $obj->answer = array();

              $optionsAnswer = array('projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),);
              $query = ["idQuestion" => $value->idQuestion, "deleted" => 0];
              $queryAnswer = new \MongoDB\Driver\Query($query, $optionsAnswer);
              $answer = $manager->executeQuery("aio.answer", $queryAnswer)->toArray();

              foreach ($answer as $a) {
                $answeridQ = $a->idQuestion;
                if ($idQuestion == $answeridQ) {
                  foreach ($a->contacts as $keycont => $valuecont) {
                    if ($con == $keycont) {
                      array_push($obj->answer, $a->answer);
                    }
                  }
                }
              }
            }
            if ($i == 1) {
              array_push($aswerAr, $contactarray);
            }

            $stop = false;
            $stan = false;
            foreach ($aswerAr as $asy) {
              if ($asy->idContact == $contactarray->idContact) {
                $stan = true;
              } else {
                $stop = true;
              }
            }

            if ($stop == true & $stan == false) {
              array_push($aswerAr, $contactarray);
            }
          }
        }
      }

      $one = $one + 1;
    }

    $data = array();
    $data['survey'] = $survey;
    $data['questions'] = $question;
    $this->surveyInfo = $data;
    //return $aswerAr;
    
    $arrayConverted = array();
    //hay que convertir los objetos dentro del arreglo a arreglos tambien.
    foreach ($aswerAr as $value) { $arrayConverted[] = (array) $value; }
    
    $arrayConverted2 = $this->ordenarArray($arrayConverted, 'dateandhour', SORT_ASC); 

    $arrayConvertedtoObjects = array(); 
    foreach ($arrayConverted2 as $value) { $arrayConvertedtoObjects[] = (object) $value; } 

    //hay que ordenar el array por fechas para que salga ordenado ascendentemente el reporte... 
    return $arrayConvertedtoObjects;
  }

  public function reportStaticsSurvey($title) {

    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStaticsSurvey();
    $excel->setData($this->getAllInfoSurveyReport());
    $excel->infoSurvey = $this->surveyInfo;
    $excel->generatedReportSurvey();
    $excel->downloadExcel($title);
  }

  public function setIdMail($idMail) {
    $this->idMail = $idMail;
  }

  public function setIdSurvey($idSurvey) {
    $this->idSurvey = $idSurvey;
  }

  public function getStatics() {
    return $this->statics;
  }

  public function setPage($page) {
    $this->page = $page;
    return $this;
  }

  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  public function setStringSearch($stringSearch) {
    $this->stringSearch = $stringSearch;
    return $this;
  }

  public function setTypeFilter($typeFilter) {
    $this->typeFilter = $typeFilter;
    return $this;
  }

  public function setIdSms($idSms) {
    $this->idSms = $idSms;
    return $this;
  }

  public function setIdSmsTwoway($idSmstwoway) {
    $this->idSmsTwoway = $idSmstwoway;
    return $this;
  }

  public function setModelSmsTwoWay($model) {
    $his->objSmsTwoway = $model;
  }

  function setSearch($search) {
    $this->search = $search;
  }

  public function getDataClicksMail() {
    $idAccountSub = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;

    $sql = "SELECT
                    mail.idMail AS ID_MAIL,
                    mail.idSubaccount AS ID_SUB_ACCOUNT_MAIL,
                    mail.name AS NAME_MAIL,
                    mail.created AS CREATED_MAIL,
                    mail.updated AS UPDATED_MAIL,
                    mail.totalOpening AS TOTAL_OPEN_MAIL,
                    mail.uniqueClicks AS UNIQUE_CLICK_MAIL,
                    mail.createdBy AS CREATEDBY_MAIL,
                    subaccount.idSubaccount AS ID_SUBACCOUNT,
                    subaccount.idAccount AS ID_ACCOUNT_SUBACCOUNT,
                    account.idAccount AS IDACCOUNT_ACCOUNT,
                    account.idAllied AS IDALLIED_ACCOUNT,
                    account.name AS NAME_ACCOUNT
                FROM
                    mail
                        inner join
                    subaccount on subaccount.idSubaccount = mail.idSubaccount
                        inner join
                    account on account.idAccount = subaccount.idAccount
                WHERE
                    mail.idSubaccount = {$idAccountSub};";

    //get id mails and total clicks links emails
    $stringIdEmails = "";
    $data = $this->db->fetchAll($sql);
    $countData = count($data);
    $countTotalClicks = 0;
    foreach ($data as $value) {
      $stringIdEmails .= $value['ID_MAIL'] . ',';
      $countTotalClicks = $value['UNIQUE_CLICK_MAIL'] + $countTotalClicks;
    }

    $stringIdEmailsClear = rtrim($stringIdEmails, ',');

    $sqlCountLinksMail = "SELECT 
                                mxl.idMail,
                                mxl.idMail_link
                            FROM
                                mxl
                            WHERE
                                idMail in ({$stringIdEmailsClear});";



    //get total links for mail
    $dataLinks = $this->db->fetchAll($sqlCountLinksMail);
    $countDataLinks = count($dataLinks);
    $this->statics["info"][] = array('totalemail' => $countData,
        'totalallemailLinks' => $countDataLinks,
        'totaluniqueclickslinks' => $countTotalClicks,
        'dataMails' => $data);
  }

  public function getDataClickLink() {
    $idAccountSub = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";

    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }

    $sql = "SELECT
                      mail.idMail,
                      count(mail.idMail),
                      mail.uniqueClicks,
                      mail.totalOpening
                  FROM
                      mxl
                          INNER JOIN
                      mail ON mail.idMail = mxl.idMail
                          INNER JOIN
                      mail_link ON mail_link.idMail_link = mxl.idMail_link
                  WHERE
                      mail.idSubaccount = {$idAccountSub}
                      AND {$wheredate}
                  GROUP BY
                      1,3,4" . ";";
    $dataClickLinkTotal = $this->db->fetchAll($sql);
    $countClickLink = count($dataClickLinkTotal);
    $this->statics['info'][] = array('dataClickLink' => $dataClickLinkTotal);
  }

  public function getDataTotalsCamp($dateInitial = "", $dateFinal = "") {
    $idAccountSub = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;

    $filDate = "";
    if ($dateInitial != 0 && $dateFinal != 0) {
      $dateInitial = "0";
    }
    $sql = "SELECT 
                  mail.idMail
              FROM
                  mail
              WHERE
                  idSubaccount = {$idAccountSub};";
    $dataCampTotal = $this->db->fetchAll($sql);
    $countDataCamp = count($dataCampTotal);
    $this->statics['info'][] = array('totalCampSubAccount' => $countDataCamp);
  }

  public function getSentMailCamp() {
    $idAccountSub = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";

    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }

    $sql = "SELECT
                  mail.idMail,
                  mail.messagesSent
              FROM
                  mail
              WHERE
                  mail.idSubaccount = {$idAccountSub} 
                  AND {$wheredate}";
    $dataCampTotal = $this->db->fetchAll($sql);
    $this->statics['info'][] = array('dataMail' => $dataCampTotal);
  }

  public function getDataTotalsLinksCamp($dateInitial = "", $dateFinal = "") {
    $idAccountSub = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }

    if ($dateInitial != 0 && $dateFinal != 0) {
      $dateInitial = "0";
    }
    $sql = "SELECT 
                  mail.idMail AS ID_MAIL,
                  count(mail.idMail) AS NUM_LINKS_MAIL
              FROM
                  mxl
                    INNER JOIN
                  mail on mail.idMail = mxl.idMail
                    INNER JOIN
                  mail_link ON mail_link.idMail_link = mxl.idMail_link
              WHERE
                  mail.idSubaccount = {$idAccountSub} 
                  AND {$wheredate}
              GROUP BY 
                  mail.idMail;";
    $dataCampLinkTotal = $this->db->fetchAll($sql);
    $countDataLinkCamp = count($dataCampLinkTotal);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $dataCampLinkTotal);
  }

  public function getDataSmsSents() {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $sqlDataSms = "SELECT
                        sms.idSms AS ID_SMS,
                        sms.idSmsCategory AS ID_CATEGORIA,
                        sms.idSubaccount AS ID_SUB_ACCOUNT,
                        sms.name AS NAMA_SMS,
                        sms.sent AS SENT_SMS,
                        sms.total AS TOTAL_SMS,
                        sms.created AS DATE_CREATE,
                        sms.status AS SMS_STATUS
                    FROM
                        sms
                          inner join
                        subaccount on subaccount.idSubaccount = sms.idSubaccount
                          inner join
                        account on account.idAccount = subaccount.idAccount
                    WHERE
                        sms.idSubaccount = {$idAccountMail};";
    //get id sms and total sents sms
    $dataSmsData = $this->db->fetchAll($sqlDataSms);
    $countDataSms = count($dataSmsData);
    $countSmsSents = 0;
    $countTotalOk = 0;
    foreach ($dataSmsData as $value) {
      $countSmsSents = $value['SENT_SMS'];
      $countTotalOk = $value['TOTAL_SMS'];
    }
    $this->statics["info"][] = array('totalsentssms' => $countSmsSents,
        'totalsmsok' => $countTotalOk,
        'totalcampsms' => $countDataSms,
        'dataSms' => $dataSmsData);
  }

  public function getTotalSms() {
    $idAccountSms = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }

    $sql = "SELECT
                  sms.idSms
              FROM
                  sms
              WHERE
                  sms.idSubaccount = {$idAccountSms} 
                  AND {$wheredate};";
    $dataSmsData = $this->db->fetchAll($sql);
    $countDataSms = count($dataSmsData);
    $this->statics["info"][] = array('totalCountSms' => $countDataSms, 'dataSms' => $dataSmsData);
  }

  public function getTotalSentSms() {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }
    $sql = "SELECT
                  sms.idSms,
                  sms.sent
              FROM
                  sms
              WHERE
                  sms.idSubaccount = {$idAccountMail} 
                  AND {$wheredate};";
    $dataSmsData = $this->db->fetchAll($sql);
    $this->statics["info"][] = array('dataSmsSent' => $dataSmsData);
  }

  public function getSmsSentTotal() {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    if (isset($this->search->valueDateFinal) && isset($this->search->valueDateInitial)) {
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    } else {
      $this->search->valueDateInitial = date('Y-m-d', strtotime('-1 day'));
      $this->search->valueDateFinal = date('Y-m-d');
      $fechainiConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateInitial . ' 00:01:01'));
      $fechaFinConseg = date('Y-m-d H:i:s', strtotime($this->search->valueDateFinal . ' 23:59:59'));
      $dateInitial = strtotime($fechainiConseg);
      $dateFinal = strtotime($fechaFinConseg);
      $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    }
    $sql = "SELECT
                  sms.idSms,
                  sms.sent,
                  sms.total
              FROM
                  sms
              WHERE
                  sms.idSubaccount = {$idAccountMail} 
                  AND {$wheredate};";
    $dataSmsData = $this->db->fetchAll($sql);
    $this->statics["info"][] = array('dataSmsSent' => $dataSmsData);
  }

  public function getChargeInitialCamp() {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $sql = "SELECT 
                (SELECT 
                        COUNT(*)
                    FROM
                        sms
                    WHERE
                        sms.idSubaccount = {$idAccountMail}) AS countSms,
                (SELECT 
                        COUNT(*)
                    FROM
                        mail
                    WHERE
                        mail.idSubaccount = {$idAccountMail}) AS countMail;";
    $dataSmsData = $this->db->fetchAll($sql);
    $this->statics["info"][] = array('dataCamp' => $dataSmsData);
  }

  public function getDataMailDate($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";

    if ($timeValue == 1) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 2) {

      $yearI = date('Y');
      $monthI = date('m') - 1;
      $dayI = '1';
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m') - 1;
      $dayF = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 3) {

      $yearI = date('Y') - 1;
      $monthI = 1;
      $dayI = '1';
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y') - 1;
      $monthF = 12;
      $dayF = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $this->db->execute('SET lc_time_names = "es_MX";');
    $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    $sql = "SELECT
              sum(mail.messagesSent) AS messagesSent,
              date_format(from_unixtime(mail.created),'%{$timespecific}') AS timeSpecific
            FROM
              mail
            WHERE
              mail.idSubaccount = {$idAccountMail} 
              AND {$wheredate}
            group by 2
            order by mail.created asc;";

    $dataCampLinkTotal = $this->db->fetchAll($sql);
    $this->db->execute('SET lc_time_names = "en_US";');
    $countDataLinkCamp = count($dataCampLinkTotal);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $dataCampLinkTotal);
  }

  public function getDateDateDay($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";

    if ($timeValue == 4) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d');
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 5) {

      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d') - 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      //$dayF = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
      $dayF = date('d') - 1;
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 6) {

      $yearI = date('Y');
      $monthI = date('m');
      $dayI = '1';
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $this->db->execute('SET lc_time_names = "es_MX";');
    $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    $sql = "SELECT
              sum(mail.messagesSent) AS messagesSent,
              date_format(from_unixtime(mail.created),'%{$timespecific}') AS timeSpecific
            FROM
              mail
            WHERE
              mail.idSubaccount = {$idAccountMail} 
              AND {$wheredate}
            group by 2
            order by mail.created asc;";

    $dataCampLinkTotal = $this->db->fetchAll($sql);
    $this->db->execute('SET lc_time_names = "en_US";');
    $countDataLinkCamp = count($dataCampLinkTotal);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $dataCampLinkTotal);
  }

  public function staticOpenCamp($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";
    if ($timeValue == 7) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d');
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 8) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d') - 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d') - 1;
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 9) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $sql = "SELECT 
              idMail
            FROM
              mail
            WHERE
              idSubaccount = {$idAccountMail};";
    $dataIdMail = $this->db->fetchAll($sql);
    $arraySingle = [];
    $count = 0;
    foreach ($dataIdMail as $value) {
      $arraySingle[$count] = (string) $value['idMail'];
      $count++;
    }

    $datos = array
        (
        array(
            '$match' => array(
                "idMail" => array(
                    '$in' => $arraySingle
                ),
                'open' => array(
                    '$gte' => $dateInitial,
                    '$lt' => $dateFinal
                )
            )
        ),
        array
            (
            '$group' => array
                (
                '_id' => array
                    (
                    '$dateToString' => array
                        (
                        'format' => "'%{$timespecific}'",
                        'date' => array
                            (
                            '$add' => [
                                new \MongoDate(0),
                                array
                                    (
                                    '$multiply' => [1000, '$open']
                                )
                            ]
                        )
                    )
                ),
                'total' => array('$sum' => 1)
            )
        ),
        array
            (
            '$project' => array
                (
                'messagesSent' => '$total',
                'timeSpecific' => '$_id'
            )
        ),
        array
            (
            '$sort' => array
                (
                'open' => 1
            )
        )
    );
    $open = \Mxc::aggregate($datos);
    $this->statics['info'][] = array('dataLinks' => $open["result"]);
  }

  public function staticsUniqueClicks($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";
    if ($timeValue == 10) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d');
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 11) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d') - 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d') - 1;
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 12) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $this->db->execute('SET lc_time_names = "es_MX";');
    $wheredate = "mail.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    $sql = "SELECT 
              idMail
            FROM
              mail
            WHERE
              idSubaccount = {$idAccountMail};";
    $dataIdMail = $this->db->fetchAll($sql);
    $this->db->execute('SET lc_time_names = "en_US";');
    $arraySingle = [];
    $count = 0;
    foreach ($dataIdMail as $value) {
      $arraySingle[$count] = (string) $value['idMail'];
      $count++;
    }

    $arrayDatos = array(
        array(
            'idMail' => array(
                '$in' => $arraySingle
            ),
            'uniqueClicks' => array(
                '$gte' => $dateInitial,
                '$lt' => $dateFinal
            )
        ),
        array(
            'idMail' => 1,
            'uniqueClicks' => 1
        )
    );
    $open = \Mxc::find($arrayDatos);
    //var_dump($open);exit;
    $countId = 0;
    $arrayData = array();
    foreach ($open as $key => $value) {
      $arrayData[$key] = array(
          'idMail' => $value->idMail,
          'countId' => $countId,
          'uniqueClicks' => date("{$timespecific}", $value->uniqueClicks)
      );
    }

    $arrayDataRepuest = array();
    foreach ($arrayData as $key => $value) {
      $countIdMail = $this->countIdMailRepeat($arrayData, $value['idMail'], $value['uniqueClicks']);
      $arrayDataRepuest[$key] = array('messagesSent' => $countIdMail, 'timeSpecific' => $value['uniqueClicks']);
    }

    $countDataLinkCamp = count($arrayCount);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $arrayDataRepuest);
  }

  public function countIdMailRepeat($arrayToFind, $idMail, $hourMail) {
    $countId = 0;
    foreach ($arrayToFind as $key => $value) {
      if ($idMail == $arrayToFind[$key]['idMail'] && $arrayToFind[$key]['uniqueClicks'] == $hourMail) {
        $countId++;
      }
    }
    return $countId;
  }

  public function getSmsSentsForDay($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";
    if ($timeValue == 13) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d');
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 14) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d') - 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d') - 1;
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 15) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $this->db->execute('SET lc_time_names = "es_MX";');
    $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    $sql = "SELECT
              sum(sms.sent) AS messagesSent,
              date_format(from_unixtime(sms.created),'%{$timespecific}') AS timeSpecific
            FROM
              sms
            WHERE
              sms.idSubaccount = {$idAccountMail} 
              AND {$wheredate}
            group by 2
            order by sms.created asc;";

    $dataCampLinkTotal = $this->db->fetchAll($sql);
    $this->db->execute('SET lc_time_names = "en_US";');
    $countDataLinkCamp = count($dataCampLinkTotal);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $dataCampLinkTotal, 'sql' => $sql);
  }

  public function getCampSmsForDay($timeValue, $timespecific) {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $wheredate = " 1 = 1 ";
    $dateInitial = "";
    $dateFinal = "";
    if ($timeValue == 16) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d');
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 17) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = date('d') - 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d') - 1;
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    } else if ($timeValue == 18) {
      $yearI = date('Y');
      $monthI = date('m');
      $dayI = 1;
      $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
      $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
      $dateInitial = strtotime($dateIniUnix);

      $yearF = date('Y');
      $monthF = date('m');
      $dayF = date('d');
      $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
      $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
      $dateFinal = strtotime($dateFinUnix);
    }

    $this->db->execute('SET lc_time_names = "es_MX";');
    $wheredate = "sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}' ";
    $sql = "SELECT
              COUNT(sms.idSms) AS messagesSent,
              date_format(from_unixtime(sms.created),'%{$timespecific}') AS timeSpecific
            FROM
              sms
            WHERE
              sms.idSubaccount = {$idAccountMail} 
              AND {$wheredate}
            group by 2
            order by sms.created asc;";

    $dataCampLinkTotal = $this->db->fetchAll($sql);
    $this->db->execute('SET lc_time_names = "en_US";');
    $countDataLinkCamp = count($dataCampLinkTotal);
    $this->statics['info'][] = array('totalLinkCamp' => $countDataLinkCamp, 'dataLinks' => $dataCampLinkTotal, 'sql' => $sql);
  }

  //funcion empleada guardar la data que almacenara la informacion de estadisticas de campa??a automatica
  public function getConfiguration() {
    return $this->configuration;
  }

//funcion que se encarga de crear la data de la estadistica de campa??as automaticas

  public function getIdContactlistBySegments($listSegment) {
    foreach ($listSegment as $key) {
      $segment = \Segment::findFirst([["idSegment" => $key->idSegment]]);
      foreach ($segment->contactlist as $k) {
        $this->idContactlist[] = $k["idContactlist"];
      }
    }
  }

  public function getIdContaclist($target) {
    $target = $target;
    switch ($target->type) {
      case "contactlist":
        if (isset($target->contactlists)) {
          foreach ($target->contactlists as $key) {
            $this->idContactlist[] = $key->idContactlist;
          }
        }
        break;
      case "segment":
        if (isset($target->segment)) {
          $this->getIdContactlistBySegments($target->segment);
        }
        break;
      default:
        throw new Exception("Se ha producido un error no se ha encontrado un tipo de destinatarios");
    }
  }

  public function getallConfiguration($idAutomaticcampaign) {

    $automaticCampaignConfiguration = \AutomaticCampaignConfiguration::findFirst(
                    array("conditions" => "idAutomaticCampaign = ?0",
                        "bind" => array($idAutomaticcampaign)));

    $automaticCampaign = \AutomaticCampaign::findFirst(array(
                'conditions' => 'idAutomaticCampaign = ?0',
                'bind' => [$idAutomaticcampaign]));

    $configuration = json_decode($automaticCampaignConfiguration->configuration);

    $automaticCampaignStatictis = new \stdClass();
    $dataStatictisCampaign = new \stdClass();
    $arrayStatictis = array();
    $cont = 0;

    $prueba = null;

    // for de los nodos de la campa??a automatica
    for ($index = 0; $index < count($configuration->nodes); $index++) {

      $statictisData = new \stdClass();

      $totalOpening = 0;
      $totalClicks = 0;
      $spam = 0;
      $bounced = 0;
      $sent = 0;
      $Notshipped = 0;

      $primary = false;

      $automaticCampaignStatictis = $configuration;
      $automaticCampaignStep = \AutomaticCampaignStep::find(array("conditions" => "idAutomaticCampaign = ?0 and idNode= ?1",
                  "bind" => array($idAutomaticcampaign, $configuration->nodes[$index]->id)));

      if ($configuration->nodes[$index]->method == "primary") {

        $automaticCampaignObj = new \Sigmamovil\General\Misc\AutomaticCampaignObj($automaticCampaign, $automaticCampaignConfiguration);
        $FirstNode = $automaticCampaignObj->getNode(0);
        $target = json_decode($automaticCampaignObj->transformTarget($FirstNode->sendData));
        $this->getIdContaclist($target);

        $contac = 0;

        foreach ($this->idContactlist as $value) {
          $contacList = \Contactlist::findfirst(array("conditions" => "idContactlist = ?0 ",
                      "bind" => array($value)));
          $contac = $contac + $contacList->cactive;
        }

        $statictisData->typeRecipients = $FirstNode->sendData->list->name;
        $statictisData->listcontacname = $target->contactlists[0]->name;
        $statictisData->totalContac = $contac;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $configuration->nodes[$index]->method;
        $primary = true;
        $statictisData->primary = $primary;

        $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
      }
      if ($configuration->nodes[$index]->method == "actions") {

        $statictisData->selectactionname = $configuration->nodes[$index]->sendData->selectAction->name;
        $statictisData->timename = $configuration->nodes[$index]->sendData->time->name;
        $statictisData->timetwoname = $configuration->nodes[$index]->sendData->timetwo->name;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $configuration->nodes[$index]->method;


        if ($configuration->nodes[$index]->sendData->selectAction->id == 6) {
          if (isset($configuration->nodes[$index]->sendData->quest)) {
            $statictisData->question = $configuration->nodes[$index]->sendData->quest->question;
          }

          if ($configuration->nodes[4]->sendData->condition->id == 1) {
            $statictisData->condition = $configuration->nodes[$index]->sendData->condition->name;
            $statictisData->answer = $configuration->nodes[$index]->sendData->answer->answer;
          }
        }

        $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
      }
      if ($configuration->nodes[$index]->method == "time") {
        $statictisData->timeName = $configuration->nodes[$index]->sendData->time->name;
        $statictisData->timetwoName = $configuration->nodes[$index]->sendData->timetwo->name;
        $statictisData->text = $configuration->nodes[$index]->sendData->text;
        $statictisData->textTitle = $configuration->nodes[$index]->sendData->textTitle;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $configuration->nodes[$index]->method;

        $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
      }
      if ($configuration->nodes[$index]->method == "email") {

        if (count($automaticCampaignStep) > 0) {
          foreach ($automaticCampaignStep as $value) {
            $totalOpening = $totalOpening + $value->totalOpening;
            $totalClicks = $totalClicks + $value->totalClicks;
            if ($value->status == "sent") {
              $sent = $sent + 1;
            }
            if ($value->status == "canceled") {
              $Notshipped = $Notshipped + 1;
            }
            $bounced = $bounced + $automaticCampaignStep->bounced;
            $spam = $spam + $value->spam;
          }
          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->totalOpening = $totalOpening;
          $statictisData->totalClicks = $totalClicks;
          $statictisData->sent = $sent;
          $statictisData->Notshipped = $Notshipped;
          $statictisData->bounced = $bounced;
          $statictisData->spam = $spam;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;


          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
        } else {
          $sql = "SELECT
                        quantitytarget,
                        uniqueClicks,
                        uniqueOpening,
                        totalClicks,
                        totalOpening,
                        spam,
                        bounced,
                        messagesSent
                    FROM
                        mail
                    WHERE
                        idAutomaticCampaign ={$idAutomaticcampaign}";
          $mail = (object) $this->db->fetchAll($sql)[0];

          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->totalOpening = $mail->uniqueOpening;
          $statictisData->totalClicks = $mail->totalClicks;
          $statictisData->sent = $mail->messagesSent;
          $statictisData->Notshipped = $mail->quantitytarget - $mail->messagesSent;
          $statictisData->bounced = $mail->bounced;
          $statictisData->spam = $mail->spam;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;

          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
        }
      }
      if ($configuration->nodes[$index]->method == "sms") {
        if (count($automaticCampaignStep) > 0) {

          foreach ($automaticCampaignStep as $value) {
            if ($value->statusSms == "delivered") {
              $sent = $sent + 1;
            } else if ($value->statusSms == "undelivered" || $value->statusSms == "canceled") {
              $Notshipped = $Notshipped + 1;
            }
          }

          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->sent = $sent;
          $statictisData->Notshipped = $Notshipped;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;

          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
        } else {
          $sql = "SELECT
                        sms.sent,
                        sms.quantity,
                        count(sms_failed.idSmsfailed) sms_failed
                    FROM
                        sms
                    INNER JOIN sms_failed ON sms.idSms = sms_failed.idSms
                    WHERE
                        sms.idAutomaticCampaign = {$idAutomaticcampaign}
                    AND sms_failed.idSms = sms.idSms";

          $sms = (object) $this->db->fetchAll($sql)[0];

          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->sent = $sms->sent;
          $statictisData->Notshipped = $sms->sms_failed;
          $statictisData->sms = true;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;

          if (!isset($statictisData->sent)) {
            $statictisData->sent = 0;
          }

          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
        }
      }
      if ($configuration->nodes[$index]->method == "survey") {
        if (count($automaticCampaignStep) > 0) {
          foreach ($automaticCampaignStep as $value) {
            $totalOpening = $totalOpening + $value->totalOpening;
            $totalClicks = $totalClicks + $value->totalClicks;
            if ($value->status == "sent") {
              $sent = $sent + 1;
            }
            if ($value->status != null) {
              $Notshipped = $Notshipped + 1;
            }
            $bounced = $bounced + $value->bounced;
            $spam = $spam + $value->spam;
          }
          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->totalOpening = $totalOpening;
          $statictisData->totalClicks = $totalClicks;
          $statictisData->sent = $sent;
          $statictisData->Notshipped = $Notshipped;
          $statictisData->bounced = $bounced;
          $statictisData->spam = $spam;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;

          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;

//          $manager = \Phalcon\DI::getDefault()->get('mongomanager');
//          $optionsQuestion = array(
//              'projection' => array('_id' => 0, 'idQuestion' => 1),
//          );
//          $query = [
//              "idSurvey" => $configuration->nodes[3]->sendData->publicsurvey->idSurvey,
//              "deleted" => 0
//          ];
//          $queryQuestion = new \MongoDB\Driver\Query($query, $optionsQuestion);
//          $questions = (array) $manager->executeQuery("aio.question", $queryQuestion)->toArray();
//          $contacs = NULL;
//
//          if ($configuration->nodes[0]->sendData->list->name == "Listas de contactos") {
//            $sql = "SELECT
//                          idContact
//                      FROM
//                          cxcl
//                      WHERE
//                          idContactlist ={$configuration->nodes[0]->sendData->selecteds[0]->idContactlist}";
//
//            $contacs = (array) $this->db->fetchAll($sql);
//          }
        //
        } else {
          $sql = "SELECT
                        quantitytarget,
                        uniqueClicks,
                        uniqueOpening,
                        totalClicks,
                        totalOpening,
                        spam,
                        bounced,
                        messagesSent
                    FROM
                        mail
                    WHERE
                        idAutomaticCampaign ={$idAutomaticcampaign}";
          $mail = (object) $this->db->fetchAll($sql)[0];

          $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
          $statictisData->totalOpening = $mail->totalOpening;
          $statictisData->totalClicks = $mail->totalClicks;
          $statictisData->sent = $mail->messagesSent;
          $statictisData->Notshipped = $mail->quantitytarget - $mail->messagesSent;
          $statictisData->bounced = $mail->bounced;
          $statictisData->spam = $mail->spam;
          $statictisData->nodeTitle = "Estadistica";
          $statictisData->nodeType = $configuration->nodes[$index]->method;

          $automaticCampaignStatictis->nodes[$index]->statictis = $statictisData;
        }
      }
    }

    $this->configuration = json_encode($automaticCampaignStatictis);
  }

  /**
   * Function get the data initial mail, sms, opens, month
   */
  public function getInfoInitialMonth() {
    $idAccountMail = \Phalcon\DI::getDefault()->get('user')->usertype->idSubaccount;
    $dateInitial = "";
    $dateFinal = "";

    $yearI = date('Y');
    $monthI = date('m');
    $dayI = 1;
    $dateStringI = $yearI . '-' . $monthI . '-' . $dayI;
    $dateIniUnix = date('Y-m-d H:i:s', strtotime($dateStringI . ' 00:01:01'));
    $dateInitial = strtotime($dateIniUnix);

    $yearF = date('Y');
    $monthF = date('m');
    $dayF = date('d');
    $dateStringF = $yearF . '-' . $monthF . '-' . $dayF;
    $dateFinUnix = date('Y-m-d H:i:s', strtotime($dateStringF . ' 23:59:59'));
    $dateFinal = strtotime($dateFinUnix);

    $sql = "SELECT 
              idMail
            FROM
              mail
            WHERE
              idSubaccount = {$idAccountMail};";
    $dataIdMail = $this->db->fetchAll($sql);
    $arraySingle = [];
    $count = 0;
    foreach ($dataIdMail as $value) {
      $arraySingle[$count] = (string) $value['idMail'];
      $count++;
    }
    $datos = array
        (
        array(
            'idMail' => array(
                '$in' => $arraySingle
            ),
            'open' => array(
                '$gte' => $dateInitial,
                '$lt' => $dateFinal
            )
        ),
    );
    $mailOpenMonth = \Mxc::count($datos);

    $bounced = \Mxc::find([["bounced" => ['$gte' => "1"], "idMail" => $this->idMail]]);
    $arr = array();
    $soft = 0;
    $hard = 0;
    foreach ($bounced as $key) {
      if ($key->bouncedCode == 10 || $key->bouncedCode == 90 || $key->bouncedCode == 200) {
        $hard++;
      } else {
        $soft++;
      }
    }

    $sqlQuantityPoll = "SELECT 
                            count(idSurvey) AS quantityPoll
                        FROM
                            survey
                        WHERE
                            idSubaccount = {$idAccountMail} AND
                            created BETWEEN '{$dateInitial}' AND '{$dateFinal}';";
    $dataQuantityPoll = $this->db->fetchAll($sqlQuantityPoll);

    $sqlQualitySmsMonth = "SELECT 
                              count(sms.idSubaccount) AS quantitySmsMonth
                          FROM
                              sms
                          WHERE
                              sms.idSubaccount = {$idAccountMail} AND
                              sms.created BETWEEN '{$dateInitial}' AND '{$dateFinal}';";
    $dataQualitySmsMonth = $this->db->fetchAll($sqlQualitySmsMonth);

    $this->statics['info'][] = array('mailOpenMonth' => $mailOpenMonth,
        'bouncedHard' => $hard,
        'bouncedSoft' => $soft,
        'qualitypoll' => (int) $dataQuantityPoll[0]['quantityPoll'],
        'quantitySmsMonth' => (int) $dataQualitySmsMonth[0]['quantitySmsMonth']);
  }

  public function getacbynode($data) {
    var_dump($data["type"]);
    $idAutomaticCampaign = $data["idAutomaticCampaign"];
    $idAutomaticCampaignNodo = $data["idNodo"];
    $service = $data["type"];

    if (!$idAutomaticCampaign) {
      throw new \InvalidArgumentException("La campa??a consultada no se encuentra registrada.");
    }
    if ($service == "sms") {
      $sql = "select *
            from
              aio.automatic_campaign_step AS acs
              INNER JOIN aio.sms ON acs.idAutomaticCampaign = aio.sms.idAutomaticCampaign
            where 
              acs.idAutomaticCampaign = {$idAutomaticCampaign} and acs.idNode={$idAutomaticCampaignNodo} LIMIT 1";
      $ac = $this->db->fetchAll($sql);
    }
    if ($service == "mail") {
      $sql = "select *
            from
              aio.automatic_campaign_step AS acs
              INNER JOIN aio.mail as m ON acs.idAutomaticCampaign = m.idAutomaticCampaign
            where 
              acs.idAutomaticCampaign = {$idAutomaticCampaign} and acs.idNode={$idAutomaticCampaignNodo} LIMIT 1";
      $ac = $this->db->fetchAll($sql);
    }
    if (!$ac) {
      throw new \InvalidArgumentException("No se han encontrado registros");
    } else {
      return $ac;
    }
  }

  public function isBirthdaySms($id) {
    $result = false;
    $sms = \Sms::findFirst([
                "conditions" => "idSms = ?0",
                "bind" => [0 => $id]
    ]);
    if (isset($sms->idAutoresponder) && !($sms->idAutoresponder == null)) {
      $autoresponder = \Autoresponder::findFirst(array(
                  "conditions" => "idAutoresponder = ?0",
                  "bind" => array($sms->idAutoresponder))
      );
      $result = ($autoresponder->birthdate) ? true : false;
      return $result; //como es uno o cero dira si es verdadero o falso
    } else {
      return false;
    }
  }

  public function buzonTotal() {
    $where = [
        "idMail" => $this->idMail,
        "status" => 'sent',
        "bounced" => 0,
        "spam" => 0
    ];
    $buzon = \Mxc::count([$where]);
    if($this->idMail == 15961){
      $this->statics["buzon"] = 12828;
    } else {
      $this->statics["buzon"] = $buzon;
    }
  }

  public function staticsBuzon() {
    /*$where = [
        "idMail" => $this->idMail,
        "status" => 'sent',
        "bounced" => 0,
        "spam" => 0
    ];
    $data = \Mxc::find([$where]);*/
  $data = \Mxc::find(array(
    "conditions" => array(
            "idMail" => $this->idMail,
            "status" => 'sent',
            "bounced" => (int) 0,
            "spam"=> (int) 0
            
    ),
    "fields" => array(
          "scheduleDate" => true,
          "email" =>true,
          "name" => true,
          "lastname" => true,
          "indicative" => true,
          "phone" => true
          )
  ));
    $arr = array();
    foreach ($data as $value) {
      $obj = new \stdClass();
      $obj->dateOpen = $value->scheduleDate;
      $obj->email = $value->email;
      $obj->name = $value->name;
      $obj->lastname = $value->lastname;
      $obj->indicative = $value->indicative;
      $obj->phone = $value->phone;
      $obj->buzon = 1;
      array_push($arr, $obj);
 
    }
    unset($data);
    return $data;

  
  }
  
  public function ordenarArray($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

  public function getAllAutomaticConfiguration($idAutomaticcampaign){
    $sql = "SELECT * FROM automatic_campaign_configuration WHERE idAutomaticCampaign = {$idAutomaticcampaign} ";
    $automaticCampaignConfiguration = (object) $this->db->fetchAll($sql)[0];
    unset($sql);
    $configuration = json_decode($automaticCampaignConfiguration->configuration);
    $automaticCampaignStatictis = new \stdClass();
    $automaticCampaignStatictis = $configuration;
    foreach ($configuration->nodes as $key => $nodes){
      //
      if ($nodes->method == "primary") {
        $contac = 0;
        foreach ($nodes->sendData->selecteds as $value) {
          $contacList = \Contactlist::findfirst(array("conditions" => "idContactlist = ?0 AND deleted = 0 ","bind" => array($value->idContactlist)));
          $contac = $contac + $contacList->cactive;
        }
        $statictisData->typeRecipients = $nodes->sendData->textTitle;
        $statictisData->listcontacname = $nodes->sendData->text;
        $statictisData->totalContac = $contac;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $nodes->method;
        $statictisData->primary = true;
        //
        $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
        unset($statictisData);
      }
      //
      if ($nodes->method == "actions") {
        $statictisData->selectactionname = $nodes->sendData->selectAction->name;
        $statictisData->timename = $nodes->sendData->time->name;
        $statictisData->timetwoname = $nodes->sendData->timetwo->name;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $nodes->method;
        //
        $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
        unset($statictisData);
      }
      //
      if ($nodes->method == "time") {
        $statictisData->timeName = $nodes->sendData->time->name;
        $statictisData->timetwoName = $nodes->sendData->timetwo->name;
        $statictisData->text = $nodes->sendData->text;
        $statictisData->textTitle = $nodes->sendData->textTitle;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $nodes->method;
        //
        $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
        unset($statictisData);
      }
      //
      if ($nodes->method == "email") {
        $step = \AutomaticCampaignStep::findFirst(array("conditions" => "idAutomaticCampaign = ?0 and idNode= ?1","bind" => array($idAutomaticcampaign, $nodes->id)));
        if(isset($step->idMail)){
          $mail = \Mail::findFirst(array("conditions" => "idMail = ?0 AND status = ?1 ","bind" => array($step->idMail, 'sent')));
          //
          $mxc = \Mxc::count(array("conditions" => "idMail = ?0 AND bounced > ?1 AND bouncedCode = ?2","bind" => array($step->idMail, 0, '22')));
          //
          if($mail != false){
            $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
            $statictisData->idMail = $mail->idMail;
            $statictisData->totalOpening = $mail->uniqueOpening;
            $statictisData->uniqueClicks = (is_null($mail->uniqueClicks)) ? 0 : $mail->uniqueClicks;
            $statictisData->sent = $mail->messagesSent;
            $statictisData->Notshipped = $mxc != false ? $mxc : 0;
            $statictisData->bounced = $mail->bounced;
            $statictisData->spam = $mail->spam;
            $statictisData->nodeTitle = "Estadistica";
            $statictisData->nodeType = $nodes->method;
            //
            $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
            unset($statictisData);
            //
          } else {
            $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
            $statictisData->totalOpening = 0;
            $statictisData->uniqueClicks = 0;
            $statictisData->sent = 0;
            $statictisData->Notshipped = 0;
            $statictisData->bounced = 0;
            $statictisData->spam = 0;
            $statictisData->nodeTitle = "Estadistica";
            $statictisData->nodeType = $nodes->method;
            //
            $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
            unset($statictisData);
          }
          unset($step);
          unset($mxc);
          unset($statictisData);
        }        
      }
      //
      if ($nodes->method == "sms") {
        $sql = "SELECT * FROM automatic_campaign_step WHERE idAutomaticCampaign = {$idAutomaticcampaign} AND idNode = {$nodes->id} ";
        $step = (object) $this->db->fetchAll($sql)[0];
        if(isset($step->idSms)){
          $sms = \Sms::findFirst(array("conditions" => "idSms = ?0 AND status = ?1 ","bind" => array($step->idSms, 'sent')));
          //
          if($sms != false){
            $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
            $statictisData->idSms = $sms->idSms;
            $statictisData->sent = $sms->sent ;
            $statictisData->Notshipped = $sms->total - $sms->sent;
            $statictisData->nodeTitle = "Estadistica";
            $statictisData->nodeType = $nodes->method;
            //
            $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
            unset($statictisData);
          } else {
            $statictisData->idAutomaticCampaign = $idAutomaticcampaign;
            $statictisData->sent = 0 ;
            $statictisData->Notshipped = 0;
            $statictisData->nodeTitle = "Estadistica";
            $statictisData->nodeType = $nodes->method;
            //
            $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
            unset($statictisData);
          }
          unset($step);
          unset($mxc);
          unset($statictisData);
        }
      }
      //
      if ($nodes->method == "clicks") {
        $statictisData->timename = $nodes->sendData->time->name;
        $statictisData->timetwoname = $nodes->sendData->timetwo->name;
        $statictisData->nodeTitle = "Informaci??n del nodo";
        $statictisData->nodeType = $nodes->method;
        //
        $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
        unset($statictisData);
      }
      //
      if ($nodes->method == "links") {
        /*$idMailLink = array();
        
       if(isset($mail->idMail)){
           
           $mxl = \Mxl::find(array(
               'conditions' => 'idMail = ?1',
               'bind' => array(1 => $mail->idMail)
           ));
           
           foreach($mxl as $value){
               $idMailLink[] = $value->idMail_link;
           }
           $listId = implode(",", $idMailLink);

           $sqlML = "SELECT * FROM mail_link WHERE link = '{$nodes->sendData->text}' AND idMail_link in ({$listId}) ";
           $mailLink = (object) $this->db->fetchAll($sqlML)[0];
           
           if(isset($mailLink->idMail_link)){
               
               $mxl2 = \Mxl::findFirst(array(
                   'conditions' => 'idMail = ?1 AND idMail_link = ?2',
                   'bind' => array(1 => $mail->idMail, 2 => $mailLink->idMail_link)
               ));
               
               \Phalcon\DI::getDefault()->get('logger')->log("listId ".json_encode($mxl2));
               
               $statictisData->linkname = $nodes->sendData->text;
               $statictisData->totalClicks = $mxl2->totalClicks;
               $statictisData->idMail = $mail->idMail;
               $statictisData->timename = $nodes->sendData->time->name;
               $statictisData->timetwoname = $nodes->sendData->timetwo->name;
               $statictisData->nodeTitle = "Informaci??n del nodo";
               $statictisData->nodeType = $nodes->method;
               //
               $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
               unset($statictisData);
           }else{
               $statictisData->linkname = $nodes->sendData->text;
               $statictisData->totalClicks = 0;
               $statictisData->idMail = $mail->idMail;
               $statictisData->timename = $nodes->sendData->time->name;
               $statictisData->timetwoname = $nodes->sendData->timetwo->name;
               $statictisData->nodeTitle = "Informaci??n del nodo";
               $statictisData->nodeType = $nodes->method;
               //
               $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
               unset($statictisData);
           }

       }else{*/
           $statictisData->linkname = $nodes->sendData->text;
           //$statictisData->totalClicks = 0;
           //$statictisData->idMail = $mail->idMail;
           $statictisData->timename = $nodes->sendData->time->name;
           $statictisData->timetwoname = $nodes->sendData->timetwo->name;
           $statictisData->nodeTitle = "Informaci??n del nodo";
           $statictisData->nodeType = $nodes->method;
           //
           $automaticCampaignStatictis->nodes[$key]->statictis = $statictisData;
           unset($statictisData);
      //}

     }
    } 
    $this->configuration = json_encode($automaticCampaignStatictis);
  }

  public function dataInfoMail() {
    (($this->page > 0) ? $this->page = ($this->page * $this->limit) : "");

    switch ($this->type) {
      case "open":
        $where = [
            "idMail" => $this->idMail,
            "open" => array('$type' => (int) 18)
        ];
        $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $this->page]);
        $total = \Mxc::count([$where]);
        unset($where);
        $data = array();
        foreach ($mxc as $value) {
            $obj = new \stdClass();
            $obj->dateOpen = $value->scheduleDate;
            $obj->email = $value->email;
            $obj->name = $value->name;
            $obj->lastname = $value->lastname;
            $obj->indicative = $value->indicative;
            $obj->phone = $value->phone;
            array_push($data, $obj);
            unset($obj);
        }
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
        unset($total);
        unset($data);
        break;
      case "clic":
        $array = array();
        $where = array();
        $where['idMail'] = $this->idMail;
        $mxcxl = \Mxcxl::find([$where, "limit" => $this->limit, "skip" => $this->page]);

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
          array_push($array, $obj);
          unset($obj);
        }
        $open = \Mxcxl::count([$where]);
        $this->statics["info"][] = $array;
        $this->statics["info"][] = array("total" => count($array), "total_pages" => ceil($open / $this->limit));
        unset($mxcxl);
        unset($open);
        unset($array);
        break;
      case "unsuscribe":
        $where = [
            "idMail" => $this->idMail,
            "unsubscribed" => array('$gte' => (int) 1)
        ];
        $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $this->page]);
        $total = \Mxc::count([$where]);
        unset($where);
        $data = array();
        foreach ($mxc as $value) {
            $obj = new \stdClass();
            $obj->dateOpen = $value->scheduleDate;
            $obj->email = $value->email;
            $obj->name = $value->name;
            $obj->lastname = $value->lastname;
            $obj->indicative = $value->indicative;
            $obj->phone = $value->phone;
            array_push($data, $obj);
            unset($obj);
        }
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
        unset($total);
        unset($mxc);
        unset($data);
        break;
      case "bounced":
        $idSub = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idSubaccount; 
        $where = array();
        $where = ["bounced" => ['$gte' => "1"], "idMail" => $this->idMail];
        if (isset($this->stringSearch->name)) {
          if ($this->stringSearch->id != -1) {
            switch ($this->typeFilter) {
              case "type":
                $in = array();
                if ($this->stringSearch->name == "soft") {
                  $in = [20, 21, 22, 23, 29, 30, 40, 50, 51, 52, 53, 54, 59, 60, 70, 100, 110, 120, 121];
                  $in = array_map('strval', $in);
                } else if ($this->stringSearch->name == "hard") {
                  $in = [10, 90, 200];
                  $in = array_map('strval', $in);
                }
                $where["bouncedCode"] = ['$in' => $in];
                 unset($in);
                break;
              case "category":
                $where["bouncedCode"] = (string) $this->stringSearch->id;
                break;
              case "domain":
                $name = $this->stringSearch->name;
                $where["email"] = ['$regex' => ".*$name"];
                unset($name);
                break;
              default :
                throw new Exception("Se ha producido un error al enviar el tipo de filtros");
                break;
            }
          }
        }
        $bounced = \Mxc::find(["limit" => $this->limit, "skip" => $this->page, $where]);
        $open = \Mxc::find([$where]);
        unset($where);
        $array = array();
        foreach ($bounced as $key) {
          $c = new \stdClass();
          $c->date = $key->bounced;
          $c->email = $key->email;
          $sql = "SELECT * FROM bounced_code WHERE idBounced_code = {$key->bouncedCode}";
          $data = $this->db->fetchAll($sql);
          if ($key->bouncedCode == 10 || $key->bouncedCode == 90 || $key->bouncedCode == 200) {
            $c->type = "hard";
          } else {
            $c->type = "soft";
          }
          $c->description = $data[0]['description'];
          array_push($array, $c);
          unset($c);
          unset($data);
          unset($sql);
        }
        unset($bounced);
        $this->statics["info"][] = $array;
        $this->statics["info"][] = array("total" => count($open), "total_pages" => ceil(count($open) / $this->limit));
        unset($array);
        unset($open);
        break;
      case "spam":
        $where = [
            "idMail" => $this->idMail,
            "spam" =>  array('$gte' => (string) '1')
        ];
        $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $this->page]);
        $total = \Mxc::count([$where]);
        unset($where);
        $data = array();
        foreach ($mxc as $value) {
            $obj = new \stdClass();
            $obj->dateOpen = $value->scheduleDate;
            $obj->email = $value->email;
            $obj->name = $value->name;
            $obj->lastname = $value->lastname;
            $obj->indicative = $value->indicative;
            $obj->phone = $value->phone;
            array_push($data, $obj);
            unset($obj);
        }
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
        unset($total);
        unset($data);
        break;
      case "buzon":
        $where = [
            "idMail" => $this->idMail,
            "status" => 'sent',
            "bounced" => 0,
            "spam" => 0
        ];
        $mxc = \Mxc::find([$where, "limit" => $this->limit, "skip" => $this->page]);
        //
        $total = \Mxc::count([$where]);
        unset($where);
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
            unset($obj);
          }
        }
        $this->statics["info"][] = $data;
        $this->statics["info"][] = array("total" => $total, "total_pages" => ceil($total / $this->limit));
        unset($total);
        unset($data);
        break;
    }
  }  

}
