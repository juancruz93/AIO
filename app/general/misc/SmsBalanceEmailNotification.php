<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\General\Misc;

/**
 * Description of SmsBalanceEmailNotification
 *
 * @author juan.pinzon
 */
class SmsBalanceEmailNotification {

  public function sendSmsNotification($arraySaxs) {
    try {
      if($arraySaxs["subaccountName"] == '' && $arraySaxs["accountName"] == '' && $arraySaxs["totalAmount"] == 0 && $arraySaxs['amount'] == 0){
        throw new \InvalidArgumentException("El saldo es inválido, por favor valida la información");
      }
      //Correo de soporte al cual llegan las notificaciones de falta de saldo
      $supportEmail = "soporte@sigmamovil.com";

      //Objeto que guardara la informacion de envio de correo
      $data = new \stdClass();

      //Datos del correo
      $data->fromEmail = "desarrollo@sigmamovil.com";
      $data->fromName = "Servicio SMS - AIO";
      $data->from = array($data->fromEmail => $data->fromName);
      $data->subject = "Notificación de saldo SMS";

      //Contenido del correo
      $content = '<table style="background-color: #E6E6E6; width: 100%;">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding: 20px;"><center>'
              . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody></tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
              . 'Estimado equipo de Desarrollo:'
              . '</span></h3>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
              . 'Se informa que la subcuenta  <b>' . $arraySaxs["subaccountName"] . '</b> , de la cuenta <b>'. $arraySaxs["accountName"] .'</b> , acaba de intentar realizar un envío de SMS pero no cuenta con saldo suficiente. En el momento dispone de </b>' . $arraySaxs["amount"] . '</b> mensajes de texto, cuyo límite total es </b>' . $arraySaxs["totalAmount"] . "</b>."
              . '</span></p>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">desarrollo.tics@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Se ha enviado una notificacion de saldo de SMS.";

      $data->to = $supportEmail;

      $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification SMS balance: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }
  
  public function sendMailNotification($arraySaxs) {
    try {
      if($arraySaxs["subaccountName"] === '' && $arraySaxs["accountName"] === '' && $arraySaxs["totalAmount"] === ''){
        throw new \InvalidArgumentException("El saldo es inválido, por favor valida la información");
      }
      //Correo de soporte al cual llegan las notificaciones de falta de saldo
      $supportEmail = "desarrollo.tics@sigmamovil.com.co";

      //Objeto que guardara la informacion de envio de correo
      $data = new \stdClass();

      //Datos del correo
      $data->fromEmail = "desarrollo@sigmamovil.com";
      $data->fromName = "Servicio Mail - AIO";
      $data->from = array($data->fromEmail => $data->fromName);
      $data->subject = "Notificación de saldo Mail";

      //Contenido del correo
      $content = '<table style="background-color: #E6E6E6; width: 100%;">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding: 20px;"><center>'
              . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody></tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
              . 'Estimado equipo de Desarrollo:'
              . '</span></h3>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
              . 'Se informa que la subcuenta  <b>' . $arraySaxs["subaccountName"] . '</b> , de la cuenta <b>'. $arraySaxs["accountName"] .'</b> , acaba de intentar realizar un envío de Mail pero no cuenta con saldo suficiente. En el momento dispone de </b>' . $arraySaxs["amount"] . '</b> envios de mail, cuyo límite total es </b>' . $arraySaxs["totalAmount"] . "</b>."
              . '</span></p>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">desarrollo.tics@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Se ha enviado una notificacion de saldo de Mail.";

      $data->to = $supportEmail;


      $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification Mail balance: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }
  
  public function sendMailNotificationCanceled($mail) {
    try {

      //Correo de soporte al cual llegan las notificaciones de falta de saldo
      $supportEmail = "desarrollo.tics@sigmamovil.com.co";

      //Objeto que guardara la informacion de envio de correo
      $data = new \stdClass();

      //Datos del correo
      $data->fromEmail = "desarrollo@sigmamovil.com";
      $data->fromName = "Servicio Cancelacion - AIO";
      $data->from = array($data->fromEmail => $data->fromName);
      $data->subject = "Notificación de Cancelacion de Campaña";

      //Contenido del correo
      $content = '<table style="background-color: #E6E6E6; width: 100%;">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding: 20px;"><center>'
              . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody></tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
              . 'Estimado equipo de Desarrollo:'
              . '</span></h3>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '<tr>'
              . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
              . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
              . '<tbody>'
              . '<tr>'
              . '<td style="padding-left: 0px; padding-right: 0px;">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
              . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
              . '<tbody>'
              . '<tr>'
              . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
              . '<p></p>'
              . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
              . 'Se informa que la subcuenta  <b>' . $mail->Subaccount->name . '</b> , de la cuenta <b>'. $mail->Subaccount->Account->name .'</b> , la campaña </b>' . $mail->idMail .'</b> nombre  </b>' . $mail->name . '</b> se ha cancelado, esta campaña tiene </b>' . $mail->quantitytarget . " destinatarios</b>."
              . '</span></p>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">desarrollo.tics@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Se ha enviado una notificacion de saldo de SMS.";

      $data->to = $supportEmail;


      $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification SMS balance: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }
  
}
