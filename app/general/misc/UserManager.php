<?php

namespace Sigmamovil\General\Misc;

class UserManager {

  private $modelsManager;

  public function __construct() {
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
  }

  public function createuser($data, $nameTypeUser) {

    $masterUser = new \User();
    $userType = new \Usertype();
    $userType->idMasteraccount = $data->idMasteraccount;
    $userType->name = $nameTypeUser;
    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion [$nameTypeUser]");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }
    $masterUser->idUsertype = $userType->idUsertype;
    $pass = $data->password;
    $pass2 = $data->password2;
    $cel = $data->cellphone;
    if (strlen($cel) < 10 && strlen($cel) > 1 || strlen($cel) > 10) {
      throw new \InvalidArgumentException("El número de celular ingresado no es valido");
    } else if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la informacin");
    } else if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña es muy corta, debe tener como minimo 8 caracteres");
    } else {
      $masterUser->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
      $masterUser->cellphone = $data->cellphone;
      $masterUser->city = $data->cityUser;
      $masterUser->email = strtolower($data->emailUser);
      $masterUser->lastname = $data->lastnameUser;
      $masterUser->name = $data->nameUser;
      $masterUser->idRole = $data->idRole;
      if (!$masterUser->save()) {
        \Phalcon\DI::getDefault()->get('db')->rollback();
        foreach ($masterUser->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      return $masterUser;
    }
  }

  public function creataMasterUser($masterUser) {

    $user = new \User();
    $pass = $masterUser['pass1'];
    $pass2 = $masterUser['pass2'];

    if (!is_numeric($masterUser['citySelectedUser'])) {
      throw new \InvalidArgumentException("La ciudad es obligatoria valide la información");
    }
    if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la información");
    }
    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña debe tener al menos 8 caracteres");
    }
    if (strlen($pass) > 20) {
      throw new \InvalidArgumentException("La contraseña es muy larga, debe tener como máximo 20 caracteres");
    }
//        $form = new UserForm($user, $this->user->role);
    $form = \Phalcon\DI::getDefault()->get('UserForm');
    $form->bind($masterUser, $user);
    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idMasteraccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idMasteraccount;

    $user->idCity = $masterUser['citySelectedUser'];
    $user->idRole = \Phalcon\DI::getDefault()->get("roles")->master;
    $userType->name = "master";
    //

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }

    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
//          \Phalcon\DI::getDefault()->get('db')->rollback();
    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function creataAlliedUser($userAllied, $idAllied) {
    $user = new \User();
    $pass = $userAllied['pass1'];
    $pass2 = $userAllied['pass2'];
    $cel = $userAllied['cellphone'];

    if (!is_numeric($userAllied['citySelectedUser'])) {
      throw new \InvalidArgumentException("La ciudad es obligatoria valide la información");
    }
    if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la información");
    }
    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña debe tener al menos 8 caracteres");
    }
    if (strlen($pass) > 20) {
      throw new \InvalidArgumentException("La contraseña es muy larga, debe tener como máximo 20 caracteres");
    }
    $form = \Phalcon\DI::getDefault()->get('UserForm');
    $form->bind($userAllied, $user);

    $this->verifyUser(array(
        "email" => $user->email,
        "role" => "Allied"
    ));

    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idAllied = $idAllied;

    $user->idCity = $userAllied['citySelectedUser'];
    $user->idRole = \Phalcon\DI::getDefault()->get("roles")->allied;
    $userType->name = "allied";
    //

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }
    $user->idUsertype = $userType->idUsertype;
    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
//          \Phalcon\DI::getDefault()->get('db')->rollback();
    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function creataAccountUser($userData, $idAccount) {
    $user = new \User();
    $pass = $userData['pass1'];
    $pass2 = $userData['pass2'];
    $cel = $userData['cellphone'];

    if (!is_numeric($userData['citySelectedUser'])) {
      throw new \InvalidArgumentException("La ciudad es obligatoria valide la información");
    }
    if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la información");
    }
    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña debe tener al menos 8 caracteres");
    }
    if (strlen($pass) > 20) {
      throw new \InvalidArgumentException("La contraseña es muy larga, debe tener como máximo 20 caracteres");
    }

    $form = \Phalcon\DI::getDefault()->get('UserForm');
    $form->bind($userData, $user);

    $this->verifyUser(array(
        "email" => $user->email,
        "role" => "Account"
    ));

    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    $user->idRole = \Phalcon\DI::getDefault()->get('roles')->account;
    $user->idCity = $userData['citySelectedUser'];

    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idAccount = $idAccount;
    $userType->name = "account";

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }
    
    $this->verifyUser(array(
        "email" => $user->email,
        "role" => "Account"
    ));
    
    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function createAlliedUserMasterAccount($user, $idAllied) {
    $pass = $user->password;

    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña es muy corta, debe tener como minimo 8 caracteres");
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idAllied = $idAllied;
    $userType->name = "allied";

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }

    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function createSubaccountUser($data, $idSubaccount) {
    $user = new \User();
    $pass = $data['password'];

    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña es muy corta, debe tener como minimo 8 caracteres");
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);


    $user->name = $data['nameUser'];
    $user->lastname = $data['lastnameUser'];
    $user->email = $data['emailUser'];
    $user->cellphone = $data['cellphone'];
    $user->idCity = $data['cityUser'];

    $user->idRole = \Phalcon\DI::getDefault()->get("roles")->subaccount;

    //\Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idSubaccount = $idSubaccount;
    $userType->name = "subaccount";

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }

    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return $user;
  }

  public function creataSubaccountUser($userSubaccount, $subaccount) {
    $user = new \User();
    $pass = $userSubaccount['pass1'];
    $pass2 = $userSubaccount['pass2'];

    if (!is_numeric($userSubaccount['citySelectedUser'])) {
      throw new \InvalidArgumentException("La ciudad es obligatoria");
    }
    if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la información");
    }
    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña es muy corta, debe tener como minimo 8 caracteres");
    }
//        $form = new UserForm($user, $this->user->role);
    $form = \Phalcon\DI::getDefault()->get('UserForm');
    $form->bind($userSubaccount, $user);
    $user->idCity = $userSubaccount['citySelectedUser'];
    $user->idRole = \Phalcon\DI::getDefault()->get("roles")->subaccount;
    
    $this->verifyUser(array(
        "email" => $user->email,
        "role" => "Subaccount"
    ));
    
    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idSubaccount = $subaccount;


    $userType->name = "subaccount";

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }

    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
//          \Phalcon\DI::getDefault()->get('db')->rollback();
    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function createMasterUserByMasteraccount($masterUser, $idmasteraccount) {

    $user = new \User();
    $pass = $masterUser['pass1'];
    $pass2 = $masterUser['pass2'];

    if ($masterUser['citySelectedUser'] == '') {
      throw new \InvalidArgumentException("La ciudad es obligatoria valide la información");
    }
    if ($pass !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden por favor verifique la información");
    }
    if (strlen($pass) < 8) {
      throw new \InvalidArgumentException("La contraseña debe tener al menos 8 caracteres");
    }
    if (strlen($pass) > 20) {
      throw new \InvalidArgumentException("La contraseña es muy larga, debe tener como máximo 20 caracteres");
    }
//        $form = new UserForm($user, $this->user->role);
    $form = \Phalcon\DI::getDefault()->get('UserForm');
    $form->bind($masterUser, $user);

    $this->verifyUser(array(
        "email" => $user->email,
        "role" => "Master"
    ));

    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $user->password = \Phalcon\DI::getDefault()->get('security')->hash($pass);
    \Phalcon\DI::getDefault()->get('db')->begin();
    $userType = new \Usertype();
    $userType->idMasteraccount = $idmasteraccount;

    $user->idCity = $masterUser['citySelectedUser'];
    $user->idRole = \Phalcon\DI::getDefault()->get("roles")->master;
    $userType->name = "master";
    //

    if (!$userType->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se creo la relacion ");
      foreach ($userType->getMessages() as $message) {
        throw new Exception($message);
      }
    }

    $user->idUsertype = $userType->idUsertype;
    if (!$user->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
//          \Phalcon\DI::getDefault()->get('db')->rollback();
    \Phalcon\DI::getDefault()->get('db')->commit();
    return $user;
  }

  public function verifyUser($params) {
    $verifyUser = $this->modelsManager->createBuilder()
            ->from("User")
            ->join("Usertype")
            ->where("User.email = :email:")
            ->andWhere("Usertype.name = :role:")
            ->getQuery()
            ->getSingleResult($params);
    if ($verifyUser) {
      throw new \InvalidArgumentException("Ya existe un usuario con email <b>{$verifyUser->email}</b> y tipo de cuenta <b>{$verifyUser->Role->name}<b>");
    }
  }

}
