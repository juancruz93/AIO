<?php

namespace Sigmamovil\General\Misc;

require_once(__DIR__ . "/../../library/phpexcel/Classes/PHPExcel.php");

/**
 *
 * @author desarrollo3
 */
class reportExcel {

  public function __construct() {
    $this->objPhpExcel = new \PHPExcel();
  }

  public $objPhpExcel,
          $data,
          $info,
          $type,
  $idMail;
  private $i = 9,
          $letter = "A";

  public function createStatics() {
    $this->basicProperties();
  }

  public function createStaticsReportMail() {
    $this->basicPropertiesReportMail();
  }

  public function createStaticsReportSms() {
    $this->basicPropertiesReportMail();
  }
  
  public function createStaticsReportRecharge() {
    $this->basicPropertiesReportMail();
  }
  
  public function createStaticsReportChangePlan() {
    $this->basicPropertiesReportMail();
  }

  public function createStaticsSms() {
    $this->basicPropertiesSms();
  }

  public function createStaticsSurvey() {
    $this->basicPropertiesSurvey();
  }

  public function SetTittle($tittle) {
    $mail = \Mail::findFirst([
        "conditions" => "idMail = ". $this->idMail,
    ]);
    $target = json_decode($mail->target, true);

    $flag = false;
    if(count($target['contactlists']) == 1){
      $idContactlist = $target['contactlists'][0]['idContactlist'];
      $flag = true;
    }else{
      $idContactlist = "";
      foreach ($target['contactlists'] as $key){
        $idContactlist .= $key['idContactlist']. " ,";
      }
      $idContactlist = substr($idContactlist, 0, -1);
      $flag = true;
    }
$letterTittle = "G";
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B3', $tittle)
            ->setCellValue('A8', "Correos enviados:")
            ->setCellValue('A9', "Aperturas unicas:")
            ->setCellValue('A10', "Clics sobre enlaces:")
            ->setCellValue('A11', "Desuscritos:")
            ->setCellValue('A12', "Rebotes::")
            ->setCellValue('B7', "Total")
            ->setCellValue('C7', "Porcentaje")
            ->setCellValue('A14', "Fecha")
            ->setCellValue('B14', "Email")
            ->setCellValue('C14', "Nombre")
            ->setCellValue('D14', "Apellido")
            ->setCellValue('E14', "Indicativo")
            ->setCellValue('F14', "Móvil");
    if($flag == true){
      if(count($target['contactlists']) == 1) {
        $customfields = \Customfield::find(["conditions" => "idContactlist = " . $idContactlist]);
        if (count($customfields) > 0) {
          foreach ($customfields as $value) {
            $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle . "14", $value->name);
            $letterTittle++;
          }
        }
      }else{
        $sql = "SELECT name FROM customfield WHERE idContactlist IN ({$idContactlist})";
        $customfields = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
        if (count($customfields) > 0) {
          foreach ($customfields as $value) {
            $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle . "14", $value['name']);
            $letterTittle++;
          }
        }
      }
    }
    $this->objPhpExcel->setActiveSheetIndex(0)
        ->setCellValue($letterTittle."14", "Total de aperturas");
    switch ($this->type) {
      case "clic":
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle."14", "Link");
        break;
      case "bounced":
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle."14", "Tipo de rebote")
                ->setCellValue($letterTittle."14", "Categoria");
        break;
    }
  }

  public function setTableInfoSms() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B3', "REPORTE SMS")
            ->setCellValue('A8', "Nombre del envió:")
            ->setCellValue('A9', "Fecha del envió:")
            ->setCellValue('A10', "Categoria:")
            ->setCellValue('A11', "Destinatarios:")
            ->setCellValue('A12', "Enviados:")
            ->setCellValue('A13', "No enviados:")//*//
            ->setCellValue('B8', $this->data["sms"]["name"])
            ->setCellValue('B9', $this->data["sms"]["startdate"])
            ->setCellValue('B10', $this->data["sms"]["namecategory"])
            ->setCellValue('B11', $this->data["sms"]["target"])
            ->setCellValue('B12', $this->data["sent"])
            ->setCellValue('B13', $this->data["undelivered"]);
  }

  public function setInfoSms() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', "INFORME CONSUMO SMS")
            ->setCellValue('A3', "Detalle del consumo de mensajes de texto durante un periodo, por cuenta.")
            ->setCellValue('A4', "Elaborado para:")
            ->setCellValue('B4', "SIGMA MOVIL S.A.S")
            ->setCellValue('A5', "Fecha de generación del informe:")
//            ->setCellValue('A6', "-----")
            ->setCellValue('A7', "Consumo de mensajes de texto por cuenta.");
  }

  public function setInfoDetailMail() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', "INFORME DETALLADO DE ENVIO DE CORREO")
            ->setCellValue('A3', "Detalle de envíos de correos electrónicos durante un periodo, por cuenta.")
            ->setCellValue('A4', "Elaborado para:")
            ->setCellValue('C4', "SIGMA MOVIL S.A.S")
            ->setCellValue('A5', "Fecha de generación del informe:")
//            ->setCellValue('A6', "-----")
            ->setCellValue('A7', "Detalle de correos electrónicos de texto por cuenta.");
  }

  public function setInfoDetailSms() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', "INFORME DETALLADO DE ENVIO DE SMS")
            ->setCellValue('A3', "Detalle de envíos de mensajes de texto durante un periodo, por cuenta.")
            ->setCellValue('A4', "Elaborado para:")
            ->setCellValue('C4', "SIGMA MOVIL S.A.S")
            ->setCellValue('A5', "Fecha de generación del informe:")
//            ->setCellValue('A6', "-----")
            ->setCellValue('A7', "Detalle de mensajes de texto por cuenta.");
  }

  public function setTableInfoReportMail() {
    
  }

  public function generatedReportMail() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A8', "Fecha:")
            ->setCellValue('B8', "Cuenta:")
            ->setCellValue('C8', "Nombre de la cuenta:")
            ->setCellValue('D8', "Subcuenta:")
            ->setCellValue('E8', "Nombre del envio:")
            ->setCellValue('F8', "Enviados:")
            ->setCellValue('G8', "Aperturas:")
            ->setCellValue('H8', "Desuscritos:")
            ->setCellValue('I8', "Rebotes:")
            ->setCellValue('J8', "Spam:");
    $i = 9;
    foreach ($this->data as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $i, $key->scheduleDate)
              ->setCellValue('B' . $i, $key->idAccount)
              ->setCellValue('C' . $i, $key->nameAccount)
              ->setCellValue('D' . $i, $key->nameSubaccount)
              ->setCellValue('E' . $i, $key->name)
              ->setCellValue('F' . $i, $key->ctotal)
              ->setCellValue('G' . $i, $key->copen)
              ->setCellValue('H' . $i, 1)
//              ->setCellValue('H' . $i, $key->copen)
              ->setCellValue('I' . $i, $key->cbounced)
              ->setCellValue('J' . $i, $key->cspam);
      $i++;
    }
  }

  public function generatedReportGeneralSms() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A8', "Fecha:")
            ->setCellValue('B8', "Cuenta:")
            ->setCellValue('C8', "Nombre de la cuenta:")
            ->setCellValue('D8', "Subcuenta:")
            ->setCellValue('E8', "Nombre del envio:")
            ->setCellValue('F8', "Enviados:");
    $i = 9;
    foreach ($this->data as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $i, $key->startdate)
              ->setCellValue('B' . $i, $key->idAccount)
              ->setCellValue('C' . $i, $key->nameAccount)
              ->setCellValue('D' . $i, $key->nameSubaccount)
              ->setCellValue('E' . $i, $key->nameSms)
              ->setCellValue('F' . $i, $key->target);
      $i++;
    }
  }
  
  public function generatedReportGeneralRecharge() {
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A8', "Nombre de la cuenta:")
                ->setCellValue('B8', "Cantidad Recargada:")
                ->setCellValue('C8', "Monto Inicial:")
                ->setCellValue('D8', "Fecha Recarga:")
                ->setCellValue('E8', "Realizado por:");
        $i = 9;
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $key->nameaccount)
                ->setCellValue('B' . $i, $key->recharge)
                ->setCellValue('C' . $i, $key->initialTotals)
                ->setCellValue('D' . $i, $key->createds)
                ->setCellValue('E' . $i, $key->createdBy);
            $i++;
        }
  }
  
  public function generatedReportChangePlan() {
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A8', "Nombre de la cuenta:")
                ->setCellValue('B8', "Plan anterior:")
                ->setCellValue('C8', "Plan Actual:")
                ->setCellValue('D8', "Fecha Cargada:")
                ->setCellValue('E8', "Cambiado por:");
        $i = 9;
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $key['nameAccount'])
                ->setCellValue('B' . $i, $key['namePreviPlan'])
                ->setCellValue('C' . $i, $key['nameCurrentPlan'])
                ->setCellValue('D' . $i, $key['dateChange'])
                ->setCellValue('E' . $i, $key['createdBys']);
            $i++;
        }
  }

  public function generatedReportInfoDetailMail() {
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->setCellValue("A" . $this->i, "ID")
            ->setCellValue("B" . $this->i, "Fecha de envío")
            ->setCellValue("C" . $this->i, "Subcuenta")
            ->setCellValue("D" . $this->i, "Nombre del envío")
            ->setCellValue("E" . $this->i, "Usuario")
            ->setCellValue("F" . $this->i, "Total")
            ->setCellValue("G" . $this->i, "Aperturas")
            ->setCellValue("H" . $this->i, "Desuscritos")
            ->setCellValue("I" . $this->i, "Rebotes")
            ->setCellValue("J" . $this->i, "Spam");
    $objActSheet->getStyle("A" . $this->i . ":J" . $this->i)->getFont()->setBold(true)
            ->setName('Arial');
    $this->i++;
    foreach ($this->data as $key) {
      $objActSheet->setCellValue("A" . $this->i, $key->idMail)
              ->setCellValue("B" . $this->i, $key->scheduleDate)
              ->setCellValue("C" . $this->i, $key->nameSubaccount)
              ->setCellValue("D" . $this->i, $key->nameMail)
              ->setCellValue("E" . $this->i, $key->createdBy)
              ->setCellValue("F" . $this->i, $key->messagesSent)
              ->setCellValue("G" . $this->i, $key->uniqueOpening)
              ->setCellValue("H" . $this->i, $key->unsuscribed)
              ->setCellValue("I" . $this->i, $key->bounced)
              ->setCellValue("J" . $this->i, $key->spam);
      $this->i++;
    }
  }

  public function generatedReportInfoDetailSms() {
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->setCellValue("A" . $this->i, "ID")
            ->setCellValue("B" . $this->i, "Fecha de envío")
            ->setCellValue("C" . $this->i, "Subcuenta")
            ->setCellValue("D" . $this->i, "Usuario")
            ->setCellValue("E" . $this->i, "Nombre del envío")
            ->setCellValue("F" . $this->i, "Enviados")
            ->setCellValue("G" . $this->i, "No enviados")
            ->setCellValue("H" . $this->i, "Total");
    $objActSheet->getStyle("A" . $this->i . ":H" . $this->i)->getFont()->setBold(true)
            ->setName('Arial');
    $this->i++;
    foreach ($this->data as $key) {
      $objActSheet->setCellValue("A" . $this->i, $key['idSms'])
              ->setCellValue("B" . $this->i, $key['startdate'])
              ->setCellValue("C" . $this->i, $key['namesubaccount'])
              ->setCellValue("D" . $this->i, $key['createdBy'])
              ->setCellValue("E" . $this->i, $key['namesms'])
              ->setCellValue("F" . $this->i, $key['sent'])
              ->setCellValue("G" . $this->i, $key['undelivered'])
              ->setCellValue("H" . $this->i, $key['total']);
      $this->i++;
    }
  }

  public function generatedReportInfoSms() {
    $i = 9;
    $totalSms = 0;
    $totalColumn = [];
    foreach ($this->data as $key) {
      $letter = "A";
      $totalRow = 0;
      foreach ($key as $value) {
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letter . $i, $value)
                ->getRowDimension($i)->setRowHeight(20);
        $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
        if ($i == 9 && $letter != "A") {
          $objActSheet->getStyle($letter . $i)
                  ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        if ($letter == "A") {
          if ($i == 9) {
            $objActSheet->getStyle($letter . $i)
                    ->getFont()->setBold(true)
                    ->setName('Arial')
                    ->setSize(15);
          } else {
            $objActSheet->getStyle($letter . $i)->getFont()
                    ->setName('Arial')
                    ->setSize(10);
          }
        }
        if ($letter != "A") {
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->getColumnDimension($letter)->setWidth(12);
          $objActSheet->getStyle($letter . $i)->getFont()
                  ->setName('Arial')
                  ->setSize(10);
          if ($i != 9) {
            $totalRow += $value;
            if (isset($totalColumn[$letter])) {
              $totalColumn[$letter] += $value;
            } else {
              $totalColumn[$letter] = $value;
            }
          }
        }
        $letter++;
      }
      if ($i != 9) {
        $totalSms += $totalRow;
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letter . $i, $totalRow);
      } else if ($i == 9) {
        $this->objPhpExcel->setActiveSheetIndex(0)->getStyle($letter . $i)->getFont()->setBold(true)
                ->setName('Arial')
                ->setSize(12);
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->getColumnDimension($letter)->setWidth(20);
        $this->objPhpExcel->setActiveSheetIndex(0)->getStyle($letter . $i)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letter . $i, "Total SMS por cuenta");
      }
      $i++;
    }
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $i, "Total SMS consumidos por periodo");
//    $this->objPhpExcel->setActiveSheetIndex(0)
//            ->getStyle("A" . $i)
//            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->getStyle("A" . $i)
            ->getFont()->setBold(true)
            ->setName('Arial')
            ->setSize(10);

    $total = 0;
    foreach ($totalColumn as $key => $value) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue($key . $i, $value);
      $total += $value;
    }
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue($letter . $i, $total);
  }

  public function generatedReportSms($idSms) {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A15', "Codigo del país	")
            ->setCellValue('B15', "Móvil")
            ->setCellValue('C15', "Mensaje")
            ->setCellValue('D15', "Estado");
    $i = 16;
    $sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $idSms]]);
//    echo 123;
//    exit;
    if ($sms->type == "contact") {
      foreach ($this->data['detail'] as $key) {
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, "+" . $key->indicative)
                ->setCellValue('B' . $i, $key->phone)
                ->setCellValue('C' . $i, $key->message)
                ->setCellValue('D' . $i, $this->traslateStatusSmsByContact($key->response));
        $i++;
      }
    } else {
      foreach ($this->data['detail'] as $key) {
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, "+" . $key["code"])
                ->setCellValue('B' . $i, $key["phone"])
                ->setCellValue('C' . $i, $key["message"])
                ->setCellValue('D' . $i, $this->traslateStatusSms($key["status"]));
        $i++;
      }
    }
  }

  public function traslateStatusSms($status) {
    $statusSpanish = "";
    switch ($status) {
      case "sent":
        $statusSpanish = "Enviado";
        break;
      case "undelivered":
        $statusSpanish = "No enviado";
        break;
    }
    return $statusSpanish;
  }

  public function traslateStatusSmsByContact($status) {
    $statusSpanish = "No enviado";
    if ($status == "0: Accepted for delivery") {
      $statusSpanish = "Enviado";
    }
    return $statusSpanish;
  }

  public function download() {
    $objWriter = new \PHPExcel_Writer_Excel2007($this->objPhpExcel, 'Excel5');
    $objWriter->save('../tmp/reporte.xlsx');
  }

  public function basicProperties() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $gdImage = imagecreatefromjpeg('images/logo.jpg');
    $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);
    $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(85);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($this->objPhpExcel->getActiveSheet());
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(30);
    $objActSheet->getColumnDimension("B")->setWidth(30);
    $objActSheet->getColumnDimension("C")->setWidth(25);
    $objActSheet->getColumnDimension("D")->setWidth(25);
    $objActSheet->getColumnDimension("E")->setWidth(10);
    $objActSheet->getColumnDimension("F")->setWidth(15);
    $objActSheet->getColumnDimension("G")->setWidth(30);
  }

  public function basicPropertiesReportMail() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $gdImage = imagecreatefromjpeg('images/logo.jpg');
    $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);
    $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(85);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($this->objPhpExcel->getActiveSheet());
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(25);
    $objActSheet->getColumnDimension("B")->setWidth(25);
    $objActSheet->getColumnDimension("C")->setWidth(25);
    $objActSheet->getColumnDimension("D")->setWidth(25);
    $objActSheet->getColumnDimension("E")->setWidth(25);
  }

  public function basicPropertiesInfoSms() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(30);
    $objActSheet->getColumnDimension("B")->setWidth(15);
    $objActSheet->getColumnDimension("C")->setWidth(15);
    $objActSheet->getColumnDimension("D")->setWidth(15);
    $objActSheet->getRowDimension(2)->setRowHeight(35);
    $objActSheet->getRowDimension(3)->setRowHeight(20);
    $objActSheet->getRowDimension(4)->setRowHeight(20);
    $objActSheet->getRowDimension(5)->setRowHeight(20);
    $objActSheet->getRowDimension(7)->setRowHeight(20);
    $objActSheet->mergeCells("A3:D3");
    $objActSheet->mergeCells("A2:B2");
    $objActSheet->mergeCells("B4:C4");
    $objActSheet->mergeCells("A7:B7");
    $objActSheet->getStyle("A2")->getFont()
            ->setName('Arial')
            ->setSize(20);
    $objActSheet->getStyle("A7")->getFont()->setBold(true)
            ->setName('Arial')
            ->setSize(13);
    $objActSheet->getStyle("A4:A5:A7:B4")->getFont()->setBold(true)
            ->setName('Arial');
  }

  public function basicPropertiesInfoDetailSms() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(10);
    $objActSheet->getColumnDimension("B")->setWidth(25);
    $objActSheet->getColumnDimension("C")->setWidth(20);
    $objActSheet->getColumnDimension("D")->setWidth(30);
    $objActSheet->getColumnDimension("E")->setWidth(25);
    $objActSheet->getColumnDimension("F")->setWidth(12);
    $objActSheet->getColumnDimension("G")->setWidth(10);
    $objActSheet->getColumnDimension("H")->setWidth(10);
    $objActSheet->getRowDimension(2)->setRowHeight(35);
    $objActSheet->getRowDimension(3)->setRowHeight(20);
    $objActSheet->getRowDimension(4)->setRowHeight(20);
    $objActSheet->getRowDimension(5)->setRowHeight(20);
    $objActSheet->getRowDimension(7)->setRowHeight(20);
    $objActSheet->mergeCells("A3:D3");
    $objActSheet->mergeCells("A2:D2");
    $objActSheet->mergeCells("A4:B4");
    $objActSheet->mergeCells("C4:D4");
    $objActSheet->mergeCells("A7:C7");
    $objActSheet->getStyle("A2")->getFont()
            ->setName('Arial')
            ->setSize(20);
    $objActSheet->getStyle("A7")->getFont()->setBold(true)
            ->setName('Arial')
            ->setSize(13);
    $objActSheet->getStyle("A4:A5:A7:B4")->getFont()->setBold(true)
            ->setName('Arial');
  }

  public function basicPropertiesInfoDetailMail() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(10);
    $objActSheet->getColumnDimension("B")->setWidth(25);
    $objActSheet->getColumnDimension("C")->setWidth(25);
    $objActSheet->getColumnDimension("D")->setWidth(25);
    $objActSheet->getColumnDimension("E")->setWidth(30);
    $objActSheet->getColumnDimension("F")->setWidth(10);
    $objActSheet->getColumnDimension("G")->setWidth(10);
    $objActSheet->getColumnDimension("H")->setWidth(12);
    $objActSheet->getColumnDimension("I")->setWidth(10);
    $objActSheet->getColumnDimension("J")->setWidth(10);
    $objActSheet->getRowDimension(2)->setRowHeight(35);
    $objActSheet->getRowDimension(3)->setRowHeight(20);
    $objActSheet->getRowDimension(4)->setRowHeight(20);
    $objActSheet->getRowDimension(5)->setRowHeight(20);
    $objActSheet->getRowDimension(7)->setRowHeight(20);
    $objActSheet->mergeCells("A3:D3");
    $objActSheet->mergeCells("A2:D2");
    $objActSheet->mergeCells("A4:B4");
    $objActSheet->mergeCells("C4:D4");
    $objActSheet->mergeCells("A7:C7");
    $objActSheet->getStyle("A2")->getFont()
            ->setName('Arial')
            ->setSize(20);
    $objActSheet->getStyle("A7")->getFont()->setBold(true)
            ->setName('Arial')
            ->setSize(13);
    $objActSheet->getStyle("A4:A5:A7:B4")->getFont()->setBold(true)
            ->setName('Arial');
  }

  public function basicPropertiesSms() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $gdImage = imagecreatefromjpeg('images/logo.jpg');
    $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);
    $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(85);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($this->objPhpExcel->getActiveSheet());
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(30);
    $objActSheet->getColumnDimension("B")->setWidth(20);
    $objActSheet->getColumnDimension("C")->setWidth(45);
    $objActSheet->getColumnDimension("D")->setWidth(15);
  }

  public function setContentMail() {
        $mail = \Mail::findFirst([
            "conditions" => "idMail = ". $this->idMail,
        ]);
        $target = json_decode($mail->target, true);

        $flag = false;
        if(count($target['contactlists']) == 1){
          $idContactlist = $target['contactlists'][0]['idContactlist'];
          $flag = true;
        }else{
          $flag = true;
        }

    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B8', $this->info['messageSent'])
            ->setCellValue('B9', $this->info['open'])
            ->setCellValue('B10', $this->info['uniqueClicks'])
            ->setCellValue('B11', $this->info['unsubscribed'])
            ->setCellValue('B12', $this->info['bounced'])
            ->setCellValue('C9', $this->calculatePercentage($this->info['messageSent'], $this->info['open']) . "%")
            ->setCellValue('C11', $this->calculatePercentage($this->info['messageSent'], $this->info['unsubscribed']) . "%")
            ->setCellValue('C12', $this->calculatePercentage($this->info['messageSent'], $this->info['bounced']) . "%");
    $i = 15;
    switch ($this->type) {
      case "clic":
        foreach ($this->data as $key) {
          $letter = "G";
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, date("d/M/Y g:i A", $key->date))
                  ->setCellValue('B' . $i, $key->email)
                  ->setCellValue('C' . $i, $key->name)
                  ->setCellValue('D' . $i, $key->lastname)
                  ->setCellValue('E' . $i, $key->indicative)
                  ->setCellValue('F' . $i, $key->phone);

          if($flag == true){
            $cxc = \Cxc::findFirst([["idContact" => (int) $key["idTmpTable"]]]);
            if(count($cxc->idContactlist[$idContactlist]) > 0){
              foreach ($cxc->idContactlist[$idContactlist] as $value){
                $this->objPhpExcel->setActiveSheetIndex(0)
                    ->setCellValue($letter . $i, $value['value']);
                $letter++;
              }
            }
          }

          $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue($letter . $i, $key->link);
          $i++;
        }
        break;
      case "unsuscribe":
      case "open":
      case "spam":
        foreach ($this->data as $key) {
          $letter = "G";
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, date("d/M/Y g:i A", $key['dateOpen']))
                  ->setCellValue('B' . $i, $key['email'])
                  ->setCellValue('C' . $i, $key['name'])
                  ->setCellValue('D' . $i, $key['lastName'])
                  ->setCellValue('E' . $i, $key['indicative'])
                  ->setCellValue('F' . $i, $key['phone']);
          if($flag == true){
          $cxc = \Cxc::findFirst([["idContact" => (int) $key["idTmpTable"]]]);
              if(count($target['contactlists']) > 1){
                  foreach ($target['contactlists'] as $key){
                    foreach ($cxc->idContactlist[$key['idContactlist']] as $value){
                      $this->objPhpExcel->setActiveSheetIndex(0)
                          ->setCellValue($letter . $i, $value['value']);
                      $letter++;
                    }
                  }
              }else{
                foreach ($cxc->idContactlist[$idContactlist] as $value){
                  $this->objPhpExcel->setActiveSheetIndex(0)
                      ->setCellValue($letter . $i, $value['value']);
                  $letter++;
                }
              }
          }
          $this->objPhpExcel->setActiveSheetIndex(0)
             ->setCellValue($letter . $i, $key['totalOpening']);
          $i++;
        }
        break;
      case "bounced":
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, date("d/M/Y g:i A", $key->date))
                  ->setCellValue('B' . $i, $key->email)
                  ->setCellValue('C' . $i, $key->name)
                  ->setCellValue('D' . $i, $key->lastname)
                  ->setCellValue('E' . $i, $key->indicative)
                  ->setCellValue('F' . $i, $key->phone)
                  ->setCellValue('G' . $i, $key->type)
                  ->setCellValue('H' . $i, $key->description);
          $i++;
        }
        break;
    }
  }

  public function basicPropertiesSurvey() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
        ->setTitle("Reporte")
        ->setSubject("Asunto")
        ->setDescription("Reporte")
        ->setKeywords("reporte")
        ->setCategory("Reporte excel");
    $gdImage = imagecreatefromjpeg('images/logo.jpg');
    $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);
    $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(85);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($this->objPhpExcel->getActiveSheet());
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $objActSheet->getColumnDimension("A")->setWidth(32);
    $objActSheet->getColumnDimension("B")->setWidth(30);
    $objActSheet->getColumnDimension("C")->setWidth(30);
    $objActSheet->getColumnDimension("D")->setWidth(30);
    $objActSheet->getColumnDimension("E")->setWidth(30);
    $objActSheet->getColumnDimension("F")->setWidth(30);
    $objActSheet->getColumnDimension("G")->setWidth(30);
  }

  public function generatedReportSurvey() {
    $this->objPhpExcel->setActiveSheetIndex(0)
        ->setCellValue('B3', "REPORTE DE ENCUESTA");

    $n = 9;
    $l = "D";
    $number = 8;
    foreach ($this->data['questions'] as $value) {
      $this->objPhpExcel->setActiveSheetIndex(0)
        ->setCellValue("A" . $number, "Nombre")
        ->setCellValue("B" . $number, "Apellido")
        ->setCellValue("C" . $number, "Correo")
        ->setCellValue("D" . $number, $value['question'])
        ->getStyle("A" . $number . ":D" . $number)->getFont()->setBold(true)
        ->setName('Arial');

      foreach ($value['answer'] as $item){
        if ($this->data['survey']['type'] == "contact"){
          $idContacts = array_keys($item['contacts']);
          $contacts = \Contact::find([
              ["idContact" => ['$in' => $idContacts]]
          ]);
          foreach ($contacts as $contact){
            $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $n, $contact->name)
                ->setCellValue("B" . $n, $contact->lastname)
                ->setCellValue("C" . $n, $contact->email)
                ->setCellValue("D" . $n, $item['answer']);
            $n++;
          }
        } else {
          for ($i = 0; $i< $item['count']; $i++){
            $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $n, "")
                ->setCellValue("B" . $n, "")
                ->setCellValue("C" . $n, "")
                ->setCellValue("D" . $n, $item['answer']);
            $n++;
          }
        }
      }
      $n = $n + 3;
      $number = $n - 1;
    }
    /*foreach ($this->data as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
          ->setCellValue('A' . $i, $key->scheduleDate)

      $i++;
    }*/
  }

  public function calculatePercentage($total, $value) {
    $p = ($value / $total) * 100;
    if ($p % 1 == 0) {
      return $p;
    } else {
      return round($p, 2);
    }
  }

  public function setData($data) {
    $this->data = $data;
    return $this;
  }

  public function setInfo($info) {
    $this->info = $info;
    return $this;
  }

  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  public function setIdMail($idMail)
  {
    $this->idMail = $idMail;
  }

}
