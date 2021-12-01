{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
  {{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}

  {#<link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">#}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  <script>
    {% if mail_content is defined %}
      objMail ={{ mail_content.content }}
    {% endif %}
  </script>

{% endblock %}

{% block js %}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  
  {{ javascript_include('js/angular/mail/dist/mail.893d208fdfd38a12f66a.min.js') }}
  {#{{ javascript_include('js/angular/mail/app.js') }}
  {{ javascript_include('js/angular/mail/controllers.js') }}
  {{ javascript_include('js/angular/mail/directives.js') }}
  {{ javascript_include('js/angular/mail/services.js') }}#}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/moment/src/moment.js') }}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
  <script>

      var idMail = 'null';
    {% if mail is defined %}
      idMail = {{mail.idMail}};
    {% endif %}

        function iframeResize() {
          var iFrame = document.getElementById('iframeEditor');
          iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
          //iFrame.height = "650px";
        }

      function htmlPreview() {
        var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
        $.ajax({
          url: "{{url('mail/previeweditor')}}/" + idMail,
          type: "POST",
          data: {
            editor: editor
          },
          error: function (msg) {
            slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
          },
          success: function () {
            $("#modal-body-preview").empty();
            $('#modal-body-preview').append($('<iframe frameborder="0" width="100%" height="100%" src="{{url('mail/previewdata')}}"/>'));
          }
        });

        document.getElementById('iframeEditor').contentWindow.RecreateEditor();
      }

  </script>
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div data-ng-controller="contentEditorController">

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Contenido del correo <em><b>{{ mail.name }}</b></em>
        </div>
        <hr class="basic-line"/>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="subtitle">
          <em>Información básica del correo</em>
        </div>
        <br>
        <p class="small-text text-justify">
          No es necesario que sepa desarrollar en html, con el editor avanzado solo seleccione, arrastre y suelte elementos, organícelos de la manera en que los necesite en cuestión de segundos. El editor se encarga de transformar sus piezas en html que podrá ser visualizado en la mayoría de los gestores de correo como Gmail o Hotmail. Recuerde que al usar el editor avanzado tendrá contenido responsive que podrá ser visualizado correctamente en dispositivos móviles de pantallas pequeñas (Este servicio podría tener un costo adicional).
        </p>
      </div>
    </div>

    <div class="row">
      <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-info">
            <div class="body row">
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}/{{mail.idMail}}" frameborder="0" height="1000px" width="100%" seamless onload="iframeResize();"></iframe>
                </div>
              </div>
            </div>
            <div class="footer row none-margin">
              <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <a href="{{ url('mail/create#/content/')~mail.idMail }}"
                   class="button btn btn-small danger-inverted"
                   data-toggle="tooltip" data-placement="top" title="Cancelar">
                  <i class="fa fa-times"></i> Salir sin guardar cambios
                </a>
              </div>
              <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 text-right">
                <a href="" onClick="htmlPreview();" class="button btn btn-small info-inverted"
                   data-placement="top" title="Previsualizar" data-toggle="modal" data-target="#preview-modal">
                  <i class="fa fa-eye"></i> Previsualizar
                </a>
                <!-- Button trigger modal SaveTemplate -->
                <button type="button" class="button btn btn-samll default-inverted" data-toggle="modal" data-target="#saveMailAsMailTemplate" data-ng-click="restart()">
                  <i class="fa fa-wpforms"></i> Guardar como plantilla
                </button>
                <a
                  class="button btn btn-small primary-inverted"
                  data-toggle="tooltip" data-placement="top" data-ng-click="saveContent('{{ mail.idMail }}')" title="Guardar y seguir editando">
                  <i class="fa fa-pencil-square-o"></i> Guardar y seguir editando
                </a>
                <button type="submit" class="button btn btn-small success-inverted" data-ng-click="registerContent('{{ mail.idMail }}')"
                        data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
                  <i class="fa fa-save"></i> Guardar y continuar
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
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

    <!-- Modal -->
    <div class="modal fade" id="saveMailAsMailTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Guardar correo como plantilla</h4>
          </div>
          <div class="modal-body" id="contentView">
            <form>
              <div class="form-group">
                <label for="nameMailTemplate">Nombre de la plantilla</label>
                <input type="text" class="undeline-input form-control" id="nameMailTemplate" name="nameMailTemplate" maxlength="80" autofocus data-ng-model="namemailtempcat">
                <p ng-show="empty" style="color:red">El campo de nombre de la plantilla no puede estar vacío</p>
                <p ng-show="length" style="color:red">El campo de nombre de la plantilla debe tener mínimo 2 caracteres y máximo 80</p>
              </div>
              <div class="form-group">
                <label for="categMailTemplate">Categoría de la plantilla</label>
                <select class="form-control chosen" id="categMailTemplate" name="categMailTemplate" style="width: 100%" data-ng-model="mailtempcat">
                  <option value=""></option>
                  {% for item in mailtempcat %}
                    <option value="{{item.idMailTemplateCategory}}">{{item.name}}</option>
                  {% endfor %}
                </select>
                <p ng-show="categ" style="color:red">Debe seleccionar un ítem del listado de categorías</p>
              </div>

              <div class="text-right">
                <button type="button" class="button fill btn btn-sm danger-inverted" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                <button type="button" class="button fill btn btn-sm success-inverted" data-ng-click="saveMailAsMailTemplate()"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
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
    var templateBase = "mail";

    $(function () {
      $(".chosen").select2({
        placeholder: 'Seleccione una categoría'
      });
    });
  </script>
{% endblock %}
