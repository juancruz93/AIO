{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {#    {{ partial("partials/notifications_partial") }}#}
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
        Usuario(s) maestro(s)
      </div>            
      <hr class="basic-line" />
      {% if page.items|length != 0 %}
        <p>
          A continuación se muestran los usuarios que pertenecen a la cuenta maestra <b> {{ nameMasteraccount }} </b>, podrá agregar, editar o eliminar
          los usuarios que desee.
        </p>            
      {% endif %}
    </div>
  </div>

  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
        {#                <a href="{{url('user/create')}}/{{userData.idAccount}}" class="button shining btn btn-sm success-inverted">Crear un nuevo Usuario</a>#}
        <a href="{{url('masteraccount/createuser/'~idMasteraccount)}}" class="button shining btn btn-sm success-inverted">Crear un nuevo Usuario</a>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'user/index/'])}}
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
              <a href="{{url('session/superuser')}}/{{item.idUser}}" class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Iniciar sesión como este usuario">
                <span class="glyphicon glyphicon-log-in"></span>
              </a>
              {#                  <a href="{{url('session/superuser')}}/{{item.idUser}}" class="button shining btn btn-xs-round shining shining-round round-button warning-inverted" data-toggle="tooltip" data-placement="top" title="Iniciar sesión como este usuario">
                                  <span class="glyphicon glyphicon-log-in"></span>
                                </a>#}
              <a href="{{url('user/passedit')}}/{{item.idUser}}" class="button btn btn-xs-round primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar contraseña del Usuario">
                <span class="glyphicon glyphicon-lock"></span>
              </a>
                <button id="delete" onClick="openModal();" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este Usuario" data-id="{{url('user/delete')}}/{{item.idUser}}">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
              <a href="{{url('user/edit')}}/{{item.idUser}}" class="button btn btn-xs-round info-inverted" data-toggle="tooltip" data-placement="top" title="Editar este Usuario">
                <span class="glyphicon glyphicon-pencil"></span>
              </a>
              
            </td>
            </tr>
          {% endfor %}
          </tbody>
        </table>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'user/index/']) }}
      </div>
    </div>
  {% else %} 
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen usuarios creados actualmente, si desea crear uno haga <a href="{{url('masteraccount/createuser/' ~ idMasteraccount)}}">clic aquí</a>.
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
        <h2>¿Esta seguro?</h2>
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
