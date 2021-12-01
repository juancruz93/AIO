<?php

class HabeasdataController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle('Habeasdata');
    parent::initialize();
  }

  public function indexAction() {
    
      $account = $this->user->UserType->Account;

      $form = new \HabeasdataForm($account);
      $this->view->setVar('form', $form);

      if ($this->request->isPost()) {
        $form->bind($this->request->getPost(), $account);
        $dataHD = $form->getValue('habeasData');
        if ($dataHD == "") {
          $dataHD = NULL; // guarde la como vacia...
        }
        if ($account->save()) {
          $this->notification->info('Se ha editado exitosamente el usuario <strong>' . $account->name . '</strong>');
          $this->trace("success", "Se edito un usuario con ID: {$account->idAccount}");
          return $this->response->redirect("tools/");
        } else {
          $account->name = $username;
          foreach ($userEdit->getMessages() as $message) {
            $this->notification->error($message);
          }
          $this->trace("success", "Se edito un usuario con ID: {$account->idAccount}");
        }
      }
   }
   
}