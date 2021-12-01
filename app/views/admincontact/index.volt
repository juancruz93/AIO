{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 wrap">
      <div class="title">
        Lista de Contactos Administrativos
      </div>
      <hr class="basic-line" />
      <p>
        En esta lista podrá ver, crear, editar y eliminar contactos administrativos de las cuentas aliadas.
      </p>
    </div>
  </div>

  {% if page.items|length != 0 %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">       
        <a href="{{url('masteraccount/aliaslist')}}/{{idMasteraccount}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas Aliadas</a>
        <a href="{{url('admincontact/create')}}/{{idAllied}}" class="button shining btn btn-sm success-inverted">Crear un nuevo contacto administrativo</a>
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'admincontact/index/'~idAllied~'/'~idMasteraccount]) }}
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
        <table class="table table-bordered sticky-enabled ">                
          <thead class="theader">
            <tr>
              <th>Nombre</th>       
              <th>Apellido</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Interacción</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {% for item in page.items %}
              <tr>
                <td>{{item.name}}</td>
                <td>{{item.lastname}}</td>
                <td>{{item.email}}</td>
                <td>{{item.phone}}</td>
                <td>
                  <small>
                    <b>Creado: </b>{{date('d/m/Y g:i a',item.created)}}
                  </small><br>
                  <small>
                    <b>Actualizado: </b>{{date('d/m/Y g:i a',item.updated)}}
                  </small>
                </td>
                <td class="user-actions text-right">
                  <a href="{{url('admincontact/edit')}}/{{item.idAdmincontact}}" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar contacto">
                    <span class="glyphicon glyphicon-pencil"></span>
                  </a>
                  <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar contacto" data-id="{{url('admincontact/delete/'~item.idAdmincontact)}}">
                    <span class="glyphicon glyphicon-trash"></span>
                  </button>
                </td>
              </tr>
            {% endfor %}
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      {{ partial('partials/pagination_static_partial', ['pagination_url': 'admincontact/index/'~idAllied~'/'~idMasteraccount]) }}
    </div>
  {% else %}
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen contactos administrativos creados actualmente, si desea crear uno haga <a href="{{url('admincontact/create')}}/{{idAllied}}">Click Aquí</a>
            </h2>
          </div>
        </div>
      </div>
    </div>
  {% endif %}
  <div id="somedialog" class="dialog z-index-1500">
    <div class="dialog__overlay"></div>
    <div class="dialog__content ">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Esta seguro?</h2>
        <div class="">                    
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