{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
  {{ javascript_include('js/angular/supportcontact/app.js') }}
  {{ javascript_include('js/angular/supportcontact/controllers.js') }}
  {{ javascript_include('js/angular/supportcontact/services.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
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

  <div ng-controller="indexcontroller">

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 wrap">
        <div class="title">
          Lista de Contactos Técnicos y/o Administrativos
        </div>
        <hr class="basic-line" />
        <p>
          En esta lista podrá ver, crear, editar y eliminar contactos técnicos de las cuentas aliadas.
        </p>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">       
        <a href="{{url('masteraccount/aliaslist')}}/{{idMasteraccount}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas Aliadas</a>
        <a href="{{url('technicalcontact/create')}}/{{idAllied}}/{{idMasteraccount}}" class="button shining btn btn-sm success-inverted">Crear un nuevo contacto técnico</a>
      </div>
    </div>

    <div ng-show ="supportcontact.total > 0">

      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{supportcontact.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (supportcontact.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (supportcontact.total_pages) || supportcontact.total_pages == 0 ? 'disabled'  : ''">
            <a href="#/" ng-click="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0 ? 'disabled'  : ''">
            <a ng-click="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
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
                <th>Tipo de contacto</th>
                <th>Interacción</th>
                <th></th>
              </tr>
            </thead>
            <tbody ng-repeat="key in supportcontact.items">
              <tr>
                <td>
                  {{"{{ key.name  }}"}}
                </td>
                <td>
                  {{"{{ key.lastname  }}"}}
                </td>
                <td>
                  {{"{{ key.email  }}"}}
                </td>
                <td>
                  {{"{{ key.phone  }}"}}
                </td>
                <td>
                  {{"{{ key.type  }}"}}
                </td>
                <td>
                  <small>
                    <b>Creado: </b>{{"{{ key.created * 1000 | date: 'yyyy-MM-dd' }}"}}
                  </small><br>
                  <small>
                    <b>Actualizado: </b>{{"{{ key.updated * 1000 | date: 'yyyy-MM-dd' }}"}}
                  </small>
                </td>
                <td class="user-actions text-right">
                  <a href="{{url('technicalcontact/edit')}}/{{"{{key.idSupportContact}}"}}/{{idMasteraccount}} " class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar contacto">
                    <span class="glyphicon glyphicon-pencil"></span>
                  </a>
                  <button id="delete" ng-click="openModalDelete(key.idSupportContact)"  class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" 
                          data-placement="top" title="Borrar contacto" >
                    <span class="glyphicon glyphicon-trash"></span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{supportcontact.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (supportcontact.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (supportcontact.total_pages) || supportcontact.total_pages == 0 ? 'disabled'  : ''">
            <a href="#/" ng-click="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0 ? 'disabled'  : ''">
            <a ng-click="page == (supportcontact.total_pages)  || supportcontact.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>
    </div>

    <div class="row" ng-show ="supportcontact.total == 0">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No existen contactos técnicos creados actualmente, si desea crear uno haga <a href="{{url('technicalcontact/create')}}/{{idAllied}}">Click Aquí</a>
            </h2>
          </div>
        </div>
      </div>
    </div>
            
    <div id="somedialog" class="dialog z-index-1500">
      <div class="dialog__overlay"></div>
      <div class="dialog__content ">
        <div class="morph-shape">
          <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
          <rect x="3" y="3" fill="none" width="556" height="276"/>
          </svg>
        </div>
        <div class="dialog-inner z-indes-150">
          <h2>¿Esta seguro?</h2>
          <div>                    
            <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a href="{{"{{url}}"}}" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
        </div>
      </div>
    </div>
    <script>
      var idAllied = "{{idAllied}}";
      var idMasteraccount = "{{idMasteraccount}}";
      var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
      var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
      var templateBase = "mail";
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
  </div>
{% endblock %}
