
{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/spectrum/css/spectrum.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }} 
{% endblock %}

{% block js %}
  {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui/0.4.0/angular-ui.min.js"></script>
  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.js')}}
  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.js')}}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}

  {{ javascript_include('js/angular/dashboardconfig/dist/dashboardconfig.9bb4f51ee6c167206a7e.min.js') }}

  {# {{ javascript_include('js/angular/dashboardconfig/directive.min.js') }}
  {{ javascript_include('js/angular/dashboardconfig/service.min.js') }}
  {{ javascript_include('js/angular/dashboardconfig/controller.min.js') }}
  {{ javascript_include('js/angular/dashboardconfig/app.min.js') }} #}
{% endblock %}
{% block content %}
<div ng-cloak >
  <div class="clearfix"></div>
  <div class="space"></div>

  <ui-view></ui-view>
</div>
{% endblock %} 
{% block footer %}
    <script type="text/javascript">
              var relativeUrlBase = "{{urlManager.get_base_uri()}}";
              var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
              var templateBase = "dashboardconfig";
              var urlBaseDefault = "{{url('')}}";
              var idAllied = {{idAllied}};
    </script>
{% endblock %}

