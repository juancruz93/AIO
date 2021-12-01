<?php

namespace Sigmamovil\Wrapper;

use MongoDB\Driver\Query;
use Psr\Log\InvalidArgumentException;

ini_set("auto_detect_line_endings", true);
ini_set('memory_limit', '768M');

require_once(__DIR__ . "/../bootstrap/index.php");
include_once(__DIR__ . "/../../library/phpexcel/Classes/PHPExcel.php");
include_once(__DIR__ . "/../../library/phpexcel/Classes/PHPExcel/Writer/Excel2007.php");


$data = 0;
//arg el arreglo completo viene asi ["\/websites\/aio\/app\/bgprocesses\/contacts\/Export.php","3569,,santiago.cardona@sigmamovil.com.co"]
if (isset($argv[1])) {
  $data = $argv[1];
}

$export = new Export();

$export->exportStart($data);

class Export {

  public function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }
  
  private $whereTypeExport;
  private $idContactlist;
  private $email;
  private $idAccount;
  private $arrTitles = array("Correo","Nombre(s) y apellido(s)","Telefono","Fecha de Nacimiento","Estado");
  private $idExportlcdetail;

  public function exportStart($data) {
    try {
        $str = explode(",",$data);
        $this->idContactList = $str[0];
        $this->whereTypeExport = $str[1];
        $this->email = $str[2];
        $this->idAccount = $str[3];
        //Llamamos a la function findIdContact para extraer los campos primarios de los contactos en la LC
        $contacsTotal = $this->findIdContact($this->idContactList);
        if($this->idAccount == 49 || $this->idAccount == 325 ){
            foreach($contacsTotal as $value){
            	$w["idContact"] = $value->idContact;
            	$p = \Mxc::findfirst([$w,'sort' => ["scheduleDate" => -1]]);
                if(!isset($p->scheduleDate)){
                    $value->scheduleDate = "";
                }else{
                    $value->scheduleDate = $p->scheduleDate;
                }
            	
            }
            array_push($this->arrTitles, "Ultimo Envio Realizado");
        }
        $customfiels = \Customfield::find(array("conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => array(0 => $this->idContactList)));
        if($customfiels){
            //FUNCION PARA AGREGAR LOS TITULOS DE LOS CP EN EL ARREGLO DE TITULOS
            $this->setTittleCP($customfiels);
            foreach($contacsTotal as $valueCT){
                foreach($customfiels as $valueCP){
                    //BUSCAMOS EL CAMPO PERSONALIZADO EN CXC
                    $cxc = \Cxc::findFirst([["idContact" => $valueCT->idContact]]);
                    if($cxc){
                        //PREGUNTAMOS SI ESE CONTACTO TIENE LA LISTA DE CONTACTOS RELACIONADA EN CXC
                        if(isset($cxc->idContactlist[$this->idContactList])){
                            //PREGUNTAMOS SI LA LISTA DE CONTACTOS TIENE EL ID DE CAMPO PERSONALIZADO RELACIONADO
                            if(isset($cxc->idContactlist[$this->idContactList][$valueCP->idCustomfield])){
                                //SI ESTA RELACIONADO EXTRAEMOS EL VALOR Y EL TITULO DEL CAMPO PERSONALIZADO
                                $titleCP = $cxc->idContactlist[$this->idContactList][$valueCP->idCustomfield]["name"];
                                $valueCP = $cxc->idContactlist[$this->idContactList][$valueCP->idCustomfield]["value"];
                                //Y LO AGREGAMOS EN EL ARREGLO DE contacsTotal
                                $valueCT->$titleCP = $valueCP;
                            }
                        }
                    }
                }
                //OBTENER EL ESTADO DEL CONTACTO Y AGREGARLO AL ARREGLO DE contactsTotal
                $cxcl = \Cxcl::findFirst(array("conditions" => "idContact = ?0","bind" => array(0 => (int) $valueCT->idContact)));
                $valueCT->status = $this->setStatus($cxcl->status);
            }
        }
        $this->processFile($contacsTotal, $this->arrTitles);
        $this->sendMailNotificationCSV();
        return true;
    } catch (InvalidArgumentException $ex) {
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\Exception $ex) {
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  public function findIdContact($idContactlist) {
    //Consulta para traer el IdContact de la tabla cxcl
    $conditions = array("columns" => "idContact", "conditions" => "idContactlist = ?0 AND deleted = ?1 ".$this->whereTypeExport, "bind" => array($idContactlist, 0));
    $contacts = \Cxcl::find($conditions);
    unset($conditions);
    //Creamos un Array
    $arrayIdContacts = [];
    //recorremos nuestro Array vacio que le asiganos el Array donde este el campo idContact
    foreach ($contacts as $contact) {
      $arrayIdContacts[] = (int) $contact['idContact'];
      unset($contacts);
    }
    //Hacemos una consulta para traer todos los contactos que estan es su respetiva contactlist
    $contacsTotal = \Contact::find([array(
      "idContact" => ['$in' => $arrayIdContacts],
      "deleted" => 0,
      ),
      "fields" => array(
        "idContact" => true,
        "email" => true,
        "name" => true,
        "lastname" => true,
        "indicative" => true,
        "phone" => true,
        "birthdate" => true
      )
    ]);
    //Le asignamos a la variable la function findContact y pasamos los datos del contacto y el id de Contactlist
    $contact = $contacsTotal;
    unset($contacsTotal);
    unset($idContactlist);;
    //Retornamos un query de Contact
    return $contact;
  }
  
  public function setTittleCP($customfiels) {
    foreach($customfiels as $valueCP){
        array_push($this->arrTitles, $valueCP->name);
    }
  }
  
  public function setStatus($status) {
    switch ($status) {
        case 'active':
          $newStatus = "Activo";
          break;
        case 'unsubscribed':
          $newStatus = "Desuscrito";
          break;
        case 'bounced':
          $newStatus = "Rebotado";
          break;
        case 'spam':
          $newStatus = "Spam";
          break;
        case 'blocked':
          $newStatus = "Bloqueado";
          break;                
      }
      return $newStatus;
  }
  
  public function processFile($contacsTotal, $arrTitles) {
    
    //Consulta para traer la informacion de la lista de contactos
    $contactlist = \Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($this->idContactList)));
    if (!$contactlist) {
      throw new \InvalidArgumentException("No se encontró la lista de contactos {$this->idContactlist}, por favor valide la información.");
    }
    $exportlcdetail = new \Exportlcdetail();
    $exportlcdetail->idContactlist = $this->idContactList;
    
    $route2 =  \Phalcon\DI::getDefault()->get('path')->path . "public/tmp/exportLC/".$contactlist->idContactlist."_".date('Y-m-d').".csv";
    $file = fopen($route2, "w");
    $exportlcdetail->fileName = $contactlist->idContactlist."_".date('Y-m-d').".csv";
    $exportlcdetail->route = $route2;
    unset($route2);
    
    $titulos = 1;
    $separador = ";";
    
    foreach ($this->arrTitles as $valueTitle) {
        fputs($file, utf8_decode($valueTitle) . $separador);
    }
    fputs($file, "\r\n");
    foreach ($contacsTotal as $value) {
      foreach ($this->arrTitles as $valueTitle) {
        if($valueTitle == "Correo"){
            fputs($file, (string) $value->email==null||""? "".$separador:$value->email. $separador);
        }else if($valueTitle == "Nombre(s) y apellido(s)"){
            $name = $value->name==null||""? "":$value->name;
            $lastName = $value->lastname==null||""? "":$value->lastname;
            fputs($file, (string) $name." ".$lastName.$separador);
        }else if($valueTitle == "Telefono"){
            fputs($file, (string) $value->phone==null||""? "".$separador:$value->phone. $separador);
        }else if($valueTitle == "Fecha de Nacimiento"){
            fputs($file, (string) $value->birthdate==null||""? "".$separador:$value->birthdate. $separador);
        }else if($valueTitle == "Estado"){
            fputs($file, (string) $value->status==null||""? "".$separador:$value->status. $separador);
        }else if($valueTitle == "Ultimo Envio Realizado"){
            fputs($file, (string) $value->scheduleDate==null||""? "".$separador:$value->scheduleDate. $separador);
        }else{
            fputs($file, (string) $value->$valueTitle==null||""? "".$separador:$value->$valueTitle. $separador);
        }
        
      }
      fputs($file, "\r\n");
      unset($value);
    }
    fclose($file);
    unset($file);
    
    $exportlcdetail->emailNotificacion = $this->email;
    $exportlcdetail->created = time();
    if (!$exportlcdetail->save()) {
      foreach ($exportlcdetail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $this->idExportlcdetail = $exportlcdetail->idExportlcdetail;
  }
  
  public function sendMailNotificationCSV() {
      try {
        
        $contactlist = \Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($this->idContactList)));
        if (!$contactlist) {
          throw new \InvalidArgumentException("No se encontró la lista de contactos {$this->idContactlist}, por favor valide la información.");
        }
        
        $account = \Account::findFirst(array("conditions" => "idAccount = ?0", "bind" => array($this->idAccount)));
        $dir =  \Phalcon\DI\FactoryDefault::getDefault()->get("urlManager")->get_base_uri(true);
        $link = $dir."contact/downloadlc/".$this->idExportlcdetail;
        //Objeto que guardara la informacion de envio de correo
        $data = new \stdClass();
  
        //Datos del correo
        $data->fromEmail = "noreply@sigmamovil.com";
        $data->fromName = "AIO - Exportación de lista de contactos";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificacion de exportacion de Lista de Contactos";
  
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
                . '<td style="word-break: break-word; padding: 15px 15px 0 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
                . 'Estimados '.$account->name.':'
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
                . '<td style="word-break: break-word; padding: 0 15px 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
                . 'Se informa que se ha completado la exportación de la lista de contactos <b>'.$contactlist->name.'</b>, haga clic para descargar el archivo:'
                . '</span></p>'
                . '<p style="text-align: center;"><a href="'.$link.'" target="_blank"><button style="border: none;color: white;padding: 12px 25px;text-decoration: none;font-size: 16px;cursor: pointer;background-color: #e36c09;">Descargar</button></p></td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';
  
        $data->html = str_replace("tmp-url", "prueba", $content);
        $data->plainText = "Se ha enviado una notificacion de exportacion LC CSV.";
        $data->to = $this->email;
  
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