{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{  stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}

  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  <script>
    {% if mail_content is defined %}
      var objMail ={{ mail_content.content }}
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
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
{% endblock %}

{% block content %}

  <script type="text/javascript">
      var idMail
      = {{mail.idMail}};
        var dir = "{{url('mail/create#/content')}}/";
    {% if pdf is defined AND pdf == 'pdf'%}
      dir = "{{url('pdfmail/compose')}}/";
    {% endif %}	

      function sendData(image) {
        var url = $('#url').val().trim();
    {#        $('#wait').show();#}
        $.ajax({
          url: "{{url('mail/urleditor')}}/" + idMail,
          type: "POST",
          data: {
            url: url,
            image: image
          },
          error: function (msg) {
            document.getElementById('submit').disabled=false;
            var obj = $.parseJSON(msg.responseText);
            slideOnTop(obj.error, 3500, 'glyphicon glyphicon-remove', 'danger');
           /* $.gritter.add({class_name: 'error', title: '<i class="icon-warning-sign"></i> Atención', text: obj.error, sticky: false, time: 10000});*/
    {#            $('#wait').hide();#}
          },
          success: function () {
            $(location).attr('href', dir + idMail);
          }
        });
      }
  </script>


  <div class="clearfix"></div>
  <div class="space"></div>

  <div data-ng-controller="contentUrlController">

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
          <em>Importar desde una URL</em>
        </div>
        <br>
        <p class="small-text text-justify">
          Cree contenido a partir de una página web, solo debe pegar la url en el campo de texto y 
          presionar el botón importar. Recuerde que el contenido html de un correo electrónico es 
          limitado, ya que los gestores de correo como Gmail y Hotmail remueven código JavaScript y 
          CSS del header. Le recomendamos que el contenido de la página web, tenga los elementos 
          acomodados por medio de tablas, no utilice divs. Utilice CSS inline para dar estilo a los 
          elementos.
        </p>
        {#<p class="small-text text-justify">
          También puede importar las imágenes que se encuentra en la página web, marcando 
          la casilla "Importar imágenes", para que estas sean guardadas en su galería en nuestra 
          plataforma y pueda usarlas luego en campañas.
        </p>#}
      </div>
    </div>

    <div class="row">
      <form name="contactlistForm" class="form-horizontal" role="form" onSubmit="document.getElementById('submit').disabled=true;">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-info fill-block-default">
            <div class="body row">
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row">
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Escriba o copie y pegue la dirección del enlace (url)</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <input type="url" name="url" id="url"
                               class="undeline-input" ng-model="data.replyto" ng-keyup="trimContent()">
                      </span>
                    </div>
                  </div>
                </div>
                {#          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row">
                            <div class="form-group">
                              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="col-sm-8 col-sm-offset-4">
                                  <md-switch name="image" id="image"   ng-model="checkboximage" aria-label="Switch 2" class="md-warn ">
                                    Importar imágenes
                                  </md-switch>
                                </span>
                              </div>
                            </div>
                          </div>#}
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row">
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="col-sm-8 col-sm-offset-4">
                        <a href="{{ url('mail/create#/content/') }}{{mail.idMail}}"
                           class="button btn btn-small danger-inverted"
                           data-toggle="tooltip" data-placement="top" title="Cancelar">
                          Cancelar
                        </a>
                        <button id="submit" type="submit" class="button btn btn-small success-inverted" ng-click="registerContentUrl();"
                                data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
                          Importar
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
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
          <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger">Cerrar</button>
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
  </script>

{% endblock %}
