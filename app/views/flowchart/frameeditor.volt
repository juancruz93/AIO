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


  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
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
  {{ javascript_include('js/angular/mail/app.js') }}
  {{ javascript_include('js/angular/mail/controllers.js') }}
  {{ javascript_include('js/angular/mail/directives.js') }}
  {{ javascript_include('js/angular/mail/services.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/moment/src/moment.js') }}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}

  <script>

      var idMail = 'null';
    {% if mail is defined %}
      idMail = {{mail.idMail}};
    {% endif %}

        function iframeResize() {
          var iFrame = document.getElementById('iframeEditor');
          //iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
          iFrame.height = "650px";
        }
    
  </script>
{% endblock %}

{% block content %}
  <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}" frameborder="0" width="100%" onload="iframeResize();" seamless></iframe>
{% endblock %}

{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "mail";
    
  </script>
{% endblock %}

