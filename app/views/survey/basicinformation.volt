<style>
  .btn-preview{
    margin-left: 5px;
    margin-top: 3px;
  }
  .width-84{
    width: 84% !important;
  }
</style>
<div class="row" data-ng-init="initComponents()">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Información básica de la encuesta</em>
    </div>
    <br>
    <p class="small-text">
      Configura la información básica acerca de la encuesta, como un nombre para identificar la encuesta, la opción para reconocer si será publica o enviada a unos contactos en específico y el mensaje de salida de la encuesta
    </p>
  </div>
</div>

<div class="row">
  <form class="form-horizontal" ng-submit="saveBasicInformation()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
      <div class="block block-primary">
        <div class="body row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap" >
            <div class="form-group">
              <label>*Nombre</label>
              {#              <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>#}
              {{ surveyForm.render('name') }}
            </div>
            <div class="form-group">
              <label>Descripción</label>
              {#              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>#}
              {{ surveyForm.render('description') }}
            </div>
            <div class="form-group" data-ng-show="!showNewCategSurvey">
              <label>*Categoría:</label>
              <div class="row">
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                  <ui-select ng-model="data.idCategorySurvey" ng-required="true" ui-select-required theme="select2"
                             sortable="false" style="width: 100% !important" close-on-select="true" >
                    <ui-select-match placeholder="Selecciona una categoría">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices repeat="key.idSurveyCategory as key in surveyCategory | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                  <a class="positive tooltip-de" data-placement="top" title="" href="" data-ng-click="newCateg.showNewCateg()" data-original-title="Nueva categoría">
                    <i class="fa fa-plus fa-2x" style="margin-right: 80%"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="form-group" data-ng-show="showNewCategSurvey">
              <label>Nueva categoría</label>
              <div class="row">
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                  <input type="text" class="undeline-input form-control" maxlength="80" data-ng-model="newcategsurvey">
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
                  <a class="negative tooltip-de" data-toggle="tooltip" data-placement="top" title="Cancelar" href="" data-ng-click="newCateg.hideNewCateg()"><i class="fa fa-times fa-2x"></i></a>
                  <a class="positive tooltip-de" data-toggle="tooltip" data-placement="top" title="Guardar" href="" data-ng-click="newCateg.saveNewCateg()"><i class="fa fa-check fa-2x"></i></a>
                </div>
              </div>
            </div>
            {#<div class="form-group" data-ng-if="data.status != 'published'">
              <label class="text-right">*Tipo de encuesta:</label>
{#              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>#}
            {#             <ui-select ng-model="data.type" ng-required="true" ui-select-required theme="select2"
                                    sortable="false" style="width: 100% !important" close-on-select="true" >
                           <ui-select-match placeholder="Selecciona un tipo">{{ "{{$select.selected.name}}" }}</ui-select-match>
                           <ui-select-choices repeat="key.id as key in types | propsFilter: {name: $select.search}">
                             <div ng-bind-html="key.name | highlight: $select.search"></div>
                           </ui-select-choices>
                         </ui-select>
                       </div>#}
            <div class="form-group">
              <label>*Mensaje final</label>
              {#              <span class="fa fa-info-circle color-gray drop_info" title="Descripción"></span>#}
              {{surveyForm.render('messageFinal')}}
            </div>
            <div class="form-group">
              <label>Url de finalización</label>
              {{surveyForm.render('url')}}
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap none-paddign">
            <div class="fill-block fill-block-primary">
              <div class="header">
                Instrucciones
              </div>
              <div class="body">
                <p>
                  Antes de comenzar, por favor lea atentamente la siguiente información:
                <ul>
                  <li>El nombre de la encuesta debe tener mínimo 2 caracteres.</li>
                  <li>El nombre de la encuesta debe contener máximo 70 caracteres.</li>
                  <li>La descripción de la encuesta debe contener un máximo de 200 caracteres</li>
                  <li>El mensaje final se mostrará cuando el contacto haya completado la encuesta.</li>
                  <li>El mensaje final debe tener un máximo de 200 caracteres.</li>
                  <li>Los campos con asterisco (*), son obligatorios.</li>
                  <li>Si la url de finalización esta vacía se tomará por defecto la url del sitio web que fue agregado al momento de crear la cuenta.</li>
                  <li>El formato aceptado para la url se debe de agregar el sufijo "http://" por ejemplo http://www.ejemplo.com</li>
                </ul>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="footer">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right col-lg-offset-6 col-md-offset-6">
            <a href="{{ url('survey') }}"
               class="button btn btn-small danger-inverted"
               data-toggle="tooltip" data-placement="top" title="Cancelar">
              Cancelar
            </a>
            <button class="button btn btn-small success-inverted" title="Guardar y continuar">
              Guardar y continuar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-prevew-width">
    <div class="modal-content modal-prevew-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
      </div>
      <div class="modal-body modal-prevew-body" id="preview-modal" style="height: 550px;"></div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="button btn btn-sm danger-inverted">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function () {
    $(".drop_info").mouseover(function () {
      $(this).parent().parent().find('.info_cointainer').show();
    });
    $(".drop_info").mouseout(function () {
      $(this).parent().parent().find('.info_cointainer').hide();
    });
    $("#type").select2({
      theme: 'classic'
    });
    $(".select2").select2({
      theme: 'classic'
    });
  });
</script>

