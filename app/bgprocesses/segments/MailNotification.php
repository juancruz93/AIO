<?php

use \Sigmamovil\General\Misc\MtaSender;

/**
 * Description of mailNotification
 *
 * @author juan.pinzon
 */
class MailNotification {

  private $mtaSender;
  private $content;
  private $subject;
  private $addressee;

  public function __construct($addressee) {
    $this->addressee = $addressee;
    $this->mtaSender = new MtaSender(\Phalcon\DI\FactoryDefault::getDefault()->get("mtadata")->address, \Phalcon\DI\FactoryDefault::getDefault()->get("mtadata")->port);
  }

  public function getMtaSender() {
    return $this->mtaSender;
  }
  
  public function getContent() {
    return $this->content;
  }
  
  public function getSubject() {
    return $this->subject;
  }
  
  public function getAddressee() {
    return $this->addressee;
  }
  
  public function setContent($content) {
    $this->content = $content;
  }

  public function setSubject($subject) {
    $this->subject = $subject;
  }
  
  public function sendNotification() {    
    $data = new stdClass();
    $data->subject = $this->getSubject();
    $data->from = "soporte@sigmamovil.com";
    $data->html = $this->template($this->getContent());
    $data->to = $this->getAddressee();
    
    $this->getMtaSender()->setDataMessage($data);
    $this->getMtaSender()->sendMail();
  }

  public function template($content) {
    $template = '<table style="background-color: #E6E6E6; width: 100%;">'
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
            . '<p>'
            . '<span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
            . "{$content}"
            . '</span></p>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody>'
            . '</table>';

    return $template;
  }

}
