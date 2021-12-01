<?php

class AdmincontactController extends ControllerBase {

  public function indexAction($idAllied,$idMasteraccount) {
    try {
      if (!$idAllied) {
        throw new InvalidArgumentException("Dato de aliado inválido");
      }
      $currentPage = $this->request->getQuery('page', null, 1);

      $admincontact = $this->modelsManager->createBuilder()
              ->from("Admincontact")
              ->where("idAllied = :id:", array("id" => $idAllied));

      $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
          "builder" => $admincontact,
          "limit" => 15,
          "page" => $currentPage
      ));
      
      
      $this->view->setVar("page", $paginator->getPaginate());
      $this->view->setVar("idAllied", $idAllied);
      $this->view->setVar("idMasteraccount", $idMasteraccount);
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("masteraccount/aliaslist/{$idMasteraccount}");
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error listando los admincontact..." . $ex->getMessage());
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("masteraccount/aliaslist/{$idMasteraccount}");
    }
  }

  public function createAction($idAllied) {
    try {
      if (!isset($idAllied)) {
        throw new InvalidArgumentException("Dato de aliado inválido");
      }
      $form = new AdmincontactForm();
      $this->view->setVar("form", $form);
      $this->view->setVar("idAllied", $idAllied);
      if ($this->request->isPost()) {
        $admincontact = new Admincontact();
        $form->bind($this->request->getPost(), $admincontact);
        $admincontact->idAllied = $idAllied;
        if (!$form->isValid() || !$admincontact->save()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($admincontact->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha creado el contacto administrativo exitosamente");
        $this->trace("success", "Se creo el tal contacto");
        return $this->response->redirect("admincontact/index/{$idAllied}/{$this->user->UserType->idMasteraccount}");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error guardando los admincontact..." . $ex->getMessage());
      $this->notification->error($ex->getMessage());
    }
  }

  public function editAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Dato de contacto inválido");
      }
      $admincontact = Admincontact::findFirst(array(
                  "conditions" => "idAdmincontact = ?0",
                  "bind" => array($id)
      ));

      if (!$admincontact) {
        $this->notification->error("El contacto que intenta editar no existe, por favor verifique la información");
        return $this->response->redirect("masteraccount/aliaslist/{$this->user->UserType->idMasteraccount}");
      }
      $form = new AdmincontactForm($admincontact);
      $this->view->setVar("form", $form);
      $this->view->setVar("Admincontact", $admincontact);
      if ($this->request->isPost()) {
        $form->bind($this->request->getPost(), $admincontact);
        if (!$form->isValid() || !$admincontact->update()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($admincontact->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->info("Se ha editado el contacto administrativo exitosamente");
        $this->trace("success", "Se actualizo el tal contacto");
        return $this->response->redirect("admincontact/index/{$admincontact->idAllied}/{$admincontact->Allied->idMasteraccount}");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error editando el contacto... " . $ex->getMessage());
      $this->notification->error("Ha ocurrido un error editando el contacto, por favor comuniquese con soporte");
    }
  }

  public function deleteAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Dato de contacto inválido");
      }
      $admincontact = Admincontact::findFirst(array(
                  "conditions" => "idAdmincontact = ?0",
                  "bind" => $id
      ));
      if (!$admincontact) {
        throw new InvalidArgumentException("El contacto que intenta eliminar no existe, por favor verifique la información");
      }
      if (!$admincontact->delete()) {
        foreach ($admincontact->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      $this->notification->warning("Se ha eliminado el contacto exitosamente");
      return $this->response->redirect("admincontact/index/{$admincontact->idAllied}/{$admincontact->Allied->idMasteraccount}");
    } catch (InvalidArgumentException $ex) {
      $this->nofication->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error eliminando el contacto... " . $ex->getMessage());
      $this->notification->error("Ha ocurrido un error eliminando el contacto, por comuniquese con soporte");
    }
  }

}
