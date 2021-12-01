<?php

namespace Sigmamovil\Wrapper;

require_once(__DIR__ . "/../bgprocesses/bootstrap/index.php");
require_once(__DIR__ . "/../bgprocesses/sender/ImageService.php");
require_once(__DIR__ . "/../bgprocesses/sender/LinkService.php");
require_once(__DIR__ . "/../library/swiftmailer/lib/swift_required.php");
require_once(__DIR__ . "/../bgprocesses/sender/PrepareMailContent.php");
require_once(__DIR__ . "/../bgprocesses/sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../bgprocesses/sender/AttachmentObject.php");
require_once(__DIR__ . "/../bgprocesses/sender/InterpreterTarget.php");
require_once(__DIR__ . "/../bgprocesses/sender/CustomfieldManager.php");

class MailWrapper extends \BaseWrapper {

    public $mail = array(),
            $totals,
            $mta,
            $dataMail = array(),
            $idMailattachment,
            $idMail,
            $arraySaxs = array();

    public function __construct() {
        $this->mta = \Phalcon\DI\FactoryDefault::getDefault()->get("mta");
        parent::__construct();
    }

    public function findMailsFilters() {
        $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
        $this->data = \Mail::find(["conditions" => "idSubaccount = ?0 AND status = ?1 AND deleted = 0", "bind" => [0 => $idSubaccount, 1 => 'sent']]);
        $this->modeldataMail();
    }

    public function modeldataMail() {
        foreach ($this->data as $key) {
            $obj = new \stdClass();
            $obj->idMail = $key->idMail;
            $obj->name = $key->name;
            array_push($this->mail, $obj);
        }
    }

    public function findAllMail($page, $data) {
        $where = " ";
        if (isset($data->name) && $data->name != "") {
            $where .= " AND mail.name like '%{$data->name}%'";
        }
        if (isset($data->category) && count($data->category) >= 1) {
            $arr = implode(",", $data->category);
            $where .= "  AND mxmc.idMailCategory IN ({$arr})";
        }
        if (isset($data->showTest) && $data->showTest == 0) {
            $where .= "  AND mail.test = {$data->showTest} ";
        }

        if (isset($data->mailStatus) && $data->mailStatus != "") {
            if ($data->mailStatus == "allStatuses") {
                $where .= " AND mail.status IN ('draft','scheduled','sending','sent','canceled','paused','birthday') ";
            } else {
                $mailStatus = $this->translateStatusMail($data->mailStatus);
                $where .= " AND  mail.status = '{$mailStatus}' ";
            }
        }

        //$where .= " AND mail.type = 'manual'";
        if (isset($data->dateinitial) && isset($data->dateend)) {
            if ($data->dateinitial != "" && $data->dateend != "") {
                $where .= " AND scheduleDate BETWEEN '{$data->dateinitial}' AND '{$data->dateend}'";
            }
        }
        (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
        $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
        $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
        $sql = "SELECT mail.idMail, mail.idSubaccount, mail.idEmailsender,  mail.categorycampaign,"
                . " mail.name, mail.sender, IF(mail.idReplyTo is null,mail.replyto,reply_tos.email) as replyto, mail.subject, mail.target, mail.created, mail.type, "
                . " mail.updated, mail.status, mail.quantitytarget, mail.test, mail.deleted, emailsender.email AS emailsender, "
                . " name_sender.name as namesender, mail.totalOpening, mail.bounced, mail.spam, mail.scheduleDate, mail_content.typecontent,"
                . " uniqueOpening, canceleduser, GROUP_CONCAT(DISTINCT mail_category.name) AS mailCategory FROM mail  "
                . " LEFT JOIN mxmc ON mxmc.idMail = mail.idMail "
                . " LEFT JOIN emailsender ON mail.idEmailsender = emailsender.idEmailsender "
                . " LEFT JOIN name_sender ON mail.idNameSender = name_sender.idNameSender "
                . " LEFT JOIN mail_category ON mxmc.idMailCategory = mail_category.idMailCategory "
                . " LEFT JOIN mail_content ON mail.idMail = mail_content.idMail "
                . " LEFT JOIN reply_tos on mail.idReplyTo = reply_tos.idReplyTo "
                . " WHERE mail.idSubaccount = {$idSubaccount} AND mail.deleted = 0 {$where}"
                . " GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24"
                . " ORDER BY mail.created DESC "
                . "  LIMIT {$limit} "
                . " OFFSET {$page}";
        $sql2 = "SELECT mail.idMail FROM mail  LEFT JOIN mxmc ON mxmc.idMail = mail.idMail "
                . " WHERE mail.idSubaccount = {$idSubaccount} AND mail.deleted = 0 {$where}"
                . " GROUP BY mail.idMail ";
        $this->data = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
        $this->totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql2);
        $this->modelData();
    }

    public function modelData() {
        $this->mail = array("total" => count($this->totals),
            "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
        );
        $arr = array();
        foreach ($this->data as $key => $value) {
            $obj = new \stdClass();
            if ($value['target']) {
                $p = json_decode($value['target']);
                if (isset($p->contactlists)) {
                    $v = "Lista de contactos: ";
                    for ($index = 0; $index < count($p->contactlists); $index++) {
                        $v .= $p->contactlists[$index]->name . ", ";
                    }
                } else if (isset($p->segment)) {
                    $v = "Segmentos: ";
                    for ($index = 0; $index < count($p->segment); $index++) {
                        $v .= $p->segment[$index]->name . ", ";
                    }
                }else if (isset($p->to)){
                    $v = $p->to;
                }
                if(!isset($p->to)){
                $v = substr($v, 0, -2);    
                }
                
                $value['target'] = $v;
            }
            $obj->$key = $value;
            array_push($arr, $value);
        }
        array_push($this->mail, ["items" => $arr]);
    }

    public function getMail() {
        return $this->mail;
    }

    public function deleteMail($idmail) {
        $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idmail]]);
        $mail->deleted = time();

        if (!$mail) {
            throw new \InvalidArgumentException("El correo no se encuentra registrado.");
        }

        $status = array("sent", "canceled", "draft");
        if (!in_array($mail->status, $status)) {
            throw new \InvalidArgumentException("El envío no se puede eliminar");
        }

        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            $this->trace("fail", "error al eliminar el mail");
        }
    }

    public function updatePlaintText($data) {
        $mailContent = \MailContent::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
        $mailContent->plaintext = $data->plaintext;
        if (!$mailContent->save()) {
            foreach ($mailContent->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            $this->trace("fail", "No se logro crear una cuenta");
        }
    }

    public function addgoogleanalytics($data) {
        $googleanalytics = \Mailgoogleanalytics::findFirst(array(
                    "conditions" => "idMail  = ?0",
                    "bind" => array($data['idMail'])
        ));
        $objReturn = array();
        if ($googleanalytics) {
            $googleanalytics->name = $data['campaignName'];
            $googleanalytics->links = json_encode($data['links']);
            if (!$googleanalytics->update()) {
                foreach ($googleanalytics->getMessages() as $message) {
                    throw new \InvalidArgumentException($message);
                }
            }
            $objReturn = array("message" => "Se modifico google analytics correctamente", "action" => "update");
        } else {
            $googleanalytics = new \Mailgoogleanalytics();
            $googleanalytics->idMail = $data['idMail'];
            $googleanalytics->name = $data['campaignName'];
            $googleanalytics->links = json_encode($data['links']);

            if (!$googleanalytics->save()) {
                foreach ($googleanalytics->getMessages() as $message) {
                    throw new \InvalidArgumentException($message);
                }
            }
            $objReturn = array("message" => "Se agrego google analytics correctamente", "action" => "create");
        }

        $mail = $googleanalytics->Mail;
        $mail->googleAnalytics = 1;
        if (!$mail->update()) {
            foreach ($mail->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return $objReturn;
    }

    public function ConfirmationMail($data) {
        $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
        $objReturn = array();
        if (!$mail) {
            throw new \InvalidArgumentException("El correo no se encuentra registrado.");
        }
        if (!$mail) {
            throw new \InvalidArgumentException("El correo no se encuentra registrado.");
        }
        //
        $flagSending = false;
        foreach ($this->user->Usertype->subaccount->saxs as $key) {
            if ($key->idServices == 2 && $key->accountingMode == "sending" && ($key->status == 1 || $key->status == '1')) {
                $flagSending = true;
            }
        }

        if ($flagSending) {
            //Se realiza validaciones de los sms programados
            $balance = $this->validateBalance();
            $target = 0;
            if ($balance['mailFindPending']) {
                foreach ($balance['mailFindPending'] as $value) {
                    $target = $target + $value['target'];
                }
            }
            $amount = $balance['balanceConsumedFind']['amount'];

            unset($balance);
            $totalTarget = $amount - $target;
            $target = $target + $mail->quantitytarget;

            if (($target > $amount) && $flagSending == true) {
                $target = $target - $amount;
                if (abs($totalTarget)) {
                    $tAvailable = (object) ["totalAvailable" => 0];
                } else {
                    $tAvailable = (object) ["totalAvailable" => $totalTarget];
                }
                $this->sendmailnotmailbalance($tAvailable);
                throw new \InvalidArgumentException("No tiene saldo disponible para realizar esta campaña!, su saldo disponlble es {$totalTarget} envios, ya que existen campañas programadas pendientes por enviar.");
            }
            unset($target);
            unset($amount);
            unset($totalTarget);
            unset($tAvailable);
        }

        $mail->confirmationDate = $data->dateConfirmation;
        $mail->status = 'scheduled';
        $mail->sentprocessstatus = 'loading-target';
        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        $msn = \MailStatisticNotification::findFirst(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $mail->idMail)
        ));

        if ($msn) {
            if (isset($msn->target) && !empty($msn->target)) {
                $msn->scheduleDate = date("Y-m-d H:i", strtotime("+" . $msn->quantity . " " . $msn->typeTime, strtotime($mail->scheduleDate)));

                $msn->status = "scheduled";
                if (!$msn->save()) {
                    foreach ($msn->getMessages() as $message) {
                        throw new InvalidArgumentException($message);
                    }
                }
            }
        }

        $objReturn = array("message" => "Se modifico correctamente el correo");
        return $objReturn;
    }

    public function getMailExits($idmail) {
        $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $idmail]]);
        return $mail;
    }

    public function ScheduledateMail($data) {
        date_default_timezone_set('America/Bogota');
        $date = $this->validateDates($data->dateSelected, $data->gmt);
        $datecompare = strtotime("+10 minutes", strtotime($date));
        $now = strtotime("now");
        if ($datecompare < $now) {
            throw new \InvalidArgumentException("La fecha de envio del mail ya expiro con relación al GMT seleccionado.");
        }
        $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);
        $objReturn = array();
        if (!$mail) {
            throw new \InvalidArgumentException("El correo no se encuentra registrado.");
        }
        $mail->scheduleDate = $date;
        $mail->gmt = $data->gmt;
        //$mail->status = 
        if (!$mail->save()) {
            foreach ($mailContent->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }


        $objReturn = array("message" => "Se programó el envío del correo correctamente.", "mail" => $mail);
        return $objReturn;
    }

    /**
     *
     * @param Date $date "1990-10-21 13:00:00"
     * @param String $timezone "-0500"
     * @return Date
     * @throws \InvalidArgumentException
     */
    private function validateDates($date, $timezone) {

        $timezone = substr($timezone, 0, 3);

        if ($timezone[1] == 0) {
            $typeGmt = substr($timezone, 0, 1);
            $timezone = substr($timezone, 2, 2);
        } else {
            $typeGmt = substr($timezone, 0, 1);
            $timezone = substr($timezone, 1, 2);
        }

        if ($typeGmt == "-") {
            if ($timezone > 5) {
                $timezone = $timezone - 5;
            } else {
                $typeGmt = "+";
                $timezone = 5 - $timezone;
            }
        } else if ($typeGmt == "+") {
            $timezone = 5 + $timezone;
        }
        $datenowstr = strtotime("{$typeGmt}{$timezone} hour", strtotime($date));
        $dateStart = date("Y-m-d H:i", $datenowstr);
        return $dateStart;
    }

    public function sendtestmail($data) {
        try {


            if (!isset($data->idMail)) {
                throw new \InvalidArgumentException("Dato de correo inválido");
            }


            $Subaccount = $this->user->Usertype->Subaccount;
            $mail = \Mail::findFirst(array(
                        "conditions" => "idMail = ?0 AND idSubaccount = ?1",
                        "bind" => array($data->idMail, $Subaccount->idSubaccount)
            ));

            if (!$mail) {
                throw new \InvalidArgumentException("El Correo que intenta enviar como prueba no existe");
            }

            $mailcontent = \MailContent::findFirst(array(
                        "conditions" => "idMail = ?0",
                        "bind" => array(0 => $mail->idMail)
            ));



            /**
             * Se reciben los datos que se envían desde la vista;
             */
            $target = $data->target;
            $msg = ((isset($data->message)) ? $data->message : '');

            /**
             * Valida que se hayan enviado correos
             */
            if (str_replace(" ", "", $target) === '') {
                throw new \InvalidArgumentException("No ha enviado ninguna dirección de correo, por favor verifique la información");
            }

            $emails = explode(",", $target);

            if (count($emails) >= 8) {
                throw new \InvalidArgumentException("Sólo puede ingresar 8 correos");
            }

            /**
             * Validación de los correos, que tengan el formato correcto y que no se repitan
             */
            $dataEmails = array();
            foreach ($emails as $email) {
                $em = str_replace(" ", "", $email);
                if (!empty($em) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!in_array($email, $dataEmails)) {
                        $dataEmails[] = $email;
                    } else {
                        throw new \InvalidArgumentException("El correo <b><i>{$email}</i></b> no debe estar repetido");
                    }
                } else {
                    throw new \InvalidArgumentException("El correo <b><i>{$email}</i></b> tiene un formato inválido");
                }
            }

            /**
             * Comprobar si es un correo con adjuntos, si lo es se construye un arreglo con el nombre y el path de estos
             */
            $attach = array();
            //$dir = $this->asset->dir . $Subaccount->Account->idAccount . '/attachments/' . $mail->idMail . '/';
            $dir = $this->asset->dir . $Subaccount->Account->idAccount;
            $dirImage = $dir . '/images/';
            $dirAttachments = $dir . '/attachments/';
            $attachments = \Mailattachment::find(array(
                        "conditions" => "idMail = ?0",
                        "bind" => array($mail->idMail)
            ));
            if ($mail->attachment == 1 || $attachments) {

                if (count($attachments) > 0) {
                    foreach ($attachments as $value) {

                        $obj = new \stdClass();
                        $obj->name = $value->Asset->name;

                        if ($value->Asset->type == "File") {
                            $obj->path = $dirAttachments . $value->Asset->idAsset . '.' . $value->Asset->extension;
                        } else {
                            $obj->path = $dirImage . $value->Asset->idAsset . '.' . $value->Asset->extension;
                        }

                        if (is_readable($obj->path)) {
                            $attach[] = $obj;
                        }
                    }
                }
            }

//    var_dump($attach);
//    exit();

            /**
             * Comienzo del proceso de envío de prueba
             */
            /* Se crea el transport y el swift */
            $transport = \Swift_SmtpTransport::newInstance($this->mta->address, $this->mta->port);
            $swift = \Swift_Mailer::newInstance($transport);

            $account = $this->user->Usertype->Subaccount->Account;
            /* Cargamos el dominio correspondiente de la cuenta para el envio */
            foreach ($account->AccountConfig->DetailConfig as $key) {
                if ($key->idServices == $this->services->email_marketing) {
                    $detailConfig = $key;
                }
            }
            $domain = $detailConfig->Dcxurldomain[0]->Urldomain;
//    $domain = \Urldomain::findFirstByIdUrldomain($this->user->Usertype->Subaccount->Account->Accountclassification->idUrldomain);

            $sendTestMail = new \TestMail();
            $sendTestMail->setAccount($account);
            $sendTestMail->setDomain($domain);
            $sendTestMail->setUrlManager($this->urlManager);
            $sendTestMail->setMail($mail);
            $sendTestMail->setMailContent($mailcontent);
            $sendTestMail->setPersonalMessage($msg);

            $sendTestMail->load();

            $this->logger->log(print_r($dataEmails, true));

            $from = array($mail->Emailsender->email => $mail->NameSender->name);
//    $from = $dataEmails;
            // Consultar el mail class para inyectarselo en las cabeceras del correo con switmailer
            $mailclass = \Mailclass::findFirstByIdMailClass($detailConfig->Dcxmailclass[0]->idMailClass);

            // Crear variables listID y sendID para inyectarlas a las cabeceras con swiftmailer
            $prefixID = \Phalcon\DI::getDefault()->get('instanceIDprefix')->prefix;
            if (!$prefixID || $prefixID == '') {
                $prefixID = '0em';
            }
            $listID = 't' . $prefixID . $mail->Subaccount->Account->idAccount;
            $sendID = $prefixID . $mail->idMail;

            // MTA a utilizar
            $mtaName = $detailConfig->Dcxmta[0]->Mta->name;
            $mta = ($mtaName == null || trim($mtaName) === '') ? 'CUST_SIGMA' : $mtaName;


            foreach ($dataEmails as $email) {
                $message = new \Swift_Message();

                // Asignacion de headers del mensaje

                $headers = $message->getHeaders();

                $headers->addTextHeader('X-GreenArrow-MailClass', $mailclass->name);
                $headers->addTextHeader('X-GreenArrow-MtaID', $mta);
                $headers->addTextHeader('X-GreenArrow-InstanceID', $sendID);
                $headers->addTextHeader('X-GreenArrow-ListID', $listID);
                $message->setFrom($from);
                $message->setSubject($mail->subject);
                $message->setTo(array($email => $email));

                //lo siguiente reemplaza algunas urls que empezaban con
                //"Https" las cuales eran erroneas por las correctas con minuscula.
                $planeHtmlBody = ereg_replace("Https", "https", $sendTestMail->getBody());
                //esto me permite visualizar las imagenes en los gestores de correo.
                //$message->setBody($sendTestMail->getBody(), "text/html");
                $message->setBody($planeHtmlBody, "text/html");
                $message->addPart($sendTestMail->getPlainText(), "text/plain");

                $replyto = ((isset($mail->idReplyTo)) ? $mail->ReplyTos->email : ((isset($mail->replyto)) ? $mail->replyto : null));

                if ($replyto != null) {
                    $message->setReplyTo($replyto);
                }

                if (count($attach) > 0) {
                    foreach ($attach as $at) {
                        $message->attach(\Swift_Attachment::fromPath($at->path)->setFilename($at->name));
                    }
                }
//        var_dump("que hubo");
//        exit();

                $send = $swift->send($message, $failures);

                if (!$send) {
                    $this->logger->log("Error while SendTestMail, idMail {$mail->idMail}");
                    throw new \InvalidArgumentException("Ha ocurrido un error enviando el corrreo de prueba {$mail->name}");
                }
            }
            if ($send) {
                return ["message" => "Se ha enviado el correo de prueba {$mail->name} exitosamente"];
            } else {
                throw new \InvalidArgumentException("Ha ocurrido un error mientras se intentaba enviar el correo de prueba, contacte al administrador");
            }
        } catch (InvalidArgumentException $ex) {
            $this->logger->log("Exception while sendtestmail... {$ex}");
        } catch (Exception $ex) {
            $this->logger->log("Exception while sendtestmail... {$ex}");
        }
    }

    public function createNewMail($data) {
        $this->dataMail = $data;
        $expSender = explode("/", $this->dataMail['mail']['sender']);
        $emailSender = trim($expSender[0]);
        $nameSender = trim($expSender[1]);

        if (!$this->dataMail["mail"]["name"]) {
            throw new \InvalidArgumentException("Nombre de Campaña es obligatorio");
        }

        if (!isset($this->dataMail["mail"]["test"])) {
            throw new \InvalidArgumentException("Debe indicar si el correo es de prueba o no");
        }

        if (!isset($this->dataMail["mail"]["replyto"])) {
            throw new \InvalidArgumentException("Correo de responder dbe estar definido");
        }

        if (!isset($this->dataMail["mail"]["scheduleDate"])) {
            throw new \InvalidArgumentException("Fecha de envío debe estar definida");
        }

        if (isset($this->dataMail["mail"]["scheduleDate"]) || !empty($this->dataMail["mail"]["scheduleDate"])) {
            if ($this->dataMail["mail"]["scheduleDate"] != "now") {
                if (!isset($this->dataMail["mail"]["gmt"]) || $this->dataMail["mail"]["gmt"] == "") {
                    throw new \InvalidArgumentException("Zona horaria debe estar definida");
                }
            }
        }

        if (isset($this->dataMail["content"])) {
            if ($this->dataMail["content"]["type"] == "" || empty($this->dataMail["content"]["type"])) {
                throw new \InvalidArgumentException("Tipo de contenido debe estar definido");
            }
            if ($this->dataMail["content"]["content"] == "" || empty($this->dataMail["content"]["content"])) {
                throw new \InvalidArgumentException("Contenido debe estar definido");
            }
        }

        if (!isset($this->dataMail["content"]["content"])) {
            throw new \InvalidArgumentException("Contenido debe estar definido");
        }

        if ($this->validateMail($emailSender)) {
            throw new \InvalidArgumentException("Formato de correo invalido");
        }

        $idEmailSender = $this->createMailSender($emailSender);
        $idNameSender = $this->createMailName($nameSender);

        if (!empty($this->dataMail['mail']['replyto']) && $this->validateMail($this->dataMail['mail']['replyto'])) {
            throw new \InvalidArgumentException("Formato del correo de responder a es invalido");
        }

        if (!isset($this->dataMail['mail']['category']) || empty($this->dataMail['mail']['category'])) {
            throw new \InvalidArgumentException("Debes enviar una categoría");
        }

        \Phalcon\DI::getDefault()->get("db")->begin();
        $mailForm = new \MailForm();
        $mail = new \Mail();

        $mail->idSubaccount = $this->user->UserType->idSubaccount;
        $mail->idEmailsender = $idEmailSender;
        $mail->idNameSender = $idNameSender;
        $mail->status = "scheduled";
        $mail->replyto = $this->dataMail['mail']["replyto"];
        $mail->test = $this->dataMail['mail']["test"];
        $mail->sentprocessstatus = "loading-target";

        $mailForm->bind($this->dataMail['mail'], $mail);

        if (isset($this->dataMail['mail']['externalApi']) && !empty($this->dataMail['mail']['externalApi'])) {

            $arraytmp = array();
            $arraytmp['singleMail'] = $this->dataMail['mail']['singleMail'];

            if (!empty($this->dataMail['mail']['target'])) {
                //---------
                $count = 0;
                switch ($this->dataMail['mail']['target']['type']) {
                    case "contactlist":
                        $idcontactlist = array();
                        foreach ($this->dataMail['mail']['target']['contactlists'] as $key => $value) {
                            $idcontactlist[] = $value['idContactlist'];
                        }
                        $listId = implode(",", $idcontactlist);
                        $sql = "SELECT COUNT(DISTINCT idContact) AS count FROM cxcl "
                                . " WHERE idContactlist IN ({$listId}) AND unsubscribed = 0 AND deleted = 0";
                        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
                        $count = $c[0]["count"];

                        break;
                    case "segment":
                        $idsSegment = [];
                        foreach ($this->dataMail['mail']['target']['segment'] as $key) {
                            $idsSegment[] = (int) $key['idSegment'];
                        }
                        $count = \Sxc::count([["idSegment" => ['$in' => $idsSegment], "deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0]]);


                        break;
                    default:
                        break;
                }
                //---------
            }
            $mail->quantitytarget = $count;
            $mail->externalApi = 1;

            if (!empty($this->dataMail['mail']['singleMail']) && ($this->dataMail['mail']['singleMail'] == 1 || $this->dataMail['mail']['singleMail'] == "1")) {
                $mail->singleMail = 1;
            }
        }

        if (!$mailForm->isValid()) {
            foreach ($mailForm->getMessages() as $msg) {
                throw new \InvalidArgumentException($msg);
            }
        }

        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        $this->assignTarget($manager);

        $mail->target = json_encode($this->dataMail['mail']['target']);

        if ($this->dataMail['mail']['scheduleDate'] == "now") {
            $mail->scheduleDate = date("Y-m-d H:i:s", time());
            $mail->confirmationDate = date("Y-m-d H:i:s", time());
            $mail->gmt = "-0500";
        } else {
            if (preg_match('/^([0-9][0-9][0-9][0-9])-([0][1-9]|[1][0-2])-([0][1-9]|[12][0-9]|3[01]) [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $this->dataMail['mail']['scheduleDate'])) {

                if (isset($this->dataMail['mail']['externalApi']) && !empty($this->dataMail['mail']['externalApi'])) {

                    if ($this->dataMail['mail']['scheduleDate'] < date("Y-m-d H:i:s", time())) {
                        throw new \InvalidArgumentException("Fecha ingresada del pasado");
                    }
                }

                $mail->scheduleDate = $this->dataMail['mail']['scheduleDate'];
                $mail->confirmationDate = date("Y-m-d H:i:s", time());
                $mail->gmt = $this->dataMail['mail']['gmt'];
            } else {
                throw new \InvalidArgumentException("Formato de fecha incorrecto");
            }
        }
        $this->validateBalanceMail($mail);

        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \InvalidArgumentException($message);
            }
        }

        foreach ($this->dataMail['mail']['category'] as $category) {
            $mxmc = new \Mxmc();
            $mxmc->idMail = $mail->idMail;
            $mxmc->idMailCategory = $category;

            if (!$mxmc->save()) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \InvalidArgumentException("Ocurrio un error");
            }
        }

        if ($this->dataMail['content']['type'] == "url") {
            $this->urlEditor($mail->idMail, $this->dataMail['content']['content']);
        } else if ($this->dataMail['content']['type'] == "html") {
            $contentmail = new \MailContent();
            $contentmail->idMail = $mail->idMail;

            $contentmail->typecontent = "html";
            $cont = str_replace("\xE2\x80\x8B", "", $this->dataMail['content']['content']);
            //$content = htmlentities($cont, ENT_QUOTES | ENT_IGNORE, "UTF-8");
            $contentmail->content = $cont;

            if (!$contentmail->save()) {
                foreach ($contentmail->getMessages() as $msg) {
                    \Phalcon\DI::getDefault()->get("logger")->log("Error while saving content mail {$msg}");
                }
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \Exception('Error while saving content mail');
            }
        } else if ($this->dataMail['content']['type'] == "template") {

            if (is_numeric($this->dataMail['content']['content'])) {

                $template = \MailTemplateContent::findFirst([
                            "conditions" => "idMailTemplate = ?0",
                            "bind" => array($this->dataMail['content']['content'])
                ]);

                if (!$template) {
                    throw new \InvalidArgumentException("La plantilla de correo ha sido eliminado por favor verifique la información.");
                }

                $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;

                if ($template->MailTemplate->idAccount != null && $template->MailTemplate->idAccount != $idAccount) {
                    throw new \InvalidArgumentException("La plantilla no pertenece a la cuenta por favor verifique la información.");
                }
                //
                $contentMail = new \MailContent();
                $contentMail->idMail = $mail->idMail;
                $contentMail->typecontent = 'Editor';
                $contentMail->content = $template->content;
                $contentMail->createdBy = time();
                $contentMail->updatedBy = time();
                //
                $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
                $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
                $editorObj->assignContent(json_decode($template->content));
                $mail_content = $editorObj->render();
                $text = new \PlainText();
                $plainText = $text->getPlainText($mail_content);
                $contentMail->plaintext = $plainText;
                //
                if (!$contentMail->save()) {
                    \Phalcon\DI::getDefault()->get("db")->rollback();
                    foreach ($contentMail->getMessages() as $msg) {
                        $this->logger->log("Message: {$msg}");
                        throw new \InvalidArgumentException($msg);
                    }
                }
            }
        }

        \Phalcon\DI::getDefault()->get("db")->commit();

        $arrayMail = array();

        foreach ($mail as $key => $value) {
            $arrayMail[$key] = $value;
        }

        return $arrayMail;
    }

    public function validateMail($mail) {
        if (!empty($mail) && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public function createMailName($name) {
        $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
        $emailName = \NameSender::findFirst(array(
                    "conditions" => "name = ?0 AND idAccount = ?1",
                    "bind" => array(0 => $name, 1 => $idAccount)
        ));

        if ($emailName) {
            return $emailName->idNameSender;
        }

        $emailName = new \NameSender();
        $emailName->idAccount = $idAccount;
        $emailName->name = $name;
        $emailName->status = 1;

        if (!$emailName->save()) {
            foreach ($emailName->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return $emailName->idNameSender;
    }

    public function createMailSender($email) {
        $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
        $emailSender = \Emailsender::findFirst(array(
                    "conditions" => "email = ?0 AND idAccount = ?1",
                    "bind" => array(0 => $email, 1 => $idAccount)
        ));

        if ($emailSender) {
            return $emailSender->idEmailsender;
        }

        /* $publicDomain = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->Account->publicDomain;
          $domain = explode('@', $email);

          if ($publicDomain == 0) {

          if (!$this->isAValidDomain($domain[1])) {
          throw new \InvalidArgumentException("Ha enviado una dirección de correo de remitente invalida, recuerde que no debe usar dominios de correo públicas como hotmail o gmail");
          }
          } */

        $emailSender = new \Emailsender();
        $emailSender->idAccount = $idAccount;
        $emailSender->email = $email;
        $emailSender->status = 1;

        if (!$emailSender->save()) {
            foreach ($emailSender->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return $emailSender->idEmailsender;
    }

    public function isAValidDomain($domain) {
        $invalidDomains = \Phalcon\DI::getDefault()->get('publicDomain');

        $d = explode('.', $domain);

        foreach ($invalidDomains as $invalidDomain) {
            if ($invalidDomain == $d[0]) {
                return false;
            }
        }
        return true;
    }

    public function assignTarget($manager) {
        if ($this->dataMail['mail']['target']['type'] == "contactlist") {
            if ($this->dataMail['mail']['target']['contactlists'] == 0) {
                $contactlists = $this->modelsManager->createBuilder()
                        ->columns(["Contactlist.idContactlist", "Contactlist.name"])
                        ->from('Contactlist')
                        ->where("deleted = 0 AND Contactlist.idSubaccount  = " . \Phalcon\DI::getDefault()->get('user')->UserType->idSubaccount)
                        ->getQuery()
                        ->execute();

                $arrayContactlist = array();
                foreach ($contactlists as $contactlist) {
                    $arr = array();
                    $arr['idContactlist'] = $contactlist->idContactlist;
                    $arr['name'] = $contactlist->name;
                    $arrayContactlist[] = $arr;
                }
                $this->dataMail['mail']['target']['contactlists'] = $arrayContactlist;
            }
        } else if ($this->dataMail['mail']['target']['type'] == "segment") {
            if ($this->dataMail['mail']['target']['segment'] == 0) {
                $optionsSegment = array(
                    'projection' => array('_id' => 0, 'idSegment' => 1, 'name' => 1)
                );
                $querySegment = ["idSubaccount" => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount];
                $driverSegment = new \MongoDB\Driver\Query($querySegment, $optionsSegment);
                $resultSegment = $manager->executeQuery("aio.segment", $driverSegment)->toArray();

                $resultSegment = json_encode($resultSegment);
                $resultSegment = json_decode($resultSegment, true);

                $this->dataMail['mail']['target']['segment'] = $resultSegment;
            }
        }
    }

    public function urlEditor($idMail, $url) {
        $account = \Phalcon\DI::getDefault()->get("user")->UserType->SubAccount->Account;
        $mail = \Mail::findfirst(array(
                    "conditions" => "idMail = ?0",
                    "bind" => array(0 => $idMail)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
        }

        $image = "";

        $dir = \Phalcon\DI::getDefault()->get("asset")->dir . $account->idAccount . "/images";

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("La url ingresada no es válida, por favor verifique la información");
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $getHtml = new \LoadHtml();
        $content = $getHtml->gethtml($url, $image, $dir, $account);

        $search = array("\xe2\x80\x8b", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x9f", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9b", "a�?", "e�?", "i�?", "o�?", "u�?", "ñ", "A�?", "E�?", "I�?", "O�?", "U�?", "Ñ", "&nbsp;");
        $replace = array('', '"', '"', '"', "'", "'", "'", "á", "é", "í", "ó", "ú", "ñ", "�?", "É", "�?", "Ó", "Ú", "Ñ", "");
//        $html = htmlentities(str_replace($search, $replace, $content));
        $html = str_replace($search, $replace, $content);

        \Phalcon\DI::getDefault()->get("logger")->log("Content: {$html}");

        $contentmail = new \MailContent();
        $contentmail->idMail = $mail->idMail;

        $contentmail->typecontent = "url";
        $contentmail->content = $html;

        if (!$contentmail->save()) {
            foreach ($contentmail->getMessages() as $msg) {
                \Phalcon\DI::getDefault()->get("logger")->log("Error while saving content mail {$msg}");
            }
            \Phalcon\DI::getDefault()->get("db")->rollback();
            throw new \Exception('Error while saving content mail');
        }

        return true;
    }

    public function setIdMailattachment($idMailattachment) {
        $this->idMailattachment = $idMailattachment;
        return $this;
    }

    public function deletedMailattachment() {
        $mailAttchement = \Mailattachment::findFirst(["conditions" => "idMailAttachment = ?0", "bind" => [0 => $this->idMailattachment]]);
        if (!$mailAttchement) {
            throw new \InvalidArgumentException("No existe un archivo adjunto con este id");
        }
        if (!$mailAttchement->delete()) {
            foreach ($mailAttchement->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }
    }

    public function editMail($idMail, $data) {
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0 AND deleted = 0',
                    'bind' => array(0 => $idMail)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo");
        }

        if ($mail->status == "scheduled") {
            $this->dataMail = $data;

            \Phalcon\DI::getDefault()->get("db")->begin();

            foreach ($this->dataMail['mail'] as $key => $mailValue) {
                if ($key == "sender") {
                    $expSender = explode("/", $mailValue);
                    $emailSender = trim($expSender[0]);
                    $nameSender = trim($expSender[1]);

                    if ($this->validateMail($emailSender)) {
                        throw new \InvalidArgumentException("Formato de correo invalido");
                    }

                    $idEmailSender = $this->createMailSender($emailSender);
                    $idNameSender = $this->createMailName($nameSender);

                    $mail->idEmailsender = $idEmailSender;
                    $mail->idNameSender = $idNameSender;

                    $this->saveMail($mail);
                } else if ($key == "replyto") {
                    if (!empty($this->dataMail['mail']['replyto']) && $this->validateMail($this->dataMail['mail']['replyto'])) {
                        throw new \InvalidArgumentException("Formato del correo de responder a es invalido");
                    }
                    $mail->replyto = $mailValue;
                    $this->saveMail($mail);
                } else if ($key == "target") {
                    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

                    if (!empty($this->dataMail['mail']['target'])) {

                        foreach ($this->dataMail['mail']['target']['contactlists'] as $value) {

                            if (empty($value['idContactlist'])) {
                                throw new \InvalidArgumentException("No ha enviado ningun un idContactlist, por favor valide la información");
                            }

                            $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => (int) $value['idContactlist'])));

                            if (empty($contactlist)) {
                                throw new \InvalidArgumentException("El idContactlist {$value['idContactlist']} no esta registrado en la plataforma, por favor valide la información");
                            }
                        }
                    }

                    $this->assignTarget($manager);

                    $mail->target = json_encode($this->dataMail['mail']['target']);
                    $this->saveMail($mail);
                } else if ($key == "scheduleDate") {
                    if ($mailValue == "now") {
                        $mail->scheduleDate = date("Y-m-d H:i:s", time());
                        $mail->confirmationDate = date("Y-m-d H:i:s", time());
                        $mail->gmt = "-0500";
                        $this->saveMail($mail);
                    } else {
                        if (preg_match('/^([0-9][0-9][0-9][0-9])-([0][1-9]|[1][0-2])-([0][1-9]|[12][0-9]|3[01]) [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $mailValue)) {
                            $mail->scheduleDate = $mailValue;
                            $mail->confirmationDate = date("Y-m-d H:i:s", time());
                            //$mail->gmt = $this->dataMail['mail']['gmt'];
                            $this->saveMail($mail);
                        } else {
                            throw new \InvalidArgumentException("Formato de fecha incorrecto");
                        }
                    }
                } else if ($key == "category") {
                    $mxmc = \Mxmc::find(array(
                                'conditions' => 'idMail = ?0',
                                'bind' => array(0 => $idMail)
                    ));
                    if (!$mxmc->delete()) {
                        \Phalcon\DI::getDefault()->get("db")->rollback();
                        throw new \InvalidArgumentException("Ocurrio un error");
                    }

                    foreach ($mailValue as $category) {
                        $mxmc = new \Mxmc();
                        $mxmc->idMail = $mail->idMail;
                        $mxmc->idMailCategory = $category;

                        if (!$mxmc->save()) {
                            \Phalcon\DI::getDefault()->get("db")->rollback();
                            throw new \InvalidArgumentException("Ocurrio un error");
                        }
                    }
                } else {
                    $mail->$key = $mailValue;
                    $this->saveMail($mail);
                }
            }

            if (isset($this->dataMail['content']) && isset($this->dataMail['content']['type'])) {
                $content = \MailContent::find(array(
                            'conditions' => 'idMail = ?0',
                            'bind' => array(0 => $idMail)
                ));
                if (!$content->delete()) {
                    \Phalcon\DI::getDefault()->get("db")->rollback();
                    throw new \InvalidArgumentException("Ocurrio un error");
                }
                if ($this->dataMail['content']['type'] == "url") {
                    $this->urlEditor($mail->idMail, $this->dataMail['content']['content']);
                } else if ($this->dataMail['content']['type'] == "html") {
                    $contentmail = new \MailContent();
                    $contentmail->idMail = $mail->idMail;

                    $contentmail->typecontent = "html";
                    $cont = str_replace("\xE2\x80\x8B", "", $this->dataMail['content']['content']);
                    //$content = htmlentities($cont, ENT_QUOTES | ENT_IGNORE, "UTF-8");
                    $contentmail->content = $cont;

                    if (!$contentmail->save()) {
                        foreach ($contentmail->getMessages() as $msg) {
                            \Phalcon\DI::getDefault()->get("logger")->log("Error while saving content mail {$msg}");
                        }
                        \Phalcon\DI::getDefault()->get("db")->rollback();
                        throw new \Exception('Error while saving content mail');
                    }
                }
            }

            \Phalcon\DI::getDefault()->get("db")->commit();

            $arrayMail = array();

            foreach ($mail as $key => $value) {
                $arrayMail[$key] = $value;
            }

            return $arrayMail;
        } else {
            throw new \InvalidArgumentException("Ocurrio un error, el envío no tiene el estado programado");
        }
    }

    public function saveMail($mail) {
        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \InvalidArgumentException($message);
            }
        }
    }

    public function cancelMail($idMail) {
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                    'bind' => array(0 => $idMail, 1 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo");
        }

        if ($mail->status == "sending" || $mail->status == "paused" || $mail->status == "scheduled") {
            if ($mail->status == "paused" || $mail->status == "scheduled") {
                $mail->status = "canceled";
                $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
                $this->saveMail($mail);
                //
                $customLogger = new \Logs();
                $customLogger->registerDate = date("Y-m-d h:i:sa");
                $customLogger->idMail = $mail->idMail;
                $customLogger->idUser = \Phalcon\DI::getDefault()->get("user")->idUser;
                $customLogger->name = \Phalcon\DI::getDefault()->get("user")->name;
                $customLogger->lastname = \Phalcon\DI::getDefault()->get("user")->lastname;
                $customLogger->email = \Phalcon\DI::getDefault()->get("user")->email;
                $customLogger->cellphone = \Phalcon\DI::getDefault()->get("user")->cellphone;
                $customLogger->status = "canceled";
                $customLogger->typeName = "RegisterMailCancelOnly";
                $customLogger->created = time();
                $customLogger->updated = time();
                $customLogger->save();

                return "El envío se ha cancelado correctamente";
            } else if ($mail->status == "sending") {
                $mxc = \Mxc::count([["idMail" => $mail->idMail, "status" => 'sent']]);
                if ($mxc == false || $mail->uniqueOpening = 0 || $mail->totalOpening = 0 || $mail->uniqueClicks = 0) {
                    $data = array(
                        "idMail" => $mail->idMail,
                        "nameFunc" => "cancel"
                    );
                    $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
                    $elephant->initialize();
                    $elephant->emit('cancel-send-mail', $data);
                    $elephant->close();
                    $mail->status = "canceled";
                    $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
                    $this->saveMail($mail);
                    //
                    $customLogger = new \Logs();
                    $customLogger->registerDate = date("Y-m-d h:i:sa");
                    $customLogger->idMail = $mail->idMail;
                    $customLogger->idUser = \Phalcon\DI::getDefault()->get("user")->idUser;
                    $customLogger->name = \Phalcon\DI::getDefault()->get("user")->name;
                    $customLogger->lastname = \Phalcon\DI::getDefault()->get("user")->lastname;
                    $customLogger->email = \Phalcon\DI::getDefault()->get("user")->email;
                    $customLogger->cellphone = \Phalcon\DI::getDefault()->get("user")->cellphone;
                    $customLogger->status = "canceled";
                    $customLogger->typeName = "RegisterMailCancelAll";
                    $customLogger->created = time();
                    $customLogger->updated = time();
                    $customLogger->save();

                    return "El envío se ha cancelado correctamente";
                } else {
                    return "Los correos están siendo entregados a sus respectivos destinatarios";
                }
            }
        } else {
            throw new \InvalidArgumentException("Los correos están siendo entregados a sus respectivos destinatarios.");
        }
    }

    public function detailMail($idMail) {
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                    'bind' => array(0 => $idMail, 1 => (int) \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica de  correo");
        }

        $mxmc = \Mxmc::find(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $idMail)
        ));

        $arrayCategory = array();

        foreach ($mxmc as $value) {
            $arr = array();
            $arr['idMailCategory'] = $value->idMailCategory;
            $arr['name'] = $value->mailCategory->name;
            $arrayCategory[] = $arr;
        }

        $rateClic = $this->calculatePercentage($mail->messagesSent, $mail->uniqueClicks);
        $rateOpenClic = $this->calculatePercentage($mail->messagesSent, $mail->uniqueOpening);

        if ($mail->externalApi == 1) {
            $rateBounced = $this->calculatePercentage($mail->messagesSent, $mail->bounced);
            $rateSpam = $this->calculatePercentage($mail->messagesSent, $mail->spam);
            $unsubscribed = \Mxc::count([["unsubscribed" => ['$gte' => "1"], "idMail" => $idMail]]);
            $rateunsubscribed = $this->calculatePercentage($mail->messagesSent, $unsubscribed);


            $where = [
                "idMail" => $idMail,
                "status" => 'sent',
                "bounced" => 0,
                "spam" => 0
            ];

            $buzon = \Mxc::count([$where]);
            $open = \Mxc::count([["open" => ['$type' => (int) 18], "idMail" => $idMail]]);
            $bounced = \Mxc::count([["bounced" => ['$gte' => "1"], "idMail" => $idMail]]);
            $clicks = \Mxc::count([["uniqueClicks" => ['$gte' => 1], "idMail" => $idMail]]);
            $spam = \Mxc::count([["spam" => ['$gte' => "1"], "idMail" => $idMail]]);
            $ratebuzon = $this->calculatePercentage($mail->messagesSent, $buzon);

        /*$arrayMail = array(
              "name" => $mail->name,
              "category" => $arrayCategory,
              "idMail" => $mail->idMail,
              "scheduleDate" => $mail->scheduleDate,
              "messagesSent" => $mail->messagesSent,
              "totalOpening" => $open,
              "status" => $mail->status,
              "totalbounced" => $bounced,
              "uniqueClicks" => $clicks,
              "rateClick" => $rateClic,
              "rateOpen" => $rateOpenClic,
              "totalspam" => $spam,
              "totalbuzon" => $buzon
              ); */

            $arrayMail = array(
                "name" => $mail->name,
                "category" => $arrayCategory,
                "scheduleDate" => $mail->scheduleDate,
                "messagesSent" => $mail->messagesSent,
                "totalOpening" => $open,
                "bounced" => $bounced,
                "uniqueClicks" => $clicks,
                "rateClick" => $rateClic,
                "rateOpenClick" => $rateOpenClic,
                "spam" => $spam,
                "idMail" => $mail->idMail,
                "status" => $mail->status,
            "rateBounced"=> $rateBounced,
            "rateSpam"=> $rateSpam,
            "rateunsubscribed"=> $rateunsubscribed,
            "ratebuzon"=> $ratebuzon,
                "buzon" => $buzon,
                "totalunsubscribed" => $unsubscribed
            );
        } else {
            $arrayMail = array(
                "name" => $mail->name,
                "category" => $arrayCategory,
                "scheduleDate" => $mail->scheduleDate,
                "messagesSent" => $mail->messagesSent,
                "totalOpening" => $mail->totalOpening,
                "bounced" => $mail->bounced,
                "uniqueClicks" => $mail->uniqueClicks,
                "rateClick" => $rateClic,
                "rateOpenClick" => $rateOpenClic,
                "spam" => $mail->spam,
            );

            $uniqueClicks = \Mxc::count([["unsubscribed" => ['$gte' => "1"], "idMail" => $idMail]]);
            $arrayMail["unsubscribed"] = $uniqueClicks;
        
        }

        return $arrayMail;
    }

    public function calculatePercentage($total, $value) {
        if ($total == 0) {
            return 0;
        }

        $res = ($value / $total) * 100;
        if ($res % 1 == 0) {
            return $res;
        } else {
            return round($res, 2);
        }
    }

    public function getThumbnailMail($idMail) {
        $idAccount = ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL);
        $dir = getcwd() . "/assets/{$idAccount}/images/mails/{$idMail}_thumbnail.png";
        $dirImage = "images/circle/opened-email-envelope.png";
        if (file_exists($dir)) {
            $dirImage = "assets/{$idAccount}/images/mails/{$idMail}_thumbnail.png";
        }
        return $dirImage;
    }

    public function downloadmailprev($idMail) {
        $idAccount = ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL);
        $dirAcc = "assets/";
        $dir = "{$dirAcc}{$idAccount}/pdf/templates/";


        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $dir .= "{$idMail}.pdf";

        if (file_exists($dir)) {
            return $dir;
        }
        $domain = "52.55.110.0/";
        exec("wkhtmltopdf {$domain}thumbnail/mailshow/{$idMail} {$dir}");

        if (file_exists($dir)) {
            return $dir;
        } else {
            return false;
        }
    }

    public function saveAdvanceOptions($idMail, $data) {
        $this->db->begin();
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $idMail)
        ));

        $mail->googleAnalytics = (($data->googleAnalytics) ? 1 : 0);
        $mail->update();

        if (!$data->googleAnalytics) {
            $this->deleteMailGoogleAnalytics($mail->idMail);
        }

        if ($mail->idSubaccount != $this->user->Usertype->Subaccount->idSubaccount) {
            $this->db->rollback();
            throw new \InvalidArgumentException("No se encontró el correo");
        }
        if (!$mail) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Ocurrio un error, no se encontro la informacion basica");
        }
        if (trim($data->notificationEmails) != "" and ! $data->notifications) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Se tiene desactivada la opción de notificar por correo electrónico");
        }
        if (trim($data->statisticsEmails) != "" and ! $data->statistics) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Se tiene desactivada la opción de enviar estadísticas automáticamente");
        }
        if (trim($data->statisticsEmails) != "" and ( !isset($data->quantity) || !isset($data->typeTime) || $data->quantity == '0')) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Por favor indique una cantidad de tiempo correcto para enviar estadísticas");
        }

        if (count(explode(",", trim($data->notificationEmails))) > 8) {
            $this->db->rollback();
            throw new \InvalidArgumentException("No se puede enviar notificación a más de 8 correos");
        }
        if (count(explode(",", trim($data->statisticsEmails))) > 8) {
            $this->db->rollback();
            throw new \InvalidArgumentException("No se puede enviar estadísticas más de 8 correos");
        }
        if (trim($data->notificationEmails) == "" and $data->notifications) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Debe ingresar aunque sea un correo electrónico para notificar el envio");
        }
        if (trim($data->statisticsEmails) == "" and $data->statistics) {
            $this->db->rollback();
            throw new \InvalidArgumentException("Debe ingresar aunque sea un correo electrónico para enviar las estadísticas");
        }

        $emailErr = "";
        foreach (explode(",", trim($data->notificationEmails)) as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr .= $email;
            }
        }
        foreach (explode(",", trim($data->statisticsEmails)) as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr .= $email;
            }
        }

        if ($emailErr != "") {
            $this->db->rollback();
            throw new \InvalidArgumentException("Algunos correos electrónicos no tienen el formato correcto");
        }

        $mail->notificationEmails = $data->notificationEmails;
        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                $this->db->rollback();
                throw new InvalidArgumentException($message);
            }
        }

        $msn = \MailStatisticNotification::findFirst(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $idMail)
        ));

        if (!$msn) {
            $msn = new \MailStatisticNotification();
            $msn->idMail = $mail->idMail;
            $msn->idSubaccount = $mail->idSubaccount;
        }

        $msn->status = "draft";
        $msn->target = $data->statisticsEmails;
        $msn->quantity = $data->quantity;
        $msn->typeTime = $data->typeTime;

        if (!$msn->save()) {
            foreach ($msn->getMessages() as $message) {
                $this->db->rollback();
                throw new InvalidArgumentException($message);
            }
        }

        if (isset($data->facebook) && !empty($data->facebook->fanPageSelected)) {
            if ($mail->postFacebook) {
                $post = \Post::findFirst(array("conditions" => "idMail = ?0", "bind" => array($mail->idMail)));
            } else {
                $post = new \Post();
            }

            $post->idMail = $mail->idMail;
            $post->idPage = $data->facebook->fanPageSelected->id;
            $post->type = "facebook";
            if (isset($data->facebook->descriptionPublish) && !empty($data->facebook->descriptionPublish)) {
                $post->description = $data->facebook->descriptionPublish;
            } else {
                if (isset($post->description) && !empty($post->description)) {
                    $post->description = null;
                }
            }
            if (!$post->save()) {
                foreach ($msn->getMessages() as $message) {
                    $this->db->rollback();
                    throw new InvalidArgumentException($message);
                }
            }
            $mail->postFacebook = 1;
            if (!$mail->save()) {
                foreach ($msn->getMessages() as $message) {
                    $this->db->rollback();
                    throw new InvalidArgumentException($message);
                }
            }
        } else if ($mail->postFacebook) {
            $mail->postFacebook = 0;
            if (!$mail->save()) {
                foreach ($msn->getMessages() as $message) {
                    $this->db->rollback();
                    throw new InvalidArgumentException($message);
                }
            }
        }

        $this->db->commit();
    }

    public function changeTestMail($idMail, $data) {
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $idMail)
        ));

        $mail->test = $data->test;
        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                throw new InvalidArgumentException($message);
            }
        }
    }

    public function getLinksByMail() {

        $sql = "SELECT mail_link.idMail_link, mail_link.link, mxl.idMail FROM mxl "
                . " LEFT JOIN mail_link ON mail_link.idMail_link = mxl.idMail_link WHERE idMail = {$this->idMail}";
        $mxl = $this->db->fetchAll($sql);
        $arr = [];
        foreach ($mxl as $key) {
            $obj = new \stdClass();
            $obj->idMail_link = $key['idMail_link'];
            $obj->link = $key['link'];
            $obj->idMail = $key['idMail'];
            array_push($arr, $obj);
        }
        return $arr;
    }

    function setIdMail($idMail) {
        $this->idMail = $idMail;
    }

    /**
     * 
     * @param object $data
     * $data = {
     *    mailsSend: Array,
     *    messageSend: String,
     *    idTester: Integer,
     * }
     */
    public function sendtestermail($data, $idMail) {

        /*
         * Variables de la funcion
         */
        $dataSendMailTester = new \stdClass();
//    var_dump($this->mta->address, $this->mta->port);
//    exit();
        $mtaSender = new \Sigmamovil\General\Misc\MtaSender($this->mta->address, $this->mta->port);

        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0',
                    'bind' => array(0 => $idMail)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("El correo no se encuentra registrado.");
        }

        if (count($data->mailsSend) <= 0) {
            throw new InvalidArgumentException("Los correos destinatarios del tester no puede estar vacio");
        }
        $dataEmails = array();
        foreach ($data->mailsSend as $email) {
            $em = str_replace(" ", "", $email);
            if (!empty($em) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!in_array($email, $dataEmails)) {
                    $dataEmails[] = $email;
                } else {
                    throw new \InvalidArgumentException("El correo <b><i>{$email}</i></b> no debe estar repetido");
                }
            } else {
                throw new \InvalidArgumentException("El correo <b><i>{$email}</i></b> tiene un formato inválido");
            }
        }
        if (empty($data->mailsSend)) {
            $data->mailsSend = null;
        }

        $tester = new \Sigmamovil\General\Misc\MailTester();
        $objSend = $tester->getTemplateHtml($this->user->Usertype->Subaccount->Account->Allied);
        $mailcontent = \MailContent::findFirst(array(
                    "conditions" => "idMail = ?0",
                    "bind" => array(0 => $mail->idMail)
        ));
        $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $editorObj->assignContent(json_decode($mailcontent->content));
        $content = $editorObj->render();
        if (!empty($data->messageSend)) {
            $replace = '<body>
							<center>
								<table border="0" cellpadding="0" cellspacing="0" width="600px" style="border-collapse:collapse;background-color:#444444;border-top:0;border-bottom:0">
									<tbody>
										<tr>
											<td align="center" valign="top" style="border-collapse:collapse">
												<span style="padding-bottom:9px;color:#eeeeee;font-family:Helvetica;font-size:12px;line-height:150%">"' . utf8_decode($data->messageSend) . '" - "' . $this->user->email . '"</span>
											</td>
										</tr>
									</tbody>
								</table>
							</center>';
            $content = str_replace('<body>', $replace, $content);
        }
        $mailTester = $tester->generateMailTester($data->idTester);
        $replace = "<a href='{$this->urlManager->get_base_uri(true)}mailtester/show/{$mailTester}/{$mail->Subaccount->Account->idAllied}'>Enlace de estadisticas de testeo</a></body>";
        $content = str_replace('</body>', $replace, $content);
        //Asignar datos de envio
        $dataSendMailTester->html = $content;
        $dataSendMailTester->subject = "Envio de tester " . $mail->name;
        $dataSendMailTester->from = array($mail->Emailsender->email => $mail->NameSender->name);

        //Asignar datos de envio de TesterMail

        $dataSendMailTester->to = array($mailTester => $mailTester);


        $mtaSender->setDataMessage($dataSendMailTester);
        $mtaSender->sendMail();

        //Asignar datos de envio notificacion
        $dataSendMailTester->to = $dataEmails;

        $mtaSender->setDataMessage($dataSendMailTester);
        $mtaSender->sendMail();
    }

    public function deleteMailGoogleAnalytics($idMail) {
        //mga => Mail Google Analytics
        $mga = \Mailgoogleanalytics::findFirst(array(
                    "conditions" => "idMail = ?0",
                    "bind" => array($idMail)
        ));

        if ($mga) {
            if (!$mga->delete()) {
                foreach ($mga->getMessages() as $message) {
                    $this->db->rollback();
                    throw new InvalidArgumentException($message);
                }
            }
        }
    }

    public function translateStatusMail($status) {
        $statusEnglish = "";
        switch ($status) {
            case "Enviado":
                $statusEnglish = "sent";
                break;
            case "Borrador":
                $statusEnglish = "draft";
                break;
            case "En proceso de Envío":
                $statusEnglish = "sending";
                break;
            case "Programado":
                $statusEnglish = "scheduled";
                break;
            case "Pausado":
                $statusEnglish = "paused";
                break;
            case "Cancelado":
                $statusEnglish = "canceled";
                break;
        }
        return $statusEnglish;
    }

    public function validateBalanceMail($mail) {
        $flagSending = false;
        foreach ($this->user->Usertype->subaccount->saxs as $key) {
            if ($key->idServices == 2 && $key->accountingMode == "sending" && ($key->status == 1 || $key->status == '1')) {
                $flagSending = true;
            }
        }
        if ($flagSending) {
            //Se realiza validaciones de los sms programados
            $balance = $this->validateBalance();
            $target = 0;
            if ($balance['mailFindPending']) {
                foreach ($balance['mailFindPending'] as $value) {
                    $target = $target + $value['target'];
                }
            }
            $amount = $balance['balanceConsumedFind']['amount'];

            unset($balance);
            $totalTarget = $amount - $target;
            $target = $target + $mail->quantitytarget;

            if ($target > $amount) {
                $target = $target - $amount;
                if ($totalTarget <= 0) {
                    $tAvailable = (object) ["totalAvailable" => 0];
                } else {
                    $tAvailable = (object) ["totalAvailable" => $totalTarget];
                }
                $this->sendmailnotmailbalance($tAvailable);
                $mail->status = 'canceled';
                $mail->canceleduser = 'Saldo Insuficiente';
                $this->saveMail($mail);
                throw new \InvalidArgumentException("No tiene saldo disponible para realizar esta campaña!, su saldo disponlble es {$totalTarget} envios, ya que existen campañas programadas pendientes por enviar.");
            }
            unset($target);
            unset($amount);
            unset($totalTarget);
            unset($tAvailable);
        }
    }

    public function validateBalance() {
        $date = date('Y-m-d h:i:s');
        $mailFindPending = \Mail::find(array(
                    'conditions' => 'idSubaccount = ?0 and status = ?1 and scheduleDate >= ?2',
                    'bind' => array(
                        0 => $this->user->Usertype->subaccount->idSubaccount,
                        1 => 'scheduled',
                        2 => $date
                    ),
                    'columns' => 'idMail, quantitytarget AS target'
        ));

        $balanceConsumedFind = \Saxs::findFirst(array(
                    'conditions' => 'idSubaccount = ?0 and idServices = ?1 and accountingMode = ?2 and status= ?3 ',
                    'bind' => array(
                        0 => $this->user->Usertype->subaccount->idSubaccount,
                        1 => 2,
                        2 => 'sending',
                        3 => 1
                    ),
                    'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
        ));

        $answer = ['mailFindPending' => $mailFindPending->toArray(), 'balanceConsumedFind' => $balanceConsumedFind->toArray()];

        return $answer;
    }

    public function sendmailnotmailbalance($data) {
        $amount = 0;
        foreach ($this->user->Usertype->subaccount->saxs as $key) {
            if ($key->idServices == 2 && $key->accountingMode == 'sending' && ($key->status == 1 || $key->status == '1')) {
                $amount = $data->totalAvailable;
                $totalAmount = $key->totalAmount;
                $subaccountName = $this->user->Usertype->Subaccount->name;
                $accountName = $this->user->Usertype->Subaccount->Account->name;
                $this->arraySaxs = array(
                    "amount" => $amount,
                    "totalAmount" => $totalAmount,
                    "subaccountName" => $subaccountName,
                    "accountName" => $accountName
                );
            }
        }
        $sendMailNot = new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
        //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de SMS
        $sendMailNot->sendMailNotification($this->arraySaxs);
        return true;
    }

    public function setstatusMail($idMail, $status) {
        $response = null;
        $mail = \Mail::findFirst(array(
                    'conditions' => 'idMail = ?0 AND idSubaccount = ?1 AND deleted = 0',
                    'bind' => array(0 => (string) $idMail, 1 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount)
        ));

        if (!$mail) {
            throw new \InvalidArgumentException("El idMail enviado no existe");
        }
        switch ($status) {

            case 0: // cancelar  
                if ($mail->status == 'sent') {
                    $response = "El envío ya se ha enviado";
                } else if ($mail->status == "sending") {
                    $mxc = \Mxc::count([["idMail" => $mail->idMail, "status" => 'sent']]);
                    if ($mxc == false || $mail->uniqueOpening = 0 || $mail->totalOpening = 0 || $mail->uniqueClicks = 0) {

                        $data = array(
                            "idMail" => $mail->idMail,
                            "nameFunc" => "cancel"
                        );
                        $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
                        $elephant->initialize();
                        $elephant->emit('cancel-send-mail', $data);
                        $elephant->close();
                        $mail->status = "canceled";
                        $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
                        $this->saveMail($mail);
                        $response = "El envío se ha cancelado correctamente";
                    } else {
                        $response = "Los correos están siendo entregados a sus respectivos destinatarios";
                    }
                } else if ($mail->status == "paused" || $mail->status == "scheduled") {

                    $mail->status = "canceled";
                    $mail->canceleduser = \Phalcon\DI::getDefault()->get("user")->email;
                    $this->saveMail($mail);
                    $response = "El correo ha sido cancelado";
                }
                break;
            case 1: // start
                switch ($mail->status) {
                    case 'paused':
                        $data = array(
                            "idMail" => $mail->idMail
                        );
                        $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
                        $elephant->initialize();
                        $elephant->emit('restart-send-mail', $data);
                        $elephant->close();
                        $response = "El correo ha sido reanudado";
                        break;
                    case 'scheduled':
                        $mail->scheduleDate = date("Y-m-d H:i:s", time());
                        $mail->confirmationDate = date("Y-m-d H:i:s", time());
                        $this->saveMail($mail);
                        $response = "El correo ha sido programado";
                        break;
                    case 'sent':
                        $response = "El envío ya se ha enviado";
                        break;
                }
                break;
            case 2: // pausar
                if ($mail->status == 'sent') {
                    $response = "El envío ya se ha enviado";
                } else if ($mail->status == 'sending') {
                    $data = array(
                        "idMail" => $mail->idMail,
                        "nameFunc" => "pause"
                    );
                    $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
                    $elephant->initialize();
                    $elephant->emit('pause-send-mail', $data);
                    $elephant->close();
                    $response = "El correo ha sido pausado";
                } else {
                    $response = "El correo no está en proceso de envío";
                }
                break;
        }

        return $response;
    }

    public function newMailSingle($data) {
        $flag = false;        
        $mail = $this->createNewMailSingle($data);
        if(isset($mail)){
        try {

            $Subaccount = $this->user->Usertype->Subaccount;
            $mailcontent = \MailContent::findFirst(array(
                        "conditions" => "idMail = ?0",
                        "bind" => array(0 => $mail->idMail)
            ));
            /**
             * Se reciben los datos que se envían desde la vista;
             */
            $target = json_decode($mail->target);
            $msg = "";

            /**
             * Valida que se hayan enviado correos
             */
            /*if (str_replace(" ", "", $target) === '') {
                throw new \InvalidArgumentException("No ha enviado ninguna dirección de correo, por favor verifique la información");
            }*/
            
            /**
             * Validación de los correos, que tengan el formato correcto y que no se repitan
             */
            $dataEmails = array();
            if(strpos($target->to, ",")){
             $emails = explode(",", $target->to);   
            }else{
             $emails = $target->to;   
            }
            
            if (is_array($emails) == true && count($emails) >10) {
                throw new \InvalidArgumentException("Sólo puede ingresar 10 correos");
            }
            
            if(is_array($emails) == true && count($emails)>1){
                foreach ($emails as $email) {
                    $em = str_replace(" ", "", $email);
                    if (!empty($em) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        if (!in_array($email, $dataEmails)) {
                            $dataEmails[] = $email;
                        } else {
                            throw new \InvalidArgumentException("El correo {$email} no debe estar repetido");
                        }
                    }
                }                
            }else{
              $dataEmails[] = $emails;  
            }
            /**
             * Comprobar si es un correo con adjuntos, si lo es se construye un arreglo con el nombre y el path de estos
             */
            $attach = array();
            //$dir = $this->asset->dir . $Subaccount->Account->idAccount . '/attachments/' . $mail->idMail . '/';
            $dir = $this->asset->dir . $Subaccount->Account->idAccount;
            $dirImage = $dir . '/images/';
            $dirAttachments = $dir . '/attachments/';
            $attachments = \Mailattachment::find(array(
                        "conditions" => "idMail = ?0",
                        "bind" => array($mail->idMail)
            ));
            if ($mail->attachment == 1 || $attachments) {

                if (count($attachments) > 0) {
                    foreach ($attachments as $value) {

                        $obj = new \stdClass();
                        $obj->name = $value->Asset->name;

                        if ($value->Asset->type == "File") {
                            $obj->path = $dirAttachments . $value->Asset->idAsset . '.' . $value->Asset->extension;
                        } else {
                            $obj->path = $dirImage . $value->Asset->idAsset . '.' . $value->Asset->extension;
                        }

                        if (is_readable($obj->path)) {
                            $attach[] = $obj;
                        }
                    }
                }
            }
            /**
             * Comienzo del proceso de envío de prueba
             */
            /* Se crea el transport y el swift */
            $transport = \Swift_SmtpTransport::newInstance($this->mta->address, $this->mta->port);
            $swift = \Swift_Mailer::newInstance($transport);

            $account = $this->user->Usertype->Subaccount->Account;
            /* Cargamos el dominio correspondiente de la cuenta para el envio */
            foreach ($account->AccountConfig->DetailConfig as $key) {
                if ($key->idServices == $this->services->email_marketing) {
                    $detailConfig = $key;
                }
            }
            $domain = $detailConfig->Dcxurldomain[0]->Urldomain;
            //$_POST['typecontent'] = $mailcontent->url;
            $sendTestMail = new \TestMail();
            $sendTestMail->setAccount($account);
            $sendTestMail->setDomain($domain);
            $sendTestMail->setUrlManager($this->urlManager);
            $sendTestMail->setMail($mail);
            $sendTestMail->setMailContent($mailcontent);
            $sendTestMail->setPersonalMessage($msg);
            $sendTestMail->load();
            $from = array($mail->Emailsender->email => $mail->NameSender->name);
            // Consultar el mail class para inyectarselo en las cabeceras del correo con switmailer
            $mailclass = \Mailclass::findFirstByIdMailClass($detailConfig->Dcxmailclass[0]->idMailClass);

            // Crear variables listID y sendID para inyectarlas a las cabeceras con swiftmailer
            $prefixID = \Phalcon\DI::getDefault()->get('instanceIDprefix')->prefix;
            if (!$prefixID || $prefixID == '') {
                $prefixID = '0em';
            }
            $listID = 't' . $prefixID . $mail->Subaccount->Account->idAccount;
            $sendID = $prefixID . $mail->idMail;

            // MTA a utilizar
            $mtaName = $detailConfig->Dcxmta[0]->Mta->name;
            //cambiar
            $mta = ($mtaName == null || trim($mtaName) === '') ? 'MTA_GENERAL_1' : $mtaName;
            $messagesSent = 0;
            foreach ($dataEmails as $email) {
                $message = new \Swift_Message();
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-GreenArrow-MailClass', $mailclass->name);
                $headers->addTextHeader('X-GreenArrow-MtaID', $mta);
                $headers->addTextHeader('X-GreenArrow-InstanceID', $sendID);
                $headers->addTextHeader('X-GreenArrow-ListID', $listID);
                $message->setFrom($from);
                $message->setSubject($mail->subject);
                $message->setTo(array($email => $email));

                //lo siguiente reemplaza algunas urls que empezaban con
                //"Https" las cuales eran erroneas por las correctas con minuscula.
                $planeHtmlBody = ereg_replace("Https", "https", $sendTestMail->getBody());
                //esto me permite visualizar las imagenes en los gestores de correo.
                //$message->setBody($sendTestMail->getBody(), "text/html");
                $message->setBody($planeHtmlBody, "text/html");
                $message->addPart($sendTestMail->getPlainText(), "text/plain");

                $replyto = ((isset($mail->idReplyTo)) ? $mail->ReplyTos->email : ((isset($mail->replyto)) ? $mail->replyto : null));

                if ($replyto != null) {
                    $message->setReplyTo($replyto);
                }

                if (count($attach) > 0) {
                    foreach ($attach as $at) {
                        $message->attach(\Swift_Attachment::fromPath($at->path)->setFilename($at->name));
                    }
                }
              $send = $swift->send($message, $failures);

                if (!$send) {
                    throw new \InvalidArgumentException("Ha ocurrido un error enviando el corrreo {$mail->name}");
                }else{
                    $messagesSent++;
                }
            }
            if ($send) {
                
                $mail->status = 'sent';
                $mail->sentprocessstatus = 'finished';
                $mail->messagesSent = $messagesSent;
                
                if (!$mail->save()) {
                    foreach ($mail->getMessages() as $message) {
                        throw new \InvalidArgumentException($message);
                    }
                }
                $sql = "CALL updateCountersSendingSaxs({$mail->idSubaccount})";
                $this->db->execute($sql);               
            } else {
                $mail->status = 'canceled';
                $mail->sentprocessstatus = 'finished';
                $mail->messagesSent = $messagesSent;
                if (!$mail->save()) {
                    foreach ($mail->getMessages() as $message) {
                        throw new \InvalidArgumentException($message);
                    }
                }
                $sql = "CALL updateCountersSendingSaxs({$mail->idSubaccount})";
                $this->db->execute($sql);
                
                throw new \InvalidArgumentException("Ha ocurrido un error mientras se intentaba enviar el correo, contacte al administrador");
            }
             return ["mail"=>["idMail"=>$mail->idMail,"status"=>$mail->status,"scheduleDate"=>$mail->scheduleDate,"target"=>$mail->target,"messageSent"=>$mail->messagesSent]];
        } catch (InvalidArgumentException $ex) {
            $this->logger->log("Exception while sendtestmail... {$ex}");
        } catch (Exception $ex) {
            $this->logger->log("Exception while sendtestmail... {$ex}");
        }
    }
       
    }
    
    public function createNewMailSingle($data) {
        $this->dataMail = $data;
        $expSender = explode("/", $this->dataMail['mail']['sender']);
        $emailSender = trim($expSender[0]);
        $nameSender = trim($expSender[1]);

        if (!$this->dataMail["mail"]["name"]) {
            throw new \InvalidArgumentException("Nombre de Campaña es obligatorio");
        }

        if (!isset($this->dataMail["mail"]["replyto"])) {
            throw new \InvalidArgumentException("Correo de responder dbe estar definido");
        }
        
        if (isset($this->dataMail["content"])) {
            if ($this->dataMail["content"]["type"] == "" || empty($this->dataMail["content"]["type"])) {
                throw new \InvalidArgumentException("Tipo de contenido debe estar definido");
            }
            if ($this->dataMail["content"]["content"] == "" || empty($this->dataMail["content"]["content"])) {
                throw new \InvalidArgumentException("Contenido debe estar definido");
            }
        }

        if ($this->validateMail($emailSender)) {
            throw new \InvalidArgumentException("Formato de correo invalido");
        }

        $idEmailSender = $this->createMailSender($emailSender);
        $idNameSender = $this->createMailName($nameSender);

        if (!empty($this->dataMail['mail']['replyto']) && $this->validateMail($this->dataMail['mail']['replyto'])) {
            throw new \InvalidArgumentException("Formato del correo de responder a es invalido");
        }

        \Phalcon\DI::getDefault()->get("db")->begin();
        $mailForm = new \MailForm();
        $mail = new \Mail();
        $mail->name = $this->dataMail['mail']["name"];
        $mail->subject = $this->dataMail['mail']["subject"];
        $mail->idSubaccount = $this->user->UserType->idSubaccount;
        $mail->idEmailsender = $idEmailSender;
        $mail->idNameSender = $idNameSender;
        $mail->status = "scheduled";
        $mail->replyto = $this->dataMail['mail']["replyto"];
        $mail->test = 0;
        $mail->sentprocessstatus = "loading-target";
        $mail->quantitytarget = count($this->dataMail["mail"]["to"]);
        $mail->externalApi = 1;
        $mail->type = "single";
        $mail->target = json_encode(array("to"=>$this->dataMail["mail"]["to"]));
        
        $mail->scheduleDate = date("Y-m-d H:i:s", time());
        $mail->confirmationDate = date("Y-m-d H:i:s", time());
        $mail->attachment = 0;
        $mail->pdf = 0;
        $mail->deleted = 0;
        $mail->gmt = "-0500";
        $mail->totalOpening = 0;
        $mail->errors = 0;
        $mail->createdBy = $this->user->email;
        $mail->updatedBy = $this->user->email;
        $mailForm->bind($this->dataMail['mail'], $mail);

        if (!$mailForm->isValid()) {
            foreach ($mailForm->getMessages() as $msg) {
                throw new \InvalidArgumentException($msg);
            }
        }

        $this->validateBalanceMail($mail);

        if (!$mail->save()) {
            foreach ($mail->getMessages() as $message) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \InvalidArgumentException($message);
            }
        }

        foreach ($this->dataMail['mail']['category'] as $category) {
            $mxmc = new \Mxmc();
            $mxmc->idMail = $mail->idMail;
            $mxmc->idMailCategory = $category;

            if (!$mxmc->save()) {
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \InvalidArgumentException("Ocurrio un error");
            }
        }

        if ($this->dataMail['content']['type'] == "url") {
            $this->urlEditor($mail->idMail, $this->dataMail['content']['content']);
        } else if ($this->dataMail['content']['type'] == "html") {
            $contentmail = new \MailContent();
            $contentmail->idMail = $mail->idMail;

            $contentmail->typecontent = "html";
            $cont = str_replace("\xE2\x80\x8B", "", $this->dataMail['content']['content']);
            $contentmail->content = $cont;

            if (!$contentmail->save()) {
                foreach ($contentmail->getMessages() as $msg) {
                    \Phalcon\DI::getDefault()->get("logger")->log("Error while saving content mail {$msg}");
                }
                \Phalcon\DI::getDefault()->get("db")->rollback();
                throw new \Exception('Error while saving content mail');
            }
        } else if ($this->dataMail['content']['type'] == "template") {

            if (is_numeric($this->dataMail['content']['content'])) {

                $template = \MailTemplateContent::findFirst([
                            "conditions" => "idMailTemplate = ?0",
                            "bind" => array($this->dataMail['content']['content'])
                ]);

                if (!$template) {
                    throw new \InvalidArgumentException("La plantilla de correo no esta registrada");
                }

                $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;

                if ($template->MailTemplate->idAccount != null && $template->MailTemplate->idAccount != $idAccount) {
                    throw new \InvalidArgumentException("La plantilla no pertenece a la cuenta");
                }
                //
                $contentMail = new \MailContent();
                $contentMail->idMail = $mail->idMail;
                $contentMail->typecontent = 'Editor';
                $contentMail->content = $template->content;
                $contentMail->createdBy = time();
                $contentMail->updatedBy = time();
                //
                $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
                $editorObj->setAccount($this->user->Usertype->Subaccount->Account);
                $editorObj->assignContent(json_decode($template->content));
                $mail_content = $editorObj->render();
                $text = new \PlainText();
                $plainText = $text->getPlainText($mail_content);
                $contentMail->plaintext = $plainText;
                //
                if (!$contentMail->save()) {
                    \Phalcon\DI::getDefault()->get("db")->rollback();
                    foreach ($contentMail->getMessages() as $msg) {
                        $this->logger->log("Message: {$msg}");
                        throw new \InvalidArgumentException($msg);
                    }
                }
            }
        }

        \Phalcon\DI::getDefault()->get("db")->commit();

        return $mail;
    }
}
