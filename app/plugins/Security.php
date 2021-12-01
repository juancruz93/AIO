<?php

use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl;

/**
 * Security
 *
 * Este es la clase que proporciona los permisos a los usuarios. Esta clase decide si un usuario pueder hacer determinada
 * tarea basandose en el tipo de ROLE que posea
 */
class Security extends Plugin {

  protected $serverStatus;
  protected $allowed_ips;
  protected $ip;

  public function __construct($dependencyInjector, $serverStatus = 0, $allowed_ips = null, $ip = null) {
    $this->_dependencyInjector = $dependencyInjector;
    $this->serverStatus = $serverStatus;
    $this->allowed_ips = $allowed_ips;
    $this->ip = $ip;
  }

  public function getAcl() {
    /*
     * Buscar ACL en cache
     */
    $acl = $this->cache->get('acl-cache-aio');

    if (!$acl) {
      // No existe, crear objeto ACL
      $acl = $this->acl;
      $roles = Role::find();

      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');

      $sql = "SELECT Resource.name AS resource, Action.name AS action 
                            FROM Action
                                    JOIN Resource ON (Action.idResource = Resource.idResource)";

      $results = $modelManager->executeQuery($sql);

      $allowed = $modelManager->executeQuery('SELECT Role.name AS rolename, Resource.name AS resname, Action.name AS actname
                                                                                                 FROM Allowed
                                                                                                        JOIN Role ON ( Role.idRole = Allowed.idRole ) 
                                                                                                        JOIN Action ON ( Action.idAction = Allowed.idAction ) 
                                                                                                        JOIN Resource ON ( Action.idResource = Resource.idResource ) ');

      //Registrando roles
      foreach ($roles as $role) {
        $acl->addRole(new Phalcon\Acl\Role($role->name));
      }

      //Registrando recursos
      $resources = array();

      foreach ($results as $key) {
        if (!isset($resources[$key['resource']])) {
          $resources[$key['resource']] = array($key['action']);
        } else {
          $resources[$key['resource']][] = $key['action'];
        }
      }

      foreach ($resources as $resource => $actions) {
        $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
      }

      //Relacionando roles y recursos desde la base de datos
      foreach ($allowed as $role) {
        $acl->allow($role->rolename, $role->resname, $role->actname);
      }

      $this->cache->save('acl-cache-aio', $acl);
    }

    // Retornar ACL
    $this->_dependencyInjector->set('acl', $acl);

    return $acl;
  }

  protected function getControllerMap() {
    $map = $this->cache->get('controllermap-cache');

    if (!$map) {
      $map = array(
          'asset::thumbnailmail' => array(),
          /* Public resources */
          'apiforms::getcontentform' => array(),
          /* Error views */
          'error::index' => array(),
          'error::notavailable' => array(),
          'error::unauthorized' => array(),
          'error::forbidden' => array(),
          'error::link' => array(),
          'country::country' => array(),
          'country::state' => array(),
          'country::cities' => array(),
          /* Session */
          'session::index' => array(),
          'session::login' => array(),
          'session::loginpass' => array(),
          'session::logout' => array(),
          'session::recoverpass' => array(),
          'session::resetpassword' => array(),
          'session::setnewpass' => array(),
          'session::changemodeadvanced' => array(),
          'session::changemodebasic' => array(),
          'apisession::login' => array(),
          'apisession::loginpass' => array(),
          'apisession::loginwithfacebook' => array(),
          'apisession::verifystatususer' => array(),
          'apisession::recoverpassgenerate' => array(),
          'session::logoutsuperuser' => array(),
          /* Comentarios de los usuarios */
          'suggestion::index' => array(),
          /* Rutas para todo el sistema */
          'masteraccount::getallmta' => array(),
          'masteraccount::adapter' => array(),
          'masteraccount::urldomain' => array(),
          'masteraccount::mailclass' => array(),
          /* Track */
          'track::click' => array(),
          /* unsuscribe */
          'unsubscribe::contact' => array(),
          'unsubscribe::list' => array(),
          'unsubscribe::new' => array(),
          'unsubscribe::create'=> array(),
          'unsubscribe::contactautomatic' => array(),
          /* Webversion */
          'webversion::show' => array(),
          /* Register */
          'register::index' => array(),
          'register::signup' => array(),
          'register::paymentplan' => array(),
          'register::paymentplandetail' => array(),
          'register::payment' => array(),
          'register::pay' => array(),
          'register::validatemail' => array(),
          'register::congratulations' => array(),
          'register::welcome' => array(),
          'register::completeprofileuser' => array(),
          /* apiregister */
          'apiregister::create' => array(),
          'apiregister::listpaymentplans' => array(),
          'apiregister::detailpaymentplan' => array(),
          'apiregister::verifyaccount' => array(),
          'apiregister::assignpaymentplan' => array(),
          'register::getappidfacebook' => array(),
          'register::createwithfacebook' => array(),
          /* Private resources */
          /* Dashboard */
          'index::index' => array(),
          /* Account */
          'account::index' => array(),
          'account::show' => array(),
          'account::search' => array(),
          'account::create' => array(),
          'account::planbycountryaccount' => array(),
          'account::getfooters' => array(),
          'account::getservicesaccount' => array(),
          'account::rechargeaccount' => array(),
          'account::createconfig' => array(),
          'account::edit' => array(),
          'account::configedit' => array(),
          'account::delete' => array(),
          'account::userlist' => array(),
          'account::usercreate' => array(),
          'account::useredit' => array(),
          'account::userdelete' => array(),
          'account::passedit' => array(),
          'account::accountconfigedit' => array(),
          'account::downloadexcelaccounts' => array(),
          'account::downloadexcel' => array(),
          /* Subaccount */
          'subaccount::activateservice' =>array(),
          'subaccount::desactivateservice' => array(),
          'subaccount::index' => array(),
          'subaccount::userlist' => array(),
          'subaccount::edituser' => array(),
          'subaccount::passedit' => array(),
          'subaccount::createuser' => array(),
          'subaccount::deleteuser' => array(),
          'subaccount::edit' => array(),
          'subaccount::configedit' => array(),
          'subaccount::create' => array(),
          'subaccount::show' => array(),
          'subaccount::showconfig' => array(),
          'subaccount::deletesubaccount' => array(),
          'account::subaccountedit' => array(),
          'account::subaccountdelete' => array(),
          /* Allied */
          'allied::deleteuser' => array(),
          'allied::createuser' => array(),
          'allied::edituser' => array(),
          'allied::configedit' => array(),
          'allied::passedituser' => array(),
          'allied::createconfig' => array(),
          'allied::listuser' => array(),
          'allied::show' => array(),
          /* Masteraccount */
          'masteraccount::aliaslistuser' => array(),
          'masteraccount::listmxssr' => array(),
          'masteraccount::getmasteraccount' => array(),
          'masteraccount::createuser' => array(),
          'masteraccount::index' => array(),
          'masteraccount::create' => array(),
          'masteraccount::planbycountry' => array(),
          'masteraccount::edit' => array(),
          'masteraccount::delete' => array(),
          'masteraccount::aliaslist' => array(),
          'masteraccount::getservicesallied' => array(),
          'masteraccount::rechargeallied' => array(),
          'masteraccount::show' => array(),
          'masteraccount::createconfig' => array(),
          'masteraccount::aliascreate' => array(),
          'masteraccount::planbycountryallied' => array(),
          'masteraccount::aliasedit' => array(),
          'masteraccount::aliasconfigedit' => array(),
          'masteraccount::myconfigedit' => array(),
          'masteraccount::aliasdelete' => array(),
          /* Account Classification */
          'accountclassification::index' => array(),
          'accountclassification::create' => array(),
          'accountclassification::edit' => array(),
          'accountclassification::delete' => array(),
          /* Adapter */
          'adapter::index' => array(),
          'adapter::create' => array(),
          'adapter::edit' => array(),
          'adapter::delete' => array(),
          'adapter::listfulladapter' => array(),
          /* Api */
          'apisecurity::indexrole' => array(),
          'apisecurity::newrole' => array(),
          'apisecurity::editrole' => array(),
          'apisecurity::deleterole' => array(),
          'apisecurity::indexresource' => array(),
          'apisecurity::newresource' => array(),
          'apisecurity::editresource' => array(),
          'apisecurity::deleteresource' => array(),
          'apisecurity::indexaction' => array(),
          'apisecurity::newaction' => array(),
          'apisecurity::editaction' => array(),
          'apisecurity::deleteaction' => array(),
          'apisecurity::indexpermissions' => array(),
          'apisecurity::addpermissions' => array(),
          /* Mail Class */
          'mailclass::index' => array(),
          'mailclass::create' => array(),
          'mailclass::edit' => array(),
          'mailclass::delete' => array(),
          'mailclass::listfullmailclass' => array(),
          /* Marketing */
          'marketing::index' => array(),
          /* Mta */
          'mta::index' => array(),
          'mta::create' => array(),
          'mta::edit' => array(),
          'mta::delete' => array(),
          'mta::listfullmta' => array(),
          'mta::getallmta' => array(),
          'mta::getidmtadcxmta' => array(),
          /* Permissions System */
          'permissionsystem::index' => array(),
          /* Plantillas */
          'plantillas::index' => array(),
          'plantillas::default' => array(),
          /* Platform */
          'services::index' => array(),
          'services::create' => array(),
          'services::edit' => array(),
          'services::delete' => array(),
          'services::listapi' => array(),
          /* System */
          'system::index' => array(),
          'system::configure' => array(),
          /* Tools */
          'tools::index' => array(),
          'accounts::index' => array(),
          /* Url Domain */
          'urldomain::index' => array(),
          'urldomain::create' => array(),
          'urldomain::edit' => array(),
          'urldomain::delete' => array(),
          'urldomain::listfullurldomain' => array(),
          /* User */
          'user::index' => array(),
          'user::search' => array(),
          'user::create' => array(),
          'user::edit' => array(),
          'user::delete' => array(),
          'user::passedit' => array(),
          'session::superuser' => array(),
          'session::logoutsuperuser' => array(),
          /* System mail */
          'systemmail::index' => array(),
          'systemmail::create' => array(),
          'systemmail::delete' => array(),
          'systemmail::edit' => array(),
          /* Mail */
          'mail::editor_frame' => array(),
          'mailpreview::preview' => array(),
          'mailpreview::createpreview' => array(),
          'mailpreview::previewdata' => array(),
          /* Mail Send */
          'mail::index' => array(),
          'mail::create' => array(),
          'mail::basicinformation' => array(),
          'mail::addressees' => array(),
          'mail::content' => array(),
          'mail::loadpdf' => array(),
          'mail::advanceoptions' => array(),
          'mail::shippingdate' => array(),
          'mail::timezone' => array(),
          'mail::emailsender' => array(),
          'mail::emailname' => array(),
          'mail::htmlcontent' => array(),
          'mail::addemailname' => array(),
          'mail::addemailsender' => array(),
          'mail::editbasicinformation' => array(),
          'mail::contenteditor' => array(),
          'mail::urleditor' => array(),
          'mail::previewhtml' => array(),
          'mail::previewdata' => array(),
          'mail::previewmail' => array(),
          'mail::uploadfiles' => array(),
          'mail::previeweditor' => array(),
          'mail::preview' => array(),
          'mail::mailstructureeditor' => array(),
          'mail::clonemail' => array(),
          /* Flash Messages */
          'flashmessage::index' => array(),
          'flashmessage::create' => array(),
          'flashmessage::edit' => array(),
          'flashmessage::delete' => array(),
          /* Data Base */
          'dbase::index' => array(),
          'dbase::create' => array(),
          'dbase::edit' => array(),
          'dbase::delete' => array(),
          /* Sending Category */
          'sendingcategory::index' => array(),
          'sendingcategory::create' => array(),
          'sendingcategory::edit' => array(),
          'sendingcategory::delete' => array(),
          /* Gallery */
          'gallery::index' => array(),
          'gallery::upload' => array(),
          'gallery::uploadimage' => array(),
          'gallery::uploadfile' => array(),
          'gallery::uploadfileadjunt' => array(),
          'gallery::uploadfileadjuntca' => array(),
          'gallery::delete' => array(),
          'gallery::show' => array(),
          'gallery::thumbnail' => array(),
          /* Contact lists */
          'contactlist::show' => array(),
          'contactlist::list' => array(),
          'contactlist::add' => array(),
          'contactlist::edit' => array(),
          'contactlist::addcustomfield' => array(),
          'contactlist::customfield' => array(),
          'contactlist::delete' => array(),
          'contactlist::editcustomfield' => array(),
          'contactlist::deletecustomfield' => array(),
          'apicontactlist::getcontactlists' => array(),
          'apicontactlist::gettotalcontactlist' => array(),
          'apicontactlist::getcontactlist' => array(),
          'apicontactlist::add' => array(),
          'apicontactlist::edit' => array(),
          'apicontactlist::delete' => array(),
          'apicontactlist::addcustomfield' => array(),
          'apicontactlist::customfield' => array(),
          'apicontactlist::editcustomfield' => array(),
          'apicontactlist::getonecustomfield' => array(),
          'apicontactlist::editCustomfield' => array(),
          'apicontactlist::deletecustomfield' => array(),
          'apicontactlist::getcontactlistinfo' => array(),
          'apicontactlist::getcontactlistbysubaccount' => array(),
          'apicontactlist::exportcontacts' => array(),
          'apicontactlist::getcontactlistcategory' => array(),
          'apicontactlist::savecategory' => array(),
          /* Contact */
          'contact::index' => array(),
          'contact::list' => array(),
          'contact::import' => array(),
          'contact::newbatch' => array(),
          'contact::importcontacts' => array(),
          'contact::newcontact' => array(),
          'contact::processfile' => array(),
          'contact::history' => array(),
          'contact::validatetotalcontacts' => array(),
          'contact::export' => array(),
          'contact::downloadlc' => array(),
          /* ApiContact */
          'apicontact::contactscsv' => array(),
          'apicontact::getcontacts' => array(),
          'apicontact::getcontactsaccount' => array(),
          'apicontact::activecontacts' => array(),
          'apicontact::changestatus' => array(),
          'apicontact::addcontactbatch' => array(),
          'apicontact::validatecontactbatch' => array(),
          'apicontact::newcontact' => array(),
          'apicontact::customfield' => array(),
          'apicontact::customfieldselect' => array(),
          'apicontact::addcontact' => array(),
          'apicontact::addcontactbyform' => array(),
          'apicontact::addcontactbyblock' => array(),
          'apicontact::updatecontact' => array(),
          'apicontact::findcontact' => array(),
          'apicontact::editcontact' => array(),
          'apicontact::getallindicative' => array(),
          'apicontact::deletecontact' => array(),
          'apicontact::deleteselected' => array(),
          'apicontact::addcontactform' => array(),
          'apicontact::getcontactsform' => array(),
          'apicontact::getcontactlisttomoveselected' => array(),
          'apicontact::movecontactselected' => array(),
          'apicontact::validatecopycontactselected' => array(),
          'apicontact::copycontactselected' => array(),
          'apicontact::changesuscribeselected' => array(),
          'apicontact::getonecontact' => array(),
          'apicontact::getallsms' => array(),
          'apicontact::getallmail' => array(),
          'apicontact::preview' => array(),
          'apicontact::getdataform' => array(),
          'apicontact::exportmorecontacts' => array(),
          /* Procesos de importacion */
          'process::index' => array(),
          'process::startservernode' => array(),
          'process::restartservernode' => array(),
          'process::stopservernode' => array(),
          'process::import' => array(),
          'process::importdetail' => array(),
          'process::getstatus' => array(),
          'process::downloaderror' => array(),
          'process::downloadsuccess' => array(),
          //contactos administrativos de aliados
          'admincontact::index' => array(),
          'admincontact::create' => array(),
          'admincontact::edit' => array(),
          'admincontact::delete' => array(),
          //contactos tecnicos de aliados
          'technicalcontact::index' => array(),
          'technicalcontact::create' => array(),
          'technicalcontact::edit' => array(),
          'technicalcontact::delete' => array(),
          'apisupportcontact::getalltechnical' => array(),
          'apisupportcontact::addtechnical' => array(),
          'apisupportcontact::edittechnical' => array(),
          'apisupportcontact::deletetechnical' => array(),
          'apisupportcontact::findfirsttechnical' => array(),
          //smstools
          'smstools::index' => array(),
          //smscategory
          'smscategory::index' => array(),
          'smscategory::list' => array(),
          'smscategory::create' => array(),
          'smscategory::edit' => array(),
          'smscategory::delete' => array(),
          //Sms
          'sms::index' => array(),
          'sms::showlote' => array(),
          'sms::tools' => array(),
          'sms::createlote' => array(),
          'sms::delete' => array(),
          'sms::edit' => array(),
          'sms::editcontact' => array(),
          'sms::createcsv' => array(),
          'sms::createcontact' => array(),
          'sms::smscancel' => array(),
          'sms::showcontact' => array(),
          //Segment
          'segment::index' => array(),
          'segment::list' => array(),
          'segment::newsegment' => array(),
          'segment::editsegment' => array(),
          'apisegment::deletesegment' => array(),
          'apisegment::getallsegment' => array(),
          'apisegment::newsegment' => array(),
          'apisegment::customfieldbycustomfields' => array(),
          'apisegment::addsegment' => array(),
          'apisegment::getallcontactlistbysubaccount' => array(),
          'apisegment::findsegment' => array(),
          'apisegment::editsegment' => array(),
          //Sxc
          'sxc::contactsegment' => array(),
          'sxc::findcontactsegment' => array(),
          'apisxc::findcontactsegment' => array(),
          'apisxc::changestatus' => array(),
          'apisxc::customfield' => array(),
          //blockade
          'blockade::index' => array(),
          'blockade::list' => array(),
          'blockade::new' => array(),
          'apiblockade::getallblock' => array(),
          'apiblockade::addblockade' => array(),
          'apiblockade::deleteblockade' => array(),
          //sendmail
          'apisendmail::getcontactlist' => array(),
          'apisendmail::getsegment' => array(),
          'apisendmail::countcontact' => array(),
          'apisendmail::addaddressees' => array(),
          'apisendmail::only' => array(),
          'apisendmail::findmail' => array(),
          'apisendmail::findmailmessagessent' => array(),
          'apisendmail::getallmail' => array(),
          'apisendmail::getcontentmail' => array(),
          'apisendmail::getmailcategory' => array(),
          'apisendmail::getmailcategorybyidmail' => array(),
          'apisendmail::deletemail' => array(),
          'apisendmail::updateplainttext' => array(),
          'apisendmail::saveadvanceoptions' => array(),
          'apisendmail::findemailname' => array(),
          'apisendmail::findemailsender' => array(),
          'apisendmail::findmailcategory' => array(),
          'apisendmail::addadjunt' => array(),
          'apisendmail::deleteasset' => array(),
          'apisendmail::getallattachment' => array(),
          'apisendmail::insertgoogleanalitics' => array(),
          'apisendmail::sendconfirmationmail' => array(),
          'apisendmail::findmailattachment' => array(),
          'apisendmail::getassetmediagallery' => array(),
          'apisendmail::getmail' => array(),
          'apisendmail::sendscheduledateemail' => array(),
          'apisendmail::findprocessedcontact' => array(),
          'apisendmail::sendtestmail' => array(),
          'apisendmail::sendtestermail' => array(),
          'apisendmail::deleteattached' => array(),
          'apisendmail::getthumbnailmail' => array(),
          'apisendmail::getcustomfield' => array(),
          'apisendmail::getmailfilters' => array(),
          'apisendmail::getlinksbymail' => array(),
          //mailcategory
          'mailcategory::index' => array(),
          'mailcategory::list' => array(),
          'mailcategory::add' => array(),
          'mailcategory::edit' => array(),
          //apiMailCategory
          'apimailcategory::getmailcategory' => array(),
          'apimailcategory::savemailcategory' => array(),
          'apimailcategory::savemailcategoryinmail' => array(),
          'apimailcategory::getonemailcategory' => array(),
          'apimailcategory::editmailcategory' => array(),
          'apimailcategory::deletemailcategory' => array(),
          'apimailcategory::getautocompletecategory' => array(),
          'apimailcategory::getallmailcategory' => array(),
          /* Footer */
          'footer::index' => array(),
          'footer::create' => array(),
          'footer::update' => array(),
          'footer::delete' => array(),
          'footer::previeweditor' => array(),
          'footer::previewdata' => array(),
          'footer::previewindex' => array(),
          'footer::preview' => array(),
          /* ApiFooter */
          'apifooter::index' => array(),
          'apifooter::create' => array(),
          'apifooter::update' => array(),
          'apifooter::findfooter' => array(),
          'apifooter::delete' => array(),
          /* Asset */
          'asset::upload' => array(),
          'asset::list' => array(),
          'asset::show' => array(),
          'asset::showalliedassets' => array(),
          'asset::thumbnail' => array(),
          /* Predeterminedstructure */
          'mail_structure::index' => array(),
          'mail_structure::create' => array(),
          'mail_structure::editor_frame' => array(),
          'mail_structure::edit' => array(),
          /* Predeterminedstructure */
          'apimailstructure::create' => array(),
          'apimailstructure::getall' => array(),
          'apimailstructure::deletestructure' => array(),
          'apimailstructure::editmailstructure' => array(),
          /* mailtemplate */
          'mailtemplate::index' => array(),
          'mailtemplate::list' => array(),
          'mailtemplate::select' => array(),
          'mailtemplate::create' => array(),
          'mailtemplate::edit' => array(),
          'mailtemplate::selectautoresp' => array(),
          /* apimailtemplate */
          'apimailtemplatecategory::getmailtemplatecategory' => array(),
          'apimailtemplatecategory::getmailtemplatecategoryfilter' => array(),
          'apimailtemplatecategory::savemailtemplatecategory' => array(),
          'apimailtemplate::savemailtemplate' => array(),
          'apimailtemplate::editmailtemplate' => array(),
          'apimailtemplate::deletemailtemplate' => array(),
          'apimailtemplate::getmailtemplate' => array(),
          'apimailtemplate::listmailtemplate' => array(),
          'apimailtemplate::previewmailtemplate' => array(),
          'apimailtemplate::listmailtemplatebyaccount' => array(),
          'apimailtemplate::getmailtemplateautocomplete' => array(),
          'apimailtemplate::getcontenttemplate' => array(),
          'apimailtemplate::getlinkstemplate' => array(),
          'apimailtemplate::getallmailtemplate' => array(),
          'apimailtemplate::getallmailtemplatebyfilter' => array(),
          'apimailtemplate::getaccountsforalied' => array(),
          'apimailtemplate::getmailtemplatecategory' => array(),
          'apimailtemplate::getallmailtemplatesurvey' => array(),
          'apimailtemplate::getallmailtemplatesurveybyfilter' => array(),
          'apimailtemplate::gettemplatemail' => array(),
          'apimailtemplate::saveastemplatemailnew' => array(),
          'apimailtemplate::getalltemplatemail' => array(),
          /* Gallery */
          'apigallery::index' => array('asset' => array('read')),
          /* Apiunsubscribe */
          'apiunsubscribe::getcontact' => array(),
          'apiunsubscribe::insunsubscribe' => array(),
          'apiunsubscribe::insunsubscribeautomatic' => array(),
          'apiunsubscribe::insunsubscribesimple' => array(),
          'apiunsubscribe::getcontactsunsubscribe' => array(),
          'apiunsubscribe::deleteunsub' => array(),
          'apiunsubscribe::getcategories' => array(),
          'apiunsubscribe::createcontactunsub' => array(),
          /* Track */
          'track::open' => array(),
          'track::mtaevent' => array(),
      'track::mtaexample' => array(),
          'track::openautomatization' => array(),
          'track::clickautomatization' => array(),
          /* statistic */
          'statistic::index' => array(),
          'statistic::mail' => array(),
          'statistic::sms' => array(),
          'statistic::share' => array(),
          'statistic::smsshare' => array(),
          'statistic::download' => array(),
          'statistic::downloadexcel' => array(),
          'statistic::survey' => array(),
          'statistic::automaticcampaign' => array(),
//          'apistatics::getallinfomail' => array('statics' => array('read')),
          'apistatics::getallinfomail' => array(),
//          'apistatics::infoopen' => array('statics' => array('read')),
          'apistatics::infoopen' => array(),
          'apistatics::infoclic' => array(),
          'apistatics::infounsuscribed' => array(),
          'apistatics::infobounced' => array(),
          'apistatics::infospam' => array(),
//          'apistatics::datainfo' => array('statics' => array('read')),
          'apistatics::datainfo' => array(),
          'apistatics::datainfoclic' => array(),
          'apistatics::getalldomain' => array(),
          'apistatics::getallcategorybounced' => array(),
          'apistatics::getinfosms' => array(),
          'apistatics::getdetailsms' => array(),
          'apistatics::reportstatics' => array(),
          'apistatics::reportstaticssurvey' => array(),
          'apistatics::reportstaticssms' => array(),
          'apistatics::getallinfosurvey' => array(),
          'apistatics::getdataclicksmails' => array(),
          'apistatics::getsmssents' => array(),
          'apistatics::getdatatotalcampmails' => array(),
          'apistatics::getdatachargeinitial' => array(),
          'apistatics::getdatatotalcampsms' => array(),
          'apistatics::getdatetabdatamail' => array(),
          'apistatics::getallconfiguration' => array(),
          'apistatics::getrolservices' => array(),
          'apistatics::datainfomail' => array(),
          'apistatics::datainfomaillote' => array(),
          /* smstemplate */
          'smstemplate::index' => array(),
          'smstemplate::list' => array(),
          'smstemplate::create' => array(),
          'smstemplate::edit' => array(),
          /* Apismstemplate */
          'apismstemplatecategory::listsmstemplatecategory' => array(),
          'apismstemplatecategory::savesmstemplatecategory' => array(),
          'apismstemplate::listsmstemplate' => array(),
          'apismstemplate::savesmstemplate' => array(),
          'apismstemplate::getsmstemplate' => array(),
          'apismstemplate::editsmstemplate' => array(),
          'apismstemplate::getsmstemplateautocomplete' => array(),
          'apismstemplate::getallsmstemplate' => array(),
          'apismstemplate::deletesmstemplate' => array(),
          'apismstemplate::gettags' => array(),
          'apismstemplate::listfullsmstemplate' => array(),
          /* automatic campaign */
          'automaticcampaign::index' => array(),
          'automaticcampaign::list' => array(),
          'automaticcampaign::create' => array(),
          'automaticcampaign::edit' => array(),
          'automaticcampaign::viewscheme' => array(),
          /* flowchart */
          'flowchart::index' => array(),
          'flowchart::call' => array(),
          'flowchart::popoversegment' => array(),
          'flowchart::popovertime' => array(),
          'flowchart::popovermail' => array(),
          'flowchart::frameeditor' => array(),
          'flowchart::popoversms' => array(),
          'flowchart::popoveraction' => array(),
          'flowchart::popoversurvey' => array(),
          'flowchart::connection' => array(),
          'flowchart::statictis' => array(),
          /* automaticcampaigncategory */
          'automaticcampaigncategory::index' => array(),
          'automaticcampaigncategory::list' => array(),
          'automaticcampaigncategory::create' => array(),
          'automaticcampaigncategory::edit' => array(),
          /* apiapiautomaticcampaigncategory */
          'apiautomaticcampaigncategory::saveautomacampcateg' => array(),
          'apiautomaticcampaigncategory::listautocampcateg' => array(),
          'apiautomaticcampaigncategory::getautomaticcampcateg' => array(),
          'apiautomaticcampaigncategory::editautomaticcampcateg' => array(),
          'apiautomaticcampaigncategory::deleteautomaticcampcateg' => array(),
          'apiautomaticcampaigncategory::getcategory' => array(),
          'apiautomaticcampaigncategory::allcategory' => array(),
          /* apiapiautomaticcampaign */
          'apiautomaticcampaign::saveautomaticcampaign' => array(),
          'apiautomaticcampaign::getautomaticcampaign' => array(),
          'apiautomaticcampaign::updateautomaticcampaign' => array(),
          'apiautomaticcampaign::updateautomaticcampaignconfiguration' => array(),
          'apiautomaticcampaign::savedraftautomaticcampaign' => array(),
          'apiautomaticcampaign::updatecampaignall' => array(),
          'apiautomaticcampaign::updatestatus' => array(),
          'apiautomaticcampaign::listautocamp' => array(),
          'apiautomaticcampaign::getschemeautomaticcampaign' => array(),
          'apiautomaticcampaign::cancelautomaticcampaign' => array(),
          'apiautomaticcampaign::cloneca' => array(),
          /* Apiapikey */
          'apiapikey::index' => array(),
          'apiapikey::create' => array(),
          'apiapikey::update' => array(),
          'apiapikey::updatestatus' => array(),
          'apiapikey::delete' => array(),
          /* Api */
          'apiversionone::echo' => array(),
          /* Apimail */
          'apimail::newmail' => array(),
          'apimail::setstatusmail' => array(),
          'apimail::newsinglemail' => array(),
          'apimail::previewmailtemplate' => array(),
          'apimail::getmailcontentjson' => array(),
          'apimail::editmail' => array(),
          'apimail::cancelmail' => array(),
          'apimail::detailmail' => array(),
          'apimail::getmdgpublish' => array(),
          /* Apikey */
          'apikey::index' => array(),
          /* Scheduled */
          'scheduled::index' => array(),
          'scheduled::list' => array(),
          /* Apischeduled */
          'apischeduled::getscheduled' => array(),
          /* Language */
          'language::index' => array(),
          'language::list' => array(),
          'language::create' => array(),
          'language::edit' => array(),
          'language::delete' => array(),
          /* Apilanguage */
          'apilanguage::delete' => array(),
          'apilanguage::edit' => array(),
          'apilanguage::getlanguage' => array(),
          'apilanguage::getlanguagefirst' => array(),
          /* History */
          'history::index' => array(),
          'history::list' => array(),
          /* Apihistory */
          'apihistory::gethistory' => array(),
          'apihistory::getmasteraccounts' => array(),
          'apihistory::getallieds' => array(),
          'apihistory::getaccounts' => array(),
          'apihistory::getsubaccounts' => array(),
          /* Knowledgebase */
          'knowledgebase::index' => array(),
          'knowledgebase::list' => array(),
          'knowledgebase::import' => array(),
          'knowledgebase::download' => array(),
          'knowledgebase::validate' => array(),
          /* Apiknowledgebase */
          'apiknowledgebase::importcsv' => array(),
          'apiknowledgebase::getimports' => array(),
          'apiknowledgebase::validatecsv' => array(),
          /* Country */
          'country::index' => array(),
          'country::list' => array(),
          'country::edit' => array(),
          /* Apicountry */
          'apicountry::getcountries' => array(),
          'apicountry::getonecountry' => array(),
          'apicountry::edit' => array(),
          'apicountry::getallindicatives' => array(),
          /* currency */
          'currency::index' => array(),
          'currency::list' => array(),
          'currency::create' => array(),
          'currency::edit' => array(),
          /* apicurrency */
          'apicurrency::listcurrency' => array(),
          'apicurrency::createcurrency' => array(),
          'apicurrency::getcurrency' => array(),
          'apicurrency::editcurrency' => array(),
          'apicurrency::deletecurrency' => array(),
          /* tax */
          'tax::index' => array(),
          'tax::list' => array(),
          'tax::create' => array(),
          'tax::edit' => array(),
          /* apitax */
          'apitax::listtax' => array(),
          'apitax::createtax' => array(),
          'apitax::gettax' => array(),
          'apitax::edittax' => array(),
          'apitax::deletetax' => array(),
          'apitax::listtaxfull' => array(),
          /* pricelist */
          'pricelist::index' => array(),
          'pricelist::list' => array(),
          'pricelist::create' => array(),
          'pricelist::edit' => array(),
          /* apipricelist */
          'apipricelist::listpricelist' => array(),
          'apipricelist::createlistprice' => array(),
          'apipricelist::getpricelist' => array(),
          'apipricelist::editpricelist' => array(),
          'apipricelist::deletepricelist' => array(),
          'apipricelist::listfullpricelist' => array(),
          /* paymentplan */
          'paymentplan::index' => array(),
          'paymentplan::show' => array(),
          'paymentplan::list' => array(),
          'paymentplan::create' => array(),
          'paymentplan::edit' => array(),
          /* apipaymentplan */
          'apipaymentplan::listpaymentplan' => array(),
          'apipaymentplan::showpaymentplan' => array(),
          'apipaymentplan::createpaymentplan' => array(),
          'apipaymentplan::getpaymentplan' => array(),
          'apipaymentplan::editpaymentplan' => array(),
          'apipaymentplan::deletepaymentplan' => array(),
          'apipaymentplan::validatecourtesyplan' => array(),
          'apipaymentplan::listservices' => array(),
          /* plantype */
          'plantype::listplanttype' => array(),
          /* Customizing */
          'customizing::index' => array(),
          'customizing::show' => array(),
          'customizing::list' => array(),
          'customizing::add' => array(),
          'customizing::edit' => array(),
          'customizing::delete' => array(),
          'customizing::select' => array(),
          /* Apicustomizing */
          'apicustomizing::delete' => array(),
          'apicustomizing::select' => array(),
          'apicustomizing::edit' => array(),
          'apicustomizing::getcustomizing' => array(),
          'apicustomizing::getonecustomizing' => array(),
          'apicustomizing::getsocialnetworks' => array(),
          'apicustomizing::setItemBlockInfo' => array(),
          'apicustomizing::add' => array(),
          /* SMS CATEGORY */
          'apismscategory::getallcategory' => array(),
          'apismscategory::getsmscategory' => array(),
          'apismscategory::deletesmscategory' => array(),
          /* API SAXS */
          'apisaxs::getall' => array(),
          'apisaxs::savedkim' => array(),
          /* REPORTES */
          'report::index' => array('report' => array('read')),
          'report::graph' => array('report' => array('read')),
          'report::indexsms' => array('report' => array('read')),
          'report::list' => array('report' => array('read')),
          'report::sms' => array('report' => array('read')),
          'report::listsms' => array('report' => array('read')),
          'report::listgraph' => array('report' => array('read')),
          'report::excelsms' => array('report' => array('read')),
          'report::excelsmsday' => array('report' => array('read')),
          'report::infosms' => array('report' => array('read')),
          'report::infomail' => array('report' => array('read')),
          'report::listrecharge' => array('report' => array('read')),
          'report::changeplanuser' => array('report' => array('read')),
          'report::statisticmail' => array('report' => array('read')),
          'report::reportvalidation' => array('report' => array('read')),
          'reports::index' => array('report' => array('read')),
          'apireport::getallreportemail' => array('report' => array('read')),
          'apireport::getallaccountbyallied' => array('report' => array('read')),
          'apireport::downloadreport' => array('report' => array('read')),
          'apireport::downloadreportsms' => array('report' => array('read')),
          'apireport::getallreportsms' => array('report' => array('read')),
          'apireport::getallreportrecharge' => array('report' => array('read')),
          'apireport::downloadreportrecharge' => array('report' => array('read')),
          'apireport::getallreportchangeplan' => array('report' => array('read')),
          'apireport::downloadreportchangeplan' => array('report' => array('read')),
          'apireport::graphmail' => array('report' => array('read')),
          'apireport::graphsms' => array('report' => array('read')),
          'apireport::getinfoexcelsms' => array('report' => array('read')),
          'apireport::getinfoexcelsmsday' => array('report' => array('read')),
          'apireport::infosms' => array('report' => array('read')),
          'apireport::infomail' => array('report' => array('read')),
          'apireport::downloadsms' => array('report' => array('read')),
          'apireport::getallsubaccount' => array('report' => array('read')),
          'apireport::getemailusers' => array('report' => array('read')),
          'apireport::downloadsmsbyday' => array('report' => array('read')),
          'apireport::dowloadreportinfodetailsms' => array('report' => array('read')),
          'apireport::dowloadreportinfodetailmail' => array('report' => array('read')),
          'apireport::getallmailvalidation' => array('report' => array('read')),
          'apireport::downloadmailvalidation' => array('report' => array('read')),
          'apireport::getallmailbounced' => array('report' => array('read')),
          'apireport::downloadgetallmailbounced' => array('report' => array('read')),
          /* Thumbnailmailtemplate */
          'thumbnail::mailtemplateshow' => array(),
          'thumbnail::mailshow' => array(),
          /* ActivityLog */
          'activitylog::index' => array(),
          'activitylog::list' => array(),
          'apiactivitylog::listactivitylog' => array(),
          /* Account category */
          'accountcategory::index' => array(),
          'accountcategory::list' => array(),
          'accountcategory::create' => array(),
          'accountcategory::edit' => array(),
          /* Api Account category */
          'apiaccountcategory::list' => array(),
          'apiaccountcategory::getaccountcategory' => array(),
          'apiaccountcategory::saveaccountcategory' => array(),
          'apiaccountcategory::editaccountcategory' => array(),
          'apiaccountcategory::deleteaccountcategory' => array(),
          'apiaccountcategory::getaccountcategories' => array(),
          /* Smssendingrule */
          'smssendingrule::index' => array(),
          'smssendingrule::list' => array(),
          'smssendingrule::create' => array(),
          'smssendingrule::edit' => array(),
          'smssendingrule::show' => array(),
          /* Apismssendingrule */
          'apismssendingrule::listsmssendingrule' => array(),
//          'apismssendingrule::listfullindicative' => array('smssendingrule' => array('read')),
          'apismssendingrule::showsmssendingrule' => array(),
          'apismssendingrule::createsmssendingrule' => array(),
          'apismssendingrule::editsmssendingrule' => array(),
          'apismssendingrule::deletesmssendingrule' => array(),
          'apismssendingrule::listall' => array(),
          /* Api sms */
          'apisms::countsaxssms' => array(),
          'apisms::getcountcontacts' => array(),
          'apisms::createsmssend' => array(),
          'apisms::editsmssend' => array(),
          'apisms::delete' => array(),
          'apisms::createsmslote' => array(),
          'apisms::createsmsencrypted' => array(),
          'apisms::createsinglesms' => array(),
          'apisms::detailsms' => array(),
          'apisms::getall' => array(),
          'apisms::getone' => array(),
          'apisms::createcsv' => array(),
          'apisms::changestatus' => array(),
          'apisms::deletevariouslotes' => array(),
          'apisms::find' => array(),
          'apisms::validatebalance' => array(),
          'apisms::getbalancesubaccount' => array(),
          'apisms::detailsmslote' => array(),
          /* Autoresponder */
          'autoresponder::index' => array(),
          'autoresponder::tools' => array(),
          'autoresponder::birthday' => array(),
          'autoresponder::birthdaysms' => array(),
          'autoresponder::list' => array(),
          'autoresponder::contenteditor' => array(),
          'autoresponder::contenthtml' => array(),
          'autoresponder::previeweditor' => array(),
          'autoresponder::previewdata' => array(),
          'autoresponder::preview' => array(),
          'autoresponder::previewhtml' => array(),
          /* ApiAutoresponder */
          'apiautoresponder::saveautoresponder' => array(),
          'apiautoresponder::saveautorespdesms' => array(),
          'apiautoresponder::savecontentautoresponder' => array(),
          'apiautoresponder::getautoresponse' => array(),
          'apiautoresponder::getallautoresponder' => array(),
          'apiautoresponder::deleteautoresponder' => array(),
          'apiautoresponder::getautoresponder' => array(),
          'apiautoresponder::getalledit' => array(),
          'apiautoresponder::getallcustomfield' => array(),
          'apiautoresponder::addcustomfield' => array(),
          /* FORMS */
          'customfieldform::template' => array(),
          'customfieldform::popovertemplate' => array(),
          'forms::index' => array(),
          'forms::structureform' => array(),
          'forms::list' => array(),
          'forms::create' => array(),
          'forms::contacts' => array(),
          'forms::basicinformation' => array(),
          'forms::forms' => array(),
          'forms::report' => array(),
          'forms::edit' => array(),
          'apiforms::savebasicinformation' => array(),
          'apiforms::savebasicinformation' => array(),
          'apiforms::listforms' => array(),
          'apiforms::deleteform' => array(),
          'apiforms::getallformscategories' => array(),
          'apiforms::getinformationform' => array(),
          'apiforms::getoptin' => array(),
          'apiforms::getwelcomemail' => array(),
          'apiforms::getnotificationform' => array(),
          'apiforms::saveforms' => array(),
          'apiforms::updatebasicinformation' => array(),
          'apiforms::addformcategory' => array(),
          'apiforms::getsuscriptsform' => array(),
          'apiforms::dowloadreportcontactsform' => array(),
          /* MAIL */
          'apisendmail::changetestmail' => array(),
          /* Survey Category */
          'surveycategory::index' => array(),
          'surveycategory::list' => array(),
          'surveycategory::create' => array(),
          'surveycategory::edit' => array(),
          /* ApiSurveyCategory */
          'apisurveycategory::listsurveycategory' => array(),
          'apisurveycategory::createsurveycategory' => array(),
          'apisurveycategory::getsurveycategory' => array(),
          'apisurveycategory::editsurveycategory' => array(),
          'apisurveycategory::deletesurveycategory' => array(),
          /* Survey */
          'survey::index' => array(),
          'survey::list' => array(),
          'survey::create' => array(),
          'survey::basicinformation' => array(),
          'survey::survey' => array(),
          'survey::confirmation' => array(),
          'survey::share' => array(),
          'survey::showsurvey' => array(),
          'survey::congratulations' => array(),
          /* Apisurvey */
          'apisurvey::list' => array(),
          'apisurvey::listcategory' => array(),
          'apisurvey::createsurvey' => array(),
          'apisurvey::findsurvey' => array(),
          'apisurvey::editsurvey' => array(),
          'apisurvey::savecontentsurvey' => array(),
          'apisurvey::getsurveycontent' => array(),
          'apisurvey::saveconfirmation' => array(),
          'apisurvey::linkgenerator' => array(),
          'apisurvey::saveanswer' => array(),
          'apisurvey::uploadimage' => array(),
          'apisurvey::sendmail' => array(),
          'apisurvey::changestatus' => array(),
          'apisurvey::changetype' => array(),
          'apisurvey::getcategory' => array(),
          'apisurvey::getpublicsurvey' => array(),
          'apisurvey::duplicatesurvey' => array(),
          'apisurvey::deletesurvey' => array(),
          /* DOBLE OPTIN */
          'apicontact::dobleoptin' => array(),
          /* DASHBOARD CONFIG */
          'dashboardconfig::index' => array(),
          'dashboardconfig::configdashboard' => array(),
          /* APIDASHBOARD CONFIG */
          'apidashboardconfig::uploadimage' => array(),
          'apidashboardconfig::getallimage' => array(),
          'apidashboardconfig::getcondigdashboard' => array(),
          'apidashboardconfig::getdefaultdashboard' => array(),
          'apidashboardconfig::savecondigdashboard' => array(),
          'apidashboardconfig::getconfigdashboardclient' => array(),
          /* apipost */
          'apipost::save' => array(),
          /* MAIL TESTER */
          'mailtester::show' => array(),
          /* accounting */
          'accounting::index' => array(),
          'accounting::list' => array(),
          /* Apiaccounting */
          'apiaccounting::list' => array(),
          'testsurvey::show' => array(),
          'apitesttwoway::receivedsms' => array(),
          'apitesttwoway::receivedsms' => array(),
          /* statisallied */
          'statisallied::index' => array(),
          'statisallied::list' => array(),
          'statisallied::create' => array(),
          /* Apistatisallied */
          'apistatisallied::getstatisallied' => array(),
          /* Apisubaccount */
          'apisubaccount::getavailableservicesamount' => array(),
          /* Apismscategory */
          'apistatisallied::downloadreport' => array(),
          /* Apisubaccount */
          'apisubaccount::getavailableservicesamount' => array(),
          /* Landing */
          'landingpage::index' => array(),
          'landingpage::list' => array(),
          'landingpage::create' => array(),
          'landingpage::pagebuilder' => array(),
          'landingpage::designer' => array(),
          'landingpage::basicinformation' => array(),
          'landingpage::landing' => array(),
          'landingpage::confirmation' => array(),
          'landingpage::share' => array(),
          'landingpage::showsurvey' => array(),
          'landingpage::congratulations' => array(),
          'landingpage::preview' => array(),
          //Smstwoway
          'smstwoway::toolstwoway' => array(),
          'smstwoway::create' => array(),
          'smstwoway::speedsent' => array(),
          'smstwoway::createcsv' => array(),
          'smstwoway::createlotetwoway' => array(),
          'smstwoway::editspeedsent' => array(),
          //Apismstwoway
          'apismstwoway::createsmslotetwoway' => array(),
          'apismstwoway::editsmslotetwowaysend' => array(),
          'apismstwoway::receivedsms' => array(),
          //Smstwoway
          'smstwoway::index' => array(),
          'apismstwoway::getalls' => array(),
          'apismstwoway::detailsms' => array(),
          'apismstwoway::info' => array(),
          'apismstwoway::getone' => array(),
          'statistic::smstwoway' => array(),
          'apistatics::reportstaticssmstwoway' => array(),
          'smstwoway::createdcontact' => array(),
          'smstwoway::list' => array(),
          'smstwoway::showlotetwoway' => array(),
          'apismstwoway::createcsv' => array(),
          'apismstwoway::changestatus' => array(),
          'apismstwoway::savesmstowwaycontact' => array(),
          'apismstwoway::getalledit' => array(),
          'apismstwoway::getalleditcontact' => array(),
          'apismstwoway::changestatusedit' => array(),
          'apismstwoway::changestatusedit' => array('sms' => array('create')),
          'apismstwoway::editcsv' => array(),
          /* Apilanding */
          'apilandingpage::listlanding' => array(),
          'apilandingpage::getlandingcategory' => array(),
          'apilandingpage::createlandingcategory' => array(),
          'apilandingpage::createlandingpage' => array(),
          'apilandingpage::findlanding' => array(),
          'apilandingpage::editlandingpage' => array(),
          'apilandingpage::createpublicview' => array(),
          'apilandingpage::findlandingcountview' => array(),
          'apilandingpage::findlandingcsc' => array(),
          'landingpage::content' => array(),
          
          'apilandingpage::findlandingcountview' => array(),
          'apilandingpage::getcontentlandingpage' => array(),
          'apilandingpage::savecontentlandingpage' => array(),
          'apilandingpage::uploadimagetolandingpage' => array(),
          'apilandingpage::deletelandingpage' => array(),
          'apilandingpage::linkgenerator' => array(),
          'apilandingpage::hascontentlandingpage' => array(),
          'apilandingpage::duplicatelandingpage' => array(),
          /* PublicLandingPage */
          'publiclandingpage::publication' => array(),
          'publiclandingpage::errors' => array(),
          'account::createhabeasdata' => array(),
          /* habeasdata */
          'habeasdata::index' => array(),
          'apimailtemplate::getallmailtemplatelandingpage' => array(),
          'apimailtemplate::getallmailtemplatelandingpagebyfilter' => array(),
          'apilandingpage::sendmaillandingpage' => array(),
          /* LandingPageTemplate */
          'landingpagetemplate::index' => array(),
          'landingpagetemplate::list' => array(),
          'landingpagetemplate::create' => array(),
          'landingpagetemplate::preview' => array(),
          'landingpagetemplate::selecttemplate' => array(),
          /* ApiLandingPageTemplate */
          'apilandingpagetemplate::getalltemplates' => array(),
          'apilandingpagetemplate::createlandingpagetemplate' => array(),
          'apilandingpagetemplate::savecontentlandingpagetemplate' => array(),
          'apilandingpagetemplate::getcontentlandingpagetemplate' => array(),
          'apilandingpagetemplate::getlandingpagetemplate' => array(),
          'apilandingpagetemplate::uploadimagetolandingpage' => array(),
          'apilandingpagetemplate::selectlandingpage' => array(),
          /* ApiLandingPageTemplateCategory */
          'apilandingpagetemplatecategory::getall' => array(),
          'apilandingpagetemplatecategory::savesimplecategory' => array(),
          'landingpagecategory::index' => array(),
          'landingpagecategory::list' => array(),
          'landingpagecategory::create' => array(),
          'apilandingpagecategory::create' => array(),
          'apilandingpagecategory::getallcategory' => array(),
          'apilandingpagecategory::getonecategory' => array(),
          'apilandingpagecategory::editcategory' => array(),
          'apisms::verifysmstwowayservice' => array(),
          'apilandingpagecategory::deletecategory' => array(),
          'apilandingpagetemplatecategory::getall' => array(),
          'apilandingpage::changestatus' => array('landingpage' => array('read')),
          'apilandingpage::changetype' => array('landingpage' => array('read')),
          /* Report Export Report mail */
          'report::reportmail' => array(),
          'report::reportsms' => array(),
          /* apimailtemplatecategory */
          'apimailtemplatecategory::listmailtemplatecategory' => array(),
          'apimailtemplatecategory::getautomaticcampcateg' => array(),
          'apimailtemplatecategory::editmailtemplatecategory' => array(),
          'apimailtemplatecategory::deletemailtemplatecategory' => array(),
          'apimailtemplatecategory::getcategory' => array(),
          'apimailtemplatecategory::allcategory' => array(),
          'apimailtemplatecategory::getmailtemplate' => array(),
          /* mailtemplatecategory */
          'mailtemplatecategory::index' => array(),
          'mailtemplatecategory::list' => array(),
          'mailtemplatecategory::create' => array(),
          'mailtemplatecategory::edit' => array(),
          /* Statistics node Automatic campaign */
          'statistic::nodo' => array(),
          'apistatics::getautomaticcampaignbynode' => array(),
          /* apisendmail */
          'apisendmail::downloadmailpreview' => array(),
          /* namesender */
          'namesender::index' => array(),
          'namesender::list' => array(),
          'namesender::create' => array(),
          'namesender::edit' => array(),
          /* apinamesender */
          'apinamesender::listnamesender' => array(),
          'apinamesender::savenamesender' => array(),
          'apinamesender::deletenamesender' => array(),
          'apinamesender::getnamesender' => array(),
          'apinamesender::editnamesender' => array(),
          /* replyto */
          'replyto::index' => array(),
          'replyto::list' => array(),
          'replyto::create' => array(),
          'replyto::edit' => array(),
          /* apireplyto */
          'apireplyto::listreplyto' => array(),
          'apireplyto::savereplyto' => array(),
          'apireplyto::deletereplyto' => array(),
          'apireplyto::getreplyto' => array(),
          'apireplyto::editreplyto' => array(),
          /* emailsender */
          'emailsender::index' => array(),
          'emailsender::list' => array(),
          'emailsender::create' => array(),
          'emailsender::edit' => array(),
          /* apiemailsender */
          'apiemailsender::listemailsender' => array(),
          'apiemailsender::saveemailsender' => array(),
          'apiemailsender::deleteemailsender' => array(),
          'apiemailsender::getemailsender' => array(),
          'apiemailsender::editemailsender' => array(),
          /* mailreplyto*/
          'mail::replyto' => array(),
          'mail::addreplyto' => array(),
          /* Rate */
          'rate::index' => array(),
          'rate::list' => array(),
          'rate::create' => array(),
          'apirate::create' => array(),
          'apirate::getall' => array(),
          'apirate::getone' => array(),
          'apirate::edit' => array(),
          /* Services */
          'services::getallservice' => array(),
          /* Country */
          'country::getallcountry' => array(),
          /* apisendmailreplyto */
          'apisendmail::findreplyto' => array(),
          /* apilanding*/
          'apilandingpage::linkfb' => array(),
           /* ip */
          'ip::index' => array(),
          'ip::list' => array(),
          'ip::create' => array(),
          'ip::edit' => array(),
          /* apiip */
          'apiip::listip' => array(),
          'apiip::saveip' => array(),
          'apiip::deleteip' => array(),
          'apiip::getip' => array(),
          'apiip::editip' => array(),
           /* mtaxip */
          'mtaxip::index' => array(),
          'mtaxip::list' => array(),
          'mtaxip::create' => array(),
          'mtaxip::edit' => array(),
          /* apimtaxip */
          'apimtaxip::listmtaxip' => array(),
          'apimtaxip::savemtaxip' => array(),
          'apimtaxip::deletemtaxip' => array(),
          'apimtaxip::getmtaxip' => array(),
          'apimtaxip::editmtaxip' => array(),
          'apimtaxip::getipmta' => array(),
          /* Smsxemail */
          'smsxemail::index' => array(),
          'smsxemail::create' => array(),
          'smsxemail::smscategory' => array(),
          'smsxemail::generatekey' => array(),
          'apismsxemail::create' => array(),
          'apismsxemail::getone' => array(),
          'apismsxemail::copykey' => array(),
          /* Report Smsxemail */
          'report::smsxemail' => array(),
          'report::downloadsmxemail' => array(),
          'report::listsmschannel' => array(),
          /* Api Report Smsxemail */
          'apireport::getallreportsmsxemail' => array(),
          /* Api Report Sms Channel */
          'apireport::getdatasmschannel' => array(),
          'downloadsms::download' => array(),
          /* Autoresponder */
//          'autoresponder::tools' => array(),
           /*Report Sms por destinatarios*/
          'report::infosmsbydestinataries' => array('report' => array('read')),
          /*Api report Sms por destinatarios*/
          'apireport::getdatasmsbydestinataries' => array(),
          /*Download Report Sms by destinataries*/
          'apireport::downloadreportsmsbydestinataries' => array(),
          /* Api Statitic Mail*/
          'apistatics::infobuzon' => array(),
          /* Api Report Mail*/
          'apireport::reportmail' => array(),
          /* Api Sms download sms failed*/
          'apisms::downloadreportsmsfailed' => array(),
          'track::eventprocessors' => array(),
          'track::testevent' => array(),
          'apisms::getsmscampaigndetail' => array(),
          /*Smstwowaypostnotify*/
          'smstwowaypostnotify::index'=>array(),
          'smstwowaypostnotify::create'=>array(),
          'smstwowaypostnotify::list'=>array(),
          /*Apismstwowaypostnotify*/
          'apismstwowaypostnotify::create'=>array(),
          'apismstwowaypostnotify::getsavedcredentials'=>array(),
          /*Apismstwoway*/
          'apismstwoway::createsinglesmstwoway' => array(),
          'apismstwoway::getavalaiblecountry' => array(),
          /* Apicontaclist */
          'apicontactlist::permissioncustomfield' => array(),
          'apisms::downloadfailedsmscontact' => array(), 
          'apisms::validatesmscontact' => array(),
          'sms::validatecontact' => array(),
          'apisms::sendmailnotsmsbalance' => array(),
          'mail::compose' => array(),
          'mail::structurename' => array(),
          'apimail::loadpdf' => array(),
          'mail::deletedall' => array(),
          'apisendmail::getallattachmentpdf' => array(),
          'error::maintenance' => array(),
          /* Api Voice-messages */
          'apivoicemessages::createlote' => array(),
          /* Subscribe */
          'subscribe::form' => array(),
          /* Register */
          'register::api' => array(),
          /* Paymentplan */
          'paymentplan::api' => array(),
          'paymentplan::prices' => array(),
          /* Register */
          'register::continueregister' => array(), 
          /* flowchart */
          'flowchart::popoverclick' => array(),
          'flowchart::popoverlinks' => array(),
          /* Account */
          'account::recharges' => array(),
          'account::rechargeservices' => array(),
          'account::response' => array(),
          'account::confirmation' => array(),
          /* Register */
          'register::rangesprices' => array(),
          /* Whatsapp */
          'whatsapp::index' => array(),
          'whatsapp::create' => array(),
          'whatsapp::getreceiver' => array(),
          'whatsapp::list' => array(),
          'statistic::whatsapp' => array(),
          /* Api Whatsapp */
          'apiwhatsapp::getalls' => array(),
          'apiwhatsapp::getallcategory' => array(),
          'apiwhatsapp::getcontactlist' => array(),
          'apiwhatsapp::listwpptemplate' => array(),
          'apiwhatsapp::countcontacts' => array(),
          /* Wpp Category */
          'wppcategory::index' => array(),
          'wppcategory::list' => array(),
          'wppcategory::create' => array(),
          'wppcategory::edit' => array(),
          'wppcategory::delete' => array(),
          /* Api WPP Category */
          'apiwppcategory::getwppcategory' => array(),
          'apiwppcategory::deletewppcategory' => array(),
          /* wpptemplate */
          'wpptemplate::index' => array(),
          'wpptemplate::list' => array(),
          'wpptemplate::create' => array(),
          'wpptemplate::edit' => array(),
          /* Apiwpptemplate */
          'apiwpptemplate::listwpptemplate' => array(),
          'apiwpptemplate::listwpptempcategory' => array(),
          'apiwpptemplate::savewpptemplate' => array(),
          'apiwpptemplate::editwpptemplate' => array(),
          'apiwpptemplate::deletewpptemplate' => array(),
      );

      $this->cache->save('controllermap-cache', $map);
    }

    return $map;
  }

  public function setJsonResponse($content, $status, $message) {
    $this->view->disable();

    $this->_isJsonResponse = true;
    $this->response->setContentType('application/json', 'UTF-8');
    $this->response->setStatusCode($status, $message);

    if (\is_array($content)) {
      $content = \json_encode($content);
    }
    $this->response->setContent($content);
    return $this->response;
  }

  protected function validateResponse($controller, $action = null) {
    $controllersWithjsonResponse = array('api');
    if (\in_array($controller, $controllersWithjsonResponse)) {
      return true;
    }
    return false;
  }

  /**
   * This action is executed before execute any action in the application
   */
  public function beforeDispatch(Event $event, Dispatcher $dispatcher) {
    $controller = \strtolower($dispatcher->getControllerName());
    $action = \strtolower($dispatcher->getActionName());
    $resource = "$controller::$action";
    if ($this->serverStatus == 0 && !\in_array($this->ip, $this->allowed_ips)) {
      $this->publicurls = array(
          'error::index',
          'error::link',
          'error::notavailable',
          'error::unauthorized',
          'error::forbidden',
          'track::click',
          'track::open',
          'track::openautomatization',
          'track::clickautomatization',
          'webversion::show',
          'apiunsubscribe::getcontact',
          'apiunsubscribe::insunsubscribe',
          'apiunsubscribe::insunsubscribeautomatic',
          'apiunsubscribe::insunsubscribesimple',
          'apiunsubscribe::getcontactsunsubscribe',
          'apiunsubscribe::deleteunsub',
          'apiunsubscribe::getcategories',
          'apiunsubscribe::createcontactunsub',
          'unsubscribe::contact',
          'unsubscribe::list',
          'unsubscribe::new',
          'unsubscribe::create',
          'unsubscribe::contactautomatic',
          /* SMSTWOWAY */
          'smstwoway::toolstwoway',
          'smstwoway::create',
          'smstwoway::list',
          'smstwoway::speedsent',
          'smstwoway::createcsv',
          'smstwoway::editspeedsent',
          'smstwoway::index',
          'smstwoway::showlotetwoway',
          /* TRACK */
          'track::mtaevent',
          'track::mtaexample',
          'thumbnail::mailtemplateshow',
          'thumbnail::mailshow',
          'statistic::share',
          'statistic::smsshare',
          'apistatics::getallinfomail',
          'apistatics::infoopen',
          'apistatics::datainfo',
          'apistatics::getinfosms',
          'apistatics::getdetailsms',
          'country::country',
          'country::state',
          'country::cities',
          /* Register */
          'register::index',
          'register::signup',
          'register::paymentplan',
          'register::paymentplandetail',
          'register::payment',
          'register::pay',
          'register::validatemail',
          'register::congratulations',
          'register::welcome',
          'apiregister::create',
          'apiregister::listpaymentplans',
          'apiregister::detailpaymentplan',
          'apiregister::verifyaccount',
          'apiregister::assignpaymentplan',
          'apiregister::getappidfacebook',
          'apiregister::createwithfacebook',
          'apiregister::completeprofileuser',
          'apicontact::addcontactform',
          'apiforms::getcontentform',
          /* DOBLE OPTIN */
          'apicontact::dobleoptin',
          'survey::showsurvey',
          'apisurvey::getsurveycontent',
          'apisurvey::saveanswer',
          'survey::congratulations',
          /* ASSET */
          'asset::thumbnailmail',
          /* MAIL TESTER */
          'mailtester::show',
          'testsurvey::show',
          'apismstwoway::receivedsms',
          'apismstwoway::getallsmstwoway',
          /* Public LandingPages */
          'publiclandingpage::publication',
          'publiclandingpage::errors',
          'landingpage::preview',
          'landingpagetemplate::preview',
          /*Api statics mail share*/
          'apistatics::infoclic',
          'apistatics::infounsuscribed',
          'apistatics::infobounced',
          'apistatics::infospam',
          /* Download */
          'downloadsms::download',
          'apisms::find',
          /* Api Report Mail*/
          'apireport::reportmail',
          'track::eventprocessors',
          'track::testevent',
          'apimail::loadpdf',
          'mail::compose',
          'mail::structurename',
          'error::maintenance',
          /* Api Voice-messages */
          'apivoicemessages::createlote',
          /* Subscribe */
          'subscribe::form',
          /* Register */
          'register::api',
          /* Paymentplan */
          'paymentplan::api',
          'paymentplan::prices',
          /* Register */
          'register::continueregister',
          /* WhatsApp */
          'whatsapp::getreceiver',
          'whatsapp::index',
          'whatsapp::list',
          /* Api Whatsapp */
          'apiwhatsapp::getallwhatsapp',
          /*Contact*/
          'contact::downloadlc',
      );

      if (!\in_array($resource, $this->publicurls)) {
        return $this->response->redirect('error/notavailable');
      }

//            return false;
    } else {
      $role = 'GUEST';
      if ($this->session->get('authenticated')) {
        $user = User::findFirstByIdUser($this->session->get('idUser'));
        if ($user) {
          $role = $user->role->name;
          $userEfective = new stdClass();
          $userEfective->enable = false;

          $efective = $this->session->get('userEfective');
          if (isset($efective)) {
            $userEfective->enable = true;
            $role = $user->role->name;
//            var_dump($user->role);
            $user->role = $efective->User[0]->role;
          }
          // Inyectar el usuario
//          var_dump($user);exit();
          $this->_dependencyInjector->set('user', $user);
          $this->_dependencyInjector->set('userEfective', $userEfective);
        }
      } else {
        try {
          /* $timer = Phalcon\DI::getDefault()->get('timerObject');
            $timer->reset();
            $timer->startTimer('Authentication', 'Start Authentication'); */

          $method = $this->request->getMethod();
          $data = $this->request->getRawBody();
          $uri = ((@$_SERVER["HTTPS"] == "on") ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
          $auth = new \Sigmamovil\General\Authorization\AuthHmacHeader($method, $uri, $data,Phalcon\DI::getDefault()->get('logger'));

          if ($auth->verifyHeader() && $auth->checkPermissions($controller, $action) && $auth->processHeader()) {
            $apikey = Apikey::findFirst(array(
              'conditions' => 'apikey = ?1 and status=1',
              'bind' => array(1 => $auth->getAuthUser())));
  
            if($apikey){
                if ($apikey && $auth->checkUserPWD($apikey) && $apikey->User) {
                  $user = $apikey->User;
                  $role = $user->Role->name;
                  $user->api = true;
                  $this->_dependencyInjector->set('user', $user);

                  if (!empty($data)) {
                    $content = new stdClass();
                    $content->content = $data;
                  //Se crea el objeto RequestContent para ser inyectado y usado en los controladores
                    $this->_dependencyInjector->set('requestContent', $content);
                  }
                } else {
                  throw new \Exception("API Key Invalido o no se encuentra activo");
                }
            } else {
              throw new \Exception("API Key Invalido o no se encuentra activo");
            }
          }
        } catch (\InvalidArgumentException $e) {
          /* Atrapa la excepciÃƒÂ³n y continua el proceso */
          $this->logger->log($e . " ");
        } catch (\Exception $e) {
          $this->response->setContentType('application/json', 'UTF-8');
          $this->response->setStatusCode(400, $e->getMessage());
          return false;
        }

        //$timer->endTimer('Authentication');
      }

      $map = $this->getControllerMap();

      $this->publicurls = array(
          /* Error views */
          'error::index',
          'error::notavailable',
          'error::unauthorized',
          'error::forbidden',
          'error::link',
          /* Session */
          'session::index',
          'session::login',
          'session::loginpass',
          'plantillas::default',
          'session::logout',
          'session::recoverpass',
          'session::resetpassword',
          'session::setnewpass',
          'apisession::login',
          'apisession::loginpass',
          'apisession::loginwithfacebook',
          'apisession::verifystatususer',
          'apisession::recoverpassgenerate',
          'session::logoutsuperuser',
          'track::click',
          'track::open',
          'track::openautomatization',
          'track::clickautomatization',
          'webversion::show',
          'apiunsubscribe::getcontact',
          'apiunsubscribe::insunsubscribe',
          'apiunsubscribe::insunsubscribeautomatic',
          'apiunsubscribe::insunsubscribesimple',
          'apiunsubscribe::getcontactsunsubscribe',
          'apiunsubscribe::deleteunsub',
          'apiunsubscribe::getcategories',
          'apiunsubscribe::createcontactunsub',
          'unsubscribe::contact',
          'unsubscribe::list',
          'unsubscribe::create',
          'unsubscribe::new',
          'unsubscribe::contactautomatic',
          'track::mtaevent',
          'track::mtaexample',
          'thumbnail::mailtemplateshow',
          'thumbnail::mailshow',
          'statistic::share',
          'statistic::smsshare',
          'apistatics::getallinfomail',
          'apistatics::infoopen',
          'apistatics::datainfo',
          'apistatics::getinfosms',
          'apistatics::getdetailsms',
          'apicontact::addcontactform',
          'country::country',
          'country::state',
          'country::cities',
          /* Register */
          'register::index',
          'register::signup',
          'register::paymentplan',
          'register::paymentplandetail',
          'register::payment',
          'register::pay',
          'register::validatemail',
          'register::congratulations',
          'register::welcome',
          'register::completeprofile',
          'apiregister::create',
          'apiregister::listpaymentplans',
          'apiregister::detailpaymentplan',
          'apiregister::verifyaccount',
          'apiregister::assignpaymentplan',
          'apiregister::getappidfacebook',
          'apiregister::createwithfacebook',
          'apiregister::completeprofileuser',
          'apiforms::getcontentform',
          'forms::structureform',
          /* DOBLE OPTIN */
          'apicontact::dobleoptin',
          'survey::showsurvey',
          'apisurvey::getsurveycontent',
          'apisurvey::saveanswer',
          'survey::congratulations',
          /* ASSET */
          'asset::thumbnailmail',
          /* MAIL TESTER */
          'mailtester::show',
          'testsurvey::show',
          /* APISMSTWOWAY */
          'apismstwoway::receivedsms',
          'apismstwoway::getallsmstwoway',
          'apismstwoway::editsmslotetwowaysend',
          /* SMSTWOWAY */
          'smstwoway::toolstwoway',
          'smstwoway::create',
          'smstwoway::list',
          'smstwoway::speedsent',
          'smstwoway::createcsv',
          'smstwoway::editspeedsent',
          'smstwoway::index',
          'smstwoway::showlotetwoway',
          /* Public LandingPages */
          'publiclandingpage::publication',
          'publiclandingpage::errors',
          'landingpage::preview',
          'landingpagetemplate::preview',
          /*Api statics mail share*/
          'apistatics::infoclic',
          'apistatics::infounsuscribed',
          'apistatics::infobounced',
          'report::downloadsmxemail',
          'downloadsms::download',
          'apisms::find',
          /* Api Report Mail*/
          'apireport::reportmail',
          'track::eventprocessors',
          'track::testevent',
          'apimail::loadpdf',
          'mail::compose',
          'mail::structurename',
          'error::maintenance',
          /* Api Voice-messages */
          'apivoicemessages::createlote',
          'subscribe::form',
          /* Register */
          'register::api',
          /* Paymentplan */
          'paymentplan::api',
          'paymentplan::prices',
          /* Register */
          'register::continueregister',
          /* WhatsApp */
          'whatsapp::getreceiver',
          'whatsapp::index',
          'whatsapp::list',
          /* Api Whatsapp */
          'apiwhatsapp::getallwhatsapp',
          /*Contact*/
          'contact::downloadlc',
      );

      if ($role == 'GUEST') {
        if ($resource == "error::notavailable") {
          $this->response->redirect("index");
          return false;
        } else if (!in_array($resource, $this->publicurls)) {
          $this->response->redirect("session");
          return false;
        }
      } else {
        $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
        $user = Usertype::findFirstByIdUsertype($user->idUsertype);
//        var_dump($user->User->Role->idRole);
//        exit();
        if ($user->User[0]->idRole != $this->roles->root) {
          if (isset($user->idAccount)) {
            $fields = "  Account.status ";
            $condition = " Account.idAccount = :idAccount:  ";
          }
          if (isset($user->idMasteraccount)) {
            $fields = "  Masteraccount.status ";
            $condition = " Masteraccount.idMasteraccount = :idMasteraccount:  ";
          }
          if (isset($user->idAllied)) {
            $fields = "  Allied.status ";
            $condition = " Allied.idAllied = :idAllied:  ";
          }
          if (isset($user->idSubaccount)) {
            $fields = "  Subaccount.status ";
            $condition = " Subaccount.idSubaccount = :idSubaccount:  ";
          }
          $sql = "SELECT {$fields } FROM Usertype LEFT JOIN Account ON Usertype.idAccount = Account.idAccount"
                  . " LEFT JOIN user ON Usertype.idUsertype = user.idUsertype "
                  . "LEFT JOIN Masteraccount ON Masteraccount.idMasteraccount = Usertype.idMasteraccount "
                  . " LEFT JOIN Subaccount ON Subaccount.idSubaccount = Usertype.idSubaccount "
                  . "LEFT JOIN Allied ON Usertype.idAllied = Allied.idAllied WHERE {$condition}  LIMIT 1";
          if (isset($user->idAccount)) {
            $result = $modelManager->executeQuery($sql, array('idAccount' => $user->idAccount));
          }
          if (isset($user->idMasteraccount)) {
            $result = $modelManager->executeQuery($sql, array('idMasteraccount' => $user->idMasteraccount));
          }
          if (isset($user->idAllied)) {
            $result = $modelManager->executeQuery($sql, array('idAllied' => $user->idAllied));
          }
          if (isset($user->idSubaccount)) {
            $result = $modelManager->executeQuery($sql, array('idSubaccount' => $user->idSubaccount));
          }
        }
//                $sql = "SELECT account.status FROM Usertype JOIN account ON Usertype.idAccount = account.idAccount"
//                    . " LEFT JOIN user ON Usertype.idUsertype = user.idUsertype "
//                    . "LEFT JOIN masteraccount ON masteraccount.idMasteraccount = Usertype.idMasteraccount "
//                    . "LEFT JOIN Allied ON Usertype.idAllied = Allied.idAllied WHERE account.idAccount = :idAccount:  LIMIT 1";
//        $idAccount = Usertype::findFirstByIdUsertype($user->idUsertype);
//        $result = $modelManager->executeQuery($sql, array('idAccount' => $idAccount[0]->idAccount));
//        $account = $result[0];
//        $account = Account::findFirstByIdAccount($user->idAccount);
        if ($resource == 'session::index') {
          $this->response->redirect("index");
          return false;
        } else if ($user->User[0]->idRole != $this->roles->root && $result[0]->status == 0 && $user->User[0]->idRole != $this->roles->root && $resource !== 'error::unauthorized') {
          $this->response->redirect("error/unauthorized");
          return false;
        } else {
          $acl = $this->getAcl();
          //$this->logger->log("Validando el usuario con rol [$role] en [$resource]");

          if (!isset($map[$resource]) && $action != 'detailsmslote') {
            if ($this->validateResponse($controller) == true) {
              $this->logger->log("El recurso no se encuentra registrado en el sistema de API's");
              $this->setJsonResponse(array('status' => 'deny'), 403, 'Accion no permitida');
            } else {
              $this->logger->log("El recurso no se encuentra registrado-".$action."--".$resource."--".$controller);
              $dispatcher->forward(array('controller' => 'error', 'action' => 'index'));
            }
            return false;
          }

          $reg = $map[$resource];
          //    var_dump($user->User);exit();
          if ($user->User[0]->idRole != $this->roles->root) {
            foreach ($reg as $resources => $actions) {
              foreach ($actions as $act) {
//                var_dump($role);
//                exit();
                if (!$acl->isAllowed($role, $resources, $act)) {
                  if ($this->validateResponse($controller) == true) {
                    $this->logger->log("Acceso al sistema de API's denegado");
                    $this->setJsonResponse(array('status' => 'deny'), 403, 'Accion no permitida');
                  } else {
                    $this->logger->log('Acceso denegado');
                    $dispatcher->forward(array('controller' => 'error', 'action' => 'forbidden'));
                  }
                  return false;
                }
              }
            }
          }

          $mapForLoginLikeAnyUser = array('session::superuser');
          if (in_array($resource, $mapForLoginLikeAnyUser)) {
            $this->session->set('userEfective', $user);
          }
          //var_dump($mapForLoginLikeAnyUser);
//          exit();
          return true;
        }
      }
    }
  }

}
