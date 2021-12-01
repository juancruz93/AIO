<?php

namespace Sigmamovil\General\Misc;

class AutoresponderMailManager
{
  private $targetMail,
      $autoresponder;

  /**
   * AutoresponderMailManager constructor.
   * @param $autoresponder
   */
  public function __construct(\Autoresponder  $autoresponder)
  {
    $this->autoresponder = $autoresponder;
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

//  /**
//   * @param mixed $targetMail
//   */
//  public function setTargetMail($targetMail)
//  {
//    $this->targetMail = $targetMail;
//  }

  public function cloneAutoresponder()
  {
    $mail = new \Mail();
    $mail->idSubaccount = $this->autoresponder->idSubaccount;
    $mail->idEmailsender = $this->autoresponder->idEmailsender;
    $mail->idNameSender = $this->autoresponder->idNameSender;
    $mail->idAutoresponder = $this->autoresponder->idAutoresponder;
    $mail->name = $this->autoresponder->name;
    $mail->replyto = $this->autoresponder->replyTo;
    $mail->subject = $this->autoresponder->subject;
    $mail->scheduleDate = date('Y-m-d H:i');
    $mail->confirmationDate = date('Y-m-d H:i');
    $mail->gmt = "-0500";
    $mail->target = $this->autoresponder->target;
    $mail->type = "autoresponder";
    $mail->test = 0;
    $mail->status = 'scheduled';
    $mail->createdBy = $this->autoresponder->AutoresponderContent->createdBy;
    $mail->updatedBy = $this->autoresponder->AutoresponderContent->updatedBy;
    if($this->autoresponder->optionAdvance == 1 && $this->autoresponder->customFields != ""){
        $mail->singleMail = 1;
    }
    if (!$mail->save()) {
      foreach ($mail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    $html = $this->getContentHtml($this->autoresponder->AutoresponderContent->content);
    $plainText = new \PlainText();

    $contentMail = new \MailContent();
    $contentMail->idMail = $mail->idMail;
    $contentMail->typecontent = $this->autoresponder->AutoresponderContent->type;
    $contentMail->content = $this->autoresponder->AutoresponderContent->content;
    $contentMail->plaintext = $plainText->getPlainText($html);
    $contentMail->createdBy = $this->autoresponder->AutoresponderContent->createdBy;
    $contentMail->updatedBy = $this->autoresponder->AutoresponderContent->updatedBy;
    if (!$contentMail->save()) {
      foreach ($contentMail->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  protected function getContentHtml($content)
  {
    $html = '';
    if ($this->autoresponder->AutoresponderContent->type == 'editor') {
      $editor = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editor->setAccount($this->autoresponder->Subaccount->Account);
      $editor->assignContent(json_decode($content));
      $html = $editor->render();
    } else if ($this->autoresponder->AutoresponderContent->type == 'html') {
      $html = $content;
    }
    return $html;
  }
}