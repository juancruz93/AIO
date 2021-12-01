<?php

class Importcontactfile extends Modelbase
{
    public $idImportcontactfile,
        $idSubaccount,
        $idImportfile,
        $rows,
        $processed,
        $imported,
        $repeated,
        $exists,
        $limitexceeded,
        $status,
        $header,
        $delimiter,
        $dateformat,
        $importmode,
        $update,
        $importrepeated,
        $fieldsmap,
        $created,
        $updated,
        $importRepeatedFile;

    public function initialize()
    {
        $this->belongsTo("idImportfile", "Importfile", "idImportfile");
        $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    }


}
