<?php

class PermissionSystemWrapper extends BaseWrapper
{
    public $data;
    public $content;
    public $role;
    public $resource;
    public $action;
    // --------------- ROLES ----------------- //

    public function setRole(Role $role)
    {
        $this->role = $role;
    }
	
    public function saveRole()
    {
        $rolename = trim($this->data->role->name);        
        $rname = strtolower($rolename);
        
        if (empty($rname)) {
            $this->setMessage('No ha enviado un nombre para el role, por favor valide la información');
            throw new InvalidArgumentException('Nombre de Role vacio');
        }
        elseif (strlen($rname) < 4 || strlen($rname) > 40) {
            $this->setMessage('El nombre del rol ingresado es menor a 4 o mayor a 40 caracteres');
            throw new InvalidArgumentException('Nombre de Role corto');
        }
        
        $name = str_replace(" ", "", $name);
        
        $role = new Role();
        $role->name = $rname;
        $role->created = time();
        $role->updated = time();
        
        $roles = Role::findFirst(array(
            'conditions' => 'name = ?1',
            'bind' => array(1 => $rname)
        ));
        
        if($rname == $roles->name){
            $this->setMessage('Ya existe un role con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Rola ya existe');
        }
        
        
        if (!$role->save()) {
            foreach ($role->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }
        
        $object = array();
        $object['id'] = $role->idRole;
        $object['name'] = $rname;
        $object['created'] = date('d/m/Y h:i a', $role->created);
        $object['updated'] = date('d/m/Y h:i a', $role->updated);
        
        $this->content = array('role' => $object);
    }
    
    public function findRoles()
    {
        $roles = Role::find();
        
        $objects = array();
        if (count($roles) > 0) {
            foreach ($roles as $role) {
                $objects[] = array(
                    'id' => $role->idRole,
                    'name' => $role->name,
                    'created' => date('d/m/Y h:i a', $role->created),
                    'updated' => date('d/m/Y h:i a', $role->updated),
                );
            }
        }
        
        $this->content = array('role' => $objects);
    }
	
    public function editRole()
    {
        $rolename = trim($this->data->role->name);
        $rename = strtolower($rolename);
        $rname = str_replace(" ", "", $rename);
        
        if (empty($rname)) {
            $this->setMessage('No ha enviado un nombre para el role, por favor valide la información');
            throw new InvalidArgumentException('Nombre de Role vacio');
        }
        elseif (strlen($rname) < 4 || strlen($rname) > 40) {
            $this->setMessage('El nombre del rol ingresado es menor a 4 o mayor a 40 caracteres');
            throw new InvalidArgumentException('Nombre de Role corto');
        }
        
        $role = Role::findFirst(array(
            'conditions' => 'name = ?1',
            'bind' => array(1 => $rname)
        ));
        
        if($rname == $role->name){
            $this->setMessage('Ya existe un role con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Recurso ya existe');
        }
        else {
            $this->role->name = $rname;
            $this->role->updated = time();

            if (!$this->role->save()) {
                foreach ($this->role->getMessages() as $msg) {
                    $this->setMessage($msg);
                    throw new InvalidArgumentException($msg);
                }
            }

            $object = array();
            $object['id'] = $this->role->idRole;
            $object['name'] = $rname;
            $object['created'] = date('d/m/Y h:i a', $this->role->created);
            $object['updated'] = date('d/m/Y h:i a', $this->role->updated);

            $this->content = array('role' => $object);
        }                
    }
	
    public function deleteRole()
    {
        if (!$this->role->delete()) {
            foreach ($this->role->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }
        
        $object = array();
        $object['id'] = $this->role->idRole;
        $object['name'] = $this->role->name;
        
        $this->content = array('role' => $object);
    }
    
    // --------------- RESOURCES ----------------- //
    
    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }
    
	
    public function saveResource()
    {
        $resourcename = trim($this->data->resource->name);
        $rename = strtolower($resourcename);
        $rname = str_replace(" ", "", $rename);
        
        if (empty($rname)) {
            $this->setMessage('No ha enviado un nombre para el recurso, por favor valide la información');
            throw new InvalidArgumentException('Nombre de Resource vacio');
        }
        
        elseif (strlen($rname) < 2 || strlen($rname) > 40) {
            $this->setMessage('El nombre del recurso ingresado es menor a 2 o mayor a 40 caracteres');
            throw new InvalidArgumentException('Nombre de Resource corto');
        }
        
        $resource = new Resource();
        $resource->name = $rname;
        $resource->created = time();
        $resource->updated = time();
        
        $resources = Resource::findFirst(array(
            'conditions' => 'name = ?1',
            'bind' => array(1 => $rname)
        ));
        
        if($rname == $resources->name){
            $this->setMessage('Ya existe un resource con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Rola ya existe');
        }
        
        if (!$resource->save()) {
            foreach ($resource->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }
        
        $object = array();
        $object['id'] = $resource->idResource;
        $object['name'] = $rname;
        $object['created'] = date('d/m/Y g:i a', $resource->created);
        $object['updated'] = date('d/m/Y g:i a', $resource->updated);
        
        $this->content = array('resource' => $object);
    }
    
    public function findResources()
    {
        $resources = Resource::find();
        
        $objects = array();
	$actions = array();
		
        if (count($resources) > 0) {
            foreach ($resources as $resource) {
                $idActions = array();
		$actionobjs = Action::findByIdResource($resource->idResource);
				
	if (count($actionobjs) > 0) {
		foreach ($actionobjs as $action) {
                    $actions[] = array(
                            'id' => $action->idAction,
                            'resource' => $action->idResource,
                            'name' => $action->name,
                            'created' => date('d/m/Y g:i a', $action->created),
                            'updated' => date('d/m/Y g:i a', $action->updated),
                    );

                    $idActions[] = $action->idAction;
                }
        }
				
                $objects[] = array(
                    'id' => $resource->idResource,
                    'name' => $resource->name,
                    'created' => date('d/m/Y g:i a', $resource->created),
                    'updated' => date('d/m/Y g:i a', $resource->updated),
                    'actions' => $idActions
                );
            }
        }
        
        $this->content = array('resources' => $objects, 'actions' => $actions);
    }
	
    public function editResource()
    {
        $resourcename = trim($this->data->resource->name);
        $rename = strtolower($resourcename);
        $rname = str_replace(" ", "", $rename);
        
        if (empty($rname)) {
            $this->setMessage('No ha enviado un nombre para el recurso, por favor valide la información');
            throw new InvalidArgumentException('Nombre de Resource vacio');
        }
        elseif (strlen($rname) < 4 || strlen($rname) > 40) {
            $this->setMessage('El nombre del rol ingresado es menor a 4 o mayor a 40 caracteres');
            throw new InvalidArgumentException('Nombre de Resource corto');
        }
        
        $resource = Resource::findFirst(array(
            'conditions' => 'name = ?1',
            'bind' => array(1 => $rname)
        ));
        
        if($rname == $resource->name){
            $this->setMessage('Ya existe un recurso con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Recurso ya existe');
        }
        else {
            $this->resource->updated = time();
            $this->resource->name = $rname;

            if (!$this->resource->save()) {
                foreach ($this->resource->getMessages() as $msg) {
                    $this->setMessage($msg);
                    throw new InvalidArgumentException($msg);
                }
            }

            $object = array();
            $object['id'] = $this->resource->idResource;
            $object['name'] = $rname;
            $object['created'] = date('d/m/Y g:i a', $this->resource->created);
            $object['updated'] = date('d/m/Y g:i a', $this->resource->updated);

            $this->content = array('resource' => $object);
        }                
    }
	
    public function deleteResource()
    {
        if (!$this->resource->delete()) {
            foreach ($this->resource->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }
        
        $object = array();
        $object['id'] = $this->resource->idResource;
        $object['name'] = $this->resource->name;
        
        $this->content = array('resource' => $object);
    }
    
     // --------------------- ACTIONS ------------------------- //
    
    public function setAction(Action $action)
    {
        $this->action = $action;
    }
    
    public function saveAction()
    {
        $actionname = trim($this->data->action->name);
        $rename = strtolower($actionname);
        $rname = str_replace(" ", "", $rename);
        
        if (empty($this->data->action->name)) {
            $this->setMessage('No ha enviado un nombre para la acción, por favor valide la información');
            throw new InvalidArgumentException('Action name is empty');
        }
        
        $objects = array();
        $actions = Action::findByIdResource($this->data->action->resource);
        foreach($actions as $action){
            $objects[] = $action->name;
        }
        if(in_array($rname, $objects)){
            $this->setMessage('Ya existe una acción con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Acción ya existe');
        }

        $action = new Action();
        $action->name = $rname;
        $action->idResource = $this->data->action->resource;
        $action->created = time();
        $action->updated = time();
        
        if (!$action->save()) {
            foreach ($action->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }

        $object = array();
        $object['id'] = $action->idAction;
        $object['name'] = $action->name;
        $object['resource'] = $action->idResource;

        $this->content = array('action' => $object);
    }

    public function findAction()
    {
        $actions = Action::find();

        $objects = array();
        if (count($actions) > 0) {
            foreach ($actions as $action) {
                $objects[] = array(
                    'id' => $action->idAction,
                    'resource' => $action->idResource,
                    'name' => $action->name,
                );
            }
        }

    $this->content = array('actions' => $objects);
    }

    public function editAction()
    {
        $actionname = trim($this->data->action->name);
        $rename = strtolower($actionname);
        $rname = str_replace(" ", "", $rename);
        
        if (empty($rname)) {
            $this->setMessage('No ha enviado un nombre para la acción, por favor valide la información');
            throw new InvalidArgumentException('Action name is empty');
        }
        
        $actions = Action::findByIdResource($this->action->idResource);
        foreach($actions as $action){
            $objects[] = $action->name;
        }
        if($rname == $this->action->name){
            
        }
        elseif(in_array($rname, $objects)){
            $this->setMessage('Ya existe una acción con este nombre, por favor valide la informacion');
            throw new InvalidArgumentException('Acción ya existe');
        }

        $this->action->name = $rname;
        $this->action->updated = time();

        if (!$this->action->save()) {
            foreach ($this->action->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }

        $object = array();
        $object['id'] = $this->action->idAction;
        $object['name'] = $this->action->name;
        $object['resource'] = $this->action->idResource;

        $this->content = array('actions' => $object);
    }
        
    public function deleteAction()
    {
        if (!$this->action->delete()) {
            foreach ($this->action->getMessages() as $msg) {
                $this->setMessage($msg);
                throw new InvalidArgumentException($msg);
            }
        }
        
        $object = array();
        $object['id'] = $this->action->idAction;
        $object['name'] = $this->action->name;
        $object['resource'] = $this->action->idResource;
        
        $this->content = array('action' => $object);
    }
    
    public function findPermissions()
    {
        $resources = Resource::find();
        $actions = Action::find();
        $allowed = Allowed::find(array(
            'conditions' => 'idRole = ?1',
            'bind' => array(1 => $this->role->idRole)
        ));
        
        $rs = array();
        if (count($resources) > 0) {
            foreach ($resources as $resource) {
                $as = array();
                
                foreach ($actions as $action) {
                    if ($action->idResource == $resource->idResource) {
                        foreach ($allowed as $all) {
                            if ($all->idAction == $action->idAction) {
                                $acl = 1;    
                                break;
                            }
                            else {
                                $acl = 0;    
                            }
                            
                        }
                        
                        $as[] = array(
                            'id' => $action->idAction,
                            'name' => $action->name,
                            'allowed' => $acl    
                        );
                    }
                }
                
		$rs[] = array(
                    'id' => $resource->idResource,
                    'name' => $resource->name,
                    'actions' => $as
                );
            }
        }
        
        $permissions = array(
            'id' => $this->role->idRole,
            'allowed' => json_encode($rs)
        );
//        $this->logger->log("Permissions");
//        $this->logger->log(print_r($rs, true));
        
        $this->content = array('permission' => $permissions);
    }
    
    public function addPermissions()
    {
        $permissions = json_decode($this->data->permission->allowed);
        
        $phql = "DELETE FROM Allowed WHERE idRole = :idRole:";
        $result = $this->modelsManager->executeQuery(
            $phql,
            array(
                'idRole' => $this->role->idRole
            )
        );
        
        if (!$result) {
            throw new Exception("Error while deleting allowed!");
        }
        
        $values = "";
        $first = true;
        foreach ($permissions as $permission) {
            foreach ($permission->actions as $action) {
                if ($action->allowed) {
                    if ($first) {
                        $values .= "({$action->id}, {$this->role->idRole}, " . time() . ", " . time() . ")";
                        $first = false;
                    }
                    else {
                        $values .= " ,({$action->id}, {$this->role->idRole}, " . time() . ", " . time() . ")";
                    }
                    
                }
            }
        }
        
        $insertSQL = "INSERT INTO allowed (idAction, idRole, created, updated) VALUES {$values} ";
        
        $this->logger->log($insertSQL);
        
        $result = $this->db->execute($insertSQL);
        
        if (!$result) {
            throw new Exception("Error while updating allowed!");
        } 
        
        $p = array(
            'id' => $this->role->idRole,
            'allowed' => json_encode($permissions)
        );
        
        $this->content = array('permission' => $p);
    }


    public function getData()
    {
        return $this->content;
    }
}

