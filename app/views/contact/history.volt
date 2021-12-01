{% extends "templates/default.volt" %}
{% block css %}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">

  {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{#  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">#}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}
  <style type="text/css" >

    #menu {
      float:left;
      {#      left:50%;#}
      list-style-type:none;
      margin:0 auto;
      padding:0;
      position:relative;
    }

    #menu li {
      float:left;
      position:relative;
      right:50%;
    }
  </style>
  {#  {{ stylesheet_link('library/ngProgress-master/ngProgress.css') }}#}
{% endblock %}

{% block js %}

  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  
  <!-- datetimepicker -->
  {{ javascript_include('library/moment/min/moment.min.js') }}
  {{ javascript_include('library/moment/locale/es.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.min.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.templates.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {# {{ javascript_include('js/angular/contact/app.js') }}
  {{ javascript_include('js/angular/contact/controllers.js') }}
  {{ javascript_include('js/angular/contact/services.js') }}
  {{ javascript_include('js/angular/contact/directives.js') }} #}
  {{ javascript_include('js/angular/contact/dist/contact.d311b2c7b96f67c60f22.min.js') }}
  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}
  {#  {{ javascript_include('library/ngProgress-master/build/ngProgress.js') }}#}
  {{ javascript_include('js/checklist-model.js') }}

  <!-- Angular Material Dependencies -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.tpl.min.js') }}
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>


{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div ng-controller="HistoryController" ng-cloak>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Historial de envios del contacto <strong>{{"{{contact.name}}"}} {{"{{contact.lastname}}"}}</strong>
        </div>
        <hr class="basic-line" />
      </div>
    </div>
    <div ng-cloak class="wrap">
      <md-content>
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="Envíos de SMS">
            <md-content class="md-padding row">
              <div class ="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                  <div class="input-group">
                    <span class=" input-group-addon cursor" id="basic-addon1" placement="top" data-toggle="popover" title="" data-content="Puede filtrar las listas de contacto por su nombre completo o por una parte del nombre." data-original-title="Instrucciones">
                      <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </span>
                    <input class="form-control ng-pristine ng-untouched ng-valid ng-empty" id="nameSms" placeholder="Buscar por nombre" ng-keyup="getAllSMS()" ng-model="nameSMS" aria-invalid="false"/>
                  </div>
                </div>
              </div>
              <div class="row wrap" ng-show="sms[0].items.length > 0">
                <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                  <ul class="pagination">
                    <li ng-class="pageSMS == 1 ? 'disabled'  : ''">
                      <a  href="#/" ng-click="pageSMS == 1 ? true  : false || fastbackwardSMS()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="pageSMS == 1 ? 'disabled'  : ''">
                      <a href="#/"  ng-click="pageSMS == 1 ? true  : false || backwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{sms.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ pageSMS }}"}}
                        </b> de <b>
                          {{ "{{ (sms.total_pages ) }}"}}
                        </b></span>
                    </li>
                    <li   ng-class="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? 'disabled'  : ''">
                      <a href="#/" ng-click="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? true  : false || forwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? 'disabled'  : ''">
                      <a ng-click="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? true  : false || fastforwardSMS()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="row wrap" ng-show="sms[0].items.length > 0">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding: 0px;">
                  <table class="table table-bordered table-responsive" id="resultTable">
                    <thead class="theader">
                      <tr>
                        <th>Información</th>
                        <th>Mensaje</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr ng-repeat="sms in sms[0].items">
                        <td>
                          <strong class=" ng-binding medium-text ">
                            {{ '{{sms.smsName}}' }}
                          </strong>
                    <dd class="small-text">Enviado a: <b>(+{{ '{{sms.indicative}}' }}){{ '{{sms.phone}}' }}</b>  </dd>
		    <dd class="small-text">Estado:<b><span ng-if="sms.response=='PENDING_ENROUTE'">Recibido</span><span ng-if="sms.response!='PENDING_ENROUTE'">No recibido</span></b>  </dd>
                    <!-- dd class="small-text">Estado: <b><span ng-if="sms.response=='0: Accepted for delivery'">Recibido</span><span ng-if="sms.response!='0: Accepted for delivery'">No recibido</span></b>  </dd-->
                    <dd> <em class="extra-small-text">Fecha de envío: <strong>{{ '{{sms.scheduleDate}}' }}</strong></em></dd>
                    </td>
                    <td>
                    <dd class="small-text">{{ '{{sms.message}}' }}</dd>
                    </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row wrap" ng-show="sms[0].items.length > 0">
                <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                  <ul class="pagination">
                    <li ng-class="pageSMS == 1 ? 'disabled'  : ''">
                      <a  href="#/" ng-click="pageSMS == 1 ? true  : false || fastbackwardSMS()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="pageSMS == 1 ? 'disabled'  : ''">
                      <a href="#/"  ng-click="pageSMS == 1 ? true  : false || backwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{sms.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ pageSMS }}"}}
                        </b> de <b>
                          {{ "{{ (sms.total_pages ) }}"}}
                        </b></span>
                    </li>
                    <li   ng-class="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? 'disabled'  : ''">
                      <a href="#/" ng-click="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? true  : false || forwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? 'disabled'  : ''">
                      <a ng-click="pageSMS == (sms.total_pages)  || sms.total_pages == 0  ? true  : false || fastforwardSMS()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="row wrap" ng-show="sms[0].items.length == 0">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-success">
                    <div class="body success-no-hover text-center">
                      <h2>
                        No se encontraron envíos de SMS.
                      </h2>    
                    </div>
                  </div>
                </div>
              </div>
            </md-content>
          </md-tab>
          <md-tab label="Envíos de correo">
            <md-content class="md-padding row">
              <div class ="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                  <div class="input-group">
                    <span class=" input-group-addon cursor" id="basic-addon1" placement="top" data-toggle="popover" title="" data-content="Puede filtrar las listas de contacto por su nombre completo o por una parte del nombre." data-original-title="Instrucciones">
                      <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </span>
                    <input class="form-control ng-pristine ng-untouched ng-valid ng-empty" id="nameSms" placeholder="Buscar por nombre" ng-keyup="getAllMAIL()" ng-model="nameMAIL" aria-invalid="false"/>
                  </div>
                </div>
              </div>
              <div class="row wrap" ng-show="mail[0].items.length > 0">
                <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                  <ul class="pagination">
                    <li ng-class="pageMAIL == 1 ? 'disabled'  : ''">
                      <a  href="#/" ng-click="pageMAIL == 1 ? true  : false || fastbackwardMAIL()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="pageMAIL == 1 ? 'disabled'  : ''">
                      <a href="#/"  ng-click="pageMAIL == 1 ? true  : false || backwardMAIL()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mail.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ pageMAIL }}"}}
                        </b> de <b>
                          {{ "{{ (mail.total_pages ) }}"}}
                        </b></span>
                    </li>
                    <li   ng-class="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? 'disabled'  : ''">
                      <a href="#/" ng-click="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? true  : false || forwardMAIL()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? 'disabled'  : ''">
                      <a ng-click="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? true  : false || fastforwardMAIL()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="row wrap" ng-show="mail[0].items.length > 0">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding: 0px;">
                  <table class="table table-bordered table-responsive" id="resultTable">
                    <thead class="theader">
                      <tr>
                        <th>Información</th>
                        <th>Contenido</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr ng-repeat="mail in mail[0].items">
                        <td>
                          <strong class=" ng-binding medium-text ">
                            {{ '{{mail.mailName}}' }}
                          </strong>
                    <dd class="small-text">Enviado a: <b>{{ '{{mail.email}}' }}</b>  </dd>
                    <dd class="small-text">Estado: <b>{{ '{{mail.response}}' }}</b>  </dd>
                    <dd> <em class="extra-small-text">Fecha de envío: <strong>{{ '{{mail.scheduleDate}}' }}</strong></em></dd>
                    </td>
                    <td >
                      <a class="cursor-pointer" ng-click="previewmailtempcont(mail.idMail);" data-toggle="modal" data-target="#myModal">Ver el contenido del correo</a>
                    </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row wrap" ng-show="mail[0].items.length > 0">
                <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                  <ul class="pagination">
                    <li ng-class="pageMAIL == 1 ? 'disabled'  : ''">
                      <a  href="#/" ng-click="pageMAIL == 1 ? true  : false || fastbackwardMAIL()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="pageMAIL == 1 ? 'disabled'  : ''">
                      <a href="#/"  ng-click="pageMAIL == 1 ? true  : false || backwardMAIL()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mail.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ pageMAIL }}"}}
                        </b> de <b>
                          {{ "{{ (mail.total_pages ) }}"}}
                        </b></span>
                    </li>
                    <li   ng-class="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? 'disabled'  : ''">
                      <a href="#/" ng-click="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? true  : false || forwardMAIL()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? 'disabled'  : ''">
                      <a ng-click="pageMAIL == (mail.total_pages)  || mail.total_pages == 0  ? true  : false || fastforwardMAIL()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="row wrap" ng-show="mail[0].items.length == 0" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-success">
                    <div class="body success-no-hover text-center">
                      <h2>
                        No se encontraron envíos de correo.
                      </h2>    
                    </div>
                  </div>
                </div>
              </div>
            </md-content>
          </md-tab>

        </md-tabs>
      </md-content>
    </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-prevew-width">
      <div class="modal-content modal-prevew-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h1 class="modal-title" id="myModalLabel">Contenido del correo</h1>
        </div>
        <div class="modal-body modal-prevew-body" id="content-preview" style="height: 550px;">

        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
        </div>
      </div>
    </div>
  </div>


{% endblock %}
{% block footer %}
  {#  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}#}

  <script type="text/javascript">
var host = "{{ url('') }}";
var relativeUrlBase = "{{urlManager.get_base_uri()}}";
var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
var templateBase = "contact";
var idContactlist = "{{ idContactlist }}";
var idContact = "{{idContact}}";
  </script>

  {#    {{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}
{% endblock %}
