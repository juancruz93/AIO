{% extends "templates/default.volt" %}
{% block header %}
   {# Notifications  #}
  {{ partial("partials/css_notifications_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {{ stylesheet_link('library/bootstrap-datetimepicker-0.0.11/build/css/bootstrap-datetimepicker.min.css') }}

  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.min.css') }}

  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}

{% endblock %}
{% block js %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
 <!-- datetimepicker -->
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-material.min.js') }}
  {{ javascript_include('js/angular/rate/services.js') }}
  {{ javascript_include('js/angular/rate/controllers.js') }}
  {{ javascript_include('js/angular/rate/app.js') }}
{% endblock %} 
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     
  <ui-view></ui-view>
{% endblock %}
{% block footer %}
  {#  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}#}

  <script type="text/javascript">
    var host = "{{ url('') }}";
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "rate";
  </script>
{% endblock %}
