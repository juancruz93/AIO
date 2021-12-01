<?php

/*
 * Api landing page category
 */

/**
 * @RoutePrefix("/api/landingpagecategory")
 */
class ApilandingpagecategoryController extends ControllerBase {

    /**
     * @Post("/create")
     */
    public function createAction() {
        try {
            $modelLandingCategory = new \LandingPageCategory();
            $form = new LandingpagecategoryForm();
            $json = $this->request->getRawBody();
            $arrayData = json_decode($json, true);

            if (!$arrayData) {
                throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
            }
            $modelLandingCategory->idAccount = $this->user->Usertype->Subaccount->idAccount;
            $form->bind($arrayData, $modelLandingCategory);
            if (!$form->isValid()) {
                foreach ($form->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }
            if (!$modelLandingCategory->save()) {
                foreach ($modelLandingCategory->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }
            return $this->set_json_response(array('message' => "Se ha creado la {$modelLandingCategory->name} correctamente."));
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while create currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

    /**
     * @Post("/getall/{page:[0-9]+}")
     */
    public function getallcategoryAction($page) {
        try {
            $json = $this->request->getRawBody();
            $arrayData = json_decode($json, true);

            if (!$arrayData) {
                throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
            }
            $wrapper = new Sigmamovil\Wrapper\LandingpagecategoryWrapper();
            return $this->set_json_response($wrapper->getAll($page, $arrayData));
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

    /**
     * @Post("/edit/{idCategory:[0-9]+}")
     */
    public function editcategoryAction($idCategory) {
        try {
             $modelLandingCategory = \LandingPageCategory::findFirst(array("conditions" => "deleted = ?0  AND idAccount = ?1 and idLandingPageCategory =?2 ","bind" => array(0, $this->user->Usertype->Subaccount->Account->idAccount,$idCategory)));
            if(!$modelLandingCategory){
                throw new \InvalidArgumentException("Por favor validar la categoria que desea editar.");
            }
            $form = new LandingpagecategoryForm();
            $json = $this->request->getRawBody();
            $arrayData = json_decode($json, true);

            if (!$arrayData) {
                throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
            }
            $modelLandingCategory->idAccount = $this->user->Usertype->Subaccount->idAccount;
            $form->bind($arrayData, $modelLandingCategory);
            if (!$form->isValid()) {
                foreach ($form->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }
            if (!$modelLandingCategory->save()) {
                foreach ($modelLandingCategory->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }
            return $this->set_json_response(array('message' => "Se ha editado la categoria {$modelLandingCategory->name} correctamente."));
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

    /**
     * @Get("/getone/{id:[0-9]+}")
     */
    public function getonecategoryAction($id) {
        try {
            $wrapper = new Sigmamovil\Wrapper\LandingpagecategoryWrapper();
            return $this->set_json_response($wrapper->getOne($id));
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }
    
    /**
     * @Get("/delete/{id:[0-9]+}")
     */
    public function deletecategoryAction($idCategory) {
        try {
             $modelLandingCategory = \LandingPageCategory::findFirst(array("conditions" => "deleted = ?0 and idLandingPageCategory =?1 ","bind" => array(0,$idCategory)));
            if(!$modelLandingCategory){
                throw new \InvalidArgumentException("Por favor validar la categoria que desea eliminar.");
            }
            $modelLandingCategory->deleted = time();
            
            if (!$modelLandingCategory->save()) {
                foreach ($modelLandingCategory->getMessages() as $msg) {
                    throw new \InvalidArgumentException($msg);
                }
            }
              return $this->set_json_response(array('message' => "Se eliminó la categoria {$modelLandingCategory->name} correctamente."));
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while finding currency ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

}
