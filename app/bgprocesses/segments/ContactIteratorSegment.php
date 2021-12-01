<?php

/**
 * Description of ContactIterator
 *
 * @author juan.pinzon
 */
class ContactIteratorSegment implements Iterator {

  private $contacts;
  private $contact;
  private $position;
  private $idContacts;

  public function __construct() {
    $this->setPosition(0);
  }

  public function findContacts() {
    $contacts = $this->getIdContacts();
    $this->setContacts($contacts);
  }

  public function current() {
    $idContact = $this->getContacts()["idContacts"][$this->getPosition()];
    $idContactlist = $this->getContacts()["idContactlists"][$this->getPosition()];

    $contact = array(
        "contact" => $this->findContact($idContact),
        "customfield" => $this->findCustomField($idContact, $idContactlist),
        "idContactlist" => $idContactlist
    );

    return $contact;
  }

  public function next() {
    $this->setPosition($this->getPosition() + 1);
  }

  public function key() {
    return $this->getPosition();
  }

  public function valid() {
    return isset($this->getContacts()["idContacts"][$this->getPosition()]);
  }

  public function rewind() {
    $this->position = 0;
  }

  public function getContacts() {
    return $this->contacts;
  }

  public function setContacts($contacts) {
    $this->contacts = $contacts;
  }

  public function getPosition() {
    return $this->position;
  }

  public function setPosition($position) {
    $this->position = $position;
  }

  public function getIdContacts() {
    return $this->idContacts;
  }

  public function setIdContacts($idContacts) {
    $this->idContacts = $idContacts;
  }

  public function getContact() {
    return $this->contact;
  }

  public function setContact($contact) {
    $this->contact = $contact;
  }

  private function findContact($idContact) {
    $contact = Contact::findFirst(array(
                "conditions" => array(
                    "idContact" => (int) $idContact
                )
    ));
    
    if (!isset($contact)) {
      return null;
    }
    
    return $contact;
  }

  private function findCustomField($idContact, $idContactlist) {
    $cxc = Cxc::findFirst(array(
                "conditions" => array(
                    "idContact" => (int) $idContact,
                ),
                "fields" => array(
                    "idContactlist" => true
                )
    ));

    if (empty($cxc->idContactlist[$idContactlist])) {
      return null;
    }

    return array_filter($cxc->idContactlist[$idContactlist]);
  }

}
