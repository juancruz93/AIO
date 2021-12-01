<?php

class Pdfmail extends Modelbase {
    public $idPdfmail,
                $idMail,
                $idContact,
                $name,
                $size,
                $type,
                $createdon;

    public function initialize() {
        $this->belongsTo("idMail", "Mail", "idMail",   array("foreignKey" => true));
    }
}

