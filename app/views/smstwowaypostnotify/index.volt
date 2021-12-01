{% extends "templates/default.volt" %}
{% block header %}
   {# Notifications  #}
  {{ partial("partials/css_notifications_partial") }}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  <!-- datetimepicker -->
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}
  <!-- ui-select -->
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <!-- Dialogs -->
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <!-- Angular-material -->
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  
  {{ stylesheet_link('library/bootstrap-datetimepicker-0.0.11/build/css/bootstrap-datetimepicker.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}

{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.js') }}

  <!-- datetimepicker -->
  {{ javascript_include('library/moment/min/moment.min.js') }}
  {{ javascript_include('library/moment/locale/es.js') }}
  
  <!-- ui-select -->
  {{ javascript_include('library/ui-select-master/dist/select.js') }}

  <!-- Angular-material -->
  {{ javascript_include('library/angular-material-1.1.0/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-messages.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}

  <!-- Smstwowaynotify -->
  {{ javascript_include('js/angular/smstwowaypostnotify/dist/smstwowaypostnotify.445267f66eaf2659312b.min.js') }}
  {#{{ javascript_include('js/angular/smstwowaypostnotify/services.js') }}
  {{ javascript_include('js/angular/smstwowaypostnotify/controllers.js') }}
  {{ javascript_include('js/angular/smstwowaypostnotify/app.js') }}#}
{% endblock %} 
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     
  <ui-view></ui-view>
{% endblock %}
{% block footer %}

  <script type="text/javascript">
    var host = "{{ url('') }}";
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "smstwowaypostnotify";
  </script>
{% endblock %}
