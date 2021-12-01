<?php

class SendingcategoryController extends ControllerBase
{
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', null, 1);
        $builder = $this->modelsManager->createBuilder()
            ->from('Sendingcategory')
            ->where("Sendingcategory.idAccount = {$this->user->idAccount}")
            ->orderBy('Sendingcategory.created');
        
        $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit"=> 15,
            "page" => $currentPage
        ));
        
        $page = $paginator->getPaginate();

        $this->view->setVar("page", $page);
    }
    
    public function createAction()
    {
        $sendingcategory = new Sendingcategory();
        $sendingcategoryForm = new SendingcategoryForm($sendingcategory);
        
        if($this->request->isPost()){
            try{
                $sendingcategoryForm->bind($this->request->getPost(), $sendingcategory);    
                
                $name = $sendingcategoryForm->getValue('name');

                $namevalidate = Sendingcategory::findFirst(array(
                    "conditions" => "name = ?1 AND idAccount = ?2",
                    "bind" => array(1 => $name,
                                    2 => $this->user->idAccount)
                ));

                if($namevalidate){
                    throw new Exception("El nombre de la categoria ya existe, por favor valide la información");                
                }
                
                $sendingcategory->idAccount = $this->user->idAccount;
                $sendingcategory->created = time();
                $sendingcategory->updated = time();

                if(!$sendingcategory->save()){
                    foreach ($sendingcategory->getMessages() as $msg){
                        throw new Exception($msg);
                    }
                    $this->trace('fail', 'No se logro crear la categoria de envios');
                }

                $this->notification->success("Se ha creado la categoria correctamente");
                $this->trace('success', "Se creo un MTA con ID: {$sendingcategory->idSendingcategory}");
                return $this->response->redirect('sendingcategory');
            }
            catch (Exception $e){
                $this->notification->error($e->getMessage());            
            }                              
        }
        $this->view->setVar('sendingcategoryForm', $sendingcategoryForm);
    }

    
    public function editAction($idSendingcategory)
    {
        $sendingcat = Sendingcategory::findFirst(array(
                "conditions" => "idSendingcategory = ?1",
                "bind" => array(1 => $idSendingcategory)
            ));
        
        if (!$sendingcat){
            $this->notification->error('La categoria que desea editar no existe, por favor valide la información');
            return $this->response->redirect('sendingcategory');
        }
        
        $form = new SendingcategoryForm($sendingcat);                
        
        $this->view->setVar('form', $form);
        $this->view->setVar('sendingcat', $sendingcat);
        
        if($this->request->isPost()){
            
            try{
                $form->bind($this->request->getPost(), $sendingcat);
            
                $name = $form->getValue('name');

                $namevalidate = Sendingcategory::findFirst(array(
                    "conditions" => "name = ?1 AND idAccount = ?2",
                    "bind" => array(1 => $name,
                                    2 => $this->user->idAccount)
                ));

                if($namevalidate){
                    throw new Exception("El nombre de la categoria ya existe, por favor valide la información");                
                }

                $sendingcat->idAccount = $this->user->idAccount;
                $sendingcat->updated = time();

                if(!$sendingcat->save()){
                    foreach ($sendingcat->getMessages() as $msg){
                        throw new Exception($msg);
                    }
                    $this->trace('fail', "No se logro editar la categoria con ID: {$sendingcat->idSendingcategory}");
                }
                else{
                    $this->notification->success("La categoria se actualizo correctamente");
                    $this->trace('success', "Se actualizo la categoria con ID: {$sendingcat->idSendingcategory}");
                    return $this->response->redirect('sendingcategory');
                }
            }
            catch (Exception $e){
                $this->notification->error($e->getMessage());            
            }
        }
    }
    
    public function deleteAction($idSendingcategory)
    {
        $sendingcategory = Sendingcategory::findFirst(array(
            "conditions" => "idSendingcategory = ?1",
            "bind" => array(1 => $idSendingcategory)
        ));
        
        try{
            if($sendingcategory->delete()){
            $this->notification->warning("Se ha eliminado la categoria correctamente");
            $this->trace('success', "Se elimino la categoria de envio con ID: {$sendingcategory->idSendingcategory}");
            return $this->response->redirect('sendingcategory');
            }         
            throw new Exception("Lo sentimos, ocurrio un error durante la elmiminacion de la categoria");
            $this->trace("fail", "No se logro eliminar la categoria con ID: {$sendingcategory->idSendingcategory}");
            return $this->response->redirect('sendingcategory');        
        }
        catch (Exception $e){
            $this->notification->error($e->getMessage());
        }
    }
}
