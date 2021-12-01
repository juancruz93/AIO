<?php

use Phalcon\Logger\Adapter\File as FileAdapter;

class TrackController extends ControllerBase {

  public $arrayData = [];
  
  public function openautomatizationAction($parameters) {
    try {
      $this->db->begin();
      $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
      $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
      list($idLink, $idAutomatizationCampaignStep, $idContact) = $this->encoder->decodeLink('track/openautomatization', $parameters);
      $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
      $automaticStep = \AutomaticCampaignStep::findFirst(array("conditions" => "idContact = ?0 and idAutomaticCampaignStep = ?1", "bind" => array($idContact, $idAutomatizationCampaignStep)));
      $automaticConfiguration = $wrapper->getAutomaticConfiguration($automaticStep->idAutomaticCampaign);
      $automaticObj = new \Sigmamovil\General\Misc\AutomaticCampaignObj($automaticConfiguration->AutomaticCampaign, $automaticConfiguration);

      if (!$automaticStep) {
        throw new \Exception("Ocurrio un problema consultado el paso de la campaña con el id: {$idAutomatizationCampaignStep}");
      }

      if (!$automaticObj->validateDateCampaign()) {
        throw new \InvalidArgumentException("Ya se cumplió la fecha de la campaña.{$automaticStep->idAutomaticCampaign}");
      }

      if ($automaticStep->open == 0) {
        $automaticObj->openAutomatization($automaticStep);
      } else {
        $automaticObj->openAutomatization($automaticStep, false);
      }

      $automaticStepActual = $automaticObj->getAutomaticStepLast($idContact, $automaticStep->idAutomaticCampaign);

      if ($automaticStepActual->idNode != $automaticStep->idNode) {

        $connection = $automaticObj->searchConnection($automaticStepActual->idNode);
        $nodeOperator = $automaticObj->getNode($connection["source"]);

        $connectionOperator = $automaticObj->searchConnection($nodeOperator->id);
        $nodeOperatorSource = $automaticObj->getNode($connectionOperator["source"]);
        if ($nodeOperatorSource->id != $automaticStep->idNode) {
          throw new Exception("El id del nodo se excede al siguiente paso");
        }

        $negation = json_decode($automaticObj->searchNegation($nodeOperator->id));
        $date = $automaticObj->getDataTime($nodeOperator);
        $beforeStep = $automaticObj->getBeforeStep($nodeOperator);

        if ($automaticStepActual->negation == 1 && ($automaticStepActual->beforeStep == "no open" || $automaticStepActual->beforeStep == "open")) {
          if ($automaticStepActual->scheduleDate > date('Y-m-d H:i')) {
            for ($i = 0; $i < count($negation); $i++) {
              if ($negation[$i]->dest->class == "success") {
                if (!$automaticObj->getAutomaticStepNode($idContact, $automaticStepActual->idAutomaticCampaign, $negation[$i]->dest->idNode)) {
                  $automaticObj->uptStatusStep($automaticStepActual, "canceled");
                  $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
                  $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date);
                }
              }
            }
            $img = __DIR__ . '/../../public/images/transparent.png';
            $this->response->setHeader("Content-Type", "image/png");
            $this->view->disable();
            $this->db->commit();
            return $this->response->setContent(file_get_contents($img));
          }
        }

        if ($automaticStepActual->beforeStep == "no open") {
          if ($automaticStepActual->scheduleDate > date('Y-m-d H:i')) {
            for ($i = 0; $i < count($negation); $i++) {
              if ($negation[$i]->dest->class == "negation") {
                if (!$automaticObj->getAutomaticStepNode($idContact, $automaticStepActual->idAutomaticCampaign, $negation[$i]->dest->idNode)) {
                  $automaticObj->uptStatusStep($automaticStepActual, "canceled");
                  $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
                  $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date, 1);
                }
              }
            }
            $img = __DIR__ . '/../../public/images/transparent.png';
            $this->response->setHeader("Content-Type", "image/png");
            $this->view->disable();
            $this->db->commit();
            return $this->response->setContent(file_get_contents($img));
          }
        }
      } else {

        $connection = $automaticObj->searchConnection($automaticStep->idNode);
        $nodeOperator = $automaticObj->getNode($connection["dest"]);

        if ($nodeOperator->method == "actions") {
          $date = $automaticObj->getDataActions($nodeOperator);
        } else {
          $date = $automaticObj->getDataTime($nodeOperator);
        }
        $beforeStep = $automaticObj->getBeforeStep($nodeOperator);
        if ($automaticStepActual->idNode == $automaticStep->idNode) {
          if ($beforeStep == "open") {
            $node = $automaticObj->getNode($automaticStep->idNode);
            $connection = $automaticObj->searchConnection($node->id);
            if (isset($connection['dest'])) {
              $nextOperator = $automaticObj->getNode($connection["dest"]);
              $beforeStep = $automaticObj->getBeforeStep($nextOperator);
              if ($nextOperator->method == "actions") {
                $negation = json_decode($automaticObj->searchNegation($nextOperator->id));
                for ($i = 0; $i < count($negation); $i++) {
                  if ($negation[$i]->dest->class == "negation") {
                    $nodeNegation = new \stdClass();
                    $nodeNegation->idNode = $negation[$i]->dest->idNode;
                    $nodeNegation->node = $automaticObj->getNode($negation[$i]->dest->idNode);
                    $nodeNegation->date = $automaticObj->getDataTime($negation[$i]->dest);
                    $nodeNegation->beforeStep = $beforeStep;
                  } else {
//              if ($beforeStep == "no clic" || $beforeStep == "no open") {
                    $nodeSuccess = new \stdClass();
                    $nodeSuccess->idNode = $negation[$i]->dest->idNode;
                    $nodeSuccess->node = $automaticObj->getNode($negation[$i]->dest->idNode);
                    $nodeSuccess->date = $automaticObj->getDataTime($nextOperator);
                    $nodeSuccess->beforeStep = $beforeStep;
//              }
                  }
                }
                if (isset($nodeSuccess) || isset($nodeNegation)) {
                  if (isset($nodeSuccess)) {
                    if ($nodeSuccess->beforeStep == "no open" || $nodeSuccess->beforeStep == "no clic") {
                      $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
                    } else {
                      if (isset($nodeNegation)) {
                        $automaticObj->insNewStep($idContact, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
                      } else {
                        $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
                      }
                    }
                  } else {
                    if (isset($nodeNegation)) {
                      $automaticObj->insNewStep($idContact, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
                    }
                  }
                }
              } else if ($nextOperator->method == "time") {
                $connection = $automaticObj->searchConnection($nextOperator->id);
                $node = $automaticObj->getNode($connection["dest"]);
                $nodeSuccess = new \stdClass();
                $nodeSuccess->idNode = $connection["dest"];
                $nodeSuccess->node = $node;
                $nodeSuccess->date = $automaticObj->getDataTime($nextOperator);
                $nodeSuccess->beforeStep = $beforeStep;
                $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
              }
            }
          }
        }
      }

      $img = __DIR__ . '/../../public/images/transparent.png';
      $this->response->setHeader("Content-Type", "image/png");
      $this->view->disable();
      $this->db->commit();
      return $this->response->setContent(file_get_contents($img));
    } catch (\InvalidArgumentException $e) {
      $this->logger->log("Error ... {$e->getMessage()}");
      $this->db->rollback();
    } catch (Exception $ex) {
      $this->logger->log("Error ... {$ex->getMessage()}");
      $this->db->rollback();
    }
  }

  public function clickautomatizationAction($parameters) {
    try {
      $this->db->begin();
      $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
      $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
      list($v, $idLink, $idAutomatizationCampaignStep, $idContact) = $this->encoder->decodeLink('track/clickopenautomatization', $parameters);

      $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
      $automaticStep = \AutomaticCampaignStep::findFirst(array("conditions" => "idContact = ?0 and idAutomaticCampaignStep = ?1", "bind" => array($idContact, $idAutomatizationCampaignStep)));
      $automaticConfiguration = $wrapper->getAutomaticConfiguration($automaticStep->idAutomaticCampaign);
      $automaticObj = new \Sigmamovil\General\Misc\AutomaticCampaignObj($automaticConfiguration->AutomaticCampaign, $automaticConfiguration);

      if (!$automaticStep) {
        throw new \Exception("Ocurrio un problema consultado el paso de la campaña con el id: {$idAutomatizationCampaignStep}");
      }

      if ($automaticStep->uniqueClicks == 0) {
        $automaticObj->clickAutomatization($automaticStep);
      } else {
        $automaticObj->clickAutomatization($automaticStep, false);
      }

      $maillink = $automaticObj->getobjmaillink($idLink);
      if (!$maillink) {
        return $this->response->redirect('error/link');
      }

      $automaticObj->clickAcxl($maillink->idMail_link, $idAutomatizationCampaignStep);
      $automaticStepActual = $automaticObj->getAutomaticStepLast($idContact, $automaticStep->idAutomaticCampaign);

      if ($automaticStepActual->idNode != $automaticStep->idNode) {

        if ($automaticObj->getStatusStep($automaticStepActual) != "scheduled") {
          return $this->response->redirect($maillink->link, true);
        }


        $connection = $automaticObj->searchConnection($automaticStepActual->idNode);
        $nodeOperator = $automaticObj->getNode($connection["source"]);

        $connectionOperator = $automaticObj->searchConnection($nodeOperator->id);
        $nodeOperatorSource = $automaticObj->getNode($connectionOperator["source"]);
        if ($nodeOperatorSource->id != $automaticStep->idNode) {
          throw new Exception("El id del nodo se excede al siguiente paso");
        }

        if ($nodeOperator->method == "actions") {
          $date = $automaticObj->getDataActions($nodeOperator);
        } else {
          $date = $automaticObj->getDataTime($nodeOperator);
        }
        $beforeStep = $automaticObj->getBeforeStep($nodeOperator);

        $negation = json_decode($automaticObj->searchNegation($nodeOperator->id));

        if ($automaticStepActual->negation == 1 && ($automaticStepActual->beforeStep == "no clic" || $automaticStepActual->beforeStep == "clic")) {
          $clic = $automaticObj->searchLink($maillink->link);
          if (is_numeric($clic)) {
            if ($automaticStepActual->scheduleDate > date('Y-m-d H:i')) {
              for ($i = 0; $i < count($negation); $i++) {
                if ($negation[$i]->dest->class == "success") {
                  if (!$automaticObj->getAutomaticStepNode($idContact, $automaticStepActual->idAutomaticCampaign, $negation[$i]->dest->idNode)) {
                    $automaticObj->uptStatusStep($automaticStepActual, "canceled");
                    $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
                    $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date);
                  }
                }
              }
              $this->db->commit();
              return $this->response->redirect($maillink->link, true);
            }
          }
        }

        if ($automaticStepActual->beforeStep == "no clic") {
          $clic = $automaticObj->searchLink($maillink->link);
          if (is_numeric($clic)) {
            if ($automaticStepActual->scheduleDate > date('Y-m-d H:i')) {
              for ($i = 0; $i < count($negation); $i++) {
                if ($negation[$i]->dest->class == "negation") {
                  if (!$automaticObj->getAutomaticStepNode($idContact, $automaticStepActual->idAutomaticCampaign, $negation[$i]->dest->idNode)) {
                    $automaticObj->uptStatusStep($automaticStepActual, "canceled");
                    $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
                    $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date, 1);
                  }
                }
              }
              $this->db->commit();
              return $this->response->redirect($maillink->link, true);
            }
          }
        }
      } else {

        $connection = $automaticObj->searchConnection($automaticStep->idNode);
        $nodeOperator = $automaticObj->getNode($connection["dest"]);

        if ($nodeOperator->method == "actions") {
          $date = $automaticObj->getDataActions($nodeOperator);
        } else {
          $date = $automaticObj->getDataTime($nodeOperator);
        }
        $beforeStep = $automaticObj->getBeforeStep($nodeOperator);

        if ($automaticStepActual->idNode == $automaticStep->idNode) {
          if ($beforeStep == "clic") {
            $clic = $automaticObj->searchLink($maillink->link);
            if (is_numeric($clic)) {
              $automaticObj->uptStatusStep($automaticStep, "sent");
              $node = $automaticObj->getNode($automaticStep->idNode);
              $connection = $automaticObj->searchConnection($node->id);
              if (isset($connection['dest'])) {
                $nextOperator = $automaticObj->getNode($connection["dest"]);
                $beforeStep = $automaticObj->getBeforeStep($nextOperator);
                if ($nextOperator->method == "actions") {
                  $negation = json_decode($automaticObj->searchNegation($nextOperator->id));
                  for ($i = 0; $i < count($negation); $i++) {
                    if ($negation[$i]->dest->class == "negation") {
                      $nodeNegation = new \stdClass();
                      $nodeNegation->idNode = $negation[$i]->dest->idNode;
                      $nodeNegation->node = $automaticObj->getNode($negation[$i]->dest->idNode);
                      $nodeNegation->date = $automaticObj->getDataTime($negation[$i]->dest);
                      $nodeNegation->beforeStep = $beforeStep;
                    } else {
                      $nodeSuccess = new \stdClass();
                      $nodeSuccess->idNode = $negation[$i]->dest->idNode;
                      $nodeSuccess->node = $automaticObj->getNode($negation[$i]->dest->idNode);
                      $nodeSuccess->date = $automaticObj->getDataTime($nextOperator);
                      $nodeSuccess->beforeStep = $beforeStep;
                    }
                  }
                  if (isset($nodeSuccess) || isset($nodeNegation)) {
                    if (isset($nodeSuccess)) {
                      if ($nodeSuccess->beforeStep == "no open" || $nodeSuccess->beforeStep == "no clic") {
                        $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
                      } else {
                        if (isset($nodeNegation)) {
                          $automaticObj->insNewStep($idContact, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
                        } else {
                          $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
                        }
                      }
                    } else {
                      if (isset($nodeNegation)) {
                        $automaticObj->insNewStep($idContact, $nodeNegation->idNode, $nodeNegation->node, $nodeNegation->beforeStep, $nodeNegation->date, 1);
                      }
                    }
                  }
                } else if ($nextOperator->method == "time") {
                  $connection = $automaticObj->searchConnection($nextOperator->id);
                  $node = $automaticObj->getNode($connection["dest"]);
                  $nodeSuccess = new \stdClass();
                  $nodeSuccess->idNode = $connection["dest"];
                  $nodeSuccess->node = $node;
                  $nodeSuccess->date = $automaticObj->getDataTime($nextOperator);
                  $nodeSuccess->beforeStep = $beforeStep;
                  $automaticObj->insNewStep($idContact, $nodeSuccess->idNode, $nodeSuccess->node, $nodeSuccess->beforeStep, $nodeSuccess->date);
                }
              }
            }
          }
        }
      }
      $this->db->commit();
      return $this->response->redirect($maillink->link, true);
    } catch (\InvalidArgumentException $e) {
      $this->logger->log("InvalidArgumentException clickautomatization ... {$e->getMessage()}");
    } catch (Exception $ex) {
      $this->logger->log("Exception  clickautomatization... {$ex->getMessage()}");
    }
  }

  public function openAction($parameters) {
    $this->registerOpen($parameters);
    $this->logger->log("Message: {$parameters}");
    try {

      $this->db->begin();
      $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
      $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
      list($idLink, $idMail, $idContact) = $this->encoder->decodeLink('track/open', $parameters);
      //Logs de OpenAction
//        $logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
        $customLogger = new \TrackLog();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->typeName = "OpenActionMethod";
        $customLogger->idMail = $idMail;
        $customLogger->idContact = $idContact;
        $customLogger->idLink = $idLink;
        $customLogger->globalDescription = $parameters;
//        $customLogger->globalLogDescription = $logger->log("Parameters on OpenAction: {$parameters}");
//        $customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
//        $customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
      //
      $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
      $mail = $wrapper->getMail($idMail);
      if (!$mail) {
        throw new \Exception("Ocurrio un problema consultado el mail {$idMail}");
      }
      if ($mail->type == "automatic") {
        //$this->openAutomatic($mail, $idContact);
      }

      $this->openMail($idMail, $idContact);

      $img = __DIR__ . '/../../public/images/transparent.png';
      $this->response->setHeader("Content-Type", "image/png");
      $this->view->disable();
      $this->db->commit();
      return $this->response->setContent(file_get_contents($img));
    } catch (\InvalidArgumentException $e) {
      $this->logger->log("Error ... {$e}");
      $this->db->rollback();
    } catch (Exception $ex) {
      $this->logger->log("Error ... {$ex}");
      $this->db->rollback();
    }
  }

  public function openAutomatic($mail, $idContact) {
    $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
    $automatic = $mail->AutomaticCampaign;
    $automaticConfiguration = $wrapper->getAutomaticConfiguration($automatic->idAutomaticCampaign);
    $automaticObj = new \Sigmamovil\General\Misc\AutomaticCampaignObj($automatic, $automaticConfiguration, $mail);
    $automaticStep = $automaticObj->getAutomaticStep($idContact, $automatic->idAutomaticCampaign);
    if (!$automaticObj->validateDateCampaign()) {
      throw new \InvalidArgumentException("Ya se cumplió la fecha de la campaña.");
    }
    if (!$automaticStep) {
      $node = $automaticObj->getNode(0);
      $connection = $automaticObj->searchConnection($node->id);
      $secondNode = $automaticObj->getNode($connection["dest"]);
      $connectionOperator = $automaticObj->searchConnection($secondNode->id);
      $firstOperator = $automaticObj->getNode($connectionOperator["dest"]);
      $beforeStep = $automaticObj->getBeforeStep($firstOperator);

      $connectionNodeNow = $automaticObj->searchConnection($firstOperator->id);
      $nextNode = $automaticObj->getNode($connectionNodeNow["dest"]);

//      $connectionNextOperator = $automaticObj->searchConnection($nextNode->id);
//      $nextOperator = $automaticObj->getNode($connectionNextOperator["dest"]);

      if ($firstOperator->method == "actions") {
        $date = $automaticObj->getDataActions($firstOperator);
      } else {
        $date = $automaticObj->getDataTime($firstOperator);
      }
      if ($beforeStep == "open") {
        $automaticObj->insNewStep($idContact, $nextNode->id, $nextNode, $beforeStep, $date);
      }
    } else {
//      if ($automaticStep->open == 0) {
//        $automaticObj->openAutomatization($automaticStep);
//      } else {
//        $automaticObj->openAutomatization($automaticStep, false);
//      }

      if ($automaticObj->getStatusStep($automaticStep) != "scheduled") {
        return false;
      }

      $connection = $automaticObj->searchConnection($automaticStep->idNode);
      $nodeOperator = $automaticObj->getNode($connection["source"]);
      $negation = json_decode($automaticObj->searchNegation($nodeOperator->id));
      $date = $automaticObj->getDataTime($nodeOperator);
      $beforeStep = $automaticObj->getBeforeStep($nodeOperator);
      if ($automaticStep->negation == 1 && ($beforeStep == "no open" || $beforeStep == "open")) {
        if ($automaticStep->scheduleDate > date('Y-m-d H:i')) {
          $automaticObj->uptStatusStep($automaticStep, "canceled");
          for ($i = 0; $i < count($negation); $i++) {
            if ($negation[$i]->dest->class == "success") {
              $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
              $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date);
            }
          }
        }
      }

      if ($automaticStep->beforeStep == "no open") {
        if ($automaticStep->scheduleDate > date('Y-m-d H:i')) {
          $automaticObj->uptStatusStep($automaticStep, "canceled");
          for ($i = 0; $i < count($negation); $i++) {
            if ($negation[$i]->dest->class == "negation") {
              $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
              $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date, 1);
            }
          }
        }
      }
    }
  }

  public function openMail($idMail, $idContact) {
    $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
    $new = $wrapper->openContact($idMail, $idContact);
    if ($new) {
      $wrapper->openMail($idMail);
    } else {
      $wrapper->openMail($idMail, false);
    }
    unset($idLink);
    unset($idMail);
    unset($idContact);
  }

  public function mtaeventAction() {
    
    $request = $this->getRequestContent();
    //$this->logger->log(print_r($request, true));
    if (trim($request) === '' || $request == null) {
      $this->logger->log('No hay contenido, no se registró evento de mta (Rebote, Spam)');
      return false;
    }

    //$this->logger->log(print_r($request, true));
    $contents = json_decode($request, true);
    $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();

    foreach ($contents as $content) {
      if(!empty($content['click_tracking_id'])){
        $arrIds = substr($content['click_tracking_id'], 2);
        $arrIds = explode("x", $arrIds);
      }else{
        if(!empty($content['sendid'])){
          $idMail = substr($content['sendid'], 10);
          $where = array("idMail" => (String) $idMail, "email" => $content['email']);
          $mxc = \Mxc::findFirst([$where]);
          $arrIds[0] = (String) $idMail;
          $arrIds[1] = $mxc->idContact;
        }else{
          $arrIds[0] = null;
          $arrIds[1] = null;
        }
      }
      
      $date = $content['event_time'];

      if($arrIds[0] == null || $arrIds[0] == 'proyectos@sigmamovil.com' || $arrIds[0] == 'emanzano@panturismo.com.co'){
        echo "ok";
        exit;
      }

      try {
        $this->logger->log("***** ENTRE A SWITCH mtaeventAction ".time());
        switch ($content['event_type']) {
          case 'bounce_all':
            $wrapper->trackSoftBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
          case 'bounce_bad_address':
            $wrapper->trackHardBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
          case 'scomp':
            $wrapper->trackScompBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
        }
        $this->logger->log("***** SALI A SWITCH mtaeventAction ".time());
      } catch (Exception $ex) {
        $this->logger->log("Error  Exception ... {$ex}");
      } catch (\InvalidArgumentException $e) {
        $this->logger->log("Error InvalidArgumentException ... {$e}");
      }
    }
  }
  
  public function mtaexampleAction() {
    
    //$request = $this->getRequestContent();
  $request = '[{"id":"85627","event_type":"bounce_all","event_time":"1508961761","email":"paola.munoz@adecco.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"--1058487f-6fb9-4f7c-9f30-49dd7ed493c9\nContent-Type: multipart\/alternative; differences=Content-Type;\n\tboundary=\"cce4218b-7273-4016-834a-0eaa978d2c88\"\n\n--cce4218b-7273-4016-834a-0eaa978d2c88\nContent-Type: text\/plain; charset=\"us-ascii\"\nContent-Transfer-En","click_url":null,"click_tracking_id":"ac4385x5026821x4385"},{"id":"85628","event_type":"bounce_bad_address","event_time":"1508961761","email":"paola.munoz@adecco.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"--1058487f-6fb9-4f7c-9f30-49dd7ed493c9\nContent-Type: multipart\/alternative; differences=Content-Type;\n\tboundary=\"cce4218b-7273-4016-834a-0eaa978d2c88\"\n\n--cce4218b-7273-4016-834a-0eaa978d2c88\nContent-Type: text\/plain; charset=\"us-ascii\"\nContent-Transfer-En","click_url":null,"click_tracking_id":"ac4385x5026821x4385"},{"id":"85636","event_type":"bounce_all","event_time":"1508961777","email":"mimiyulieth17_07@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4410","bounce_type":"h","bounce_code":"10","bounce_text":"104.44.194.235 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 104.44.194.235.","click_url":null,"click_tracking_id":"ac4410x5132446x4410"},{"id":"85637","event_type":"bounce_bad_address","event_time":"1508961777","email":"mimiyulieth17_07@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4410","bounce_type":"h","bounce_code":"10","bounce_text":"104.44.194.235 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 104.44.194.235.","click_url":null,"click_tracking_id":"ac4410x5132446x4410"},{"id":"85648","event_type":"bounce_all","event_time":"1508961815","email":"jaimealonso1@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.38.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [BL2NAM02FT013.eop-nam02.prod.protection.outlook.com]\nGiving up on 104.47.38.33.","click_url":null,"click_tracking_id":"ac4385x5028052x4385"},{"id":"85649","event_type":"bounce_bad_address","event_time":"1508961815","email":"jaimealonso1@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.38.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [BL2NAM02FT013.eop-nam02.prod.protection.outlook.com]\nGiving up on 104.47.38.33.","click_url":null,"click_tracking_id":"ac4385x5028052x4385"},{"id":"85652","event_type":"bounce_all","event_time":"1508961861","email":"edwardval7@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.54.188.110 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.54.188.110.","click_url":null,"click_tracking_id":"ac4385x5027175x4385"},{"id":"85653","event_type":"bounce_bad_address","event_time":"1508961861","email":"edwardval7@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.54.188.110 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.54.188.110.","click_url":null,"click_tracking_id":"ac4385x5027175x4385"},{"id":"85650","event_type":"bounce_all","event_time":"1508961873","email":"jazo1980@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.54.188.110 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.54.188.110.","click_url":null,"click_tracking_id":"ac4385x5025313x4385"},{"id":"85651","event_type":"bounce_bad_address","event_time":"1508961873","email":"jazo1980@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.54.188.110 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.54.188.110.","click_url":null,"click_tracking_id":"ac4385x5025313x4385"},{"id":"85654","event_type":"bounce_all","event_time":"1508961889","email":"servicioalcliente@co.g4s.com","listid":"t0ai257","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4142","bounce_type":"o","bounce_code":"40","bounce_text":"--94eb2c123f1e5f3d1f055c6491f2\nContent-Type: multipart\/related; boundary=\"94eb2c123f1e5f3d9a055c6491f3\"\n\n--94eb2c123f1e5f3d9a055c6491f3\nContent-Type: multipart\/alternative; boundary=\"94eb2c123f1e5f3da0055c6491f4\"\n\n--94eb2c123f1e5f3da0055c6491f4\nContent-Ty","click_url":null,"click_tracking_id":"ac4142x3130248x4142"},{"id":"85671","event_type":"bounce_all","event_time":"1508962048","email":"maopilatos2101@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"This is a MIME-formatted message.  \nPortions of this message may be unreadable without a MIME-capable mail program.\n\n--9B095B5ADSN=_01D340F5607FE73E002B01C4SNT004?MC8F16.ho\nContent-Type: text\/plain; charset=unicode-1-1-utf-7\n\nThis is an automatically gene","click_url":null,"click_tracking_id":"ac4385x5027435x4385"},{"id":"85672","event_type":"bounce_bad_address","event_time":"1508962048","email":"maopilatos2101@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"This is a MIME-formatted message.  \nPortions of this message may be unreadable without a MIME-capable mail program.\n\n--9B095B5ADSN=_01D340F5607FE73E002B01C4SNT004?MC8F16.ho\nContent-Type: text\/plain; charset=unicode-1-1-utf-7\n\nThis is an automatically gene","click_url":null,"click_tracking_id":"ac4385x5027435x4385"},{"id":"85668","event_type":"bounce_all","event_time":"1508962053","email":"stevenvargas14450@gmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"74.125.141.26 does not like recipient.\nRemote host said: 550-5.1.1 The email account that you tried to reach does not exist. Please try\n550-5.1.1 double-checking the recipients email address for typos or\n550-5.1.1 unnecessary spaces. Learn more at\n550 5.","click_url":null,"click_tracking_id":"ac4385x5027347x4385"},{"id":"85669","event_type":"bounce_bad_address","event_time":"1508962053","email":"stevenvargas14450@gmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"74.125.141.26 does not like recipient.\nRemote host said: 550-5.1.1 The email account that you tried to reach does not exist. Please try\n550-5.1.1 double-checking the recipients email address for typos or\n550-5.1.1 unnecessary spaces. Learn more at\n550 5.","click_url":null,"click_tracking_id":"ac4385x5027347x4385"},{"id":"85680","event_type":"bounce_all","event_time":"1508962169","email":"danigo0212@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.40.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [CO1NAM03FT035.eop-NAM03.prod.protection.outlook.com]\nGiving up on 104.47.40.33.","click_url":null,"click_tracking_id":"ac4385x5027576x4385"},{"id":"85681","event_type":"bounce_bad_address","event_time":"1508962169","email":"danigo0212@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.40.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [CO1NAM03FT035.eop-NAM03.prod.protection.outlook.com]\nGiving up on 104.47.40.33.","click_url":null,"click_tracking_id":"ac4385x5027576x4385"},{"id":"85678","event_type":"bounce_all","event_time":"1508962194","email":"rugaeli@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.92.152 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.92.152.","click_url":null,"click_tracking_id":"ac4385x5026747x4385"},{"id":"85679","event_type":"bounce_bad_address","event_time":"1508962194","email":"rugaeli@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.92.152 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.92.152.","click_url":null,"click_tracking_id":"ac4385x5026747x4385"},{"id":"85684","event_type":"bounce_all","event_time":"1508962304","email":"toaa04@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4410","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.37.72 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.37.72.","click_url":null,"click_tracking_id":"ac4410x5132148x4410"},{"id":"85685","event_type":"bounce_bad_address","event_time":"1508962304","email":"toaa04@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4410","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.37.72 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.37.72.","click_url":null,"click_tracking_id":"ac4410x5132148x4410"},{"id":"85687","event_type":"bounce_all","event_time":"1508962304","email":"alejandrodiaz.64@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.37.72 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.37.72.","click_url":null,"click_tracking_id":"ac4385x5024817x4385"},{"id":"85688","event_type":"bounce_bad_address","event_time":"1508962304","email":"alejandrodiaz.64@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"65.55.37.72 does not like recipient.\nRemote host said: 550 Requested action not taken: mailbox unavailable\nGiving up on 65.55.37.72.","click_url":null,"click_tracking_id":"ac4385x5024817x4385"},{"id":"85682","event_type":"bounce_all","event_time":"1508962357","email":"tesorito1969@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.4.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [AM5EUR02FT058.eop-EUR02.prod.protection.outlook.com]\nGiving up on 104.47.4.33.","click_url":null,"click_tracking_id":"ac4385x5026927x4385"},{"id":"85683","event_type":"bounce_bad_address","event_time":"1508962357","email":"tesorito1969@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.4.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [AM5EUR02FT058.eop-EUR02.prod.protection.outlook.com]\nGiving up on 104.47.4.33.","click_url":null,"click_tracking_id":"ac4385x5026927x4385"},{"id":"85689","event_type":"bounce_all","event_time":"1508962384","email":"epalomino@ingeniopichichi.com","listid":"t0ai93","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4322","bounce_type":"s","bounce_code":"21","bounce_text":"This is a MIME-encapsulated message.\n\n--DF785B6E48.1508960539\/correo.ingeniopichichi.com\nContent-Description: Notification\nContent-Type: text\/plain; charset=us-ascii\n\nThis is the mail system at host correo.ingeniopichichi.com.\n\nIm sorry to have to inform","click_url":null,"click_tracking_id":"ac4322x1574580x4322"},{"id":"85693","event_type":"bounce_all","event_time":"1508962417","email":"danielarroyo1990@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.46.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [BN3NAM04FT028.eop-NAM04.prod.protection.outlook.com]\nGiving up on 104.47.46.33.","click_url":null,"click_tracking_id":"ac4385x5026687x4385"},{"id":"85694","event_type":"bounce_bad_address","event_time":"1508962417","email":"danielarroyo1990@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.46.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [BN3NAM04FT028.eop-NAM04.prod.protection.outlook.com]\nGiving up on 104.47.46.33.","click_url":null,"click_tracking_id":"ac4385x5026687x4385"},{"id":"85696","event_type":"bounce_all","event_time":"1508962444","email":"lbejarano@ingeniopichichi.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4385","bounce_type":"s","bounce_code":"21","bounce_text":"This is a MIME-encapsulated message.\n\n--0A8C0B6E4B.1508960599\/correo.ingeniopichichi.com\nContent-Description: Notification\nContent-Type: text\/plain; charset=us-ascii\n\nThis is the mail system at host correo.ingeniopichichi.com.\n\nIm sorry to have to inform","click_url":null,"click_tracking_id":"ac4385x5025279x4385"},{"id":"85714","event_type":"bounce_all","event_time":"1508962691","email":"jrbaez2016@hotmail.com","listid":"t0ai256","list_name":null,"list_label":null,"sendid":"SIGMA_NEWEMKTG_DEVEL0AI4410","bounce_type":"h","bounce_code":"10","bounce_text":"104.47.33.33 does not like recipient.\nRemote host said: 550 5.5.0 Requested action not taken: mailbox unavailable. [BN3NAM01FT064.eop-nam01.prod.protection.outlook.com]\nGiving up on 104.47.33.33.","click_url":null,"click_tracking_id":"ac4410x5131692x4410"}]';
    
    if (trim($request) === '' || $request == null) {
      $this->logger->log('No hay contenido, no se registró evento de mta (Rebote, Spam)');
      return false;
    }
  
    $contents = json_decode($request, true);
  //var_dump($contents);exit();
    $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();

    foreach ($contents as $content) {
      $arrIds = substr($content['click_tracking_id'], 2);
      $arrIds = explode("x", $arrIds);
      $date = $content['event_time'];
         echo $arrIds[0]." -- ".$arrIds[1]."<br>";
      try {
    
        switch ($content['event_type']) {
          case 'bounce_all':
            $wrapper->trackSoftBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
          case 'bounce_bad_address':
            $wrapper->trackHardBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
          case 'scomp':
            $wrapper->trackScompBounceEvent($arrIds[0], $arrIds[1], $content, $date);
            break;
        }
      } catch (Exception $ex) {
        $this->logger->log("Error  Exception ... {$ex}");
      } catch (\InvalidArgumentException $e) {
        $this->logger->log("Error InvalidArgumentException ... {$e}");
      }
    }
  }

  public function clickAction($parameters) {
    try {
      $this->db->begin();
      $linkEncoder = new Sigmamovil\General\Links\ParametersEncoder();
      $linkEncoder->setBaseUri(Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true));
      $idenfifiers = $linkEncoder->decodeLink('track/click', $parameters);
      list($v, $idLink, $idMail, $idContact) = $idenfifiers;
      //Logs de Click Action
//        $logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
        $customLogger = new \TrackLog();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->idContact = $idContact;
        $customLogger->idMail = $idMail;
        $customLogger->idLink = $idLink;
        $customLogger->typeName = "clickActionMethod";
        $customLogger->globalDescription = $parameters;
        $customLogger->detailedDescription = $idenfifiers;
//        $customLogger->globalLogDescription = $logger->log("Parameters on ClickAction: {$parameters}");
//        $customLogger->detailedLogDescription = $logger->log("Identifiers on ClickAction: {$idenfifiers}");
//        $customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
//        $customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
      //
      $trackingObj = new TrackingObject();
      $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
      $mail = $wrapper->getMail($idMail);
      if (!$mail) {
        throw new \Exception("Ocurrio un problema consultado el mail {$idMail}");
      }

      //if ($mail->type == "automatic") {
//        $automatic = $mail->AutomaticCampaign;
//        $automaticConfiguration = $wrapper->getAutomaticConfiguration($automatic->idAutomaticCampaign);
//        $automaticObj = new \Sigmamovil\General\Misc\AutomaticCampaignObj($automatic, $automaticConfiguration, $mail);
//        $automaticStep = $automaticObj->getAutomaticStep($automatic->idAutomaticCampaign);
//
//        if (!$automaticObj->validateDateCampaign()) {
//          throw new \InvalidArgumentException("Ya se cumplió la fecha de la campaña.");
//        }
//
//        if (!$automaticStep) {
//          $node = $automaticObj->getNode(0);
//          $connection = $automaticObj->searchConnection($node->id);
//          $secondNode = $automaticObj->getNode($connection["dest"]);
//          $connectionOperator = $automaticObj->searchConnection($secondNode->id);
//          $firstOperator = $automaticObj->getNode($connectionOperator["dest"]);
//          $beforeStep = $automaticObj->getBeforeStep($firstOperator);
//
//          $connectionNodeNow = $automaticObj->searchConnection($firstOperator->id);
//          $nextNode = $automaticObj->getNode($connectionNodeNow["dest"]);
//
//          if ($firstOperator->method == "actions") {
//            $date = $automaticObj->getDataActions($firstOperator);
//          } else {
//            $date = $automaticObj->getDataTime($firstOperator);
//          }
//          if ($beforeStep == "clic") {
//            $automaticObj->insNewStep($idContact, $nextNode->id, $nextNode, $beforeStep, $date);
//          }
//        } else {
//          if ($automaticObj->getStatusStep($automaticStep) == "scheduled") {
//            $connection = $automaticObj->searchConnection($automaticStep->idNode);
//            $nodeOperator = $automaticObj->getNode($connection["source"]);
//            $negation = json_decode($automaticObj->searchNegation($nodeOperator->id));
//            $date = $automaticObj->getDataTime($nodeOperator);
//            $beforeStep = $automaticObj->getBeforeStep($nodeOperator);
//
//            if ($automaticStep->negation == 1) {
//              if ($automaticStep->scheduleDate > date('Y-m-d H:i')) {
//                $automaticObj->uptStatusStep($automaticStep, "canceled");
//                for ($i = 0; $i < count($negation); $i++) {
//                  if ($negation[$i]->dest->class == "success") {
//                    $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
//                    $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date);
//                  }
//                }
//              }
//            }
//
//            if ($automaticStep->beforeStep == "no clic") {
//              if ($automaticStep->scheduleDate > date('Y-m-d H:i')) {
//                $automaticObj->uptStatusStep($automaticStep, "canceled");
//                for ($i = 0; $i < count($negation); $i++) {
//                  if ($negation[$i]->dest->class == "negation") {
//                    $nodeSuccess = $automaticObj->getNode($negation[$i]->dest->idNode);
//                    $automaticObj->insNewStep($idContact, $nodeSuccess->id, $nodeSuccess, $beforeStep, $date, 1);
//                  }
//                }
//              }
//            }
//          }
//        }
//      }

      $this->clickNormal($idLink, $idMail, $idContact);
      $this->db->commit();
    } catch (\InvalidArgumentException $e) {
      $this->logger->log('Exception: [' . $e . ']');
      $link = ($idLink != 0) ? $trackingObj->getLinkToRedirect($idLink, false) : false;
      $this->db->rollback();
      if ($link) {
        return $this->response->redirect($link, true);
      }
      return $this->response->redirect('error/link');
    } catch (\Exception $e) {
      $this->logger->log('Exception: [' . $e . ']');
      $this->db->rollback();
      $link = ($idLink != 0) ? $trackingObj->getLinkToRedirect($idLink, false) : false;
      if ($link) {
        return $this->response->redirect($link, true);
      }
      return $this->response->redirect('error/link');
    }
  }

  public function clickNormal($idLink, $idMail, $idContact) {
    $trackingObj = new TrackingObject();
    $trackingObj->setIdContact($idContact);
    if ($idContact != 0) {
      $trackingObj->setSendIdentification($idMail, $idContact);
    }
    $url = $trackingObj->trackClickEvent($idLink);
        //Logs de Click Normal
//        $logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
        $customLogger = new \TrackLog();
        $customLogger->registerDate = date("Y-m-d h:i:sa");
        $customLogger->idContact = $idContact;
        $customLogger->idMail = $idMail;
        $customLogger->idLink = $idLink;
        $customLogger->url = $url;
        $customLogger->typeName = "clickNormalMethod";
//        $customLogger->globalLogDescription = $logger->log("Parameters on ClickAction: {$parameters}");
//        $customLogger->detailedLogDescription = $logger->log("Identifiers on ClickAction: {$idenfifiers}");
//        $customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
//        $customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
        $customLogger->created = time();
        $customLogger->updated = time();
        $customLogger->save();
      //
    if (!$url) {
      $link = $trackingObj->getLinkToRedirect($idLink, false);
      if (!$link) {
        return $this->response->redirect('error/link');
      }
      return $this->response->redirect($link, true);
    }
    return $this->response->redirect($url);
  }

  public function registerOpen($parameters) {
    $idenfifiers = explode("-", $parameters);
    list($idLink, $idMail, $type, $md5) = $idenfifiers;
    //Logs de RegisterOpen 
//      $logger = new FileAdapter(__DIR__."/../logs/trackLog.log");
      $customLogger = new \TrackLog();
      $customLogger->registerDate = date("Y-m-d h:i:sa");
      $customLogger->idMail = $idMail;
      $customLogger->type = $type;
      $customLogger->typeName = "RegisterOpenMethod";
      $customLogger->idLink = $idLink;
      $customLogger->globalDescription = $parameters;
      $customLogger->detailedDescription = $idenfifiers;
//      $customLogger->globalLogDescription = $logger->log("Parameters on Register Open: {$parameters}");
//      $customLogger->detailedLogDescription = $logger->log("Identifiers on Register Open: {$idenfifiers}");
//      $customLogger->createdBy = \Phalcon\DI::getDefault()->get('user')->email;
//      $customLogger->updatedBy = \Phalcon\DI::getDefault()->get('user')->email;
      $customLogger->created = time();
      $customLogger->updated = time();
      $customLogger->save();
    //
    if ($type == "public") {
      $ip = $this->getIpClient();
      $mxvp = \Mxvp::findFirst(array(
                  "conditions" => array(
                      "ip" => (string) $ip,
                      "deleted" => 0
                  )
      ));
      if ($mxvp) {
        $mxvp->totalopen = $mxvp->totalopen + 1;
        if (!$mxvp->save()) {
          foreach ($deliverableEmail->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $this->logger->debug("visita de publicacion de email en facebook registrada");
      } else {
        $mxvpModel = new Mxvp();
        $mxvpModel->ip = (string) $ip;
        $mxvpModel->openunit = (int) 1;
        $mxvpModel->totalopen = (int) 1;
        $mxvpModel->type = (string) "public";
        if (!$mxvpModel->save()) {
          foreach ($deliverableEmail->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $this->logger->debug("visita de publicacion de email en facebook registrada");
      }
    }
  }

  public function getIpClient() {
    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
      return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
      return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
      return $_SERVER["HTTP_FORWARDED"];
    } else {
      return $_SERVER["REMOTE_ADDR"];
    }
  }

  public function eventprocessorsAction() { 
    $value = $_POST;
//    $mtaEvent = new LogsxMtaxEvent();
//    $mtaEvent->sendid = $value['sendid'];
//    $mtaEvent->id = $value['id'];
//    $mtaEvent->listid = $value['listid'];
//    $mtaEvent->email = $value['email'];
//    $mtaEvent->datetime = $value['event_time'];
//    $mtaEvent->type = $value['event_type'];
//    $mtaEvent->bounce_code = $value['bounce_code'];
//    $mtaEvent->bounce_type = $value['bounce_type'];
//    $mtaEvent->description = $value['bounce_text'];
//    $mtaEvent->click_tracking_id = $value['click_tracking_id'];
//    $mtaEvent->studio_is_unique = $value['studio_is_unique'];
//    $mtaEvent->studio_campaign_id = $value['studio_campaign_id'];
//    $mtaEvent->studio_rl_seq = $value['studio_rl_seq'];
//    $mtaEvent->is_retry = $value['is_retry'];
//    $mtaEvent->studio_autoresponder_id = $value['studio_autoresponder_id'];
//    $mtaEvent->studio_rl_recipid = $value['studio_rl_recipid'];
//    $mtaEvent->click_url = $value['click_url'];
//    $mtaEvent->throttleid = $value['throttleid'];
//    $mtaEvent->json_after = $value['json_after'];
//    $mtaEvent->timestamp = $value['timestamp'];
//    $mtaEvent->sendsliceid = $value['sendsliceid'];
//    $mtaEvent->msguid = $value['msguid'];
//    $mtaEvent->list_name = $value['list_name'];
//    $mtaEvent->studio_ip = $value['studio_ip'];
//    $mtaEvent->message = $value['message'];
//    $mtaEvent->engine_ip = $value['engine_ip'];
//    $mtaEvent->sender = $value['sender'];
//    $mtaEvent->studio_subscriber_id = $value['studio_subscriber_id'];
//    $mtaEvent->studio_mailing_list_id = $value['studio_mailing_list_id'];
//    $mtaEvent->mtaid = $value['mtaid'];
//    $mtaEvent->user_agent = $value['user_agent'];
//    $mtaEvent->json_before = $value['json_before'];
//    $mtaEvent->channel = $value['channel'];
//    $mtaEvent->status = $value['status'];
//    $mtaEvent->outmtaid = $value['outmtaid'];
//    $mtaEvent->studio_rl_seq_id = $value['studio_rl_seq_id'];
//    $mtaEvent->list_label = $value['list_label'];
//    $mtaEvent->injected_time = $value['injected_time'];
//
//    $logs = \LogsxMtaxEvent::findFirst([["id" => (string) $value['id']]]);
//    if($logs){
//      echo "ok";
//    }
//
//    if (!$mtaEvent->save()) {
//      foreach ($mtaEvent->getMessages() as $message) {
//        throw new \InvalidArgumentException($message);
//      }
//    }
    $t0em = substr($value['listid'], 0, 4);
    if($t0em == 't0em'){
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://elasticmail.sigmamovil.com/track/mtaevent",
        //CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        //CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $value,
        //CURLOPT_HTTPHEADER => array(
            //"Accept: application/json",
            //"Authorization: Basic {$key}",
            //"Content-Type: application/json"
          //'Content-type: application/xml',
        //)
      ));
      //$this->logger->log(print_r(curl_exec($curl),true));
      //curl_exec($curl);
      $result = curl_exec($curl);
      if($result){
        echo "ok";
      } 
      curl_close($curl);
      //$this->logger->log(print_r($value['sendid'], true));
      //$this->logger->log(print_r($value['listid'], true));
    } else {
      //$logs = \LogsxMtaxEvent::findFirst([["id" => (string) $value['id']]]);
	  $logs = false;
      if(!$logs){
        $wrapper = new \Sigmamovil\Wrapper\TrackWrapper();
        if(!empty($value['click_tracking_id'])){
          $arrIds = substr($value['click_tracking_id'], 2);
          $arrIds = explode("x", $arrIds);
        }else{
          if(!empty($value['sendid'])){
            $idMail = substr($value['sendid'], 10);
            $where = array("idMail" => (String) $idMail, "email" => $value['email']);
            $mxc = \Mxc::findFirst([$where]);
            $arrIds[0] = (String) $idMail;
            $arrIds[1] = $mxc->idContact;
          }else{
            $arrIds[0] = null;
            $arrIds[1] = null;
          }
        }
        $date = $value['event_time'];

        $this->logger->log(print_r($arrIds, true));
        if($arrIds[0] == null || $arrIds[0] == 'proyectos@sigmamovil.com' || $arrIds[0] == 'emanzano@panturismo.com.co'){
          echo "ok";
          exit;
        }

        try {
          $this->logger->log("ENTRE A SWITCH eventprocessorsAction ".time());
          switch ($value['event_type']) {
            case 'bounce_all':
              $wrapper->trackSoftBounceEvent($arrIds[0], $arrIds[1], $value, $date);
              break;
            case 'bounce_bad_address':
              if($arrIds[0] != null && $arrIds[1] != null){
                $wrapper->trackHardBounceEvent($arrIds[0], $arrIds[1], $value, $date);
              }
              break;
            case 'scomp':
              $wrapper->trackScompBounceEvent($arrIds[0], $arrIds[1], $value, $date);
              break;
          }
          $this->logger->log("SALI DE SWITCH eventprocessorsAction ".time());
          //Se debe devolver un archivo Ok para que que el envio deje de estar en cola.
          echo "ok";
        } catch (Exception $ex) {
          $this->logger->log("Error  Exception ... {$ex}");
        } catch (\InvalidArgumentException $e) {
          $this->logger->log("Error InvalidArgumentException ... {$e}");
        }
      } else { 
        echo "ok";
      }
      unset($logs); 
    }
  }
  
  public function testeventAction(){
    try {

      return $this->set_json_response("ACK", 200, "OK");
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding listfullservices ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}