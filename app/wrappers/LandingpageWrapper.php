<?php

namespace Sigmamovil\Wrapper;

use MongoDB\Driver\Query;
use Sigmamovil\General\Links\ParametersEncoder as pe;
use Sigmamovil\General\Misc\AssetsManager;

class LandingpageWrapper extends \BaseWrapper {

  private $dirPublic;
  private $dirFooterDefault;
  private $space;
  private $idAccount;

  public function __construct() {
    $this->form = new \LandingpageForm();
    $this->dirPublic = getcwd();
    $this->dirFooterDefault = "$this->dirPublic/library/htmlbuilder/footerDefault.json";
    parent::__construct();
    $this->ipServer = \Phalcon\DI::getDefault()->get('ipServer');
  }

  function getSpace() {
    return $this->space;
  }

  function setSpace($space) {
    $this->space = $space;
  }

  function getIdAccount() {
    return $this->idAccount;
  }

  function setIdAccount($idAccount) {
    $this->idAccount = $idAccount;
  }

  /**
   * @author: Juan Cruz
   * @description: lista todas las landing page por usuario
   * @return: Array  numero de items, total de pages, total
   * @param: int $page numero de pagina, object $filter por nombre, por categoria y por fechas
   */
  public function listLanding($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $name = (isset($filter->name) ? " AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

    if (isset($filter->category) && count($filter->category) >= 1) {
      $arr = implode(",", $filter->category);
      $where .= "  AND idLandingPageCategory IN ({$arr})";
    }

    $filter->dateinitial = strtotime($filter->dateinitial);
    $filter->dateend = strtotime($filter->dateend);
    if (isset($filter->dateinitial) && isset($filter->dateend)) {
      if ($filter->dateinitial > $filter->dateend) {
        throw new \InvalidArgumentException("La fecha inicial no puede ser menor a la fecha final");
      } else {
        if ($filter->dateinitial != "" && $filter->dateend != "") {
          $where .= " AND created BETWEEN '{$filter->dateinitial}' AND '{$filter->dateend}'";
        }
      }
    }

    $conditions = array(
        "conditions" => "idSubaccount = {$this->user->Usertype->idSubaccount} AND deleted = ?0 {$name} $where",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $landing = \LandingPage::find($conditions);

    $total = count($landing);

    unset($conditions, $conditions["offset"], $conditions["order"]);

    $contador = 0;
    $data = array();
    if (count($landing) > 0) {
      foreach ($landing as $key => $value) {
        $contador = $contador + 1;

        $data[$key] = array(
            "idLandingPage" => $value->idLandingPage,
            "landingCategory" => $value->LandingPageCategory->name,
            "name" => $value->name,
            "description" => $value->description,
            "status" => $value->status,
            "startDate" => $value->startDate,
            "endDate" => $value->endDate,
            "updated" => date("Y-m-d", $value->updated),
            "created" => date("Y-m-d", $value->created),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  /**
   * @author: Juan Cruz
   * @description: lista las categorias en select
   * @return: array con todas las categorias creadas por el usuario
   * @param: ----
   */
  public function findlandingcategory() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));

    $conditions = array(
        "conditions" => "deleted = ?0 AND status = 1 AND idAccount = ?1",
        "bind" => array(0, $idAccount)
    );

    $landingcategory = \LandingPageCategory::find($conditions);
    $data = array();
    if (count($landingcategory) > 0) {
      foreach ($landingcategory as $key => $value) {
        $data[$key] = array(
            "idLandingPageCategory" => $value->idLandingPageCategory,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "description" => $value->description,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }
    return $data;
  }

  /**
   * @author: Juan Cruz
   * @description: crea categorias para las landing page
   * @return: array mensaje de confirmacion
   * @param: integer $data, nombre de la categoria a crear
   */
  public function createLandingCategory($data) {

    if (!$data['name']) {
      throw new \InvalidArgumentException("Por favor ingrese un nombre de categoria!");
    }
    $idAccount = $this->user->Usertype->Subaccount->Account->idAccount;

    $lc = \LandingPageCategory::findFirst(array(
                "columns" => "idLandingPageCategory",
                "conditions" => "deleted = ?0 AND status = ?1 AND name = ?2 AND idAccount = ?3",
                "bind" => array(0, 1, $data['name'], $idAccount)
    ));

    if ($lc) {
      throw new \InvalidArgumentException("El nombre de categoría que intenta crear ya existe en esta cuenta");
    }

    $landingcategory = new \LandingPageCategory();
    $landingcategory->name = $data['name'];
    $landingcategory->status = $data['status'];
    $landingcategory->idAccount = $idAccount;


    if (!$landingcategory->save()) {
      foreach ($landingcategory->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La categoría <b>{$landingcategory->name}</b> ha sido guardada exitosamente", "idLandingPageCategory" => $landingcategory->idLandingPageCategory];
  }

  /**
   * @author: Juan Cruz
   * @description: guarda la landing page 
   * @return: array datos que fueron guardados
   * @param: integer $data, todos los datos basicos para crear una landing page
   */
  public function saveLanding($data) {

    if (!isset($data['name'])) {
      throw new \InvalidArgumentException("El nombre de la Landing está vacío, por favor valide la información");
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("El correo <b><i>{$data['email']}</i></b> tiene un formato inválido o esta vacio");
    }

    if (!isset($data['idCountry'])) {
      throw new \InvalidArgumentException("El campo pais es obligatorio, por favor valide la información");
    }

    if (!isset($data['idState'])) {
      throw new \InvalidArgumentException("El campo estado/departamento/provincia es obligatorio, por favor valide la información");
    }

    if (!isset($data['idCity'])) {
      throw new \InvalidArgumentException("El campo ciudad es obligatorio, por favor valide la información");
    }

    if (!isset($data['website'])) {
      throw new \InvalidArgumentException("El nombre del sitio Web está vacío, por favor valide la información");
    }

    $data["website"] = str_replace(' ', '', $data["website"]);

    if (!isset($data['idCategoryLanding'])) {
      throw new \InvalidArgumentException("El campo categoría es obligatorio, por favor valide la información");
    }

    if (!isset($data['nameauthor'])) {
      $data['nameauthor'] = '';
    }

    if (!isset($data['address'])) {
      $data['address'] = '';
    }

    if (!isset($data['website'])) {
      $data['website'] = '';
    }

    if (!isset($data['nit'])) {
      $data['nit'] = '';
    }

    if (!isset($data['description'])) {
      $data['description'] = '';
    }

    $landingpage = new \LandingPage();
    $landingpage->idSubaccount = ((isset($this->user->Usertype->idSubaccount)) ? $this->user->Usertype->idSubaccount : Null);

    $landingpage->idLandingPageCategory = $data['idCategoryLanding'];
    $landingpage->name = $data['name'];
    $landingpage->description = $data['description'];

    $arr = array(
        "name" => $data['name'],
        "nameauthor" => $data['nameauthor'],
        "email" => $data['email'],
        "idCity" => (int) $data['idCity'],
        "address" => $data['address'],
        "website" => $data['website'],
        "nit" => $data['nit']
    );

    $dat = json_encode($arr);
    $landingpage->footerInfo = $dat;

    $this->form->bind($data, $landingpage);

    if (!$this->form->isValid() || !$landingpage->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($landingpage->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    //JC
    $path = "{$this->dirPublic}/assets/{$this->user->Usertype->Subaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/";
    if (!file_exists($path)) {
      if (!mkdir($path, 0777, true)) {
        throw new \InvalidArgumentException("No se ha podido crear el directorio de la Landing Page <b>{$landingpage->name}</b>");
      }
    }

    if (!copy($this->dirFooterDefault, "{$path}site{$landingpage->idLandingPage}.json")) {
      throw new \InvalidArgumentException("El archivo base para Landing Page no pudo se pudo copiar correctamente");
    }
    //endJC

    return $landingpage;
  }

  /**
   * @author: Juan Cruz
   * @description: busca una sola landing page para editar 
   * @return: array datos para ponerlos en la vista
   * @param: integer $idLandingpage, busca la id de la landing para modificarla
   */
  public function findLanding($idLandingpage) {
    $landing = \LandingPage::findFirst(array(
                'conditions' => "idLandingPage = ?0",
                'bind' => array($idLandingpage)
    ));

    $array = array();

    $array['idLandingPage'] = $landing->idLandingPage;
    $array['idCategoryLanding'] = $landing->idLandingPageCategory;
    $array['name'] = $landing->name;
    $array['description'] = $landing->description;
    $array['startDate'] = $landing->startDate;
    $array['endDate'] = $landing->endDate;
    $array['namecategory'] = $landing->LandingPageCategory->name;

    $dat = json_decode($landing->footerInfo);

    $CountryState = \City::findFirst(array(
                'conditions' => "idCity = ?0",
                'bind' => array($dat->idCity)
    ));

    $array['nameauthor'] = $dat->nameauthor;
    $array['email'] = $dat->email;
    $array['idCountry'] = $CountryState->idCountry;
    $array['idState'] = $CountryState->idState;
    $array['idCity'] = (string) $dat->idCity;
    $array['address'] = $dat->address;
    $array['website'] = $dat->website;
    $array['nit'] = $dat->nit;

    return $array;
  }

  /**
   * @author: Juan Cruz
   * @description: busca una sola landing page para editar 
   * @return: array datos para ponerlos en la vista
   * @param: integer $idLandingpage, busca la id de la landing para modificarla
   */
  public function findlandingCSC($idLandingpage) {
    $landing = \LandingPage::findFirst(array(
                'conditions' => "idLandingPage = ?0",
                'bind' => array($idLandingpage)
    ));

    $array = array();
    $dat = json_decode($landing->footerInfo);

    $CountryState = \City::findFirst(array(
                'conditions' => "idCity = ?0",
                'bind' => array($dat->idCity)
    ));

    $array['idCountry'] = $CountryState->idCountry;
    $array['idState'] = $CountryState->idState;
    $array['idCity'] = $dat->idCity;

    return $array;
  }

  /**
   * @author: Juan Cruz
   * @description: edita la landing page por id 
   * @return: array datos que fueron guardados.
   * @param: object $data, datos que se van a modificar; integer $idLanding, busca la id de la landing para modificarla
   */
  public function editLanding($data, $idLanding) {
    $landing = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = {$idLanding}"
    ));



    if (!isset($data['name'])) {
      throw new \InvalidArgumentException("El nombre de la Landing está vacío, por favor valide la información");
    }
//    if ($data["website"] != "") {
//      $website = 'https://';
//    }
//    
//    if (filter_var($data["website"], FILTER_VALIDATE_URL)) {
//      //pasa
//    } else {
//      $data["website"] = $website . $data["website"];
//    }
//
//    if ($data["website"] != "") {
//      if (!preg_match('/^(http|https|ftp):\/\/([\w]*)\.([\w]*)\.(com|net|org|biz|info|mobi|us|cc|bz|tv|ws|name|co|me)(\.[a-z]{1,3})?\z/i', $data["website"])) {
//        throw new \InvalidArgumentException("La direccion url que ingresa no es valida, por favor valide la información.");
//      }
//    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("El correo <b><i>{$data['email']}</i></b> tiene un formato inválido o esta vacio");
    }

    if (!$landing) {
      throw new \InvalidArgumentException("No se encontró la landing page, por favor valide la información o hay algun error");
    }

    if (!isset($data['idCountry'])) {
      throw new \InvalidArgumentException("El campo pais es obligatorio, por favor valide la información");
    }

    if (!isset($data['idState'])) {
      throw new \InvalidArgumentException("El campo estado/departamento/provincia es obligatorio, por favor valide la información");
    }

    if (!isset($data['idCity'])) {
      throw new \InvalidArgumentException("El campo ciudad es obligatorio, por favor valide la información");
    }

    if (!isset($data['website'])) {
      throw new \InvalidArgumentException("El nombre del sitio Web está vacío, por favor valide la información");
    }

    $data["website"] = str_replace(' ', '', $data["website"]);

    if (!isset($data['idCategoryLanding'])) {
      throw new \InvalidArgumentException("El campo categoría es obligatorio, por favor valide la información");
    }

    if (!isset($data['nameauthor'])) {
      $data['nameauthor'] = '';
    }

    if (!isset($data['address'])) {
      $data['address'] = '';
    }

    if (!isset($data['website'])) {
      $data['website'] = '';
    }

    if (!isset($data['nit'])) {
      $data['nit'] = '';
    }

    if (!isset($data['description'])) {
      $data['description'] = '';
    }

    $landing->idSubaccount = ((isset($this->user->Usertype->idSubaccount)) ? $this->user->Usertype->idSubaccount : Null);

    $landing->idLandingPageCategory = $data['idCategoryLanding'];
    $landing->name = $data['name'];
    $landing->description = $data['description'];

    $arr = array(
        "name" => $data['name'],
        "nameauthor" => $data['nameauthor'],
        "email" => $data['email'],
        "idCity" => (int) $data['idCity'],
        "address" => $data['address'],
        "website" => $data['website'],
        "nit" => $data['nit']
    );

    $dat = json_encode($arr);
    $landing->footerInfo = $dat;

    $this->form->bind($data, $landing);

    if (!$this->form->isValid() || !$landing->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($landing->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $landing;
  }

  /**
   * @author: Juan Cruz
   * @description: edita las visualizaciones de la lading page por fecha
   * @return: array datos que fueron guardados.
   * @param: object $data, datos que se van a modificar; integer $idLanding, busca la id de la landing para modificarla
   */
  public function editPublicView($data, $idLanding) {

    $landing = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = {$idLanding}"
    ));

    if (!$landing) {
      throw new \InvalidArgumentException("No se encontró la landing page, por favor valide la información o hay algun error");
    }

    if ($data['status'] == false) {
      if (!isset($data['countview'])) {
        throw new \InvalidArgumentException("Indique la cantidad de visualizaciones");
      }
      if ($data['countview'] > $data['totalview']) {
        throw new \InvalidArgumentException("La cantidad de visualizaciones ingresada no puede superar a las disponibles");
      }
    }
    if (!isset($data['startDate'])) {
      throw new \InvalidArgumentException("Seleccione una fecha de inicio");
    }
    if (!isset($data['endDate'])) {
      throw new \InvalidArgumentException("Seleccione una fecha de expiración");
    }

    if (strtotime($data['endDate']) <= strtotime($data['startDate'])) {
      throw new \InvalidArgumentException("La fecha y hora de expiración no puede ser anterior a la fecha inicial");
    }

    if ($data['status'] == true) {
      $landing->countview = 0;
    } else {
      $landing->countview = $data['countview'];
    }

    $landing->startDate = $data['startDate'];
    $landing->endDate = $data['endDate'];

    if (!$landing->save()) {
      foreach ($landing->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $landing;
  }

  /**
   * @author: Juan Cruz
   * @description: busca una landing page por visualizaciones y fechas programadas
   * @return: array devuelve datos de visualizaciones y fechas programadas.
   * @param: integer $idLanding, busca la id de la landing
   */
  public function findLandingCountView($idLandingpage) {

    $landing = \LandingPage::findFirst(array(
                'conditions' => "idLandingPage = ?0",
                'bind' => array($idLandingpage)
    ));

    $saxs = \Saxs::findFirst(array("conditions" => "idSubaccount = ?0 and idServices=8", "bind" => array($this->user->userType->idSubaccount)));

    $totalview = number_format($saxs->amount, 0, ",", ".");

    $array = array();
    $array['idLandingPage'] = $landing->idLandingPage;
    $array['startDate'] = $landing->startDate;
    $array['endDate'] = $landing->endDate;
    $array['countview'] = (int) $landing->countview;
    $array['totalview'] = (int) $totalview;

    return $array;
  }

  public function getContentOfLandingPage($idLandingPage) {
    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("No se ha podido obtener el argumento de Landing Page");
    }

    if ($idLandingPage == 0) {
      return [];
    }

    $landingpage = \LandingPage::findFirst(array(
                "columns" => "idLandingPage, footerInfo",
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    if (!$landingpage) {
      throw new \InvalidArgumentException("La Landing Page a la que intenta agregar o editar contenido, no existe");
    }

    $path = "{$this->dirPublic}/assets/{$this->user->Usertype->Subaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/site{$landingpage->idLandingPage}.json";
    if (!file_exists($path)) {
      throw new \InvalidArgumentException("No se ha encontrado el archivo base de la Landing Page");
    }

    $content = file_get_contents($path);

    if (!$content) {
      throw new \InvalidArgumentException("No se pudo leer correctamente el archivo base de la LandingPage");
    }

    $site = json_decode($this->replaceDataFooter($content, $landingpage->footerInfo));

    return (array) $site;
  }

  public function replaceDataFooter($content, $infoFooter) {
    $tags = ["%%NAME%%", "%%COUNTRY%%", "%%CITY%%", "%%ADDRESS%%", "%%NAMEAUTHOR%%", "%%EMAIL%%", "%%NIT%%"];
    $info = json_decode($infoFooter);
    $city = $this->getCity($info->idCity);
    $infoTags = [
        $info->name,
        $city->State->Country->name,
        $city->name,
        ((!empty($info->address)) ? ", $info->address" : ''),
        ((!empty($info->nameauthor)) ? $info->nameauthor : ''),
        $info->email,
        ((!empty($info->nit)) ? "NIT: {$info->nit}" : '')
    ];

    $finalString = str_replace($tags, $infoTags, $content);

    return $finalString;
  }

  public function getCity($idCity) {
    if (!isset($idCity)) {
      return "";
    }

    $city = \City::findFirst(array(
                "conditions" => "idCity = ?0",
                "bind" => array($idCity)
    ));

    return $city;
  }

  public function saveContentLandingPage($idLandingPage, $data) {
    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("No se ha podido obtener el argumento de Landing Page");
    }

    $landingpage = \LandingPage::findFirst(array(
                "columns" => "idLandingPage",
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    if (!$landingpage) {
      throw new \InvalidArgumentException("La Landing Page a la que intenta guardar un contenido, no existe");
    }

    $objSubaccount = $this->user->Usertype->Subaccount;

    $json = json_encode($data['data']);
    $filePath = "{$this->dirPublic}/assets/{$objSubaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/site{$landingpage->idLandingPage}.json";
    if (!file_exists($filePath)) {
      throw new \InvalidArgumentException("El archivo del contenido de la Landing Page no existe");
    }

    if (file_put_contents($filePath, $json) === FALSE) {
      throw new \InvalidArgumentException("No se pudo escribir el contenido de la Landing Page en el archivo base");
    }

    $this->createThumbnailLandingPage($landingpage->idLandingPage);

    return ["responseCode" => 1, "urlRedirect" => "landingpage#/create/confirmation/{$landingpage->idLandingPage}", "message" => "El contenido se ha guardado correctamente"];
  }

  public function uploadFileLandingPage($file) {
    $this->validateSpaceInAccount();
    $this->validateFile($file);
    $assetsManager = new AssetsManager();
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0",
                "bind" => array($this->getIdAccount())
    ));
    $assetsManager->setAccount($account);
    $assetsManager->setFile($file);
    $dirImage = $assetsManager->uploadImage();

    return ["message" => "La imagen se cargó correctamenta", "dirImage" => "{$this->asset->assets}{$this->getIdAccount()}/images/{$dirImage}", "code" => 1];
  }

  private function validateSpaceInAccount() {
    $account = \Account::findFirst(array(
                "conditions" => "idAccount = ?0",
                "bind" => array($this->getIdAccount())
    ));

    if ($this->getSpace() >= $account->AccountConfig->diskSpace) {
      throw new InvalidArgumentException("Ha sobrepasaso el limite de espacio en disco. para liberar espacio en disco elimine imágenes o archivos que considere innecesarios");
    }
  }

  private function validateFile($file) {
    if (empty($file['name'])) {
      throw new InvalidArgumentException("No ha enviado ningún archivo o ha enviado un tipo de archivo no soportado, por favor verifique la información");
    }

    if ($file['error'] == 1) {
      throw new InvalidArgumentException("Ha ocurrido un error mientras se cargaba el archivo, por favor valide la información");
    }
  }

  /**
   * @author: Juan Cruz
   * @description: elimina la lading page
   * @return: array datos que fueron eliminados.
   * @param: $idLanding, busca la id de la landing para su respectiva eliminacion
   */
  public function deletelandingpage($idLanding) {
    $landing = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = {$idLanding}"
    ));

    if (!$landing) {
      throw new \InvalidArgumentException("No se encontró la landing page, por favor valide la información o hay algun error");
    }

    $landing->deleted = time();

    if (!$landing->save()) {
      foreach ($landing->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $landing;
  }

  public function linkGenerator($idLandingPage) {
    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("Dato de landingPage inválido");
    }

    $landingPage = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    if (!$landingPage) {
      throw new \InvalidArgumentException("La encuesta a la que intenta generar link no existe");
    }

    $landingPage->status = 3;
    $landingPage->save();

    $footerInfo = json_decode($landingPage->footerInfo);

    $titleLP = strtolower(str_replace(" ", "", $footerInfo->website));

    $url = "{$this->urlManager->get_base_uri(true)}lp/{$titleLP}/{$landingPage->idLandingPage}";

    return ["link" => $url, "message" => "La LandingPage ha cambiado el estado a <b>Publicada</b>"];
  }

  /**
   * @author: Juan Cruz
   * @description: guarda envio de correo
   * @return: message de confirmacion 
   * @param: object $data informacion del correo, trae toda la informacion para enviar el correo 
   */
  public function sendMail($data) {

    if (isset($data->mailtemplate->idMailTemplate)) {
      $mailTemplate = \MailTemplate::findFirst(array("conditions" => "idMailTemplate = ?0 and deleted = 0", "bind" => array($data->mailtemplate->idMailTemplate)));

      if (!$mailTemplate) {
        throw new \InvalidArgumentException("La plantilla seleccionada no se encuentra registrada o puede estar eliminada, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar la plantilla de correo,por favor validar.");
    }

    if (isset($data->mailcategory->idMailCategory)) {
      $mailCategory = \MailCategory::findFirst(array("conditions" => "idMailCategory = ?0 and deleted = 0", "bind" => array($data->mailcategory->idMailCategory)));

      if (!$mailCategory) {
        throw new \InvalidArgumentException("La categoria seleccionada no se encuentra registrada o puede estar eliminada, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar la categoria de correo,por favor validar.");
    }

    if (isset($data->senderName->idNameSender)) {
      $nameSender = \NameSender::findFirst(array("conditions" => "idNameSender = ?0 and status = 1", "bind" => array($data->senderName->idNameSender)));

      if (!$nameSender) {
        throw new \InvalidArgumentException("El nombre del remitente seleccionado no se encuentra registrado o puede estar en estado inactivo, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar el nombre del remitente,por favor validar.");
    }

    if (isset($data->senderEmail->idEmailsender)) {
      $emailSender = \Emailsender::findFirst(array("conditions" => "idEmailsender = ?0 and status = 1", "bind" => array($data->senderEmail->idEmailsender)));

      if (!$emailSender) {
        throw new \InvalidArgumentException("El correo del remitente seleccionado no se encuentra registrado o puede estar en estado inactivo, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar el correo del remitente,por favor validar.");
    }

    if (!isset($data->subject)) {
      throw new \InvalidArgumentException("Diligenciar el asunto del correo,por favor validar.");
    }
    $target = new \stdClass();
    if (!isset($data->listDestinatary)) {
      throw new \InvalidArgumentException("Diligenciar la lista de destinatario del correo,por favor validar.");
    } else {
      $target->type = ($data->listDestinatary->id == 1) ? "contactlist" : "segment";
    }

    if (!isset($data->destinatary)) {
      throw new \InvalidArgumentException("Diligenciar los destinatarios correo,por favor validar.");
    } else {
      if ($target->type == "contactlist") {
        $target->contactlists = $data->destinatary;
      } else {
        $target->segment = $data->destinatary;
      }
    }

    $mailTemplateContent = \MailTemplateContent::findFirst(array("conditions" => "idMailTemplate = ?0", "bind" => array($mailTemplate->idMailTemplate)));

    if (!$mailTemplateContent) {
      throw new \InvalidArgumentException("No se encontro el contenido de la plantilla, contacte al administrador.");
    }

    $this->db->begin();

    $sendMail = new \Mail();

    $sendMail->idSubaccount = $this->user->Usertype->idSubaccount;
    $sendMail->idEmailsender = $emailSender->idEmailsender;
    $sendMail->idLandingPage = $data->landing->idLandingPage;
    $sendMail->name = $data->landing->name;
    $sendMail->replyto = (isset($data->replyTo)) ? $data->replyTo : null;
    $sendMail->subject = $data->subject;
    $sendMail->scheduleDate = (isset($data->scheduleDate)) ? $data->scheduleDate : date("Y-m-d H:i", time());
    $sendMail->confirmationDate = date("Y-m-d H:i", time());
    $sendMail->gmt = "-0500";
    $sendMail->target = json_encode($target);
    $sendMail->attachment = 0;
    $sendMail->idNameSender = $nameSender->idNameSender;
    $sendMail->type = "landingpage";
    $sendMail->status = "scheduled";

    if (!$sendMail->save()) {
      $this->db->rollback();
      foreach ($sendMail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $sendMailContent = new \MailContent();

    $sendMailContent->idMail = $sendMail->idMail;
    $sendMailContent->typecontent = "Editor";
    $sendMailContent->content = $mailTemplateContent->content;

    if (!$sendMailContent->save()) {
      $this->db->rollback();
      foreach ($sendMailContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $changeStatus = new \stdClass();
    $changeStatus->status = "published";

    if (!$this->changeStatus($changeStatus, $data->landing->idLandingPage)) {
      $this->db->rollback();
      throw new \InvalidArgumentException($msg);
    }

    $this->db->commit();
    return array("message" => "El envio de email se programo correctamente.");
  }

  /**
   * @author: Juan Cruz
   * @description: cambia el status de la landing
   * @return: booleano 
   * @param: object $data informacion con el status, int $idLandingPage landing page a cambiar de estado 
   */
  public function changeStatus($data, $idLandingPage) {
    $landing = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));
    if (!$landing) {
      throw new \InvalidArgumentException("No se encontro la landing page seleccionada, contacte al administrador.");
    }

    $landing->status = $data->status;
    if (!$landing->save()) {
      return false;
    }
    return true;
  }

  public function hasContentLandingPage($idLandingPage) {
    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("No envió el argumento correcto para la LandingPage");
    }

    $landingpage = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    if (!$landingpage) {
      throw new \InvalidArgumentException("La LandingPage a la que busca un contenido no existe");
    }

    $filePath = "{$this->dirPublic}/assets/{$landingpage->Subaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/site{$landingpage->idLandingPage}.json";

    if (!file_exists($filePath)) {
      throw new \InvalidArgumentException("El archivo base de la LandingPage no existe");
    }

    $json = file_get_contents($filePath);
    $content = json_decode($json);

    if (count($content->pages->index->blocks) > 1) {
      $path = "{$this->asset->assets}{$landingpage->Subaccount->Account->idAccount}/landing-pages/{$landingpage->idLandingPage}/{$landingpage->idLandingPage}_thumbnail.png";
      $thumbnail = "{$this->dirPublic}{$path}";
      $thumb = "{$this->urlManager->get_base_uri(true)}{$path}";
      if (!file_exists($thumbnail)) {
        $thumb = "{$this->urlManager->get_base_uri(true)}images/circle/plantillas.jpg";
      }
      return [
          "hasContent" => true,
          "thumbnail" => $thumb
      ];
    }

    return ["hasContent" => false];
  }

  private function createThumbnailLandingPage($idLandingPage) {
    $idAccount = ((isset($this->user->Usertype->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : NULL));

    $dirAcc = "{$this->dirPublic}{$this->asset->assets}{$idAccount}/landing-pages/";

    if (isset($idAccount)) {
      $dir = "{$dirAcc}{$idLandingPage}/";
    }

    if (!file_exists($dir)) {
      mkdir($dirAcc, 0777, true);
    }

    $dirthumb = "{$dir}{$idLandingPage}_thumbnail.png";
    $domain = $this->urlManager->get_base_uri(true);

    exec("wkhtmltoimage --quality 25 --zoom 0.2 --width 180 --height 180 {$domain}landingpage/preview/{$idLandingPage} $dirthumb");
  }

  public function duplicateLandingPage($idLandingPage) {
    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("Argumento de LandingPage inválido");
    }

    $originLandingPage = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    if (!$originLandingPage) {
      throw new \InvalidArgumentException("La LandingPage que intenta duplicar no existe");
    }

    $newLandingPage = new \LandingPage();
    $newLandingPage->idSubaccount = $originLandingPage->idSubaccount;
    $newLandingPage->idLandingPageCategory = $originLandingPage->idLandingPageCategory;
    $newLandingPage->countview = $originLandingPage->countView;
    $newLandingPage->status = 1;
    $newLandingPage->name = "{$originLandingPage->name} (copia)";
    $newLandingPage->description = $originLandingPage->description;
    $newLandingPage->footerInfo = $originLandingPage->footerInfo;

    if (!$newLandingPage->save()) {
      foreach ($newLandingPage->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $originDir = "{$this->dirPublic}{$this->asset->assets}{$originLandingPage->Subaccount->idAccount}/landing-pages/"
            . "{$originLandingPage->idLandingPage}/site{$originLandingPage->idLandingPage}.json";

    if (!file_exists($originDir)) {
      throw new \InvalidArgumentException("El archivo base de la Landign Page que desea duplicar no existe");
    }

    $newDir = "{$this->dirPublic}{$this->asset->assets}{$newLandingPage->Subaccount->idAccount}/landing-pages/{$newLandingPage->idLandingPage}/";

    if (!mkdir($newDir)) {
      throw new InvalidArgumentException("no se pudo crear el directorio de la nueva Landing Page");
    }

    $newDir .= "site{$newLandingPage->idLandingPage}.json";

    if (!copy($originDir, $newDir)) {
      throw new \InvalidArgumentException("El archivo base para Landing Page no pudo se pudo copiar correctamente");
    }

    if (!file_exists($newDir)) {
      throw new \InvalidArgumentException("El archivo base de la Landing Page duplicada no existe");
    }

    return ["message" => "La Landing Page fue duplicada exitosamente"];
  }

  /**
   * @author: Juan Cruz
   * @description: cambia el type de la landing
   * @return: booleano 
   * @param: object $data informacion con el status, int $idLandingPage landing page a cambiar de estado 
   */
  public function changeType($data, $idLanding) {
    $landing = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLanding)
    ));
    if (!$landing) {
      throw new \InvalidArgumentException("No se encontro la landing seleccionada, contacte al administrador.");
    }

    $landing->type = $data->type;
    if (!$landing->save()) {
      return false;
    }
    return true;
  }

  public function linkfb($idLandingPage) {

    $landingPage = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));


    $footerInfo = json_decode($landingPage->footerInfo);

    $titleLP = strtolower(str_replace(" ", "", $footerInfo->website));

    //$url = "{$this->urlManager->get_base_uri(true)}lp/{$titleLP}/{$landingPage->idLandingPage}";
    $url = "{$this->ipServer->ip}/lp/{$titleLP}/{$landingPage->idLandingPage}";

    return ["link" => $url];
  }

}
