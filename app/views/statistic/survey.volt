<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}      
    </div>            
    <hr class="basic-line" />
  </div>
</div>

<div class="clearfix"></div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <em class="text-2em"><strong>{{'{{stactics.survey.name}}'}}</strong></em><br>
    <em>Encuesta publicada el <strong>{{'{{stactics.survey.startDate}}'}}</strong> y valida hasta el <strong>{{'{{stactics.survey.endDate}}'}}</strong></em>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <table class="border-table-block-not-padding" style="width: 100%">
      <tr>
        <td class="text-right">
          <em ><strong>Descripción</strong></em>
        </td>
        <td class="text-left">
          {{'{{stactics.survey.description}}'}}
        </td>
        <td class="text-right">
          <em ><strong>Tipo</strong></em>
        </td>
        <td class="text-left">
          {{"{{ translationType(stactics.survey.type) }}"}}
        </td>
      </tr>
      <tr>
        <td class="text-right">
          <em ><strong>Total encuestados</strong></em>
        </td>
        <td class="text-left">
          <em class="small-text"><strong>{{"{{ stactics.survey.totalCount }}"}}</strong></em>
        </td>
        <td class="text-center" colspan="2">
          <a href="" data-ng-click="previsualizar(stactics.survey.surveycontent.content)"><strong>Ver contenido de la encuesta</strong></a><br>
        </td>
      </tr>
    </table>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <button class="btn btn-md default-inverted pull-right" ng-click="reportDetail()" >Descargar Detalle</button>
    {#<button class="btn btn-md primary-inverted" ng-click="printDetail()" >Imprimir Detalle</button>#}
  </div>
  <div class="clearfix"></div>

</div>
<div class="clearfix"></div>

<div class="clearfix"></div>
<div class="space"></div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <hr class="basic-line" />

  <div data-ng-repeat="question in stactics.questions" ng-if="showDom">
    <md-card md-theme="default" md-theme-watch>
    <div class="text-center">
      <label class="medium-text">{{ '{{ question.question }}' }}</label>
      <p>Respondido: {{ '{{ stactics.survey.totalCount }}' }} Omitido: {{ '{{ stactics.survey.totalCount - question.count }}' }} </p>
    </div>
    <div data-ng-if="question.component != 'textArea'">
      <highchart data-ng-init="chartBar(question, stactics.survey.totalCount)" config="question.chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>
      <table class="table table-bordered">
        <thead class="theader ">
        <tr>
          <th>
            Opciones de respuesta
          </th>
          <th>
            Respuestas
          </th>
        </tr>
        </thead>
        <tfoot>
          <tr>
            <td><b>Total</b></td>
            <td class="text-align-right"><b>{{ '{{ question.totalAnswer }}' }}</b></td>
          </tr>
        </tfoot>
        <tbody>
          <tr ng-repeat="answer in question.chart.data" >
            <td>
                {{ '{{answer.name}}' }}
            </td>
            <td class="text-align-right">
                <spam class="float-left">{{ '{{((answer.y / stactics.survey.totalCount) * 100).toFixed(2) }}' }}%</spam> <spam>{{ '{{answer.y}}' }}</spam>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div data-ng-if="question.component == 'textArea'">
      <table class="table table-bordered">
        <thead class="theader ">
          <tr>
            <th>
              Mostrando {{ '{{ question.chart.data.length }}' }} respuesta
            </th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="answer in question.chart.data" >
            <td>
                {{ '{{answer.name}}' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </md-card>
  </div>
  <br>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    {#<label class="small-text">Comparar estadística con otro envio de correo</label>
    <div class="form-inline">
      <select class="form-control"><option selected>Otro envio del pais</option></select> 
      <button class="btn btn-md primary-inverted">Comparar</button>
    </div>#}

  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
    <button class="btn btn-md default-inverted" ng-click="back()" style="margin-top: 20px; margin-right: 3px;">
      <i class="fa fa-arrow-left"></i>
      Regresar
    </button>
    {#<a class="btn btn-md success-inverted pull-right" style="margin-top: 20px;" onclick="openModal();">Compartir estadística</a>#}
  </div>
</div>
<div id="preview" class="dialog" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <md-progress-linear md-mode="indeterminate" ng-hide="previewShow" class="md-warn"></md-progress-linear>
      <div ng-if="previewShow" style="text-align: left !important;">
        <div class="container-fluid">
          <div class="form-group" style="overflow: scroll;" >
            <form  ng-submit="validateSurvey()"ng-style="{'background-color':backgroundForm}">
              <div ng-model="input"  fb-form="sigmaSurvey" fb-default="defaultValue"></div>
            </form>
          </div>
        </div>
      </div>
      <div class="form-group">
        <a ng-click="removeDialog('preview');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
      </div>
    </div>
  </div>
</div>
<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner" style="padding: 2em;">
      <div class="modal-header">
        <a type="button" class="close"aria-hidden="true" onClick="closeModal();">×</a>
        <h4 class="modal-title htitle">Compartir estadísticas</h4>
      </div>
      <div class="modal-body">
        <p style="text-align: left;">
          Copie estos enlaces y compartalos con quien quiera, y así las personas que lo abran
          en el navegador podrán ver las estadisticas del correo.
        </p>

        <h4 class="htitle">Compartir estadisticas completas del correo</h4>
        <p id="complete"><input type="text" class="col-sm-12" readonly="readonly" id="inputcomplete" value="{{"{{ stactics.fullStaticsUrl }}"}}" onclick='highlight(this);'></p>
        <h4 class="htitle">Compartir estadisticas parciales del correo</h4>
        <p id="summary"><input type="text" class="col-sm-12" readonly="readonly" id="inputsummary" value="{{"{{ stactics.shortStaticsUrl }}"}}" onclick='highlight(this);'></p>

      </div>
      <div class="modal-footer">
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
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
{#<a id="bottom"></a> You're at the bottom!#}


{#</div>#}
