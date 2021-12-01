<?php

include_once( "../app/library/phpexcel/Classes/PHPExcel.php");
include_once( "../app/library/phpexcel/Classes/PHPExcel/Writer/Excel2007.php");

class DownloadsmsController extends ControllerBase {
  
  public function downloadAction($sms){
    try {
      $objPHPExcel = $this->phpExcelSmsxEmail();
      $this->phpExcelWorksheet($objPHPExcel);
      $this->findData($objPHPExcel,$sms);
      $this->findSmsxEmail($objPHPExcel,$sms);
      return $this->downloadSmsxemail($sms,$objPHPExcel);
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
    $objPHPExcel->getActiveSheet()->getStyle("B18:F18")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
    //Colocamos el titulo de Reporte SMS
    $objPHPExcel->getActiveSheet()->setCellValue("C3", "REPORTE SMS");
    $objPHPExcel->getActiveSheet()->setCellValue("B18", "Codigo del país");
    $objPHPExcel->getActiveSheet()->setCellValue("C18", "Móvil");
    $objPHPExcel->getActiveSheet()->setCellValue("D18", "Mensaje");
    $objPHPExcel->getActiveSheet()->setCellValue("E18", "Estado");
    $objPHPExcel->getActiveSheet()->setCellValue("F18", "Cantidad para cobro");
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
  
  public function findData($objPHPExcel,$sms) {
    
    $smsSender = \Sms::findFirst(array("conditions" => "idSms = ?0","bind" => array($sms)));
    $countTotalSms = 0;
    //Para contar la cantidad de sms lote o contact y no el target de sms ya que el target no esta funcionando correctamente
    if($smsSender->type=='contact'){
      $findSmsxc = array(
          'conditions'=>array(
              'idSms'=> $sms
          )
      );
      $respuestSmsxc = \Smsxc::Count($findSmsxc);
      $countTotalSms = $respuestSmsxc;
    }
//    else if($smsSender->type=='lote'||$smsSender->type=='csv'){Se comenta debido a que sie s api se envian por lote
    else{
      $sqlLote = "SELECT
                      count(smslote.idSmslote) as target
                  FROM
                      smslote
                  WHERE
                      smslote.idSms = {$sms};";
      $totalSmsLote = $this->db->fetchAll($sqlLote);
      $countTotalSms = $totalSmsLote[0]['target'];
    }
    
    $sql = "SELECT indicative, phone, message, count(*) as count FROM smslote WHERE idSms = {$sms} GROUP BY phone HAVING count(*) > 1";
    $sendTotal = $this->db->fetchAll($sql);
    $number = 0;
    for($i=0; $i<count($sendTotal); $i++){
      $number += $sendTotal[$i]['count'];
    }
//    $statusSuccess = ["0: Accepted for delivery", "PENDING_ENROUTE"];
    $statusSuccess = ["sent"];

    if($smsSender->type == 'contact'){
//      $sent = \Smsxc::count(array("conditions" => array("idSms" => (string) $smsSender->idSms,"response" => array('$in' => $statusSuccess))));
//      $undelivered = \Smsxc::count(array("conditions" => array("idSms" => (string) $smsSender->idSms,"response" => array('$nin' => $statusSuccess))));
      $sent = \Smsxc::count(array("conditions" => array("idSms" => (string) $smsSender->idSms,"status" => array('$in' => $statusSuccess))));
      $undelivered = \Smsxc::count(array("conditions" => array("idSms" => (string) $smsSender->idSms,"status" => array('$nin' => $statusSuccess))));
    /*else if($smsSender->type == 'csv'){//Se comenta debido a que 
      $consulta1 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status = 'sent'";
      $sent = $this->db->fetchAll($consulta1);
      $sent = count($sent);
      $consulta2 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status != 'sent'";
      $undelivered = $this->db->fetchAll($consulta2);
      $undelivered = count($undelivered);
    }*/
    } else{
      //Validación cuando sea un envio de sms x email
      $report = \ReportSmsxemail::findFirst(array("conditions" => "idSms = ?0","bind" => array($sms)));
      if($report){
        $consulta1 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status = 'sent'";
        $sent = $this->db->fetchAll($consulta1);
        $sent = count($sent);
        $consulta2 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status = 'undelivered'";
        $count = $this->db->fetchAll($consulta2);
        $count = count($count);
        $encode = json_decode($report->smsFailed);
        $undelivered = count(get_object_vars($encode)) + $count;
      } else {
        $consulta1 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status = 'sent'";
        $sent = $this->db->fetchAll($consulta1);
        $sent = count($sent);
        $consulta2 = "SELECT * FROM smslote WHERE idSms = {$sms} AND status != 'sent'";
        $undelivered = $this->db->fetchAll($consulta2);
        $undelivered = count($undelivered);
      }
    }
    $sqlcountfailed = "SELECT count(*) as total FROM sms_failed WHERE idSms = {$sms}";
    $countfailed = $this->db->fetchAll($sqlcountfailed);
        //
    $objPHPExcel->getActiveSheet()->setCellValue("B7", $smsSender->name);
    $objPHPExcel->getActiveSheet()->setCellValue("B8", $smsSender->startdate);
//    $objPHPExcel->getActiveSheet()->setCellValue("B9", (($smsSender->target == 1) ? "Uno a uno" : "Uno a muchos"));
    $objPHPExcel->getActiveSheet()->setCellValue("B9", (($countTotalSms == 1) ? "Uno a uno" : "Uno a muchos"));
    $objPHPExcel->getActiveSheet()->setCellValue("B10", $smsSender->email);
//    $objPHPExcel->getActiveSheet()->setCellValue("B11", $smsSender->target);
    $objPHPExcel->getActiveSheet()->setCellValue("B11", $countTotalSms);
    
    /*if($smsSender->type == 'csv'){
      $objPHPExcel->getActiveSheet()->setCellValue("B12", $number);
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B14", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", (int) $undelivered);
    } else if($smsSender->type == 'contact' || $smsSender->type == 'encrypted'){
      $objPHPExcel->getActiveSheet()->setCellValue("B12", ($countfailed[0]["total"] ? $countfailed[0]["total"] : 0));
//      $objPHPExcel->getActiveSheet()->setCellValue("B13", ($sent ? $sent : 1)); Se comenta debido que se desconoce la utilidad de la validación
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B14", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", (int) $undelivered);
    } else if($smsSender->type == 'lote'){
      $objPHPExcel->getActiveSheet()->setCellValue("B12", $number);
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B14", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", (int) $undelivered);
    }*/
    
    if($smsSender->type == 'contact'){
      $objPHPExcel->getActiveSheet()->setCellValue("B12", ($countfailed[0]["total"] ? $countfailed[0]["total"] : 0));
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B14", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", (int) $undelivered);
    }
    else{
      $objPHPExcel->getActiveSheet()->setCellValue("B12", $number);
      $objPHPExcel->getActiveSheet()->setCellValue("B13", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B14", (int) $sent);
      $objPHPExcel->getActiveSheet()->setCellValue("B15", (int) $undelivered);
    }
    
    // 
    return $objPHPExcel;
  }
  
  public function findSmsxEmail($objPHPExcel,$idSms) {
    for ($i = 65; $i < 71; $i++) {
      $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
    }
    $smslote = \Smslote::find(array("conditions" => "idSms = ?0 ", "bind" => array($idSms)));
    $rowContacs = 19;
    $rowCsv = 19;
    $rowLote = 19;
    $rowSmsxEmail = 0;
    
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $idSms]]);
    if ($sms->type == "contact" || $sms->type == "automatic") {
      $detail = \Smsxc::find([["idSms" => (string) $idSms]]);
      $smsxc = $this->modelDataSmsContactlist($detail);
      foreach ($smsxc as $reciver){
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowContacs, $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowContacs, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowContacs, $reciver->message);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowContacs, $this->traslateStatusSms($reciver->status));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowContacs, $reciver->messageCount)->getStyle('F' . $rowContacs);
        $rowContacs++;
        unset($smsxc);
      }
    } else if ($sms->type == "csv") {
      foreach ($smslote as $reciver) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowCsv, "+" . $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowCsv, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowCsv, $reciver->message);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowCsv, $this->traslateStatusSms(($reciver->status=='sent')?$reciver->status:'undelivered'));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowCsv, $reciver->messageCount)->getStyle('F' . $rowCsv);
        //$objPHPExcel->getActiveSheet()->setCellValue('E' . $rowCsv, $this->traslateStatusSms((($reciver->response == "0: Accepted for delivery" || $reciver->response == "PENDING_ENROUTE") ? "sent" : "undelivered")));      
        $rowCsv++;
        $rowSmsxEmail++;
        unset($smslote);
      }
      $this->setMessageFailed($objPHPExcel,$rowCsv,$idSms);
//    } else if ($sms->type == "lote") {
    } else if ($sms->type == "lote"  || $sms->type == "encrypted" || ($smsSender->type == '' || $smsSender->type == null)) {
      foreach ($smslote as $reciver) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowLote, "+" . $reciver->indicative);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowLote, $reciver->phone);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowLote, $reciver->message);
        //$objPHPExcel->getActiveSheet()->setCellValue('E' . $rowLote, $this->traslateStatusSms((($reciver->response == "0: Accepted for delivery" || $reciver->response == "PENDING_ENROUTE") ? "sent" : "undelivered")));     
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowLote, $this->traslateStatusSms(($reciver->status=='sent')?$reciver->status:'undelivered'));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowLote, $reciver->messageCount)->getStyle('F' . $rowLote);
        $rowLote++;
        unset($smslote);
      }
      $this->setMessageFailed($objPHPExcel,$rowLote,$idSms);
      $report = \ReportSmsxemail::findFirst(array("conditions" => "idSms = {$idSms}"));
      $reporSms = json_decode($report->smsFailed);
      if($reporSms){
        $objPHPExcel->getActiveSheet()->getStyle("G18")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue("G18", "Validación de error");
        $rowSmsxEmail = $rowLote;
        foreach ($reporSms as $reciver){
          $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowSmsxEmail, "+" . $reciver->indicative);
          $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowSmsxEmail, $reciver->phone);
          $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowSmsxEmail, $reciver->message);
          $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowSmsxEmail, $this->traslateStatusSms($reciver->status));
          $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowSmsxEmail, $reciver->smsFailed);
          $rowSmsxEmail++;
          unset($reporSms);
        }
      } 
    }
    //Retornamos el objecto PhpExcel
    return $objPHPExcel;
  }
  
  public function downloadSmsxemail($sms, $objPHPExcel) {
    $modelSms = \Sms::findFirst(array("conditions" => "idSms = ?0","bind" => array($sms)));
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
  
  public function modelDataSmsContactlist($detail) {
    $arr = [];
    foreach ($detail as $value) {
      $obj = new \stdClass();
      $obj->indicative = $value->indicative;
      $obj->phone = $value->phone;
      $obj->message = $value->message;
      $obj->status = (($value->response == "0: Accepted for delivery" || $value->response == "PENDING_ENROUTE") ? "sent" : "undelivered");
      $obj->messageCount = $value->messageCount;
      array_push($arr, $obj);
    }
    return $arr;
  }
  public function setMessageFailed($objPHPExcel,$continue,$idSms) {
    $i = $continue + 2;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $i)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, "Registros repetidos");
    $i++;
    $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C' . $i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D' . $i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $i)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B' . $i, "Codigo del país")
            ->setCellValue('C' . $i, "Móvil")
            ->setCellValue('D' . $i, "Mensaje")
            ->setCellValue('E' . $i, "Cant. Veces");
    $i++;
    $sql = "SELECT indicative, phone, message, count(*) as count FROM smslote WHERE idSms = {$idSms} GROUP BY phone HAVING count(*) > 1";
    $sendTotal = $this->db->fetchAll($sql);
    $this->setRepeated($objPHPExcel,$i,$sendTotal);
    return $objPHPExcel;
  }
  
  public function setRepeated($objPHPExcel,$i,$sendTotal){
    foreach ($sendTotal as $key) {
      $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('B' . $i, "+" . $key['indicative'])
              ->setCellValue('C' . $i, $key['phone'])
              ->setCellValue('D' . $i, $key['message'])
              ->setCellValue('E' . $i, $key['count']);
      $i++;
    }
    return $objPHPExcel;
  }
}
