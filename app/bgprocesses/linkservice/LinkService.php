<?php

ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/ImageService.php");
require_once(__DIR__ . "/../sender/LinkService.php");
require_once(__DIR__ . "/../sender/PrepareMailContent.php");
require_once(__DIR__ . "/../sender/TrackingUrlObject.php");
require_once(__DIR__ . "/../sender/CustomfieldManager.php");


if (isset($argv[1])) {
  $idmail = $argv[1];
  $account = $argv[2];
}

$account = Account::findFirst(array("conditions" => "idAccount = ?0", "bind" => array($account)));
$domain = Urldomain::findFirst(array("conditions" => "idUrldomain = ?0", "bind" => array($account->accountclassification->idUrldomain)));
$urlManager = Phalcon\DI\FactoryDefault::getDefault()->get("urlManager");
$mail = Mail::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idmail)));
$contentMail = MailContent::findFirst(array("conditions" => "idMail = ?0", "bind" => array($idmail)));


if ($contentMail->typecontent == "Editor") {
  $contentMail->url = "mail/contenteditor";
  $editor = new Sigmamovil\Logic\Editor\HtmlObj();
  $editor->setAccount($account);
  $editor->assignContent(json_decode($contentMail->content));
  $html = $editor->render();
} else if ($contentMail->typecontent == "html") {

  $contentMail->url = "mail/htmlcontent";
  $footerObj = new Sigmamovil\General\Misc\FooterObj();
  $footerObj->setAccount($account);
  $html = $footerObj->addFooterInHtml(html_entity_decode($contentMail->content));
}

$imageService = new ImageService($account, $domain, $urlManager);
$linkService = new LinkService($account, $mail);
$prepareMail = new PrepareMailContent($linkService, $imageService);
list($contens, $links) = $prepareMail->processContent($html);

$customfieldManager = new CustomfieldManager($contentMail, $urlManager);
$content = $customfieldManager->prepareUpdatingForms($contens);
$field = $customfieldManager->searchCustomFields($content);





$contacts = Mxc::find(["limit" => 10]);

foreach ($contacts as $contact) {

  $contentss = $customfieldManager->processCustomFields($contact, $field, $content);
  \Phalcon\DI::getDefault()->get('logger')->log($contentss["html"]);
}
exit();



if ($mail->attachment == 1) {
  $attachments = Mailattachment::find(array(
              'conditions' => 'idMail = ?1',
              'bind' => array(1 => $mail->idMail)
  ));
//echo count($attachments);
//exit;
  if (count($attachments) > 0) {
    foreach ($attachments as $att) {
      $attPath = $dir . $att->fileName;

      $obj = new stdClass();
      $obj->name = $att->fileName;
      $obj->path = $attPath;
      $attach[] = $obj;
    }
  }
}

echo count($attach);
exit;

$html = $contens;
$trackingObj = new TrackingUrlObject();

$htmlWithTracking = $trackingObj->getTrackingUrl($html, 35, 26, $links);
\Phalcon\DI::getDefault()->get('logger')->log($htmlWithTracking);
exit();
