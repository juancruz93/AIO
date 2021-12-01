<?php

/**
 * Description of MailLote
 *
 * @author dayana
 */
class Maillote extends Modelbase {

    public $idMailLote,
            $idMail,
            $bounced,
            $bouncedCode,
            $scheduleDate,
            $email,
            $name,
            $lastname,
            $birthdate,
            $indicative,
            $phone,
            $status,
            $open,
            $totalClicks,
            $uniqueClicks,
            $totalOpening,
            $spam,
            $unsubscribed,
            $customfield;

    public function initialize() {
        $this->belongsTo("idMail", "Mail", "idMail");
    }

}
