<?php

class LanguageController extends ControllerBase {

  public function indexAction() {

    $this->view->setVar("app_name", "language");
  }

  public function listAction() {
    
  }

  public function createAction() {
    try {
      $form = new LanguageForm();
      $this->view->SetVar("form", $form);
      if ($this->request->isPost()) {
        $language = new Language();
        $form->bind($this->request->getPost(), $language);
        $language -> created = time();
        $language -> updated = 0;
        
        if (!$form->isValid() || !$language->save()) {
          foreach ($form->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          foreach ($language->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->notification->success("Se ha creado el idioma exitosamente");
        return $this->response->redirect("language");
      }
    } catch (InvalidArgumentException $ex) 
    {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->logger->error("Ha ocurrido un error guardando el idioma..." . $ex->getMessage());
      $this->notfication->error("Ha ocurrido un error guardando el idioma, por favor comuníquese con soporte");
    }
  }

  public function editAction() {
    
  }
}
