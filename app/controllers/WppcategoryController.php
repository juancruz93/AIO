<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class WppcategoryController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Categorías de WhatsApp");
    parent::initialize();
  }

  public function IndexAction() {}

  public function listAction(){}
  
  public function deleteAction() {}

  public function validateNameWPPCategory($name) {
    $validateCategory = \WppCategory::findFirst(array("conditions" => "name = ?0 AND deleted=0 AND idAccount=?1", "bind" => array(0 => $name, 1 => $this->user->Usertype->Subaccount->idAccount)));
    if ($validateCategory) {
      throw new \InvalidArgumentException('El nombre de la categoría ya existe');
    }
  }

  public function createAction() {
    try {
      $form = new WppCategoryForm();
      $this->view->SetVar("form", $form);
      if ($this->request->isPost()) {
        $this->validateNameWPPCategory($this->request->getPost()['name']);

        $wppcategory = new WppCategory();
        $form->bind($this->request->getPost(), $wppcategory);

        if ($this->request->getPost()['status'] == null) {
          $wppcategory->status = 0;
        }
        $wppcategory->idAccount = $this->user->Usertype->Subaccount->idAccount;

        if (!$form->isValid() || !$wppcategory->save()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($wppcategory->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha creado la categoría de WhatsApp exitosamente");
        return $this->response->redirect("wppcategory#/");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error guardando la categoria de WhatsApp..." . $ex->getMessage());
      $this->notfication->error("Ha ocurrido un error guardando la categoría de WhatsApp, por favor comuníquese con soporte");
    }
  }

  public function EditAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Dato de categría no válido");
      }
      $wppcategory = WppCategory::findFirst(array(
                  "conditions" => "idWppCategory = ?0",
                  "bind" => array($id)
      ));
      if (!$wppcategory) {
        $this->notification->error("La categoría que intenta editar no existe, por favor verifique la información");
        return $this->response->redirect("wppcategory#/");
      }

      $form = new WppCategoryForm($wppcategory);
      $this->view->setVar("form", $form);
      $this->view->setVar("wppcategory", $wppcategory);
      if ($this->request->isPost()) {
        $form->bind($this->request->getPost(), $wppcategory);

        if ($this->request->getPost()['status'] == null) {
          $wppcategory->status = 0;
        }

        if (!$form->isValid() || !$wppcategory->update()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($wppcategory->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha editado la categoría de SMS exitosamente");
        return $this->response->redirect("wppcategory#/");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error editando la categoria de SMS..." . $ex->getMessage());
      $this->notfication->error("Ha ocurrido un error editando la categoría de SMS, por favor comuníquese con soporte");
    }
  }

}