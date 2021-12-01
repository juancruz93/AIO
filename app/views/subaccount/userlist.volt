{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

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
        Usuario(s) 
      </div>            
      <hr class="basic-line" />
      {% if page.items|length != 0 %}
        <p>
          A continuación se muestran los usuarios que pertenecen a la subcuenta <b> {{ name }} </b>, podrá agregar, editar o eliminar
          los usuarios que desee.
        </p>            
      {% endif %}
    </div>
  </div>

  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
        <a href="{{url('subaccount/index/')}}{{ user.userType.idAccount }}" class="button shining btn btn-sm default-inverted">Regresar a lista de Subcuentas</a>
        <a href="{{url('subaccount/createuser/'~idSubaccount)}}" class="button shining btn btn-sm success-inverted">Crear un nuevo Usuario</a>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'subaccount/userlist/'~idSubaccount])}}
      </div>

    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="table table-bordered">                
          <thead class="theader">
            <tr>
              <th></th>
              <th>Informacion</th>
              <th>Detalles</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {% for item in page.items %}                    
              <tr>
                <td>
                  {{(item.idUser)}}
                </td>
                <td>
                  <font class="strong-text ng-binding" size="5">
                  {{item.email}}
                  </font>
                  <dl>
                    <dd> {{item.name}} {{(item.lastname)}}</dd>
                    <dd> <em class="extra-small-text">Creado por <strong>{{(item.createdBy)}}</strong> , a las <strong>{{date('d/m/Y g:i a', item.created)}}</strong> </em></dd>
                    <dd> <em class="extra-small-text">Actualizado por <strong> {{(item.updatedBy)}}</strong>, a las  <strong>{{date('d/m/Y g:i a', item.updated)}}</strong></em></dd>
                  </dl>

                </td>
                <td>
            <di>
              <dd>{{item.cellphone}}</dd>
              <dd><em>{{ item.city.state.country.name }}, {{ item.city.state.name }}, {{ item.city.name }}</em></dd>
            </di>
            </td>
            <td class="user-actions text-right">
              <a href="{{url('session/superuser')}}/{{item.idUser}}" class="button  btn btn-xs-round  round-button warning-inverted" data-toggle="tooltip" data-placement="top" title="Iniciar sesión como este usuario">
                <span class="glyphicon glyphicon-log-in"></span>
              </a>
              <a href="{{url('subaccount/passedit')}}/{{item.idUser}}" class="button  btn btn-xs-round   round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar contraseña del Usuario">
                <span class="glyphicon glyphicon-lock"></span>
              </a>
              <a href="{{url('subaccount/edituser')}}/{{item.idUser}}" class="button  btn btn-xs-round   round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar este Usuario">
                <span class="glyphicon glyphicon-pencil"></span>
              </a>
              <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este Usuario" data-id="{{url('subaccount/deleteuser')}}/{{item.idUser}}">
                 <span class="glyphicon glyphicon-trash"></span>
                </button>
            </td>
            </tr>
          {% endfor %}
          </tbody>
        </table>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'subaccount/userlist//'~idSubaccount])}}
      </div>
    </div>
  {% else %} 
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen usuarios creados actualmente, si desea crear uno haga <a href="{{url('subaccount/createuser/'~idSubaccount)}}">clic aquí</a>.
            </h2>    
          </div>
        </div>
      </div>
    </div>
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
        <h2>¿Estás seguro de eliminar este usuario?</h2>
        <div>                    
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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
