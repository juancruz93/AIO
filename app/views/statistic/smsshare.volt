{% extends "templates/default_public.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/angular-form-builder-master/dist/angular-form-builder.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap.js') }}

  {# HighCharts & HighMaps #}
  {{ javascript_include('library/highstock/highcharts-ng.js') }}
  {{ javascript_include('library/drilldown/highstock.src.js') }}
  {{ javascript_include('library/drilldown/drilldown.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
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
  <div ng-controller="smssharecontroller" ng-cloak >

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Estadística de envío de SMS
        </div>            
        <hr class="basic-line" />
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <em class="text-2em"><strong>{{'{{sms.sms.name}}'}}</strong></em><br>
        <em>enviado el <strong>{{'{{sms.sms.startdate}}'}}</strong></em>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="border-table-block-not-padding" style="width: 100%">
          <tr>
            <td class="text-right">
              <em ><strong>Categoria </strong></em>
            </td>
            <td class="text-left">
              {{'{{sms.sms.namecategory}}'}}
            </td>
            <td class="text-right">
              <em ><strong>Destinatarios</strong></em>
            </td>
            <td class="text-left">
              {{"{{ sms.sms.target  }}"}} 
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row" ng-show='sms.detail[1].total > 0  '>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <highchart  config="chartConfig"  ></highchart>
      </div>
    </div>
 
  </div>
{% endblock %}  
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
var templateBase = "mail";
var type = '{{type}}';
var idSms = '{{ idSms }}';

  </script>
{% endblock %}