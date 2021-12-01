<?php

class ApiKeyManager
{
  protected $user;

  public function setUser(User $user)
  {
    $this->user = $user;
  }

  protected function APIKeyGenerator()
  {
    $key = uniqid('', true);
    return $this->user->Usertype->Subaccount->idAccount . '-' . $this->user->idUser . '-' . $key;
  }

  protected function SecretGenerator()
  {
    return sha1($this->user->Usertype->Subaccount->Account->name . $this->user->name . time());
  }

  public function createAPIKey()
  {
    $key = new Apikey();
    $key->idUser = $this->user->idUser;
    $key->apikey = $this->APIKeyGenerator();
    $key->secret = $this->SecretGenerator();
    $key->status = 1;

    if (!$key->save()) {
      throw new InvalidArgumentException('No se pudo crear la API Key, por favor contacte al administrador');
    }

    return $key;
  }

  public function updateAPIKey()
  {

    $apikey = Apikey::findFirst(array(
        'conditions' => 'idApikey = ?0 ',
        'bind' => array(0 => $this->user->Apikey[0]->idApikey)
    ));

    $apikey->apikey = $this->APIKeyGenerator();
    $apikey->secret = $this->SecretGenerator();
    $apikey->status = 1;

    if (!$apikey->save()) {
      throw new InvalidArgumentException('No se pudo crear la API Key, por favor contacte al administrador');
    }

    return $apikey;
  }

  public function updateAPIKeyStatus($status)
  {
    $apikey = Apikey::findFirst(array(
        'conditions' => 'idApikey = ?0 ',
        'bind' => array(0 => $this->user->Apikey[0]->idApikey)
    ));
    $apikey->status = ($status == true) ? 1 : 0;

    if (!$apikey->save()) {
      throw new InvalidArgumentException('No se pudo crear la API Key, por favor contacte al administrador');
    }

    return $apikey;
  }

  public function deleteAPIKey()
  {
    $apikey = Apikey::findFirst(array(
        'conditions' => 'idApikey = ?0 ',
        'bind' => array(0 => $this->user->Apikey[0]->idApikey)
    ));

    if (!$apikey->delete()) {
      throw new InvalidArgumentException('No se pudo eliminar la API Key, por favor contacte al administrador');
    }
  }
}
