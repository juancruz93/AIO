{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">

  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}

{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/angular-bootstrap-colorpicker-master/css/colorpicker.min.css') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('library/angular-bootstrap-colorpicker-master/js/bootstrap-colorpicker-module.js') }}
  {{ javascript_include('js/angular/customizing/app.js') }}
  {{ javascript_include('js/angular/customizing/controllers.js') }}
  {{ javascript_include('js/angular/customizing/directives.js') }}
  {{ javascript_include('js/angular/customizing/filters.js') }}
  {{ javascript_include('js/angular/customizing/services.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.tpl.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>


{% endblock %}
{% block content %}

  <div class="clearfix"></div>
  <div class="space"></div>     
  <div ui-view></div>


{% endblock %}

{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });

    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "customizing";
  </script>    

{% endblock %}
