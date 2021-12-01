<?php

class ReportSmsxemail extends Modelbase {
  
  public $idReportSmsxEmail,
          $idEmail,
          $idSubaccount,
          $idSms,
          $smsFailed;
  
  public function getSource() {
    return "report_smsxemail";
  }
}
