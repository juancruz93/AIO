<?php

/**
 * @RoutePrefix("/api/customizing")
 */
class ApicustomizingController extends ControllerBase {

  /**
   * 
   * @Get("/getcustomizing/")
   */
  public function getcustomizingAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $wrapper->findCustomizing();
//      var_dump($this->set_json_response($wrapper->getThemes(), 200));
//      exit;
      return $this->set_json_response($wrapper->getThemes(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{idPersonalizationTheme:[0-9]+}", methods="DELETE")
   */
  public function deleteAction($idPersonalizationThemes) {

    try {
      $theme = PersonalizationThemes::findFirst(array('conditions' => "idPersonalizationThemes = ?0", 'bind' => array($idPersonalizationThemes)));
      if (!$theme) {
        throw new InvalidArgumentException("No se ha encontrado el tema personalizado, por favor intenta de nuevo");
      }
      if ($theme->status == "selected") {
        throw new InvalidArgumentException("No se puede eliminar este tema porque es el que se usa actualmente");
      }

      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $wrapper->setTheme($theme);
      $wrapper->deleteTheme();
      $this->trace("success", "Se ha eliminado el tema " . $theme->name);

      return $this->set_json_response(array("message" => "Se ha eliminado el tema " . $theme->name . " exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while deleting themes... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Put("/select/{idPersonalizationTheme:[0-9]+}")
   */
  public function selectAction($idPersonalizationThemes) {
    try {
      $theme = PersonalizationThemes::findFirst(array('conditions' => "idPersonalizationThemes = ?0 AND deleted=0", 'bind' => array($idPersonalizationThemes)));
      if (!$theme) {
        throw new InvalidArgumentException("No se ha encontrado el tema personalizado, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $wrapper->setTheme($theme);
      if ($theme->idAllied == null) {
        $wrapper->selectDefaultTheme();
      } else {
        $wrapper->selectTheme();
      };
      $this->trace("success", "Se ha seleccionado el tema " . $theme->name . "como tema del sistema");

      return $this->set_json_response(array("message" => "Se ha seleccionado el tema " . $theme->name . " como tema del sistema"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while selecting theme... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/setItemBlockInfo/{page:[0-9]+}")
   */
  public function setItemBlockInfoAction($data) {
//    try {
//      $contentsraw = $this->getRequestContent();
//      $data = json_decode($contentsraw);
//      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
//      $wrapper->findCustomizing($page, $data);
//      return $this->set_json_response($wrapper->getCustomizing(), 200);
//    } catch (InvalidArgumentException $ex) {
//      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
//    } catch (Exception $ex) {
//      $this->logger->log("Exception while finding customizing... {$ex}");
//      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
//    }
  }

  /**
   * 
   * @Post("/add")
   */
  public function addAction() {
    try {
      $contentsraw = $_POST['data'];
      $data = json_decode($contentsraw, true);
      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $wrapper->setDataArray($data);
      $wrapper->setFooter($data);
      $wrapper->setSocialsordered($data);
      $wrapper->setInfosordered($data);
      if (isset($_FILES['logo'])) {
        $logo = $_FILES['logo'];
        $wrapper->setLogo($logo);
      }
      if ($data['id'] == null) {
        $customizing = $wrapper->saveCustomizing();
      } else {
        $theme = PersonalizationThemes::findFirst(array('conditions' => "idPersonalizationThemes = ?0", 'bind' => array($data['id'])));
        if (!$theme) {
          throw new InvalidArgumentException("No se ha encontrado el tema personalizado, por favor intenta de nuevo");
        }
        $data = (object) $data;

        $wrapper->editFooter();
        $wrapper->setTheme($theme);
        $wrapper->editTheme();
        $customizing = $theme;
      }
      $this->trace("success", "Se ha guardado el tema personalizado");

      return $this->set_json_response(array("message" => "Se ha guardado el tema personalizado exitosamente", "customizing" => $customizing), 200);
    } catch (\InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while adding customizing... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getonecustomizing/{idPersonalizationThemes:[0-9]+}")
   */
  public function getonecustomizingAction($idPersonalizationThemes) {
    try {
      $theme = PersonalizationThemes::findFirst(array('conditions' => "idPersonalizationThemes = ?0", 'bind' => array($idPersonalizationThemes)));
      if (!$theme) {
        throw new InvalidArgumentException("No se ha encontrado el tema personalizado, por favor intenta de nuevo");
      }
      $blocks = \FooterBlock::find(["conditions" => "idPersonalizationThemes = ?0 AND deleted = 0 ", "bind" => [0 => $theme->idPersonalizationThemes]]);
//      var_dump($blocks);
//      exit;
      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();

      if (count($blocks) > 0) {

        if (count($blocks) == 1) {
          $socialNets = \PersonalizationSocialNetwork::find(["conditions" => "idFooterBlock = ?0 AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock]]);
          $additionalInfos = \AdditionalInfo::find(["conditions" => "idFooterBlock = ?0 AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock]]);
        } else if (count($blocks) == 2) {
          $socialNets = \PersonalizationSocialNetwork::find(["conditions" => "(idFooterBlock = ?0 OR idFooterBlock = ?1) AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock, 1 => $blocks[1]->idFooterBlock]]);
          $additionalInfos = \AdditionalInfo::find(["conditions" => "(idFooterBlock = ?0 OR idFooterBlock = ?1) AND deleted = 0 ", "bind" => [0 => $blocks[0]->idFooterBlock, 1 => $blocks[1]->idFooterBlock]]);
        }

        $wrapper->setInfoBlock($blocks, $socialNets, $additionalInfos);

        if ($socialNets) {
          $wrapper->setAllSocialNets($socialNets);
        }
        if ($additionalInfos) {
          $wrapper->setAllAdditionalInfos($additionalInfos);
        }
      }

      $wrapper->setTheme($theme);
      $wrapper->modelTheme();


      return $this->set_json_response($wrapper->getTheme(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding one theme... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getsocialnetworks")
   */
  public function getsocialnetworksAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $wrapper->findSocialNetworks();
//        var_dump($wrapper->getSocialNetworks());
//        exit;
      return $this->set_json_response($wrapper->getSocialNetworks(), 200);
    } catch (\InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (\Exception $ex) {
      $this->logger->log("Exception while finding social networks... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/edit/{idPersonalizationThemes:[0-9]+}")
   */
  public function editAction($idPersonalizationThemes) {

    try {
      $theme = PersonalizationThemes::findFirst(array('conditions' => "idPersonalizationThemes = ?0", 'bind' => array($idPersonalizationThemes)));
      if (!$theme) {
        throw new InvalidArgumentException("No se ha encontrado el tema personalizado, por favor intenta de nuevo");
      }


//      $contentsraw = $this->getRequestContent();
//      $data = json_decode($contentsraw);

      $contentsraw = $_POST['data'];
      $data = json_decode($contentsraw, true);


      $wrapper = new \Sigmamovil\Wrapper\CustomizingWrapper();
      $data = (object) $data;
      if (isset($_FILES['logo'])) {
        $logo = $_FILES['logo'];
        $wrapper->setLogo($logo);
      }

      $wrapper->setDataArray($data);
      $wrapper->setFooter($data);
      $wrapper->setSocialsordered($data);
      $wrapper->setInfosordered($data);
//      $wrapper->setSocialsdeleted($data);
//      $wrapper->setInfosdeleted($data);
      $wrapper->editFooter();
      $wrapper->setTheme($theme);

//      $wrapper->setData($data);
      $wrapper->editTheme();
      $this->trace("success", "Se ha editado el tema personalizado");

      return $this->set_json_response(array("message" => "Se ha editado el tema personalizado exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while editing themes... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
