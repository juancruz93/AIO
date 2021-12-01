<?php

class UserController extends ControllerBase
{

  protected $config;
  
  public function initialize() {
    $this->tag->setTitle("Usuarios");
    parent::initialize();
  }

  public function indexAction() {
    $currentPage = $this->request->getQuery('page', null, 1);

    if (isset($this->user->Usertype->idAccount)) {
      $builder = $this->modelsManager->createBuilder()
          ->from('User')
          ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
          ->leftJoin("City", "City.idCity = User.idCity")
          ->where("Usertype.idAccount  = {$this->user->Usertype->idAccount} AND User.deleted = 0")
          ->orderBy('User.created');
    }

    if (isset($this->user->Usertype->idMasteraccount)) {
      $builder = $this->modelsManager->createBuilder()
          ->from('User')
          ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
          ->leftJoin("City", "City.idCity = User.idCity")
          ->where("Usertype.idMasteraccount  = {$this->user->Usertype->idMasteraccount} AND User.deleted = 0")
          ->orderBy('User.created');
    }

    if (isset($this->user->Usertype->idAllied)) {
      $builder = $this->modelsManager->createBuilder()
          ->from('User')
          ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
          ->join("City", "City.idCity = User.idCity")
          ->where("Usertype.idAllied  = {$this->user->Usertype->idAllied} AND User.deleted = 0")
          ->orderBy('User.created');
    }

    if (isset($this->user->Usertype->idSubaccount)) {
      $builder = $this->modelsManager->createBuilder()
          ->from('User')
          ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
          ->leftJoin("City", "City.idCity = User.idCity")
          ->where("Usertype.idSubaccount  = {$this->user->Usertype->idSubaccount} AND User.deleted = 0")
          ->orderBy('User.created');
    }

    if ($this->user->Role->idRole == -1) {
      $builder = $this->modelsManager->createBuilder()
          ->from('User')
          ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
          ->leftJoin("City", "City.idCity = User.idCity")
          ->where("Usertype.name  = 'master' AND User.deleted = 0")
          ->orderBy('User.created');
    }

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
      "builder" => $builder,
      "limit" => 15,
      "page" => $currentPage
    ));
    $page = $paginator->getPaginate();
    $this->view->setVar("page", $page);
  }

  public function createAction() {
    $user = new User();
    $config = $this->config;

 
    if (isset($this->user->Usertype->idAccount)) {
      $account = Account::findFirst(array(
            'conditions' => 'idAccount= ?1',
            'bind' => array(1 => $this->user->Usertype->idAccount)
      ));
    }
    if (isset($this->user->Usertype->idMasteraccount)) {
      $account = Masteraccount::findFirst(array(
            'conditions' => 'idMasteraccount = ?1',
            'bind' => array(1 => $this->user->Usertype->idMasteraccount)
      ));
    }
    
    if (isset($this->user->Usertype->idAllied)) {
      $account = Allied::findFirst(array(
            'conditions' => 'idAllied = ?1',
            'bind' => array(1 => $this->user->Usertype->idAllied)
      ));
    }
//    var_dump($account);
//    exit();
    $form = new UserForm($user, $this->user->role);
    if (!$account) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("user/index");
    }
    if ($this->request->isPost()) {
      $userManager = new \Sigmamovil\General\Misc\UserManager();
      try {
        if (isset($this->user->Usertype->idMasteraccount)) {
          $user = $userManager->creataMasterUser($this->request->getPost());
          $this->notification->success('Se ha creado el usuario exitosamente en la cuenta maestra <strong>' . $account->name . '</strong>');
          $this->trace("success", "Se creo un usuario con ID: {$user->idUser}");
          return $this->response->redirect("user/index");
        }
        if (isset($this->user->Usertype->idAllied)) {
          $user = $userManager->creataAlliedUser($this->request->getPost(), $this->user->Usertype->idAllied);
          $this->notification->success('Se ha creado el usuario exitosamente en la cuenta aliada <strong>' . $account->name . '</strong>');
          $this->trace("success", "Se creo un usuario con ID: {$user->idUser}");
          return $this->response->redirect("user/index/{$account->idAccount}");
        }
        if (isset($this->user->Usertype->idAccount)) {
          $user = $userManager->creataAccountUser($this->request->getPost(), $this->user->Usertype->idAccount);
          $this->notification->success('Se ha creado el usuario exitosamente en la cuenta <strong>' . $account->name . '</strong>');
          $this->trace("success", "Se creo un usuario con ID: {$user->idUser}");
          return $this->response->redirect("user/index/{$account->idAccount}");
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }
    }

    $this->view->UserForm = $form;
    $this->view->setVar('account', $account);
  }

  public function editAction($id) {
    $userEdit = User::findFirst(array(
          "conditions" => "idUser = ?1",
          "bind" => array(1 => $id)
    ));

    if (!$userEdit) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("user/index");
    }

//    if ($userEdit->idAccount != $this->user->idAccount) {
//      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
//      return $this->response->redirect("user/index");
//    }

    $obj = new stdClass();
    $obj->name = 'sudo';

    $city = City::findByIdCity($userEdit->idCity);

    $state = State::findByIdState($city[0]->idState);

    $idState = $city[0]->idState;
    $idcountry = $state[0]->idCountry;
    $this->view->setVar("userE", $userEdit);
    $form = new UserForm($userEdit, $obj);
    $this->view->UserForm = $form;
    $this->view->setVar("idCountry", $idcountry);
    $this->view->setVar("idState", $idState);

    if ($this->request->isPost()) {
      try {

        $form->bind($this->request->getPost(), $userEdit);

        $userEdit->idCity = $this->request->getPost('citySelectedUser');
        $cel = $form->getValue('cellphone');

        $email = strtolower($form->getValue('email'));
        $userEdit->email = $email;
        $userEdit->cellphone = $cel;
        if ($userEdit->name == "") {
          throw new InvalidArgumentException("El campo nombre de usuario es obligatorio");
        }
        if ($userEdit->lastname == "") {
          throw new InvalidArgumentException("El campo nombre de usuario es obligatorio");
        }
        if ($userEdit->cellphone == "") {
          throw new InvalidArgumentException("El campo nombre de usuario es obligatorio");
        }
        if (!is_numeric($userEdit->idCity)) {
          throw new InvalidArgumentException("El campo ciudad de usuario es obligatorio");
        }
        if ($userEdit->save()) {
          $this->notification->info('Se ha editado exitosamente el usuario <strong>' . $userEdit->username . '</strong>');
          $this->trace("success", "Se edito un usuario con ID: {$userEdit->idUser}");
          return $this->response->redirect("user/index/{$account->idAccount}");
        } else {
          $userEdit->username = $username;
          foreach ($userEdit->getMessages() as $message) {
            $this->notification->error($message);
          }
          $this->trace("fail", "No se edito el usuario con ID: {$userEdit->idUser}");
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }
    }
  }

  public function deleteAction($id) {
    try {
      $idUser = $this->session->get('idUser');

      $user = User::findFirst(array(
            "conditions" => "idUser = ?1",
            "bind" => array(1 => $id)
      ));

      if ($id == $idUser) {
        throw new Exception("No se puede eliminar el usuario que esta actualmente en sesión, por favor verifique la información");
        $this->trace('fail', "Se intento borrar un usuario en sesión: {$idUser}");
      }

      if (!$user) {
        throw new Exception("El usuario que ha intentado eliminar no existe, por favor verifique la información");
        $this->trace('fail', "El usuario no existe: {$idUser}");
      }
      $email = $user->email;
      $users = User::find(array(
                  "conditions" => "email = ?1",
                  "bind" => array(1 => $email)
      ));

    //Aqui se eliminan todos los usuarios asociados a ese correo ya se subaccount o account
    foreach($users as $us){
      $us->deleted = time();
      $email = str_replace("@", "_1@", $us->email);
      $us->email = $email;
      $us->deletedBy = \Phalcon\DI::getDefault()->get('user')->email;
      if (!$us->update()) {
        foreach ($us->getMessages() as $msg) {
          throw new Exception($msg);
        }
      } 
    }
    $this->notification->warning("Se ha eliminado el usuario <strong>{$user->username}</strong> exitosamente");
    $this->trace('success', "Se elimino el usuario: {$id}");
    return $this->response->redirect("user/index");
    
   /* $user->deleted = time();
    $email = str_replace("@", "_1@", $user->email);
    $user->email = $email;
    $user->deletedBy = \Phalcon\DI::getDefault()->get('user')->email;
      if (!$user->update()) {
        foreach ($user->getMessages() as $msg) {
          throw new Exception($msg);
        }
      } else {
        $this->notification->warning("Se ha eliminado el usuario <strong>{$user->username}</strong> exitosamente");
        $this->trace('success', "Se elimino el usuario: {$id}");
        return $this->response->redirect("user/index");
      }*/
      
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
      return $this->response->redirect("user/index");
    }
  }

  public function passeditAction($id) {
    $editUser = User::findFirst(array(
          "conditions" => "idUser = ?1",
          "bind" => array(1 => $id)
    ));

    if (!$editUser) {
      $this->flashSession->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("user/index");
    }
    if ($editUser->idAccount != $this->user->idAccount) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("user/index");
    }
    $account = $editUser->account;
    $this->view->setVar("userE", $editUser);
    if ($this->request->isPost()) {
      $pass = $this->request->getPost('pass1');
      $pass2 = $this->request->getPost('pass2');

      try {
        if ((empty($pass) || empty($pass2))) {
          throw new InvalidArgumentException("El campo Contraseña esta vacío, por favor valide la información");
        }
        if (($pass != $pass2)) {
          throw new InvalidArgumentException("Las contraseñas no coinciden");
        }
        if (strlen($pass) < 8) {
          throw new InvalidArgumentException("La contraseña es muy corta, debe tener como mínimo 8 caracteres'");
        }
        if (strlen($pass) > 20) {
          throw new InvalidArgumentException("La contraseña no puede tener mas de 20 caracteresS'");
        }
        $editUser->password = $this->security->hash($pass);
        if (!$editUser->save()) {
          foreach ($editUser->getMessages() as $message) {
            $this->notification->error($message);
          }
          $this->trace("fail", "No se edito la contraseña del usuario con ID: {$editUser->idUser}");
        } else {
          $this->notification->success('Se ha editado la contraseña exitosamente del usuario <strong>' . $editUser->username . '</strong>');
          $this->trace("sucess", "Se edito la contraseña del usuario con ID: {$editUser->idUser}");
          return $this->response->redirect("user/index/{$account->idAccount}");
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }
    }
  }

}
