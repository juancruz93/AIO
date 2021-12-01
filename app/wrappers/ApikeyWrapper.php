<?php

namespace Sigmamovil\Wrapper;

class ApikeyWrapper extends \BaseWrapper
{

  public function findSubaccount($page)
  {

    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $builder = $this->modelsManager->createBuilder()
        ->columns(["Subaccount.name AS subaccountname", "User.name as username", "User.idUser as iduser", "User.lastname as userlastname",
            "User.email as useremail", "Role.name as rolename", "Apikey.idApikey as idapikey", "Apikey.apikey as apikey",
            "Apikey.secret as apiSecret", "Apikey.status as apiStatus",])
        ->from('Subaccount')
        ->join("Usertype", "Usertype.idSubaccount = Subaccount.idSubaccount")
        ->join("User", "User.idUsertype = Usertype.idUsertype")
        ->join("Role", "Role.idRole = User.idRole")
        ->leftJoin("Apikey", "Apikey.idUser = User.idUser")
        ->where("Subaccount.idAccount = " . \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount)
        ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, $page)
        ->getQuery()
        ->execute();

    $total = $this->modelsManager->createBuilder()
        ->columns(["Count(Subaccount.name) As count"])
        ->from('Subaccount')
        ->join("Usertype", "Usertype.idSubaccount = Subaccount.idSubaccount")
        ->join("User", "User.idUsertype = Usertype.idUsertype")
        ->join("Role", "Role.idRole = User.idRole")
        ->leftJoin("Apikey", "Apikey.idUser = User.idUser")
        ->where("Subaccount.idAccount = " . \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount)
        ->getQuery()
        ->execute();

    $consult = array();
    if (count($builder)) {
      foreach ($builder as $key => $value) {
        $consult[$key] = array(
            "subaccountname" => $value['subaccountname'],
            "username" => $value['username'],
            "iduser" => $value['iduser'],
            "userlastname" => $value['userlastname'],
            "useremail" => $value['useremail'],
            "rolename" => $value['rolename'],
            "idapikey" => $value['idapikey'],
            "apikey" => $value['apikey'],
            "apiSecret" => $value['apiSecret'],
            "apiStatus" => $value['apiStatus'],
        );
      }
    }

    $arrFinish = array("total" => $total[0]['count'], "total_pages" => ceil($total[0]['count'] / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "items" => $consult);
    return $arrFinish;
    //$this->mailcategory = array("total"=>count($total),"total_pages"=>ceil(count($total) / 2),"items"=>$consult);
  }
  
}