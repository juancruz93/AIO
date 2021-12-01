{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.min.css') }}
  {{ stylesheet_link('css/customTabsReportSmsByDestinataries.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.min.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.min.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.min.css') }}
  {#  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">#}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {#  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.min.css">#}
  {{ stylesheet_link('library/select2/3-4-5/css/select2.min.css') }}
  {#  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">#}
  {{ stylesheet_link('library/selectize/css/selectize.default.min.css') }}
  {#  {{ stylesheet_link('library/tarruda/tarruda.css') }}#}

  {{ stylesheet_link('library/select2-4.0.0/css/select2.min.css') }}

  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
  {#  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }}#}
  {#  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}#}
  {{ stylesheet_link('library/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}

  <link href="https://fonts.googleapis.com/css?family=Dosis|Signika|Unica+One" rel="stylesheet">

  <!-- datetimepicker -->
  {{ stylesheet_link('library/angular-bootstrap-datetimepicker-master/src/css/datetimepicker.css') }}

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

{% block js %}
  {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}

  
{#  {{ javascript_include('js/angular/report/app.min.js') }}
  {{ javascript_include('js/angular/report/controllers.min.js') }}
  {{ javascript_include('js/angular/report/services.min.js') }}#}
  
  {{ javascript_include('js/angular/report/app.js') }}
  {{ javascript_include('js/angular/report/controllers.js') }}
  {{ javascript_include('js/angular/report/services.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-aria.min.js') }}
  {#  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}#}
  {{ javascript_include('https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js',false) }}
  {#  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>#}

  {{ javascript_include('library/moment/moment-with-locales.min.js') }}
  {#<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment-with-locales.js"></script>#}


  {{ javascript_include('library/moment/src/prueba.min.js') }}

  {#  {{ javascript_include('library/moment/src/moment.js') }}#}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {# HighCharts & HighMaps #}
  {{ javascript_include('library/highstock/highcharts-ng.js') }}

  {#  {{ javascript_include('library/drilldown/highstock.src.min.js') }}#}
  {{ javascript_include('library/highstock/highcharts.js') }}
  {{ javascript_include('library/highstock/highcharts-more.js') }}
  {{ javascript_include('library/highstock/exporting.js') }}

  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.min.js')}}

  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.min.js')}}

  {#    <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.5.0.js"></script>#}

  {{ javascript_include('library/ui.bootstrap/ui-bootstrap-tpls-2.2.0.min.js') }}

  {{ javascript_include('library/angular-google-chart-development/ng-google-chart.min.js') }}

  <!-- datetimepicker -->
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}

  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}

  <!-- datetimepicker -->
  {{ javascript_include('library/moment/min/moment.min.js') }}
  {{ javascript_include('library/moment/locale/es.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.js') }}
  {{ javascript_include('library/angular-bootstrap-datetimepicker-master/src/js/datetimepicker.templates.js') }}

{% endblock %}
