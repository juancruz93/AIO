{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.min.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/activitylog/app.js') }}
  {{ javascript_include('js/angular/activitylog/controllers.js') }}
  {{ javascript_include('js/angular/activitylog/services.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker.min.js') }}
  {{ javascript_include('library/bootstrap-datepicker-1.6.4-dist/locales/bootstrap-datepicker.es.min.js') }}

{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div ui-view></div>
    </div>
  </div>
{% endblock %}  
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "activitylog";
  </script>

{% endblock %}

