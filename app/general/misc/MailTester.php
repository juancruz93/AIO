<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\General\Misc;

class MailTester {

  protected $nameUserMailTester,
          $idMailTester,
          $timeMailTester,
          $configMailTester,
          $domainConfigMailTester;

  public function __construct() {
    $this->configMailTester = \Phalcon\DI::getDefault()->get('mail_tester');
    $this->nameUserMailTester = $this->configMailTester->nameuser;
    $this->domainConfigMailTester = "@mail-tester.com";
  }

  public function generateMailTester($idMailTester) {
    $returnMailTester;
    $this->timeMailTester = time();
    $this->idMailTester = $idMailTester;
    $returnMailTester = $this->nameUserMailTester . "-" . $this->idMailTester . "-" . $this->timeMailTester . $this->domainConfigMailTester;
    return $returnMailTester;
  }

  public function getTime() {
    return $this->timeMailTester;
  }

  public function getUser() {
    return $this->nameUserMailTester;
  }

  public function getId() {
    return $this->idMailTester;
  }
  
  /**
   * 
   * @param object $Allied
   * @return \stdClass
   */

  public function getTemplateHtml($Allied) {
    
    $systemMail = \Systemmail::findFirst(array(
                'conditions' => 'category = ?0 and idAllied = ?1',
                'bind' => array(0 => 'mail-tester', 1 => $Allied->idAllied)
    ));
    
    $data = new \stdClass();

    if ($systemMail) {
      $data->fromName = $systemMail->fromName;
      $data->fromEmail = $systemMail->fromEmail;
      $data->subject = $systemMail->subject;

      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->assignContent(json_decode($systemMail->content));
      $content = $editorObj->render();

      $data->html = $content;
      $data->plainText = $systemMail->plainText;
      $data->from = array($systemMail->fromEmail => $systemMail->fromName);
    } else {
      $data->fromEmail = $Allied->email;
      $data->fromName = $Allied->name;
      $data->from = array($Allied->email => $Allied->name);
      $data->subject = "Notificación de envío de correo electrónico";
      $content = '<body>'
              .'<table style="background-color: #E6E6E6; width: 100%;">'
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
              . 'Se le informa que se ha enviado el correo electrónico <b> sasadasdasd </b> satisfactoriamente en la fecha <b>sadddsasda</b>'
              . '</span></p>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table>'
              . '</td>'
              . '</tr>'
              . '</tbody>'
              . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>'
              . '</body>';

      $data->html = $content;
      $data->plainText = "Se ha enviado un correo electronico.";
    }
    return $data;
  }

}
