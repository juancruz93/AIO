<?php

namespace Sigmamovil\General\Misc;

require_once(__DIR__ . "/../../library/swiftmailer/lib/swift_required.php");

class MtaSender {

  protected $swift;
  protected $dataSend;
  protected $transport;

  public function __construct($address, $port) {
    $this->transport = \Swift_SmtpTransport::newInstance($address, $port);
    $this->swift = \Swift_Mailer::newInstance($this->transport);
  }
  
  /**
   * 
   * @param Object $data
   * $data = {
   *      subject: String,
   *      from: Array,
   *      html: String,
   *      to: Array,
   *      replyto : String, (Optional)
   * }
   */
  public function setDataMessage($data) {
    $this->dataSend = $data;
  }

  public function sendMail() {
    $message = new \Swift_Message($this->dataSend->subject);
    $message->setFrom($this->dataSend->from);
    $message->setBody($this->dataSend->html, 'text/html');
    $message->setTo($this->dataSend->to);
    if (isset($this->dataSend->replyto) && !empty($this->dataSend->replyto) && $this->dataSend->replyto != "") {
      $message->setReplyTo($this->dataSend->replyto);
    }
    $recipients = $this->swift->send($message, $failures);
  }

}
