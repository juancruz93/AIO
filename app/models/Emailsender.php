<?php

class Emailsender extends Modelbase
{
    public $idEmailsender,
        $idAccount,
        $email,
        $status;

    public function initialize()
    {
        $this->belongsTo("idAccount", "Account", "idAccount");
        $this->hasMany("idEmailsender", "Mail", "idEmailsender");
        $this->hasMany("idEmailsender", "Autoresponder", "idEmailsender");
    }

}
