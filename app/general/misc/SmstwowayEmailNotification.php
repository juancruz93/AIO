<?php
//require_once(__DIR__ . "/../../bootstrap/index.php");
namespace Sigmamovil\General\Misc;

/**
 * Description of SmstwowayEmailNotification
 *
 * @author juan.pinzon
 */
class SmstwowayEmailNotification {
  
  public function sendMailNotification($smsTwoWay) {
    try {
      $idAllied = $smsTwoWay->Subaccount->Account->idAllied;
      $mail = \Systemmail::findFirst(array(
                  'conditions' => 'category = ?0 and idAllied = ?1',
                  'bind' => array(0 => 'smstwoway-finished', 1 => $idAllied)
      ));
      $data = new \stdClass();
      if ($mail) {
        $data->fromName = $mail->fromName;
        $data->fromEmail = $mail->fromEmail;
        $data->from = array($mail->fromEmail => $mail->fromName);
        $data->subject = $mail->subject;

        $mail->content = str_replace("%NAME_SENT%", $smsTwoWay->name, $mail->content);
        $mail->content = str_replace("%DATETIME_SENT%", $smsTwoWay->startdate, $mail->content);
        $mail->content = str_replace("%TOTAL_SENT%", $smsTwoWay->sent, $mail->content);

        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($mail->content));
        $content = $editorObj->render();

        $data->html = str_replace("tmp-url", $url, $content);
        $data->plainText = $mail->plainText;
      } else {
        $data->fromEmail = (isset($smsTwoWay->Subaccount->Account->Allied->email)) ? $smsTwoWay->Subaccount->Account->Allied->email : "soporte@sigmamovil.com";
        $data->fromName = (isset($smsTwoWay->Subaccount->Account->Allied->name)) ? $smsTwoWay->Subaccount->Account->Allied->name : "soporte Sigmamovil";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificación de envío de SMS doble-via";
        $target = json_decode($smsTwoWay->receiver);

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
                . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
                . 'Estimado usuario:'
                . '</span></h2>'
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
                . 'Se le informa que se ha enviado el envío de SMS doble vía <b>' . $smsTwoWay->name . '</b> se realizó satisfactoriamente en la fecha <b>' . $smsTwoWay->startdate . "h</b>, con ". $smsTwoWay->sent ." mensajes enviados en total."
                . '</span></p>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

        $data->html = str_replace("tmp-url", "prueba", $content);
        $data->plainText = "Se ha enviado un SMS doble-via.";
      }
      $to = [];
      if (isset($smsTwoWay->email) && $smsTwoWay->email != "") {
        $email = explode(",", trim($smsTwoWay->email));
        foreach ($email as $key) {
          array_push($to, trim($key));
        }
      }
      $data->to = array_unique($to);


      $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating masteraccount: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }
  
  //put your code here
}
