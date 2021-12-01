<?php

    
/**
 * @RoutePrefix("/api/subaccount")
 */
class ApisubaccountController extends ControllerBase {
    
   /**
   * @Get("/getavailableservicesamount")
   */
    
    /*
     * @author Felipe Garcia 
     * @return arrayObject Retorna un array de objetos con la informacion devuelta, ya sea un errror para caso negativo o la data en caso positivo la data
     */
    public function getavailableservicesamountAction() {
        try {
            $wrapper = new \Sigmamovil\Wrapper\SaxsWrapper();
                                                                //getAvailableServices() metodo ubicado en SaxsWrapper.php
                                                                //donde esta alojada la logica del requerimiento
            return $this->set_json_response(array('message'=>$wrapper->getAvailableServices()), 200);
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while [ApisubaccountController - getavailableservicesamountAction]... {$ex}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

}

/*$sql = "select saxserv.idSubaccount, saxserv.totalAmount, saxserv.amount, (saxserv.totalAmount - saxserv.amount) as availableQuantity, serv.name 
            from aio.saxs saxserv, aio.services serv
            where saxserv.idServices=serv.idServices 
            and saxserv.idSubaccount={idSubaccount}";*/