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
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap-tpls-2.2.0.min.js') }}
  
  {# HighCharts & HighMaps #}
  {#  {{ javascript_include('library/highstock/highcharts-ng.js') }}#}
  {#  {{ javascript_include('library/drilldown/highstock.src.min.js') }}#}
  {#  {{ javascript_include('library/drilldown/drilldown.js') }}#}
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
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>http://localhost/aio/library/drilldown/drilldown.js
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}


{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div ng-controller="shareController" ng-cloak ng-init="complete = '{{type}}' ">
    <style>
      .progress-bar-success{
        background-color: #00c1a5 !important;
      }
      .progress-bar-warning{
        background-color: #ff6e00 !important;
      }
      .progress-bar-info{
        background-color: #b700c1 !important;
      }
      .progress-bar-danger{
        background-color: #ff2400 !important;
      }
      .progress-bar-primary{
        background-color: #00bede !important;
      }
      .progress-bar-default{
        background-color: #777 !important;
      }
      .ch-item-resize{
        width: 45px !important;
        height: 45px !important;
        padding-top:  9px !important;
      }

      .htitle{
        font-weight: bold;
        text-align: left;
      }
    </style>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Estadística de envío de correo
        </div>            
        <hr class="basic-line" />
      </div>
    </div>

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <em class="text-2em"><strong>{{'{{stactics.mail.name}}'}}</strong></em><br>
        <em>enviado el <strong>{{'{{stactics.mail.confirmationDate}}'}}</strong></em>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <table class="border-table-block-not-padding" style="width: 100%">
          <tr>
            <td class="text-right">
              <em ><strong>Asunto</strong></em>
            </td>
            <td class="text-left">
              {{'{{stactics.mail.subject}}'}}
            </td>
            <td class="text-right">
              <em ><strong>Remitente</strong></em>
            </td>
            <td class="text-left">
              {{"{{ stactics.mail.namesender  }}"}} 
              <{{"{{ stactics.mail.emailsender  }}"}}>
            </td>
            <td rowspan="3" style="text-align: center;">
              <img ng-src="{{ "{{stactics.urlImg}}"}}" fallback-src="{{ "{{stactics.urlImgDefault}}"}}" alt="">
            </td>
          </tr>
          <tr>
            <td class="text-right">
              <em ><strong>Destinatarios</strong></em>
            </td>
            <td class="text-left">
              {{"{{ stactics.mail.target  }}"}}
            </td>
            <td class="text-right">
              <em ><strong>Responder a</strong></em>
            </td>
            <td class="text-left" ng-if="stactics.mail.replyto != 'No asignado'">
              {{"{{ stactics.mail.replyto  }}"}}
            </td>
            <td class="text-left" ng-if="stactics.mail.replyto == 'No asignado'">
              <i>{{"{{ stactics.mail.replyto  }}"}}</i>
            </td>
          </tr>
          <tr>
            <td class="text-right">
              <em ><strong>Correos enviados</strong></em>
            </td>
            <td class="text-left">
              <em class="small-text"><strong>{{"{{ stactics.messageSent }}"}}</strong></em>
            </td>
            <td class="text-center" colspan="2">
              <a href="#/" data-ng-click="previewmailtempcont(stactics.mail.idMail);" data-toggle="modal" data-target="#myModal" ng-show="!type"><strong>Ver contenido del correo</strong></a><br>
          <spam ng-show="stactics.mail.test==1"><b>Correo marcado como prueba</b></spam>
          </td>
          </tr>
        </table>
      </div>
      <div class="clearfix"></div>
      {#  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="pull-right">
            <button class="btn btn-md primary-inverted">Enviar estadistica por correo</button>
            <button class="btn btn-md default-inverted">Descargar estadisticas como un archivo PDF</button>
          </div>
        </div>#}

      <div class="clearfix"></div>
      <div class="space"></div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
        {#    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-center ">
              <div class="inline-block none-padding">
                <em class="text-3em"><strong>{{"{{ stactics.messageSent }}"}}</strong></em>
                <br>  
                <span >Correos enviados</span>
              </div>  
            </div>#}
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="success-no-hover small-text" ng-click="goOpen(); activeJustified = 0 ">Aperturas</span>
            <span class="medium-text success-no-hover">
              <uib-progressbar style="height: 10px" class="success-no-hover" value="stactics.open" 
                               max="stactics.messageSent" type="success"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.open }}"}}
            <span class="medium-text success-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.open)}}"}}%
            </span>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="warning-no-hover small-text" ng-click="goBounced(); activeJustified = 3" >Rebotes</span>
            <span class="medium-text success-no-hover">
              <uib-progressbar style="height: 10px" class="success-no-hover" value="stactics.bounced" 
                               max="stactics.messageSent" type="warning"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.bounced }}"}}
            <span class="medium-text warning-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.bounced)}}"}}%
            </span>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="primary-no-hover small-text" ng-click="goClic(); activeJustified = 1">Clics</span>
            <span class="medium-text primary-no-hover">
              <uib-progressbar style="height: 10px" class="" value="stactics.uniqueClicks" 
                               max="stactics.messageSent" type="primary"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.uniqueClicks }}"}} 
            <span class="medium-text success-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.uniqueClicks)}}"}}%
            </span>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="danger-no-hover small-text" ng-click="goSpam(); activeJustified = 4">Spam</span>
            <span class="medium-text danger-no-hover">
              <uib-progressbar style="height: 10px" class="danger-no-hover" value="stactics.spam" 
                               max="stactics.messageSent" type="danger"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.spam }}"}} 
            <span class="medium-text danger-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.spam)}}"}}%
            </span>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row wrap ">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="default-no-hover small-text" ng-click="goUnsuscribe(); activeJustified = 2">Desuscritos</span>
            <span class="medium-text default-no-hover">
              <uib-progressbar style="height: 10px" class="default-no-hover" value="stactics.unsubscribed" 
                               max="stactics.messageSent" type="default"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.unsubscribed }}"}} 
            <span class="medium-text default-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.unsubscribed)}}"}}%
            </span>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
          <div class="  col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <span class="info-no-hover small-text" ng-click="goBuzon(); activeJustified = 5">Buzón</span>
            <span class="medium-text info-no-hover">
              <uib-progressbar style="height: 10px" class="info-no-hover" value="stactics.buzon" 
                               max="stactics.messageSent" type="info"></uib-progressbar>
            </span>
          </div>
          <div class=" col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center" style="padding-top: 3%">
            {{"{{ stactics.buzon }}"}} 
            <span class="medium-text info-no-hover" style="font-size: 1.8em">
              {{"{{calculatePercentage(stactics.messageSent, stactics.buzon)}}"}}%
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-if="complete != 'summary' ">
      <hr class="basic-line" />
      <label class="medium-text"> Detalle de estadística</label>
      <uib-tabset active="activeJustified" >
        <uib-tab index="0" heading="Aperturas" ng-click="opening()" id="open">
          <div class="clearfix"></div>
          <div class="space"></div>
          <div ng-show="countTotal >= 1">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Aperturas</span>
                <br>
                <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="space"></div>
            <div id="highchartOpen"></div>
          </div>
          <div ng-show="countTotal == 0">
            <img class='logo' src='/images/general/open.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
          </div>
        </uib-tab>
        <uib-tab index="1" heading="Clics" ng-click="clic()" id="clic">
          <div class="clearfix"></div>
          <div class="space"></div>
          <div ng-show="countTotal >= 1">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 medium-text">
                <label>{{"{{countTotal}}"}} </label> <span> Clics únicos.</span>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <label class="">{{"{{countTotal}}"}} </label> <span class="small-text"> Contactos de </span><label class="small-text"> {{"{{stactics.messageSent}}"}} </label>
                <span class="small-text"> posibles hicieron clic en un enlace.</span>
                <br>
                <label class="small-text">({{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%)</label><span class="small-tex"> Tasa de clics</span>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Contactos de </span> <label class="small-text">{{"{{stactics.open}}"}} </label>
                <span class="small-text"> que abrieron el correo, hicieron clic en un enlace.</span>
                <br>
                <label class="small-text">({{"{{calculatePercentage(stactics.open, countTotal)}}"}}%) </label> <span class="small-text"> Click To Open Rate</span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="space"></div>
            <div id="highchartClic"></div>
          </div>
          <div ng-show="countTotal == 0">
            <img class='logo' src='/images/general/clics.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
          </div>
        </uib-tab>
        <uib-tab index="2" heading="Desuscritos" ng-click="unsuscribe()" id="unsuscribe">
          <div class="clearfix"></div>
          <div class="space"></div>
          <div ng-show="countTotal >= 1">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Total de desuscritos</span>
                <br>
                <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="space"></div>
            <div id="highchartUnsuscribe"></div>
          </div>
          <div ng-show="countTotal == 0">
            <img class='logo' src='/images/general/unsuscribed.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
          </div>
        </uib-tab>
        <uib-tab index="3" heading="Rebotados" ng-click="bounced()" id="bounced">

          <div class="clearfix"></div>
          <div class="space"></div>
          <div ng-show="countTotal >= 1">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label class="small-text">{{"{{graphPie[0].hard}}"}} </label> 
                <br>
                <label class="small-text">{{"{{calculatePercentage(countTotal, graphPie[0].hard)}}"}}% </label><span class="small-text"> Suave</span>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label class="small-text">{{"{{graphPie[0].soft}}"}} </label> 
                <br>
                <label class="small-text">{{"{{calculatePercentage(countTotal, graphPie[0].soft)}}"}}% </label><span class="small-text"> Suave</span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="space"></div>
            <div id="highchartBounced"></div>
          </div>
          <div ng-show="countTotal == 0">
            <img class='logo' src='/images/general/rebound.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
          </div>
        </uib-tab>
        <uib-tab index="4" heading="Spam" ng-click="spam()" id="spam">
          <div class="clearfix"></div>
          <div class="space"></div>
          <div ng-show="countTotal >= 1">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label class="small-text">{{"{{countTotal}}"}} </label> <span class="small-text"> Total de spam</span>
                <br>
                <label class="small-text">{{"{{calculatePercentage(stactics.messageSent, countTotal)}}"}}%</label>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="space"></div>
            <div id="highchartSpam"></div>
          </div>
          <div ng-show="countTotal == 0">
            <img class='logo' src='/images/general/spam.png' style='width:750px;padding-left: 5px;display:inline;' alt='No hay aun reporte de aperturas de esta campaña'/>
          </div>
        </uib-tab>
      </uib-tabset>
      <br>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
      <div class="modal-dialog modal-prevew-width">
        <div class="modal-content modal-prevew-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h1 class="modal-title" id="myModalLabel">Contenido del correo</h1>
          </div>
          <div class="modal-body modal-prevew-body" id="content-preview" style="height: 550px;">

          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
function openModal() {
  $('.dialog').addClass('dialog--open');
}

function closeModal() {
  $('.dialog').removeClass('dialog--open');
}
  </script>
  <script type='text/javascript'>
    function highlight(field)
    {
      field.focus();
      field.select();
    }

  </script>

{% endblock %}  
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var idMail = '{{idMail}}';
    var type = '{{type}}';
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "statistic";
  </script>

{% endblock %}
