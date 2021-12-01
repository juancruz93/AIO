<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$cleanMailbox = new cleanMailbox();
$cleanMailbox->index();

class CleanMailbox {
  
  public function index(){
    $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
    $testname = 'CODERE';
    $username = 'emailtosms@sigmamovil.com';
    $password = 'emsms2017+';
    
    $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
    $mails = imap_search($inbox, "ALL");
    if(!is_array($mails)){
      return;
    }
    $reverse = array_reverse($mails);
    foreach($reverse as $value){
      //recorre todos los id la posicion del buzon para luego mandarlo a otro buzon
      imap_mail_move($inbox, $value, $testname);
      imap_setflag_full($inbox, $value, "\\Seen \\Flagged");
    }
    imap_close($inbox);
  }
}
