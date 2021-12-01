{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}


  {{ stylesheet_link('library/redactor/redactor.min.css') }}
  {{ stylesheet_link('library/spectrum/css/spectrum.css') }}


{% endblock %}

{% block js %}
  {{ javascript_include('library/redactor/redactor.min.js') }}
  {{ javascript_include('library/redactor/langs/es.js') }}
  {{ javascript_include('library/redactor/plugins/fullscreen.js') }}
  {{ javascript_include('library/redactor/plugins/clips.js') }}
  {{ javascript_include('library/redactor/plugins/fontcolor.js') }}
  {{ javascript_include('library/redactor/plugins/fontfamily.js') }}
  {{ javascript_include('library/redactor/plugins/fontsize.js') }}
  {{ javascript_include('library/redactor/plugins/textdirection.js') }}
  {{ javascript_include('library/spectrum/js/spectrum.js') }}


  <script>

    var idMail
    = {{idMail}};
      var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    $(document).ready(
      function () {
        $('#content').redactor({
          imageUpload: fullUrlBase + '/asset/upload/',
          imageGetJson: fullUrlBase + '/asset/list/',
          imageUploadErrorCallback: function (json) {
            slideOnTop(json.error, 3500, 'glyphicon glyphicon-remove', 'danger');
          },
          lang: 'es',
          plugins: ['fontcolor', 'fontfamily', 'fontsize', 'fullscreen', 'clips', 'advanced'],
          fullpage: true,
          minHeight: 400
        });
      }
    );

    function verHTML() {
      var content = $('#content').val();
      $.ajax({
        url: "{{url('mail/previewhtml')}}/" + idMail,
        type: "POST",
        data: {html: content},
        error: function (msg) {
          $.gritter.add({class_name: 'error', title: '<i class="icon-warning-sign"></i> Atención', text: msg.statusText, sticky: false, time: 10000});
        },
        success: function () {
          $("#modal-body-preview").empty();
          $('#modal-body-preview').append($('<iframe frameborder="0" width="100%" height="100%" src="{{url('mail/previewdata')}}"/>'));
        }
      });
    }

    function SaveEdit(url) {
      var content = $('#content').val();
      $.ajax({
        url: url,
        type: "POST",
        data: {content: content, save: false},
        error: function (msg) {
          window.location.href = fullUrlBase + "mail/create#/content/" + idMail;
        },
        success: function (msg) {
          slideOnTop(msg.msg, 3500, 'glyphicon glyphicon-ok-circle', 'success');
        }
      });
    }
    function saveExit(url) {
      var content = $('#content').val();
      $.ajax({
        url: url,
        type: "POST",
        data: {content: content, save: true},
        error: function (msg) {
          window.location.href = fullUrlBase + "mail/create#/content/" + idMail;
        },
        success: function (msg) {
          window.location.href = fullUrlBase + "mail/create#/content/" + idMail;
        }
      });
    }
  </script>    
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  {# <form action="{{url('mail/htmlcontent')}}" method="post"> #}
  <div class="row">
    <div class="col-md-12 wrap">
      <div class="title">
        Contenido del correo <strong>{{nameMail}}</strong>
      </div>
      <hr class="basic-line"/>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 wrap">
      Editor Html
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 wrap">
      Cree contenido de html puro, recuerde que el contenido html de un correo electroníco es limitado, ya que los gestores de correo cómo Gmail y Hotmail remueven código Javascript y CSS del header. le recomendamos acomodar los elementos acomodar los elementos por medio de tablas, no utilice divs. Utilice CSS inline para dar estilo a los elementos.
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 wrap">
      <textarea name="content" class="form-control" rows="4" required id="content">{% if content is defined%} {{content}} {% elseif footer.editable == 1 %} {{footer.html}} {% endif %}</textarea>
      {% if footer.editable == 0 %}
        <div class="footer-html">
          {{footer.html}}
        </div>
      {% endif %}
    </div>
  </div>

  <div id="clipsmodal" style="display: none;">
    <section>
      <ul class="redactor_clips_box">
        <li>
          <a href="#" class="redactor_clip_link">Email</a>
          <div class="redactor_clip" style="display: none;">
            %%EMAIL%%
          </div>
        </li>
        <li>
          <a href="#" class="redactor_clip_link">Nombre</a>
          <div class="redactor_clip" style="display: none;">
            %%NOMBRE%%
          </div>
        </li>
        <li>
          <a href="#" class="redactor_clip_link">Apellido</a>
          <div class="redactor_clip" style="display: none;">
            %%APELLIDO%%
          </div>
        </li>
        <li>
          <a href="#" class="redactor_clip_link">Fecha de nacimiento</a>
          <div class="redactor_clip" style="display: none;">
            %%FECHA_DE_NACIMIENTO%%​
          </div>
        </li>
        {%if cfs is defined %}
          {%for cf in cfs%}
            <li>
              <a href="#" class="redactor_clip_link">{{cf.name}}</a>

              <div class="redactor_clip" style="display: none;">
                %%{{cf.alternativename}}%%
              </div>
            </li>
          {%endfor%}
        {%endif%}
        <li>
          <a href="#" class="redactor_clip_link">Enlace de des-suscripcion</a>
          <div class="redactor_clip" style="display: none;">
            <a href="%%UNSUBSCRIBE%%">Para desuscribirse haga clic aqui</a>
          </div>
        </li>
        <li>
          <a href="#" class="redactor_clip_link">Enlace de version web</a>
          <div class="redactor_clip" style="display: none;">
            <a href="%%WEBVERSION%%">Version web</a>
          </div>
        </li>
      </ul>
    </section>
    <footer>
      <a href="#" class="redactor_modal_btn redactor_btn_modal_close">Close</a>
    </footer>
  </div>

  <div class="row">
    <div class="col-md-6 wrap" >
      <a href="{{url('mail/create#/content')}}/{{ idMail }}"
         class="button btn btn-small danger-inverted"
         data-toggle="tooltip" data-placement="top" title="Salir">
        <i class="fa fa-times"></i> Salir sin guardar cambios
      </a>
    </div>
    <div class="col-md-6 wrap" align="right">
      <button onclick="verHTML();" class="button btn btn-small info-inverted" data-toggle="modal" data-target="#preview-modal" title="Visualizar">Visualizar</button>
      <button onclick="SaveEdit('{{url('mail/htmlcontent')}}/{{ idMail }}')"
              type="button"
              class="button btn btn-small info-inverted"
              data-toggle="tooltip" data-placement="top" title="Seguir editando">
        guardar y seguir editando
      </button>
      <button onclick="saveExit('{{url('mail/htmlcontent')}}/{{ idMail }}')"
              type="button"
              class="button btn btn-small success-inverted"
              data-toggle="tooltip" data-placement="top" title="Guardar y salir">
        Guardar y salir
      </button>
    </div>    
  </div>
  <div id="preview-modal" class="modal fade">
    <div class="modal-dialog modal-prevew-width">
      <div class="modal-content modal-prevew-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h1 class="modal-title">Previsualización</h1>
        </div>
        <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-small danger-inverted" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!--/form-->
{% endblock %}

{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "htmlcontent";
    var idMail = "{{ idMail }}";
  </script>
{% endblock %}

