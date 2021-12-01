<?php

class TechnicalcontactController extends ControllerBase {

  public function IndexAction($idAllied, $idMasteraccount) {
    $this->view->setVar("app_name", "supportcontact");
    $this->view->setVar("idAllied", $idAllied);
    $this->view->setVar("idMasteraccount", $idMasteraccount);
  }

  public function CreateAction($idAllied, $idMasteraccount) {
    if (!$idAllied) {
      throw new InvalidArgumentException("Dato de aliado inválido");
    }
    $this->view->setVar("app_name", "supportcontact");
    $this->view->setVar("idAllied", $idAllied);
    $this->view->setVar("idMasteraccount", $idMasteraccount);

    $form = new TechnicalcontactForm();
    $this->view->setVar("form", $form);
  }

  public function EditAction($id, $idMasteraccount) {
    if (!isset($id)) {
      throw new InvalidArgumentException("Dato de contacto inválido");
    }
    $technicalcontact = SupportContact::findFirst([
                "conditions" => "idSupportContact = ?0",
                "bind" => [$id]
    ]);
    if (!$technicalcontact) {
      $this->notification->error("El contacto que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("masteraccount/aliaslist/{$this->user->UserType->idMasteraccount}");
    }

    $form = new TechnicalcontactForm($technicalcontact);
    $this->view->setVar("form", $form);
    $this->view->setVar("app_name", "supportcontact");
    $this->view->setVar("idAllied", $technicalcontact->idAllied);
    $this->view->setVar("idSupportContact", $id);
    $this->view->setVar("idMasteraccount", $idMasteraccount);
  }

  public function DeleteAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Dato de contacto inválido");
      }
      $technicalcontact = SupportContact::findFirst([
                  "conditions" => "idSupportContact = ?0",
                  "bind" => [$id]
      ]);
      if (!$technicalcontact) {
        throw new InvalidArgumentException("El contacto que intenta eliminar no existe, por favor verifique la información");
      }
      $technicalcontact->deleted = time();
      if (!$technicalcontact->save()) {
        foreach ($technicalcontact->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      $this->notification->warning("Se ha eliminado el contacto exitosamente");
      return $this->response->redirect("technicalcontact/index/{$technicalcontact->idAllied}/{$technicalcontact->Allied->idMasteraccount}");
    } catch (InvalidArgumentException $ex) {
      $this->nofication->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error eliminando el contacto... " . $ex->getMessage());
      $this->notification->error("Ha ocurrido un error eliminando el contacto, por comuniquese con soporte");
    }
  }

}
