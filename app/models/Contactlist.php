<?php

use Phalcon\Mvc\Model\Validator\PresenceOf;

class Contactlist extends Modelbase {

  public $idContactlist;
  public $idSubaccount;
  public $ctotal;
  public $cunsubscribed;
  public $cactive;
  public $cspam;
  public $cbounced;
  public $created;
  public $updated;
  public $name;
  public $description;
  public $cblocked;
  public $createdBy;
  public $updatedBy;
  public $deleted;
  public $idContactlistCategory;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idContactlist", "Cxcl", "idContactlist");
    $this->belongsTo("idSubaccount", "Subaccount", "idSubaccount");
    $this->hasMany("idContactlist", "Customfield", "idContactlist");
    $this->belongsTo("idContactlist", "Form", "idContactlist");
    $this->belongsTo("idContactlistCategory", "ContactlistCategory", "idContactlistCategory");
  }

  public function validation() {
    $this->validate(new PresenceOf(array(
        'field' => 'name',
        'message' => 'Debe colocar un nombre a la lista de contactos, por favor valide la información'
    )));
  }

//  public function afterFetch() {
//    $this->created = date("d/M/Y H:i", $this->created);
//    $this->updated = date("d/M/Y H:i", $this->updated);
//  }
}
