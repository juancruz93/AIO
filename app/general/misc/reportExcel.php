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
          $idMail,
          $titleOpening,
          $arrTwoWayResponse = array(),
          $infoSurvey;
  private $i = 9,
          $letter = "A";

  public function createStatics() {
    $this->basicProperties();
  }

  public function createStaticsReportMail() {
    $this->basicPropertiesReportMail();
  }

  public function createStatisalliedReport() {
    $this->basicPropertiesReportStatisallied();
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

  public function setTitleOpening($title) {
    $this->titleOpening = $title;
  }

  public function SetTittle($tittle) {
    $mail = \Mail::findFirst([
                "conditions" => "idMail = " . $this->idMail,
    ]);
    $target = json_decode($mail->target);

    $flag = false;
    $arrayIdsContactlist = array();
    if ($target->type == "contactlist") {
      $arrayIdsContactlist = $target->contactlists;
    } else if ($target->type == "segment") {
      //$dataIdSegment = "";
      foreach ($target->segment as $dataSegment) {
        //$dataIdSegment .= $dataSegment['idSegment'] . " ,";
        $dataIdSegment[] = $dataSegment->idSegment;
      }
      //$dataIdSegment = substr($val, 0, -1);
      $contactListSegments = \Segment::find([["idSegment" => ['$in' => $dataIdSegment]]]);
      foreach ($contactListSegments as $clsegment) {
        foreach ($clsegment->contactlist as $dataIdCl) {
          $arrayIdsContactlist[] = array(
              "idContactlist" => $dataIdCl['idContactlist']
          );
        }
      }
    }
    $idContactlist = "";
    foreach ($arrayIdsContactlist as $key) {
      $idContactlist .= $key->idContactlist . " ,";
    }
    $idContactlist = substr($idContactlist, 0, -1);
    $letterTittle = "G";
    $this->objPhpExcel->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("A14:F14")->getFont()->setBold(true);
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('C3', $tittle)
            ->setCellValue('A6', "Correos enviados:")
            ->setCellValue('A7', "Aperturas unicas:")
            ->setCellValue('A8', "Clics sobre enlaces:")
            ->setCellValue('A9', "Desuscritos:")
            ->setCellValue('A10', "Rebotes:")
            ->setCellValue('A11', "Spam:")
            ->setCellValue('A12', "Correos en Buzón:")
            ->setCellValue('B7', "Total")
            ->setCellValue('C7', "Porcentaje")
            ->setCellValue('A14', "Fecha")
            ->setCellValue('B14', "Email")
            ->setCellValue('C14', "Nombre")
            ->setCellValue('D14', "Apellido")
            ->setCellValue('E14', "Indicativo")
            ->setCellValue('F14', "Móvil");
    if ($flag == true) {
      // var_dump(count($target['contactlists']));
      if (count($target['contactlists']) == 1) {
        $customfields = \Customfield::find(["conditions" => "idContactlist = " . $idContactlist]);
        if (count($customfields) > 0) {
          foreach ($customfields as $value) {
            $this->objPhpExcel->setActiveSheetIndex(0)
                    ->setCellValue($letterTittle . "14", $value->name);
            $letterTittle++;
          }
        }
      } else {
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
    $this->objPhpExcel->getActiveSheet()->getStyle($letterTittle . "14")->getFont()->setBold(true);
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue($letterTittle . "14", "Total de aperturas");
    $this->setTitleOpening($letterTittle);
    switch ($this->type) {
      case "clic":
        $this->objPhpExcel->getActiveSheet()->getStyle($letterTittle . "14")->getFont()->setBold(true);
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle . "14", "Link");
        break;
      case "bounced":
        $this->objPhpExcel->getActiveSheet()->getStyle($letterTittle . "14")->getFont()->setBold(true);
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle . "14", "Tipo de rebote")
                ->setCellValue($letterTittle . "14", "Categoria");
        break;
      case "buzon":
        $this->objPhpExcel->getActiveSheet()->getStyle($letterTittle . "14")->getFont()->setBold(true);
        $this->objPhpExcel->setActiveSheetIndex(0)
                ->setCellValue($letterTittle . "14", "Total en buzón");
    }
  }

  public function setTableInfoSms() {

    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B3', "REPORTE SMS")
            ->setCellValue('A8', "Nombre del envió:")
            ->setCellValue('A9', "Fecha del envió:")
            ->setCellValue('A10', "Categoria:")
            ->setCellValue('A11', "Cantidad de registros:")
            ->setCellValue('A12', "Registros repetidos:")
            ->setCellValue('A13', "Destinatarios:")
            ->setCellValue('A14', "Enviados:")
            ->setCellValue('A15', "No enviados:")//*//
            ->setCellValue('B8', $this->data["sms"]["name"])
            ->setCellValue('B9', $this->data["sms"]["startdate"])
            ->setCellValue('B10', $this->data["sms"]["namecategory"])
            ->setCellValue('B11', count($this->data["detail"]) + (Int) $this->data["countfailed"][0]["total"])
            ->setCellValue('B12', (Int) $this->data["countfailed"][0]["total"])
            ->setCellValue('B13', $this->data["sms"]["target"])
            ->setCellValue('B14', $this->data["sent"])
            ->setCellValue('B15', $this->data["undelivered"]);
  }

  public function setTableInfoAccounts($typeRegister, $status) {
    if(is_null($typeRegister) ){
      $typeRegister = "N/A";
    }
    if($typeRegister == "todosOrg"){
      $typeRegister = "Todos los origenes";
    }
    if($typeRegister == "aio"){
      $typeRegister = "AIO";
    }
    if($typeRegister == "facebook"){
      $typeRegister = "Facebook";
    }
    if($typeRegister == "form"){
      $typeRegister = "Formulario Gratuito";
    }
    if($typeRegister == "google"){
      $typeRegister = "Google";
    }
    if($typeRegister == "online"){
      $typeRegister = "Tienda";
    }
    if(is_null($status) ){
      $status = "N/A";
    }
    if($status == "todosEst"){
      $status = "Todos los estados";
    }
    if($status == "activo"){
      $status = "Activo";
    }
    if($status == "inactivo"){
      $status = "Inactivo";
    }
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B3',  "REPORTE LISTADO DE CUENTAS")
            ->setCellValue('A8',  "Listado de cuentas")
            ->setCellValue('A9',  "Estado")
            ->setCellValue('A10', "Tipo de registro")
            ->setCellValue('B9',  $status)
            ->setCellValue('B10', $typeRegister);
    $this->objPhpExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("A8")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("A9")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("A10")->getFont()->setBold(true);
  }

  public function generatedReportAccounts() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A12', "Nit")
            ->setCellValue('B12', "Nombre Empresa")
            ->setCellValue('C12', "Nombre")
            ->setCellValue('D12', "Correo")
            ->setCellValue('E12', "Celular")
            ->setCellValue('F12', "Servicio")
            ->setCellValue('G12', "Modalidad")
            ->setCellValue('H12', "Saldo")
            ->setCellValue('I12', "Disponible")
            ->setCellValue('J12', "Envios Realizados")
            ->setCellValue('K12', "Fecha Creación");
    //ENVIO LAS CELDAS CUYO TEXTO ESTARA EN NEGRITA
    $this->objPhpExcel->getActiveSheet()->getStyle("A12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("B12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("C12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("D12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("E12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("F12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("G12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("H12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("I12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("J12")->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle("K12")->getFont()->setBold(true);
    //ENVIO ANCHO A LAS COLUMNAS NECESARIAS
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("A")->setWidth(32);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("B")->setWidth(28);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("C")->setWidth(28);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("D")->setWidth(26);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("E")->setWidth(16);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("F")->setWidth(15);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("G")->setWidth(15);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("H")->setWidth(10);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("I")->setWidth(13);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("J")->setWidth(17);
    $this->objPhpExcel->setActiveSheetIndex()->getColumnDimension("K")->setWidth(22);

    $i = 13;
    foreach($this->data as $value){
      $this->objPhpExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $value["nit"])
        ->setCellValue('B'.$i, $value["companyName"])
        ->setCellValue('C'.$i, $value["name"])
        ->setCellValue('D'.$i, $value["correo"])
        ->setCellValue('E'.$i, $value["celular"])
        ->setCellValue('F'.$i, $value["service"])
        ->setCellValue('G'.$i, $value["accountingMode"])
        ->setCellValue('H'.$i, $value["totalAmount"])
        ->setCellValue('I'.$i, $value["amount"])
        ->setCellValue('J'.$i, $value["quantitySent"])
        ->setCellValue('K'.$i, $value["fecha_creacion"]);
      $i++;
    }
  }

  public function setTableInfoSmsTwoWay() {
//    var_dump($this->data["sms"]);
//    exit();
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('D3', "REPORTE SMS")
            ->setCellValue('A8', "Nombre del envió:")
            ->setCellValue('A9', "Fecha del envió:")
            ->setCellValue('A10', "Categoria:")
            ->setCellValue('A11', "Creado por:")
            ->setCellValue('A12', "Cantidad de registros:")
            ->setCellValue('A13', "Destinatarios:")
            ->setCellValue('A14', "Enviados:")
            ->setCellValue('A15', "No enviados:")//*//
            ->setCellValue('B8', $this->data["sms"]->name)
            ->setCellValue('B9', $this->data["sms"]->startdate)
            ->setCellValue('B10', $this->data["sms"]->Smscategory->name)
            ->setCellValue('B11', $this->data["sms"]->createdBy)
            ->setCellValue('B12', count($this->data["detail"]))
            ->setCellValue('B13', $this->data["sms"]->total)
            ->setCellValue('B14', $this->data["sent"])
            ->setCellValue('B15', $this->data["undelivered"])
            ->setCellValue('A17', "Respuestas");
    $this->objPhpExcel->getActiveSheet()->getStyle('A17')->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
    $this->objPhpExcel->getActiveSheet()->getStyle('D3')->getAlignment();
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
            ->setCellValue('C2', "Reporte de Envíos de Email")
            ->setCellValue('C3', "Detalle de envíos de correo electrónico.")
            ->setCellValue('C4', "Elaborado por : SIGMA MOVIL S.A.S")
            ->setCellValue('C5', "Fecha y hora de generación de informe: " . date('Y-m-d h:i:sa'))
            ->setCellValue('C6', "Generado por usuario: " . \Phalcon\DI::getDefault()->get('user')->email);

    $this->objPhpExcel->setActiveSheetIndex(0)->getStyle("C2")->getFont()->setBold(true)->setName('Arial');
    $this->objPhpExcel->setActiveSheetIndex(0)->getStyle("C4")->getFont()->setBold(true)->setName('Arial');
  }

  public function setInfoDetailContactsForm() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', "INFORME DETALLADO DE CONTACTOS INSCRITOS POR FORMULARIO")
            ->setCellValue('A3', "Detalle de inscritos por formulario")
            ->setCellValue('A4', "Elaborado por:")
            ->setCellValue('C4', "SIGMA MOVIL S.A.S")
            ->setCellValue('A5', "Fecha de generación del informe:")
//            ->setCellValue('A6', "-----")
            ->setCellValue('A7', "Detalle de inscritos por formulario");
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

  public function generatedReportStatisallied() {
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('C7', "Mes anterior")
            ->setCellValue('E7', "Mes Actual")
            ->setCellValue('A8', "Nombre:")
            ->setCellValue('B8', "Contactos:")
            ->setCellValue('C8', "Envios por mail:")
            ->setCellValue('D8', "Envios por sms:")
            ->setCellValue('E8', "Envios por mail:")
            ->setCellValue('F8', "Envios por sms:");
    $i = 9;

    foreach ($this->dat as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $i, $key->name)
              ->setCellValue('B' . $i, $key->contacts)
              ->setCellValue('C' . $i, $key->enviosmail)
              ->setCellValue('D' . $i, $key->enviossms)
              ->setCellValue('E' . $i, $key->enviosmaila)
              ->setCellValue('F' . $i, $key->enviossmsb);
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
            ->setCellValue('C2', "REPORTE HISTORIAL DE RECARGAS POR CUENTA")
            ->setCellValue('C4', "Fecha de Generación: ". date("Y-m-d h:i:s"))
            ->setCellValue('C5', "Realizado por Usuario: ". \Phalcon\DI::getDefault()->get('user')->email);

    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A8', "Cuenta")
            ->setCellValue('B8', "Servicio")
            ->setCellValue('C8', "Cantidad Recargada:")
//            ->setCellValue('D8', "Monto Inicial:")
            ->setCellValue('D8', "Nuevo Saldo Disponible")
            ->setCellValue('E8', "Nuevo Limite")
            ->setCellValue('F8', "Fecha Recarga")
            ->setCellValue('G8', "Realizado por");
    $i = 9;
    foreach ($this->data as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $i, $key["nameaccount"])
              ->setCellValue('B' . $i, ($key["idService"] == 1 ?"SMS":"Contactos"))
              ->setCellValue('C' . $i, $key["rechargeAmount"])
//              ->setCellValue('D' . $i, $key->initialTotals)
              ->setCellValue('D' . $i, $key["DisponibleAfter"])
              ->setCellValue('E' . $i, $key["TotalAfter"])
              ->setCellValue('F' . $i, $key["createds"])
              ->setCellValue('G' . $i, $key["createdBy"]);
     
      $i++;
      $history = \RechargeHistory::find(["conditions" => "idAccountConfig = ?0 "
          . "                                             AND idServices=?1 "
          . "                                             AND idRechargeHistory!=?2", 
                                  "bind" => [0 =>$key["idAccountConf"],1 => $key["idService"],2 =>$key["idRecharge"]],
                                  //"columns" => ["idAccount","name"],
                                  //"group" => ["idAccountConfig","idServices"],
                                  "order" => "created DESC"])->toArray();
      if($history){
        foreach($history as $v){
          $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('C' . $i, $v["rechargeAmount"])
              ->setCellValue('D' . $i, $v["initialAmount"] + $v["rechargeAmount"])
              ->setCellValue('E' . $i, $v["initialTotal"] + $v["rechargeAmount"])
              ->setCellValue('F' . $i, date("Y-m-d h:i:s",$v["created"]))
              ->setCellValue('G' . $i, $v["createdBy"])
              ->getStyle('C' . $i . ':G' . $i)
              ->getFont()->setItalic(true);
          $i++;
        }
      }
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
    $objActSheet
            ->setCellValue("A" . $this->i, "Subcuenta")
            ->setCellValue("B" . $this->i, "Usuario")
            ->setCellValue("C" . $this->i, "Nombre del envío")
            ->setCellValue("D" . $this->i, "Fecha del envío")
            ->setCellValue("E" . $this->i, "Total")
            ->setCellValue("F" . $this->i, "Aperturas")
            ->setCellValue("G" . $this->i, "Clicks")
            ->setCellValue("H" . $this->i, "Desuscritos")
            ->setCellValue("I" . $this->i, "Rebotes")
            ->setCellValue("J" . $this->i, "Spam");
    $objActSheet->getStyle("A" . $this->i . ":J" . $this->i)->getFont()->setBold(true)
            ->setName('Arial');
    $this->i++;
    foreach ($this->data as $key) {

      $objActSheet->getColumnDimension("A")->setAutoSize(true);
      $objActSheet->setCellValue("A" . $this->i, $key->nameSubaccount);
      $objActSheet->getColumnDimension("B")->setAutoSize(true);
      $objActSheet->setCellValue("B" . $this->i, $key->createdBy);
      $objActSheet->getColumnDimension("C")->setAutoSize(true);
      $objActSheet->setCellValue("C" . $this->i, $key->nameMail);
      $objActSheet->getColumnDimension("D")->setAutoSize(true);
      $objActSheet->setCellValue("D" . $this->i, $key->scheduleDate);
      $objActSheet->getColumnDimension("E")->setAutoSize(true);
      $objActSheet->setCellValue("E" . $this->i, $key->messagesSent);
      $objActSheet->getColumnDimension("F")->setAutoSize(true);
      $objActSheet->setCellValue("F" . $this->i, $key->uniqueOpening);
      $objActSheet->getColumnDimension("G")->setAutoSize(true);
      $objActSheet->setCellValue("G" . $this->i, $key->uniqueClicks);
      $objActSheet->getColumnDimension("H")->setAutoSize(true);
      $objActSheet->setCellValue("H" . $this->i, $key->unsuscribed);
      $objActSheet->getColumnDimension("I")->setAutoSize(true);
      $objActSheet->setCellValue("I" . $this->i, $key->bounced);
      $objActSheet->getColumnDimension("J")->setAutoSize(true);
      $objActSheet->setCellValue("J" . $this->i, $key->spam);
      $this->i++;
    }
  }

  public function generatedReportContactForm($dataForm, $total) {

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

    //$objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);
    $numLetterDescrip = 6;
    $a = 'A';
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Formulario');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Categoría');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Creado por:');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Fecha de Creación');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Número de suscripción');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Actualizado por');
    $objActSheet->setCellValue($a . $numLetterDescrip++, 'Fecha de actualización');

    $a = 'B';
    $numLetterDescrip = 6;
    //foreach ($dataForm as $key => $value) {
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['nameForm']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['name']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['createdBy']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['created']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $total);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['updatedBy']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $dataForm['updated']);
    //}

    $arryDataPrint = $this->data;
    $arrayTitles = array_keys($this->data[0]);
    $a = 'A';
    $numColumnLetter = 14;
    foreach ($arrayTitles as $keyTitle => $valueTitle) {
      $objActSheet->setCellValue($a++ . 14, $valueTitle);
      $objActSheet->getStyle($a . $numColumnLetter)->getFont()->setBold(true)->setName('Arial');
      $this->objPhpExcel->getActiveSheet()->getColumnDimension($a . $numColumnLetter)->setAutoSize(true);
    }
    $objActSheet->getStyle("A" . $numColumnLetter)->getFont()->setBold(true)->setName('Arial');
    $numColumnLetter++;

    $letters = 'A';
    $numFil = 15;
    foreach ($this->data as $key => $value) {
      foreach ($value as $key2 => $value2) {
        $objActSheet->setCellValue($letters++ . $numFil, $value2);
      }
      $letters = 'A';
      $numFil++;
    }
  }

  public function generatedInfoDetailEmailValidation() {
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
    /* $objActSheet->getColumnDimension("A")->setWidth(25);
      $objActSheet->getColumnDimension("B")->setWidth(25);
      $objActSheet->getColumnDimension("C")->setWidth(25);
      $objActSheet->getColumnDimension("D")->setWidth(25);
      $objActSheet->getColumnDimension("E")->setWidth(25); */

    $numLetterDescrip = 6;
    $a = 'A';
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Fecha del reporte");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Hora del reporte");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Aliado");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Cantidad de correos validados");
    $objActSheet->getStyle("A6" . ":" . $a . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');

    $countData = count($this->data['data']);
    $a = 'B';
    $numLetterDescrip = 6;
    $objActSheet->setCellValue($a . $numLetterDescrip++, date('Y-m-d'));
    $objActSheet->setCellValue($a . $numLetterDescrip++, date('H:i:s'));
    $objActSheet->setCellValue($a . $numLetterDescrip++, $this->data['nameAllied']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $countData);
    $objActSheet->getStyle($a . "6:" . $a . $numLetterDescrip);

    $numLetterDescrip = 11;
    $a = 'A';
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Fecha de validación');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Cuenta');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'SubCuenta');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Correo');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Nombre de la campaña');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Categoría');
    $objActSheet->setCellValue($a . $numLetterDescrip, 'Validación');
    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip);
    $objActSheet->setAutoFilter("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip);

    $numLetterDescrip = 12;
    $a = 'A';
    foreach ($this->data['data'] as $key => $value) {
      $a = 'A';
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->dateTime);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->account);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->subaccount);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->email);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->name);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->score);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->evaluation);
      $numLetterDescrip++;
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
            ->setCellValue('A17', "Envíos realizados  ")
            ->setCellValue('A18', "Codigo del país  ")
            ->setCellValue('B18', "Móvil")
            ->setCellValue('C18', "Mensaje")
            ->setCellValue('D18', "Estado");
    $i = 19;
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
                ->setCellValue('A' . $i, "+" . $key["indicative"])
                ->setCellValue('B' . $i, $key["phone"])
                ->setCellValue('C' . $i, $key["message"])
                ->setCellValue('D' . $i, $this->traslateStatusSms($key["status"]));
        $i++;
      }
    }
    $this->setMessageFailed($i);
  }

  public function generatedReportSmsTwoWay($idSms) {
    $typeResponse = json_decode($this->data["sms"]->typeResponse);
    $rowInitial = 19;
    $initial = (count($typeResponse->typeResponse) + 2) + $rowInitial;
    $this->objPhpExcel->setActiveSheetIndex(0)->setCellValue('A' . $initial, "Envíos realizados ");
    $this->objPhpExcel->getActiveSheet()->getStyle('A' . $initial)->getFont()->setBold(true);
    $initial = $initial + 1;
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B' . $initial, "Codigo del país ")
            ->setCellValue('C' . $initial, "Móvil")
            ->setCellValue('D' . $initial, "Mensaje")
            ->setCellValue('E' . $initial, "Estado")
            ->setCellValue('F' . $initial, "Fecha respuesta")
            ->setCellValue('G' . $initial, "Respuesta")
            ->setCellValue('H' . $initial, "Grupo");
    $i = $initial + 1;
    $textStyle = "A" . $initial . ":H" . $initial;

    $this->objPhpExcel->getActiveSheet()->getStyle($textStyle)->getFont()->setBold(true);
    $sms = \Smstwoway::findFirst(["conditions" => "idSmsTwoway = ?0", "bind" => [0 => $idSms]]);

    $idsSmslote = \Smslotetwoway::find(array(
                "conditions" => "idSmsTwoway = ?0",
                "bind" => array($sms->idSmsTwoway)
    ));

    foreach ($this->data['detail'] as $key) {

      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('B' . $i, "+" . $key->indicative)
              ->setCellValue('C' . $i, $key->phone)
              ->setCellValue('D' . $i, $key->message)
              ->setCellValue('E' . $i, $this->traslateStatusSms($key->status))
              ->setCellValue('F' . $i, date("Y-m-d H:i:s", $key->updated))
              ->setCellValue('G' . $i, $key->userResponse)
              ->setCellValue('H' . $i, $key->userResponseGroup);
      $i++;

      //consulta el SmsLote para luego extraer su id
      $smsLoteTwoway = \Smslotetwoway::findFirst(
                      array(
                          "conditions" => "idSmsLoteTwoway = ?0",
                          "bind" => array($key->idSmsLoteTwoway)
                      )
      );

      //consulta el Receiversms para luego extraer el historial de mensajes de el anterior id
      $receiverTable = \Receiversms::findFirst(array(
                  "conditions" => array(
                      "idSmslote" => (string) $key->idSmsLoteTwoway
                  )
      ));

      //creo un arreglo temporal
      $arrayInfo = array();

      //validando que tenga mensajes en el modelo ReceiverSms...
      if ($receiverTable) {

        foreach ($receiverTable->dataReceiver as $key2) {  //paseme todos los datos a un nuevo array
          //adelanto un ciclo para que no arme el arreglo de 
          //repuesta mostrando 2 veces el ultimo numero en el excel
          $arrayInfo[] = [
              "dateRegister" => $key2['dateRegister'],
              "receiver" => $key2['receiver'],
              "group" => $key2['group']
          ];
        }

        $arrayInfo = array_reverse($arrayInfo); //ubico las posiciones al reves...


        /* para borrar la ultima respuesta 
         * que actualmente ya se muestra 
         * en la fila de doble via */
        unset($arrayInfo[0]);
        //var_dump($arrayInfo);exit();
        //para que devuelva el arreglo al reves...
        //var_dump($arrayInfo);exit();
        //devuelvo el arreglo al reves para que imprima en este orden en excel...
        foreach ($arrayInfo as $key => $value2) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('F' . $i, $value2['dateRegister'])
                  ->setCellValue('G' . $i, $value2['receiver'])
                  ->setCellValue('H' . $i, $value2['group'])
                  ->getStyle('F' . $i . ':H' . $i)
                  ->getFont()->setItalic(true);
          $i++;
        }
      }
    }
    $this->setResponseGroup();
  }

  public function setResponseGroup() {
    $arrLote = \Smslotetwoway::find(array("conditions" => " idSmsTwoway = ?0 and status = 'sent'", "bind" => array($this->data["sms"]->idSmsTwoway), "columns" => "count(IFNULL(userResponseGroup, 1)) as count,userResponseGroup", "group" => "userResponseGroup"));
//    var_dump($arrLote->toArray());
//    exit();
    $initial = 18;
    foreach ($arrLote as $key => $value) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $initial, ($value["userResponseGroup"] == null) ? "Sin responder" : $value["userResponseGroup"])
              ->setCellValue('B' . $initial, ($value["count"] / $this->data["sent"] * 100) . '%');
      $initial++;
    }
  }

  public function homologate($homo, $response) {
    $homoJson = json_decode($homo);
    $stringReturn = "Otro";
    for ($i = 0; $i < count($homoJson->typeResponse); $i++) {
      $selection = $homoJson->typeResponse[$i]->response;
      $homologate = array_map('strtolower', explode(",", $homoJson->typeResponse[$i]->homologate));
      if (strtolower($selection) == strtolower($response) || in_array(strtolower($response), $homologate)) {
        $this->arrTwoWayResponse[$selection] += 1;
        return $selection;
      }
    }
    $this->arrTwoWayResponse[$stringReturn] += 1;
    return $stringReturn;
  }

  public function setMessageFailed($continue) {
    $i = $continue + 2;
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, "Registros repetidos");
    $i++;
    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, "Codigo del país")
            ->setCellValue('B' . $i, "Móvil")
            ->setCellValue('C' . $i, "Mensaje")
            ->setCellValue('D' . $i, "Cant. Veces");
    $i++;
    foreach ($this->data['failed'] as $key) {
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $i, "+" . $key->code)
              ->setCellValue('B' . $i, $key->phone)
              ->setCellValue('C' . $i, $key->message)
              ->setCellValue('D' . $i, $key->count);
      $i++;
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
    if ($status == "0: Accepted for delivery" || $status == "PENDING_ENROUTE") {
      $statusSpanish = "Enviado";
    }
    return $statusSpanish;
  }

  public function download() {
    $objWriter = new \PHPExcel_Writer_Excel2007($this->objPhpExcel, 'Excel5');
    $objWriter->save('../tmp/reporte.xlsx');
//    $objWriter->save('C:/Users/william.montiel/Documents/Plantillas personalizadas de Office/reporte'.time().'.xlsx');
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

  public function basicPropertiesReportStatisallied() {
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
    $objActSheet->getColumnDimension("F")->setWidth(25);
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
  }

  public function basicPropertiesInfoContactsForm() {
    $this->objPhpExcel->getProperties()->setCreator("AIO")
            ->setTitle("Reporte")
            ->setSubject("Asunto")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");
    $objActSheet = $this->objPhpExcel->setActiveSheetIndex(0);

    /* $objActSheet->getColumnDimension("A")->setWidth(10);
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
      $objActSheet->getRowDimension(7)->setRowHeight(20); */

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
    $objActSheet->getColumnDimension("A")->setWidth(35);
//    $objActSheet->getColumnDimension("B")->setWidth(20);
//    $objActSheet->getColumnDimension("C")->setWidth(45);
//    $objActSheet->getColumnDimension("D")->setWidth(15);
    $this->objPhpExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
    $this->objPhpExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
    $this->objPhpExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
  }

  public function setContentMail() {
    $mail = \Mail::findFirst([
                "conditions" => "idMail = " . $this->idMail,
    ]);
    $target = json_decode($mail->target, true);

    $flag = false;
    if (count($target['contactlists']) == 1) {
      $idContactlist = $target['contactlists'][0]['idContactlist'];
      $flag = true;
    } else {
      $flag = true;
    }

    $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('B6', $this->info['messageSent'])
            ->setCellValue('B7', $this->info['open'])
            ->setCellValue('B8', $this->info['uniqueClicks'])
            ->setCellValue('B9', $this->info['unsubscribed'])
            ->setCellValue('B10', $this->info['bounced'])
            ->setCellValue('B11', $this->info['spam'])
            ->setCellValue('B12', $this->info['buzon'])
            ->setCellValue('C7', $this->calculatePercentage($this->info['messageSent'], $this->info['open']) . "%")
            ->setCellValue('C8', $this->calculatePercentage($this->info['messageSent'], $this->info['uniqueClicks']) . "%")
            ->setCellValue('C9', $this->calculatePercentage($this->info['messageSent'], $this->info['unsubscribed']) . "%")
            ->setCellValue('C10', $this->calculatePercentage($this->info['messageSent'], $this->info['bounced']) . "%")
            ->setCellValue('C11', $this->calculatePercentage($this->info['messageSent'], $this->info['spam']) . "%")
            ->setCellValue('C12', $this->calculatePercentage($this->info['messageSent'], $this->info['buzon']) . "%");
    $i = 15;
    switch ($this->type) {
      case "clic":
        foreach ($this->data as $key) {
          $letter = "G";
          $flag = false;
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, date("d/M/Y g:i A", $key->date))
                  ->setCellValue('B' . $i, $key->email)
                  ->setCellValue('C' . $i, $key->name)
                  ->setCellValue('D' . $i, $key->lastname)
                  ->setCellValue('E' . $i, $key->indicative)
                  ->setCellValue('F' . $i, $key->phone);

          if ($flag == true) {
            $cxc = \Cxc::findFirst([["idContact" => (int) $key->idTmpTable]]);
            if (count($cxc->idContactlist[$idContactlist]) > 0) {
              foreach ($cxc->idContactlist[$idContactlist] as $value) {
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
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $key->dateOpen)
            ->setCellValue('B' . $i, $key->email)
            ->setCellValue('C' . $i, $key->name)
            ->setCellValue('D' . $i, $key->lastname)
            ->setCellValue('E' . $i, $key->indicative)
            ->setCellValue('F' . $i, $key->phone)
            ->setCellValue('G' . $i, $key->motive);
          $i++;
        }
        break;
      case "open":
      case "spam":
        foreach ($this->data as $key) {
          $letter = "H";
          $opening = $key['totalOpening'];
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, date("d/M/Y g:i A", $key['dateOpen']))
                  ->setCellValue('B' . $i, $key['email'])
                  ->setCellValue('C' . $i, $key['name'])
                  ->setCellValue('D' . $i, $key['lastName'])
                  ->setCellValue('E' . $i, $key['indicative'])
                  ->setCellValue('F' . $i, $key['phone']);
          if ($flag == true) {
            $cxc = \Cxc::findFirst([["idContact" => (int) $key["idTmpTable"]]]);
            if (count($target['contactlists']) > 1) {
              foreach ($target['contactlists'] as $key) {
                foreach (array_key_exists(0, $cxc->idContactlist[$key['idContactlist']]) as $value) {
                  $this->objPhpExcel->setActiveSheetIndex(0)
                          ->setCellValue($letter . $i, $value['value']);
                  $letter++;
                }
              }
            } else {
              if(isset($cxc->idContactlist[$idContactlist])){
                foreach ($cxc->idContactlist[$idContactlist] as $value) {
                  $this->objPhpExcel->setActiveSheetIndex(0)
                          ->setCellValue($letter . $i, $value['value']);
                  $letter++;
                }
              }
            }
          }
//          $this->objPhpExcel->setActiveSheetIndex(0)->setCellValue($letter . $i, $key['totalOpening']);
          $this->objPhpExcel->setActiveSheetIndex(0)->setCellValue($this->titleOpening . $i, 1);
          $i++;
        }
        break;
      case "bounced":
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                  //->setCellValue('A' . $i, $key->dateOpen)
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
      case "buzon":
        foreach ($this->data as $key) {
          $this->objPhpExcel->setActiveSheetIndex(0)
                  ->setCellValue('A' . $i, $key->dateOpen)
                  ->setCellValue('B' . $i, $key->email)
                  ->setCellValue('C' . $i, $key->name)
                  ->setCellValue('D' . $i, $key->lastname)
                  ->setCellValue('E' . $i, $key->indicative)
                  ->setCellValue('F' . $i, $key->phone)
                  ->setCellValue('G' . $i, $key->buzon);
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

    $n = 8;
    $l = "E";
    $number = 7;

    $questionExcel = $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue("B2", "Nombre de la encuesta")
            ->setCellValue("B3", "Descripción")
            ->setCellValue("B4", "Tipo")
            ->setCellValue("B5", "Total encuestados")
            ->setCellValue("C2", $this->infoSurvey['survey']['name'])
            ->setCellValue("C3", $this->infoSurvey['survey']['description'])
            ->setCellValue("C4", $this->infoSurvey['survey']['type'])
            ->setCellValue("C5", $this->infoSurvey['survey']['totalCount']);

    $questionExcel->getStyle('B2', "Nombre de la encuesta")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle('B3', "Descripción")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle('B4', "Tipo")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle('B5', "Total encuestados")->getFont()->setBold(true)->setName('Arial');


    $questionExcel = $this->objPhpExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $number, "Fecha y Hora:")
            ->setCellValue("B" . $number, "Nombre")
            ->setCellValue("C" . $number, "Apellido")
            ->setCellValue("D" . $number, "Correo");
    $questionExcel->getStyle("A" . $number, "Fecha y Hora")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle("B" . $number, "Nombre")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle("C" . $number, "Apellido")->getFont()->setBold(true)->setName('Arial');
    $questionExcel->getStyle("D" . $number, "Correo")->getFont()->setBold(true)->setName('Arial');
    $one = 0;
      
    $flag = true;

    foreach ($this->data as $value) {
      $le = "E";
      $this->objPhpExcel->setActiveSheetIndex(0)
              ->setCellValue("A" . $n, date('Y-m-d G:i:s', $value->dateandhour))
              ->setCellValue("B" . $n, $value->name)
              ->setCellValue("C" . $n, $value->lastname)
              ->setCellValue("D" . $n, $value->email);
      foreach ($value->questions as $ques) {
        if ($flag) {
          $questionExcel->setCellValue($l . $number, $ques->question);
          $questionExcel->getStyle($l . $number, $ques->question)->getFont()->setBold(true)->setName('Arial');
        }
        $answerfinal = '';
        foreach ($ques->answer as $answer) {
          $answerfinal = $answerfinal . $answer . "," ;  
        }        
        $answerfinal = substr($answerfinal, 0, -1);        
        $questionExcel->setCellValue($le . $n, $answerfinal);
        $le++;
        $l++;
      }
      $flag = false;
      $n++;
    }
  }

  public function calculatePercentage($total, $value) {
    $p = ($value / $total) * 100;
    if ($p % 1 == 0) {
      return round($p, 2);
    } else {
      return $p;
    }
  }

  public function setData($data) {
    $this->data = $data;
    return $this;
  }

  public function setDatastatic($dat) {
    $this->dat = $dat;
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

  public function setIdMail($idMail) {
    $this->idMail = $idMail;
  }

  public function phpExcelWorksheet($objPHPExcel) {
    //Instaciamos la Clase PHPExcel_Worksheet_Drawing
    $objDrawing = new \PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('aio');
    $objDrawing->setDescription('aio');
    //Colocamos la imagen institucional
    $objDrawing->setPath('./images/logo.jpg');
    //Esta es la celda donde la imagen va aparecer
    $objDrawing->setCoordinates('A1');
    $objDrawing->getShadow()->setVisible(true);
    $objDrawing->getShadow()->setDirection(45);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    //Retornamos la Clase PHPExcel_Worksheet_Drawing
    return $objDrawing;
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
            ->setCellValue("H" . $this->i, "Total")
            ->setCellValue("I" . $this->i, "Cantidad para cobro");
    $objActSheet->getStyle("A" . $this->i . ":I" . $this->i)->getFont()->setBold(true)
            
            ->setName('Arial');
    $this->i++;
   
    foreach ($this->data as $key) {
      if($key['type'] == "contact" || $key['type'] == "automatic"){ 
        $messageCount = $this->findMessageCount($key['idSms'], $key['type']);
      } else {
        $messageCount = $key['messageCount'];
      }
      $objActSheet->setCellValue("A" . $this->i, $key['idSms'])
              ->setCellValue("B" . $this->i, $key['startdate'])
              ->setCellValue("C" . $this->i, $key['namesubaccount'])
              ->setCellValue("D" . $this->i, $key['createdBy'])
              ->setCellValue("E" . $this->i, $key['namesms'])
              ->setCellValue("F" . $this->i, $key['sent'])
              ->setCellValue("G" . $this->i, $key['undelivered'])
              ->setCellValue("H" . $this->i, $key['total'])
              ->setCellValue("I" . $this->i, $messageCount);
      $this->i++;
    }
  }

  public function downloadExcel($name) {
    $nameFull = str_replace(" ", "_", $name) . "_" . date('Y-m-d') . ".xlsx";
    $objWriter = new \PHPExcel_Writer_Excel2007($this->objPhpExcel, 'Excel5');
    $valorWriter = $objWriter->save('../tmp/' . $nameFull);
    $existFile = file_exists('../tmp/' . $nameFull);
//    $valorWriter = $objWriter->save("C:/Users/juan.cruz/Documents/NetbeansProjecst/aio/public/tmp/".$nameFull);//local
//    $existFile = file_exists("C:/Users/juan.cruz/Documents/NetbeansProjecst/aio/public/tmp/".$nameFull);//local
    if ($existFile) {
      return ['respuest' => 1];
    } else {
      return ['respuest' => 0];
    }
  }

  public function generatedInfoDetailEmailBounced() {
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

    $numLetterDescrip = 6;
    $a = 'A';
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Fecha del reporte");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Hora del reporte");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Aliado");
    $objActSheet->setCellValue($a . $numLetterDescrip++, "Cantidad de correos validados");
    $objActSheet->getStyle("A6" . ":" . $a . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');

    $countData = count($this->data['data']);
    $a = 'B';
    $numLetterDescrip = 6;
    $objActSheet->setCellValue($a . $numLetterDescrip++, date('Y-m-d'));
    $objActSheet->setCellValue($a . $numLetterDescrip++, date('H:i:s'));
    $objActSheet->setCellValue($a . $numLetterDescrip++, $this->data['nameAllied']);
    $objActSheet->setCellValue($a . $numLetterDescrip++, $countData);
    $objActSheet->getStyle($a . "6:" . $a . $numLetterDescrip)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $numLetterDescrip = 11;
    $a = 'A';
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Fecha de validación');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Cuenta');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'SubCuenta');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Correo');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Nombre de la campaña');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Categoría');
    $objActSheet->setCellValue($a . $numLetterDescrip, 'Validación');
    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objActSheet->setAutoFilter("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip);

    $numLetterDescrip = 12;
    $a = 'A';
    foreach ($this->data['data'] as $key => $value) {
      $a = 'A';
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->datetime);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->account);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->subaccount);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->email);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->name);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->code);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value->evaluation);
      $numLetterDescrip++;
    }
  }

  public function generatedInfoSmsByDestinataries() {
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

    $numLetterDescrip = 10;
    $a = 'A';
    $objActSheet->setCellValue('C2', "DETALLE DE ENVÍOS DE SMS POR CELULAR");
    $objActSheet->getStyle('C2', "DETALLE DE ENVÍOS DE SMS POR CELULAR")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C4', "Elaborado por: SIGMA MÓVIL");
    $objActSheet->getStyle('C4', "Elaborado por: SIGMA MÓVIL")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C5', "Generado por usuario: " . \Phalcon\DI::getDefault()->get('user')->email);
    $objActSheet->setCellValue('C6', "Fecha y hora de generación: " . date("Y-m-d h:i:sa"));
    $objActSheet->setCellValue('C7', "Cantidad de Registros: " . count($this->data));
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Fecha y hora de Envío');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Número de celular');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Nombre Campaña Sms');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Mensaje');
    $objActSheet->setCellValue($a++ . $numLetterDescrip, 'Estado del envío');
    $objActSheet->setCellValue($a . $numLetterDescrip, 'Cantidad para cobro');

    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
    $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip);
//    $objActSheet->setAutoFilter("A" . $numLetterDescrip . ":" . $a . $numLetterDescrip);

    $numLetterDescrip = 11;
    $a = 'A';
    foreach ($this->data as $key => $value) {
      $a = 'A';
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value["date"]);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value["phone"]);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value["name"]);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $value["message"]);
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a++ . $numLetterDescrip, $this->traslateStatusSms($value["status"]));
      $objActSheet->getColumnDimension($a)->setAutoSize(true);
      $objActSheet->setCellValue($a . $numLetterDescrip, $value["messageCount"]);
      $numLetterDescrip++;
    }
  }

  public function generatedReportSmsFailed() {
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

    $numLetterDescrip = 14;
    $c = 'C';

    $objActSheet->setCellValue('C2', "DETALLE DE NÚMEROS CELULARES INVÁLIDOS");
    $objActSheet->getStyle('C2', "DETALLE DE NÚMEROS CELULARES INVÁLIDOS")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C4', "Elaborado por: SIGMA MÓVIL");
    $objActSheet->getStyle('C4', "Elaborado por: SIGMA MÓVIL")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C5', "Generado por usuario: " . \Phalcon\DI::getDefault()->get('user')->email);
    $objActSheet->setCellValue('C6', "Fecha y hora de generación: " . date("Y-m-d h:i:sa"));

    $objActSheet->setCellValue('B8', "Cantidad de registros: " . $this->data->preload->data->rowsCsv);
    $objActSheet->setCellValue('B9', "Cantidad de registros repetidos: " . $this->data->validations->data->countRepeat);
    $objActSheet->setCellValue('B10', "Cantidad de registros no compatible con el indicativo: " . $this->data->validations->data->countInvalid);
    $objActSheet->setCellValue('B11', "Cantidad de registros inválidos: " . $this->data->validations->data->countTotal);
    $objActSheet->setCellValue('B12', "Total registros para envío: " . $this->data->load->data->countSent);

    $b = 'B';

    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Código del país');
    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Móvil');
    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Mensaje');
    $objActSheet->setCellValue($b . $numLetterDescrip, 'Descripción de validación');
    $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
    $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip);

    $numLetterDescrip = 15;
    $b = 'B';

    foreach ($this->data->excel as $key => $value) {
      $b = 'B';
//        $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->getColumnDimension('B')->setWidth(50);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $value["indicativo"]);
      $objActSheet->getColumnDimension($b)->setWidth(52);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $value["celular"]);
      $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $value["mensaje"]);
      $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->setCellValue($b . $numLetterDescrip, $value["detalle"]);
      $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip);
      $numLetterDescrip++;
    }
  }

  public function generatedReportSmsFailedContact($Smsdetail) {
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

    $numLetterDescrip = 14;
    $c = 'C';

    $objActSheet->setCellValue('C2', "DETALLE DE NÚMEROS CELULARES INVÁLIDOS");
    $objActSheet->getStyle('C2', "DETALLE DE NÚMEROS CELULARES INVÁLIDOS")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C4', "Elaborado por: SIGMA MÓVIL");
    $objActSheet->getStyle('C4', "Elaborado por: SIGMA MÓVIL")->getFont()->setBold(true)->setName('Arial');
    $objActSheet->setCellValue('C5', "Generado por usuario: " . \Phalcon\DI::getDefault()->get('user')->email);
    $objActSheet->setCellValue('C6', "Fecha y hora de generación: " . date("Y-m-d h:i:sa"));


    $objActSheet->setCellValue('B7', "Cantidad inicial de Registros: " . $Smsdetail[0]['TotalInicial']);
    $objActSheet->setCellValue('B8', "Cantidad Registros únicos: " . $Smsdetail[1]['TotalUnicos']);
    $objActSheet->setCellValue('B9', "Cantidad de registros repetidos: " . $Smsdetail[4]['Repetidos']);
    $objActSheet->setCellValue('B10', "Cantidad de registros inválidos: " . $Smsdetail[3]['Invalidos']);
    $objActSheet->setCellValue('B11', "Total registros para envío: " . $Smsdetail[2]['Total']);

    $b = 'B';

    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Código del país');
    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Móvil');
    $objActSheet->setCellValue($b++ . $numLetterDescrip, 'Mensaje');
    $objActSheet->setCellValue($b . $numLetterDescrip, 'Descripción de validación');
    $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
    $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip);

    $numLetterDescrip = 15;
    $b = 'B';

    foreach ($this->data->excel as $key => $value) {
      $b = 'B';
//        $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->getColumnDimension('B')->setWidth(50);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $value["indicativo"]);
      $objActSheet->getColumnDimension($b)->setWidth(52);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $value["celular"]);
      $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->setCellValue($b++ . $numLetterDescrip, $Smsdetail[5]['mensaje']);
      $objActSheet->getColumnDimension($b)->setAutoSize(true);
      $objActSheet->setCellValue($b . $numLetterDescrip, $value["detalle"]);
      $objActSheet->getStyle("B" . $numLetterDescrip . ":" . $b . $numLetterDescrip);
      $numLetterDescrip++;
    }
    if(isset($Smsdetail[6]['numerosRepetidos'])){
      $A = 'A';
      $numLetterDescrip = 14;
      $objActSheet->setCellValue('A' . $numLetterDescrip, 'Números Repetidos');
      $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $A . $numLetterDescrip)->getAlignment();
      $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $A . $numLetterDescrip)->getFont()->setBold(true)->setName('Arial');
      $objActSheet->getColumnDimension($A)->setWidth(22);
      $numLetterDescrip = $numLetterDescrip+1;
      $arrRepeatedNumbersLength = count($Smsdetail[6]['numerosRepetidos']);
      for($i=0;$i<$arrRepeatedNumbersLength;$i++) {
        $objActSheet->setCellValue($A . $numLetterDescrip, $Smsdetail[6]['numerosRepetidos'][$i]);
        $objActSheet->getStyle("A" . $numLetterDescrip . ":" . $A . $numLetterDescrip);
        $numLetterDescrip++;
      }
    }
  }

  public function setDataContact($data) {
    $this->data = $data;
    return $this;
  }
  
  public function findMessageCount($idSms, $type){
    $collectionSmsXc = [[ '$match' => ['idSms' => (string) $idSms] ],[ '$group' => ['_id' => '$idSms', 'messageCount' => ['$sum' => '$messageCount']]]];
    $count1 = \Smsxc::aggregate($collectionSmsXc);
    return $count1['result'][0]['messageCount'];
  }

}
