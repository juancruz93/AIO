<?php

class AccountclassificationController extends ControllerBase
{
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', null, 1);
        $builder = $this->modelsManager->createBuilder()
            ->from('Accountclassification') 
            ->join('Mta', 'Mta.idMta = Accountclassification.idMta')    
            ->join('Adapter', 'Adapter.idAdapter = Accountclassification.idAdapter')
            ->join('Urldomain', 'Urldomain.idUrldomain = Accountclassification.idUrldomain')
            ->join('Mailclass', 'Mailclass.idMailClass = Accountclassification.idMailClass')
            ->orderBy('Accountclassification.created');

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
        $account = new Accountclassification();
        $account_form = new AccountclassificationForm();
        
        if($this->request->isPost()){
            
            $account_form->bind($this->request->getPost(), $account);
            
            $cant = $this->request->getPost('cant');
            $date = $this->request->getPost('date');
            $senderAllowed = $this->request->getPost('senderAllowed');
            $footerEditable = $this->request->getPost('footerEditable');
            $mail = $this->request->getPost('mailLimit');
            
            if($mail == 0){
                $account->mailLimit = $mail;
            }
                        
            $account->created = time();
            $account->updated = time();
            $account->expiryDate = "{$cant} {$date}";            
            $account->senderAllowed = (empty($senderAllowed) ? 0 : 1);
            $account->footerEditable = (empty($footerEditable) ? 0 : 1);
            
            if (!$account->save()) {
                foreach ($account->getMessages() as $message) {
                    $this->notification->error($message);
                }
                $this->trace("fail", "No se creo la clasificación de la cuenta");
            }
            else {
                $this->notification->success("Se ha creado la clasificación de la cuenta correctamente!");
                $this->trace("success", "Se creo la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
                return $this->response->redirect('accountclassification');
            }         
        }
        $this->view->setVar('account_form', $account_form);
    }
    
    public function editAction($idAccountclassification)
    {
        $account = Accountclassification::findFirst(array(
            "conditions" => "idAccountclassification = ?1",
            "bind" => array(1 => $idAccountclassification)
        ));
        
        if (!$account) {
            $this->notification->error('La clasificación de cuenta que desea editar no existe, por favor valide la información');
            return $this->response->redirect('account');
        }
                        
        $part = explode(" ", $account->expiryDate);
        $account->cant = $part[0];
        $account->date = $part[1];
        
        $form = new AccountclassificationForm($account);
        $this->view->setVar('form', $form);
        $this->view->setVar('account', $account);
        
        if($this->request->isPost()){
            
            $form->bind($this->request->getPost(), $account);
                        
            
            $cant = $this->request->getPost('cant');
            $date = $this->request->getPost('date');
            $senderAllowed = $this->request->getPost('senderAllowed');
            $footerEditable = $this->request->getPost('footerEditable');
            
            $account->updated = time();
            $account->expiryDate = "{$cant} {$date}";
            $account->senderAllowed = (empty($senderAllowed) ? 0 : 1);
            $account->footerEditable = (empty($footerEditable) ? 0 : 1);
            
            if(!$account->save()){
                foreach ($account->getMessages() as $message) {
                    $this->notification->error($message);
                }
                $this->trace("fail", "No se actualizo la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
            }
            else{
                $this->notification->info("La configuración de la Cuenta se actualizo correctamente");
                $this->trace("success", "Se edito la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
                return $this->response->redirect('accountclassification');
            }
            
        }
    }
    
    public function deleteAction($idAccountclassification)
    {
        $account = Accountclassification::findFirst(array(
            "conditions" => "idAccountclassification = ?1",
            "bind" => array(1 => $idAccountclassification)
        ));
        
        if($account->delete()){
            $this->notification->warning("Se ha eliminado la Clasificación de la Cuenta correctamente!");
            $this->trace("success", "Se elimino la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
            return $this->response->redirect('accountclassification/index');
        }
        else {
            $this->notification->error("Lo sentimos, ocurrio un error durante la elmiminación de la Clasificación de la Cuenta");
            $this->trace("fail", "No se elimino la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
            return $this->response->redirect('accountclassification/index');
        }
    }
    
    
}
