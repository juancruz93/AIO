{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
      {{ stylesheet_link('library/bootstrap-datetimepicker-0.0.11/build/css/bootstrap-datetimepicker.min.css') }}

    {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.min.css') }}
    {{ stylesheet_link('css/checkboxStyle.css') }}
    {{ stylesheet_link('library/ui-select-master/dist/select.css') }} 
    {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
    {# Dialogs #}
{% endblock %}    

{% block js %}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
    {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
    {{ javascript_include('library/moment/src/prueba.js') }}
    {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.min.js') }}
    {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
    {{ javascript_include('js/socket.io.js') }}
    {{ javascript_include('js/main.js') }}
    {{ javascript_include('js/angular/whatsapp/services.js') }}
    {{ javascript_include('js/angular/whatsapp/controllers.js') }}
    {{ javascript_include('js/angular/whatsapp/app.js') }}
 <!-- datetimepicker -->
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.min.js') }}
    {{ javascript_include('library/angular-moment/angular-moment.min.js') }}


    
{% endblock %}
{% block content %}

    <ui-view></ui-view>
  
    <script type="text/javascript">
        var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
        var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
        var templateBase = "whatsapp";
        var idSubaccount = {{user.Usertype.idSubaccount}};
    </script>
{% endblock %}
