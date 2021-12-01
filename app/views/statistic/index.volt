{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }} 
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/flowchart/app.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {{ stylesheet_link('library/tarruda/tarruda.css') }} 
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/angular-form-builder-master/dist/angular-form-builder.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap.js') }}
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap-tpls-2.2.0.min.js') }}

  {# HighCharts & HighMaps #}
  {#{{ javascript_include('library/highstock/highcharts-ng.js') }}
  {{ javascript_include('library/drilldown/highstock.src.min.js') }}
  {{ javascript_include('library/drilldown/drilldown.js') }}#}
  {{ javascript_include('library/ocLazyLoad-master/dist/ocLazyLoad.min.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
  
  {{ javascript_include('library/flowchart/flowchart/svg_class.js') }}
  {{ javascript_include('library/flowchart/flowchart/mouse_capture_service.js') }}
  {{ javascript_include('library/flowchart/flowchart/dragging_service.js') }}
  {{ javascript_include('library/flowchart/flowchart/flowchart_viewmodel.js?v=1.0.8') }}
  {{ javascript_include('library/flowchart/flowchart/flowchart_directive.js?v=1.0.8') }}
  {{ javascript_include('library/flowchart/flowchart/flowchart_services.js?v=1.0.8') }}
  {{ javascript_include('library/flowchart/flowchart/flowchart_controller.js?v=1.0.8') }}
  {{ javascript_include('library/flowchart/app.js?v=1.0.8') }}
  
  {# {{ javascript_include('js/angular/statistic/app.js') }}
  {{ javascript_include('js/angular/statistic/controllers.js') }}
  {{ javascript_include('js/angular/statistic/services.js') }}
  {{ javascript_include('js/angular/statistic/directives.js') }} #}
  {{ javascript_include('js/angular/statistic/dist/statistic.423a36b666a287ef2199.min.js') }}
  {{ javascript_include('library/angular-form-builder-master/dist/angular-form-builder-v1.js') }}
  {{ javascript_include('js/angular/survey/angular-form-builder-components-survey.js') }}
  {{ javascript_include('library/angular-validator/angular-validator.min.js') }}
  {{ javascript_include('library/angular-validator/angular-validator-rules.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

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
    var templateBase = "statistic";
  </script>

{% endblock %}
