{% extends "templates/default.volt" %}
{% block css %}
{# Notifications #}
{{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
{{ partial("partials/js_notifications_partial") }}
{{ partial("partials/slideontop_notification_partial") }}
{{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
{% endblock %}
{% block header %}
{# Notifications #}
{{ partial("partials/slideontop_notification_partial") }}
{# Dialogs #}
{{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
{{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

<script type="text/javascript">
  $(function () {
    $('#details').tooltip();
  });
</script>
{% endblock %}

{% block content %}
<div class="clearfix"></div>
<div class="space"></div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de Subcuentas
    </div>
    <hr class="basic-line" />
    <p>
      En esta lista podra ver, crear, editar y eliminar subcuentas de una cuenta.
    </p>
  </div>
</div>

{% if page.items|length != 0 %}
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    {#<a href="{{url('account/index')}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas</a>#}
    <a href="{{url('subaccount/create')}}/{{idAccount}}" class="button shining btn btn-sm success-inverted">Crear una
      nueva Subcuenta</a>
    {{ partial('partials/pagination_static_partial', ['pagination_url': 'subaccount/index/'~idAccount]) }}
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
    <table class="table table-bordered sticky-enabled ">
      <thead class="theader">
        <tr>
          <th>Informaión</th>
          <th>Detalles</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        {% for item in page.items %}
        <tr {% if item.status == 0 %} class="deactivate" {% endif %}>
          <td>
            <strong class="medium-text">
              {{item.name}}
            </strong>
            <p>{{item.description}} </p>
            <dl>
              <dd> <em class="extra-small-text">Creado por <strong>{{(item.createdBy)}}</strong> , a las
                  <strong>{{date('d/m/Y g:i a', item.created)}}</strong> </em></dd>
              <dd> <em class="extra-small-text">Actualizado por <strong> {{(item.updatedBy)}}</strong>, a las
                  <strong>{{date('d/m/Y g:i a', item.updated)}}</strong></em></dd>
            </dl>
          </td>
          <td>
            <dl>
              <dd>
              <span> Servicios:</span> 
                {% for saxs in item.Saxs %}

                {% if saxs.status == 1 %}
                <div ng-show="buttoninactive" style="margin-top: 6px;">
                  &raquo;<em>{{saxs.Services.name}}</em> 
                    <button  style="height: 18px;width: 18px;float: left;margin-top: 0px !important;" id="delete" onClick="openModal(1);"
                      class="button shining btn btn-xs-round shining shining-round round-button success-inverted"
                      data-toggle="tooltip" data-placement="top" title="desactivar servicio"
                      data-id="{{url('subaccount/desactivateservice')}}/{{saxs.idSaxs}}">
                      <span style="font-size: 10px;top: -8px!important;left: -6px;"
                        class="glyphicon glyphicon-ok"></span>
                    </button>
                  </div>
                {% else %} 
                <div ng-show="buttonactive" style="margin-top: 6px;">
                  &raquo;<em>{{saxs.Services.name}}</em> 
                  <button  style="height: 18px;width: 18px;float: left;margin-top: 0px !important;" id="activate" onClick="openModal(2);"
                    class="button shining btn btn-xs-round shining shining-round round-button danger-inverted"
                    data-toggle="tooltip" data-placement="top" title="activar servicio"
                    data-id="{{url('subaccount/activateservice')}}/{{saxs.idSaxs}}">
                    <span style="font-size: 10px;top: -8px!important;left: -6px;"
                     class="glyphicon glyphicon-remove"></span>
                  </button>
  
                  
                </div>
                {% endif %}
            
                {% endfor %}
              </dd>
              {#<dd>
                      Espacio de almacenamiento:
                      {% if item.diskSpace is defined %}
                        {{ item.diskSpace }} MB
                      {% else %}
                        Sin configurar
                      {% endif %}
                    </dd>
                    <dd>
                      Limite de correos:
                      {% if item.mailLimit is defined %}
                        {{ item.mailLimit  }} 
                      {% else %}
                        Sin configurar
                      {% endif %}
                    </dd>
                    <dd>
                      Limite de contactos:
                      {% if item.contactLimit is defined %}
                        {{ item.contactLimit  }} 
                      {% else %}
                        Sin configurar
                      {% endif %}
                    </dd>
                    <dd>
                      Limite de sms:
                      {% if item.smsLimit is defined %}
                        {{ item.smsLimit  }} 
                      {% else %}
                        Sin configurar
                      {% endif %}
                    </dd>#}
            </dl>
          </td>
          <td class="user-actions text-right">
            <a href="{{url('subaccount/show')}}/{{item.idSubaccount}}" class="button btn btn-xs-round default-inverted"
              data-toggle="tooltip" data-placement="top" title="Informacion completa de la subcuenta">
              <span class="glyphicon glyphicon-eye-open"></span>
            </a>
            <a href="{{url('subaccount/edit')}}/{{item.idSubaccount}}"
              class="button  btn btn-xs-round  round-button info-inverted" data-toggle="tooltip" data-placement="top"
              title="Editar esta subcuenta">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <a href="{{url('subaccount/userlist')}}/{{(item.idSubaccount)}}"
              class="button  btn btn-xs-round  round-button success-inverted" data-toggle="tooltip" data-placement="top"
              title="Lista de Usuarios">
              <span class="glyphicon glyphicon-user"></span>
            </a>
            <button id="deleteSubaccount" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar subcuenta" data-id="{{url('subaccount/deletesubaccount')}}/{{item.idSubaccount}}">
              <span  class="glyphicon glyphicon-trash"></span>
            </button>
          </td>
        </tr>
        {% endfor %}
      </tbody>
    </table>
    {{ partial('partials/pagination_static_partial', ['pagination_url': 'subaccount/index/'~idAccount]) }}
  </div>
</div>
{% else %}
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="text-right">
      <a href="{{url('account/index')}}" class="button shining btn btn-sm default-inverted">Regresar a lista de
        Cuentas</a>
    </div>
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No existen subcuentas creadas actualmente, si desea crear una haga <a
            href="{{url('subaccount/create')}}/{{idAccount}}">clic aquí</a>.
        </h2>
      </div>
    </div>
  </div>
</div>
{% endif %}

<div id="somedialogInactivate" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
        preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276" />
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Desea desactivar el servicio?</h2>
      <div style="z-index: 999999;">
        <a onClick="closeModal(1);" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#" id="btn-okD" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>

  </div>
</div>
<div id="somedialogActivate" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
        preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276" />
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Desea activar el servicio?</h2>
      <div style="z-index: 999999;">
        <a onClick="closeModal(2);" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#" id="btn-okA" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA ELIMINAR CUENTA -->
<div id="modalDeleteSA" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
        preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276" />
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Desea eliminar la subcuenta?</h2>
      <div style="z-index: 999999;">
        <a onClick="$('#modalDeleteSA').toggleClass('dialog--open');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="{{url('subaccount/activateservice')}}/{{item.idSubaccount}}" id="confirmDelete" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<script>
  function esconderbotones() { }


  $(document).on("click", "#delete", function () {
    var myURL = $(this).data('id');
    $("#btn-okD").attr('href', myURL);
  });
  $(document).on("click", "#activate", function () {
    var myURL = $(this).data('id');
    console.log("ENTRE**********", myURL);
    $("#btn-okA").attr('href', myURL);

  });
  function openModal(prm) {
    if (prm == 1) {
      $('#somedialogInactivate').addClass('dialog--open');
    } else {
      $('#somedialogActivate').addClass('dialog--open');
    }
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
    if (prm == 1) {

      $('#somedialogInactivate').removeClass('dialog--open');
    } else {
      $('#somedialogActivate').removeClass('dialog--open');

    }
  }

  //FUNCIONES PARA ELIMINAR SUBCUENTA
  $('button#deleteSubaccount').on('click', function(){
    console.log($(this).data('id'));
    $('#modalDeleteSA').toggleClass('dialog--open');
    $("#confirmDelete").attr('href', $(this).data('id'));
  });


</script>

{% endblock %}