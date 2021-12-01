<?php

namespace Sigmamovil\General\Misc;


class ValidateNameUnique
{

    public function validatename($name)
    {
        if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->idAccount)) {
            $subaccount = \Phalcon\DI::getDefault()->get('Subaccount')->findFirst(array(
                'conditions' => 'idAccount = ?1 AND name = ?2',
                'bind' => array(1 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount, 2 => $name)
            ));

            if (empty($subaccount)) {
                return false;
            } else {
                return true;
            }
        }

        /*if (isset($this->user->Usertype->idMasteraccount)) {

        } */

        if (isset(\Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)) {
            $account = \Phalcon\DI::getDefault()->get('Account')->findFirst(array(
                'conditions' => 'idAllied = ?1 AND name = ?2',
                'bind' => array(1 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied, 2 => $name)
            ));

            if (empty($account)) {
                return false;
            } else {
                return true;
            }
        }
    }

}
