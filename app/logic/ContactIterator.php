<?php

class ContactIterator implements Iterator {

  public $mail,
          $contacts,
          $start,
          $offset,
          $manager;

  const ROWS_PER_FETCH = 1000;

  /**
   * ContactIterator constructor.
   * @param Mail $mail
   * @param $manager
   * @internal param $fields
   */
  public function __construct(Mail $mail, $manager) {
    $this->mail = $mail->idMail;
    $this->manager = $manager;
  }

  public function extractContactsFromDB($start = 0) {
    unset($this->contacts);

    $options = array(
        'limit' => self::ROWS_PER_FETCH,
        'sort' => array(
            'idContact' => 1
        )
    );
    $queryGt = array("idMail" => $this->mail, "idContact" => array('$gt' => $start), "status" => "scheduled");
    $queryMxc = new MongoDB\Driver\Query($queryGt, $options);
    $this->contacts = $this->manager->executeQuery("aio.mxc", $queryMxc)->toArray();

    /* var_dump($this->contacts[1]);
      exit; */
    if (count($this->contacts) <= 0) {
      return false;
    }

    $this->offset = 0;
    $end = end($this->contacts);
    $this->start = $end->idContact;

    return true;
  }

  /**
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current() {
    //var_dump(__METHOD__);
    return $this->contacts[$this->offset];
  }

  /**
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next() {
    //var_dump(__METHOD__);
    $this->offset++;
  }

  /**
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key() {
    //var_dump(__METHOD__);
    return $this->contacts[$this->offset]->idContact;
  }

  /**
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
   */
  public function valid() {
    //var_dump(__METHOD__);
    $cnt = count($this->contacts);
    if (($cnt - $this->offset) <= 0) {
      if ($this->extractContactsFromDB($this->start)) {
        return true;
      } else {
        return false;
      }
    } else {
      return true;
    }
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind() {
    //var_dump(__METHOD__);
    $this->start = 0;
    $this->contacts = array();
    $this->offset = 0;
  }

}
