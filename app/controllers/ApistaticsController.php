<?php

/**
 * Description of ApistaticsController
 * @RoutePrefix("/api/statics")
 * @author desarrollo3
 */
class ApistaticsController extends \ControllerBase {

  /**
   * 
   * @Get("/getallinfomail/{idMail:[0-9]+}/{type:[0-9]+}")
   * type id
   * 1 complete
   * 2 summary
   * 0 else
   */
  public function getallinfomailAction($idMail, $type) {

    try {
                    
      if(empty($idMail)){
        throw new \InvalidArgumentException("El idMail está vacío");       
      }      
      
      $mail = \Mail::findFirst(array(
                'conditions' => 'idMail = ?0 AND deleted = 0',
                'bind' => array(0 => $idMail)
      ));

      if (!$mail) {
        throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo");
      }
                
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->getAllInfoMail($type);
//      $wraper->getAllOpen();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/infoopen/{idmail:[0-9]+}/{page:[0-9]+}")
   */
  public function infoopenAction($idMail, $page) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setPage($page);
      $wraper->staticsOpen();
//      $wraper->getAllOpen();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/infoclic/{page:[0-9]+}")
   */
  public function infoclicAction($idMail) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->staticsClic();
//      $wraper->getAllOpen();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/infounsuscribed/{page:[0-9]+}")
   */
  public function infounsuscribedAction($idMail) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->staticsUnsuscribed();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/infobounced/{page:[0-9]+}")
   */
  public function infobouncedAction($idMail) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      return $this->set_json_response($wraper->staticsBounced(), 200);
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
   * @Get("/infospam/{page:[0-9]+}")
   */
  public function infospamAction($idMail) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->staticsSpam();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Post("/datainfo/{idmail:[0-9]+}/{page:[0-9]+}")
   */
  public function datainfoAction($idMail, $page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setPage($page);
      $wraper->setType($data->route);
      $wraper->setStringSearch($data->filters);
      $wraper->setTypeFilter($data->type);
      $wraper->dataInfo();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Post("/datainfoclic/{idmail:[0-9]+}/{page:[0-9]+}")
   */
  public function datainfoclicAction($idMail, $page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setPage($page);
      $wraper->setStringSearch($data->link);
      return $this->set_json_response($wraper->dataInfoClic(), 200);
//      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/getalldomain")
   */
  public function getalldomainAction() {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      return $this->set_json_response($wraper->getAllDomain(), 200);
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
   * @Get("/getallcategorybounced")
   */
  public function getallcategorybouncedAction() {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      return $this->set_json_response($wraper->getAllCategoryBounced(), 200);
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
   * @Get("/getinfosms/{idsms:[0-9]+}")
   */
  public function getinfosmsAction($idSms) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSms($idSms);
      $wraper->getInfoSms();
      $wraper->graphSms();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Post("/getdetailsms/{idsms:[0-9]+}/{page:[0-9]+}")
   */
  public function getdetailsmsAction($idSms, $page) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw); 
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSms($idSms);
      $wraper->setPage($page);
      $phone = "";      
      foreach(get_object_vars($data) as $value){
       if(!empty($value)){
        $phone = $value;        
       }  
      }
      $wraper->setSearchPhone($phone);
      $wraper->getDetailSms();
//      $wraper->modelDataSms();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Post("/reportstatics/{idMail:[0-9]+}/{title}")
   */
  public function reportstaticsAction($idMail, $title) {
    try {
      $contentsraw = $this->getRequestContent();
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setType($contentsraw);
      $wraper->reportStatics($title);
//      $wraper->modelDataSms();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/reportstaticssms/{idSms:[0-9]+}/{title}")
   */
  public function reportstaticssmsAction($idSms, $title) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSms($idSms);
      $wraper->getInfoSms();
      $wraper->getDetailSmsReport();
      $wraper->graphSms();
      $wraper->reportStaticsSms($title);
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Get("/getallinfosurvey/{idSurvey:[0-9]+}")
   */
  public function getallinfosurveyAction($idSurvey) {

    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSurvey($idSurvey);

      return $this->set_json_response($wraper->getAllInfoSurvey(), 200);
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
   * @Get("/reportstaticssurvey/{idSurvey:[0-9]+}/{title}")
   */
  public function reportstaticssurveyAction($idSurvey, $title) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSurvey($idSurvey);
      $wraper->reportStaticsSurvey($title);

      return $this->set_json_response(array(), 200);
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
   * @Post("/staticsmailsmessages")
   */
  public function getdataclicksmailsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->getDataClicksMail();
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mails or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/staticsmailstotalcamp/{valFilMail:[0-9]+}")
   */
  public function getdatatotalcampmailsAction($valFilMail) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      if ($data == null) {
        $data = new \stdClass();
      }
      $wraper->setSearch($data);
      if ($valFilMail == 1) {
        $wraper->getSentMailCamp();
      } else if ($valFilMail == 2) {
        $wraper->getDataClickLink();
      } else if ($valFilMail == 3) {
        $wraper->getDataTotalsLinksCamp();
      } else {
        $wraper->getDataClicksMail();
      }
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mails or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/staticsmstotalcamp/{valFilSms:[0-9]+}")
   */
  public function getdatatotalcampsmsAction($valFilSms) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setSearch($data);
      if ($valFilSms == 4) {
        $wraper->getTotalSms();
      } else if ($valFilSms == 5) {
        $wraper->getTotalSentSms();
      } else if ($valFilSms == 6) {
        $wraper->getSmsSentTotal();
      } else {
        $wraper->getDataClicksMail();
      }
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mails or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/staticssmssents/{valFilSms:[0-9]+}")
   */
  public function getsmssentsAction($valFilSms) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();

      if ($valFilSms == 4) {
        $wraper->getTotalSms();
      } else if ($valFilSms == 5) {
        $wraper->getTotalSentSms();
      } else if ($valFilSms == 6) {
        $wraper->getSmsSentTotal();
      } else {
        $wraper->getDataSmsSents();
      }
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding sms... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/camptotaldata")
   */
  public function getdatachargeinitialAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      //$wraper->getChargeInitialCamp();
      $wraper->getInfoInitialMonth();
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mails or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/datetabdata/{valDateTabata:[0-9]+}/{timespecific}/{category}/{valueoption}")
   */
  public function getdatetabdatamailAction($valDateTabata, $timespecific, $category, $valueoption) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      if ($category == 'm') {
        if ($valueoption == 1) {
          $wraper->getDataMailDate($valDateTabata, $timespecific);
        } else if ($valueoption == 2) {
          $wraper->getDateDateDay($valDateTabata, $timespecific);
        } else if ($valueoption == 3) {
          $wraper->staticOpenCamp($valDateTabata, $timespecific);
        } else if ($valueoption == 4) {
          $wraper->staticsUniqueClicks($valDateTabata, $timespecific);
        }
      } else if ($category == 's') {
        if ($valueoption == 5) {
          $wraper->getSmsSentsForDay($valDateTabata, $timespecific);
        } else if ($valueoption == 6) {
          $wraper->getCampSmsForDay($valDateTabata, $timespecific);
        }
      } else {
        var_dump('No entro al if');
        exit;
      }
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding mail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getallconfiguration/{idAutomaticcampaign:[0-9]+}")
   *
   */
  public function getallconfigurationAction($idAutomaticcampaign) {

    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->getAllAutomaticConfiguration($idAutomaticcampaign);
      return $this->set_json_response($wraper->getConfiguration(), 200);
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
   * @Get("/datetabdata2/{valDateTabata:[0-9]+}/{timespecific}/{category}/{valueoption}")
   */
  public function getdatetabdatamail2Action($valDateTabata, $timespecific, $category, $valueoption) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      if ($category == 'm') {
        if ($valueoption == 1) {
          $wraper->getDataMailDate2($valDateTabata, $timespecific);
        } else if ($valueoption == 2) {
          $wraper->getDateDateDay($valDateTabata, $timespecific);
        } else if ($valueoption == 3) {
          $wraper->staticOpenCamp($valDateTabata, $timespecific);
        } else if ($valueoption == 4) {
          $wraper->staticsUniqueClicks($valDateTabata, $timespecific);
        }
      } else if ($category == 's') {
        if ($valueoption == 5) {
          $wraper->getSmsSentsForDay($valDateTabata, $timespecific);
        } else if ($valueoption == 6) {
          $wraper->getCampSmsForDay($valDateTabata, $timespecific);
        }
      } else {
        var_dump('No entro al if');
        exit;
      }
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding mail... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms or messages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/reportstaticssmstwoway/{idSmstwoway:[0-9]+}/{title}")
   */
  public function reportstaticssmstwowayAction($idSmstwoway, $title) {
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdSmsTwoway($idSmstwoway);
      $wraper->getInfoSmsTwoway();
      $wraper->getDetailSmsTwoWayReport();
      $wraper->graphSmsTwoway();
      $wraper->reportStaticsSmsTwoway($title);
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/rolservices")
   */
  public function getrolservicesAction() {
    try {
      $wraper = new \Sigmamovil\Wrapper\SaxsWrapper();
      return $this->set_json_response($wraper->getall(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding services rol... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    }
  }

  /**
   * @Post("/getAutomaticCampaignByNode")
   */
  public function getautomaticcampaignbynodeAction() {

    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw, true);

      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      return $this->set_json_response($wraper->getacbynode($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while getting automatic campaign by node... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getting automatic campaign by node {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Get("/infobuzon/{idmail:[0-9]+}")
   */
  public function infobuzonAction($idMail){
    try {
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      return $this->set_json_response($wraper->staticsBuzon(), 200);
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
   * @Post("/datainfomail/{idmail:[0-9]+}/{page:[0-9]+}")
   */
  public function dataInfoMailAction($idMail, $page) {
    try {
     $mail = \Mail::findFirst(array(
                'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                'bind' => array(0 => $idMail, 1 =>(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
                ));
      if (!$mail) {
        throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo ");
      }
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setPage($page);
      $wraper->setType($data->route);
      $wraper->dataInfoMail();
      return $this->set_json_response($wraper->getStatics(), 200);
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
   * @Post("/datainfomaillote/{idmail:[0-9]+}/{page:[0-9]+}")
   */
  public function dataInfoMailLoteAction($idMail, $page) {
    try {
     $mail = \Mail::findFirst(array(
                'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                'bind' => array(0 => $idMail, 1 =>(int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
                ));
      if (!$mail) {
        throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo ");
      }
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wraper = new \Sigmamovil\Wrapper\StaticsWrapper();
      $wraper->setIdMail($idMail);
      $wraper->setPage($page);
      $wraper->setType($data->route);
      $wraper->dataInfoMail();
      return $this->set_json_response($wraper->getStatics(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
