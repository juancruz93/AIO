<?php

class SystemmailController extends ControllerBase
{
    public function indexAction()
    {
      
        $currentPage = $this->request->getQuery('page', null, 1);
        
        $builder = $this->modelsManager->createBuilder()
            ->from('Systemmail')	
            ->where('idAllied = '.\Phalcon\DI::getDefault()->get('user')->UserType->idAllied." AND deleted=0")
            ->orderBy('created');

        $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit"=> \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
            "page" => $currentPage
        ));
		
        $page = $paginator->getPaginate();
		
        $this->view->setVar("page", $page);
    }
    
    public function createAction()
    {
        try {
            if ($this->request->isPost()) {
                $name = $this->request->getPost("name");
                $desc = $this->request->getPost("desc");
                $category = $this->request->getPost("category");
                $editor = $this->request->getPost("editor");
                $subject = $this->request->getPost("subject");
                $fromEmail = $this->request->getPost("fromEmail");
                $fromName = $this->request->getPost("fromName");

                if (empty($name)) {
                    throw new Exception('No ha enviado un nombre para el correo, por favor valide la información');
                }
                
                if (empty($desc)) {
                    throw new Exception('No ha enviado una descripción para el correo, por favor valide la información');
                }
                
                if (empty($category)) {
                    throw new Exception('No ha enviado una categpría para el correo, por favor valide la información');
                }
                
                if (empty($subject)) {
                    throw new Exception('No ha enviado un asusnto para el correo, por favor valide la información');
                }
                
                if (strlen($subject) > 80) {
                    throw new Exception('El asunto del correo debe tener máximo 80 caracteres, por favor valide la información');
                }
              
                if (empty($fromName)) {
                    throw new Exception('No ha enviado un nombre de remitente para el correo, por favor valide la información');
                }
                
                if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('El correo de remitente es invalido, por favor valide la información');
                }

                $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
                $editorObj->assignContent(json_decode($editor));
                $content = $editorObj->render();

                $text = new \Sigmamovil\General\Misc\PlainText();
                $plainText = $text->getPlainText($content);
                
                $previewData = null;
                if ($this->cache->exists("preview-img64-cache-{$this->user->idUser}")) {
                    $previewData = $this->cache->get("preview-img64-cache-{$this->user->idUser}");
                } 
                
                $system = new Systemmail();
                $system->name = $name;
                $system->description = $desc;
                $system->category = $category;
                $system->content = $editor;
                $system->plainText = $plainText;
                $system->subject = $subject;
                $system->fromEmail = $fromEmail;
                $system->fromName = $fromName;
                $system->previewData = $previewData;
                $system->idAllied = \Phalcon\DI::getDefault()->get('user')->UserType->idAllied;
                $system->created = time();
                $system->updated = time();
                
                if (!$system->save()) {
                    foreach ($system->getMessages() as $message) {
                        throw new Exception($message);
                    } 
                }
                
                $this->notification->success('Se ha guardado el correo interno exitosamente');
                return $this->set_json_response(array('message' => 'ok'), 200, 'success');
            }
        } 
        catch (Exception $ex) {
            return $this->set_json_response(array('message' => $ex), 404, 'error');
        }
    }
    
    public function deleteAction($id)
    {
        $smail = Systemmail::findFirst(array(
            'conditions' => 'idSystemmail = ?1',
            'bind' => array(1 => $id)
        ));
        
        if (!$smail) {
            $this->notification->error('No se ha encontrado el correo interno, por favor valide la información');
            return $this->response->redirect('systemmail');
        }
        
        try {
            $smail->delete();
            $this->notification->warning('Se ha eliminado el correo interno exitosamente');
            return $this->response->redirect('systemmail');
        } 
        catch (Exception $ex) {
            $this->notification->error('Ha ocurrido un error por favor contacte al administrador');
            return $this->response->redirect('systemmail');
        }
    }
    
    public function editAction($id)
    {
        $system = Systemmail::findFirst(array(
            'conditions' => 'idSystemmail = ?1',
            'bind' => array(1 => $id)
        ));
        
        if (!$system) {
            $this->notification->error('No se ha encontrado el correo interno, por favor valide la información');
            return $this->response->redirect('systemmail');
        }
        
        $this->view->setVar('smail', $system);
        
        if ($this->request->isPost()) {
            try {
                $name = $this->request->getPost("name");
                $desc = $this->request->getPost("desc");
                $category = $this->request->getPost("category");
                $editor = $this->request->getPost("editor");
                $subject = $this->request->getPost("subject");
                $fromEmail = $this->request->getPost("fromEmail");
                $fromName = $this->request->getPost("fromName");

                if (empty($name)) {
                    throw new Exception('No ha enviado un nombre para el correo, por favor valide la información');
                }
                
                if (empty($desc)) {
                    throw new Exception('No ha enviado una descripción para el correo, por favor valide la información');
                }
                
                if (empty($category)) {
                    throw new Exception('No ha enviado una categpría para el correo, por favor valide la información');
                }
                
                if (empty($subject)) {
                    throw new Exception('No ha enviado un asusnto para el correo, por favor valide la información');
                }
                
                if (strlen($subject) > 80) {
                    throw new Exception('El asunto del correo debe tener máximo 80 caracteres, por favor valide la información');
                }
              
                if (empty($fromName)) {
                    throw new Exception('No ha enviado un nombre de remitente para el correo, por favor valide la información');
                }
                
                if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('El correo de remitente es invalido, por favor valide la información');
                }
                
                $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
                $editorObj->assignContent(json_decode($editor));
                $content = $editorObj->render();

                $text = new \Sigmamovil\General\Misc\PlainText();
                $plainText = $text->getPlainText($content);
                
                $previewData = null;
                if ($this->cache->exists("preview-img64-cache-{$this->user->idUser}")) {
                    $previewData = $this->cache->get("preview-img64-cache-{$this->user->idUser}");
                } 
                
                $system->name = $name;
                $system->description = $desc;
                $system->category = $category;
                $system->content = $editor;
                $system->plainText = $plainText;
                $system->subject = $subject;
                $system->fromEmail = $fromEmail;
                $system->fromName = $fromName;
                $system->previewData = $previewData;
                $system->created = time();
                $system->updated = time();
                
                if (!$system->save()) {
                    foreach ($system->getMessages() as $message) {
                        throw new Exception($message);
                    } 
                }
                
                $this->notification->success('Se ha guardado el correo interno exitosamente');
                return $this->set_json_response(array('message' => 'ok'), 200, 'success');
            } 
            catch (Exception $ex) {
                return $this->set_json_response(array('message' => $ex), 404, 'error');
            }
        }
    }
}
