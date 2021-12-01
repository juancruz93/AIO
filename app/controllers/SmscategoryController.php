<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class SmsCategoryController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Categorías de SMS");
    parent::initialize();
  }

  public function IndexAction() {
//    $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
//    $paginator = new PaginatorModel(array(
//        "data" => SmsCategory::find(["conditions" => "deleted = ?0 and idAccount = ?1", "bind" => [0 => 0, 1 => $idAccount], "order" => "created DESC"]),
//        "limit" => 10,
//        "page" => $this->request->getQuery("page", "int")
//    ));
//    $this->view->SetVar("page", $paginator->getPaginate());
  }

  public function listAction(){}
  
  public function deleteAction($id) {
    if (!isset($id)) {
      throw new InvalidArgumentException("Dato de categría no válido");
    }
    $smscategory = SmsCategory::findFirst(array(
                "conditions" => "idSmsCategory = ?0",
                "bind" => array($id)
    ));
    if (!isset($smscategory)) {
      throw new InvalidArgumentException("Dato de categría no válido");
    }
    $smscategory->deleted = time();
    if (!$smscategory->save()) {
      foreach ($smscategory->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
    }
    $this->notification->warning("Se ha eliminado una categoria");
    return $this->response->redirect("smscategory");
  }

  public function validateNameSMSCategory($name) {
    $validateCategory = \SmsCategory::findFirst(array("conditions" => "name = ?0 AND deleted=0 AND idAccount=?1", "bind" => array(0 => $name, 1 => $this->user->Usertype->Subaccount->idAccount)));
    if ($validateCategory) {
      throw new \InvalidArgumentException('El nombre de la categoría ya existe');
    }
  }

  public function CreateAction() {
    try {
      $form = new SmsCategoryForm();
      $this->view->SetVar("form", $form);
      if ($this->request->isPost()) {
        $this->validateNameSMSCategory($this->request->getPost()['name']);



        $smscategory = new SmsCategory();
        $form->bind($this->request->getPost(), $smscategory);

        if ($this->request->getPost()['status'] == null) {
          $smscategory->status = 0;
        }
        $smscategory->idAccount = $this->user->Usertype->Subaccount->idAccount;

        if (!$form->isValid() || !$smscategory->save()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($smscategory->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha creado la categoría de SMS exitosamente");
        return $this->response->redirect("smscategory#/");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error guardando la categoria de SMS..." . $ex->getMessage());
      $this->notfication->error("Ha ocurrido un error guardando la categoría de SMS, por favor comuníquese con soporte");
    }
  }

  public function EditAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Dato de categría no válido");
      }
      $smscategory = SmsCategory::findFirst(array(
                  "conditions" => "idSmsCategory = ?0",
                  "bind" => array($id)
      ));
      if (!$smscategory) {
        $this->notification->error("La categoría que intenta editar no existe, por favor verifique la información");
        return $this->response->redirect("smscategory#/");
      }

      $form = new SmsCategoryForm($smscategory);
      $this->view->setVar("form", $form);
      $this->view->setVar("smscategory", $smscategory);
      if ($this->request->isPost()) {
        $form->bind($this->request->getPost(), $smscategory);

        if ($this->request->getPost()['status'] == null) {
          $smscategory->status = 0;
        }

        if (!$form->isValid() || !$smscategory->update()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($smscategory->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha editado la categoría de SMS exitosamente");
        return $this->response->redirect("smscategory#/");
      }
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error editando la categoria de SMS..." . $ex->getMessage());
      $this->notfication->error("Ha ocurrido un error editando la categoría de SMS, por favor comuníquese con soporte");
    }
  }

}