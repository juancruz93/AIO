{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}    

{% block js %}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {# Dialogs #}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ javascript_include('js/angular/mail_structure/app.js') }}
  {{ javascript_include('js/angular/mail_structure/controllers.js') }}
  {{ javascript_include('js/angular/mail_structure/directives.js') }}
  {{ javascript_include('js/angular/mail_structure/services.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
    function htmlPreview(editor) {
      $.ajax({
        url: "{{url('mailpreview/preview')}}",
        type: "POST",
        data: {
          editor: editor
        },
        error: function (msg) {
          slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
        },
        success: function () {
          $("#modal-body-preview").empty();
          $('#modal-body-preview').append($('<iframe frameborder="0" width="100%" height="100%" src="{{url('mailpreview/previewdata')}}"/>'));
        }
      });
      document.getElementById('iframeEditor').contentWindow.RecreateEditor();
    }
  </script>
{% endblock %}
{% block content %}
  <style type="text/css">
    #navlist li
    {
      display: inline;
      list-style-type: none;
      padding-right: 20px;
    }   
  </style>

  <div class="clearfix"></div>
  <div class="space"></div>     
  <div ng-app="mail_structure" ng-controller="ctrlIndex" ng-cloak>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Lista de estructuras prediseñadas
        </div>            
        <hr class="basic-line" />
        <p>
          Las estructuras prediseñadas, son útiles para que los usuarios creen contenido de correo 
          con el editor avanzado y partan desde una base o marco de trabajo. Solo deberán rellenar 
          la maqueta o esqueleto con imágenes o texto.
        </p>            
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
        <a href="{{url('tools')}}" class="button shining btn btn-sm default-inverted">Regresar</a>
        <a href="{{url('mail_structure/create')}}" class="button shining btn btn-sm success-inverted">Crear una nueva estructura</a>
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
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mailstructure.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (mailstructure.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (mailstructure.total_pages) || mailstructure.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>

    <div class="wrap row">

      <div class="fill-block fill-block-default text-center " >
        <div class="body">

          <div class="row" style="margin-right: 5%">
            <div class="col-xs-12 col-sm-12 col-lg-12  row ">
              <div class="col-xs-4 col-sm-4 col-lg-4 ">
                <div class="input-group">
                  <input class="form-control"  id="name" ng-keyup='search()' placeholder="Buscar por nombre" ng-model="filter.name" />
                  <span class=" input-group-addon" id="basic-addon1" >
                    <i class="fa fa-search"></i>
                  </span>
                </div>
              </div>
            </div> 
          </div> 

          <br>

          <div class="row" >
            <div class="col-sm-6 col-md-3"  ng-repeat="key in mailstructure[0].items">
              <div class="thumbnail text-center">
{#                <img src="{{url('')}}images/1.png" style="width: 50%">#}
                <img src="{{url('')}}mail_structure/{{user.userType.idAllied}}/{{"{{key.idMailStructure}}"}}_thumb.png" >
                <div class="caption none-padding" style=" margin-bottom: -8px">
                  <br>
                  {{"{{ key.name }}"}}
                  <p>
                    {{"{{ key.description }}"}}
                  </p>
                  <div>

                    <a id="delete" href="{{url("mail_structure/edit/")~"{{key.idMailStructure}}"}}" 
                       class="button shining btn btn-xs-round round-button info-inverted" 
                       data-toggle="tooltip" data-placement="top" title="Editar" style="float: none !important;" >
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>

                    <button id="delete" ng-click="openModal(key.idMailStructure);" 
                            class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" 
                            data-toggle="tooltip" data-placement="top" title="Borrar" style="float: none !important;">
                      <span class="glyphicon glyphicon-trash"></span>
                    </button>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
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
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mailstructure.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (mailstructure.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (mailstructure.total_pages) || mailstructure.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>

    <div class="modal fade " id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-prevew-width">
        <div class="modal-content modal-prevew-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
          </div>
          <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;"></div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger">Cerrar</button>
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
            <a  ng-click="confirmDelete()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
        </div>
      </div>
    </div>

  </div>
{% endblock %}
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "mail_structure";
    function openModal() {
      $('#somedialog').addClass('dialog--open');
    }

    function closeModal() {
      $('.dialog').removeClass('dialog--open');
    }
  </script>

{% endblock %}
