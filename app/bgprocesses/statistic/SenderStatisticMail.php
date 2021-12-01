<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$mailSender = new SenderStatisticMail();

$mailSender->startSender($id);

class SenderStatisticMail {

  public function __construct() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->services = $di->get('services');
    $this->mta = $di->get('mta');
  }

  public function startSender($idMailStatisticNotification) {
    try {

      $msn = \MailStatisticNotification::findFirst(array(
                  'conditions' => 'idMailStatisticNotification= ?0',
                  'bind' => array(0 => $idMailStatisticNotification)
      ));

      $mail = \Mail::findFirst(array(
                  'conditions' => 'idMail= ?0',
                  'bind' => array(0 => $msn->idMail)
      ));
      $idAllied = $mail->Subaccount->Account->idAllied;
      $systemMail = \Systemmail::findFirst(array(
                  'conditions' => 'category= ?0 AND deleted=0 AND idAllied=?1',
                  'bind' => array(0 => 'statistic-notification', $idAllied)
      ));

      if ($msn) {

        $subaccount = \Subaccount::findFirst(array(
                    'conditions' => 'idSubaccount= ?0',
                    'bind' => array(0 => $msn->idSubaccount)
        ));
        $account = \Account::findFirst(array(
                    'conditions' => 'idAccount= ?0',
                    'bind' => array(0 => $subaccount->idAccount)
        ));

        $data = new stdClass();

        if ($systemMail) {
          $data->fromName = $systemMail->fromName;
          $data->fromEmail = $systemMail->fromEmail;
          $data->from = array($systemMail->fromEmail => $systemMail->fromName);
          $data->subject = $systemMail->subject;
          $systemMail->content = str_replace("%NAME_SENT%", $mail->name, $systemMail->content);
          $systemMail->content = str_replace("%DATETIME_SENT%", $mail->scheduleDate, $systemMail->content);
          $systemMail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLink($msn->idMail, $msn->idSubaccount, "complete"), $systemMail->content);
          $systemMail->content = str_replace("%LINK_SUMMARY_SENT%", $this->encodeLink($msn->idMail, $msn->idSubaccount, "summary"), $systemMail->content);
          $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
          $editorObj->setAccount(null);
          $editorObj->assignContent(json_decode($systemMail->content));
          $content = $editorObj->render();
          $data->html = $content;
          $data->plainText = $systemMail->plainText;
        } else {
          $data->fromName = $account->name;
          $data->fromEmail = $account->email;
          $data->from = array($account->email => $account->name);

          $data->subject = "Estadísticas de envío de email";
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
                  . 'A continuación encontrará las estadísticas del envío de nombre <i>' . $mail->name . '</i> y cuya fecha de envío fue <i>' . $mail->scheduleDate . 'h</i>. Puede acceder '
                  . 'a las estadísticas completas haciendo clic <a href="' . $this->encodeLink($msn->idMail, $msn->idSubaccount, "complete") . '"><b>aquí</b></a> '
                  . 'o bien puede acceder a las estadísticas parciales haciendo clic <a href="' . $this->encodeLink($msn->idMail, $msn->idSubaccount, "summary") . '"><b>aquí.</b></b></a><br> '
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
          $data->plainText = "Se ha enviado un correo electronico.";
        }

        $email = explode(",", trim($msn->target));
        $to = [];
        foreach ($email as $key) {
          array_push($to, trim($key));
        }
        $data->to = $to;
      }
      $msn->status = 'sending';
      if (!$msn->save()) {
        foreach ($mail->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      //\Phalcon\DI::getDefault()->get('logger')->log(print_r($data, true));
      $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);

      $mtaSender->setDataMessage($data);
      $mtaSender->sendMail();
      $msn->status = 'sent';
      if (!$msn->save()) {
        foreach ($mail->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
      //return $this->set_json_response($mtaSender, 200);
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
//      $this->trace("fail", $e->getTraceAsString());
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while creating sendMailNotificatio: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
//      $this->notification->error($e->getMessage());
    }
  }

  public function encodeLink($idMail, $idSubaccount, $type) {
    $src = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true) . 'statistic/share/1-' . $idMail . "-" . $idSubaccount . "-" . $type;
    return $src . '-' . md5($src . '-Sigmamovil_Rules');
  }

}