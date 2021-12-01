<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap'>
  <div class="form-group">
    <label for="selectAction">*Seleccionar opción</label>
    <ui-select name="selectAction" ng-change="changeSelectedAction(selected.selectAction)" ng-model="selected.selectAction" theme="select2"
      sortable="false" close-on-select="true" class='min-width-100'>
      <ui-select-match
        placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices
        repeat="key in listActions | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>

  <div class="form-group" ng-show="showLinksTemplate">
    <label >*Enlaces de la plantilla</label>
    <ui-select ng-model="selected.linksTemplateSelected" ng-required="true"  ui-select-required  class='min-width-100' 
               theme="select2" title="" sortable="false" close-on-select="true">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in listLinksTemplate | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
    <div align="right">
      <a  ng-click="refreshData()" class="primary-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar Links">
        <i ng-class="classRefreshrotate ? 'fa fa-refresh fa-spin fa-fw': 'fa fa-refresh fa-fw'"></i>
      </a>
    </div>
  </div>
  <!-- COMENTADO ENLACES DE PLANTILLA EN INPUT
  <div class="form-group" ng-show="showLinksTemplate">
    <label >*Enlaces de la plantilla</label>
    <ui-select multiple ng-model="selected.linksTemplateSelected" ng-required="true"  ui-select-required  class='min-width-100' 
               theme="select2" title=""  sortable="false" close-on-select="true">
      <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
      <ui-select-choices repeat="key in listLinksTemplate | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div> -->

  <div class="form-group">
    <label >*Tiempo de envio</label>
    
    <ui-select  ng-model="selected.time" ng-required="true"  ui-select-required  class='min-width-100' 
                theme="select2" title="" sortable="false" close-on-select="true">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in timeList | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>

  <div class="form-group">
    <label >*Formato de tiempo</label>
    <ui-select ng-model="selected.timetwo" ng-required="true"  ui-select-required  class='min-width-100' 
               theme="select2" title=""  sortable="false" close-on-select="true">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in timeListtwo | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>

  <div class="form-group" >
    <p class="text-danger" ng-show="selected.error">Todos los campos son obligarotios.</p>
  </div>
  <div class="clearfix"></div>
  <div class="footer" align="right">                                                
    <a  ng-click="closePopover()" class="danger-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Cancelar">
      <span class="glyphicon glyphicon-remove"></span>
    </a>
    <a ng-click="applyListSelectedAction()" class="success-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Guardar">
      <span class="glyphicon glyphicon-ok"></span>
    </a>
  </div>
</div>