<?php

class MailpreviewController extends ControllerBase
{
    public function previewAction()
    {
        if ($this->request->isPost()) {
            $content = $this->request->getPost("editor");
            $this->session->remove("mail-preview-{$this->user->idUser}");
            
            $url = $this->url->get('mailpreview/createpreview');

            $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj(true, $url);
            $editorObj->setAccount(((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null)));
            $editorObj->assignContent(json_decode($content));

            $this->session->set("mail-preview-{$this->user->idUser}", $editorObj->render());

            return $this->set_json_response(array('message' => 'preview processed success'), 201, 'success');
        }
        
        return $this->set_json_response(array('message' => 'error happens'), 404, 'error');
    }

    public function createpreviewAction()
    {
        $content = $this->request->getPost("imgData");
        
        $imgObj = new \Sigmamovil\General\Misc\ImageObject();
        
//        $this->logger->log("IMG: {$content}");
        
        $imgObj->createFromBase64($content);
        $imgObj->resizeImage(200, 300);
        $newImg = $imgObj->getImageBase64();

        $this->cache->save("preview-img64-cache-{$this->user->idUser}", $newImg, 7200);
    }
    
    public function previewdataAction()
    {
        $htmlObj = $this->session->get("mail-preview-{$this->user->idUser}");
        $this->session->remove("mail-preview-{$this->user->idUser}");

        $this->view->disable();
//		$this->logger->log($htmlObj);
        return $this->response->setContent($htmlObj);
    }
}
