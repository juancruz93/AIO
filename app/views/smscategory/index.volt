{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }} 
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <!-- datetimepicker -->
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

  {{ javascript_include('js/angular/smscategory/controllers.js') }}
  {{ javascript_include('js/angular/smscategory/services.js') }}
    {{ javascript_include('js/angular/smscategory/app.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  <!-- datetimepicker -->
  {{ javascript_include('library/moment/min/moment.min.js') }}
  {{ javascript_include('library/moment/locale/es.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.templates.js') }}
  {% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div ng-app="smscategory" {#ng-controller="listController"#} ng-cloak>
    <div ui-view></div>
  </div>
{% endblock %}
{% block footer %}

  <script type="text/javascript">
var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
var templateBase = "smscategory";
  </script>

{% endblock %}