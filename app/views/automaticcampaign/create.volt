<script type="text/javascript">
  function formatRepo(repo) {

    var markup = '<div class="clearfix">' +
      '<div clas="col-sm-10">' +
      '<div class="clearfix">' +
      '<div class="col-sm-6">' + repo.name + '</div>' +
      '</div>';


    markup += '</div></div>';

    return markup;
  }

  function formatRepoSelection(repo) {
    return repo.name;
  }

  $.fn.select2.defaults.set('language', 'es');
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();

  {#$('#datetimepickerStart').datetimepicker({
    format: 'yyyy-MM-dd hh:mm',
    language: 'es',
    startDate: new Date()
  });

  $('#datetimepickerEnd').datetimepicker({
    format: 'yyyy-MM-dd hh:mm',
    language: 'es',
    startDate: new Date()
  });#}

      $("#Category").select2({
        theme: "classic",
        ajax: {
          url: fullUrlBase + "/api/automacampcateg/getcategory",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, page) {
            // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data
            dataObjSelectMailTemplate = data;
            return {
              results: data.items
            };
          },
          cache: true
        },
        escapeMarkup: function (markup) {
          return markup;
        }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page  

      });

    });
    

</script>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="title">
      Crear una nueva campaña automática
    </div>            
    <hr class="basic-line" />
  </div>
</div>

<div class="clearfix"></div>
<div class="space"></div>
{#<pre>{{"{{chartViewModel.data | json}}"}}</pre>#}

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <p class="small-text">Cree campañas automáticas de marketing digital en donde podrá asociar muchos productos que se activarán dependiendo de una acción o un tiempo determinado como por ejemplo enviar un sms 3 horas después de que un contacto haya abierto un correo enviado.</p>
    <div class="space"></div>
    <div class="pull-right ">
      <button class="btn button danger-inverted"  ng-click="toReturn()" >Cancelar</button>   
      <button class="btn button primary-inverted" ng-click="createAutomaticCampaign(1)">Guardar y seguir editando</button>  
      <button class="btn button success-inverted" ng-click="createAutomaticCampaign(2)">Guardar y finalizar</button>  
    </div>
  </div>
</div>
 
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 block-basic none-padding">
  <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1 none-padding">
    <div class="list-group " >
      <a class="list-group-item " ng-repeat="item in items" ng-class="item.class" ng-click="addNewNode(item,item.image,item.template)" ng-show="item.name != 'Tiempo'">
        <i ng-if="item.icon" ng-class="item.iconClass"></i>
        <div ng-if="!item.icon">{{'{{item.name}}'}}</div> 
        <md-tooltip ng-if="item.icon" md-direction="right">
          {{'{{item.name}}'}}
        </md-tooltip>
      </a>
    </div>
  </div>
  <div class="col-xs-12 col-sm-11 col-md-11 col-lg-11 none-padding-left " 
       mouse-capture
       ng-keydown="keyDown($event)"
       ng-keyup="keyUp($event)">
    <flow-chart  style="margin: 5px; width: 100%; height: 4000px;"chart="chartViewModel"></flow-chart>
  </div>
</div>

<div id="dialogDeleteNode" class="dialog" ng-controller="FlowChartController">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Esta seguro?</h2>
      <div>
        Todo lo relacionado con el componente que desea eliminar hacia adelante tambien sera eliminado ¿esta seguro?
      </div>
      <br>
      <div>
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a ng-click="deleteNodeSelected()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<div id="createAutomaticCampaign" class="dialog" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <div class="form-horizontal">
        <div class="form-group">
          <label for="name" class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">*Nombre</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <input id="name" type="text" ng-model="formCampaign.nameCampaign" placeholder="Nombre"  class="undeline-input" ng-minlength="2" ng-maxlength="45" minlength="2" maxlength="45"/>
            <div class="text-right" data-ng-class="formCampaign.nameCampaign.length > 45 ? 'negative':''">{{"{{formCampaign.nameCampaign.length > 0 ?  formCampaign.nameCampaign.length+'/45':''}}"}}</div>
          </div>
        </div>  
        <div class="form-group">
          <label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">*Fecha Inicio</label>
          <div id='datetimepickerStart' class="col-xs-12 col-sm-12 col-md-9 col-lg-9 date">
            <span class="input-append date add-on input-group none-padding">
              <input id="datestartCampaign"  type="text" class="undeline-input">
              {#{{ smsloteform.render('startdate', { 'readonly':'', 'ng-model': 'startdate', 'class': 'undeline-input' , 'id': 'datesend', 'required' : 'required' , 'keep-current-value':'' , 'ng-model': 'startdate' }) }}#}
              <span class="add-on input-group-addon">
                <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="form-group">
          <label for="Category" class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3 ">*GMT</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 text-left">
            <ui-select name="gmt" ng-model="formCampaign.gmt" ng-change="applyTextGmt()"theme="select2" sortable="false"
                       close-on-select="true" style="width: 100%"> 
              <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.countries}}" }}</ui-select-match>
              <ui-select-choices
                repeat="key.gmt as key in listZonaHoraria | propsFilter: {countries: $select.search}">
                <div ng-bind-html="key.countries | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
        </div>
        <div class="form-group" ng-show="showTextGmtStart">
          <label for="Category" class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3" >Fecha Inicio:</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9"> {{"{{dateGmtStart}}"}}</div>
          
        </div>      
      
        <div class="form-group">
          <label for="Category" class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">*Categoría</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 text-left">
            {#          <select name="Category" id="Category" ng-model="formCampaign.campaignCategory" style="width: 100%"></select>#}
            <ui-select name="senderName" ng-model="formCampaign.campaignCategory" theme="select2" sortable="false"
                       close-on-select="true" style="width: 100%">
              <ui-select-match
                placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
              <ui-select-choices
                repeat="key.id as key in listCategory | propsFilter: {name: $select.search}">
                <div ng-bind-html="key.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
        </div>
        <div class="form-group">
          <label for="Description" class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">Descripcion</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 ">
            <textarea id="Description" type="text" ng-model="formCampaign.descriptionCampaign" placeholder="Descripcion"  class="undeline-input" minlength="2" maxlength="200" style="resize: none;"></textarea>
            <div class="text-right" data-ng-class="formCampaign.descriptionCampaign.length > 200 ? 'negative':''">{{"{{formCampaign.descriptionCampaign.length > 0 ?  formCampaign.descriptionCampaign.length+'/200':''}}"}}</div>
          </div>
        </div>
      </div>


      <br>
      <div>
        <a onClick="closeModalForm();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a ng-click="insCampaign()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>    
<script>

  function closeModal() {
    $('#dialogDeleteNode').removeClass('dialog--open');
  }

  function closeModalForm() {
    $('#createAutomaticCampaign').removeClass('dialog--open');
  }
</script>    
