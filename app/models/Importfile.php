<?php

class Importfile extends Modelbase
{
    public $idImportfile,
        $idContactlist,
        $idUser,
        $internalname,
        $originalname,
        $created;

    public function initialize(){
        $this->belongsTo("idContactlist", "Contactlist", "idContactlist");
        $this->belongsTo("idUser", "User", "idUser");
        $this->hasMany("idImportfile", "Importcontactfile", "idImportfile");
    }
}
