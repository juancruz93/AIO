<?php

/**
 * Description of ApireportController
 *
 * @author desarrollo3
 * @RoutePrefix("/api/report")
 */
class ApireportController extends ControllerBase {

  /**
   * 
   * @Post("/getallreportemail/{page:[0-9]+}")
   */
  public function getallreportemailAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->getAllReportMail();
      return $this->set_json_response($wrapper->getReport(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/getallreportsms/{page:[0-9]+}")
   */
  public function getallreportsmsAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->getAllReportSms();
      return $this->set_json_response($wrapper->getReport(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getallaccountbyallied")
   */
  public function getallaccountbyalliedAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->getAllAccountByAllied();
      return $this->set_json_response($wrapper->getAccount(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/downloadreport")
   */
  public function downloadreportAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      
      $wrapper->setSearch($data);
//      var_dump($data); exit;
      $wrapper->generateReportExcel();
      return $this->set_json_response($wrapper->getAccount(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/downloadreportsms")
   */
  public function downloadreportsmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->generateReportExcelSms();
      return $this->set_json_response($wrapper->getAccount(), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/downloadreportrecharge/{title}")
   */
    public function downloadreportrechargeAction($title) {
        try {
            $contentsraw = $this->getRequestContent();
            $data = json_decode($contentsraw);
            $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
            $wrapper->setSearch($data);
//            return $this->set_json_response($wrapper->getAccount(), 200);
            return $this->set_json_response($wrapper->generateReportExcelRecharge($title), 200);
        } catch (InvalidArgumentException $msg) {
            $this->logger->log("Exception while create report... {$msg}");
            return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
            $this->logger->log("Exception while create report... {$ex}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
    /**
   * 
   * @Post("/downloadreportchangeplan/{title}")
   */
    public function downloadreportchangeplanAction($title) {
        try {
            $contentsraw = $this->getRequestContent();
            $data = json_decode($contentsraw);
            $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
            $wrapper->setSearch($data);
            $wrapper->generateReportExcelChangePlan($title);
            return $this->set_json_response($wrapper->getAccount(), 200);
        } catch (InvalidArgumentException $msg) {
            $this->logger->log("Exception while create excel... {$msg}");
            return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
            $this->logger->log("Exception while create excel... {$ex}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

  /**
   * 
   * @Post("/graphmail")
   */
  public function graphmailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->reportGraphMail();
      return $this->set_json_response($wrapper->getGraph(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/graphsms")
   */
  public function graphsmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->reportGraphSms();
      return $this->set_json_response($wrapper->getGraph(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/getinfoexcelsms/{page:[0-9]+}")
   */
  public function getinfoexcelsmsAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->getInfoExcelSms();
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/getinfoexcelsmsday/{page:[0-9]+}")
   */
  public function getinfoexcelsmsdayAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->getInfoExcelSmsByDay();
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/downloadsms")
   */
  public function downloadsmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->downloadInfoSms();
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/downloadsmsbyday/{title}")
   */
  public function downloadsmsbydayAction($title) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->downloadInfoSmsByDay($title);
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/infosms/{page:[0-9]+}")
   */
  public function infosmsAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->infoDetailSms();
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/infomail/{page:[0-9]+}")
   */
  public function infomailAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      $wrapper->infoDetailMail();
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/dowloadreportinfodetailsms/{title}")
   */
  public function dowloadreportinfodetailsmsAction($title) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->dowloadReportInfoDetailSms($title);
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/dowloadreportinfodetailmail/{title}")
   */
  public function dowloadreportinfodetailmailAction($title) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->dowloadReportInfoDetailMail($title);
      return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getallsubaccount")
   */
  public function getallsubaccountAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->getAllSubaccount();
      return $this->set_json_response($wrapper->getSubaccount(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Get("/getemailusers")
   */
  public function getemailusersAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->getEmailUsers();
      return $this->set_json_response($wrapper->getUsers(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/reportrecharge/{page:[0-9]+}")
   */
  public function getallreportrechargeAction($page) {
        try {
          $contentsraw = $this->getRequestContent();
          $data = json_decode($contentsraw);
          $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
          $wrapper->setSearch($data);
          $wrapper->setPage($page);
          $wrapper->getAllReportRecharge();
          return $this->set_json_response($wrapper->getReport(), 200);
        } catch (InvalidArgumentException $msg) {
          $this->logger->log("Exception while create mail... {$msg}");
          return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
          $this->logger->log("Exception while create mail... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
    /**
     * 
     * @Post("/reportchangeplan/{page:[0-9]+}")
     */
    public function getallreportchangeplanAction($page){
        try {
          $contentsraw = $this->getRequestContent();
          $data = json_decode($contentsraw);
          $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
          $wrapper->setSearch($data);
          $wrapper->setPage($page);
          $wrapper->getAllReportPlan();
          return $this->set_json_response($wrapper->getReport(), 200);
        } catch (InvalidArgumentException $msg) {
          $this->logger->log("Exception while create mail... {$msg}");
          return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
          $this->logger->log("Exception while create mail... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
    /**
     * @Post("/getallmailvalidation/{page:[0-9]+}")
     */
    public function getallmailvalidationAction($page) {
      try {
        $contentsraw = $this->getRequestContent();
        $data = json_decode($contentsraw);
        $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
        $wrapper->setSearch($data);
        $wrapper->setPage($page);
        return $this->set_json_response($wrapper->getAllMailValidation(), 200);
      }
      catch (InvalidArgumentException $msg) {
          $this->logger->log("Exception while create mail... {$msg}");
          return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
          $this->logger->log("Exception while create mail... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
  /**
   * 
   * @Post("/downloadmailvalidation/{page:[0-9]+}")
   */
  public function downloadmailvalidationAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      return $this->set_json_response($wrapper->downloadMailValidation(), 200);
      //return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
     * @Post("/getallmailbounced/{page:[0-9]+}")
     */
    public function getallmailbouncedAction($page) {
      try {
        $contentsraw = $this->getRequestContent();
        $data = json_decode($contentsraw);
        $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
        $wrapper->setSearch($data);
        $wrapper->setPage($page);
        return $this->set_json_response($wrapper->getAllMailBounced(), 200);
      }
      catch (InvalidArgumentException $msg) {
          $this->logger->log("Exception while create mail... {$msg}");
          return $this->set_json_response(array('message' => $msg->getMessage()), 403);
        } catch (Exception $ex) {
          $this->logger->log("Exception while create mail... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
  /**
   * 
   * @Post("/downloadgetallmailbounced/{page:[0-9]+}")
   */
  public function downloadgetallmailbouncedAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      return $this->set_json_response($wrapper->downloadGetAllMailBounced(), 200);
      //return $this->set_json_response($wrapper->getInfoDetail(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/getallreportsmsxemail/{page:[0-9]+}")
   */
  function getallreportsmsxemailAction($page){
    try {
      $json = $this->request->getRawBody();
      $arrayData = json_decode($json, true);

      if (!$arrayData) {
          throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $this->set_json_response($wrapper->getAllReportSmsxEmail($page,$arrayData), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
    }
  }
  
  /**
   * 
   * @Post("/getdatasmschannel/{page:[0-9]+}")
   */
  public function getdatasmschannelAction($page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setSearch($data);
      $wrapper->setPage($page);
      return $this->set_json_response($wrapper->getDataSmschannel(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  
  /**
   * 
   * @Post("/getdatasmsbydestinataries/{page:[0-9]+}")
   */
  public function getdatasmsbydestinatariesAction($page) {
    
    try{
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      $wrapper->setPage($page);
      return $this->set_json_response($wrapper->getSmsByDestinataries($data->nameCampaign,$data->phoneNumber,$data->dateInitial,$data->dateEnd,$data->type), 200);  
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getting SMS by destinataries... {$ex}");
      return $this->set_json_response(array('message' => "Ha ocurrido un error, contacte al administrador"), 500);
    }
  }
  
  /**
   * 
   * @Post("/downloadreportsmsbydestinataries")
   */
  
  public function downloadreportsmsbydestinatariesAction(){
      try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $this->set_json_response($wrapper->downloadRepSmsByDestinataries($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while downloading report sms by destinataries... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while downloading report sms by destinataries... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * @Post("/reportmail")
   */
  public function reportmailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);
      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wrapper = new \Sigmamovil\Wrapper\ReportWrapper();
      return $this->set_json_response($wrapper->findReportmail($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("InvalidArgumentException receivedsms ... {$ex->getMessage()} --> \n {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception receivedsms ... {$ex->getMessage()} --> \n {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
