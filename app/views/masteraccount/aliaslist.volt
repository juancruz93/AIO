{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Select 2 #}
  {{ javascript_include('library/select2/js/select2.min.js') }}

  {{ javascript_include('js/angular/allied/controller.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>

  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <script>
    $('#collapse').collapse({
      toggle: false
    })

    $(function () {
      $(".select2").select2({
        theme: 'classic',
        placeholder: 'Seleccionar'
      });
    });

    {% if limitContactMaster is defined %}
      var contactTotalMaster = {{ limitContactMaster }};
    {% else %}
      var contactTotalMaster = 0;
    {% endif %}

    {% if  limitSmsMaster is defined %}
      var smsTotalMaster = {{ limitSmsMaster }};
    {% else %}
      var smsTotalMaster = 0;
    {% endif %}

    {% if  limitSmstwowayMaster is defined %}
      var smstwowayTotalMaster = {{ limitSmstwowayMaster }};
    {% else %}
      var smstwowayTotalMaster = 0;
    {% endif %}

    {% if  limitLandingpageMaster is defined %}
      var landingpageTotalMaster = {{ limitLandingpageMaster }};
    {% else %}
      var landingpageTotalMaster = 0;
    {% endif %}

    {% if  accountingModeMaster is defined %}
      var accountingModeMaster = '{{ accountingModeMaster }}';
    {% else %}
      var accountingModeMaster = '';
    {% endif %}

  </script>
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}

{% endblock %}

{% block content %}
  <div ng-app="aio" ng-controller="ctrlAlliedList" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>     

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Lista de Cuentas Aliadas
        </div>            
        <hr class="basic-line" />
        <p>
          En esta lista podra ver, crear, editar y eliminar las cuentas aliadas de una cuenta maestra.
        </p>            
      </div>
    </div>

    {% if page.items|length != 0 %}
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">       
          <a href="{{url('masteraccount/index')}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas Maestras</a>
          <a href="{{url('masteraccount/aliascreate')}}/{{idMasteraccount}}" class="button shining btn btn-sm success-inverted">Crear una nueva cuenta aliada</a>
          {{ partial('partials/pagination_static_partial', ['pagination_url': 'masteraccount/aliaslist/'~idMasteraccount]) }}
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
          <table class="table table-bordered sticky-enabled ">                
            <thead class="theader">
              <tr>
                <th></th>
                <th>Detalles</th>
                <th>Servicios</th>
                <th>Ubicación</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {% for item in page.items %}
                <tr {% if item.status == 0 %} class="account-disabled" {% endif %}>
                  <td>{{item.idAllied}}</td>
                  <td>
                    <font class="strong-text ng-binding medium-text" >
                    {{item.name}}
                    </font>
                    <dl>
                      <dd>Nit: {{item.nit}}</dd>
                      <dd class="extra-small-text"> Telefono: {{item.phone}}</dd>
                      <dd> <em class="extra-small-text">Creado por <strong>{{(item.createdBy)}}</strong> , a las <strong>{{date('d/m/Y g:i a', item.created)}}</strong> </em></dd>
                      <dd> <em class="extra-small-text">Actualizado por <strong> {{(item.updatedBy)}}</strong>, a las  <strong>{{date('d/m/Y g:i a', item.updated)}}</strong></em></dd>
                    </dl>
                  </td>
                  <td>
                    {% if item.Alliedconfig.DetailConfig is defined %}
                      {% for detail in item.Alliedconfig.DetailConfig %}
                        &raquo; <em>{{detail.Services.name}}</em><br/>
                      {% endfor %}
                    {% endif %}
                  </td>
                  <td>
                    Direccion: {{item.address}}
              <di>
                <dd><em>{{item.city.state.country.name}}, {{item.city.state.name}}, {{item.city.name}} </em></dd>
                <dd>Correo: {{item.email}}</dd>
                <dd>Zip-code: {{item.zipcode}}</dd>
              </di>
              </td>
              <td class="user-actions text-right">
                {#              <a class="button btn btn-xs-round default-inverted" data-toggle="collapse" data-placement="top"
                                 href="#collapseDetails{{item.allied.idAllied}}" aria-expanded="false" aria-controls="collapseDetails" id="details" 
                                 title="Detalles de configuración">
                                <span class="glyphicon glyphicon-collapse-down"></span>
                              </a>#}
                <a href="{{url('allied/show')}}/{{item.idAllied}}" class="button btn btn-xs-round default-inverted" data-toggle="tooltip" data-placement="top" title="Informacion completa de la cuenta maestra">
                  <span class="glyphicon glyphicon-eye-open"></span>
                </a>     
                <a href="{{url('allied/listuser')}}/{{item.idAllied}}" class="button btn btn-xs-round success-inverted" 
                   data-toggle="tooltip" data-placement="top" title="Usuarios de esta cuenta">
                  <span class="glyphicon glyphicon-user"></span>
                </a>
                <button class="button  btn btn-xs-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Recargar servicio" ng-click="rechargeService({{item.idAllied}})">
                  <i class="fa fa-plus-square" aria-hidden="true"></i>
                </button>
                {#              <a href="{{url('masteraccount/aliasconfigedit')}}/{{item.allied.idAllied}}" class="button btn btn-xs-round primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar configuración">
                                <span class="glyphicon glyphicon-cog"></span>
                              </a>#}
                <a href="{{url('masteraccount/aliasedit')}}/{{item.idAllied}}" class="button btn btn-xs-round info-inverted" data-toggle="tooltip" data-placement="top" title="Editar información">
                  <span class="glyphicon glyphicon-pencil"></span>
                </a>
                {#         <a href="{{url('admincontact/index')}}/{{item.idAllied}}/{{idMasteraccount}}" class="button shining btn btn-xs-round shining-round round-button warning-inverted" data-toggle="tooltip" data-placement="top" title="Contactos Administrativos">
                           <span class="glyphicon glyphicon-earphone"></span>
                         </a>#}
                <a href="{{url('technicalcontact/index')}}/{{item.idAllied}}/{{idMasteraccount}}" class="button shining btn btn-xs-round shining-round round-button warning-inverted" data-toggle="tooltip" data-placement="top" title="Contactos Técnicos y/o Administrativos">
                  <span class="fa fa-wrench"></span>
                </a>
                {#                  <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta cuenta aliada" data-id="{{url('masteraccount/aliasdelete')}}/{{item.allied.idAllied}}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                  </button>#}
              </td>
              </tr>
            {% endfor %}                
            </tbody>                    
          </table>            
        </div>    
      </div>

      <div class="row">
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'masteraccount/aliaslist/'~idMasteraccount]) }}
      </div>

    {% else %}    
      {% if configMaster == false %}    
        <div class="row">
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
      {% else %}    
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
              <div class="body success-no-hover text-center">
                <h2>
                  No existen cuentas aliadas creadas actualmente, si desea crear una haga <a href="{{url('masteraccount/aliascreate')}}/{{idMasteraccount}}">clic aquí</a>.
                </h2>    
              </div>
            </div>
          </div>
        </div>
      {% endif %}
    {% endif %}

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
                      <md-slider flex min="1" class="md-warn" max="{{ '{{ smsTotalMaster }}' }}" ng-model="smsLimit" aria-label="red" id="red-slider">
                      </md-slider>
                      <md-input-container>
                        <input flex type="number" min="1" max="{{ '{{ smsTotalMaster }}' }}" ng-model="smsLimit" aria-label="red" aria-controls="red-slider">
                      </md-input-container>
                      <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                             ng-model="spaceTotal">/{{ '{{ var = (smsTotalMaster - smsLimit) }}' }} Mensajes
                      </label>
                    </md-slider-container>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showsmstwoway">
                  <label class="col-sm-3 col-md-3 ">*Limite de Mensajes de Texto doble-via:</label>
                  <div class="col-sm-9 col-md-9">
                    <md-slider-container>
                      <md-slider flex min="1" class="md-warn" max="{{ '{{ smstwowayTotalMaster }}' }}" ng-model="smstwowayLimit" aria-label="red" id="red-slider">
                      </md-slider>
                      <md-input-container>
                        <input flex type="number" min="1" max="{{ '{{ smstwowayTotalMaster }}' }}" ng-model="smstwowayLimit" aria-label="red" aria-controls="red-slider">
                      </md-input-container>
                      <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                             ng-model="spaceTotal">/{{ '{{ var = (smstwowayTotalMaster - smstwowayLimit) }}' }} Mensajes 
                      </label>
                    </md-slider-container>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showlandingpage">
                  <label class="col-sm-3 col-md-3 ">*Limite de visualizaciones de Landing Page:</label>
                  <div class="col-sm-9 col-md-9">
                    <md-slider-container>
                      <md-slider flex min="1" class="md-warn" max="{{ '{{ landingpageTotalMaster }}' }}" ng-model="landingpageLimit" aria-label="red" id="red-slider">
                      </md-slider>
                      <md-input-container>
                        <input flex type="number" min="1" max="{{ '{{ landingpageTotalMaster }}' }}" ng-model="landingpageLimit" aria-label="red" aria-controls="red-slider">
                      </md-input-container>
                      <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                             ng-model="spaceTotal">/{{ '{{ var = (landingpageTotalMaster - landingpageLimit) }}' }} Mensajes 
                      </label>
                    </md-slider-container>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="showemail">
                  <label class="col-sm-3 col-md-3">*Limite de Correos:</label>
                  <div class="col-sm-9 col-md-9">
                    <md-slider-container>
                      <md-slider flex min="1" max="{{ '{{ contactTotalMaster }}' }}" ng-model="mailLimit" aria-label="red" id="red-slider">
                      </md-slider>
                      <md-input-container>
                        <input flex type="number" min="1" max="{{ '{{ contactTotalMaster }}' }}" ng-model="mailLimit" aria-label="red" aria-controls="red-slider">
                      </md-input-container>
                      <label id="" ng-class="{positive: var > 0, negative: var <= 0}"
                             ng-model="spaceTotal">/{{ '{{ var = (contactTotalMaster - mailLimit) }}' }} Mensajes
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
  <script>
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
  </script>


{% endblock %}    
