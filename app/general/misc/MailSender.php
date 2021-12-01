<?php

namespace Sigmamovil\General\Misc;

class MailSender
{
    public $session;
    public $logger;
    public $data;
    public $html;
    public $plainText;
    
    public function __construct() 
    {
        $this->session = \Phalcon\DI::getDefault()->get('session');
        $this->logger = \Phalcon\DI::getDefault()->get('logger');
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function setHtml($html)
    {
        $this->html = $html;
    }
    
    public function setPlainText($plainText)
    {
        $this->plainText = $plainText;
    }
    
    public function sendBasicMail()
    {
        $headers = "From: {$this->data->fromName} <{$this->data->fromEmail}> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        foreach ($this->data->target as $to) {
            $sent = mail($to, $this->data->subject,  $this->html, $headers);
            if (!$sent) {
                $this->logger->log("El correo con destino a {$to} no pudo ser envíado");
                $this->logger->log("Subject: {$this->data->subject}");
                $this->logger->log("Headers: {$headers}");
            }
        }
    }
}
