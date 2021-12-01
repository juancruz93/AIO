<?php

class SuggestionController extends ControllerBase
{
    public function indexAction()
    {
        if($this->request->isPost()) {
//            $name = $this->request->getPost('name');
//            $lastname = $this->request->getPost('lastname');
//            $email = $this->request->getPost('email');
//            $suggestions = $this->request->getPost('suggestions');
//            $headers = "From: ".$name." ".$lastname." <".$email."> \r\n";
//            $headers .= "MIME-Version: 1.0\r\n";
//            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//            $txt = '<img src="https://www.sigmamovil.com/comunicacion-digital/img/Logotipo-Original-opt.png" width="270" /><br /><br /><strong>Nombre</strong>: '.$name.' '.$lastname.'<br /><strong>Email</strong>: '.$email.'<br /> <strong>Sugerencias</strong>:'.$suggestions;
//        
//            //Se envia el Email a nuestro correo
//
//            mail(\Phalcon\DI::getDefault()->get("suggestionsDatas")->emailTo,\Phalcon\DI::getDefault()->get("suggestionsDatas")->subject,$txt,$headers);
//
//            $this->notification->success('Sus sugerencias han sido enviadas satisfactoriamente.');
//            $this->trace("success", "Se enviarion sugerencias");
//            return $this->response->redirect('suggestion/index');
        
        }
    }
}
