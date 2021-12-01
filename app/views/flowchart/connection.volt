<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
  <div class="form-group">
    <label >*Tiempo de envio</label>
    <ui-select  ng-model="selected.time" ng-required="true"  ui-select-required  
                theme="select2" title=""  sortable="false" close-on-select="true" class="min-width-100">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in timeList | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div class="form-group">
    <label >*Formato de tiempo</label>
    <ui-select ng-model="selected.timetwo" ng-required="true"  ui-select-required  
               theme="select2" title=""  sortable="false" close-on-select="true" class="min-width-100">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in timeListtwo | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div class="form-group" >
    <p class="text-danger" ng-show="selected.error">Todos los campos son obligarotios.</p>
  </div>
</div>

<div class="clearfix"></div>
<div class="footer" align="right">                                                
  <a  ng-click="closePopover()"class="danger-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Cancelar">
    <span class="glyphicon glyphicon-remove"></span>
  </a>
  <a ng-click="applyListSelectedConnection()" class="success-no-hover"  style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Guardar">
    <span class="glyphicon glyphicon-ok"></span>
  </a>
</div>
