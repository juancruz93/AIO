<?php

namespace Sigmamovil\Wrapper;

class SessionWrapper extends \BaseWrapper {

  private $hash;
  private $session;
  private $nameRoles;

  public function __construct() {
    parent::__construct();
    $this->hash = \Phalcon\DI::getDefault()->get('hash');
    $this->session = \Phalcon\DI::getDefault()->get('session');
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
    $this->roles = \Phalcon\DI::getDefault()->get('roles');
  }

  public function login($data) {
    if (empty($data->email)) {
      throw new \InvalidArgumentException("El campo de email es obligatorio");
    }

    //Variable de usuario para verificar si se registro por social networks
    $usersn = \User::findFirst(array(
                "columns" => "registerType",
                "conditions" => "email = ?0 AND idRole = ?1",
                "bind" => array($data->email, $this->roles->subaccount)
    ));

    if ($usersn) {
      if ($usersn->registerType != "aio") {
        throw new \InvalidArgumentException("Inicio de sesión inválida");
      }
    }

    $count = \User::count(array(
                "columns" => "email",
                "conditions" => "email = ?0",
                "bind" => array($data->email)
    ));

    if ($count > 0) {
      $res = $this->accountSubaccount($data->email);
      if (is_array($res)) {
        return $res;
      }
    }

    $this->session->set("mode", false);

    $roles = array();
    if ($count < 1) {
      throw new \InvalidArgumentException("Usuario inválido, no esta registrado en la plataforma");
    } elseif ($count > 1) {
      $user = \User::find(array(
                  "conditions" => "email = ?0",
                  "bind" => array($data->email)
      ));
      foreach ($user as $key => $value) {
        $roles[$key] = array(
            "idRole" => $value->Role->idRole,
            "name" => $value->Role->nameForView
        );
      }
      return array(
          "email" => $data->email,
          "roles" => $roles,
          "verified" => true
      );
    } else {
      $user = \User::findFirst(array(
                  "columns" => "email",
                  "conditions" => "email = ?0",
                  "bind" => array($data->email)
      ));
      return array(
          "email" => $user->email,
          "verified" => true
      );
    }
  }

  public function loginPass($data) {
    if (empty($data->email)) {
      throw new \InvalidArgumentException("El campo correo no ha sido ingresado correctamente");
    }
    if (empty($data->password)) {
      throw new \InvalidArgumentException("El campo contraseña no puede estar vacío");
    }
    if (!isset($data->rol)) {
      throw new \InvalidArgumentException("Debe seleccionar un rol");
    }


    if (!empty($data->rol)) {
      $conditions = array(
          "conditions" => "email = ?0 AND idRole = ?1",
          "bind" => array($data->email, $data->rol)
      );
    } else {
      $conditions = array(
          "conditions" => "email = ?0",
          "bind" => array($data->email)
      );
    }
    $user = \User::findFirst($conditions);

    if ($user && $this->hash->checkHash($data->password, $user->password)) {
      $this->session->set('idUser', $user->idUser);
      $this->session->set('authenticated', true);
      return array("status" => "authorized");
    } else {
      throw new \InvalidArgumentException("La clave es inválida");
    }
  }

  public function accountSubaccount($email) {
    $user = \User::find(array(
                "conditions" => "email = ?0",
                "bind" => array($email)
    ));

    $cont = 0;
    $flag = true;
    $role = "";
    foreach ($user as $us) {        
      if ($us->Role->name == $this->nameRoles->account) {
        $subaccount = $us;
        $role = $us->Role->name;
          $cont++;
      } elseif ($us->Role->name == $this->nameRoles->subaccount) {
        $role = $us->Role->name;
        $subaccount = $us;
        $cont++;
      } else {
        $flag = false;
      }
    }

    if ($flag && $cont > 0) {
      $this->validateStatus($subaccount,$role);
      $this->session->set("mode", "advanced");
      return array(
          "email" => $subaccount->email,
          "rol" => $subaccount->idRole,
          "verified" => true
      );
    }

    return false;
  }

  public function loginWithSocialNetworks($data) {
    $user = \User::findFirst(array(
                "conditions" => "idRole = ?0 AND email = ?1",
                "bind" => array($this->roles->subaccount, $data->email)
    ));

    if (!$user) {
      return array("status" => "notregistered");
    }

    if ($user->Usertype->Subaccount->status == 0 || $user->Usertype->Subaccount->Account->status == 0) {
      throw new \InvalidArgumentException("Tu cuenta no ha sido activada, por favor verifica tu correo");
    }
    
    if($user->deleted != 0){
       throw new \InvalidArgumentException("El usuario no existe o se encuentra eliminado"); 
    }

    switch ($user->registerType) {
      case "facebook":
        $password = $data->id;
        break;
      case "google":
        $password = $data->id;
        break;
      default:
        $password = "";
        break;
    }

    $this->accountSubaccount($user->email);

    if ($user && $this->hash->checkHash($password, $user->password)) {
      if (!isset($user->idCity)) {
        $this->session->set("parcialUser", $user);
        return array("status" => "completeprofile");
      }
      $this->session->set('idUser', $user->idUser);
      $this->session->set('authenticated', true);
      return array("status" => "authorized");
    } else {
      throw new \InvalidArgumentException("La clave es inválida");
    }
  }

  public function validateStatus($user,$role) {
//    if ($user->Usertype->Subaccount->status == 0 || $user->Usertype->Account->status == 0) {
//      throw new \InvalidArgumentException("La Cuenta o Subcuenta a la que intenta ingresar no está activa o verificada");
//    }
    if($role == 'account' && $user->Usertype->Account->status == 0){
        throw new \InvalidArgumentException("La cuenta a la que intenta ingresar no está activa o verificada");
    }else if($role == 'subaccount' && $user->Usertype->Subaccount->status == 0){
        throw new \InvalidArgumentException("La subcuenta a la que intenta ingresar no está activa o verificada");
    }
    if($user->deleted != 0){
       throw new \InvalidArgumentException("El usuario no existe o se encuentra eliminado"); 
    }
  }

  public function recoverpass($data) {

    $user = \User::find(array(
                'conditions' => 'email = ?0 AND idRole = ?1 AND deleted = 0',
                'bind' => array(0 => $data->email, 1 => $data->rol)
    ));

    if ($user) {

      $cod = uniqid();


      $url = $this->urlManager->get_base_uri(true);
      $url .= 'session/resetpassword/' . $cod;


      $recoverObj = new \Tmprecoverpass();
      $recoverObj->idTmprecoverpass = $cod;
      $recoverObj->idUser = $user[0]->idUser;
      $recoverObj->url = $url;
      $recoverObj->date = time();

      if (!$recoverObj->save()) {
        foreach ($recoverObj->getMessages() as $msg) {
          throw new Exception($msg);
        }
      }

      $mail = \Systemmail::findFirst(array(
                  'conditions' => 'category = ?1',
                  'bind' => array(1 => 'recover-password')
      ));




      if ($mail) {
        $data = new \stdClass();
        $data->fromName = $mail->fromName;
        $data->fromEmail = $mail->fromEmail;
        $data->subject = $mail->subject;
        $data->target = array($user[0]->email);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($mail->content));
        $content = $editorObj->render();

        $html = str_replace("tmp-url", $url, $content);
        $plainText = str_replace("tmp-url", $url, $mail->plainText);
      } else {
        $data = new \stdClass();
        $data->fromName = "soporte@sigmamovil.com";
        $data->fromEmail = "Soporte Sigma Móvil";
        $data->subject = "Instrucciones para recuperar la contraseña de Sigma Móvil All In One";
        $data->target = array($user[0]->email);

        $content = '<table style="background-color: #E6E6E6; width: 100%;"><tbody><tr><td style="padding: 20px;"><center><table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0"><tbody><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">Estimado usuario:</span></h2></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Usted ha solicitado recuperar la contrase&ntilde;a de su usuario para ingresar a nuestra plataforma. Para finalizar este proceso, por favor, visite el siguiente enlace:</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;">tmp-url</span></p></td></tr></tbody></table></td></tr></tbody></table></td><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p style="text-align: center;"><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Conoce el paso a paso de c&oacute;mo usar nuestra plataforma All In One.</span></p><p style="text-align: center;"><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"><a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ/playlists">Haz click aqu&iacute;</a></span></p></td></tr></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ning&uacute;n cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

        $html = str_replace("tmp-url", $url, $content);
        $plainText = $url;
      }

      $mailSender = new \Sigmamovil\General\Misc\MailSender();
      $mailSender->setData($data);
      $mailSender->setHtml($html);
      $mailSender->setPlainText($plainText);
      $mailSender->sendBasicMail();
      $info["message"] = "Se ha enviado un correo electrónico con instrucciones para recuperar la contraseña al usuario {$user[0]->name}  {$user[0]->lastname} con email {$user[0]->email}";
      $info["type"] = "success";

      return $info;
    } else {
      throw new \InvalidArgumentException("No se logro recuperar la contraseña del usuario [{$user[0]->name}  {$user[0]->lastname}], [{$user[0]->email}]");
    }
  }

}
