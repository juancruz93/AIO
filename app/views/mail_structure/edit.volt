{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/mail_structure/app.js') }}
  {{ javascript_include('js/angular/mail_structure/controllers.js') }}
  {{ javascript_include('js/angular/mail_structure/directives.js') }}
  {{ javascript_include('js/angular/mail_structure/services.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
    {% if mailstructure is defined %}
      var objMail ={{ mailstructure.content }} ;
        var idMailstructure = {{ mailstructure.idMailStructure }};
    {% endif %}
  </script>
  <script>
      function iframeResize() {
        var iFrame = document.getElementById('iframeEditor');
        //iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
        iFrame.height = "650px";
      }

    function htmlPreview() {
      var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
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
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}    
  <div ng-app="mail_structure" ng-controller="ctrlEdit" ng-init="data.name = '{{mailstructure.name}}';data.description = '{{mailstructure.name}}'; data.idMailStructure = '{{mailstructure.idMailStructure}}' ">
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Nueva estructura prediseñada
        </div>            
        <hr class="basic-line" />            
        <p>
          Las estructuras prediseñadas, son útiles para que los usuarios creen contenido de 
          correo con el editor avanzado y partan desde una base o marco de trabajo. Solo deberán 
          rellenar la maqueta o esqueleto con imágenes y texto.
        </p>   
      </div>
    </div>       

    <div class="row" >
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-info">
          <div class="body row">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label>Nombre de la estructura:</label>
                    <input ng-model="data.name" class="form-control" >
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 wrap">
                    <label>Imagen actual: </label>
                    <img src="{{url('')}}mail_structure/{{user.userType.idAllied}}/{{"{{data.idMailStructure}}"}}_thumb.png" >
                  </div>
                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 wrap">
                    <label>Cargar previsualización:</label>
                    <input type="file" name="file"  class="form-control"
                           onchange="angular.element(this).scope().uploadedFile(this)" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label>Descripcion:</label>
                    <textarea ng-model="data.description" class="form-control"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <iframe id="iframeEditor" src="{{url('mail_structure/editor_frame')}}" frameborder="0" width="100%" onload="iframeResize();" seamless></iframe>
              </div>
            </div>
          </div>
          <div class="footer row none-margin">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
              <a href="{{ url('mail_structure') }}"
                 class="button btn btn-small danger-inverted"
                 data-toggle="tooltip" data-placement="top" title="Cancelar">
                Salir sin guardar cambios
              </a>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
              <a href="#/" onClick="htmlPreview();" class="button btn btn-small info-inverted"
                 data-placement="top" title="Previsualizar" data-toggle="modal" data-target="#preview-modal">
                Previsualizar
              </a>
              <a
                class="button btn btn-small primary-inverted" data-ng-click="editContent()"
                data-toggle="tooltip" data-placement="top"  title="Guardar y seguir editando">
                Editar y seguir editando
              </a>
              <button type="submit" class="button btn btn-small success-inverted"  data-ng-click="editContentExit()"
                      data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
                Editar y salir
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
          <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
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
  </script>

{% endblock %}