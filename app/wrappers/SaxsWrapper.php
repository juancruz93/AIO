<?php

namespace Sigmamovil\Wrapper;

class SaxsWrapper extends \BaseWrapper {

    public function getall() {
        $validateDKIM = FALSE;
        $idSubAccount = $this->user->Usertype->Subaccount->idSubaccount;
        $DetailConfig = \DetailConfig::findFirst(array("conditions" => "idAccountConfig = ?0 AND idServices = 2", "bind" => array($this->user->Usertype->Subaccount->Account->AccountConfig->idAccountConfig)));
        $DKIM = \Dcxdkim::findFirst(array("conditions" => "idDetailConfig = ?0", "bind" => array($DetailConfig->idDetailConfig)));
        $validateDKIM = (!empty($DKIM)) ? true : false;
        $saxs = \Saxs::find(array("conditions" => "idSubaccount = ?0", "bind" => array($idSubAccount)));

        $data = [];
        if (count($saxs) > 0) {
            foreach ($saxs as $key => $value) {
                $data[$key] = array(
                    "idSaxs" => $value->idSaxs,
                    "idSubaccount" => $value->idSubaccount,
                    "idService" => $value->idServices,
                    //"service"=> ($value->idServices == $this->services->sms) ? 'sms' : 'email',
                    "service" => $value->Services->description,
                    "amount" => $value->amount,
                    "accountingMode" => $value->accountingMode,
                    "status" => $value->status
                );
                if (($value->idServices == 2 || $value->idServices == '2')&& $this->user->Usertype->Subaccount->Account->registerType =='online') {
                    $data[$key]['validatedkim'] = $validateDKIM;
                }
            }
        }

        return $data;
    }

    /**
     * metodo que me permite traer la informacion de cantidad de servicios disponible para un cliente (subaccount)

     * @author Felipe Garcia 
     * @return array
     */
    public function getAvailableServices() {
        $idSubAccount = $this->user->Usertype->Subaccount->idSubaccount;
        $arrayReturn = array();
        //$saxs = \Saxs::find(array("conditions"=>"idSubaccount = ?0","bind"=>array($idSubAccount)));
        $saxssql = "select saxserv.idSubaccount as idSubaccount, saxserv.accountingMode as accountingMode, saxserv.totalAmount as totalAmount, saxserv.amount as Amount, (saxserv.totalAmount - saxserv.amount) as availableAmount, serv.name as serviceName
	from aio.saxs saxserv, aio.services serv
	where saxserv.idServices=serv.idServices 
	and saxserv.idSubaccount = {$idSubAccount}";

        $saxs = $this->db->fetchAll($saxssql);
        if (count($saxs) > 0) {
            foreach ($saxs as $key => $value) {
                $arrayReturn[$key] = array(
                    "idSubaccount" => $value['idSubaccount'],
                    "accountingMode" => $value['accountingMode'],
                    "totalAmount" => $value['totalAmount'],
                    "Amount" => $value['Amount'],
                    "availableAmount" => $value['availableAmount'],
                    "serviceName" => $value['serviceName'],
                );
            }
        }

        return $arrayReturn;
    }
    
    public function saveDetailConfigDKIM($idsubaccount,$domain){
      $domain = strtolower($domain);  
      $subaccount = \Subaccount::findFirst(array("conditions" => "idSubaccount = ?0","bind" => array($idsubaccount))); 
      $DetailConfig = \DetailConfig::findFirst(array("conditions" => "idAccountConfig = ?0 AND idServices = 2", "bind" => array($subaccount->Account->AccountConfig->idAccountConfig)));
      $DKIM = \Dcxdkim::findFirst(array("conditions" => "idDetailConfig = ?0", "bind" => array($DetailConfig->idDetailConfig)));
      if(empty($DKIM)){
        if($this->isAValidDomain($domain)){
         $this->createDKIM($domain, $DetailConfig->idDetailConfig, $subaccount);
        //$this->sendMessageDKIM("sigma._domainkey.dayanatest.com", 'TXT', 'k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC5mB8SNynldLRSvt+KoAB+67SHaSS+ViteB1nF+lTVRlq8dTMPQUTHPORfwUW1glObkGcwIB9gGu2i5s8DIjX8jLN9qBrqrX89F1yM0Nol1yNf5kaIDby3xyNXFXb/VfqkArtrafbKL9DZsAamn/cuyPxulkGyj4kP/vkaeLmuIQIDAQAB', $domain);
         return ["message" => "Se ha creado el registro DKIM exitosamente, por favor revisa tu correo: ".$this->user->email]; 
        }else{
         throw new \InvalidArgumentException("Estimado usuario, el dominio ingresado es inválido");       
        }       
      }else{
        throw new \InvalidArgumentException("Estimado usuario, la cuenta ya tiene configurado un registro DKIM con el dominio: ".$DKIM->domain);   
      } 
           
    }
    
    public function isAValidDomain($domain) {
        $invalidDomains = \Phalcon\DI::getDefault()->get('publicDomain');
        $d = explode('.', $domain);

        foreach ($invalidDomains as $invalidDomain) {
          if ($invalidDomain == strtolower($d[0])) {
            return false;
          }
        }
        return true; 
    }
    
    public function createDKIM($domain, $idDetailconfig, $subaccount){
        //traer la clave de base de datos
        $url = "https://admin:SXqMjD8CoRU6veLy@admin.sigmamovil.com/ga/api/v3/eng/dkim_keys";
        $method ="POST";
        $data = json_encode(array(
          "dkim_key" => array(
            "domain"=> strtolower($domain),
            "selector"=> "sigma",
            "default_for_domain"=> true,
            "key" => array(
              "bits"=> "1024"
            )
          )
        ));

        $response = $this->send_curl( $url, $method, $data);   
        $result = json_decode($response,true);
        if(!empty($result['success']) || count($result['success'])>0){
            $name = $result['data']['dkim_key']['dns']['public_key']['name'];
            $type = $result['data']['dkim_key']['dns']['public_key']['type'];
            $value = $result['data']['dkim_key']['dns']['public_key']['value'];
            $dcxdkim = new \Dcxdkim();
            $dcxdkim->name_public_key = $name;
            $dcxdkim->type_public_key = $type;
            $dcxdkim->value_public_key = $value;
            $dcxdkim->domain = strtolower($domain);
            $dcxdkim->idDetailConfig = $idDetailconfig;
            if (!$dcxdkim->save()) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                foreach ($dcxdkim->getMessages() as $msg) {
                  $this->logger->log("Message: {$msg}");
                  throw new \InvalidArgumentException($msg);
                }
            }
            $this->sendMessageDKIM($name, $type, $value, $domain);
        }else if(!empty($result['error_code']) || count($result['error_code'])>0){            
            throw new \InvalidArgumentException("Estimado usuario, la cuenta ya tiene configurado un registro DKIM con el dominio: ".$domain);      
        }
        
    }
    
    public function send_curl( $uri, $method, $data){
		
        try{
            $ch = curl_init($uri);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: '.strlen($data)));
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $result = curl_exec($ch);

            if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            return $result;

        }catch(Exception $ex){
            return "Error: ".$ex->getMessage();
        }
			
    }
    

      public function sendMessageDKIM($name, $type, $value, $domain){
       
      try {

      $supportEmail = $this->user->email;

      //Objeto que guardara la informacion de envio de correo
      $data = new \stdClass();

      //Datos del correo
      $data->fromEmail = "noreply@sigmamovil.com";
      $data->fromName = "Soporte Sigma Móvil - AIO";
      $data->from = array($data->fromEmail => $data->fromName);
      $data->subject = "Notificación registro DKIM - AIO";

      //Contenido del correo
      $content = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Style-Type" content="text/css"/> <title>U7ci7gm2</title> <style type="text/css"> *{font-family: Arial_TrueType_2,Arial,serif;}font{font-weight: bold;}.content{width: 65%; margin: 0 auto;}img{width:143.2pt;height:55.132pt;}</style> </head> <body> <div> <div style="text-align: center"> <img src="https://aio.sigmamovil.com/images/general/sigma-logo.png" alt="Image_10_0"/> </div><div class="content"><br><font style="font-size: 40px;">Configuración de Registros DKIM '.$domain.' para envío de Correo Electrónico</font><br><br><font>(Personalizado para dominio '.$domain.')</font> <p>Este FAQ está destinado a responder preguntas comunes sobre la configuración de registros DKIM para el envío de correos masivos a través de nuestra plataforma</p><font style="font-size: 20px;">Registros DKIM</font> <p><strong>¿Por qué necesito hacer esto?</strong><br>Usted va a utilizar nuestro sistema de Email Marketing para hacer envío de correo electrónico de forma masiva y quiere que sus campañas sean exitosas.</p><p>Nuestra plataforma se asegura de esto, enviando los correos electrónicos, revisando cuantos han fallado, las razones de la falla, llevando estadísticas de los envíos, los rechazos, los clics, las aperturas, etc. Estos procesos requieren de una capacidad de cómputo, espacio de almacenamiento y velocidad de conexión a Internet que por lo general exceden los recursos de las empresas.</p><p>Con nuestro servicio, usted no debe preocuparse por estos detalles, nuestra infraestructura está diseñada para soportar millones de mensajes al día y nuestro personal constantemente monitorea los procesos de envío.</p><p>Debido a que es nuestro servidor el que se encarga del envío de correos, para que estos puedan ser originados desde direcciones de correo de su dominio, utilizamos mecaismos de autenticación que son consultados por la mayoría de los proveedores de correo, como Hotmail, Yahoo y Gmail. Estos mecanismos requieren que el dominio (el texto que está después del símbolo @ en los correos electrónicos), confirme que nuestro servidor puede enviar mensajes con cuentas registradas en su dominio.</p><p>Esta es la razón para hacer los ajustes que se mencionan más adelante.</p><p><strong>¿Cuáles son los ajustes que debo hacer a mi dominio?</strong><br>Debe adicionar los siguientes tres registros al servidor DNS de su dominio. En este caso se muestra con el dominio “ '.$domain.'”.</p><table cellpadding="0" cellspacing="0" style="width:539.8pt; margin:0 auto; border:0.75pt solid #000000; border-collapse:collapse;"> <tbody> <tr style="height:32.9pt;"> <td style="width:68.5pt; border-right-style:solid; border-right-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><strong><span style="font-family:Arial;">DESCRIPCIÓN REGISTRO&nbsp;</span></strong></p></td><td style="width:49.8pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><strong><span style="font-family:Arial;">TIPO&nbsp;</span></strong></p><p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><strong><span style="font-family:Arial;">REGISTRO DNS&nbsp;</span></strong></p></td><td style="width:94.9pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><strong><span style="font-family:Arial;">UBICACIÓN&nbsp;</span></strong></p></td><td style="width:288.85pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><strong><span style="font-family:Arial;">VALOR&nbsp;</span></strong></p></td></tr><tr style="height:22.3pt;"> <td style="width:68.5pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">SPF&nbsp;</span></p></td><td style="width:49.8pt; border-style:solid; border-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">TXT&nbsp;</span></p></td><td style="width:94.9pt; border-style:solid; border-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">Raíz del dominio ( '.$domain.')&nbsp;</span></p></td><td style="width:288.85pt; border-top-style:solid; border-top-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">"v=spf1 include:admin.sigmamovil.com ~all"&nbsp;</span></p></td></tr><tr style="height:22.1pt;"> <td style="width:68.5pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">DKIM politica&nbsp;</span></p></td><td style="width:49.8pt; border-style:solid; border-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">TXT&nbsp;</span></p></td><td style="width:94.9pt; border-style:solid; border-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">_domainkey. '.$domain.'&nbsp;</span></p></td><td style="width:288.85pt; border-top-style:solid; border-top-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">o=~&nbsp;</span></p></td></tr><tr style="height:65.3pt;"> <td style="width:68.5pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">DKIM llave pública&nbsp;</span></p></td><td style="width:49.8pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-left:0.25pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;">TXT&nbsp;</span></p></td><td style="width:94.9pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;"> '.$name.'&nbsp;</span></p></td><td style="width:288.85pt; border-top-style:solid; border-top-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-top:1.48pt; padding-right:3.58pt; padding-left:4.92pt; vertical-align:top;"> <p style="margin-top:0pt; margin-bottom:0pt; line-height:108%; font-size:9pt;"><span style="font-family:Arial;"> '.$value.'</span></p></td></tr></tbody> </table><p>Cada una de las filas de la tabla es un registro para el servidor DNS, <strong style="font-style: italic;">NO debe tener saltos de línea ni caracteres especiales.</strong> En el caso del registro default._domainkey, hay un espacio entre “k=rsa;” y “p=...”.</p><p>Los tres registros son de tipo TXT. El TTL de los registros puede ser igual al utilizado en otros registros DNS de su dominio.</p><p>Si usted ya tiene registros TXT para alguno de estos tres casos, tendremos que revisar su caso particular y le daremos los registros específicos que debe aplicar.</p><p><strong>¿En dónde debo hacer este cambio?</strong><br>Los cambios o ajustes aquí descritos se deben aplicar al servidor DNS de su dominio. Estos cambios no van a alterar el funcionamiento de sus servicios de DNS y/o de correo.</p><p><strong>¿Quién debe hacer los ajustes?</strong><br>Estos cambios deben ser hechos por el administrador de su servidor de nombres de dominio. Muchas veces es la empresa que provee el hosting de su sitio web, o es su área de tecnología.</p><p><strong>No sé hacer estos cambios, o no sé quién me los pueda hacer</strong><br>Si usted no está seguro de quien le puede hacer los cambios a estos registros, nosotros podemos asistirlo. Si su área de tecnología tiene preguntas sobre este tema, también podemos apoyarlo en resolver cualquier duda que tenga.</p><p><strong>¿Dónde puedo encontrar más información sobre esto?</strong><br>Usamos dos mecanismos diferentes de autenticación de dominios.</p><p><strong>DKIM - DomainKeys Identified Mail</strong><br>Puede encontrar información aquí: <a href="http://www.dkim.org/" target="_blank">http://www.dkim.org/</a></p><p><strong>SPF - Sender Policy Framework</strong><br>Mas información en la página: <a href="http://www.openspf.org/" target="_blank">http://www.openspf.org/</a></p><p>Si tiene cualquier otra pregunta acerca de su cuenta, por favor contacte con nuestro equipo de asistencia en <span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;font-size:150%"><b>soporte@sigmamovil.com.co</b></span></p></div></div></div></body></html>';
      $data->html = str_replace("tmp-url", "prueba", $content);
      $data->plainText = "Se ha enviado una notificacion de dkim";

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
