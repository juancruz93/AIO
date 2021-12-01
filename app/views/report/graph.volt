{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}

{% endblock %}

{% block js %}
  {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('js/angular/report/app.js') }}
  {{ javascript_include('js/angular/report/controllers.js') }}
  {{ javascript_include('js/angular/report/services.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment-with-locales.js"></script>
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  {{ javascript_include('library/moment/src/moment.js') }}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div ng-view></div>
    </div>
  </div>
{% endblock %} 
{% block footer %}

  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "report";
  </script>

{% endblock %}
