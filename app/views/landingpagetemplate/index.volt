{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  <!-- datetimepicker -->
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}

  <!-- ui-select -->
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}

  <!-- Angular-material -->
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
{% endblock %}
{% block header %}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.js') }}

  <!-- datetimepicker -->
  {{ javascript_include('library/moment/min/moment.min.js') }}
  {{ javascript_include('library/moment/locale/es.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.templates.js') }}

  <!-- ui-select -->
  {{ javascript_include('library/ui-select-master/dist/select.js') }}

  <!-- Angular-material -->
  {{ javascript_include('library/angular-material-1.1.0/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-messages.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}

  <!-- Landing Page Template -->
  {{ javascript_include('js/angular/landingpagetemplate/controllers.js') }}
  {{ javascript_include('js/angular/landingpagetemplate/services.js') }}
  {{ javascript_include('js/angular/landingpagetemplate/app.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div ng-cloak>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <ui-view></ui-view>
      </div>
    </div>  
  </div>
{% endblock %}
{% block footer %}
  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";

  </script>
{% endblock %}
