<?php

class TestMail {

  public $mail;
  public $mailContent;
  public $body;
  public $plainText;
  public $message;
  public $account;
  public $domain;
  public $urlManager;
  public $logger;

  public function __construct() {
    $this->logger = Phalcon\DI::getDefault()->get('logger');
  }

  public function setMail(Mail $mail) {
    $this->mail = $mail;
	if($this->mail->MailContent->typecontent=='html'){
		$_POST['typecontent'] = "mail/htmlcontent";
	}
  }

  public function setMailContent(Mailcontent $mailContent) {
    $this->mailContent = $mailContent;
  }

  public function setContent($content) {
    $this->content = $content;
  }

  public function setPersonalMessage($message = null) {
    $this->message = $message;
  }

  public function setAccount(Account $account) {
    $this->account = $account;
  }

  public function setDomain(Urldomain $domain) {
    $this->domain = $domain;
  }

  public function setUrlManager($urlManager) {
    $this->urlManager = $urlManager;
  }

  public function load() {
    
    $this->createBody();
    $this->createPlaintext();
    $this->replaceCustomFields();
    $this->replaceUrlImages(false);
	if(isset($_POST['typecontent'])){
		unset($_POST['typecontent']);	
	}	
  }

  public function transformContent() {
    $editorObj = new Sigmamovil\Logic\Editor\HtmlObj();
    $editorObj->setAccount($this->account);
    $editorObj->assignContent(json_decode($this->content));
    $content = $editorObj->replacespecialchars($editorObj->render());
    $this->body = utf8_decode($content);

    $text = new PlainText();
    $this->plainText = $text->getPlainText($this->body);

    $imageService = new ImageService($this->account, $this->domain, $this->urlManager);
    $linkService = new LinkService($this->account, null);
    $prepareMail = new PrepareMailContent($linkService, $imageService, false);
    list($this->body, $links) = $prepareMail->processContent($this->body, false);
  }

  protected function createBody() {
    
    if ($this->mail->MailContent->typecontent == 'Editor') {
      $editorObj = new Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->setAccount($this->account);
      $editorObj->assignContent(json_decode($this->mailContent->content));
      $content = $editorObj->replacespecialchars($editorObj->render());
    } else {
//      var_dump("hola");
//      exit();
      $footerObj = new Sigmamovil\General\Misc\FooterObj();
//      var_dump("hola1");
//      exit();
      $footerObj->setAccount($this->account);
      
      $content = $footerObj->addFooterInHtml(html_entity_decode($this->mailContent->content));
    }
    
    
    if (!empty($this->message)) {
      $replace = '<body>
							<center>
								<table border="0" cellpadding="0" cellspacing="0" width="600px" style="border-collapse:collapse;background-color:#444444;border-top:0;border-bottom:0">
									<tbody>
										<tr>
											<td align="center" valign="top" style="border-collapse:collapse">
												<span style="padding-bottom:9px;color:#eeeeee;font-family:Helvetica;font-size:12px;line-height:150%">"' . utf8_decode($this->message) . '" - ' . $this->mail->Emailsender->email . '</span>
											</td>
										</tr>
									</tbody>
								</table>
							</center>';

      $content = str_replace('<body>', $replace, $content);
    }

    $this->body = utf8_decode($content);
  }

  protected function createPlaintext() {
    $text = new PlainText();
    $this->plainText = $text->getPlainText($this->body);
  }

  /**
   * Reemplaza las variables en el correo por los datos de la gestión inteligente, se le pasa
   * un arreglo con los datos en este orden: 
   * correo, puntos ganados, puntos totales, correo, puntos ganados, puntos totales
   * se deben de repetir como se muestra en el ejemplo
   * @param type Array
   */
  public function replaceVariablesForSmart($replace) {
    $search = array('%%CORREO%%', '%%PUNTOS_GANADOS%%', '%%PUNTOS_TOTALES%%', '%%MAIL%%', '%%SCORED%%', '%%SCORE%%');
    $this->body = str_replace($search, $replace, $this->body);
    $this->plainText = str_replace($search, $replace, $this->plainText);
  }

  protected function replaceCustomFields() {
    preg_match_all('/%%([a-zA-Z0-9_\-]*)%%/', $this->plainText, $textFields);
    preg_match_all('/%%([a-zA-Z0-9_\-]*)%%/', $this->body, $htmlFields);

    $result = array_merge($textFields[0], $htmlFields[0]);

    $search = array_unique($result);
    $replace = array();
    foreach ($search as $s) {
      $replace[] = strtolower(substr($s, 2, -2));
    }

    $this->body = str_replace($search, $replace, $this->body);
    $this->plainText = str_replace($search, $replace, $this->plainText);
  }

  protected function replaceUrlImages($link_service = true) {
    $imageService = new ImageService($this->account, $this->domain, $this->urlManager);
    $linkService = new LinkService($this->account, $this->mail);
    $prepareMail = new PrepareMailContent($linkService, $imageService, false);
    list($this->body, $links) = $prepareMail->processContent($this->body, $link_service,$this->mail);
  }

  public function getBody() {
    return $this->body;
  }

  public function getPlainText() {
    return $this->plainText;
  }

}
