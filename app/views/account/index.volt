{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}    

{% block js %}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
    {{ javascript_include('library/select2/js/select2.min.js') }}
  {# Dialogs #}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ javascript_include('js/angular/account/controller.js') }}
  {# {{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }} #}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>

  <script type="text/javascript">
    $(function () {
      $('#details').tooltip();
    });
    var urlsearch = '{{url('account/search')}}"}}';
    var urluserlist = '{{url('account/userlist')}}"}}';
    var urlaccountedit = '{{url('account/edit')}}"}}';
    $(document).on("click", "#delete", function () {
      var myURL = $(this).data('id');
      $("#btn-ok").attr('href', myURL);
    });
    function openModal() {
      $('.dialog').addClass('dialog--open');
    }

    function closeModal() {
      $('.dialog').removeClass('dialog--open');
    }

    var idAllied = "";

    $(function () {
        $('#toggle-one').bootstrapToggle({
            on: 'On',
            off: 'Off',
            onstyle: 'success',
            offstyle: 'danger',
            size: 'small'
        });
        $(".select2").select2({
            theme: 'classic',
            placeholder : 'Seleccionar'
        });
    });

    {% if limitContactAllied is defined %}
    var contactTotalAllied = {{ limitContactAllied }};
    {% else %}
    var contactTotalAllied = 0;
    {% endif %}

    {% if  limitSmsAllied is defined %}
    var smsTotalAllied = {{ limitSmsAllied }};
    {% else %}
    var smsTotalAllied = 0;
    {% endif %}
      
    {% if  limitSmstwowayAllied is defined %}
    var smstwowayTotalAllied = {{ limitSmstwowayAllied }};
    {% else %}
    var smstwowayTotalAllied = 0;
    {% endif %}
      
    {% if  limitLandingpageAllied is defined %}
    var landingpageTotalAllied = {{ limitLandingpageAllied }};
    {% else %}
    var landingpageTotalAllied = 0;
    {% endif %}

    {% if  accountingModeAllied is defined %}
    var accountingModeAllied = '{{ accountingModeAllied }}';
    {% else %}
    var accountingModeAllied = '';
    {% endif %}

  </script>
    {{ javascript_include('js/search/search-account.js') }}
  {% endblock %}
  {% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     
  <div ng-app="aio" ng-cloak ng-controller="ctrlAccountlist">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Lista de Cuentas
        </div>            
        <hr class="basic-line" />
        <p>
          En esta lista podra ver, editar y eliminar las cuentas de nuestros clientes. 
        </p>            
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-offset-4 col-md-4 col-lg-offset-4 col-lg-4 wrap text-right">
        <input ng-model="name" ng-change="restartPagination();getAll(name)" class="undeline-input" id="name" placeholder="Buscar por nombre" />
      </div>
      <div class="col-lg-4 wrap text-right" style="padding-bottom: 0;">
        <select class="form-control " data-toggle="tooltip" data-placement="bottom" title="Buscar por estado de cuenta" name="status" ng-model="status" ng-change='restartPagination();statusFunc()' style="width: auto;display: unset;padding: 6px 7px;">
          <option value="" selected disabled>Estado de la cuenta</option>
          <option value="todosEst">Todos los estados</option>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
        <select class="form-control " data-toggle="tooltip" data-placement="bottom" title="Buscar por registro de cuenta" name="accountRegisterType" ng-model="accountRegisterType" ng-change='restartPagination();typeFunc()' style="width: auto;display: unset;padding: 6px 7px;">
          <option value="" selected disabled>Buscar por origen de la cuenta</option>
          <option value="todosOrg">Todos los origenes</option>
          <option value="aio">AIO</option>
          <option value="facebook">Facebook</option>
          <option value="form">Formulario Gratuito</option>
          <option value="google">Google</option>
          <option value="online">Tienda</option>
        </select>
      </div>
    </div>
    <div ng-if="accounts.items.length != 0">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
          <a ng-click="downloadReport()" class="button shining btn btn-sm info-inverted">Descargar Listado de Cuentas</a>           
          <a href="{{url('account/create')}}" class="button shining btn btn-sm success-inverted">Crear una nueva cuenta</a>
        </div>
      </div>

      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{accounts.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (accounts.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (accounts.total_pages)  || accounts.total_pages == 0  ? 'disabled'  : ''">
            <a href="" ng-click="page == (accounts.total_pages)  || accounts.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (accounts.total_pages)  || accounts.total_pages == 0  ? 'disabled'  : ''">
            <a ng-click="page == (accounts.total_pages)  || accounts.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <table class="table table-bordered table-responsive" id="resultTable">                
            <thead class="theader">
              <tr>
                {#              <th></th>            #}
                <th>Detalles</th>
                <th>Ubicacion</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              
              <tr ng-repeat="account in accounts.items" ng-class="account.status == 0 ? 'deactivate' : ''">
                <td>
                  <font class="strong-text ng-binding medium-text">
                  {{"{{account.name}}"}}
                  </font>
                  <dl>
                    <dd ng-if="account.registerType === 'form'"><strong>Origen: </strong><span style="color: #ff6e00;font-style: italic;">Formulario Gratuito</span></dd>
                    <dd ng-if="account.registerType === 'online'"><strong>Origen: </strong><span style="color: #ff6e00;font-style: italic;">Tienda</span></dd>
                    <dd ng-if="account.registerType === 'aio'"><strong>Origen: </strong><span style="color: #ff6e00;font-style: italic;">AIO</span></dd>
                    <dd ng-if="account.registerType === 'facebook'"><strong>Origen: </strong><span style="color: #ff6e00;font-style: italic;">Facebook</span></dd>
                    <dd ng-if="account.registerType === 'google'"><strong>Origen: </strong><span style="color: #ff6e00;font-style: italic;">Google</span></dd>
                    <dd>Nit: {{"{{account.nit}}"}}</dd>
                    <dd class="extra-small-text"> Telefono: {{"{{account.phone}}"}} </dd>
                    <dd> <em class="extra-small-text">Creado por <strong>{{"{{account.createdBy}}"}}</strong> , el <strong ng-bind="account.created | date:'dd/MM/yyyy h:ma'"></strong> </em></dd>
                    <dd> <em class="extra-small-text">Actualizado por <strong> {{"{{account.updatedBy}}"}}</strong>, el  <strong ng-bind="account.updated | date:'dd/MM/yyyy h:ma'"></strong></em></dd>
                  </dl>
                </td>
                <td>
                  Direccion:   {{"{{account.address}}"}}
            <di>
              {#                <dd><em>{{"{{account.city.state.country.name}}"}}, {{"{{account.city.state.name}}"}}, {{"{{account.city.name}}"}} </em></dd>#}
            </di>
            </td>
            <td class="user-actions text-right">
              {#      <a class="button shining btn btn-xs-round   round-button default-inverted" data-toggle="collapse" href="#collapseDetails{{"{{account.idAccount}}"}}" aria-expanded="false" aria-controls="collapseDetails" id="details" data-placement="top" title="Ver detalles">
                      <span class="glyphicon glyphicon-collapse-down"></span>
                    </a>#}
              <a href="{{url('dashboardconfig')}}#/{{"{{account.idAccount}}"}}" class="button btn btn-xs-round primary-inverted" data-toggle="tooltip" data-placement="top" title="Configurar Dashboard">
               <i class="fa fa-cogs" aria-hidden="true"></i>
                <md-tooltip md-direction="bottom">
                  Configurar Dashboard
                </md-tooltip>
              </a>  
              <a href="{{url('account/show')}}/{{"{{account.idAccount}}"}}" class="button btn btn-xs-round default-inverted" data-toggle="tooltip" data-placement="top" title="Informacion completa de la cuenta maestra">
                <span class="glyphicon glyphicon-eye-open"></span>
                <md-tooltip md-direction="bottom">
                  Informacion completa de la cuenta
                </md-tooltip>
              </a>
              <button class="button  btn btn-xs-round   round-button warning-inverted" data-toggle="tooltip" data-placement="top" title="Lista de plantillas de cuenta" ng-click="listMailTemplateAccount(account.idAccount)">
                <i class="fa fa-wpforms" aria-hidden="true"></i>
                <md-tooltip md-direction="bottom">
                  Lista de plantillas de cuenta
                </md-tooltip>
              </button>
              <button class="button  btn btn-xs-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Recargar servicio" ng-click="rechargeService(account.idAccount)">
                <i class="fa fa-plus-square" aria-hidden="true"></i>
                <md-tooltip md-direction="bottom">
                  Recargar servicio
                </md-tooltip>
              </button>
              <a href="{{url('account/userlist')}}/{{"{{account.idAccount}}"}}" class="button btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Lista de Usuarios">
                <span class="glyphicon glyphicon-user"></span>
                <md-tooltip md-direction="bottom">
                  Lista de Usuarios
                </md-tooltip>
              </a>
              {# <a href="{{url('account/accountconfigedit')}}"}}/{{"{{account.idAccountclassification}}"}}" class="button  btn btn-xs-round   round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar configuración">
                 <span class="glyphicon glyphicon-cog"></span>
               </a>#}
              <a href="{{url('account/edit')}}/{{"{{account.idAccount}}"}}" class="button shining btn btn-xs-round   round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta Cuenta">
                <span class="glyphicon glyphicon-pencil"></span>
                <md-tooltip md-direction="bottom">
                  Editar esta Cuenta
                </md-tooltip>
              </a>
            </td>
            </tr>

            <tr class="collapse" id="collapseDetails{{"{{account.idAccount}}"}}" >
              <td colspan="8">
                <table id="collapse" class="table table-bordered" style="width: 45%;" align="center">
                  <tbody>
                    {#<tr>
                        <td>
                            <strong>Remitentes:</strong>
                        </td>
                        <td >
                            {% if sr|length == 0%}
                                Esta cuenta no tiene remitentes registrados
                            {% else %}
                                {% for s in sr %}
                                    {{s.name}}"}} / {{s.email}}"}} <br />
                                {% endfor %}    
                            {% endif %}
                        </td>
                    </tr>#}
                    <tr>
                      <td>
                        <strong>Creado:</strong>
                      </td>
                      <td >
                        {#                          {{date('d/m/Y g:i a', item.created)}}#}
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <strong>Actualizado:</strong>
                      </td>
                      <td>
                        {#                          {{date('d/m/Y g:i a', item.updated)}}#}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </table>            
        </div>    
      </div>

      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{accounts.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (accounts.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (accounts.total_pages)  || accounts.total_pages == 0  ? 'disabled'  : ''">
            <a href="" ng-click="page == (accounts.total_pages)  || accounts.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (accounts.total_pages)  || accounts.total_pages == 0  ? 'disabled'  : ''">
            <a ng-click="page == (accounts.total_pages)  || accounts.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>

    </div>
    <div ng-if="accounts.items.length == 0">
      <div class="row" ng-if="!configAllied">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-success">
            <div class="body success-no-hover text-center">
              <h2>
                Tu cuenta no se encuentra configurarada comunícate con soporte.
              </h2>    
            </div>
          </div>
        </div>
      </div>
      <div class="row" ng-if="configAllied">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-success">
            <div class="body success-no-hover text-center">
              <h2>
                No existen cuentas creadas actualmente, si desea crear una haga <a href="{{url('account/create')}}">clic aquí</a>.
              </h2>    
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="somedialog" class="dialog">
      <div class="dialog__overlay"></div>
      <div class="dialog__content">
        <div class="morph-shape">
          <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
          <rect x="3" y="3" fill="none" width="556" height="276"/>
          </svg>
        </div>
        <div class="dialog-inner">
          <h2>¿Esta seguro?</h2>
          <div style="z-index: 999999;">           
            <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
          <div class="modal-body">
            <div class="body row">
              <div ng-show="list.items.length > 0">
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" data-ng-repeat="i in list.items">
                  <div class="thumbnail">
                    <img src="{{url('')}}{{'{{i.urlThumbnail}}'}}" />
                    <div class="caption text-center">
                      <div><h4><b>{{'{{i.name}}'}}</b></h4>
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="{{url('mailtemplate/')}}edit/{{'{{i.idMailTemplate}}'}}" class="btn info-inverted" data-toggle="tooltip" data-placement="bottom" title="Editar"><i class="fa fa-pencil"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="pagination" class="text-center" ng-show="list.items.length > 0">
              <ul class="pagination">
                <li ng-class="page == 1 ? 'disabled'  : ''">
                  <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li  ng-class="page == 1 ? 'disabled'  : ''">
                  <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                  <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
                    </b> registros </span><span>Página <b>{{"{{ page }}"}}
                    </b> de <b>
                      {{'{{list.total_pages}}'}}
                    </b></span>
                </li>
                <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                  <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                  <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
              </ul>
            </div>

            <div ng-show="list.items.length == 0">
              <h2>
                Esta cuenta no tiene plantillas creadas actualmente, para crear una haga <a href="{{url('mailtemplate#/create')}}">clic aquí</a>.
              </h2>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="danger-inverted" data-dismiss="modal" ng-click="restartPagination()"><i class="fa fa-times"></i> Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalRecharge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
          <form data-ng-submit="rechargeApply()">
          <div class="modal-body">
            <div class="body row">

              <div class="">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-3 ">*Servicios:</label>
                  <span class="input hoshi input-default col-sm-9">
                      <select class="undeline-input select2" multiple="multiple" ng-model="services" name="services[]"
                              id="services[]" ng-change="selectedServices()" required>
                        <option data-ng-repeat="service in result.services" value="{{ '{{ service.idServices }}' }}" >{{ '{{ service.name }}'}}</option>
                      </select>
                    </span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showsms">
                <label class="col-sm-3 col-md-3 ">*Limite de Mensajes de Texto:</label>
                <div class="col-sm-9 col-md-9">
                  <md-slider-container>
                    <md-slider flex min="1" class="md-warn" max="{{ '{{ smsTotalAllied }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                    </md-slider>
                    <md-input-container>
                      <input flex type="number" min="1" max="{{ '{{ smsTotalAllied }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">
                    </md-input-container>
                    <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                           ng-model="spaceTotal">/{{ '{{ var = (smsTotalAllied - smsLimit) }}' }} Mensajes
                    </label>
                  </md-slider-container>
                </div>
              </div>
                    
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showsmstwoway">
                <label class="col-sm-3 col-md-3 ">*Limite de Mensajes de Texto doble via:</label>
                <div class="col-sm-9 col-md-9">
                  <md-slider-container>
                    <md-slider flex min="1" class="md-warn" max="{{ '{{ smstwowayTotalAllied }}' }}" ng-model="smstwowayLimit" aria-label="red" id="red-slider">
                    </md-slider>
                    <md-input-container>
                      <input flex type="number" min="1" max="{{ '{{ smstwowayTotalAllied }}' }}" ng-model="smstwowayLimit" aria-label="red" aria-controls="red-slider">
                    </md-input-container>
                    <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                           ng-model="spaceTotal">/{{ '{{ var = (smstwowayTotalAllied - smstwowayLimit) }}' }} Mensajes
                    </label>
                  </md-slider-container>
                </div>
              </div>
                    
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showlandingpage">
                <label class="col-sm-3 col-md-3 ">*Limite de visualizaciones Landing Page:</label>
                <div class="col-sm-9 col-md-9">
                  <md-slider-container>
                    <md-slider flex min="1" class="md-warn" max="{{ '{{ landingpageTotalAllied }}' }}" ng-model="landingpageLimit" aria-label="red" id="red-slider">
                    </md-slider>
                    <md-input-container>
                      <input flex type="number" min="1" max="{{ '{{ landingpageTotalAllied }}' }}" ng-model="landingpageLimit" aria-label="red" aria-controls="red-slider">
                    </md-input-container>
                    <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                           ng-model="spaceTotal">/{{ '{{ var = (landingpageTotalAllied - landingpageLimit) }}' }} Mensajes
                    </label>
                  </md-slider-container>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showemail ">
                <label class="col-sm-3 col-md-3">*Limite de Correos:</label>
                <div class="col-sm-9 col-md-9">
                <md-slider-container>
                  <md-slider flex min="1" max="{{ '{{ contactTotalAllied }}' }}" ng-model="mailLimit" aria-label="red" id="red-slider">
                  </md-slider>
                  <md-input-container>
                    <input flex type="number" min="1" max="{{ '{{ contactTotalAllied }}' }}" ng-model="mailLimit" aria-label="red" aria-controls="red-slider">
                  </md-input-container>
                  <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                         ng-model="spaceTotal">/{{ '{{ var = (contactTotalAllied - mailLimit) }}' }} Mensajes
                  </label>
                </md-slider-container>
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <a class="button shining btn btn-sm danger-inverted" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
            <button type="submit" class="button shining btn btn-sm success-inverted none-margin"><i class="fa fa-check"></i> Recargar</button>
          </div>
          </form>
        </div>
      </div>
    </div>

  </div>
{% endblock %}
{% block footer %}
  <script type="text/javascript">
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var templateBase = "account";
  </script>
{% endblock %}
